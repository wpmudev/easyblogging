<div class="wrap">
	<h2><?php _e('Easy Wizard settings','wdeb'); ?></h2>

<?php if (WP_NETWORK_ADMIN) { ?>
	<form action="settings.php" method="post" enctype="multipart/form-data">
<?php } else { ?>
	<form action="options.php" method="post" enctype="multipart/form-data">
<?php } ?>

	<?php settings_fields('wdeb_wizard'); ?>
	<?php do_settings_sections('wdeb_wizard'); ?>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
	</form>

</div>

<div id="wdeb_step_edit_dialog" style="display:none">
	<p>
		<label><?php _e("Title", 'wdeb'); ?></label>
			<input class="widefat" id="wdeb_step_edit_dialog_title" />
	</p>
	<p>
		<label><?php _e("URL", 'wdeb'); ?></label>
			<input class="widefat" id="wdeb_step_edit_dialog_url" />
	</p>
	<p>
		<label><?php _e("Help", 'wdeb'); ?></label>
			<textarea class="widefat" id="wdeb_step_edit_dialog_help"></textarea>
	</p>
</div>

<style type="text/css">
.wdeb_step {
	width: 400px;
	height: 50px;
	background: #eee;
	margin-bottom: 1em;
	cursor: move;
}
.wdeb_step h4 {
	margin: 0;
	float: left;
}
.wdeb_step .wdeb_step_actions {
	float: right;
}
</style>
<script type="text/javascript">
(function ($) {
$(function () {

function updateUrlPreview () {
	var type = "<?php echo site_url(); ?>" + $("#wdeb_last_wizard_step_url_type").val();
	var url = $("#wdeb_last_wizard_step_url").val();

	var preview = type + url;

	$("#wdeb_url_preview code").text(preview);

	return true;
}

if (typeof $("#wdeb_steps").sortable != "undefined") {
	$("#wdeb_steps")
		.sortable({
			"update": function () {
				$("#wdeb_steps li").each(function (idx) {
					$(this).find('h4 .wdeb_step_count').html(idx+1);
				});
			}
		})
		.disableSelection()
	;
}

$(".wdeb_step_delete").click(function () {
	$(this).parents('li.wdeb_step').remove();
	return false;
});

$("#wdeb_last_wizard_step_url_type").change(updateUrlPreview);
$("#wdeb_last_wizard_step_url").keyup(updateUrlPreview);

$(".wdeb_step_edit").click(function () {
	var $parent = $(this).parents('li.wdeb_step');
	var $url = $parent.find('input:hidden.wdeb_step_url');
	var $title = $parent.find('input:hidden.wdeb_step_title');
	var $help = $parent.find('input:hidden.wdeb_step_help');
	var $titleSpan = $parent.find('h4 .wdeb_step_title');

	$("#wdeb_step_edit_dialog_title").val($title.val());
	$("#wdeb_step_edit_dialog_url").val($url.val());
	$("#wdeb_step_edit_dialog_help").val($help.val());

	$("#wdeb_step_edit_dialog").dialog({
		"title": $title.val(),
		"modal": true,
		"width": 600,
		"close": function () {
			$title.val($("#wdeb_step_edit_dialog_title").val());
			$titleSpan.html($("#wdeb_step_edit_dialog_title").val());
			$url.val($("#wdeb_step_edit_dialog_url").val());
			$help.val($("#wdeb_step_edit_dialog_help").val());
		}
	});

	return false;
});

updateUrlPreview();

});
})(jQuery);
</script>