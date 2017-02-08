<?php
namespace Asper\JsonHandler;

use Asper\Datasource\LASS;
use Asper\Datasource\LASS4U;
use Asper\Datasource\AsusAirbox;
use Asper\Datasource\EdimaxAirbox;
use Asper\Datasource\EPAPlat;
use Asper\Datasource\Independent;
use Asper\Datasource\ProbeCube;

class Datasource implements Handleable{

	use ResponseTrait;

	public function register(){
		return [
			'lass', 
			'lass4u', 
			'edimax-airbox', 
			'asus-airbox', 
			'epa', 
			'independent',
			'probecube',
		];
	}

	public function trigger(Array $params=[]){
		$data =  [];
		switch($params['requestFile']){
			case 'lass': 			$data = (new LASS())->load(); break;
			case 'lass4u': 			$data = (new LASS4U())->load(); break;
			case 'asus-airbox': 	$data = (new AsusAirbox())->load(); break;
			case 'edimax-airbox': 	$data = (new EdimaxAirbox())->load(); break;
			case 'epa': 			$data = (new EPAPlat())->load(); break;
			case 'independent': 	$data = (new Independent())->load(); break;
			case 'probecube': 		$data = (new ProbeCube())->load(); break;
		}

		$callback = isset($params['callback']) ? $params['callback'] : null;
		$this->setExpire( 5*60 );
		$this->response($data, $callback);
	}

}