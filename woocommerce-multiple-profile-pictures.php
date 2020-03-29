<?php

/**
 * Plugin Name: WooCommerce Multiple Profile Pictures
 * Plugin URI: https://github.com/Fahani/woocommerce-multiple-profile-pictures
 * Description: Allows a customer to have <strong>multiple profile pictures</strong> to switch between them. The <strong>profile picture will be save when placing an order</strong>.
 * Version: 1.0.0
 * Author: Nicolás González
 * Author URI: https://github.com/Fahani
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * @package WC-Multiple-Profile-Pictures
 *
 */

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * Plugin main class
 *
 * @since 1.0.0
 */
class MultipleProfilePictures {

	/** the plugin name, for displaying notices */
	const PLUGIN_NAME = 'WooCommerce Multiple Profile Pictures';

	/** minimum PHP version required by this plugin */
	const MINIMUM_PHP_VERSION = '7.1.0';

	/** minimum WooCommerce version required by this plugin */
	//TODO Checks real requirements at the end
	const MINIMUM_WC_VERSION = '3.0.9';

	/** minimum WordPress version required by this plugin */
	//TODO Checks real requirements at the end
	const MINIMUM_WP_VERSION = '4.4';

	/** @var MultipleProfilePictures */
	protected static $instance;

	public function __construct() {

	}

	/**
	 * Ensures only one instance is set.
	 * @since 1.0.0
	 * @return MultipleProfilePictures
	 */
	public static function instance(): MultipleProfilePictures {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

// Fire it up! :)
$myPlugin = MultipleProfilePictures::instance();