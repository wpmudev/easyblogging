<?php
/*
Plugin Name: Pro Sites: Force Easy mode on Free sites
Description: Forces Easy mode on Free sites. <b>Requires Pro Sites plugin.</b>
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdeb_Pro_ForceOnFreeSites {

	private $_data;

	private function __construct () {
		$this->_data = new Wdeb_Options;
	}

	public static function serve () {
		$me = new Wdeb_Pro_ForceOnFreeSites;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		// Add settings
		add_action('wdeb_admin-register_settings-settings', array($this, 'add_settings'));
		add_filter('wdeb_admin-options_changed', array($this, 'save_settings'));

		// Actual forcing
		add_filter('wdeb_get_option-wdeb-auto_enter_role', array($this, 'force_roles_on_free_sites'));
	}

	function force_roles_on_free_sites ($roles) {
		if (!class_exists('ProSites')) return $roles; // Doesn't apply if we don't have ProSites
		if (current_user_can('manage_network_options')) return $roles; // Doesn't affect Super Admins
		if (!function_exists('is_pro_site')) return $roles; // Erm... should never happen.

		$values = get_site_option('wdeb_pro');
		$force = @$values['force_on_free'];
		if (!$force) return $roles; // No forcing, nothing to do

		if (is_pro_site()) return $roles; // Pro site, no forcing;

		// Force on ALL roles
		return array (
			'administrator' => 'administrator',
			'editor' => 'editor',
			'author' => 'author',
			'contributor' => 'contributor',
			'subscriber' => 'subscriber',
		);
	}

	function add_settings () {
		if (!class_exists('ProSites')) return false;
		add_settings_field('wdeb_pro_force_on_free', __('Force Easy mode on free sites', 'wdeb'), array($this, 'render_settings'), 'wdeb_options_page', 'wdeb_settings');
	}

	function render_settings () {
		if (!class_exists('ProSites')) return false;
		$pfx = 'wdeb_pro';
		$name = 'force_on_free';
		$values = get_site_option('wdeb_pro');
		$value = @$values[$name];
		echo
			"<input type='radio' name='{$pfx}[{$name}]' id='{$name}-yes' value='1' " . ((int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-yes'>" . __('Yes', 'wdeb') . "</label>" .
			'&nbsp;' .
			"<input type='radio' name='{$pfx}[{$name}]' id='{$name}-no' value='0' " . (!(int)$value ? 'checked="checked" ' : '') . " /> " .
				"<label for='{$name}-no'>" . __('No', 'wdeb') . "</label>" .
		"";
	}

	function save_settings ($changed) {
		if ('wdeb' == @$_POST['option_page']) {
			update_site_option('wdeb_pro', $_POST['wdeb_pro']);
			$changed = true;
		}
		return $changed;
	}
}

if (is_admin()) Wdeb_Pro_ForceOnFreeSites::serve();