<?php
/**
 * New users Start page.
 *
 * This page is shown exactly ONCE per session, if hijacking the start page
 * has been enabled.
 */
?>
<html>
<head>
<title><?php _e("Start here",'wdeb'); ?></title>

	<link type="text/css" href="<?php echo WDEB_PLUGIN_THEME_URL ?>/style.css" rel="stylesheet" /> <!-- the layout css file -->
	<link type="text/css" href="<?php echo WDEB_PLUGIN_THEME_URL ?>/css/jquery.cleditor.css" rel="stylesheet" />

	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/jquery-1.4.2.min.js"></script>	<!-- jquery library -->

	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/jquery-ui-1.8.5.custom.min.js"></script> <!-- jquery UI -->

	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/cufon-yui.js"></script> <!-- Cufon font replacement -->
	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/ColaborateLight_400.font.js"></script> <!-- the Colaborate Light font -->
	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/easyTooltip.js"></script> <!-- element tooltips -->
	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/jquery.tablesorter.min.js"></script> <!-- tablesorter -->

	<!--[if IE 8]>
		<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/excanvas.js'></script>
		<link rel="stylesheet" href="<?php echo WDEB_PLUGIN_THEME_URL ?>/css/IEfix.css" type="text/css" media="screen" />
	<![endif]-->

	<!--[if IE 7]>
		<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/excanvas.js'></script>
		<link rel="stylesheet" href="<?php echo WDEB_PLUGIN_THEME_URL ?>/css/IEfix.css" type="text/css" media="screen" />
	<![endif]-->

	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/visualize.jQuery.js"></script> <!-- visualize plugin for graphs / statistics -->
	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/iphone-style-checkboxes.js"></script> <!-- iphone like checkboxes -->
	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/jquery.cleditor.min.js"></script> <!-- wysiwyg editor -->

	<script type='text/javascript' src="<?php echo WDEB_PLUGIN_THEME_URL ?>/js/custom.js"></script> <!-- the "make them work" script -->

<style type="text/css">
.wdeb_meta, #wdeb_meta_container {
	display: none;
}
dt {
	font-weight: bold;
}
dd {
	margin-bottom: 1em;
	margin-left: 2em;
}

/** Style fixes for WP **/
body {
	min-height: 100%;
	height: auto;
}


.post-new-php .wdeb_help_popup a {
   <?php if( is_admin_bar_showing() ) { ?>
    top: -103px !important;
   <?php } ?>
}

.wdeb_tooltip {
	background: url(<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/tooltip.png) top left no-repeat;
}
.wdeb_help_popup a {
	background: #fff url(<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/help.png) 10px 4px no-repeat;
}

#wpadminbar {
	display: none;
}
#start-page .inner {
  padding: 50px 30px 0px 50px;
}
#start-page .start-headline {
  width: 90%;
  float:left;
  font-size: 18px;
  color: #555;
  margin: 0px 0px 25px;
  padding: 0px;
  line-height: 24px;
}

#start-page dl {
  width: 100%;
  float:left;
  margin: 0px;
  padding: 0px;
}
#start-page dl dt {
  width: 100%;
  float:left;
  font-size: 16px;
  color: #555;
  margin: 0px;
  padding: 0px;
}

#start-page dl dd {
  width: 100%;
  float:left;
  font-size: 13px;
  color: #676767;
  margin: 0px 0px 25px;
  padding: 10px 0px;
}
#start-page p.start-remember {
  color: #888;
  font-size: 12px;
  font-style: italic;
  display: block;
  width: 100%;
  float:left;
}
#start-page #primary_right .inner small a, #start-page #primary_right .inner small a:hover {
  font-size: 11px !important;
  	padding: 3px 6px !important;
}

@media (max-width: 1280px) {
#menu ul li a, #menu ul li a:hover {
    height: 25px !important;
}
#menu ul li a img {
    height: 22px;
    margin: 1px 4px 4px 4px;
    width: 22px;
}
#menu ul li a span {
    padding: 4px 14px;
}
#primary_right .inner {
    width: 700px;
}
.available-theme a.screenshot {
    width: 200px;
}
}

</style>


<script type="text/javascript">
(function ($) {
$(function () {

/************* Tips **************/
$("#menu li").each(function () {
	$(this).attr('title', $(this).find('.wdeb_meta').text());
});

/************* Notifications **************/
if ($('#menu li.current .wdeb_meta').length) {
	$("#wdeb_meta_container")
		.show()
		.find(".text p")
		.html($('#menu li.current .wdeb_meta').html())
	;
	/*
	setTimeout(function () {
		$("#wdeb_meta_container .text p").empty();
		$("#wdeb_meta_container").hide('slow');
	}, 2000);
	*/
}

/************* Fix WP **************/

// Page wrapper width


});
})(jQuery);
</script>

