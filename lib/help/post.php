<?php
wp_enqueue_script('wdeb_help', WDEB_PLUGIN_URL . '/js/help/post.js');
wp_localize_script('wdeb_help', 'l10WdebHelp', array(
	'new_post' => __('Write up a new post to appear at the top of your blog.', 'wdeb'),
	'post_title' => __('The best post titles are usually really descriptive.', 'wdeb'),
	'post_body' => __('Write the content of your post, upload images or audio and choose if you want to use HTML (code) or Visual (like Word). You can paste in embed code for videos and widgets under the HTML tab.', 'wdeb'),
	'publish' => __('You can save your post as a draft or make it private by clicking on "Edit" next to "Visibility" below. You can also schedule posts to publish in the future by clicking "Edit" next to "Immediately". ', 'wdeb'),
	'tags' => __('Tags are a great way to help search engines find your posts, or to help you organize your content. Add as many as you can!', 'wdeb'),
	'categories' => __('Categories are more serious than tags. They are the main themes of your blog.', 'wdeb'),

	'help' => __('A long string of help here', 'wdeb'),

));