<?php

namespace WMPP\admin;

use MultipleProfilePictures;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * Takes care of setting up the Setting page and the settings link into the plugin page
 * @package WMPP\admin
 */
class Settings {

	/**
	 * Registers the needed actions and filters to set up the Settings page.
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_filter( 'plugin_action_links_' . WMPP_BASENAME, [ $this, 'add_settings_link' ] );
		add_action( 'admin_menu', [ $this, 'add_settings_menu' ] );
		add_action( 'admin_init', [ $this, 'register_custom_fields' ] );
	}

	/**
	 * It pushes a new link to our plugin details that redirects us to the Settings page
	 *
	 * @param array $links
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function add_settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=wmpp_settings">' . __( 'Settings', 'wmpp' ) . '</a>';
		array_push( $links, $settings_link );

		return $links;
	}


	/**
	 * It adds a new section into the admin menu that goes to the plugin's settings page
	 * @return void
	 * @since 1.0.0
	 */
	public function add_settings_menu() {
		add_menu_page(
			MultipleProfilePictures::PLUGIN_NAME,
			__( 'WMPP Settings', 'wmpp' ),
			'manage_options',
			'wmpp_settings',
			[ $this, 'settings_page' ],
			'dashicons-format-image',
			110
		);
	}

	/**
	 * Loads the form that contains the inputs for our settings page
	 * @return void
	 * @since 1.0.0
	 */
	public function settings_page() {
		include( WMPP_DIR_PATH . 'templates/admin/settings-form.php' );
	}

	/**
	 * This functions generate the group, section and field for the form inside the settings page.
	 * @return void
	 * @since 1.0.0
	 */
	public function register_custom_fields() {
		// Register setting
		register_setting(
			'wmpp_options_group',
			'max_profile_pictures',
			[
				'default'           => 3,
				'type'              => 'integer',
				'sanitize_callback' => 'intval',
			] );

		// Add setting section
		add_settings_section( 'wmpp_settings_section', __( 'Settings', 'wmpp' ), '', 'wmpp_settings' );

		// Add setting field
		add_settings_field(
			'max_profile_pictures',
			__( 'Max number of profile pictures', 'wmpp' ),
			[ $this, 'add_input_information' ],
			'wmpp_settings',
			'wmpp_settings_section' );

	}

	/**
	 * It loads the numeric input for our settings. The number of pictures an user can have.
	 * @return void
	 * @since 1.0.0
	 */
	public function add_input_information() {
		include( WMPP_DIR_PATH . 'templates/admin/settings-input.php' );
	}
}