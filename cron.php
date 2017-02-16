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
		break;
	case 'g0v':
		(new Independent())->exec();
		(new ProbeCube())->exec();
		break;
}