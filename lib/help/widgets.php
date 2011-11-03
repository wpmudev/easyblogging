<?php
wp_enqueue_script('wdeb_help', WDEB_PLUGIN_URL . '/js/help/widgets.js');
wp_localize_script('wdeb_help', 'l10WdebHelp', array(
	'title' => __('To add elements to the sidebar of your blog drag and drop them below.', 'wdeb'),

	'help' => __('Here you can customize what shows up in your sidebar', 'wdeb'),

));