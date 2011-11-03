(function ($) {
$(function () {
	$('#site-heading').after(
		'<div class="wdeb_switch admin_area button"><a href="' + _wdebLandingPage + '?wdeb_on">' + l10WdebSwitch.activate + '</a></div>'
	);
});
})(jQuery);