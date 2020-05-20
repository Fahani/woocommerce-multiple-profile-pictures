<?php

namespace WMPP\order;

use WC_Order;
use WMPP\helpers\Utils;
use WMPP\interfaces\RegisterAction;

/**
 * This class will take care of the order's actions
 * @since 1.0.0
 * @package WMPP\order
 */
class Order implements RegisterAction {

	/**
	 * Triggers the registration of actions and filters when all the plugins are loaded.
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'plugins_loaded', [ $this, 'register_actions_filters' ] );
	}

	/**
	 * It registers the actions to interact with the order
	 * @return void
	 * @since 1.0.0
	 */
	public function register_actions_filters() {
		add_action( 'woocommerce_thankyou', [ $this, 'match_picture_to_order' ] );
		add_action( 'woocommerce_admin_order_data_after_order_details', [ $this, 'insert_picture_in_order_detail' ] );
		add_filter( 'manage_edit-shop_order_columns', [ $this, 'insert_new_picture_column' ] );
		add_action( 'manage_shop_order_posts_custom_column', [ $this, 'insert_picture_in_column' ], 10, 2 );
		add_action( 'before_delete_post', [ $this, 'delete_order_picture_info' ], 10, 1 );
	}

	/**
	 * Inserts a new row into the table woocommerce_mpp_order_picture and also create the picture inside
	 * wp-content/uploads/orders
	 *
	 * @param int $order_id
	 */
	public function match_picture_to_order( $order_id ) {
		$user_main_picture_post_id = get_user_meta( get_current_user_id(), 'main_picture', true );
		$order_picture             = true;
		if ( $user_main_picture_post_id ) {
			$order_picture = get_post_meta( $order_id, 'main_picture' );
		}
		// Avoid F5 in thank you page
		if ( $user_main_picture_post_id != false && $order_picture == false ) {
			$user_main_picture = get_post_meta( $user_main_picture_post_id, '_wp_attachment_metadata', true );
			$origin_path       = wp_upload_dir()['basedir'] . '/' . $user_main_picture['file'];
			$destination_name  = Utils::generate_name( $user_main_picture['sizes']['thumbnail']['mime-type'] );
			$destination_path  = wp_upload_dir()['basedir'] . "/wmpp/orders/$destination_name";

			if ( copy( $origin_path, $destination_path ) ) {
				add_post_meta( $order_id, 'main_picture', $destination_name );
			}
		}
	}

	/**
	 * Inserts into the Order details page, the picture the user had when he made the order
	 *
	 * @param WC_Order $order
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function insert_picture_in_order_detail( $order ) {
		if ( method_exists( $order, 'get_id' ) ) {
			$order_picture = get_post_meta( $order->get_id(), 'main_picture', true );
			if ( ! empty( $order_picture ) ) {
				include( WMPP_DIR_PATH . 'templates/admin/orders/order-details-display-picture.php' );
			}
		}
	}

	/**
	 * Push the new column into the orders list table
	 *
	 * @param array $columns
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function insert_new_picture_column( $columns ) {
		$new_columns = array();

		foreach ( $columns as $column_name => $column_info ) {

			$new_columns[ $column_name ] = $column_info;

			if ( 'cb' === $column_name ) {
				$new_columns['order_profile'] = __( 'Profile', 'wmpp' );
			}
		}

		return $new_columns;
	}

	/**
	 * Display the picture the user had when ordering into the new column we have created. A small picture
	 *
	 * @param string $column
	 * @param int $order_id
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function insert_picture_in_column( $column, $order_id ) {
		if ( 'order_profile' === $column ) {
			$order_picture = get_post_meta( $order_id, 'main_picture', true );

			if ( $order_picture != false ) {
				include( WMPP_DIR_PATH . 'templates/admin/orders/order-preview-display-small-picture.php' );
			}
		}
	}

	/**
	 * Deletes the picture file in (wp-content/uploads/wmpp/orders) and also remove the row in wp_woocommerce_mpp_order_picture
	 *
	 * @param int $order_id
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function delete_order_picture_info( $order_id ) {
		global $post_type;

		if ( $post_type !== 'shop_order' ) {
			return;
		}
		$order_picture = get_post_meta( $order_id, 'main_picture', true );

		if ( $order_picture != false ) {
			if ( unlink( wp_upload_dir()['basedir'] . '/wmpp/orders/' . $order_picture ) ) {
				delete_post_meta($order_id, 'main_picture');
			}
		}
	}
}