<?php
/*
Plugin Name: Easy Blogging
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Description: Modifies the Wordpress admin area to default it to a "Beginner" area, with the option to switch to the normal, "Advanced" area
Version: 3.2.2
Text Domain: wdeb
Author: Incsub
Author URI: http://premium.wpmudev.org
WDP ID: 133

Copyright 2009-2011 Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

///////////////////////////////////////////////////////////////////////////
/* -------------------- Update Notifications Notice -------------------- */
if ( !function_exists( 'wdp_un_check' ) ) {
  add_action( 'admin_notices', 'wdp_un_check', 5 );
  add_action( 'network_admin_notices', 'wdp_un_check', 5 );
  function wdp_un_check() {
    if ( class_exists( 'WPMUDEV_Update_Notifications' ) )
      return;

    if ( $delay = get_site_option( 'un_delay' ) ) {
      if ( $delay <= time() && current_user_can( 'install_plugins' ) )
      	echo '<div class="error fade"><p>' . __('Please install the latest version of <a href="http://premium.wpmudev.org/project/update-notifications/" title="Download Now &raquo;">our free Update Notifications plugin</a> which helps you stay up-to-date with the most stable, secure versions of WPMU DEV themes and plugins. <a href="http://premium.wpmudev.org/wpmu-dev/update-notifications-plugin-information/">More information &raquo;</a>', 'wpmudev') . '</a></p></div>';
	  } else {
			update_site_option( 'un_delay', strtotime( "+1 week" ) );
		}
	}
}
/* --------------------------------------------------------------------- */

define ('WDEB_PLUGIN_SELF_DIRNAME', basename(dirname(__FILE__)), true);

//Setup proper paths/URLs and load text domains
if (is_multisite() && defined('WPMU_PLUGIN_URL') && defined('WPMU_PLUGIN_DIR') && file_exists(WPMU_PLUGIN_DIR . '/' . basename(__FILE__))) {
	define ('WDEB_PLUGIN_LOCATION', 'mu-plugins', true);
	define ('WDEB_PLUGIN_BASE_DIR', WPMU_PLUGIN_DIR, true);
	define ('WDEB_PLUGIN_URL', str_replace('http://', (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://'), WPMU_PLUGIN_URL), true);
	$textdomain_handler = 'load_muplugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/' . WDEB_PLUGIN_SELF_DIRNAME . '/' . basename(__FILE__))) {
	define ('WDEB_PLUGIN_LOCATION', 'subfolder-plugins', true);
	define ('WDEB_PLUGIN_BASE_DIR', WP_PLUGIN_DIR . '/' . WDEB_PLUGIN_SELF_DIRNAME, true);
	define ('WDEB_PLUGIN_URL', str_replace('http://', (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://'), WP_PLUGIN_URL) . '/' . WDEB_PLUGIN_SELF_DIRNAME, true);
	$textdomain_handler = 'load_plugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/' . basename(__FILE__))) {
	define ('WDEB_PLUGIN_LOCATION', 'plugins', true);
	define ('WDEB_PLUGIN_BASE_DIR', WP_PLUGIN_DIR, true);
	define ('WDEB_PLUGIN_URL', str_replace('http://', (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://'), WP_PLUGIN_URL), true);
	$textdomain_handler = 'load_plugin_textdomain';
} else {
	// No textdomain is loaded because we can't determine the plugin location.
	// No point in trying to add textdomain to string and/or localizing it.
	wp_die(__('There was an issue determining where Post Voting plugin is installed. Please reinstall.'));
}
$textdomain_handler('wdeb', false, WDEB_PLUGIN_SELF_DIRNAME . '/languages/');

define('WDEB_LOGO_URL', WDEB_PLUGIN_URL . '/img/logo.png', true);
define('WDEB_LANDING_PAGE', 'index.php', true);


require_once WDEB_PLUGIN_BASE_DIR . '/lib/class_wdeb_installer.php';
Wdeb_Installer::check();

require_once WDEB_PLUGIN_BASE_DIR . '/lib/wdeb_callbacks.php';
require_once WDEB_PLUGIN_BASE_DIR . '/lib/class_wdeb_options.php';
Wdeb_Options::populate();

require_once WDEB_PLUGIN_BASE_DIR . '/lib/class_wdeb_plugins_handler.php';
Wdeb_PluginsHandler::init();

add_action('wp_logout', 'wdeb_reset_autostart');

if (is_admin()) {
	require_once WDEB_PLUGIN_BASE_DIR . '/lib/class_wdeb_admin_form_renderer.php';
	require_once WDEB_PLUGIN_BASE_DIR . '/lib/class_wdeb_admin_pages.php';
	require_once WDEB_PLUGIN_BASE_DIR . '/lib/class_wdeb_tooltips.php';
	require_once WDEB_PLUGIN_BASE_DIR . '/lib/class_wdeb_wizard.php';
	Wdeb_AdminPages::serve();
	Wdeb_Tooltips::serve();
	Wdeb_Wizard::serve();
}