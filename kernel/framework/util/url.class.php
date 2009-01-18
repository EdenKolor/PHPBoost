<?php
/*##################################################
 *                             url.class.php
 *                            -------------------
 *   begin                : January 14, 2009
 *   copyright            : (C) 2009 Lo�c Rouchon
 *   email                : horn@phpboost.com
 *
 *
###################################################
 *
 *   This program is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
###################################################*/

define('URL__CLASS','url');

/**
 * @author Lo�c Rouchon horn@phpboost.com
 * @desc This class offers a simple way to transform an absolute or relative link
 * to a relative one to the website root.
 * It can also deals with absolute url and will convert only those from this
 * site into relatives ones.
 * Usage :
 * <ul>
 *   <li>In content, get the url with the absolute() method. It will allow content include at multiple level</li>
 *   <li>In forms, get the url with the relative() method. It's a faster way to display url</li>
 * </ul>
 * @package util
 */
class Url
{
    /**
     * @desc Build a Url object
     * @param string $url the url string relative to the current path,
     * to the website root if beginning with a "/" or an absolute url
     */
    function url($url = '')
    {
        if (!empty($url))
            $this->set_url($url);
    }
    
    /**
     * @desc Set the url
     * @param string $url the url string relative to the current path,
     * to the website root if beginning with a "/" or an absolute url
     */
    function set_url($url)
    {
        $url = str_replace(HOST . DIR, '', $url);
        if (!strpos($url, '://'))
        {
            if (substr($url, 0, 1) == '/')
            {   // Relative url from the website root (good form)
                $this->relative = $url;
            }
            else
            {   // The url is relative to the current foler
                $this->relative = Url::root_to_local() . $url;
            }
            $this->relative = rtrim(Url::compress($this->relative), '/');
        }
        
        global $CONFIG;
        if ($this->relative !== '')
            $this->absolute = Url::compress(trim($CONFIG['server_name']) . '/' . trim($CONFIG['server_path'], '/') . $this->relative);
        else
            $this->absolute = Url::compress($url);
    }
    
    /**
     * @desc Returns the relative url if defined, else the empty string
     * @return string the relative url if defined, else the empty string
     */
    function relative()
    {
        return $this->relative;
    }
    
    /**
     * @desc Returns the absolute url
     * @return string the absolute url
     */
    function absolute()
    {
        return $this->absolute;
    }
    
    
    /**
     * @desc Compress a url by removing all "folder/.." occurrences
     * @param string $url the url to compress
     * @return string the compressed url
     */
    /* static */function compress($url)
    {
        $url = preg_replace('`([^:])?/+`', '$1/', preg_replace('`/[^/]+/\.\.`', '$1', $url));
        if (strpos($url, '/' . PATH_TO_ROOT) === 0)
        {
            return str_replace('/' . PATH_TO_ROOT, '', $url);
        }
        return $url;
    }
    
    /**
     * @desc Returns the relative path from the website root to the current path if working on a relative url
     * @return string the relative path from the website root to the current path if working on a relative url
     */
    /* static */ function root_to_local()
    {
        // Retrieve working path
        $a_local = explode('/', substr($_SERVER['PHP_SELF'], 0 , strrpos($_SERVER['PHP_SELF'], '/')));
        $a_root = explode('/', Url::compress(substr($_SERVER['PHP_SELF'], 0 , strrpos($_SERVER['PHP_SELF'], '/')) . '/' . PATH_TO_ROOT));
        $a_local_size = count($a_local);
        $a_root_size = count($a_root);
        
        // Come back to the root level
        $a_to_local = array();
        $separation_idx = -1;
        for ($i = 0; $i < $a_root_size; $i++)
        {
            if (!isset($a_local[$i]) || $a_root[$i] != $a_local[$i])
            {
                $a_to_local[] = '..';
                if ($separation_idx < 0)
                {
                    $separation_idx = $i;
                }
            }
        }
        
        // descend into the local folder
        for ($i = $separation_idx; $i < $a_local_size; $i++)
        {
            $a_to_local[] = $a_local[$i];
        }
        return '/' . implode('/', $a_to_local) . '/';
    }
    
    var $relative = '';
    var $absolute = '';
}
?>