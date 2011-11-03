<?php
wp_enqueue_script('wdeb_help', WDEB_PLUGIN_URL . '/js/help/page.js');
wp_localize_script('wdeb_help', 'l10WdebHelp', array(
	'new_page' => __('Create a new "static" page below - this won\'t appear at the top of your latest posts.', 'wdeb'),
	'title' => __('Give your page a title here.', 'wdeb'),
	'body' => __('Write the content of your page, upload images or audio and choose if you want to use HTML (code) or Visual (like Word). You can paste in embed code for videos and widgets under the HTML tab.', 'wdeb'),
	'publish' => __('Publish your page or save it as a draft below. You can also make it private or schedule publication for the future by clicking on the "Edit" links.', 'wdeb'),

	'help' => __('A page is a <em>stand-alone</em> item that does not appear at the top of your blog - e.g an <em>about page</em> or a page with contact details, a course outline or even a CV. Turn off comments for a professional look.', 'wdeb'),

));