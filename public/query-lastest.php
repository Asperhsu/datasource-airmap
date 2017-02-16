<?php
require("bootstrap.php");

use Asper\Datasource\Factory;
use Asper\JsonHandler\ResponseTrait;

$Datasource = Factory::make($_GET['group']);
$id 		= $_GET['id'];
$includeRAW = isset($_GET['raw']) ? (bool)$_GET['raw'] : false;

if( is_null($Datasource) || !strlen($id) ){ 
	http_response_code(402); //402 Payment Required
	echo "402 Payment Required";
	exit;
}

$data = $Datasource->queryLastest($id, $includeRAW);

class Res {
	use ResponseTrait;
}

$res = new Res();
$res->setExpire( 24*60*60 );
$res->response($data, isset($_GET['callback']) ? $_GET['callback'] : null );