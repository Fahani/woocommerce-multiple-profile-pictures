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

use WMPP\base\Activate;
use WMPP\base\Init;
use WMPP\database\ActivationRepository;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

define( 'WMPP_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'WMPP_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'WMPP_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Triggers the actions during plugin activation
 *
 * @return void
 * @since 1.0.0
 */
function activate_wmpp_plugin() {
	$activate = new Activate( new ActivationRepository() );
	$activate->activate();
}

register_activation_hook( __FILE__, 'activate_wmpp_plugin' );

// Fire it up! :)
Init::register_services();