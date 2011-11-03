<?php
/**
 * Handles Tooltips access functionality.
 */
class Wdeb_Tooltips {

	var $data;

	function Wdeb_Tooltips () { $this->__construct(); }

	function __construct () {
		$this->data = new Wdeb_Options;
	}

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	function serve () {
		$me = new Wdeb_Tooltips;
		$me->add_hooks();
	}

	function css_print_styles () {
		wp_enqueue_style('thickbox');
		wp_enqueue_style('wdeb_help', WDEB_PLUGIN_URL . '/css/wdeb_help.css');
	}

	function js_print_scripts () {
		global $current_screen;

		if (!$this->is_in_easymode()) return false;

		wp_enqueue_script('thickbox');

		$file_base = $current_screen->id;
		if (file_exists(WDEB_PLUGIN_BASE_DIR . '/lib/help/' . $file_base . '.php')) require_once (WDEB_PLUGIN_BASE_DIR . '/lib/help/' . $file_base . '.php');

		printf(
			"<script type='text/javascript'>" .
				"var _wdeb_tooltip_tpl = '%s';" .
				"var _wdeb_help_tpl = '%s';" .
			"</script>",
			'<div class="wdeb_tooltip"><div class="tooltip" title="%%text%%">&nbsp;</div></div>',
			'<div class="wdeb_help_popup"><a href="#" id="wdeb_show_help"><span>' . __('Help', 'wdeb') . '</span></a><div class="help" id="wdeb_help_container"><div id="wdeb_help_inside_wrapper">%%text%%</div></div></div>'
		);
	}

	function is_in_easymode () {
		return (defined('WDEB_IS_IN_EASY_MODE') && WDEB_IS_IN_EASY_MODE);
	}

	function add_hooks () {
		if (WP_NETWORK_ADMIN) return false;
		if (!$this->data->get_option('show_tooltips', 'wdeb_help')) return false;

		add_action('admin_print_scripts', array($this, 'js_print_scripts'));
		add_action('admin_print_styles', array($this, 'css_print_styles'));
	}
}