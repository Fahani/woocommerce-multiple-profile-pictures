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