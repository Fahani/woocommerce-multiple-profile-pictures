<?php

namespace WMPP\admin;

use WMPP\database\Repository;
use WMPP\interfaces\RegisterAction;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

class EditProfile implements RegisterAction {

	private $repository;

	public function __construct( Repository $repository ) {
		$this->repository = $repository;
	}

	public function register() {
		add_action( 'edit_user_profile', [ $this, 'add_picture_info' ], 10, 1 );
	}

	public function add_picture_info( $profile ) {
		wp_enqueue_style( 'wmpp_style', WMPP_PLUGIN_URL . '/assets/css/style.css' );
		$main_picture  = $this->repository->get_main_picture_by_user_id( $profile->ID );
		$rest_pictures = $this->repository->get_no_main_pictures_by_user_id( $profile->ID );
		include( WMPP_DIR_PATH . 'templates/admin/users/edit-profile-display-user-pictures.php' );

	}


}