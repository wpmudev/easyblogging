<?php
/*
Plugin Name: Small switch button
Description: Replace the standard Easy mode toggle button with a smaller, icon-based version.
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: WPMU DEV
*/

class Wdeb_Theme_SmallSwitchButton {
	
	private function __construct () {}

	public static function serve () {
		$me = new Wdeb_Theme_SmallSwitchButton;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('admin_footer', array($this, 'add_icon_styles'));
	}

	function add_icon_styles () {
		$img_url = WDEB_PLUGIN_URL . '/img/easy-mode-small.png';
		echo <<<EoSmallSwitchCss
<style type="text/css">
.wdeb_switch.small_switch {
	margin-top: 1px !important;
	padding: 0 2px !important;
	border-radius: 5px;
	background: url({$img_url}) center center no-repeat #ccc;
}
</style>
<script type="text/javascript">
(function ($) {
$(function () {
	var link = $(".wdeb_switch").removeClass("button").addClass("small_switch").find("a")
		text = link.text()
	;
	link.text('').attr("title", text);
});
})(jQuery);
</script>
EoSmallSwitchCss;
	}
}
if (is_admin()) Wdeb_Theme_SmallSwitchButton::serve();