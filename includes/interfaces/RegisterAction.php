<?php

namespace WMPP\interfaces;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * It makes sure that injected classes have the register method
 * @package WMPP\interfaces
 * @since 1.0.0
 */
interface RegisterAction {

	/**
	 * Add the actions and filters needed for the purpose of the class
	 * @return void
	 * @since 1.0.0
	 */
	public function register();
}