<?php
/*
Plugin Name: Admin toolbar switch button
Description: Replace the standard Easy mode toggle button with an Admin toolbar menu entry.
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdeb_Theme_ToolbarSwitchButton {
	
	private function __construct () {}

	public static function serve () {
		$me = new Wdeb_Theme_ToolbarSwitchButton;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('admin_print_scripts', array($this, 'dequeue_scripts'), 19);
		add_action('admin_bar_menu', array($this, 'add_to_admin_bar'), 99);
	}

	function dequeue_scripts () {
		wp_dequeue_script('wdeb_switch');
	}

	function add_to_admin_bar () {
		global $wp_admin_bar;
		if (!(defined('WDEB_IS_IN_EASY_MODE') && WDEB_IS_IN_EASY_MODE)) {
			$href = apply_filters('wdeb_easy_mode_init', WDEB_LANDING_PAGE . '?wdeb_on');
			$title = __('Activate easy mode', 'wdeb');
		} else {
			$data = new Wdeb_Options;
			$auto_enter_roles = $data->get_option('auto_enter_role');
			if (!$auto_enter_roles || !wdeb_current_user_can($auto_enter_roles)) {
				$href = apply_filters('wdeb_easy_mode_init', WDEB_LANDING_PAGE . '?wdeb_off');
				$title = __('Exit easy mode', 'wdeb');
			} else return false; // Not showing exit easy mode link if not applicable
		}
		$wp_admin_bar->add_menu(array(
			'parent' => 'site-name',
			'id' => 'wdeb-my_sites-ttsb',
			'title' => $title,
			'href' => $href,
		));
	}
}
if (is_admin()) Wdeb_Theme_ToolbarSwitchButton::serve();