<?php

namespace WMPP\translations;

use WMPP\interfaces\RegisterAction;

defined( 'ABSPATH' ) or die( 'This is not what you are looking for' );

/**
 * Enable plugin's translations
 * @package WMPP\translations
 */
class EnableTranslations implements RegisterAction {

	/**
	 * Registers the needed actions to enable the translations.
	 * @return void
	 * @since 1.0.0
	 */
	public function register() {
		add_action( 'init', [ $this, 'load_translation' ] );
	}

	/**
	 * Loads the mo file for your translations
	 * @return void
	 * @since 1.0.1
	 */
	public function load_translation() {
		load_plugin_textdomain( 'wmpp', false, dirname( WMPP_BASENAME ) . '/i18n/languages' );
	}
}