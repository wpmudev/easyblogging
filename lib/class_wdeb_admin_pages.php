<?php
/**
 * Handles all Admin access functionality.
 */
class Wdeb_AdminPages {

	var $data;
	var $_is_in_easymode;
	var $_translation_replacements = array();
	var $_menu_partial;

	function Wdeb_AdminPages () { $this->__construct(); }

	function __construct () {
		$this->data = new Wdeb_Options;
		/*
		$this->_translation_replacements = array(
			__('Widget') => __('Item', 'wdeb'),
			__('Widgets') => __('Items', 'wdeb'),
		);
		*/
		$this->_menu_partial = 'menu-default';
	}

	/**
	 * Main entry point.
	 *
	 * @static
	 */
	function serve () {
		$me = new Wdeb_AdminPages;
		$me->add_hooks();
	}

	function _handle_logo_upload () {
		if (!isset($_FILES['wdeb_logo'])) return false;
		$name = $_FILES['wdeb_logo']['name'];
		if (!$name) return false;

		$allowed = array('jpg', 'jpeg', 'png', 'gif');
		$ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
		if (!in_array($ext, $allowed)) wp_die(__('This file type is not supported', 'wdeb'));

		$wp_upload_dir = wp_upload_dir();
		$logo_dir = $wp_upload_dir['basedir'] . '/wdeb';
		$logo_path = $wp_upload_dir['baseurl'] . '/wdeb';

		if (!file_exists($logo_dir)) wp_mkdir_p($logo_dir);
		while (file_exists("{$logo_dir}/{$name}")) $name = rand(0,9) . $name;

		if (move_uploaded_file($_FILES['wdeb_logo']['tmp_name'], "{$logo_dir}/{$name}")) {
			if (defined('WP_NETWORK_ADMIN') && WP_NETWORK_ADMIN) {
				$opts = $this->data->get_options('wdeb');
				$opts['wdeb_logo'] = "{$logo_path}/{$name}";
				$this->data->set_options($opts, 'wdeb');
			} else {
				update_option('wdeb_logo', "{$logo_path}/{$name}");
			}
		}
	}

	function get_menu_partial () {
		include(WDEB_PLUGIN_BASE_DIR . '/lib/forms/partials/' . $this->_menu_partial . '.php');
	}

	function create_site_admin_menu_entry () {
		if (@$_POST && isset($_POST['option_page'])) {
			$changed = false;
			if('wdeb' == @$_POST['option_page']) {
				$this->data->set_options($_POST['wdeb'], 'wdeb');
				$this->_handle_logo_upload();
				$changed = true;
			} else if ('wdeb_wizard' == @$_POST['option_page']) {
				if (isset($_POST['wdeb_wizard']['wizard_steps']['_last_'])) {
					$last = $_POST['wdeb_wizard']['wizard_steps']['_last_'];
					unset($_POST['wdeb_wizard']['wizard_steps']['_last_']);
					$original_url = @$last['url'];
					$last['url'] = rtrim($last['url_type'], '/') . trim($last['url']);
					unset($last['url_type']);
					if (trim(@$last['url']) && trim($original_url)) {
						$last['title'] = trim(stripslashes(htmlspecialchars($last['title'], ENT_QUOTES)));
						@$last['help'] = stripslashes(htmlspecialchars($last['help'], ENT_QUOTES));
						$_POST['wdeb_wizard']['wizard_steps'][] = $last;
					}
				}
				if (isset($_POST['wdeb_wizard']['wizard_steps'])) {
					$_POST['wdeb_wizard']['wizard_steps'] = array_filter($_POST['wdeb_wizard']['wizard_steps']);
				}
				$this->data->set_options($_POST['wdeb_wizard'], 'wdeb_wizard');
				$changed = true;
			} else if ('wdeb_help' == @$_POST['option_page']) {
				$this->data->set_options($_POST['wdeb_help'], 'wdeb_help');
				$changed = true;
			}
			$changed = apply_filters('wdeb_admin-options_changed', $changed);

			if ($changed) {
				$goback = add_query_arg('settings-updated', 'true',  wp_get_referer());
				wp_redirect($goback);
				die;
			}
		}
		$perms = (defined('WP_NETWORK_ADMIN') && WP_NETWORK_ADMIN) ? 'manage_network_options' : 'manage_options';
		add_menu_page(__('Easy Blogging', 'wdeb'), __('Easy Blogging', 'wdeb'), $perms, 'wdeb', array($this, 'create_admin_blogging_page'), WDEB_PLUGIN_URL . '/img/eb_plugin.png');
		add_submenu_page('wdeb', __('Easy Blogging', 'wdeb'), __('Easy Blogging', 'wdeb'), $perms, 'wdeb', array($this, 'create_admin_blogging_page'));
		add_submenu_page('wdeb', __('Easy Blogging Wizard', 'wdeb'), __('Easy Blogging Wizard', 'wdeb'), $perms, 'wdeb_wizard', array($this, 'create_admin_wizard_page'));
		add_submenu_page('wdeb', __('Easy Blogging Tooltips', 'wdeb'), __('Easy Blogging Tooltips', 'wdeb'), $perms, 'wdeb_help', array($this, 'create_admin_tooltips_page'));
		add_submenu_page('wdeb', __('Add-ons', 'wdeb'), __('Add-ons', 'wdeb'), $perms, 'wdeb_plugins', array($this, 'create_admin_plugins_page'));

		do_action('wdeb_admin-add_pages', $perms);
	}

