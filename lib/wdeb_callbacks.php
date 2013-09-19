<?php

function wdeb_reset_autostart () {
	setcookie("wdeb_on", "", time()-60000, '/', COOKIE_DOMAIN);
	$user = wp_get_current_user();
	if (!$user || !$user->ID) return;
	//delete_user_meta($user->ID, "wdeb_autostart");
	delete_user_meta($user->ID, "wdeb_started");
}

function wdeb_current_user_can ($roles) {
	if (current_user_can('manage_network_options')) return false; /* Don't do this for Super Admin */
	if (defined('WDEB_CORE_RESCUE_ADMINS') && WDEB_CORE_RESCUE_ADMINS && current_user_can('manage_options')) return false;
	$roles = $roles ? $roles : array();
	$cap_enter = false;
	foreach ($roles as $cap) {
		if (current_user_can($cap)) {
			$cap_enter = true;
			break;
		}
	}
	return $cap_enter;
}

function wdeb_expand_url ($url) {
	// Passthrough relative URLs
	$is_absolute = preg_match('/^https?:\/\//', $url);
	$url = preg_replace('/^https?:\/\//', '', $url);
	if (is_multisite()) {
		global $blog_id;
		$current_site = get_blog_details($blog_id);
		$root_site = get_blog_details(1);
	
		$current_path = trim(preg_replace('/' . preg_quote($root_site->path, '/') . '/', '', $current_site->path), '/');
		$url = preg_replace('/BLOG_PATH/', $current_path, $url);
	}
	$url = preg_replace('/LOGOUT_URL/', preg_replace('/^https?:\/\//', '', wp_logout_url()), $url);
	
	$url = preg_replace('/\/\/+/', '/', $url);
	if ($is_absolute) $url = (@$_SERVER["HTTPS"] == 'on' ? 'https://' : 'http://') . $url;
	return $url;
}

/**
 * Menu items callbacks section.
 *
 * Menu items callbacks are set as menu item 'check_callback' argument.
 * They are always called for a particular menu item, *after* the
 * user permissions have been checked.
 * Callbacks are called with one optional argument, the menu item itself.
 * They should return TRUE-ish value for default behavior (showing the menu item).
 * If a callback returns FALSE-ish value, the menu item won't be displayed.
 */

function wdeb_supporter_themes_enabled () {
	if (class_exists('ProSites')) {
		global $psts;
		if ($psts && !empty($psts->version) && version_compare($psts->version, '3.3.3', '>=')) {
			$has_themes = false;
		} else {
			$ps_modules = ProSites::get_setting('modules_enabled');
			$ps_modules = $ps_modules ? $ps_modules : array();
			$has_themes = in_array('ProSites_Module_PremiumThemes', $ps_modules);
		}
	} else $has_themes = function_exists('supporter_themes_page');
	return (function_exists('is_supporter') && is_supporter() && $has_themes);
}

function wdeb_supporter_themes_not_enabled () {
	if (class_exists('ProSites')) {
		global $psts;
		if ($psts && !empty($psts->version) && version_compare($psts->version, '3.3.3', '>=')) {
			$has_themes = false;
		} else {
			$ps_modules = ProSites::get_setting('modules_enabled');
			$ps_modules = $ps_modules ? $ps_modules : array();
			$has_themes = in_array('ProSites_Module_PremiumThemes', $ps_modules);
		}
	} else $has_themes = function_exists('supporter_themes_page');
	return !(function_exists('is_supporter') && is_supporter() && $has_themes);
}

function wdeb_not_supporter () {
	return (function_exists('is_supporter') && current_user_can('manage_options') && !is_supporter());
}