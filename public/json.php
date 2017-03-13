<?php
require("bootstrap.php");

$jsonType = call_user_func(function(){
	$matches = [];
	$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
	preg_match("/\/(.+?).json$/", $path, $matches);
	return isset($matches[1]) ? $matches[1] : null;
});

if( $jsonType == 'edimax-airbox' && !isAuthorized() ){ show_550(); }

use Asper\JsonHandler\ServiceProvider;
use Asper\JsonHandler\Fallback;

use Asper\JsonHandler\Airmap;
use Asper\JsonHandler\Datasource;

use Asper\JsonHandler\IndependentConfig;
use Asper\JsonHandler\ProbecubeConfig;

use Asper\JsonHandler\DatasourceLog;


//for CORS
if( $_SERVER['REQUEST_METHOD'] == 'OPTIONS' ){
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header('Content-Type: application/json');
	exit;
}



$params = array_merge($_GET, [
	'requestFile' => $jsonType,
]);

$handlers = [
	Airmap::class,
	Datasource::class,

	IndependentConfig::class,
	ProbecubeConfig::class,

	DatasourceLog::class,
];
$serviceProvider = new ServiceProvider();
$serviceProvider->register($handlers);
$result = $serviceProvider->trigger($jsonType, $params);

if(!$result){
	//fallback
	(new Fallback)->res($params);
}