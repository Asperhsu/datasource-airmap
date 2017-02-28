<?php
require("bootstrap.php");
if( !isAuthorized() ){ show_550(); }

use Asper\Datasource\Factory;
use Asper\JsonHandler\ResponseTrait;

$Datasource = Factory::make($_GET['group']);
$id 		= $_GET['id'];
$start 		= $_GET['start'];
$end 		= $_GET['end'];

if( is_null($Datasource) || !strlen($id) || !strlen($start) || !strlen($end) ){ 
	http_response_code(402); //402 Payment Required
	echo "402 Payment Required";
	exit;
}

$data = $Datasource->queryHistory($id, $start, $end);

class Res {
	use ResponseTrait;
}

$res = new Res();
$res->setExpire( 24*60*60 );
$res->response($data, isset($_GET['callback']) ? $_GET['callback'] : null );