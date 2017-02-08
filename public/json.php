<?php
require("bootstrap.php");

use Asper\JsonHandler\ServiceProvider;
use Asper\JsonHandler\Fallback;

use Asper\JsonHandler\Airmap;
use Asper\JsonHandler\Datasource;

use Asper\JsonHandler\IndependentConfig;
use Asper\JsonHandler\ProbecubeConfig;

$jsonType = call_user_func(function(){
	$matches = [];
	preg_match("/\/(.+?).json$/", $_SERVER['REQUEST_URI'], $matches);
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