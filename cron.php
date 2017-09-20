<?php
require("bootstrap.php");

use Asper\Datasource\LASS;
use Asper\Datasource\LASS4U;
use Asper\Datasource\LASSMAPS;
use Asper\Datasource\AsusAirbox;
use Asper\Datasource\EdimaxAirbox;
use Asper\Datasource\EPAPlat;

use Asper\Datasource\Independent;
use Asper\Datasource\ProbeCube;

use Asper\Datasource\LassRanking;
use Asper\Datasource\LassDeviceStatus;

use Asper\JsonHandler\Airmap as AirmapJson;
use Asper\JsonHandler\Datasource as DSJson;

$job = strtolower($_GET['job']);
switch($job){
	case 'lass':
		(new LASS())->exec();
		(new LASS4U())->exec();
		(new LASSMAPS())->exec();

		$ary1 = json_decode(loadJson('lass'), true);
		$ary2 = json_decode(loadJson('lass-4u'), true);
		$ary3 = json_decode(loadJson('lass-maps'), true);
		$json = json_encode(array_merge($ary1, $ary2, $ary3));

		$myjsonID = env('MYJSON_LASS_ID');
		fetchMyJson("PUT", $myjsonID, $json);
		break;
	case 'asus-airbox':
		(new AsusAirbox())->exec();

		$json = loadJson('asus-airbox');
		$myjsonID = env('MYJSON_ASUS_ID');
		fetchMyJson("PUT", $myjsonID, $json);
		break;
	case 'edimax-airbox':
		(new EdimaxAirbox())->exec();

		$json = loadJson('edimax-airbox');
		$myjsonID = env('MYJSON_EDIMAX_ID');
		fetchMyJson("PUT", $myjsonID, $json);
		break;
	case 'epa':
		(new EPAPlat())->exec();
		break;
	case 'g0v':
		(new Independent())->exec();
		(new ProbeCube())->exec();
		
		$ary1 = json_decode(loadJson('independent'), true);
		$ary2 = json_decode(loadJson('probecube'), true);
		$json = json_encode(array_merge($ary1, $ary2));

		$myjsonID = env('MYJSON_G0V_ID');
		fetchMyJson("PUT", $myjsonID, $json);
		break;
	case 'data-analysis':
		(new LassRanking())->exec();
		(new LassDeviceStatus())->exec();
		break;
}

function loadJson($type) {
	ob_start();
	(new DSJson())->trigger(['requestFile' => $type]);
	$json = ob_get_contents();
	ob_end_clean();

	return $json;
}