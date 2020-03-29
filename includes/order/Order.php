<?php

namespace WMPP\order;

use WMPP\database\Repository;
use WMPP\helpers\Utils;
use WMPP\interfaces\RegisterAction;

/**
 * This class will take care of the order's actions
 * @since 1.0.0
 * @package WMPP\order
 */
class Order implements RegisterAction {

	/** @var Repository */
	private $repository;

	/**
	 * Initializes the attributes of the class
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
	 * Registers the different hooks to interact with the order
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'woocommerce_thankyou', [ $this, 'match_picture_to_order' ] );
	}

	/**
	 * Inserts a new row into the table woocommerce_mpp_order_picture and also create the picture inside
	 * wp-content/uploads/orders
	 *
	 * @param int $order_id
	 */
	public function match_picture_to_order( $order_id ) {
		$main_picture  = $this->repository->get_main_picture_by_user_id( wp_get_current_user()->ID );
		$order_picture = $this->repository->get_picture_by_order_id( $order_id );

		// Avoid F5 in thank you page
		if ( ! empty( $main_picture ) && empty( $order_picture ) ) {
			$destination_name = Utils::generate_name( $main_picture[0]['pic_type'] );
			$destination_path = wp_upload_dir()['basedir'] . "/wmpp/orders/$destination_name";
			$source_path      = wp_upload_dir()['basedir'] . "/wmpp/users/{$main_picture[0]['pic_name']}";

			if ( copy( $source_path, $destination_path ) ) {
				$this->repository->insert_order_picture( $order_id, $destination_name );
			}
		}
	}
}