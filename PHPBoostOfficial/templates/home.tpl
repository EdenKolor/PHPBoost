<!-- Download link -->
<div id="p-container">

	<img src="{PATH_TO_ROOT}/PHPBoostOfficial/templates/images/pub.png" alt="PHPBoost CMS" class="p-img" width="400" height="256"/>
	
	<div id="p-slide">
		<a href="#" class="p-slide-title">PHPBoost CMS</a>
		<p class="p-slide-content">
			{@site_slide_description}
		</p>
	</div>
	<div id="p-more" class="p-left">
		<a href="{PATH_TO_ROOT}/pages/fonctionnalites-de-phpboost" class="p-more_content" title="{@phpboost_features}">{@phpboost_features.explain}</a>
	</div>
	
	<div id="p-link" class="p-left">
		<div class="p-link-btn btn-ddl">
			<a href="{PATH_TO_ROOT}/download/" title="{@download.phpboost}">
				<i class="fa fa-download fa-2x"></i>
				<p class="p-link-title">{@download}</p>
				<p class="p-link-com ddl-com">{@version} {@download.last_major_version_number}</p>
			</a>
		</div>
		<div class="p-link-btn btn-try">
			<a href="http://demo.phpboost.com" title="{@demo}">
				<i class="fa fa-cog fa-2x"></i>
				<p class="p-link-title">{@try}</p>
				<p class="p-link-com try-com">{@demo.website}</p>
			</a>
		</div>
	</div>
	
</div>

<!-- 3 last modules and templates -->
<div id="lc-container">

	<div id="lt-container">
		<div class="lc-img lt-title-img"></div>
		<div class="lc-title title">{@last_themes}</div>
	
		<div id="lt-content">
			# INCLUDE THEMES #
			<div class="lc-more">
				<a href="{@download.last_version_themes_cat_link}" title="{@themes_for_phpboost}">[{@discover_other_themes}]</a>
			</div>
			<div class="spacer"></div>
		</div>
	</div>
	
	<div id="lm-container">
		<div class="lc-img lm-title-img"></div>
		<div class="lc-title title">{@last_modules}</div>
		
		<div id="lm-content">
			# INCLUDE MODULES #
			<div class="lc-more" id="lt-more">
				<a href="{@download.last_version_modules_cat_link}" title="{@modules_for_phpboost}">[{@discover_other_modules}]</a>
			</div>
			<div class="spacer"></div>
		</div>
	</div>
	
</div>

<!-- Last news -->
<div id="ln-container">

	<div id="ln-top-img">
		<a href="{PATH_TO_ROOT}/syndication/rss/news/" title="{@news.phpboost.rss}"></a>
	</div>
	<div id="ln-top" class="title">{@news.phpboost}</div>

	# INCLUDE LAST_NEWS #
	
	<div class="ln-list">
		<div id="ln-list-title" class="title">{@news.previous_news}</div>
		<div class="ln-list-content">
			{FEED_NEWS}
		</div>
	</div>
	
</div>