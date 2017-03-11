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

$job = strtolower($_GET['job']);
switch($job){
	case 'lass':
		(new LASS())->exec();
		(new LASS4U())->exec();
		(new LASSMAPS())->exec();
		break;
	case 'asus-airbox':
		(new AsusAirbox())->exec();
		break;
	case 'edimax-airbox':
		(new EdimaxAirbox())->exec();
		break;
	case 'epa':
		(new EPAPlat())->exec();
		break;
	case 'airmap':
		(new LASS())->exec();
		(new LASS4U())->exec();
		(new LASSMAPS())->exec();
		(new AsusAirbox())->exec();
		(new EdimaxAirbox())->exec();
		(new EPAPlat())->exec();

		// save to myjson.com
		ob_start();
		(new AirmapJson())->trigger(['requestFile' => 'airmap']);
		$json = ob_get_contents();
		ob_end_clean();

		$myjsonID = env('MYJSON_AIRMAP_ID');
		fetchMyJson("PUT", $myjsonID, $json);
		break;
	case 'g0v':
		(new Independent())->exec();
		(new ProbeCube())->exec();
		break;
	case 'data-analysis':
		(new LassRanking())->exec();
		(new LassDeviceStatus())->exec();
		break;
}