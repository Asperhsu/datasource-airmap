<?php
require("bootstrap.php");

use Asper\JsonHandler\ServiceProvider;
use Asper\JsonHandler\Fallback;

use Asper\JsonHandler\Airmap;
use Asper\JsonHandler\Datasource;

use Asper\JsonHandler\IndependentConfig;
use Asper\JsonHandler\ProbecubeConfig;

//for CORS
if( $_SERVER['REQUEST_METHOD'] == 'OPTIONS' ){
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Content-Type: application/json');
	exit;
}

syslog(LOG_INFO, print_r($_SERVER, true));


$jsonType = call_user_func(function(){
	$matches = [];
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	preg_match("/\/(.+?).json$/", $path, $matches);
	return isset($matches[1]) ? $matches[1] : null;
});

$params = array_merge($_GET, [
	'requestFile' => $jsonType,
]);

$handlers = [
	Airmap::class,
	Datasource::class,

	IndependentConfig::class,
	ProbecubeConfig::class,
];
$serviceProvider = new ServiceProvider();
$serviceProvider->register($handlers);
$serviceProvider->trigger($jsonType, $params);

//fallback
(new Fallback)->res($params);