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

use WMPP\admin\Settings;
use WMPP\interfaces\RegisterAction;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

define( 'WMPP_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WMPP_BASENAME', plugin_basename( __FILE__ ) );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Plugin main class
 *
 * @since 1.0.0
 */
class MultipleProfilePictures implements RegisterAction {

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

	/** @var Settings */
	protected $settings;


	public function __construct( Settings $settings ) {
		$this->settings = $settings;

	}

	/**
	 * Registers all the one time actions needed for this plugin to work
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		register_activation_hook( __FILE__, [ $this, 'activation_check' ] );
	}

	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
	 * @return void
	 * @since 1.0.0
	 */
	public function activation_check() {
		if ( ! version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' ) ) {
			$this->deactivate_and_die( self::PLUGIN_NAME, 'PHP', self::MINIMUM_PHP_VERSION, PHP_VERSION );
		}

		if ( ! version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' ) ) {
			$this->deactivate_and_die( self::PLUGIN_NAME, 'WordPress', self::MINIMUM_WP_VERSION, get_bloginfo( 'version' ) );
		}

		if ( ! ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' ) ) ) {
			$this->deactivate_and_die( self::PLUGIN_NAME, 'WooCommerce', self::MINIMUM_WC_VERSION, defined( 'WC_VERSION' ) ? WC_VERSION : '0' );
		}
	}

	/**
	 * Deactivates the plugin and shows an informative message with the missing requirement
	 *
	 * @param string $plugin_name Name of the plugin
	 * @param string $requirement The requirement
	 * @param string $version_needed Minimum version needed
	 * @param string $current_version Current version
	 *
	 * @return void
	 * @since 1.0.0
	 */
	protected function deactivate_and_die( $plugin_name, $requirement, $version_needed, $current_version ) {
		deactivate_plugins( WMPP_BASENAME );

		wp_die( sprintf(
				__( '%1$s could not be activated. The minimum %2$s version required for this plugin is %3$s. You are running %4$s.', 'wmpp' ),
				$plugin_name,
				$requirement,
				$version_needed,
				$current_version )
		);
	}


	/**
	 * Loads all necessary actions after the plugin has been activated
	 * @return void
	 * @since 1.0.0
	 */
	public function load_plugins() {
		if ( is_plugin_active( WMPP_BASENAME ) ) {
			add_action( 'init', [ $this, 'load_translation' ] );
			add_action( 'plugins_loaded', [ $this, 'check_wc_active' ] );
			add_action( 'plugins_loaded', [ $this, 'load_plugin_dependencies' ] );
		}
	}

	/**
	 * It fires up the injected dependencies.
	 * @return void
	 * @since 1.0.0
	 */
	public function load_plugin_dependencies() {
		$this->settings->register();
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
	 *
	 * @param Settings $settings
	 *
	 * @return MultipleProfilePictures
	 * @since 1.0.0
	 */
	public static function instance( Settings $settings ): MultipleProfilePictures {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $settings );
		}

		return self::$instance;
	}
}

// Fire it up! :)
$my_plugin = MultipleProfilePictures::instance( new Settings() );
$my_plugin->register();
$my_plugin->load_plugins();