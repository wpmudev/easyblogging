<div class="wrap">
	<h2><?php _e("Easy Blogging Add-ons", 'wdeb'); ?></h2>

<?php
	$all = Wdeb_PluginsHandler::get_all_plugins();
	$active = Wdeb_PluginsHandler::get_active_plugins();
	$sections = array('thead', 'tfoot');
	echo "<table class='widefat'>";
	foreach ($sections as $section) {
		echo "<{$section}>";
		echo '<tr>';
		echo '<th width="30%">' . __('Add-on name', 'wdeb') . '</th>';
		echo '<th>' . __('Add-on description', 'wdeb') . '</th>';
		echo '</tr>';
		echo "</{$section}>";
	}
	echo "<tbody>";
	foreach ($all as $plugin) {
		$plugin_data = Wdeb_PluginsHandler::get_plugin_info($plugin);
		if (!@$plugin_data['Name']) continue; // Require the name
		$is_active = in_array($plugin, $active);
		echo "<tr>";
		echo "<td width='30%'>";
		echo '<b>' . $plugin_data['Name'] . '</b>';
		echo "<br />";
		echo ($is_active
			?
			'<a href="#deactivate" class="wdeb_deactivate_plugin" wdeb:plugin_id="' . esc_attr($plugin) . '">' . __('Deactivate', 'wdeb') . '</a>'
			:
			'<a href="#activate" class="wdeb_activate_plugin" wdeb:plugin_id="' . esc_attr($plugin) . '">' . __('Activate', 'wdeb') . '</a>'
		);
		echo "</td>";
		echo '<td>' .
			$plugin_data['Description'] .
			'<br />' .
			sprintf(__('Version %s', 'wdeb'), $plugin_data['Version']) .
			'&nbsp;|&nbsp;' .
			sprintf(__('by %s', 'wdeb'), '<a href="' . $plugin_data['Plugin URI'] . '">' . $plugin_data['Author'] . '</a>') .
		'</td>';
		echo "</tr>";
	}
	echo "</tbody>";
	echo "</table>";
?>

<script type="text/javascript">
(function ($) {
$(function () {
	$(".wdeb_activate_plugin").click(function () {
		var me = $(this);
		var plugin_id = me.attr("wdeb:plugin_id");
		$.post(ajaxurl, {"action": "wdeb_activate_plugin", "plugin": plugin_id}, function (data) {
			window.location = window.location;
		});
		return false;
	});
	$(".wdeb_deactivate_plugin").click(function () {
		var me = $(this);
		var plugin_id = me.attr("wdeb:plugin_id");
		$.post(ajaxurl, {"action": "wdeb_deactivate_plugin", "plugin": plugin_id}, function (data) {
			window.location = window.location;
		});
		return false;
	});
});
})(jQuery);
</script>

</div>