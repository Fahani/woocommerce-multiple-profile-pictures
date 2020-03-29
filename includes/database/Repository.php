<?php


namespace WMPP\database;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * This class will take care of the interactions with the database
 * @package WMPP\database
 * @since 1.0.0
 */
class Repository {

	private $db;

	/**
	 * Initialize class attributes
	 * @return void
	 * @since 1.0.0
	 */
	function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	 * Removes a picture (row) given a picture id
	 *
	 * @param int $mpp_user_picture_id
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function delete_picture_by_picture_id( $mpp_user_picture_id ) {
		$sql = "DELETE FROM `{$this->db->prefix}woocommerce_mpp_user_picture` WHERE mpp_user_picture_id = %d";

		$this->db->query( $this->db->prepare( $sql, $mpp_user_picture_id ) );
	}

	/**
	 * Returns, given an user id, all the pictures of an user that aren't his main picture.
	 *
	 * @param int $user_id
	 *
	 * @return array|object|null
	 * @since 1.0.0
	 *
	 */
	function get_no_main_pictures_by_user_id( $user_id ) {
		$sql = "SELECT * FROM `{$this->db->prefix}woocommerce_mpp_user_picture` WHERE active = 0 and user_id = %d";

		return $this->db->get_results( $this->db->prepare( $sql, $user_id ), ARRAY_A );
	}

	/**
	 * Returns the main picture of an user given his id
	 *
	 * @param int $user_id
	 *
	 * @return array|object|null
	 * @since 1.0.0
	 *
	 */
	function get_main_picture_by_user_id( $user_id ) {
		$sql = "SELECT * FROM `{$this->db->prefix}woocommerce_mpp_user_picture` WHERE active = 1 and user_id = %d";

		return $this->db->get_results( $this->db->prepare( $sql, $user_id ), ARRAY_A );
	}

	/**
	 * Returns a picture given a picture id and an user id
	 *
	 * @param int $mpp_user_picture_id
	 * @param int $user_id
	 *
	 * @return array|object|null
	 * @since 1.0.0
	 */
	function get_picture_by_picture_id_and_user_id( $mpp_user_picture_id, $user_id ) {
		$sql = "SELECT * FROM `{$this->db->prefix}woocommerce_mpp_user_picture` WHERE mpp_user_picture_id = %d and user_id = %d";

		return $this->db->get_results( $this->db->prepare( $sql, $mpp_user_picture_id, $user_id ), ARRAY_A );
	}

	/**
	 * It unsets the main picture of the given user id
	 *
	 * @param int $user_id
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function unset_main_picture_by_user_id( $user_id ) {
		$sql = "UPDATE `{$this->db->prefix}woocommerce_mpp_user_picture` SET active=0 WHERE active=1 and user_id = %d";

		$this->db->query( $this->db->prepare( $sql, $user_id ) );
	}

	/**
	 * It sets a picture as main picture given its id
	 *
	 * @param int $mpp_user_picture_id
	 *
	 * @return void
	 * @since 1.0.0
	 */
	function set_main_picture_by_picture_id( $mpp_user_picture_id ) {
		$sql = "UPDATE `{$this->db->prefix}woocommerce_mpp_user_picture` SET active=1 WHERE mpp_user_picture_id = %d";

		$this->db->query( $this->db->prepare( $sql, $mpp_user_picture_id ) );
	}


	/**
	 * Returns the number of pictures an user as by its id
	 *
	 * @param int $user_id
	 *
	 * @return int
	 * @since 1.0.0
	 */
	function get_number_pics_by_user( $user_id ) {
		$sql = "SELECT COUNT(*) AS number FROM `{$this->db->prefix}woocommerce_mpp_user_picture` WHERE user_id = %d";

		return (int) $this->db->get_results( $this->db->prepare( $sql, $user_id ), ARRAY_A )[0]['number'];
	}

	/**
	 * Insert a new picture in wp_woocommerce_mpp_user_picture table and returns its id
	 *
	 * @param int $user_id
	 * @param string $name
	 * @param string $type
	 * @param int $active
	 *
	 * @return int
	 * @since 1.0.0
	 */
	function insert_picture( $user_id, $name, $type, $active ) {
		$sql = "INSERT INTO `{$this->db->prefix}woocommerce_mpp_user_picture` (user_id, pic_name, pic_type, active) 
		VALUES (%d, %s, %s, %d)";
		$this->db->query( $this->db->prepare( $sql, $user_id, $name, $type, $active ) );

		return $this->db->insert_id;
	}


	/**
	 * Creates the two table will be using in the plugin
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function create_tables() {
		$this->db->query( "
		CREATE TABLE IF NOT EXISTS `{$this->db->prefix}woocommerce_mpp_user_picture` (
		`mpp_user_picture_id` int(11)  unsigned NOT NULL AUTO_INCREMENT,
		`user_id` int(11) NOT NULL,
	    `pic_name` varchar(255) NOT NULL,
	    `pic_type` varchar(255) NOT NULL,
	    `active` tinyint(1) NOT NULL,
	    PRIMARY KEY (`mpp_user_picture_id`)
		)" );

		$this->db->query( "
		CREATE TABLE IF NOT EXISTS `{$this->db->prefix}woocommerce_mpp_order_picture` 
		(`order_id` int(11) NOT NULL,
		`pic_name` VARCHAR (255) NOT NULL)
		" );
	}
}