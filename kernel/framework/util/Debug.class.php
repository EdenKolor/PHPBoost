<?php
/*##################################################
 *                             Debug.class.php
 *                            -------------------
 *   begin                : October 3, 2009
 *   copyright            : (C) 2009 Loic Rouchon
 *   email                : loic.rouchon@phpboost.com
 *
 ###################################################
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 ###################################################*/

/**
 * @author loic rouchon <loic.rouchon@phpboost.com>
 * @desc
 * @package {@package}
 */
class Debug
{
	const STRICT_MODE = 'strict_mode';
	const DISPLAY_DATABASE_QUERY = 'display_database_query';
	
	private static $enabled = true;
	private static $options = array();
	private static $html_output = true;

	public static function __static()
    {
        $file = new File(PATH_TO_ROOT . '/cache/debug.php');
        if ($file->exists())
        {
            include $file->get_path();
            self::$enabled = $enabled;
            self::$options = $options;
        }
    }

    /**
     * @desc Enables the debug mode for the current script only.
     * @param mixed[string] $options see <code>self::enabled_debug_mode()</code> for information on this parameter
     */
    public static function enabled_current_script_debug(array $options = array())
    {
        self::$enabled = true;
        self::$options = $options;
    }

	/**
	 * @desc Enables the debug mode
	 * @param mixed[string] $options Here is a description of the optional debug parameters that can
	 * be passed in the options array
	 * <ul>
	 *     <li><code>self::STRICT_MODE</code>: boolean - If true, page processing will stop
	 *     <li><code>self::DISPLAY_DATABASE_QUERY</code>: boolean - If true, display database query
	 *     at the first notice / warning / error encountered</li>
     * </ul>
	 */
	public static function enabled_debug_mode(array $options = array())
	{
        self::$enabled = true;
        self::$options = $options;
		self::write_debug_file();
	}

    /**
     * @desc Disables the debug mode
     */
    public static function disable_debug_mode()
    {
        self::$enabled = false;
        self::$options = array();
        self::write_debug_file();
    }

    private static function write_debug_file()
    {
        $file = new File(PATH_TO_ROOT . '/cache/debug.php');
        $file->write('<?php ' . "\n" .
	        '$enabled = ' . var_export(self::$enabled, true) . ';' . "\n" .
	        '$options = ' . var_export(self::$options, true) . ';' . "\n" .
	        ' ?>');
        $file->close();
    }
    
    /**
     * @desc Tells whether the debug mode is enabled
     * @return bool true if enabled, false otherwise
     */
    public static function is_debug_mode_enabled()
    {
        return self::$enabled;
    }
    
    /**
     * @desc Returns true if the strict debug mode is enabled.
     * If true, the page processing will be stopped if any notice, warning or error is encountered. 
     * @return bool true if the strict debug mode is enabled
     */
    public static function is_strict_mode_enabled()
    {
        return self::is_debug_mode_enabled() && self::get_option(self::STRICT_MODE, false);
    }
    
	/**
     * @desc Returns true if the display database query is enabled.
     * If true, the page display a database query with the Debug::dump() function and display stacktrace
     * @return bool true if the display database query is enabled.
     */
    public static function is_display_database_query_enabled()
    {
        return self::is_debug_mode_enabled() && self::get_option(self::DISPLAY_DATABASE_QUERY, false);
    }
    
    private static function get_option($key, $default)
    {
        if (array_key_exists($key, self::$options))
        {
            return self::$options[$key];
        }
        return $default;
    }

	/**
	 * @desc Returns true if the page is rendered in a browser mode.
	 * @return bool true if the page is rendered in a browser mode
	 */
	public static function is_output_html()
	{
		return self::$html_output;
	}

	/**
	 * @desc Inform PHPBoost that the dbug message have to be rendered in a non html (plain text) mode.
	 */
	public static function set_plain_text_output_mode()
	{
		self::$html_output = false;
	}

	/**
	 * @desc Displays information on an exception and exits
	 * @param Exception $exception the exception to display information on
	 */
	public static function fatal(Exception $exception)
	{
		if (!self::$html_output)
		{
			$message = get_class($exception) . ': ' . $exception->getMessage();
			if (empty($message))
			{
				$message .= 'An exception has been thrown';
			}
			echo $message . "\n--------------------------------------------------------------------------------\n";
			Debug::print_stacktrace(0, $exception);
		}
		else
		{
			$printer = new HTTPFatalExceptionPrinter($exception);
			echo $printer->render();
		}
		exit;
	}

	/**
	 * @desc prints the stacktrace and exits
	 * @param $object
	 */
	public static function stop($object = null)
	{
		if ($object != null)
		{
			Debug::dump($object);
		}
		self::print_stacktrace();
		exit;
	}

	/**
	 * @desc returns the current exception
	 * @return Exception the current exception
	 */
	public static function get_exception_context()
	{
		return new Exception();
	}

	/**
	 * @desc returns the current stacktrace
	 * @return string the current stacktrace
	 */
	public static function get_stacktrace()
	{
		$stack = self::get_exception_context()->getTrace();
		unset($stack[0]);
		return array_merge($stack, array());
	}

	/**
	 * @desc print the current stacktrace
	 */
	public static function get_stacktrace_as_string($start_trace_index = 0,	Exception $exception = null)
	{
		$string_stacktrace = '';
		$stacktrace = null;
		if ($exception === null)
		{
			$stacktrace = self::get_stacktrace();
		}
		else
		{
			$start_trace_index--;
			$stacktrace = $exception->getTrace();
		}
		$stacktrace_size = count($stacktrace);
		$start_trace_index = $start_trace_index + 1;
		for ($i = $start_trace_index; $i < $stacktrace_size; $i++)
		{
			$trace =& $stacktrace[$i];
			$string_stacktrace .= '[' . ($i - $start_trace_index) . '] ' . ExceptionUtils::get_file($trace) .
				':' . ExceptionUtils::get_line($trace) . ' - ' . ExceptionUtils::get_method_prototype($trace) . "\n";
		}

		if (self::is_output_html())
		{
			$string_stacktrace = str_replace("\n", '<br />', $string_stacktrace);
		}
		return $string_stacktrace;
	}

	/**
	 * @desc print the current stacktrace
	 */
	public static function print_stacktrace($start_trace_index = 0, Exception $exception = null)
	{
		if ($exception !== null)
		{
			$start_trace_index--;
		}
		echo self::get_stacktrace_as_string($start_trace_index + 1, $exception);
	}

	/**
	 * @desc executes a <code>print_r()</code> in an html &lt;pre&gt; block
	 * @param mixed $object the object to see using print_r
	 */
	public static function dump($object)
	{
		if (self::$html_output)
		{
			echo '<pre>'; print_r($object); echo '</pre>';
		}
		else
		{
			echo "\n"; print_r($object); echo "\n";
		}
	}
}
?>