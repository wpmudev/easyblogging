<div class="wrap">
	<h2>Easy Blogging settings</h2>

<?php if (WP_NETWORK_ADMIN) { ?>
	<form action="settings.php" method="post" enctype="multipart/form-data">
<?php } else { ?>
	<form action="options.php" method="post" enctype="multipart/form-data">
<?php } ?>

	<?php settings_fields('wdeb'); ?>
	<?php do_settings_sections('wdeb_options_page'); ?>
	<p class="submit">
		<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
	</p>
	</form>

</div>
<script type="text/javascript">
(function ($) {
	
$(function () {
	$("#wdeb-logo-remove_logo").on('click', function () {
		$("#wdeb-logo-custom_logo").val('');
		$("#wdeb-logo-logo_output").remove();
		return false;
	});
});
})(jQuery);
</script>
