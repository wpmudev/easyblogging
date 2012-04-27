<?php
/*
Plugin Name: Dashboard widgets setup
Description: Easily manage displayed dashboard widgets on your Easy Blogging installs.
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdeb_Dashboard_WidgetsSetup {
	
	private $_data;

	private function __construct () {
		$this->_data = new Wdeb_Options;
	}

	public static function serve () {
		$me = new Wdeb_Dashboard_WidgetsSetup;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		// Removal history recoring
		add_action('wdeb_dashboard_cleanup_removing_item', array($this, 'record_removed_history'), 10, 4);
		
		// Add page
		add_action('wdeb_admin-add_pages', array($this, 'register_page'));

		// Add settings
		add_action('wdeb_admin-register_settings-settings', array($this, 'add_settings'));
		add_filter('wdeb_admin-options_changed', array($this, 'save_settings'));
		
		// Actual filtering interception
		add_filter('wdeb_allowed_dashboard_metaboxes', array($this, 'add_allowed_widgets_to_dashboard'));
	}
	
	private function _load_history () {
		return get_site_option('wdeb_wdws_history');
	}
	
	private function _save_history ($history) {
		return update_site_option('wdeb_wdws_history', $history);
	}
	
	function record_removed_history ($board, $position, $key, $widget) {
		$history = $this->_load_history();
		if (isset($history[$key])) return false;
		$history[$key] = $widget;
		$this->_save_history($history);
	}

	function add_allowed_widgets_to_dashboard ($allowed) {
		$by_plugin = $this->_data->get_options('wdeb_dashboard_items');
		$by_plugin = $by_plugin ? $by_plugin : array();
		foreach ($by_plugin as $item) {
			$allowed[] = $item;
		}
		return $allowed;
	}
	
	function register_page ($perms) {
		add_submenu_page('wdeb', __('Dashboard items', 'wdeb'), __('Dashboard items', 'wdeb'), $perms, 'wdeb_dashboard_items', array($this, 'render_page'));
	}

	function render_page () {
		echo '<div class="wrap"><h2>Easy Blogging Dashboard widgets</h2>';
		echo (is_network_admin()
			? '<form action="settings.php" method="post" enctype="multipart/form-data">'
			: '<form action="options.php" method="post" enctype="multipart/form-data">'
		);
		settings_fields('wdeb_dashboard_items');
		do_settings_sections('wdeb_dashboard_items');
		echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __('Save Changes') . '" /></p>';
		echo '</form></div>';
	}

	function add_settings () {
		register_setting('wdeb', 'wdeb_dashboard_items');
		add_settings_section('wdeb_dashboard_items', __('Widgets', 'wdeb'), create_function('', ''), 'wdeb_dashboard_items');
		add_settings_field('wdeb_show_dashboard_items', __('Previously hidden Dashboard items', 'wdeb'), array($this, 'create_show_hide_box'), 'wdeb_dashboard_items', 'wdeb_dashboard_items');
	}

	function save_settings ($changed) {
		if ('wdeb_dashboard_items' == @$_POST['option_page']) {
			$this->_data->set_options($_POST['wdeb_dashboard_items'], 'wdeb_dashboard_items');
			$changed = true;
		}
		return $changed;
	}

	function create_show_hide_box () {
		$history = $this->_load_history();
		if (!$history) {
			echo '<div class="error below-h2"><p>' . __('No recorded history. You may want to visit your blog\'s Dashboard first.', 'wdeb') . '</p></div>';
			return false;
		}
		$allowed = $this->_data->get_options('wdeb_dashboard_items');
		$allowed = $allowed ? $allowed : array();
		foreach ($history as $item) {
			$checked = in_array($item['id'], $allowed) ? 'checked="checked"' : '';
			echo '<div>' .
				"<input type='checkbox' name='wdeb_dashboard_items[]' id='wdeb_dashboard_items-" . $item['id'] . "' value='" . $item['id'] . "' {$checked} />" .
				"&nbsp;<label for='wdeb_dashboard_items-" . $item['id'] . "'>" . $item['title'] . '</label>' .
			'</div>';
		}
		echo '<p>' .
			__('If you do not see your Dashboard widget here, please visit your site\'s Dashboard in Easy Mode first.', 'wdeb') .
		'</p>';
	}
}

if (is_admin()) Wdeb_Dashboard_WidgetsSetup::serve();