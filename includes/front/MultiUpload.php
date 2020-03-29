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
	 * Registers the actions to insert the picture inside the Account details form
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'woocommerce_before_edit_account_form', [ $this, 'enqueue_assets' ] );
		add_action( 'woocommerce_edit_account_form_tag', [ $this, 'add_multipart_to_form' ] );
		add_action( 'woocommerce_edit_account_form', [ $this, 'add_picture_selection_to_form' ] );
	}

	/**
	 * It loads assets into the page
	 * @return void
	 * @since 1.0.0
	 */
	public function enqueue_assets() {
		wp_enqueue_style( 'wmpp_style', WMPP_PLUGIN_URL . '/assets/css/style.css' );
	}

	/**
	 * Loads the encoding multi part attribute into the form from a template
	 * @return void
	 * @since 1.0.0
	 */
	public function add_multipart_to_form() {
		include( WMPP_DIR_PATH . 'templates/myaccount/add-multi-part-to-form.php' );
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