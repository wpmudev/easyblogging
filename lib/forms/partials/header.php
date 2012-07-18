<?php
global $pagenow, $admin_body_class, $current_screen, $wp_version;
$version = preg_replace('/-.*$/', '', $wp_version);
?>
	<link type="text/css" href="<?php echo WDEB_PLUGIN_THEME_URL ?>/style.css" rel="stylesheet" /> <!-- the layout css file -->
	<link type="text/css" href="<?php echo WDEB_PLUGIN_THEME_URL ?>/css/jquery.cleditor.css" rel="stylesheet" />

<?php if (version_compare($version, '3.3', '<')) { ?>
	<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/jquery-ui-1.8.5.custom.min.js'></script> <!-- jquery UI -->
<?php } ?>
	<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/cufon-yui.js'></script> <!-- Cufon font replacement -->
	<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/ColaborateLight_400.font.js'></script> <!-- the Colaborate Light font -->
	<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/easyTooltip.js'></script> <!-- element tooltips -->
	<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/jquery.tablesorter.min.js'></script> <!-- tablesorter -->

	<!--[if IE 8]>
		<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/excanvas.js'></script>
		<link rel="stylesheet" href="<?php echo WDEB_PLUGIN_THEME_URL ?>/css/IEfix.css" type="text/css" media="screen" />
	<![endif]-->

	<!--[if IE 7]>
		<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/excanvas.js'></script>
		<link rel="stylesheet" href="<?php echo WDEB_PLUGIN_THEME_URL ?>/css/IEfix.css" type="text/css" media="screen" />
	<![endif]-->

	<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/visualize.jQuery.js'></script> <!-- visualize plugin for graphs / statistics -->
	<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/iphone-style-checkboxes.js'></script> <!-- iphone like checkboxes -->
	<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/jquery.cleditor.min.js'></script> <!-- wysiwyg editor -->

	<script type='text/javascript' src='<?php echo WDEB_PLUGIN_THEME_URL ?>/js/custom.js'></script> <!-- the "make them work" script -->

	<?php //do_action('admin_head'); ?>

<style type="text/css">
.wdeb_meta, #wdeb_meta_container {
	display: none;
}
/** Style fixes for WP **/
body {
	min-height: 100%;
	height: auto;
	/*padding-top: 28px;*/
}
html.wp-toolbar { padding-top: 0;}
<?php if ((int)$this->data->get_option('admin_bar')) { ?>
	body { padding-top: 28px; }
	#wpwrap #primary_left #logo { padding-top: 30px; }
<?php } ?>
#add-custom-links.postbox .howto input {
	float: none;
}

.post-new-php .wdeb_help_popup a {
   <?php if( is_admin_bar_showing() ) { ?>
    /*top: -103px !important;*/
   <?php } ?>
}

.wdeb_tooltip {
	background: url(<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/tooltip.png) top left no-repeat;
}
.wdeb_help_popup a {
	background: #fff url(<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/icons/theme_icons/help.png) 10px 4px no-repeat;
}

<?php if (!$this->data->get_option('admin_bar')) { ?>
#wpadminbar, #wp-admin-bar {
	display: none;
}
<?php } ?>

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
.wdeb_wizard_step b, .wdeb_wizard_step br {
	display: none;
}
#primary_right .inner {
    width: 700px;
}
.available-theme a.screenshot {
    width: 200px;
}
}


#wpwrap #primary_left #logo img {
    max-width: 90%;
    height/**/:/**/ auto;
}

#wpwrap #screen-meta {
    position: absolute;
    right: 140px;
    top: 0;
}

/* ----- RTL ----- */
.rtl #primary_right {
	margin-right: 230px;
	margin-left: 0;
}
.rtl #primary_right .inner {
	margin-left: 0;
	float: right;
}
.rtl #wpbody-content {
	background: url("<?php echo WDEB_PLUGIN_THEME_URL ?>/assets/stripe.png") repeat-y fixed top right transparent;
}
.rtl .wdeb_visit_site {
	left: 3px;
}

