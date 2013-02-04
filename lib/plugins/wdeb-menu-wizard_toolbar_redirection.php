<?php
/*
Plugin Name: Admin toolbar redirection for Wizard mode
Description: Redirects non-wizard links from Admin toolbar.
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdeb_Menu_WizardToolbarRedirection {

	private function __construct () {}

	public static function serve () {
		$me = new Wdeb_Menu_WizardToolbarRedirection;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('wdeb-menu-wizard-after_menu_items', array($this, 'output_javascript'));
	}

	function output_javascript () {
		$confirmation_msg = esc_js(__('Following this link will exit the Wizard mode. Are you sure you want to proceed?', 'wdeb'));
		echo <<<EoWizardRedirectionJs
<script type="text/javascript">
(function ($) {
$(function () {
	var links = $("#wpadminbar a")
	;
	links.each(function () {
		var me = $(this)
			href = me.attr("href"),
			new_href = href,
			separator = href.match(/\?/) ? '&' : '?',
			in_menu = $('.wdeb_wizard_step a[href="' + href + '"]')
		;
		if (in_menu.length) return true; // Link exists in the menu, no need to rebind
		if (href.match(/^#/)) return true; // Don't do this for local links

		new_href += separator + 'wdeb_off';

		me
			.attr("href", new_href)
			.off("click")
			.on("click", function () {
				if (!confirm("{$confirmation_msg}")) return false;
				return true;
			})
		;
	});
});
})(jQuery);
</script>
EoWizardRedirectionJs;
	}
}
if (is_admin()) Wdeb_Menu_WizardToolbarRedirection::serve();