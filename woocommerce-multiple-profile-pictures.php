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
 * Text Domain: wmpp
 * Domain Path: /i18n/languages/
 * @package WC-Multiple-Profile-Pictures
 *
 */

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

define( 'WMPP_BASENAME', plugin_basename( __FILE__ ) );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

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
	 * Loads all necessary actions after the plugin has been activated
	 * @return void
	 * @since 1.0.0
	 */
	public function load_plugins() {
		if ( is_plugin_active( WMPP_BASENAME ) ) {
			add_action( 'init', array( $this, 'load_translation' ) );
		}
	}

	/**
	 * Loads the mo file for your translations
	 * @return void
	 * @since 1.0.1
	 */
	public function load_translation() {
		load_plugin_textdomain( 'wmpp', false, dirname( WMPP_BASENAME ) . '/i18n/languages' );
	}

	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 * @return void
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, sprintf( __( 'You cannot clone instances of %s.', 'wmpp' ), get_class( $this ) ), '1.0.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 * @return void
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, sprintf( __( 'You cannot unserialize instances of %s.', 'wmpp' ), get_class( $this ) ), '1.0.0' );
	}

	/**
	 * Ensures only one instance is set.
	 * @return MultipleProfilePictures
	 * @since 1.0.0
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
$myPlugin->load_plugins();