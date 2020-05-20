<?php

namespace WMPP\API;

use WMPP\interfaces\RegisterAction;
use WP_Error;

/**
 * This class creates a new endpoint www.yourdomain.com/wp-json/wc/v1/profiles
 * @since 1.0.0
 * @package WMPP\API
 */
class Api implements RegisterAction {

	/**
	 * Triggers the registration of actions and filters when all the plugins are loaded.
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'plugins_loaded', [ $this, 'register_actions_filters' ] );
	}

	/**
	 * It registers the actions to create a new endpoint and deny the access if the user is not logged in
	 * @return void
	 * @since 1.0.0
	 */
	public function register_actions_filters() {
		add_action( 'rest_api_init', [ $this, 'register_endpoint' ] );
		add_filter( 'rest_authentication_errors', [ $this, 'deny_if_logout' ] );
	}

	/**
	 * Registers the new endpoint wp-json/wc/v1/profiles
	 * @return void
	 * @since 1.0.0
	 */
	public function register_endpoint() {
		register_rest_route( 'wc/v1/', 'profiles',
			[
				'methods'  => 'GET',
				'callback' => [ $this, 'return_profile_pictures' ],
			] );
	}

	/**
	 * Function executed when calling the endpoint. It returns all the main pictures of the customers
	 * @return mixed|\WP_REST_Response
	 * @since 1.0.0
	 */
	public function return_profile_pictures() {
		$users_with_main_picture = get_users( [ 'meta_key' => 'main_picture' ] );
		$response                = [];
		foreach ( $users_with_main_picture as $user ) {
			$main_picture_post_id = get_user_meta( $user->ID, 'main_picture', true );
			$attachment           = get_post_meta( $main_picture_post_id, '_wp_attachment_metadata', true );
			$response[]           = [
				'user_id'  => $user->ID,
				'pic_name' => $attachment['file'],
				'pic_mime' => $attachment['sizes']['thumbnail']['mime-type']
			];
		}

		return rest_ensure_response( $response );
	}

	/**
	 * Deny the access of that route if the user is not logged in
	 *
	 * @param WP_Error|null|bool $params
	 *
	 * @return WP_Error
	 * @since 1.0.0
	 */
	public function deny_if_logout( $params ) {
		if ( strpos( $_SERVER['REQUEST_URI'], 'wc/v1/profiles' ) !== false && ! is_user_logged_in() ) {
			return new WP_Error( 'logout', __( 'To access this endpoint, you need to be logged in', 'wmpp' ) );
		}

		return $params;
	}
}