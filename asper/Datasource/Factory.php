<?php
namespace Asper\Datasource;

use Asper\Datasource\LASS;
use Asper\Datasource\LASS4U;
use Asper\Datasource\LASSMAPS;
use Asper\Datasource\AsusAirbox;
use Asper\Datasource\EdimaxAirbox;
use Asper\Datasource\EPAPlat;
use Asper\Datasource\Independent;
use Asper\Datasource\ProbeCube;

class Factory {

	private function __construct(){
	}

	static public function make($group){
		$group = strtolower($group);
		switch($group){
			case 'lass':  			return new LASS();
			case 'lass-4u':  		return new LASS4U();
			case 'lass-maps':  		return new LASSMAPS();
			case 'asus-airbox':  	return new AsusAirbox();
			case 'edimax-airbox':  	return new EdimaxAirbox();
			case 'epa':  			return new EPAPlat();
			case 'independent':  	return new Independent();
			case 'probecube':  		return new ProbeCube();
		}

		return null;
	}
}