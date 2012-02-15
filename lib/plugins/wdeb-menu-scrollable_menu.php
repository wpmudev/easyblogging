<?php
/*
Plugin Name: Scrollable menu
Description: Allows menu to scroll on small screens. Also allows for more verbose wizard step titles. 
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0.1
Author: Ve Bailovity (Incsub)
*/

class Wdeb_Menu_ScrollableMenu {
	
	private function __construct () {}
	
	public static function serve () {
		$me = new Wdeb_Menu_ScrollableMenu;
		$me->_add_hooks();
	}
	
	private function _add_hooks () {
		add_filter('wdeb_menu-wizard-non_breaking_space', array($this, 'handle_whitespace'));
		add_action('wdeb_script-custom_javascript', array($this, 'handle_javascript'));
		add_action('wdeb_style-custom_stylesheet_rules', array($this, 'handle_css'));
	}
	
	function handle_whitespace () {
		return ' ';
	}

	function handle_javascript () {
		echo '
// Adapt menu to small screen sizes
function wdeb_menu_make_scrollable () {
	var $menu = $("#menu");
	var top_pos = $menu.height() + $menu.position().top;
	if (top_pos < $(window).height()) return;
	
	// Pop the scroll, as necessary
	$menu.height($(window).height() - $menu.position().top);
	$("#primary_left")
		// Fix positioning issues
		.css("z-index", "999")
		// Do stuffs
		.hover(
			function () {
				if ($menu.is(".hover-active")) return false;
				$menu
					.addClass("hover-active")
					.find("ul")
						.css({
							"position": "relative"
						})
						.end()
					.css({
						"overflow-y": "scroll",
						"overflow-x": "hidden"
					})
					.width($menu.width() - 15)
				;
			},
			function () {
				$menu
					.removeClass("hover-active")
					.css({
						"overflow-y": "hidden",
						"overflow-x": "auto",
						"width": "100%"
					})
				;
			}
		)
	;
}
$(window)
	.load(wdeb_menu_make_scrollable)
	.resize(function () {
		// Reset scrolling first
		$("#primary_left").unbind("mouseenter").unbind("mouseleave");
		$("#menu")
			.removeClass("hover-active")
			.find("ul")
				.css({
					"position": "static"
				})
				.end()
			.css({
				"height": "auto",
				"overflow-y": "hidden",
				"overflow-x": "hidden"
			})
		;
		// Make menu scrollable again, if appropriate
		wdeb_menu_make_scrollable();
	});
;';
	}
	
	function handle_css () {
		$theme_url = WDEB_PLUGIN_THEME_URL;
		echo <<<EoWdebScrollableMenuCss
#menu .wdeb_wizard_step a {
	height: auto;
}
#menu .wdeb_wizard_step.current {
	background:url('{$theme_url}/assets/menu_current-large.png') top right no-repeat;
}
@media (max-width: 1280px) {
	#menu ul li a, #menu ul li a:hover {
	    height: auto !important;
	}
}
EoWdebScrollableMenuCss;
	}
}

if (is_admin()) Wdeb_Menu_ScrollableMenu::serve();
