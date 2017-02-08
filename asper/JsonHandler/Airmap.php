<?php
namespace Asper\JsonHandler;

use Asper\Datasource\LASS;
use Asper\Datasource\LASS4U;
use Asper\Datasource\AsusAirbox;
use Asper\Datasource\EdimaxAirbox;
use Asper\Datasource\EPAPlat;
use Asper\Datasource\Independent;
use Asper\Datasource\ProbeCube;

class Airmap implements Handleable{

	use ResponseTrait;

	public function register(){
		return ['airmap'];
	}

	public function trigger(Array $params=[]){
		$data = [];

		$data = array_merge($data, (new LASS())->load());
		$data = array_merge($data, (new LASS4U())->load());

		$data = array_merge($data, (new AsusAirbox())->load());
		$data = array_merge($data, (new EdimaxAirbox())->load());
		
		$data = array_merge($data, (new EPAPlat())->load());

		$data = array_merge($data, (new Independent())->load());
		$data = array_merge($data, (new ProbeCube())->load());

		$callback = isset($params['callback']) ? $params['callback'] : null;
		$this->setExpire( 5*60 );
		$this->response($data, $callback);
	}

}