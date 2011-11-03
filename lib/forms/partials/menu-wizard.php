<?php
// TMP
$wizard_steps = apply_filters('wdeb_wizard_steps', array());
$admin_base = admin_url();
$scheme = preg_match('!^https!', $admin_base) ? 'https://' : 'http://';
$current_request = preg_replace('!^' . preg_quote($admin_base) . '!', '', $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
$current_request = admin_url($current_request);
?>

<ul>

<?php $count = 1; foreach ($wizard_steps as $step) { ?>
	<?php $url = site_url($step['url']); ?>
<li class="wdeb_wizard_step <?php echo (($url == $current_request) ? 'current' : '');?>" >
	<a href="<?php echo $url;?>" class="wdeb_menu_link">
		<b>Step <?php echo $count++;?>:</b> <br />
		<?php echo preg_replace('/\s/', '&nbsp;', $step['title']);?>
	</a>
	<div class="wdeb_meta">
		<?php echo $step['help']; ?>
	</div>
</li>

<?php } ?>


<li><span>&nbsp;</span></li> <!-- Spacer -->

<li>
	<a href="#" class="wdeb_menu_link" id="wdeb_wizard_next_step">
		<img src="<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/forward.png" alt="" />
		<span><?php _e('Next step', 'wdeb');?></span>
	</a>
	<div class="wdeb_meta">
		<?php _e('Proceed to the next step', 'wdeb') ?>
	</div>
</li>

<li><span>&nbsp;</span></li> <!-- Spacer -->

<?php if ($this->data->get_option('wizard_enabled', 'wdeb_wizard')) { ?>
<!-- Wizard is enabled, add menu entry -->
	<li>
		<a href="<?php echo admin_url('index.php');?>?wdeb_wizard_off" class="dashboard">
			<img src="<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/wizard-mode.png" alt="" />
			<span class="current"><?php _e('Exit Wizard mode', 'wdeb');?></span>
		</a>
		<div class="wdeb_meta">
			<strong><?php _e('Exit Wizard mode', 'wdeb');?></strong>
			<?php _e('Exit guided step-by-step mode', 'wdeb')?>
		</div>
	</li>
<?php } ?>
<?php $auto_enter_roles = $this->data->get_option('auto_enter_role'); ?>
<?php if (!$auto_enter_roles || wdeb_current_user_can($auto_enter_roles)) { ?>
<!-- Easy mode not forced, so user can toggle between the two -->
	<li>
		<a href="<?php echo admin_url('index.php');?>?wdeb_off" id="wdeb_exit_easy_mode" class="dashboard">
			<img src="<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/home.png" alt="" />
			<span class="current"><?php _e('Exit Easy Mode', 'wdeb');?></span>
		</a>
		<div class="wdeb_meta">
			<strong><?php _e('Exit Easy Mode', 'wdeb');?></strong>
			<?php _e('Return to standard mode', 'wdeb')?>
		</div>
	</li>
<?php } ?>
<?php if ($auto_enter_roles && !wdeb_current_user_can($auto_enter_roles)) { ?>
<!-- Easy Mode forced. Add Logout link -->
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

<style type="text/css">
#menu li.current * {
	color: #fff;
}
</style>
<script type="text/javascript">
(function ($) {
$(function () {

function gotoFirstStep() {
	var $first = $("#menu ul li.wdeb_wizard_step:first");
	if (!$first.length) return false;

	var href = $first.find('a').attr('href');
	$first.find('a').click();
	window.location = href;
}

function initialize () {
	var $current = $("#menu ul li.current");
	if (!$current.length) {
		gotoFirstStep();
		return false;
	}
}

$("#wdeb_wizard_next_step").click(function () {
	var $current = $("#menu ul li.current");
	if (!$current.length) {
		gotoFirstStep();
		return false;
	}

	var $next = $current.next('.wdeb_wizard_step');
	if (!$next.length) return false;

	var href = $next.find('a').attr('href');
	$next.find('a').click();
	window.location = href;
	return false;
});

initialize();

});
})(jQuery);
</script>