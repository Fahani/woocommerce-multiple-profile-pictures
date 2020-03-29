<?php

namespace WMPP\admin;

use WMPP\database\Repository;
use WMPP\interfaces\RegisterAction;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * Takes care of deleting pictures of the user if we delete him from WordPress
 * @since 1.0.0
 * @package WMPP\admin
 */
class DeleteUser implements RegisterAction {

	/** @var Repository */
	private $repository;

	/**
	 * Initializes attributes of the class
	 *
	 * @param Repository $repository
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct( Repository $repository ) {
		$this->repository = $repository;
	}

	/**
	 * Registers the action of deleting an user
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'delete_user', [ $this, 'delete_user_info' ], 10, 1 );
	}

	/**
	 * Deletes all the pictures of the given user id from wp-content/uploads/users and also from the table
	 * wp_woocommerce_mpp_user_picture
	 *
	 * @param int $user_id
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function delete_user_info( $user_id ) {
		$pictures = $this->repository->get_pictures_by_user_id( $user_id );

		foreach ( $pictures as $picture ) {
			if ( unlink( wp_upload_dir()['basedir'] . '/wmpp/users/' . $picture['pic_name'] ) ) {
				$this->repository->delete_picture_by_picture_id( $picture['mpp_user_picture_id'] );
			}
		}
	}
}