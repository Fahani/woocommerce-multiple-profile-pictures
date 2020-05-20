<?php
/**
 * Trigger this file when uninstalling the plugin
 *
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die( 'This is not what you are looking for' );
}

/**
 * Removes the main_picture meta from orders purchased by users with main picture
 *
 * @return void
 * @since 1.0.0
 */
function delete_meta_from_orders() {
	delete_post_meta_by_key( 'main_picture' );
}

/**
 * Removes the main_picture meta from users when they selected a main picture
 *
 * @return void
 * @since 1.0.0
 */
function delete_meta_from_users() {
	$users_with_main_picture = get_users( [ 'meta_key' => 'main_picture' ] );
	foreach ( $users_with_main_picture as $user ) {
		delete_user_meta( $user->ID, 'main_picture' );
	}
}

/**
 * Removes the wp_option that stores the number of max pictures an user can upload
 *
 * @return void
 * @since 1.0.0
 */
function delete_wmpp_settings() {
	delete_option( 'max_profile_pictures' );
}

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

deleteAll( wp_upload_dir()['basedir'] . '/wmpp' );
delete_meta_from_orders();
delete_meta_from_users();
delete_wmpp_settings();