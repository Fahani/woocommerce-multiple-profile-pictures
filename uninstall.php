<?php


/**
 * Trigger this file when uninstalling the plugin
 *
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die('This is not what you are looking for');
}

global $wpdb;

$wpdb->query( "DROP TABLE `{$wpdb->prefix}woocommerce_mpp_user_picture`" );
$wpdb->query( "DROP TABLE `{$wpdb->prefix}woocommerce_mpp_order_picture`" );


/**
 * Deletes folder, files and sub folders
 *
 * @param string $dir
 *
 * @return void
 * @since 1.0.0
 */
function deleteAll( $dir ) {
	foreach ( glob( $dir . '/*' ) as $file ) {
		if ( is_dir( $file ) ) {
			deleteAll( $file );
		} else {
			unlink( $file );
		}
	}
	rmdir( $dir );
}

deleteAll(wp_upload_dir()['basedir'] . '/wmpp');