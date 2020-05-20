<?php

namespace WMPP\admin;

use WMPP\interfaces\RegisterAction;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * Takes care of displaying customer's picture under Edit User page (admin panel)
 *
 * @since 1.0.0
 * @package WMPP\admin
 */
class EditProfile implements RegisterAction {

	/**
	 * Triggers the registration of actions and filters when all the plugins are loaded.
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'plugins_loaded', [ $this, 'register_actions_filters' ] );
	}

	/**
	 * Registers the action needed to display info under Edit Profile page
	 * @return void
	 * @since 1.0.0
	 */
	public function register_actions_filters() {
		add_action( 'edit_user_profile', [ $this, 'add_picture_info' ], 10, 1 );
	}

	/**
	 * Inject into the Edit Profile page the information of his pictures
	 *
	 * @param \WP_User $profile
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function add_picture_info( $profile ) {
		wp_enqueue_style( 'wmpp_style', WMPP_PLUGIN_URL . '/assets/css/style.css' );
		$main_picture_url = null;

		$main_picture_post_id = get_user_meta( $profile->ID, 'main_picture', true );
		if ( $main_picture_post_id != false ) {
			$main_picture_url = get_post( $main_picture_post_id )->guid;
		}

		$rest_pictures = get_posts( [
				'numberposts' => - 1,
				'author'      => $profile->ID,
				'post_type'   => 'attachment',
				'exclude'     => $main_picture_post_id != false ? [ $main_picture_post_id ] : []
			]
		);

		include( WMPP_DIR_PATH . 'templates/admin/users/edit-profile-display-user-pictures.php' );
	}
}