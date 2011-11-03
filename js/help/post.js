(function ($){
$(function () {
/*	
$("#side-info-column").prepend(_wdeb_help_tpl.replace(/%%text%%/, l10WdebHelp.help));
$("#wdeb_show_help").click(function () {
	$("#wdeb_help_inside_wrapper").html($("#wdeb_help_inside_wrapper").text().replace(/%%text%%/, l10WdebHelp.help));
	tb_show('Help', '#TB_inline?width=640&inlineId=wdeb_help_container');
});
*/
$("h2").prev().after(_wdeb_tooltip_tpl.replace(/%%text%%/, l10WdebHelp.new_post)).end().prev('.wdeb_tooltip').css('margin-top', '16px');
$("#titlewrap label").append(_wdeb_tooltip_tpl.replace(/%%text%%/, l10WdebHelp.post_title));
$("#postdivrich").prepend(_wdeb_tooltip_tpl.replace(/%%text%%/, l10WdebHelp.post_body));
$("#submitdiv .hndle span").prepend(_wdeb_tooltip_tpl.replace(/%%text%%/, l10WdebHelp.publish));
$("#tagsdiv-post_tag .hndle span").prepend(_wdeb_tooltip_tpl.replace(/%%text%%/, l10WdebHelp.tags));
$("#categorydiv .hndle span").prepend(_wdeb_tooltip_tpl.replace(/%%text%%/, l10WdebHelp.categories));
	
});
})(jQuery);