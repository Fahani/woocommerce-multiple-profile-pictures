<?php

namespace WMPP\front;

use WMPP\interfaces\RegisterAction;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * This class will take care of insert the profile pictures functionality into the Account details page of the user
 * @since 1.0.0
 */
class MultiUpload implements RegisterAction {

	/**
	 * Triggers the registration of actions and filters when all the plugins are loaded.
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'plugins_loaded', [ $this, 'register_actions_filters' ] );
	}

	/**
	 * Registers the actions and filters related to insert the picture inside the Account details form
	 * @return void
	 * @since 1.0.0
	 */
	public function register_actions_filters() {
		add_action( 'woocommerce_before_edit_account_form', [ $this, 'enqueue_assets' ] );
		add_action( 'woocommerce_edit_account_form', [ $this, 'add_picture_selection_to_form' ] );

		add_filter( 'ajax_query_attachments_args', [ $this, 'filter_attachments_by_current_user' ] );
		add_action( 'wp_ajax_set_main_picture', [ $this, 'ajax_set_main_picture' ] );
		add_action( 'wp_ajax_delete_main_picture', [ $this, 'ajax_delete_main_picture' ] );
		add_filter( 'wp_handle_upload_prefilter', [ $this, 'check_if_user_can_upload_more_pictures' ] );
	}

	/**
	 * It checks if the user can upload pictures or he reached the max limit already
	 *
	 * @param $file
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	function check_if_user_can_upload_more_pictures( $file ) {
		$max_pictures = get_option( 'max_profile_pictures' );
		if ( wp_count_posts( 'attachment' )->inherit > $max_pictures ) {
			$file['error'] = sprintf( __( "You can't upload more than %s pictures" ), $max_pictures );
		}

		return $file;
	}

	/**
	 * Ajax called when the user select a main picture
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function ajax_set_main_picture() {
		$user_id = get_current_user_id();
		if ( isset( $_POST['id'] ) && $user_id ) {
			$main_picture = get_user_meta( $user_id, 'main_picture' );
			if ( ! $main_picture ) {
				add_user_meta( $user_id, 'main_picture', $_POST['id'] );
			} else {
				update_user_meta( $user_id, 'main_picture', $_POST['id'] );
			}
		}
	}

	/**
	 * Ajax called when a customer deletes his profile picture
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function ajax_delete_main_picture() {
		$user_id = get_current_user_id();
		if ( isset( $_POST['id'] ) && $user_id ) {
			delete_user_meta( $user_id, 'main_picture' );
		}
	}

	/**
	 * It makes sure the customer only sees their own uploaded profile pictures
	 *
	 * @param $query
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	function filter_attachments_by_current_user( $query ) {
		$query['author'] = get_current_user_id();

		return $query;
	}

	/**
	 * It loads assets into the page
	 * @return void
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_media();
		wp_enqueue_style( 'wmpp_style', WMPP_PLUGIN_URL . '/assets/css/style.css' );
		wp_enqueue_script( 'wmpp_script', WMPP_PLUGIN_URL . '/assets/js/media_uploader.js' );
	}

	/**
	 * Loads the template that will display the information about the user's pictures and allow him to upload,
	 * delete and select a main picture
	 * @return void
	 * @since 1.0.0
	 */
	public function add_picture_selection_to_form() {
		include( WMPP_DIR_PATH . 'templates/myaccount/add-picture-selection-to-form.php' );
	}

}