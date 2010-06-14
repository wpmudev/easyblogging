<?php
    require_once(ABSPATH . 'wp-admin/admin.php');
    
    $title = __('Welcome!', $this->localizationDomain);

    remove_action( 'admin_footer', 'bp_core_admin_bar');
    
    require_once(ABSPATH . 'wp-admin/admin-header.php');
?>
<style type="text/css">
    .error { display: none; }
</style>

<div id="wizard_complete" class="wrap"> <div id="wizard_step">
<?php screen_icon(); ?>
<h2><?php echo esc_html( $title ); ?></h2>

<div id="welcome-area">

<h3><?php _e( 'Welcome! Please choose the way you would like to use', $this->localizationDomain ) ?> <?php bloginfo('name'); ?>:</h3>
<p>&nbsp;</p>


<div id="view_your_blog" class="admin_area_step">
<div class="complete_button">
<a href="<?php echo admin_url('index.php?easyadmin=on');?>">
<?php _e( 'Go to the Easy Admin Area', $this->localizationDomain ) ?>
</a>
</div>
<p><?php _e( 'Very simple and easy', $this->localizationDomain ) ?>
<?php global $easy_admin_wizard_var; echo ($easy_admin_wizard_var)?', starts with a step-by-step quick start to get you up and running fast!':' for beginners!'; ?></p>
</div>


<div id="view_your_blog" class="admin_area_step">
<div class="complete_button">
<a href="<?php echo admin_url('index.php?easyadmin=off');?>" target="_top">
<?php _e( 'Go to the Advanced Admin Area', $this->localizationDomain ) ?>
</a></div>
<p><?php _e( 'For advanced bloggers, gives you more features and options!', $this->localizationDomain ) ?></p>
</div>

<p>&nbsp;</p>

</div>

<?php require_once(ABSPATH . 'wp-admin/admin-footer.php'); ?>
</div></div>
