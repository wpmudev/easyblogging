<?php

if (!class_exists('easy_admin')) {
    class easy_admin {
        //This is where the class variables go, don't forget to use @var to tell what they're for
        /**
        * @var string The options string name for this plugin
        */
        var $optionsName = 'easy_admin_options';

        /**
        * @var string $localizationDomain Domain used for localization
        */
        var $localizationDomain = "easy_admin";

        /**
        * @var string $pluginurl The path to this plugin
        */
        var $thispluginurl = '';
        /**
        * @var string $pluginurlpath The path to this plugin
        */
        var $thispluginpath = '';

        /**
        * @desc string $current_url Stores the current page's url
        */
        var $currenturl = '';

        /**
        * @desc string $currenturl_with_querystring Stores the current page's url including the querystring
        */
        var $currenturl_with_querystring = '';

        /**
        * @desc date $installdate stores the date/time this plugin was installed
        */
        var $installdate;

        /**
        * @var array $options Stores the options for this plugin
        */
        var $options = array();

        /**
        * @var array $siteOptions Stores the options for this plugin
        */
        var $siteOptions = array();

        //Translation helper vars
        var $trans_widget = '';
        var $trans_widgets = '';
        var $trans_item = '';
        var $trans_customize = '';

	var $blacklist = array("blog-php");
	var $allowedpages = array(
	    'index-php', 'post-php', 'post-new-php', 'edit-php', 'page-new-php', 'edit-pages-php', 'edit-comments-php', 'themes-php', 'widgets-php', 'profile-php', 'admin-php',
	    'premium-themes-php', 'media-upload-php', 'comment-php', 'admin-ajax-php', 'async-upload-php', 'page-php', 'supporter-help-php', 'supporter-php'
	);

        //Class Functions
        /**
        * PHP 4 Compatible Constructor
        */
        function easy_admin() {
			$this->__construct();
		}

        /**
        * PHP 5 Constructor
        */
        function __construct(){
            //Language Setup
            $locale = get_locale();
            $mo = dirname(__FILE__) . "/languages/" . $this->localizationDomain . "-".$locale.".mo";
            load_textdomain($this->localizationDomain, $mo);

            //"Constants" setup
			if (defined('WPMU_PLUGIN_DIR') && file_exists(WPMU_PLUGIN_DIR . '/easyblogging.php') ) { //We're not in the WPMU Plugin Directory
                $this->thispluginpath = WPMU_PLUGIN_DIR . '/easybloggingfiles/';
                $this->thispluginurl = WPMU_PLUGIN_URL . '/easybloggingfiles/';
            } else { //We are in the WPMU Plugin Directory
				$this->thispluginpath = WP_PLUGIN_DIR . '/easyblogging/easybloggingfiles/';
                $this->thispluginurl = WP_PLUGIN_URL . '/easyblogging/easybloggingfiles/';
            }

            $this->currenturl_with_querystring = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
            if (strstr($this->currenturl_with_querystring,'?') != '') {
                $urlary = explode('?',$this->currenturl_with_querystring);
                $this->currenturl = $urlary[0];
            } else $this->currenturl = $this->currenturl_with_querystring;

            //Initialize the options
            //This is REQUIRED to initialize the options when the plugin is loaded!
            $this->getOptions();

            //Translate a few words for later use (to get around the infinite recursion problem with the __ function in replace_text)
            $this->trans_widget = __('Widget', $this->localizationDomain);
            $this->trans_widgets = __('Widgets', $this->localizationDomain);
            $this->trans_item = __('Item', $this->localizationDomain);
            $this->trans_customize = __('Customize Design', $this->localizationDomain);

            //Actions
            add_action("init", array(&$this,"init"),4); //Set to 4 to allow other plugins to run before this one, just in case
            add_action("admin_init", array(&$this,"first_login"), 1); //We want to check if this is the first login before anything else, so we can hijack the files if necessary
            add_action("in_admin_footer", array(&$this,"admin_footer"));

            wp_enqueue_style( 'easy-admin-both-css', $this->thispluginurl.'css/easy.admin.both.css');

        }

        /**
        * Initialization function, enqueues necessary JS and CSS files and sets up the Easy Admin area, if necessary
        */
        function init() {
            if (!is_admin()) return; //If we're not in the admin area, this isn't needed
            global $user_ID, $pagenow;

	    $oldpage = $pagenow;
	    if (isset($_GET['post_type']) && $_GET['post_type'] == 'page') {
		switch ($pagenow) {
		    case 'post-new.php':
			$pagenow = 'page-new.php';
			break;
		    case 'edit.php':
			$pagenow = 'edit-pages.php';
			break;
		}
		$_SERVER['QUERY_STRING'] = preg_replace('/&{0,1}post_type=page/', '', $_SERVER['QUERY_STRING']);
	    }

	    if ($_SERVER['QUERY_STRING'] == '&' || $_SERVER['QUERY_STRING'] == '?') {
		$_SERVER['QUERY_STRING'] = '';
	    }

            $hash = str_replace('.','-',$pagenow);
            if(!empty($hash) && !in_array($hash, $this->allowedpages) && !in_array($hash, $this->blacklist) && !$this->options['disabled'][$user_ID]) {
		wp_safe_redirect('index.php#noteasy');
		exit;
	    }
	    if ($_SERVER['QUERY_STRING']) {
                $hash .= '|' . $_SERVER['QUERY_STRING'];
            }

            //Check if the user clicked the on/off link in the footer
            if ($_GET['frame']) {
                require_once('frame.php');
                die();
            }

            if ($pagenow == 'media-upload.php' || $pagenow == 'admin-ajax.php') {
                return; //We don't want to do a thing if this is the media-upload or admin-ajax page
            }

            if (isset($this->installdate) && !isset($this->options['disabled'][$user_ID])) { //Make sure $this->installdate is defined, and there is no value for the disabled setting
                global $wpdb;
                $registered = strtotime($wpdb->get_var("SELECT `registered` FROM $wpdb->blogs WHERE blog_id=$wpdb->blogid"));
                if ($registered < $this->installdate) { //If this blog was registered before the installation of Easy Blogging, set this user's admin area to advanced (IE - disable Easy Blogging)
                    $this->options['disabled'][$user_ID] = true;
                    $this->saveAdminOptions();
                }
            }

            if (isset($_GET['easyadmin'])) {
                switch ($_GET['easyadmin']) {
                    case 'on':
                        //echo 'enabling';
                        $this->options['disabled'][$user_ID] = false;
                        break;
                    case 'off':
                        //echo 'disabling';
                        $this->options['disabled'][$user_ID] = true;
                        break;
                }
                $this->saveAdminOptions();
            }

            if ($_GET['start']==1) {
                unset($this->options['disabled'][$user_ID]);
                $this->saveAdminOptions();
                wp_redirect(remove_query_arg('start',$pagenow));
                die();
            }

            //Add the admin menu link
            add_action('admin_menu', array(&$this,'admin_menu_link'));

            //Allow other scripts to stop Easy Admin from running
            $do_init = apply_filters('run_easy_admin_head',true);
            if (!$do_init) return;

            if (!$this->options['disabled'][$user_ID]) {
				// Add in the removal of the admin bar from the heading
				// remove the admin bar
				add_filter( 'show_admin_bar', '__return_false' );
                //The jQuery UI CSS is required for the tabs & the UI State Highlight, so we need to add it here
                wp_enqueue_style( 'jquery-custom-ui-tabs', $this->thispluginurl.'css/jquery.ui.tabs.css');

                wp_enqueue_style( 'easy-admin-css', $this->thispluginurl.'css/easy.admin.css'); //Enqueue the easy admin area css

                $doing_ajax = false;
                if (defined('DOING_AJAX')) {
                    $doing_ajax = DOING_AJAX;
                }

                if ($this->is_dash() && !$doing_ajax) { //If this page isn't in a tab, and it's not the admin ajax page, then hijack it via the admin_init function
                    add_action("admin_init", array(&$this,"admin_area_init"), 11); //We want to fire this after the default plugin init actions, in case another plugin needs to fire first (Setting it to < 10 caused problems with other plugins that called a admin_init hook that didn't fire)
                    add_action("admin_head", array(&$this,"admin_head"));

                    wp_enqueue_script('jquery-ui-tabs');
                } else if (!$this->is_dash()) { //If we're in a tabbed page
                    add_action('admin_head',array(&$this,'admin_head_resize'));
                    add_action('do_meta_boxes',array(&$this,'kill_meta_boxes'));

                    if ($pagenow == 'widgets.php' || $pagenow == 'themes.php') {
                        add_action('sidebar_admin_page', array(&$this, 'custom_header_addition'));
                        add_filter('gettext', array(&$this, 'replace_text'), 10, 3);
                    }
                }
                //Do this regardless of whether or not we're in a tab
                add_action('admin_head', array(&$this,'admin_head_css'));
                remove_action( 'admin_footer', 'bp_core_admin_bar');

                //Keep the Supporter popup from appearing on the main page
                if ($this->is_dash()) {
                    remove_action('admin_head','supporter_admin_box_hide_js');
                    remove_action('admin_footer','supporter_admin_box');
                }
            }
        }

        function custom_header_addition() {
            if ($GLOBALS['custom_image_header']) { //Only show this area if the current theme has a custom image header
                echo '<div id="iframe"><iframe id="custom-image-header-php" scrolling="no" src="' . admin_url('themes.php') . '?page=custom-header" style="height: 600px; width: 100%; border: none;" frameborder="0"></iframe></div>';
            }
        }

        /**
        * Replaces any instance of the word 'Widget' with 'Item', both for 'Widget', and 'widget'
        */
        function replace_text($transtext, $normtext, $domain) {
            //### Left in because for some reason __ won't work in here
            if ($transtext == $this->trans_widgets) { //The only place 'Widgets' shows up by itself that we care about is in the <h2> tag, so we know what to change it to
                return $this->trans_customize;
            }

            $transtext = str_replace($this->trans_widget, $this->trans_item, $transtext);
            $transtext = str_replace(strtolower($this->trans_widget), strtolower($this->trans_item), $transtext);

            return $transtext;
        }

        /**
        * Returns whether or not this is the user's first login (aka - there's nothing in the $this->options['disabled'][$user_ID] variable
        */
        function is_started() {
            global $user_ID;
            return isset($this->options['disabled'][$user_ID]);
        }

        /**
        * @desc Adds the necessary JS and CSS to the admin header for the easy admin area
        * The live_resize function was modified from the iResize function here: http://css-tricks.com/snippets/jquery/fit-iframe-to-content/
        *
        *
        * This is an example of how to use the easy_admin_tab_options filter:
        *
        *
        function easy_admin_tab_options($tab_options) {
            $tab_options['disabled'] = "[0,1,2,3,4,5,6,7,8,9,10]";
            $tab_options['load'] = "function (event,ui) {
                jQuery('a[href=\"#' + ui.panel.id + '\"]').html('<span>Quick Start</span>');
                var url = '" . admin_url('?wizard-step=0') . "';
                jQuery('#' + ui.panel.id + ' iframe').attr('src',url);
            }";
            return $tab_options;
        }
        */
        function admin_head() {

            ?>
            <script type="text/javascript">
                var advancedpage = '';
                var querystring = '';
                jQuery(document).ready(function(){
                    var anchor = jQuery(document).attr('location').hash; // the anchor in the URL
                    if (anchor.indexOf('|') > -1) {
                        //If this anchor has a | in it, we need to remove the | part because it's not actually part of the anchor, and save it in the querystring var
                        querystring = anchor.substring(anchor.indexOf('|')+1); //+1 to ignore the |
                        anchor = anchor.substring(0,anchor.indexOf('|'));
                    }
                    if (anchor == '#themes-php') { //Check to make sure the user isn't looking for the custom header page
                        if (querystring.indexOf('page=custom-header') > -1) anchor = '#widgets-php'; //The custom header area is on the same tab as the widgets, so we'll need to redirect there intead of themes-php
                        if (querystring.indexOf('page=premium-themes') > -1) anchor = '#premium-themes-php'; //We're trying to go to the premium themes page. Make it happen
                    }

                    advancedpage = anchor.replace('-php','').substring(1);

                    if (anchor != '') {
                        var index = jQuery('#easy_admin_tabs li a').index(jQuery(anchor)); // in tab index of the anchor in the URL
                        if (index < 0) {
                            querystring = ''; //Kill the querystring, because we didn't find the tab it goes with
                            index = jQuery('#easy_admin_tabs li').index(jQuery('#easy_admin_tabs li:not(#hidden_tab)'));
                        }
                    } else {
                        querystring = ''; //Kill the querystring, because we didn't find the tab it goes with
                        index = jQuery('#easy_admin_tabs li').index(jQuery('#easy_admin_tabs li:not(#hidden_tab)'));
                    }
                    jQuery('#easy_admin_tabs').tabs({
                        <?php
                        $tab_options = apply_filters('easy_admin_tab_options', array('selected'=>index, 'spinner'=>"''",
                            'select'=>'function(event, ui) {
                                jQuery("#easy_admin_tabs li a .loader").remove();
                                jQuery("#easy_admin_tabs li a").eq(ui.index).prepend("<span class=\'loader\'><img src=\'images/wpspin_light.gif\'/></span> ");
                            }',
                            'load'=>'function(event, ui) {
                            jQuery(".loader, ui.panel").remove();
                            }'));
                        $i=0;
                        foreach ($tab_options as $key=>$value) {
                            if ($i>0) {
                                echo ",\r\n";
                            } else $i = 1;
                            echo "'$key': $value";
                        }
                        ?>
                    }); // select the tab


                    jQuery('#easy_admin_tabs').bind('tabsshow', function(event, ui) { // change the url anchor when we click on a tab
                        //var scrollto = window.pageYOffset;
                        document.location.hash = jQuery('#easy_admin_tabs li a[href="#' + ui.panel.id + '"]').attr('id');
                        window.scroll(0,0);
			//jQuery( 'html, body' ).animate( { scrollTop: scrollto }, 0 ); //This line causes an undefined error in IE only

                        if (jQuery('#easy_admin_tabs li a[href="#' + ui.panel.id + '"]').attr('id') != 'noteasy') {
                            advancedpage = jQuery('#easy_admin_tabs li a[href="#' + ui.panel.id + '"]').attr('id').replace('-php','');
                        }

                        var href = '<?php echo admin_url('%%replace%%.php') . '?easyadmin=off'; ?>';

			if (querystring != '') {
			    if (querystring.substring(0,1) == '&') {
				href += querystring;
			    } else {
				href += '&' + querystring;
			    }
                        }

                        switch (advancedpage) {
                            case 'supporter-help':
                                jQuery("#to_advanced_page").attr('href','<?php echo admin_url('supporter.php') . '?easyadmin=off&page=premium-support'; ?>');
                            break;

                            case 'premium-themes':
                                jQuery("#to_advanced_page").attr('href','<?php echo admin_url('themes.php') . '?easyadmin=off&page=premium-themes'; ?>');
                            break;

			    case 'page-new':
                                jQuery("#to_advanced_page").attr('href','<?php echo admin_url('post-new.php') . '?post_type=page&easyadmin=off'; ?>');
                            break;

			    case 'edit-pages':
                                jQuery("#to_advanced_page").attr('href','<?php echo admin_url('edit.php') . '?post_type=page&easyadmin=off'; ?>');
                            break;

                            case 'widgets':
                                if (querystring.indexOf('page=custom-header')) {
                                    jQuery("#to_advanced_page").attr('href','<?php echo admin_url('themes.php') . '?easyadmin=off&page=custom-header'; ?>');
                                }
                            break;
                            default:
                                jQuery("#to_advanced_page").attr('href',href.replace('%%replace%%',advancedpage));
                        }
                        querystring = '';
                    });

                    jQuery('.supporter_help').live('click', function(event){
                        event.preventDefault(); //stop default browser behaviour
                        jQuery("#easy_admin_tabs").tabs('select', jQuery('#easy_admin_tabs li a').index(jQuery('#supporter-help-php')));
                        return false;
                    });

                    jQuery('.supporter_join').live('click', function(event){
                        event.preventDefault(); //stop default browser behaviour
                        jQuery("#easy_admin_tabs").tabs('select', jQuery('#easy_admin_tabs li a').index(jQuery('#supporter-php')));
                        return false;
                    });
                });
            </script>
            <?php
        }

        /**
        * The jQuery iframe resizing function that this function is based on found here: http://stackoverflow.com/questions/153152/resizing-an-iframe-based-on-content
        */
        function admin_head_resize() {
            global $pagenow;

	    $oldpage = $pagenow;
	    if (isset($_GET['post_type']) && $_GET['post_type'] == 'page') {
		switch ($pagenow) {
		    case 'post-new.php':
			$pagenow = 'page-new.php';
			break;
		    case 'edit.php':
			$pagenow = 'edit-pages.php';
			break;
		}
		$_SERVER['QUERY_STRING'] = preg_replace('/&{0,1}post_type=page/', '', $_SERVER['QUERY_STRING']);
	    }

	    if ($_SERVER['QUERY_STRING'] == '&' || $_SERVER['QUERY_STRING'] == '?') {
		$_SERVER['QUERY_STRING'] = '';
	    }

            $hash = str_replace('.','-',$pagenow);

            if ($_SERVER['QUERY_STRING']) {
                $hash .= '|' . $_SERVER['QUERY_STRING'];
            }

	    if (!in_array($hash, $this->blacklist)) {
		if(!in_array($hash, $this->allowedpages)) {
	    	    $jscript = "window.location.replace('" . admin_url("index.php#noteasy") . "');";
    		} else {
		    $jscript = "window.location.replace('" . admin_url("index.php#$hash") . "');";
		}
            }

	    if ($pagenow != $oldpage) {
		$nscript = "MyWindow.location = '" . admin_url("index.php#$hash") . "';";
	    } else {
		$nscript = "";
	    }

            ?>
            <script type="text/javascript">
                jQuery(document).ready(function() {
                    var theDiv = jQuery("iframe", parent.document.body).parent();

		    if (parent && parent.window) {
			MyWindow = parent.window;
		    } else {
			MyWindow = window;
		    }

		    <?php print $nscript; ?>

                    if (theDiv.length == 0) { //We're not in an iframe, redirect to the dashboard
			<?php echo $jscript; ?>
                    } else { //We are in an iframe, resize & do other actions
                        jQuery("iframe", parent.document.body).css('height','100%');
                        var height = jQuery(document).height();
                        if (height < 500) height = 500;
						height += 300;
                        theDiv.height(height); //Update the height of the parent div, so there isn't an iframe scrollbar

                        <?php if ($pagenow == 'edit.php' || $pagenow == 'edit-pages.php') { ?>
                        jQuery('.row-actions').find('.inline').remove();
                        <?php } else if ($pagenow == 'themes.php') { ?>
                        jQuery('.tb-theme-preview-link').live('click',function () {
			    return false;
                        });
                        <?php } ?>
                    }
                });
            </script>
            <?php
        }

        /**
        * If this is not the dashboard (which means it's likely in a tab) then hide the update-nag, wphead, and footer areas
        */
        function admin_head_css() {
            echo '<style type="text/css">';
            if (!$this->is_dash()) {
                echo 'div.wpmu-notice, .update-nag, #wphead, #footer { display: none; }';
                if ($this->siteOptions['remove_admin_notices_below_tabs']) {
                    echo '#message { display: none }';
                }
                /*if (!empty($this->siteOptions['remove_from_iframed_pages'])) {
                    echo $this->siteOptions['remove_from_iframed_pages'] . ' { display: none; }';
                }*/
            } else { //Is Dashboard
                if ($this->siteOptions['remove_admin_notices_above_tabs']) {
                    echo '#message { display: none }';
                }
                /*if (!empty($this->siteOptions['remove_from_above_tabs'])) {
                    echo $this->siteOptions['remove_from_above_tabs'] . ' { display: none; }';
                }*/
            }
            echo '</style>';
        }

        /**
        * Is this page the dashboard?
        */
        function is_dash() {
             global $pagenow;
             return (is_admin() && $pagenow == 'index.php');
        }

        /**
        * Removes all meta boxes from the $wp_meta_boxes array
        */
        function kill_meta_boxes() {
            global $wp_meta_boxes;


            $postcommentsbox = $wp_meta_boxes['post']['normal']['core']['commentstatusdiv'];
			if(isset($wp_meta_boxes['page']['normal']['core']['pagecommentstatusdiv'])) {
				$pagecommentsbox = $wp_meta_boxes['page']['normal']['core']['pagecommentstatusdiv'];
				$pagec = true;
			} else {
				$pagecommentsbox = $wp_meta_boxes['page']['normal']['core']['commentstatusdiv'];
				$pagec = false;
			}


            unset($wp_meta_boxes['post']['normal']);
            unset($wp_meta_boxes['page']['normal']);
            unset($wp_meta_boxes['page']['side']['core']['pageparentdiv']);
            unset($wp_meta_boxes['post']['side']['low']);

			add_meta_box('commentstatusdiv', __('Discussion'), 'post_comment_status_meta_box', 'post', 'normal', 'core');
			// For campus
			if($pagec) {
				add_meta_box('pagecommentstatusdiv', __('Discussion'), 'page_comments_status_meta_box', 'page', 'normal', 'core');
			} else {
				add_meta_box('commentstatusdiv', __('Discussion'), 'post_comment_status_meta_box', 'page', 'normal', 'core');
			}

		}

        /**
        * Wrapper function to make sure is_supporter() exists (meaning, the supporter plugin is active)
        */
        function is_supporter() {
            return function_exists('is_supporter') && is_supporter();
        }

        /**
        * Add a link to the footer to turn on/off the easy admin area
        */
        function admin_footer() {
            global $user_ID, $pagenow;

            if (strpos($this->currenturl_with_querystring,'?') > 0)
                $connector = '&';
            else
                $connector = '?';

            if (function_exists('is_supporter')) {
                $supporter_rebrand = get_site_option( "supporter_rebrand" );
                if ($supporter_rebrand == '') {
                    $supporter_rebrand = __('Supporter','supporter');
                }
            }

            ?><script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('#footer-left').after('<br/>&nbsp;');
                    $('#site-heading').after('<?php
            if (!$this->options['disabled'][$user_ID]) {
		// print $this->currenturl_with_querystring . $connector . 'easyadmin=off';
	        echo '<a id="to_advanced_page" href="' . $this->currenturl_with_querystring . $connector . 'easyadmin=off"><div id="admin_area_to_advanced" class="admin_area button">', __('Activate Advanced Admin',$this->localizationDomain) . '</div></a>';
	    } else {
                echo '<a id="to_easy_page" href="' . admin_url('index.php') . '?easyadmin=on#' . str_replace('.','-',$pagenow);
                if ($_SERVER['QUERY_STRING']) {
                    echo '|' . str_replace('easyadmin=off','',str_replace('easyadmin=on','',$_SERVER['QUERY_STRING'])); //Remove the easyadmin querystring vars
                }
                echo '"><div id="admin_area_to_easy" class="admin_area button">', __('Activate Easy Admin',$this->localizationDomain) . '</div></a>';
            }
            ?>');
            <?php if (!$this->options['disabled'][$user_ID]) { ?>
                    $('#wphead-info').before('<div id="logout">\
                    <?php if (function_exists('is_supporter') && current_user_can('manage_options') && function_exists('supporter_support_plug_page')) { ?><a class="supporter_help" href="<?php echo admin_url('supporter.php'); ?>?page=premium-support"><?php echo $supporter_rebrand . ' ' . __('Support',$this->localizationDomain) ?></a> |\<?php } ?>
                    <a href="<?php echo wp_logout_url() ?>" title="<?php _e('Log Out') ?>"><?php _e('Log Out'); ?></a></div>');
            <?php
                      if ($pagenow == 'themes.php') { //This is inside [if (!$this->options['disabled'][$user_ID])] because we don't need to add it unless we're in the easy admin area'
            ?>
                        $('#wpbody a:not(.thickbox, .activatelink, .submitdelete, .button, .updated a, .wizard_button, .page-numbers)').attr('target','_blank');
                        $(".add-new-h2").remove();
                        $(".theme-description, .action-links").next().remove();
            <?php     }
                    } ?>
                });
            </script>
            <?php if (!$this->options['disabled'][$user_ID]) { ?>
                <style type="text/css">
                    body {
                        padding-top:0;
                    }
                </style>
            <?php
            }
        }

        /**
        *  Checks if this is the user's first login, and if so, hijacks the admin area and replaces it with the start page
        */
        function first_login() {
            if (!$this->is_started() && get_option('supporter_signed_up') != 1) {
                wp_enqueue_style( 'easy-admin-css', $this->thispluginurl.'css/easy.admin.css'); //Enqueue the easy admin area css
                require_once('start.php');
                die();
            }
        }

        /**
        * Hijacks the admin area interface and replaces it with the Easy Admin area
        */
        function admin_area_init() {
            require_once('admin_area.php');
            die();
        }

        /**
        * @desc Retrieves the plugin options from the database.
        * @return array
        */
        function getOptions() {
            //Don't forget to set up the default options
            if (!$theOptions = get_option($this->optionsName)) {
                $theOptions = array('disabled'=>array());
                update_option($this->optionsName, $theOptions);
            }
            $this->options = $theOptions;

            if (!$theSiteOptions = get_site_option($this->optionsName)) {
                $theSiteOptions = array( 'remove_from_above_tabs'=>'#message.tip','remove_from_iframed_pages'=>'#update-nag, #message.tip');
                update_site_option($this->optionsName,$theSiteOptions);
            }
            $this->siteOptions = $theSiteOptions;

            //Setting up the $this->installdate value
            if (!$this->installdate = get_site_option($this->optionsName . '_installdate')) {
                $this->installdate = time();
                update_site_option($this->optionsName . '_installdate', $this->installdate);
            }

            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            //There is no return here, because you should use the $this->options variable!!!
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }

        /**
        * @desc Saves the admin options to the database.
        */
        function saveAdminOptions(){
            $retval = update_option($this->optionsName, $this->options);
            return update_site_option($this->optionsName, $this->siteOptions) && $retval;
        }

        /**
        * @desc Adds the options subpanel
        */
        function admin_menu_link() {
            //If you change this from add_options_page, MAKE SURE you change the filter_plugin_actions function (below) to
            //reflect the page filename (ie - options-general.php) of the page your plugin is under!

            if (get_bloginfo('version') >= 3)
                add_submenu_page( 'ms-admin.php', __('Easy Blogging',$this->localizationDomain), __('Easy Blogging',$this->localizationDomain), 10, basename(__FILE__), array(&$this,'admin_options_page'));
            else
                add_submenu_page( 'wpmu-admin.php', __('Easy Blogging',$this->localizationDomain), __('Easy Blogging',$this->localizationDomain), 10, basename(__FILE__), array(&$this,'admin_options_page'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );
        }

        /**
        * @desc Adds the Settings link to the plugin activate/deactivate page
        */
        function filter_plugin_actions($links, $file) {
            //If your plugin is under a different top-level menu than Settiongs (IE - you changed the function above to something other than add_options_page)
            //Then you're going to want to change options-general.php below to the name of your top-level page
            if (get_bloginfo('version') >= 3)
                $settings_link = '<a href="ms-admin.php?page=' . basename(__FILE__) . '">' . __('Settings',$this->localizationDomain) . '</a>';
            else
                $settings_link = '<a href="wpmu-admin.php?page=' . basename(__FILE__) . '">' . __('Settings',$this->localizationDomain) . '</a>';

            array_unshift( $links, $settings_link ); // before other links

            return $links;
        }

        /**
        * Adds settings/options page
        */
        function admin_options_page() {
            if (!empty($_POST['easy_blogging_save'])) {
                //$this->siteOptions['remove_from_above_tabs'] = stripslashes($_POST['remove_from_above_tabs']);
                //$this->siteOptions['remove_from_iframed_pages'] = stripslashes($_POST['remove_from_iframed_pages']);
                $this->siteOptions['remove_admin_notices_above_tabs'] = ($_POST['remove_admin_notices_above_tabs']=='on')?true:false;
                $this->siteOptions['remove_admin_notices_below_tabs'] = ($_POST['remove_admin_notices_below_tabs']=='on')?true:false;
                $this->saveAdminOptions();
            }
?>
                <div class="wrap">
                <h2><?php _e('Easy Blogging Options', $this->localizationDomain); ?></h2>
                <form method="post" id="options">
                <p><?php _e('Note: the "Hide admin messages" options are not foolproof. They remove standard admin messages, and messages from plugins that use the correct markup.',$this->localizationDomain); ?></p>
                <table width="100%" cellspacing="2" cellpadding="5" class="widefat fixed" id="add_new_table">
                    <tr valign="top">
                        <th width="33%" scope="row"><?php _e('Hide admin messages from above the tabs:', $this->localizationDomain); ?></th>
                        <td><input type="checkbox" name="remove_admin_notices_above_tabs" <?php echo ($this->siteOptions['remove_admin_notices_above_tabs'])?'checked="CHECKED"':''; ?> /></td>
                    </tr>
                    <tr valign="top">
                        <th width="33%" scope="row"><?php _e('Hide admin messages from below the tabs:', $this->localizationDomain); ?></th>
                        <td><input type="checkbox" name="remove_admin_notices_below_tabs" <?php echo ($this->siteOptions['remove_admin_notices_below_tabs'])?'checked="CHECKED"':''; ?> /></td>
                    </tr>
                </table>
                <p><div class="submit"><input type="submit" name="easy_blogging_save" class="button-primary" /></div></p>

                <?php /* wp_nonce_field('easy_admin-update-options'); ?>
                <h3><?php _e('Hidden Classes', $this->localizationDomain); ?></h3>
                <p><?php _e('Because of the way the Easy Admin Area works, you might find that some messages, ads, notices, etc show up above and below the tabs on the page. To
                help remedy this problem, the boxes below can be used to control what is displayed and what isn\'t, based on CSS. All you have to do is enter the
                <a href="http://www.google.com/search?sourceid=navclient&ie=UTF-8&rlz=1T4GGLL_enUS371US371&q=css+selector" title="Click here to find out more about CSS Selectors">CSS selector</a>
                of the item you want removed, and enter it in one or both of the boxes below. Make sure each selector is separated by a comma.', $this->localizationDomain); ?>
                </p>
                    <table width="100%" cellspacing="2" cellpadding="5" class="widefat fixed" id="add_new_table">
                        <tr valign="top">
                            <th width="33%" scope="row"><?php _e('Hide these from above the tabs:', $this->localizationDomain); ?></th>
                            <td><textarea name="remove_from_above_tabs" id="remove_from_above_tabs" cols="80" rows="4"><?php echo $this->siteOptions['remove_from_above_tabs']; ?></textarea></td>
                        </tr>
                        <tr valign="top">
                            <th width="33%" scope="row"><?php _e('Hide these from below the tabs:', $this->localizationDomain); ?></th>
                            <td><textarea name="remove_from_iframed_pages" id="remove_from_iframed_pages" cols="80" rows="4"><?php echo $this->siteOptions['remove_from_iframed_pages']; ?></textarea></td>
                        </tr>
                    </table>
                    <p><div class="submit"><input type="submit" name="easy_blogging_save" class="button-primary" /></div></p>
                */ ?>
                </form>
                <?php
        }
    } //End Class
    //instantiate the class
	if (is_admin() ) {
		if(function_exists('is_network_admin') && is_network_admin()) {
		} else {
			global $easy_admin_var;
	        $easy_admin_var = new easy_admin();
		}

    }

} //End if easy_admin class exists statement

function do_meta_stuff() {
    global $wp_meta_boxes;

    echo '<pre>';
    print_r($wp_meta_boxes['post']['normal']);
    echo '</pre>';

}
//add_action('do_meta_boxes',do_meta_stuff);
?>
