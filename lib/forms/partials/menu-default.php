<?php
$menu_items = apply_filters('wdeb_initialize_menu', array());
$menu_items = apply_filters('wdeb_menu_items', $menu_items);
$admin_base = admin_url();
$scheme = preg_match('!^https!', $admin_base) ? 'https://' : 'http://';
$current_request = preg_replace('!^' . preg_quote($admin_base) . '!', '', $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$current_request = admin_url($current_request);
?>
<ul>

<?php foreach ($menu_items as $key=>$item) { ?>
	<?php if ( // Show item if...
		!isset($item['capability']) // ... either no capability set
		|| // ... or
		(isset($item['capability']) && !$item['capability']) // ... capability set to false-ish
		|| // ... or
		(isset($item['capability']) && current_user_can($item['capability'])) // ... user has this capability
	) { ?>
	<?php
	if (isset($item['check_callback']) && $item['check_callback']) {
		// Check custom callback - if we don't get true-ish value back, we shouldn't be here
		if (!call_user_func($item['check_callback'], $item)) continue;
	}
	$url = admin_url($item['url']);
	$item = apply_filters('wdeb_menu-item_before_render', $item);
	?>
	<li <?php echo (($url == $current_request) ? 'class="current"' : '');?> >
		<a href="<?php echo wdeb_expand_url($item['url']); ?>" class="wdeb_menu_link dashboard">
			<img src="<?php echo $item['icon'];?>" alt="" />
			<span class="current"><?php echo $item['title'];?></span>
		</a>
		<div class="wdeb_meta"><?php echo $item['help'];?></div>
	</li>
	<?php } ?>
<?php } ?>


	<li><span>&nbsp;</span></li> <!-- Spacer -->


<?php if ($this->data->get_option('wizard_enabled', 'wdeb_wizard')) { ?>
<!-- Wizard is enabled, add menu entry -->
	<li>
		<a href="<?php echo admin_url('index.php');?>?wdeb_wizard_on" class="dashboard">
			<img src="<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/wizard-mode.png" alt="" />
			<span class="current"><?php _e('Wizard mode', 'wdeb');?></span>
		</a>
		<div class="wdeb_meta">
			<strong><?php _e('Wizard mode', 'wdeb');?></strong>
			<?php _e('Enter guided step-by-step mode', 'wdeb')?>
		</div>
	</li>
<?php } ?>
<?php $auto_enter_roles = $this->data->get_option('auto_enter_role'); ?>
<?php if (!$auto_enter_roles || !wdeb_current_user_can($auto_enter_roles)) { ?>
<!-- Easy mode not forced, so user can toggle between the two -->
	<li>
		<a href="<?php echo admin_url('index.php');?>?wdeb_off" id="wdeb_exit_easy_mode" class="dashboard">
			<img src="<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/home.png" alt="" />
			<span class="current"><?php _e('Exit Easy Mode', 'wdeb');?></span>
		</a>
		<div class="wdeb_meta">
			<strong><?php _e('Exit Easy Mode', 'wdeb');?></strong>
			<?php _e('Return to advanced mode', 'wdeb')?>
		</div>
	</li>
<?php } ?>

<?php if ($this->data->get_option('show_logout') || ($auto_enter_roles && wdeb_current_user_can($auto_enter_roles))) { ?>
<!-- Easy Mode forced, or showing logout link requested. Add Logout link -->
	<li>
		<a href="<?php echo wp_logout_url();?>" class="dashboard">
			<img src="<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/settings.png" alt="" />
			<span class="current"><?php _e('Log Out', 'wdeb');?></span>
		</a>
		<div class="wdeb_meta">
			<strong><?php _e('Log Out', 'wdeb');?></strong>
			<?php _e('Log Out of your website', 'wdeb')?>
		</div>
	</li>
<?php } ?>

</ul>