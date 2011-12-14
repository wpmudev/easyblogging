<?php
/*
Plugin Name: Add facebook permission widget
Description: Adds the Ultimate Facebook permission widget to the Easy Blogging dashboard.
Plugin URI: http://gaatverweg.nl/
Version: 1.0
Author: pbrink
*/

class Wdeb_Menu_AddFacebookPermissionWidget {

	private function __construct () {}

	public static function serve () {
		$me = new Wdeb_Menu_AddFacebookPermissionWidget;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_filter('wdeb_allowed_dashboard_metaboxes', array($this, 'add_widget_to_dashboard'));
	}

	function add_widget_to_dashboard($allowed) {
		$allowed[count($allowed)] = "wdfb_dashboard_permissions_widget";
		return $allowed;
	}
}

if (is_admin()) Wdeb_Menu_AddFacebookPermissionWidget::serve();