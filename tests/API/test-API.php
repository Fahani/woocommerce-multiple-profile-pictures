<?php
/**
 * Class TestAPI
 *
 * @package Woocommerce_Multiple_Profile_Pictures
 */

use WMPP\API\Api;

/**
 * Sample test case.
 */
class TestAPI extends WP_UnitTestCase {

	/** @var API */
	private $api;

	private $expected_json_data;

	private $user_id;

	public function setUp() {
		parent::setUp();
		$this->api = new API();

		$this->user_id = 1;

		$this->expected_json_data = [
			[
				'user_id'  => $this->user_id,
				'pic_name' => '2020/06/picture_a.jpg',
				'pic_mime' => 'image/jpeg'
			]
		];
	}

	/**
	 * Creates dummy data into the database (post, post_meta and user_meta)
	 */
	private function create_user_post_meta() {
		$post_id = wp_insert_post( [
			'post_title'  => 'picture_a',
			'post_status' => 'publish',
			'post_author' => $this->user_id,
			'post_type'   => 'attachment',
		] );

		add_post_meta( $post_id, '_wp_attachment_metadata', [
			'width'      => 1240,
			'height'     => 1748,
			'file'       => '2020/06/picture_a.jpg',
			'sizes'      =>
				[
					'medium'                        =>
						[
							'file'      => 'picture_a-213x300.jpg',
							'width'     => 213,
							'height'    => 300,
							'mime-type' => 'image/jpeg',
						],
					'large'                         =>
						[
							'file'      => 'picture_a-726x1024.jpg',
							'width'     => 726,
							'height'    => 1024,
							'mime-type' => 'image/jpeg',
						],
					'thumbnail'                     =>
						[
							'file'      => 'picture_a-150x150.jpg',
							'width'     => 150,
							'height'    => 150,
							'mime-type' => 'image/jpeg',
						],
					'medium_large'                  =>
						[
							'file'      => 'picture_a-768x1083.jpg',
							'width'     => 768,
							'height'    => 1083,
							'mime-type' => 'image/jpeg',
						],
					'1536x1536'                     =>
						[
							'file'      => 'picture_a-1090x1536.jpg',
							'width'     => 1090,
							'height'    => 1536,
							'mime-type' => 'image/jpeg',
						],
					'woocommerce_thumbnail'         =>
						[
							'file'      => 'picture_a-324x324.jpg',
							'width'     => 324,
							'height'    => 324,
							'mime-type' => 'image/jpeg',
							'uncropped' => false,
						],
					'woocommerce_single'            =>
						[
							'file'      => 'picture_a-416x586.jpg',
							'width'     => 416,
							'height'    => 586,
							'mime-type' => 'image/jpeg',
						],
					'woocommerce_gallery_thumbnail' =>
						[
							'file'      => 'picture_a-100x100.jpg',
							'width'     => 100,
							'height'    => 100,
							'mime-type' => 'image/jpeg',
						],
					'shop_catalog'                  =>
						[
							'file'      => 'picture_a-324x324.jpg',
							'width'     => 324,
							'height'    => 324,
							'mime-type' => 'image/jpeg',
						],
					'shop_single'                   =>
						[
							'file'      => 'picture_a-416x586.jpg',
							'width'     => 416,
							'height'    => 586,
							'mime-type' => 'image/jpeg',
						],
					'shop_thumbnail'                =>
						[
							'file'      => 'picture_a-100x100.jpg',
							'width'     => 100,
							'height'    => 100,
							'mime-type' => 'image/jpeg',
						],
				],
			'image_meta' =>
				[
					'aperture'          => '0',
					'credit'            => '',
					'camera'            => '',
					'caption'           => '',
					'created_timestamp' => '0',
					'copyright'         => '',
					'focal_length'      => '0',
					'iso'               => '0',
					'shutter_speed'     => '0',
					'title'             => '',
					'orientation'       => '0',
					'keywords'          =>
						[
						],
				],
		] );

		add_user_meta( $this->user_id, 'main_picture', $post_id );
	}

	/**
	 * Tests if the function 'register_actions_filters' is registered into the action 'plugin_loaded'
	 */
	public function test_register() {
		$this->api->register();
		$this->assertEquals( 10, has_action( 'plugins_loaded', [ $this->api, 'register_actions_filters' ] ) );
	}

	/**
	 * Tests if the function 'register_endpoint' is registered into the action 'rest_api_init' and the function
	 * 'deny_if_logout' registered into the filter 'rest_authentication_errors'
	 */
	public function test_register_actions_filters() {
		$this->api->register_actions_filters();
		$this->assertEquals( 10, has_action( 'rest_api_init', [ $this->api, 'register_endpoint' ] ) );
		$this->assertEquals( 10, has_filter( 'rest_authentication_errors', [ $this->api, 'deny_if_logout' ] ) );
	}

	/**
	 * Tests if the function 'register_endpoint' registers correctly the new endpoint '/wc/v1/profiles'
	 */
	public function test_register_endpoint() {
		$this->api->register_actions_filters();
		$routes = rest_get_server()->get_routes();
		$this->api->register_endpoint();
		$this->assertTrue( array_key_exists( '/wc/v1/profiles', $routes ) );
	}

	/**
	 * Tests if the new endpoint callback 'return_profile_pictures' will return the expected data
	 */
	public function test_return_profile_pictures() {
		$response = $this->api->return_profile_pictures();
		$this->assertEmpty( $response->get_data() );

		// Creates dummy data
		$this->create_user_post_meta();

		/** var WP_REST_Response */
		$response = $this->api->return_profile_pictures();

		$this->assertEquals( $this->expected_json_data, $response->get_data() );
	}

	/**
	 * Tests if the register 'deny_if_logout' function will block logged out user when accessing the API
	 */
	public function test_deny_if_logout() {
		// User logout gets an error
		$_SERVER['REQUEST_URI'] = 'wc/v1/profiles';
		$response               = $this->api->deny_if_logout( $this->expected_json_data );
		$this->assertInstanceOf( 'WP_Error', $response );

		// Use logged in gets the data
		wp_clear_auth_cookie();
		wp_set_current_user ( $this->user_id );
		wp_set_auth_cookie  ( $this->user_id );
		$response               = $this->api->deny_if_logout( $this->expected_json_data );
		$this->assertEquals( $this->expected_json_data, $response );
	}
}