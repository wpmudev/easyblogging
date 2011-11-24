<?php
/*
Plugin Name: Pending comments notification
Description: Adds a balloon with pending comments count to Comments menu item.
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdeb_Menu_PendingComments {

	private function __construct () {}

	public static function serve () {
		$me = new Wdeb_Menu_PendingComments;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_filter('wdeb_menu-item_before_render', array($this, 'filter_menu_item'));
		add_action('wdeb_style-custom_stylesheet_rules', array($this, 'css_add_style_rules'));
	}

	public function css_add_style_rules () {
		echo <<<EOMenuPcStyle
li.current span.wdeb-count {
	display: none !important;
}
span.wdeb-count {
	float: none !important;
	font-size: 8px;
	position: relative;
}
span.wdeb-comments_count {
	float: none !important;
	position: absolute;
	display: block;
	background: #000;
	right: -25px;
	top: 5px;
	padding: 2px !important;
	line-height: 13px !important;
	height: 13px;
	width: 30px !important;
	text-align: center;
	color: #fff;
	border-radius: 10px;
}

@media (max-width: 1280px) {
	span.wdeb-comments_count {
		top: -2px !important;
	}
}

EOMenuPcStyle;
	}

	public function filter_menu_item ($item) {
		if ('edit-comments.php' != $item['url']) return $item;
		$item['title'] = $item['title'] . $this->_inject_count();
		return $item;
	}

	private function _inject_count () {
		$count = (int)$this->_count_comments();
		return $count ? " <span class='wdeb-count'><span class='wdeb-comments_count wdeb-comments_moderated'>{$count}</span></span>" : '';
	}

	private function _count_comments ($what=false) {
		$what = $what ? $what : 'moderated';
		$comments = wp_count_comments();
		return (int)@$comments->$what;
	}
}

if (is_admin()) Wdeb_Menu_PendingComments::serve();