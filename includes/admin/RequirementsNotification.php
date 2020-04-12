<?php

namespace WMPP\admin;

use WMPP\interfaces\RegisterAction;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * Shows notifications in admin area when a requirement is missing
 * @package WMPP\admin
 */
class RequirementsNotification implements RegisterAction {

	/**
	 * Registers the needed actions to notify admins about missing requirements.
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'plugins_loaded', [ $this, 'check_wc_active' ] );
	}

	/**
	 * Checks if WooCommerce is still active after our plugin is activated. If not, it shows a message in the admin area.
	 * @return void
	 * @since 1.0.0
	 */
	public function check_wc_active() {
		if ( ! defined( 'WC_VERSION' ) ) {
			add_action( 'admin_notices', function () {
				include( WMPP_DIR_PATH . 'templates/admin/error-woocommerce-not-found.php' );
			} );
		}
	}
}