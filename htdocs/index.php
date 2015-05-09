<?php 
ini_set('display_errors', 'off');
/**
 * @link http://www.php.net/manual/en/language.oop5.autoload.php
 */
function __autoload ($className)
{
	$fileName = str_replace('_','/', $className) . '.php';
	include_once $fileName;
}

// bootstrapping
define('WEB_ROOT', dirname(__FILE__));
define('APP_ROOT', dirname(WEB_ROOT));
define('LIB_DIR', APP_ROOT . '/lib');
define('RESOURCES_DIR', APP_ROOT . '/resources');

set_include_path(get_include_path() . PATH_SEPARATOR . implode(PATH_SEPARATOR, array(WEB_ROOT, LIB_DIR)));

require_once 'inc/config.php';
require_once 'inc/images.php';

$requestFactory = new Placebeet_ImageRequestFactory($config, $images);
$request = $requestFactory->create($_GET);

if (false === $request) {
	header("{$_SERVER['SERVER_PROTOCOL']} 400 Bad request");
	exit; 
}

$server = new Placebeet_ImageServer($config);
$image = $server->getImage($request);

if (false === $image) {
	header("{$_SERVER['SERVER_PROTOCOL']} 500 Internal Error");
	exit;
}

header('Content-type: image/jpeg');
imagejpeg($image, null, 100);
imagedestroy($image);
