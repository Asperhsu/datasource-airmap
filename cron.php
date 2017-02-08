<?php
require("bootstrap.php");

use Asper\Datasource\LASS;
use Asper\Datasource\LASS4U;
use Asper\Datasource\AsusAirbox;
use Asper\Datasource\EdimaxAirbox;
use Asper\Datasource\EPAPlat;

use Asper\Datasource\Independent;
use Asper\Datasource\ProbeCube;

$job = strtolower($_GET['job']);
switch($job){
	case 'airmap':
		(new LASS())->exec();
		(new LASS4U())->exec();
		(new AsusAirbox())->exec();
		(new EdimaxAirbox())->exec();
		(new EPAPlat())->exec();
		break;
	case 'g0v':
		(new Independent())->exec();
		(new ProbeCube())->exec();
		break;
}