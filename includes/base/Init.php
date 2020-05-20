<?php

namespace WMPP\base;


use WMPP\admin\EditProfile;
use WMPP\admin\RequirementsNotification;
use WMPP\admin\Settings;
use WMPP\API\Api;
use WMPP\front\MultiUpload;
use WMPP\order\Order;
use WMPP\translations\EnableTranslations;

/**
 * This class initializes all the services involved in the life of this plugin
 * @package WMPP\base
 */
final class Init {

	/**
	 * Listing all the classes will use in our plugin
	 *
	 * @return array An array with the different classes to load
	 * @since 1.0.0
	 */
	private static function get_services() {
		return [
			new RequirementsNotification(),
			new EnableTranslations(),
			new Settings(),
			new MultiUpload(),
			new EditProfile(),
			new Api(),
			new Order()
		];
	}

	/**
	 * Calling the register function for each of the defined classes in our plugin
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public static function register_services() {
		if ( is_plugin_active( WMPP_BASENAME ) ) {
			foreach ( self::get_services() as $class ) {
				if ( method_exists( $class, 'register' ) ) {
					$class->register();
				}
			}
		}
	}
}