<?php
/*
Plugin Name: Admin Footer
Description: Show a footer area on all pages in the Easy mode.
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: WPMU DEV
*/

class Wdeb_Theme_AdminFooter {
	
	private $_data;

	private function __construct () {
		$this->_data = new Wdeb_Options;
	}

	public static function serve () {
		$me = new Wdeb_Theme_AdminFooter;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		// Actually show the footer
		add_filter('wdeb_style-custom_stylesheet_rules', array($this, 'footer_styles'));
		add_filter('wdeb-admin_footer-markup', array($this, 'show_footer'));

		// Toggle footer for wizard mode, according to settings.
		add_action('admin_init', array($this, 'wizard_mode_check'), 999);

		// Add page
		add_action('wdeb_admin-add_pages', array($this, 'register_page'));

		// Add settings
		add_action('wdeb_admin-register_settings-settings', array($this, 'add_settings'));
		add_filter('wdeb_admin-options_changed', array($this, 'save_settings'));
	}

	function wizard_mode_check () {
		if ($this->_data->get_option('apply_to_wizard', 'wdeb_admin_footer')) return false; // We're omnipresent

		$user = wp_get_current_user();
		$user_id = ($user && $user->ID) ? $user->ID : false;
		if (!get_user_meta($user_id, 'wdeb_wizard', true)) return false; // Not in wizard mode

		remove_filter('wdeb_style-custom_stylesheet_rules', array($this, 'footer_styles'));
		remove_filter('wdeb-admin_footer-markup', array($this, 'show_footer'));
	}

	function footer_styles () {
		echo <<<EOFooterCss
.wdeb-admin_footer {
	position: absolute;
	bottom: 0;
	left: 220px;
	right: 0;
}
EOFooterCss;
	}

	function show_footer () {
		$footer = '';

		if ($this->_have_existing_footer() && $this->_data->get_option('use_existing', 'wdeb_admin_footer')) {
			$footer .= '<div class="wdeb-admin_footer-existing">' .
				apply_filters('admin_footer_text', '') .
			'</div>';
		}

		if ($this->_data->get_option('use_custom', 'wdeb_admin_footer')) {
			$footer .= '<div class="wdeb-admin_footer-custom">' . 
				$this->_data->get_option('custom_footer', 'wdeb_admin_footer') .
			'</div>';
		}

		return '<div class="wdeb-admin_footer">' . $footer . '</div>';
	}
	
	function register_page ($perms) {
		add_submenu_page('wdeb', __('Admin footer', 'wdeb'), __('Admin Footer', 'wdeb'), $perms, 'wdeb_admin_footer', array($this, 'render_page'));
	}

	function render_page () {
		echo '<div class="wrap"><h2>' . __('Easy Blogging Admin Footer', 'wdeb') . '</h2>';
		echo (is_network_admin()
			? '<form action="settings.php" method="post" enctype="multipart/form-data">'
			: '<form action="options.php" method="post" enctype="multipart/form-data">'
		);
		settings_fields('wdeb_admin_footer');
		do_settings_sections('wdeb_admin_footer');
		echo '<p class="submit"><input name="Submit" type="submit" class="button-primary" value="' . __('Save Changes') . '" /></p>';
		echo '</form></div>';
	}

	function add_settings () {
		register_setting('wdeb', 'wdeb_admin_footer');
		
		if ($this->_have_existing_footer()) {
			add_settings_section('wdeb_existing_footer', __('Admin Footer', 'wdeb'), create_function('', ''), 'wdeb_admin_footer');
			add_settings_field('wdeb_use_existing_footer', __('Use existing admin footer', 'wdeb'), array($this, 'create_use_existing_box'), 'wdeb_admin_footer', 'wdeb_existing_footer');
		}

		add_settings_section('wdeb_custom_footer', __('Custom Footer', 'wdeb'), create_function('', ''), 'wdeb_admin_footer');
		add_settings_field('wdeb_use_custom_footer', __('Use custom footer', 'wdeb'), array($this, 'create_use_custom_box'), 'wdeb_admin_footer', 'wdeb_custom_footer');
		add_settings_field('wdeb_custom_footer', __('Custom footer content', 'wdeb'), array($this, 'create_custom_footer_box'), 'wdeb_admin_footer', 'wdeb_custom_footer');

		add_settings_section('wdeb_footer_settings', __('Settings', 'wdeb'), create_function('', ''), 'wdeb_admin_footer');
		add_settings_field('wdeb_wizard_footer', __('Use the footer on Wizard pages too', 'wdeb'), array($this, 'create_wizard_footer_box'), 'wdeb_admin_footer', 'wdeb_footer_settings');
	}

	function save_settings ($changed) {
		if ('wdeb_admin_footer' == @$_POST['option_page']) {
			if (!empty($_POST['wdeb_admin_footer']['custom_footer'])) {
				$_POST['wdeb_admin_footer']['custom_footer'] = stripslashes(wp_filter_post_kses($_POST['wdeb_admin_footer']['custom_footer']));
			}
			$this->_data->set_options($_POST['wdeb_admin_footer'], 'wdeb_admin_footer');
			$changed = true;
		}
		return $changed;
	}

	private function _create_bool_box ($name, $label=false) {
		$label = $label ? "<label for='wdeb_admin_footer-{$name}'>{$label}</label>" : '';
		$checked = $this->_data->get_option($name, 'wdeb_admin_footer') ? 'checked="checked"' : '';
		return "<input type='hidden' name='wdeb_admin_footer[{$name}]' value='' />" .
			"<input type='checkbox' id='wdeb_admin_footer-{$name}' name='wdeb_admin_footer[{$name}]' value='1' {$checked} />" .
		"&nbsp;{$label}";
	}

	private function _have_existing_footer () {
		return 
			class_exists('Admin_Footer_Text')
			||
			class_exists('ub_Admin_Footer_Text')
		;
	}

	function create_use_existing_box () {
		echo $this->_create_bool_box('use_existing', __('Use the footer as defined by the Admin Footer plugin', 'wdeb'));
	}

	function create_use_custom_box () {
		echo $this->_create_bool_box('use_custom', __('Use footer as defined in the box below', 'wdeb'));
	}

	function create_custom_footer_box () {
		$body = $this->_data->get_option('custom_footer', 'wdeb_admin_footer');
		echo '<textarea name="wdeb_admin_footer[custom_footer]" class="widefat" rows="8">' . esc_textarea($body) . '</textarea>';
	}

	function create_wizard_footer_box () {
		echo $this->_create_bool_box('apply_to_wizard', __('Apply the footer to Wizard pages too', 'wdeb'));
	}
}

if (is_admin()) Wdeb_Theme_AdminFooter::serve();