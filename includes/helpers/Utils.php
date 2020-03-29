<?php

namespace WMPP\helpers;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * A util class with some helpers
 * @since 1.0.0
 * @package WMPP\helpers
 */
class Utils {

	/**
	 * It returns an unique md5 filename base on the current time. Passing the mime to know the extension of the filename
	 *
	 * @param $mime
	 *
	 * @return string
	 * @since 1.0.0
	 */
	static function generate_name( $mime ) {
		return md5( microtime() ) . '.' . self::get_extension( $mime );
	}

	/**
	 * Return the extension of a given mime
	 *
	 * @param $mime
	 *
	 * @return string
	 * @since 1.0.0
	 */
	static function get_extension( $mime ) {
		switch ( $mime ) {
			case 'image/png':
				return 'png';

			case 'image/bmp':
			case 'image/x-ms-bmp':
				return 'bmp';

			case 'image/jpeg':
			default:
				return 'jpg';
		}
	}

}