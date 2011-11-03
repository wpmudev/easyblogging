<?php
wp_enqueue_script('wdeb_help', WDEB_PLUGIN_URL . '/js/help/edit-page.js');
wp_localize_script('wdeb_help', 'l10WdebHelp', array(
	'edit_page' => __('Below is a list your pages. You can quickly see important information about them, as well as edit, delete, or view each one.', 'wdeb'),

	'help' => __('Here you can manage the pages that are on your blog', 'wdeb'),

));