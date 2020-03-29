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
		add_action( 'woocommerce_edit_account_form_tag', [ $this, 'add_multipart_to_form' ] );
	}

	/**
	 * Loads the encoding multi part attribute into the form from a template
	 * @return void
	 * @since 1.0.0
	 */
	public function add_multipart_to_form() {
		include( WMPP_DIR_PATH . 'templates/myaccount/add-multi-part-to-form.php' );
	}


}