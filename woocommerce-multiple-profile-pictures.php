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

use WMPP\admin\DeleteUser;
use WMPP\admin\EditProfile;
use WMPP\admin\Settings;
use WMPP\API\Api;
use WMPP\database\Repository;
use WMPP\front\MultiUpload;
use WMPP\order\Order;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

define( 'WMPP_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WMPP_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'WMPP_BASENAME', plugin_basename( __FILE__ ) );

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

/**
 * Plugin main class
 *
 * @since 1.0.0
 */
class MultipleProfilePictures {


	/** @var MultipleProfilePictures */
	protected static $instance;

	/** @var Settings */
	protected $settings;

	/** @var Repository */
	protected $repository;

	/** @var MultiUpload */
	protected $multi_upload;

	/** @var EditProfile */
	protected $edit_profile;

	/** @var DeleteUser */
	protected $delete_user;

	/** @var Api */
	protected $api;

	/** @var Order */
	protected $order;

	/**
	 * Initializes attributes.
	 *
	 * @param Settings $settings
	 * @param Repository $repository
	 * @param MultiUpload $multi_upload
	 * @param EditProfile $edit_profile
	 * @param DeleteUser $delete_user
	 * @param Api $api
	 * @param Order $order
	 *
	 * @since 1.0.0
	 */
	public function __construct(
		Settings $settings,
		Repository $repository,
		MultiUpload $multi_upload,
		EditProfile $edit_profile,
		DeleteUser $delete_user,
		Api $api,
		Order $order
	) {
		$this->settings     = $settings;
		$this->repository   = $repository;
		$this->multi_upload = $multi_upload;
		$this->edit_profile = $edit_profile;
		$this->delete_user  = $delete_user;
		$this->api          = $api;
		$this->order        = $order;
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
		$this->multi_upload->register();
		$this->edit_profile->register();
		$this->delete_user->register();
		$this->api->register();
		$this->order->register();
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
	 * @param Repository $repository
	 * @param MultiUpload $multi_upload
	 * @param EditProfile $edit_profile
	 * @param DeleteUser $delete_user
	 * @param Api $api
	 * @param Order $order
	 *
	 * @return MultipleProfilePictures
	 * @since 1.0.0
	 */
	public static function instance(
		Settings $settings,
		Repository $repository,
		MultiUpload $multi_upload,
		EditProfile $edit_profile,
		DeleteUser $delete_user,
		Api $api,
		Order $order
	): MultipleProfilePictures {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self( $settings, $repository, $multi_upload, $edit_profile, $delete_user, $api, $order );
		}

		return self::$instance;
	}
}

/**
 * Triggers the actions during plugin activation
 *
 * @return void
 * @since 1.0.0
 */
function activate_wmpp_plugin() {
	$activate = new \WMPP\base\Activate( new \WMPP\database\ActivationRepository() );
	$activate->activate();

}

register_activation_hook( __FILE__, 'activate_wmpp_plugin' );

$repository = new Repository();

// Fire it up! :)
$my_plugin = MultipleProfilePictures::instance(
	new Settings(),
	$repository,
	new MultiUpload( $repository ),
	new EditProfile( $repository ),
	new DeleteUser( $repository ),
	new Api( $repository ),
	new Order( $repository ) );

$my_plugin->load_plugins();