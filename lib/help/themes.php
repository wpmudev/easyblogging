<?php
wp_enqueue_script('wdeb_help', WDEB_PLUGIN_URL . '/js/help/themes.js');
wp_localize_script('wdeb_help', 'l10WdebHelp', array(
	'title' => __('Change the design of your blog, preview and activate new themes.', 'wdeb'),
	'current' => __('This is the design that you are currently using for your blog. If you would like to change it, you can choose from the Available Themes below', 'wdeb'),
	'available' => __('You can choose any of these designs, and your blog will be automatically updated to look like this. Simply click on any of the images to preview what your blog will look like with that design.', 'wdeb'),

	'help' => __('Here you can change your blog\'s theme', 'wdeb'),

));