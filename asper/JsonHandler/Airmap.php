<?php
namespace Asper\JsonHandler;

use Asper\Datasource\LASS;
use Asper\Datasource\LASS4U;
use Asper\Datasource\LASSMAPS;
use Asper\Datasource\AsusAirbox;
use Asper\Datasource\EdimaxAirbox;
use Asper\Datasource\EPAPlat;
use Asper\Datasource\Independent;
use Asper\Datasource\ProbeCube;

class Airmap implements Handleable{

	use ResponseTrait;

	public function register(){
		return ['airmap', 'airmap-expire'];
	}

	public function trigger(Array $params=[]){
		$index = $params['requestFile'] == "airmap-expire" ? 'expire' : 'valid';
		$includeRAW = isset($params['raw']) ? (bool)$params['raw'] : false;

		$data = [];

		$data = array_merge($data, (new LASS())->load($includeRAW)[$index]);
		$data = array_merge($data, (new LASS4U())->load($includeRAW)[$index]);
		$data = array_merge($data, (new LASSMAPS())->load($includeRAW)[$index]);

		$data = array_merge($data, (new AsusAirbox())->load($includeRAW)[$index]);
		$data = array_merge($data, (new EdimaxAirbox())->load($includeRAW)[$index]);
		
		$data = array_merge($data, (new EPAPlat())->load($includeRAW)[$index]);

		$data = array_merge($data, (new Independent())->load($includeRAW)[$index]);
		$data = array_merge($data, (new ProbeCube())->load($includeRAW)[$index]);

		$callback = isset($params['callback']) ? $params['callback'] : null;
		$this->setExpire( 5*60 );
		$this->response($data, $callback);
	}

}