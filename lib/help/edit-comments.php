<?php
wp_enqueue_script('wdeb_help', WDEB_PLUGIN_URL . '/js/help/edit-comments.js');
wp_localize_script('wdeb_help', 'l10WdebHelp', array(
	'edit_comments' => __('Here you can edit, approve, delete or reply to comments on your blog posts. Click on tabs or select "Filter" to see only specific comments.', 'wdeb'),

	'help' => __('Here you can manage the comments that are on your blog', 'wdeb'),

));