</head>

<body id="start-page">

<div id="wpwrap">
<div id="wpbody-content">

			<div id="primary_left">

				<div id="logo">
					<!-- <a href="dashboard.html" title="Dashboard"><img src="assets/logo.png" alt="" /></a> -->
					<a href="<?php echo home_url(); ?>" title="<?php echo bloginfo('description'); ?>">
                    <img src="<?php echo $this->data->get_logo();?>" />
                    </a>
				</div> <!-- logo end -->

				<div id="menu"> <!-- navigation menu -->
					<ul>
						<li class="tooltip" title="<strong><?php _e('Easy mode', 'wdeb');?></strong><br /><?php _e('I want to start in Easy mode', 'wdeb')?>">
							<a href="<?php echo WDEB_LANDING_PAGE;?>?wdeb_on" class="wdeb_menu_link dashboard">
								<img src="<?php echo WDEB_PLUGIN_URL ?>/img/easy-mode.png" alt="" />
								<span class="current"><?php _e('Easy mode', 'wdeb');?></span>
							</a>



						</li>
					<?php if ($this->data->get_option('wizard_enabled', 'wdeb_wizard')) { ?>
					<!-- Wizard is enabled, add menu entry -->
						<li class="tooltip" title="<strong><?php _e('Wizard mode', 'wdeb');?></strong><br /><?php _e('I want to start in a guided, step-by-step mode', 'wdeb')?>">
							<a href="<?php echo WDEB_LANDING_PAGE;?>?wdeb_on&wdeb_wizard_on" class="wdeb_menu_link dashboard">
							  <img src="<?php echo WDEB_PLUGIN_URL ?>/img/wizard-mode.png" alt="" />
								<span class="current"><?php _e('Wizard mode', 'wdeb');?></span>
							</a>

						</li>
					<?php } ?>
						<li class="tooltip" title="<strong><?php _e('Standard mode', 'wdeb');?></strong><br /><?php _e('I want to start in Standard mode', 'wdeb')?>">
							<a href="index.php?wdeb_off" class="wdeb_menu_link dashboard">
							   <img src="<?php echo WDEB_PLUGIN_URL ?>/img/advance-mode.png" alt="" />
								<span class="current"><?php _e('Standard mode', 'wdeb');?></span>
							</a>

						</li>
					</ul>
				</div> <!-- navigation menu end -->
			</div> <!-- sidebar end -->


			<div id="primary_right">
				<div class="inner">

<?php if(is_multisite()) {
                    global $blog_id, $current_site, $current_blog; ?>

<?php
$current_site = get_current_site();
$current_network_site = get_current_site_name(get_current_site());

?>

<h1><?php printf(__('Welcome to %s', 'wdeb'), $current_network_site->site_name );?></h1>

                    <?php } else { ?>

					<h1><?php printf(__('Welcome to %s', 'wdeb'), get_bloginfo('name'));?></h1>

                    <?php } ?>

       <div class="start-headline"><?php printf(__("Select how you'd like to use your site - %s (you can change at any time)", 'wdeb'), get_bloginfo('name'));?></div>

					<!--<div class="notification tip">
						<span></span>
						<div class="text">

						</div>
					</div>-->

					<dl>
						<dt><?php _e('Easy mode', 'wdeb');?>&nbsp;&nbsp;<small><a href="<?php echo WDEB_LANDING_PAGE;?>?wdeb_on">Activate</a></small></dt>
						<dd><?php _e('Great for beginners, easily and quickly manage your blog', 'wdeb');?></dd>

					<?php if ($this->data->get_option('wizard_enabled', 'wdeb_wizard')) { ?>
					<!-- Wizard is enabled, add description -->
						<dt><?php _e('Wizard mode', 'wdeb');?>&nbsp;&nbsp;<small><a href="<?php echo WDEB_LANDING_PAGE;?>?wdeb_on&wdeb_wizard_on">Activate</a></small></dt>
						<dd><?php _e('Even greater for beginners, offers a guided step-by-step tour of your blog', 'wdeb');?></dd>
					<?php } ?>

						<dt><?php _e('Standard mode', 'wdeb');?>&nbsp;&nbsp;<small><a href="index.php?wdeb_off">Activate</a></small></dt>
						<dd><?php _e('Allows for fine-tuning your blog', 'wdeb');?></dd>
					</dl>

					<p class="start-remember"><?php _e('Remember, no matter which mode you select now, you can switch back at any time' , 'wdeb');?></p>



				</div> <!-- inner -->
			</div> <!-- primary_right -->

		</div> <!-- wpbody-content -->
	</div> <!-- wpwrap -->
</body>
</html>