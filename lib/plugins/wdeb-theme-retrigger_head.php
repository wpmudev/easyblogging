<?php
/*
Plugin Name: Compatibility mode
Description: If you experience a conflict with your plugin and Easy Blogging, try activating this add-on.
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdeb_AdminHead_Retrigger {

	private function __construct () {}

	public static function serve () {
		$me = new Wdeb_AdminHead_Retrigger;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('admin_init', array($this, 'init'));
	}

	public function init () {
		if (defined('WDEB_CORE_ACTIONS_REDO_ADMIN_HEAD')) return false;
		define('WDEB_CORE_ACTIONS_REDO_ADMIN_HEAD', true, true);
	}

}
Wdeb_AdminHead_Retrigger::serve();