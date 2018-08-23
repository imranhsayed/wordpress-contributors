<?php
/**
 * Uninstall file for the plugin
 * This magic file is run automatically when the users deletes the plugin
 *
 * @package WordPress Contributors
 */

// If uninstall.php is not called by WordPress, die.
if ( !defined('WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

// Delete the post meta key for contributors when the plugin is deleted.
global $wpdb;
$table = $wpdb->prefix.'postmeta';
$wpdb->delete ( $table, array( 'meta_key' => 'wpco_post_contributor_ids') );

// Delete options 'wpco_post_types'
delete_option( 'wpco_post_types' );
