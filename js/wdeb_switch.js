(function ($) {
$(function () {
	var el = $("#wp-admin-bar-my-account").length ? $("#wp-admin-bar-my-account") : $("#site-heading");
	el.after(
		'<div class="wdeb_switch admin_area button"><a href="' + _wdebLandingPage + '">' + l10WdebSwitch.activate + '</a></div>'
	);
});
})(jQuery);