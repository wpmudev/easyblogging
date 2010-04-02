<?php
    require_once(ABSPATH . 'wp-admin/admin.php');
    
    $title = __('Easy Admin Area', $this->localizationDomain);
    
    wp_enqueue_script('jquery');
    wp_enqueue_script('hoverintent');
    wp_enqueue_script('cluetip', $this->thispluginurl.'js/cluetip-1.0.6/jquery.cluetip.js');
    wp_enqueue_style( 'cluetip', $this->thispluginurl.'js/cluetip-1.0.6/jquery.cluetip.css');
    
    require_once(ABSPATH . 'wp-admin/admin-header.php');
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery(".tab_tooltip").cluetip({splitTitle: "|", cluetipClass: "rounded", dropShadow: false, arrows: true});
    });
</script>
   <div class="wrap">
        <div id="easy-admin-area">

            <div id="easy_admin_tabs" class="ui-tabs-nav">
                <ul>
                    <li><a id="post-new-php" href="<?php bloginfo('wpurl'); ?>/wp-admin/?frame=post-new" class="tab_tooltip" title="<?php _e( 'New Post|Create a new post', $this->localizationDomain ) ?>"><span><?php _e( 'New Post', $this->localizationDomain ) ?></span></a></li>
                    <li><a id="edit-php" href="<?php bloginfo('wpurl'); ?>/wp-admin/?frame=edit" class="tab_tooltip" title="<?php _e( 'My Posts|Edit your posts', $this->localizationDomain ) ?>"><span><?php _e( 'My Posts', $this->localizationDomain ) ?></span></a></li>
                    <li><a id="page-new-php" href="<?php bloginfo('wpurl'); ?>/wp-admin/?frame=page-new" class="tab_tooltip" title="<?php _e( 'New Page|Create a new page', $this->localizationDomain ) ?>"><span><?php _e( 'New Page', $this->localizationDomain ) ?></span></a></li>
                    <li><a id="edit-pages-php" href="<?php bloginfo('wpurl'); ?>/wp-admin/?frame=edit-pages" class="tab_tooltip" title="<?php _e( 'My Pages|Edit your pages', $this->localizationDomain ) ?>"><span><?php _e( 'My Pages', $this->localizationDomain ) ?></span></a></li>
                    <li><a id="edit-comments-php" href="<?php bloginfo('wpurl'); ?>/wp-admin/?frame=edit-comments" class="tab_tooltip" title="<?php _e( 'Comments|Manage the comments on your blog', $this->localizationDomain ) ?>"><span>Comments</span></a></li>
                    <li><a id="themes-php" href="<?php bloginfo('wpurl'); ?>/wp-admin/?frame=themes" class="tab_tooltip" title="<?php _e( 'Change Theme|Change to a different theme', $this->localizationDomain ) ?>"><span><?php _e( 'Change Theme', $this->localizationDomain ) ?></span></a></li>
                    <li><a id="widgets-php" href="<?php bloginfo('wpurl'); ?>/wp-admin/?frame=widgets" class="tab_tooltip" title="<?php _e( 'Customize Design|Customize the look and content of your design', $this->localizationDomain ) ?>"><span><?php _e( 'Customize Design', $this->localizationDomain ) ?></span></a></li>
                    <li style="float: right;"><a id="profile-php" class="tab_tooltip" href="<?php bloginfo('wpurl'); ?>/wp-admin/?frame=profile" title="<?php _e( 'Profile|Edit your profile', $this->localizationDomain ) ?>"><span><?php _e( 'Profile', $this->localizationDomain ) ?></span></a></li>
                    <?php do_action('easy_admin_more_tabs'); ?>
                </ul>
            </div>
            <div class="clear"></div>
        </div>
<?php
    require_once(ABSPATH . 'wp-admin/admin-footer.php');
?>
    </div>
