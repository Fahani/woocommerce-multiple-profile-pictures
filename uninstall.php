<?php

use WMPP\database\Repository;
use WMPP\helpers\Utils;

/**
 * Trigger this file when uninstalling the plugin
 *
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die('This is not what you are looking for');
}

$repository = new Repository();
$repository->delete_tables();

Utils::deleteAll(wp_upload_dir()['basedir'] . '/wmpp');