<?php

/**
 * UFO CMF
 *
 * PHP application framework
 *
 * @package		UFO CMF
 * @copyright		(c) 2013, Rinamika, http://rinamika.ru
 * @author			Viktor Suprun
 * @since			1.0
 * @license		http://rinamika.ru/ufo/licence.txt Rinamika Application License
 * @filesource
 */

/**
 * PHP Road application bootstrap script
 */

error_reporting(E_ALL & ~E_NOTICE);
ini_set('display_errors', true);

/**
 * This variable contains a path to this file.
 */
$bootstrapPath = __FILE__;

/**
 * Specify the application directory root
 *
 * Leave this variable blank if application root directory matches the site root directory.
 * Otherwise specify an absolute path to the application root, for example:
 * $applicationRoot = realpath( dirname($bootstrapPath)."/../app" );
 *
 */
$applicationRoot = "";

/*
 * Include the PHP Road library
 *
 * You may need to specify a full path to the phproad.php script,
 * in case if the PHP Road root directory is not specified in the PHP includes path.
 *
 */
include(dirname(__FILE__) . "/phproad/system/phproad.php");

if (Phpr::$request->isDebugRequest()) {
	echo sprintf("<!-- %.06fsec, %.02fMb -->", microtime(true) - $start, memory_get_peak_usage() / 1024 / 1024);
}