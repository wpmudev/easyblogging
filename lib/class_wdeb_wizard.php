<?php
/**
 * Handles Wizard access functionality.
 */
class Wdeb_Wizard {

	var $data;

	function __construct () {
		$this->data = new Wdeb_Options;
	}

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	public static function serve () {
		$me = new Wdeb_Wizard;
		$me->add_hooks();
	}

	function css_print_styles () {
		if (!isset($_GET['page']) || 'wdeb_wizard' != $_GET['page']) return false;
		$protocol = ($_SERVER["HTTPS"] == 'on') ? 'https://' : 'http://';
		//wp_enqueue_style('jquery-ui', $protocol . 'ajax.googleapis.com/ajax/libs/jqueryui/1.7.2/themes/ui-lightness/jquery-ui.css');
		wp_enqueue_style('wp-jquery-ui-dialog');
	}

	function js_print_scripts () {
		if (!isset($_GET['page']) || 'wdeb_wizard' != $_GET['page']) return false;
		wp_enqueue_script( array("jquery", "jquery-ui-core", "jquery-ui-sortable", 'jquery-ui-dialog') );
	}

	function rebind_menu_partial ($menu) {
		$user = wp_get_current_user();
		$user_id = ($user && $user->ID) ? $user->ID : false;
		$use_wizard = ($user_id) ? get_user_meta($user_id, 'wdeb_wizard', true) : false;
		return apply_filters('wdeb-wizard-use_wizard', $use_wizard)
			? 'menu-wizard' 
			: $menu
		;
	}

	function rebind_wizard_steps () {
		$steps = $this->data->get_option('wizard_steps', 'wdeb_wizard');
		$steps = is_array($steps) ? $steps : array();
		return $steps;
	}

	function initialize_wizard () {
		$user = wp_get_current_user();
		$user_id = ($user && $user->ID) ? $user->ID : false;
		do_action('wdeb-wizard-initialize_wizard_mode', $user_id);
		if ($user_id && isset($_GET['wdeb_wizard_on'])) {
			update_user_meta($user_id, 'wdeb_wizard', 1);
			do_action('wdeb-wizard-initialize_wizard_mode-on', $user_id);
			wp_redirect(admin_url(WDEB_LANDING_PAGE));
			die;
		} else if ($user_id && isset($_GET['wdeb_wizard_off'])) {
			update_user_meta($user_id, 'wdeb_wizard', 0);
			do_action('wdeb-wizard-initialize_wizard_mode-off', $user_id);
			wp_redirect(admin_url(WDEB_LANDING_PAGE));
			die;
		}
	}

	public static function in_wizard_mode ($user_id=false) {
		if (empty($user_id) || !(int)$user_id) {
			$user_id = get_current_user_id();
		}
		return $user_id
			? get_user_meta($user_id, 'wdeb_wizard', true) 
			: false
		;
	}

	function add_hooks () {
		if (!$this->data->get_option('wizard_enabled', 'wdeb_wizard')) return false;

		add_action('admin_print_scripts', array($this, 'js_print_scripts'));
		add_action('admin_print_styles', array($this, 'css_print_styles'));

		if (defined('WP_NETWORK_ADMIN') && WP_NETWORK_ADMIN) return false;
		add_filter('wdeb_menu_partial', array($this, 'rebind_menu_partial'));
		add_filter('wdeb_wizard_steps', array($this, 'rebind_wizard_steps'));

		// Handle turning the wizard on/off
		add_action('admin_init', array($this, 'initialize_wizard'));
	}
}