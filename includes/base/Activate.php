<?php

namespace WMPP\base;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

use WMPP\database\ActivationRepository;

/**
 * This class takes care of the activation process
 * @package WMPP\base
 * @since 1.0.0
 */
class Activate {

	/** The plugin name, for displaying notices */
	public const PLUGIN_NAME = 'WooCommerce Multiple Profile Pictures';

	/** Minimum PHP version required by this plugin */
	private const MINIMUM_PHP_VERSION = '7.1.0';

	/** Minimum WordPress version required by this plugin */
	private const MINIMUM_WP_VERSION = '4.7.1';

	/** minimum WooCommerce version required by this plugin */
	private const MINIMUM_WC_VERSION = '3.0.9';

	/** @var ActivationRepository */
	private $activation_repository;

	/**
	 * Activate constructor.
	 *
	 * @param ActivationRepository $activation_repository
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function __construct( ActivationRepository $activation_repository ) {
		$this->activation_repository = $activation_repository;
	}

	/**
	 * Execute the actions to activate the plugin: checks, create table and directory structure
	 */
	public function activate() {
		$this->activation_check();
		$this->activation_repository->create_tables();
		$this->create_directories();

		$this->allow_customer_upload();
	}

	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
	 * @return void
	 * @since 1.0.0
	 */
	private function activation_check() {
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
	private function deactivate_and_die( $plugin_name, $requirement, $version_needed, $current_version ) {
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
	 * Creates the structure of folders inside uploads. Where the pictures for users and orders will be stored.
	 * @return void
	 * @since 1.0.0
	 */
	private function create_directories() {
		if ( ! is_dir( wp_upload_dir()['basedir'] . '/wmpp' ) ) {
			mkdir( wp_upload_dir()['basedir'] . '/wmpp', 0755 );
		}
		if ( ! is_dir( wp_upload_dir()['basedir'] . '/wmpp/users' ) ) {
			mkdir( wp_upload_dir()['basedir'] . '/wmpp/users', 0755 );
		}
		if ( ! is_dir( wp_upload_dir()['basedir'] . '/wmpp/orders' ) ) {
			mkdir( wp_upload_dir()['basedir'] . '/wmpp/orders', 0755 );
		}
	}

	/**
	 * Add the capability to upload files to contributor role
	 * @return void
	 * @since 1.0.0
	 */
	private function allow_customer_upload() {
		$customer = get_role( 'customer' );
		if ( $customer ) {
			$customer->add_cap('upload_files');
			$customer->add_cap('delete_posts');
		}
	}
}