<?php
/*
Plugin Name: Control "Product" metaboxes
Description: Allows control over which metaboxes appear in the "Product" custom post type. <b>Requires MarketPress plugin</b>
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdeb_Editor_ControlProductMetaboxes {
	
	private $_data;

	private function __construct () {
		$this->_data = new Wdeb_Options;
	}

	public static function serve () {
		$me = new Wdeb_Editor_ControlProductMetaboxes;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		// Add settings
		add_action('wdeb_admin-register_settings-settings', array($this, 'add_settings'));
		add_filter('wdeb_admin-options_changed', array($this, 'save_settings'));

		// Actual removing
		add_action('wdeb_admin-editor_metaboxes_cleanup', array($this, 'remove_metaboxes'));
	}

	function remove_metaboxes () {
		global $wp_meta_boxes;
		$opts = $this->_data->get_options('wdeb_ecpm');
		$post_boxes = @$opts['hide_boxes'];
		$post_boxes = is_array($post_boxes) ? $post_boxes : array();

		if (is_array(@$wp_meta_boxes['product']['side']['core'])) foreach ($wp_meta_boxes['product']['side']['core'] as $name => $box) if (in_array($name, $post_boxes)) unset($wp_meta_boxes['product']['side']['core'][$name]);
		if (is_array(@$wp_meta_boxes['product']['side']['low'])) foreach ($wp_meta_boxes['product']['side']['low'] as $name => $box) if (in_array($name, $post_boxes)) unset($wp_meta_boxes['product']['side']['low'][$name]);
		if (is_array(@$wp_meta_boxes['product']['normal']['core'])) foreach ($wp_meta_boxes['product']['normal']['core'] as $name => $box) if (in_array($name, $post_boxes)) unset($wp_meta_boxes['product']['normal']['core'][$name]);
		if (is_array(@$wp_meta_boxes['product']['normal']['high'])) foreach ($wp_meta_boxes['product']['normal']['high'] as $name => $box) if (in_array($name, $post_boxes)) unset($wp_meta_boxes['product']['normal']['high'][$name]);
	}

	function add_settings () {
		add_settings_field('wdeb_ecpm_boxes', __('Hide these Product metaboxes', 'wdeb'), array($this, 'render_settings'), 'wdeb_options_page', 'wdeb_settings');
	}

	function render_settings () {
		$pfx = 'wdeb_ecpm';
		$name = 'hide_boxes';
		$opts = $this->_data->get_options($pfx);
		$hides = @$opts[$name];
		$hides = is_array($hides) ? $hides : array();
		
		$_boxes = array (
			'authordiv' => __('Author'),
			'postexcerpt' => __('Excerpt'),
			'product_categorydiv' => __('Product Categories', 'wdeb'),
			'tagsdiv-product_tag' => __('Product Tags', 'wdeb'),
			'mp-meta-download' => __('Product Download', 'wdeb'),
		);
		foreach ($_boxes as $bid => $label) {
			$checked = in_array($bid, $hides) ? 'checked="checked"' : '';
			echo "<input type='hidden' name='{$pfx}[{$name}][{$bid}]' value='0' />" .
				"<input {$checked} type='checkbox' name='{$pfx}[{$name}][{$bid}]' value='{$bid}' id='wdeb_product_post_boxes_{$bid}' /> " .
				"<label for='wdeb_product_post_boxes_{$bid}'>{$label}</label><br />\n";
		}
		_e(
			'<p><b>Warning:</b> all other boxes will be shown or hidden according to their screen settings</p>',
		'wdeb');
	}

	function save_settings ($changed) {
		if ('wdeb' == @$_POST['option_page']) {
			$this->_data->set_options($_POST['wdeb_ecpm'], 'wdeb_ecpm');
			$changed = true;
		}
		return $changed;
	}
}

if (is_admin()) Wdeb_Editor_ControlProductMetaboxes::serve();