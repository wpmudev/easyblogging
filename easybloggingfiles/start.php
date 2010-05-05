<?php
    require_once(ABSPATH . 'wp-admin/admin.php');
    
    $title = __('Welcome!', $this->localizationDomain);

    remove_action( 'admin_footer', 'bp_core_admin_bar');
    
    require_once(ABSPATH . 'wp-admin/admin-header.php');
?>
<style type="text/css">
    .error { display: none; }
</style>
   <div class="wrap">
        <?php screen_icon(); ?>
        <h2><?php echo esc_html( $title ); ?></h2>
        <div id="welcome-area">
                <div id="welcome-area-box">
            <h4><?php _e( 'Welcome! Please choose the way you would like to use', $this->localizationDomain ) ?> <?php bloginfo('name'); ?>:</h4>

            <div id="admin_area_step_change_theme" class="admin_area_step">
                <a class='wizard_button' href="<?php echo admin_url('index.php?easyadmin=on');?>" style="color: #ffffff;" target="_top"><div id="admin_area_to_easy" class="admin_area button">
		<?php _e( 'Go to the Easy Admin Area', $this->localizationDomain ) ?>
</div></a><div class="clear"></div>
	<?php _e( 'Very simple and easy', $this->localizationDomain ) ?>
                <?php global $easy_admin_wizard_var; echo ($easy_admin_wizard_var)?', starts with a step-by-step quick start to get you up and running fast!':' for beginners!'; ?>
            </div>
            <div id="admin_area_step_new_post" class="admin_area_step">
                <a class='wizard_button' href="<?php echo admin_url('index.php?easyadmin=off');?>" target="_top"><div id="admin_area_to_advanced" class="admin_area button">
		<?php _e( 'Go to the Advanced Admin Area', $this->localizationDomain ) ?>
	</div></a><div class="clear"></div>
		<?php _e( 'For advanced bloggers, gives you more features and options!', $this->localizationDomain ) ?>
            </div>
            <div class="clear"></div>
            <p>&nbsp;</p>
            </div>
        </div>
<?php
    require_once(ABSPATH . 'wp-admin/admin-footer.php');
?>
    </div>
