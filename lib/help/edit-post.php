<?php
wp_enqueue_script('wdeb_help', WDEB_PLUGIN_URL . '/js/help/edit-post.js');
wp_localize_script('wdeb_help', 'l10WdebHelp', array(
	'edit_post' => __('Below is a list your posts. You can quickly see important information about them, as well as edit, delete, or view each one.', 'wdeb'),

	'help' => __('Here you can manage the posts that are on your blog', 'wdeb'),

));