<?php do_action('wdeb_style-custom_stylesheet_rules'); ?>
</style>

<script type="text/javascript">
(function ($) {
$(function () {

	/************* Tips **************/
	$("#menu li").each(function () {
		var str = $(this).find('.wdeb_meta').text();
		var arr = str.split("\n");
		var title = '';
		$.each(arr, function(idx, el) {
			if (idx > 1) title += " \n";
			title += $.trim(el);
		});
		$(this).attr('title', title);
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
	$("#container")
		.width($(window).width())
		.css('max-width', 'none')
	;
	var width = ($("#container").width() - 320);
	$(".wrap").width(width);
	// AJAX loading circle
	$(".ajax-loading").hide();
	$("#wpbody-content").css("min-height", $(window).height());
});

/* ----- Videos bugfix ----- */
<?php if (class_exists('WPMUDEV_Videos')) { ?>
$(window).load(function () {
	$('.contextual-help-tabs-wrap [id*="wpmudev_vids"]').css({
		"height": $(window).height() + "px",
		"overflow-y": "scroll"
	});
});
<?php } ?>

/* ----- Theme preview fix ----- */
$(function () {
	$('body.themes-php .action-links a[href*="TB_iframe"]').each(function () {
		var $me = $(this);
		$me
			.addClass("thickbox")
			.click(function () {
				var width = $(window).width() - $("#primary_left").width()*2.1;
				var height = $(window).height() - 150;
				var href = $me.attr("href");
				if (href.match(/&(width|height)=[^&]+/)) href = href.replace(/&(width|height)=[^&]+/g, '');
				$me.attr("href", href + "&width=" + width + '&height=' + height);
				return true;
			});
		;
	});
});

<?php do_action('wdeb_script-custom_javascript'); ?>

<?php $auto_enter_role = $this->data->get_option('auto_enter_role'); ?>
<?php if ($this->data->get_option('easy_bar') && (!$auto_enter_role || !wdeb_current_user_can($auto_enter_role))) { ?>
// Add exit easy mode link
$(function () {
	$(".wdeb_visit_site").first().append("<a href='<?php echo WDEB_LANDING_PAGE; ?>?wdeb_off'><?php _e('Exit easy mode', 'wdeb');?></a>");
});
<?php } ?>
})(jQuery);
</script>
</head>

<body id="wdeb-mode" class="wp-admin no-js <?php echo apply_filters( 'admin_body_class', '' ) . " $admin_body_class"; ?>">

<script type="text/javascript">
//<![CDATA[
(function(){
var c = document.body.className;
c = c.replace(/no-js/, 'js');
document.body.className = c;
})();
//]]>
</script>

<div id="wpwrap">

<div id="wpbody-content">

<?php do_action('eab-admin_toolbar-render'); ?>
<?php if ($this->data->get_option('easy_bar')) { ?>
	<div class="wdeb_visit_site">
	<a href="<?php echo site_url();?>"><?php _e('Visit site', 'wdeb');?></a>
	</div>
<?php } ?>

<div id="primary_left">

<div id="logo">
<!--<a href="dashboard.html" title="Dashboard"><img src="assets/logo.png" alt="" /></a> -->
<a href="<?php echo home_url(); ?>" title="<?php echo bloginfo('description'); ?>">
<img src="<?php echo $this->data->get_logo();?>" />
</a>
</div> <!-- logo end -->
<div id="menu"> <!-- navigation menu -->
<?php $this->get_menu_partial(); ?>
</div> <!-- navigation menu end -->
</div> <!-- primary-left end -->


<div id="primary_right">
<div class="inner">

<?php
if ($this->data->get_option('screen_options')) {
	echo "<div>" . screen_meta($current_screen) . "</div>";
}
?>

<!--<div class="notification tip" id="wdeb_meta_container">
<span></span>
<div class="text">
<p></p>
</div>
</div>-->