	function register_settings () {
		$form = new Wdeb_AdminFormRenderer;

		register_setting('wdeb', 'wdeb');
		add_settings_section('wdeb_settings', __('Editor Settings', 'wdeb'), create_function('', ''), 'wdeb_options_page');
		add_settings_field('wdeb_metaboxes_posts', __('Hide these meta boxes on "Edit Post" pages', 'wdeb'), array($form, 'create_metaboxes_posts_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_metaboxes_pages', __('Hide these meta boxes on "Edit Page" pages', 'wdeb'), array($form, 'create_metaboxes_pages_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_admin_bar', __('Show Admin bar', 'wdeb'), array($form, 'create_admin_bar_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_screen_options', __('Show help and screen options', 'wdeb'), array($form, 'create_screen_options_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_easy_bar', __('Show Easy Bar', 'wdeb'), array($form, 'create_easy_bar_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_auto_enter_role', __('Force "Easy" mode for user with this role', 'wdeb'), array($form, 'create_auto_enter_role_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_hijack_start_page', __('Hijack start page for new users', 'wdeb'), array($form, 'create_hijack_start_page_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_show_logout', __('Always show logout link', 'wdeb'), array($form, 'create_show_logout_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_plugin_theme', __('Use this theme', 'wdeb'), array($form, 'create_plugin_theme_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_logo', __('Use this logo', 'wdeb'), array($form, 'create_logo_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_dashboard_widget', __('Dashboard widget', 'wdeb'), array($form, 'create_dashboard_widget_box'), 'wdeb_options_page', 'wdeb_settings');
		add_settings_field('wdeb_dashboard_right_now', __('Show &quot;Right Now&quot; in Dashboard', 'wdeb'), array($form, 'create_dashboard_right_now_widget_box'), 'wdeb_options_page', 'wdeb_settings');

		do_action('wdeb_admin-register_settings-settings', $form);

		register_setting('wdeb', 'wdeb_wizard');
		add_settings_section('wdeb_wizard', __('Wizard Settings', 'wdeb'), create_function('', ''), 'wdeb_wizard');
		add_settings_field('wdeb_wizard_enable', __('Enable Wizard', 'wdeb'), array($form, 'create_wizard_enabled_box'), 'wdeb_wizard', 'wdeb_wizard');
		add_settings_field('wdeb_wizard_steps', __('Configure Wizard Steps', 'wdeb'), array($form, 'create_wizard_steps_box'), 'wdeb_wizard', 'wdeb_wizard');
		add_settings_field('wdeb_wizard_add_step', __('Add new Wizard Step', 'wdeb'), array($form, 'create_wizard_add_step_box'), 'wdeb_wizard', 'wdeb_wizard');

		do_action('wdeb_admin-register_settings-wizard', $form);

		register_setting('wdeb', 'wdeb_help');
		add_settings_section('wdeb_help', __('Tooltips Settings', 'wdeb'), create_function('', ''), 'wdeb_help');
		add_settings_field('wdeb_show_tooltips', __('Show Tooltips', 'wdeb'), array($form, 'create_show_tooltips_box'), 'wdeb_help', 'wdeb_help');

		do_action('wdeb_admin-register_settings-help', $form);
	}

	function create_admin_blogging_page () {
		include(WDEB_PLUGIN_BASE_DIR . '/lib/forms/blogging_settings.php');
	}
	function create_admin_tooltips_page () {
		include(WDEB_PLUGIN_BASE_DIR . '/lib/forms/tooltips_settings.php');
	}
	function create_admin_wizard_page () {
		include(WDEB_PLUGIN_BASE_DIR . '/lib/forms/wizard_settings.php');
	}
	function create_admin_plugins_page () {
		include(WDEB_PLUGIN_BASE_DIR . '/lib/forms/plugins_settings.php');
	}

	function js_print_scripts () {
		if (WP_NETWORK_ADMIN) return;
		if (!$this->is_in_easymode()) {
			wp_enqueue_script('wdeb_switch', WDEB_PLUGIN_URL . '/js/wdeb_switch.js', 'jquery');
			wp_localize_script('wdeb_switch', 'l10WdebSwitch', array(
				'activate' => __('Activate easy mode', 'wdeb')
			));
		} else {
			wp_enqueue_script(array(
				'jquery', 
				'jquery-ui-core', 
				'jquery-ui-sortable',
				'jquery-ui-dialog', 
				'jquery-ui-tabs', 
				'jquery-ui-datepicker', 
				'jquery-ui-dialog', 
				'jquery-ui-slider', 
				'jquery-ui-progressbar', 
			));
		}
		printf(
			'<script type="text/javascript">_wdebLandingPage = "%s";</script>',
			apply_filters('wdeb_easy_mode_init', WDEB_LANDING_PAGE . '?wdeb_on')
		);

	}

	function css_print_styles () {
		global $wp_version;
		$version = preg_replace('/-.*$/', '', $wp_version);
		
		if (WP_NETWORK_ADMIN) return;
		wp_enqueue_style('wdeb_switch', WDEB_PLUGIN_URL . '/css/wdeb_switch.css');
		if (version_compare($version, '3.3', '<')) {
			echo '<style type="text/css">.wdeb_switch {height: 13px;}</style>';
		} else {
			echo '<style type="text/css">.wdeb_switch {height: 24px !important;}</style>';
		}
        wp_enqueue_style('wdeb_global', WDEB_PLUGIN_URL . '/css/wdeb_global.css');
	}

	function apply_text_overrides ($haystack) {
		foreach ($this->_translation_replacements as $needle => $rpl) {
			$haystack = str_replace($needle, $rpl, $haystack);
			$haystack = str_replace(strtolower($needle), strtolower($rpl), $haystack);
		}
		return $haystack;
	}

	function apply_meta_boxes_overrides () {
		global $wp_meta_boxes;

		$post_boxes = $this->data->get_option('post_boxes');
		$post_boxes = is_array($post_boxes) ? $post_boxes : array();
		$page_boxes = $this->data->get_option('page_boxes');
		$page_boxes = is_array($page_boxes) ? $page_boxes : array();

		if (is_array(@$wp_meta_boxes['post']['side']['core'])) foreach ($wp_meta_boxes['post']['side']['core'] as $name => $box) if (in_array($name, $post_boxes)) unset($wp_meta_boxes['post']['side']['core'][$name]);
		if (is_array(@$wp_meta_boxes['post']['side']['low'])) foreach ($wp_meta_boxes['post']['side']['low'] as $name => $box) if (in_array($name, $post_boxes)) unset($wp_meta_boxes['post']['side']['low'][$name]);
		if (is_array(@$wp_meta_boxes['post']['normal']['core'])) foreach ($wp_meta_boxes['post']['normal']['core'] as $name => $box) if (in_array($name, $post_boxes)) unset($wp_meta_boxes['post']['normal']['core'][$name]);

		if (is_array(@$wp_meta_boxes['page']['side']['core'])) foreach ($wp_meta_boxes['page']['side']['core'] as $name => $box) if (in_array($name, $page_boxes)) unset($wp_meta_boxes['page']['side']['core'][$name]);
		if (is_array(@$wp_meta_boxes['page']['side']['low'])) foreach ($wp_meta_boxes['page']['side']['low'] as $name => $box) if (in_array($name, $page_boxes)) unset($wp_meta_boxes['page']['side']['low'][$name]);
		if (is_array(@$wp_meta_boxes['page']['normal']['core'])) foreach ($wp_meta_boxes['page']['normal']['core'] as $name => $box) if (in_array($name, $page_boxes)) unset($wp_meta_boxes['page']['normal']['core'][$name]);

		do_action('wdeb_admin-editor_metaboxes_cleanup');
	}

	function strip_down_dashboard () {
		global $wp_meta_boxes;
		$allowed = $this->data->get_option('dashboard_right_now') ? array('dashboard_right_now') : array();

		$allowed = apply_filters('wdeb_allowed_dashboard_metaboxes', $allowed);
		foreach ($wp_meta_boxes['dashboard'] as $board => $position) {
			foreach ($position as $pos => $boxes) {
				foreach ($boxes as $key => $box) {
					if (in_array($box['id'], $allowed)) continue;
					do_action('wdeb_dashboard_cleanup_removing_item', $board, $pos, $key, $wp_meta_boxes['dashboard'][$board][$pos][$key]);
					unset($wp_meta_boxes['dashboard'][$board][$pos][$key]);
				}
			}
		}
	}

	function easy_dashboard_widget () {
		echo stripslashes($this->data->get_option('widget_contents'));
	}

	function process_dashboard () {
		$this->strip_down_dashboard();
		if ($this->data->get_option('show_dashboard_widget')) {
			$title = stripslashes($this->data->get_option('widget_title'));
			$title = $title ? $title : __('Easy Blogging', 'wdeb');
			wp_add_dashboard_widget('wdeb_dashboard_widget', $title, array($this, 'easy_dashboard_widget'));
		}
	}

	function start_cache () {
		ob_start();
	}
	function end_header_cache () {
		$header_html = ob_get_contents();
		ob_end_clean();
		include apply_filters('wdeb_theme_header_partial', WDEB_PLUGIN_BASE_DIR . '/lib/forms/partials/header.php');
	}
	function end_footer_cache () {
		$footer_html = ob_get_contents();
		ob_end_clean();
		echo $footer_html;
		include apply_filters('wdeb_theme_footer_partial', WDEB_PLUGIN_BASE_DIR . '/lib/forms/partials/footer.php');
	}

	function initialize_easy_mode () {
		global $pagenow;

		if (defined('DOING_AJAX')) return;

		$theme = $this->data->get_option('plugin_theme');
		$theme = $theme ? $theme : 'default';
		define('WDEB_PLUGIN_THEME_URL', WDEB_PLUGIN_URL . '/themes/' . $theme, true);

		$user = wp_get_current_user();
		$user_id = ($user && $user->ID) ? $user->ID : false;

		if (!$this->is_in_easymode() && !isset($_GET['wdeb_on'])) {

			// We just landed. Auto-start easy mode?
			$top_caps = $this->data->get_option('auto_enter_role');
			$top_caps = is_array($top_caps) ? $top_caps : array();
			$cap_enter = false;
			if (!current_user_can('manage_network_options')) /* Don't do this for Super Admin */ foreach ($top_caps as $cap) {
				if (current_user_can($cap)) {
					$cap_enter = true;
					break;
				}
			}
			if ($cap_enter) {
				wp_redirect(admin_url(WDEB_LANDING_PAGE . '?wdeb_on'));
				exit();
			}

			if ($user_id && $this->data->get_option('hijack_start_page')) {
				$start = get_user_meta($user_id, 'wdeb_autostart', true);
				$started = get_user_meta($user_id, 'wdeb_started', true);
				//if ('yes' == $start && $pagenow == WDEB_LANDING_PAGE && !isset($_GET['wdeb_off'])) { //!isset($_COOKIE['wdeb_on'])) {
				//if ('yes' == $start && !isset($_COOKIE['wdeb_on'])) {
				if (!$start && !$started && !isset($_GET['wdeb_off'])) {
					// Hijack
					include (WDEB_PLUGIN_BASE_DIR . '/lib/forms/start_page.php');
					exit();
				} else if ('yes' == $start && !$started) {
					// Autostart
					update_user_meta($user_id, 'wdeb_started', 'yes');
					wp_redirect(admin_url(WDEB_LANDING_PAGE . '?wdeb_on'));
					exit();
				} else if ('no' == $start && !$started) {
					// Regular admin page
					update_user_meta($user_id, 'wdeb_started', 'no');
				}
			}
		}

		if ($this->is_in_easymode()) {
			// Allow others (i.e. Wizard) to hook into the menu
			$this->_menu_partial = apply_filters('wdeb_menu_partial', $this->_menu_partial);

			add_action('admin_head', array($this, 'start_cache'), 1);
			add_action('admin_notices', array($this, 'end_header_cache'), 1);

			add_filter('gettext', array(&$this, 'apply_text_overrides'));
			add_action('do_meta_boxes', array($this, 'apply_meta_boxes_overrides'));
			add_action('wp_dashboard_setup', array($this, 'process_dashboard'));

			add_action('in_admin_footer', array($this, 'start_cache'), 1);
			add_action('admin_footer', array($this, 'end_footer_cache'), 999);

			remove_action('in_admin_header', 'wp_admin_bar_render', 0);
			add_action('eab-admin_toolbar-render', 'wp_admin_bar_render');

			// Take care of autostart values - turn on
			if ($user_id && $this->data->get_option('hijack_start_page')) {
				$start = get_user_meta($user_id, 'wdeb_autostart', true);
				if (!$start) update_user_meta($user_id, 'wdeb_autostart', 'yes');
			}
		} else {
			// Take care of autostart values - turn off
			if ($user_id && $this->data->get_option('hijack_start_page')) {
				$start = get_user_meta($user_id, 'wdeb_autostart', true);
				if (!$start) update_user_meta($user_id, 'wdeb_autostart', 'no');
			}
		}
	}

	function is_in_easymode () {
		return $this->_is_in_easymode;
	}

	/**
	 * This is where the default menu items are set.
	 * Menu items are set as an array of menu items.
	 * Each item is an associative array, with these values:
	 * 	- "check_callback" - Custom PHP callback that will be called prior
	 * 		to rendering the item, in order to determine if the item
	 * 		should be displayed at all.
	 * 	- "capability" - Minimum required user capability to display the
	 * 		menu item. This will be checked prior to "check_callback".
	 * 	- "url" - Administrative page url (e.g. edit.php).
	 * 	- "icon" - Full menu icon URL.
	 * 	- "title" - Main text for the menu item.
	 * 	- "help" - Will be shown as tooltip and (optionally) transient
	 * 		help text screen for the menu item.
	 *
	 * Plugins can register their own menu items by hooking into
	 * "wdeb_menu_items" filter.
	 */
	function easy_mode_menu () {
		$pro_href = $pro_title = false;
		if (class_exists('ProSites')) { // Official
			$pro_href = 'admin.php?page=psts-checkout';
			$pro_title = ProSites::get_setting('rebrand');
		} else if (class_exists('ProBlogs')) { // Beta
			$pro_href = 'admin.php?page=pblgs-checkout';
			$pro_title = ProBlogs::get_setting('rebrand');
		} else if (function_exists('is_supporter')) { // Old
			$pro_href = 'supporter.php';
			$pro_title = __('Supporter', 'wdeb');
		}
		return array (
			array (
				'check_callback' => false,
				'capability' => false,
				'url' => 'index.php',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/home.png',
				'title' => __('Dashboard', 'wdeb'),
				'help' => __('Your start page', 'wdeb'),
			),
			array (
				'check_callback' => false,
				'capability' => 'edit_posts',
				'url' => 'post-new.php',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/new-post.png',
				'title' => __('New Post', 'wdeb'),
				'help' => __('Create a new post', 'wdeb'),
			),
			array (
				'check_callback' => false,
				'capability' => 'edit_posts',
				'url' => 'edit.php',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/posts.png',
				'title' => __('My Posts', 'wdeb'),
				'help' => __('Edit your posts', 'wdeb'),
			),
			array (
				'check_callback' => false,
				'capability' => 'edit_pages',
				'url' => 'post-new.php?post_type=page',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/new-page.png',
				'title' => __('New Page', 'wdeb'),
				'help' => __('Create a new page', 'wdeb'),
			),
			array (
				'check_callback' => false,
				'capability' => 'edit_pages',
				'url' => 'edit.php?post_type=page',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/pages.png',
				'title' => __('My Pages', 'wdeb'),
				'help' => __('Edit your pages', 'wdeb'),
			),
			array (
				'check_callback' => false,
				'capability' => 'edit_posts', // Was moderate_comments up to v3.1
				'url' => 'edit-comments.php',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/comments.png',
				'title' => __('Comments', 'wdeb'),
				'help' => __('Moderate your comments', 'wdeb'),
			),
			array (
				'check_callback' => 'wdeb_supporter_themes_enabled',
				'capability' => 'switch_themes',
				'url' => 'themes.php',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/free-themes.png',
				'title' => __('Free Themes', 'wdeb'),
				'help' => __('Change your site appearance', 'wdeb'),
			),
			array (
				'check_callback' => 'wdeb_supporter_themes_enabled',
				'capability' => 'switch_themes',
				'url' => 'themes.php?page=premium-themes',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/premium-themes.png',
				'title' => __('Premium Themes', 'wdeb'),
				'help' => __('Change your site appearance', 'wdeb'),
			),
			array (
				'check_callback' => 'wdeb_supporter_themes_not_enabled',
				'capability' => 'switch_themes',
				'url' => 'themes.php',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/free-themes.png',
				'title' => __('Manage Themes', 'wdeb'),
				'help' => __('Change your site appearance', 'wdeb'),
			),
			array (
				'check_callback' => false,
				'capability' => 'edit_theme_options',
				'url' => 'widgets.php',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/edit-themes.png',
				'title' => __('Customize Design', 'wdeb'),
				'help' => __('Personalize your site', 'wdeb'),
			),
			array (
				'check_callback' => 'wdeb_not_supporter',
				'capability' => 'manage_options',
				'url' => $pro_href,//'supporter.php',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/supporter.png',
				'title' => $pro_title,//__('Supporter', 'wdeb'),
				'help' => __('Support us!', 'wdeb'),
			),
			array (
				'check_callback' => false,
				'capability' => false,
				'url' => 'profile.php',
				'icon' => WDEB_PLUGIN_THEME_URL . '/assets/icons/theme_icons/profiles.png',
				'title' => __('Profile', 'wdeb'),
				'help' => __('Edit your profile information', 'wdeb'),
			),

		);
	}

	function set_easy_mode_flag () {
		if (isset($_GET['wdeb_on'])) {
			if (!isset($_COOKIE['wdeb_on']) || (int)$_COOKIE['wdeb_on'] <= 0) setcookie("wdeb_on", 1, time() + 31556926, '/', COOKIE_DOMAIN);
			$this->_is_in_easymode = true;
		} else if (isset($_GET['wdeb_off'])) {
			if (isset($_COOKIE['wdeb_on']) && (int)$_COOKIE['wdeb_on'] > 0) setcookie("wdeb_on", 0, time() + 31556926, '/', COOKIE_DOMAIN);
			$this->_is_in_easymode = false;
		} else {
			$this->_is_in_easymode = (isset($_COOKIE['wdeb_on']) && (int)$_COOKIE['wdeb_on'] > 0) ? true : false;
		}

		if (!defined('WDEB_IS_IN_EASY_MODE')) define('WDEB_IS_IN_EASY_MODE', $this->_is_in_easymode);

		// Setup BuddyBar removal, if needed
		if ($this->_is_in_easymode && defined('BP_VERSION') && !(int)$this->data->get_option('admin_bar')) {
			remove_action('bp_init', 'bp_core_load_buddybar_css');
			remove_action('bp_loaded',  'bp_core_load_admin_bar');
		}
	}

	function json_activate_plugin () {
		$status = Wdeb_PluginsHandler::activate_plugin($_POST['plugin']);
		echo json_encode(array(
			'status' => $status ? 1 : 0,
		));
		exit();
	}

	function json_deactivate_plugin () {
		$status = Wdeb_PluginsHandler::deactivate_plugin($_POST['plugin']);
		echo json_encode(array(
			'status' => $status ? 1 : 0,
		));
		exit();
	}

	function add_hooks () {
		add_action('admin_init', array($this, 'register_settings'));
		if (is_multisite()) {
			add_action('network_admin_menu', array($this, 'create_site_admin_menu_entry'));
		} else {
			add_action('admin_menu', array($this, 'create_site_admin_menu_entry'));
		}

		if (!WP_NETWORK_ADMIN) {
			add_action('admin_init', array($this, 'initialize_easy_mode'));
		}
		add_action('admin_print_scripts', array($this, 'js_print_scripts'));
		add_action('admin_print_styles', array($this, 'css_print_styles'));
		add_filter('wdeb_initialize_menu', array($this, 'easy_mode_menu'));

		// Set easy mode flag - are we men, or are we mice
		//$this->set_easy_mode_flag();
		add_action('after_setup_theme', array($this, 'set_easy_mode_flag'));

		// AJAX plugin handlers
		add_action('wp_ajax_wdeb_activate_plugin', array($this, 'json_activate_plugin'));
		add_action('wp_ajax_wdeb_deactivate_plugin', array($this, 'json_deactivate_plugin'));
	}
}