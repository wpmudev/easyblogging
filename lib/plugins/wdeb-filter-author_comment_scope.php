<?php
/*
Plugin Name: Author Comments scope
Description: Filters out comments in comments list to show only the ones on the user-authord posts for your non-admin users.
Plugin URI: http://premium.wpmudev.org/project/easy-blogging
Version: 1.0
Author: Ve Bailovity (Incsub)
*/

class Wdeb_Menu_AuthorCommentsScope {

	private function __construct () {}

	public static function serve () {
		$me = new Wdeb_Menu_AuthorCommentsScope;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		if (is_admin() && !is_network_admin()) add_action('wdeb_admin-register_settings-settings', array($this, 'dispatch_comment_display_filter'));
	}

	function dispatch_comment_display_filter () {
		if (current_user_can('manage_options')) return false; // Admin (or better) use - nothing to do here
		add_filter('wp_count_comments', array($this, 'get_filtered_comments_count_for_author'));
		add_action('edit-comments', array($this, 'filter_out_unrelated_comments_for_author'));
	}

	function filter_out_unrelated_comments_for_author ($query) {
		if (!defined('WDEB_IS_IN_EASY_MODE')) return false;
		if (!WDEB_IS_IN_EASY_MODE) return false;
		
		$user = wp_get_current_user();
		$query->query_vars['post_author'] = $user->ID;
	}

	function get_filtered_comments_count_for_author ($stats, $post_id=false) {
		if (!defined('WDEB_IS_IN_EASY_MODE')) return $stats;
		if (!WDEB_IS_IN_EASY_MODE) return $stats;

		if ($post_id) return $stats; // Let WP take care of that - hopefully it'll do the right thing
		if (!empty($stats)) return $stats; // Yeah...

		global $wpdb;
		$user = wp_get_current_user();
		$count = wp_cache_get("comments-eab_author_filtered-{$user->ID}", 'counts');

		if (false !== $count) return $count;

		$post_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_author={$user->ID}");
		if (!$post_ids) die('wtf');
		$where = 'WHERE comment_post_ID IN (' . join(',', $post_ids) . ')';

		// Below is taken from wp-includes/comment.php::wp_count_comments
		$count = $wpdb->get_results( "SELECT comment_approved, COUNT( * ) AS num_comments FROM {$wpdb->comments} {$where} GROUP BY comment_approved", ARRAY_A );

		$total = 0;
		$approved = array('0' => 'moderated', '1' => 'approved', 'spam' => 'spam', 'trash' => 'trash', 'post-trashed' => 'post-trashed');
		foreach ( (array) $count as $row ) {
			// Don't count post-trashed toward totals
			if ( 'post-trashed' != $row['comment_approved'] && 'trash' != $row['comment_approved'] )
				$total += $row['num_comments'];
			if ( isset( $approved[$row['comment_approved']] ) )
				$stats[$approved[$row['comment_approved']]] = $row['num_comments'];
		}

		$stats['total_comments'] = $total;
		foreach ( $approved as $key ) {
			if ( empty($stats[$key]) )
				$stats[$key] = 0;
		}

		$stats = (object) $stats;
		// End of taken code...

		wp_cache_set("comments-eab_author_filtered-{$user->ID}", $stats, 'counts');
		return $stats;
	}
}

if (is_admin()) Wdeb_Menu_AuthorCommentsScope::serve();