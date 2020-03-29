<?php

namespace WMPP\admin;

use WMPP\database\Repository;
use WMPP\interfaces\RegisterAction;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * Takes care of displaying customer's picture under Edit User page (admin panel)
 *
 * @since 1.0.0
 * @package WMPP\admin
 */
class EditProfile implements RegisterAction {

	/** @var Repository */
	private $repository;

	/**
	 * Initializes class attributes
	 *
	 * @param Repository $repository
	 *
	 * @return void
	 */
	public function __construct( Repository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Register the action needed to display info under Edit Profile page
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
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
		$main_picture  = $this->repository->get_main_picture_by_user_id( $profile->ID );
		$rest_pictures = $this->repository->get_no_main_pictures_by_user_id( $profile->ID );
		include( WMPP_DIR_PATH . 'templates/admin/users/edit-profile-display-user-pictures.php' );
	}
}