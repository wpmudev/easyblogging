(function ($) {
$(function () {
	
var __oldSentToEditor;

/* --- Sortable table --- */
$("table#wdeb_show_hide_root tbody").sortable({
	"items": "tr",
	"containment": "tbody"
});

/* --- (Un)Check all --- */
$(".wdeb_check_all_items").click(function () {
	$("table#wdeb_show_hide_root tbody input:checkbox").attr("checked", true);
	return false;
});
$(".wdeb_uncheck_all_items").click(function () {
	$("table#wdeb_show_hide_root tbody input:checkbox").attr("checked", false);
	return false;
});

/* --- Choosing icon --- */
$("#wdeb_menu_items-new-icon-trigger").click(function () {
	var height = jQuery(window).height*0.35;
	tb_show("&nbsp;", _wdeb_menu_items.admin_base + "media-upload.php?type=video&TB_iframe=1&width=640&height="+height);
	__oldSentToEditor = window.send_to_editor;
	window.send_to_editor = function (html) {
		var $el = $(html);
		$("#wdeb_menu_items-new-icon").val($el.attr("href"));
		$("#wdeb_menu_items-new-icon-target").html('<img src="' + $el.attr("href") + '" />');
		tb_remove();
		window.send_to_editor = __oldSentToEditor;
	};
	return false;
});

/* --- Remove menu item --- */
$(".wdeb_remove_menu_item").click(function () {
	$.post(_wdeb_menu_items.ajax_url, {
		"action": "wdeb_menu_items_remove_my_item",
		"url_id": $(this).parents("tr").find("input.wdeb_menu_items-url_id").val()
	}, function (data) {
		window.location.reload();
	});
	return false;
});

/* --- Resets --- */
$("#wdeb_menu_items-reset_order").click(function () {
	if (!confirm(l10nMenuItems.reset_order_confirmation)) return false;
	$.post(_wdeb_menu_items.ajax_url, {
		"action": "wdeb_menu_items_reset_order"
	}, function (data) {
		window.location.reload();
	});
	return false;
});
$("#wdeb_menu_items-reset_items").click(function () {
	if (!confirm(l10nMenuItems.reset_items_confirmation)) return false;
	$.post(_wdeb_menu_items.ajax_url, {
		"action": "wdeb_menu_items_reset_items"
	}, function (data) {
		window.location.reload();
	});
	return false;
});
$("#wdeb_menu_items-reset_all").click(function () {
	if (!confirm(l10nMenuItems.reset_all_confirmation)) return false;
	$.post(_wdeb_menu_items.ajax_url, {
		"action": "wdeb_menu_items_reset_all"
	}, function (data) {
		window.location.reload();
	});
	return false;
});

$("#wdeb_menu_items-manual_capability").click(function () {
	var $me = $(this);
	var $select = $("#wdeb_menu_items-new-capability");
	if (!$select.length) return false;
	
	$select.replaceWith(
		'<input type="text" class="widefat" id="' + 
			$select.attr("id") + '" name="' + 
			$select.attr("name") + '" value="' + 
			$select.val() + 
		'" />'
	);
	$me.remove();
	
	return false;
});

});
})(jQuery);