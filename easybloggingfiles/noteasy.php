<?php
    require_once(ABSPATH . 'wp-admin/admin.php');
    require_once(ABSPATH . 'wp-admin/includes/admin.php');
    
    $title = __('Whoops!', $this->localizationDomain);

    remove_action( 'admin_footer', 'bp_core_admin_bar');
?>
<script type="text/javascript">
    var href = '<?php echo admin_url('%%replace%%.php') . '?easyadmin=off'; ?>';
    if (querystring != '') {
        href += '&' + querystring;
    }
    jQuery("#to_advanced_page.noteasy").attr('href',href.replace('%%replace%%',advancedpage));
</script>
<style type="text/css">
    .error { display: none; }
</style>
   <div id="wizard_complete" class="wrap"> <div id="wizard_step">
        <?php screen_icon(); ?>
        <h2><?php echo esc_html( $title ); ?></h2>
        <div id="welcome-area">
            <p><?php _e( 'Whoops! This function is not available in the Easy Admin area. You\'ll have to switch over to the Advanced Admin area to access this page.', $this->localizationDomain ) ?></p>
            <div id="admin_area_step_new_post" class="admin_area_step">
                <a id="to_advanced_page" class='wizard_button noteasy' href="<?php echo admin_url('%%replace%%.php?easyadmin=off');?>" target="_top">
                    <div id="admin_area_to_advanced" class="admin_area button">
		            <?php _e( 'Go to the Advanced Admin Area', $this->localizationDomain ) ?>
	            </div></a>
                <div class="clear"></div>
            </div>
            <div class="clear"></div>
            <p>&nbsp;</p>
        </div>
    </div>
      </div>