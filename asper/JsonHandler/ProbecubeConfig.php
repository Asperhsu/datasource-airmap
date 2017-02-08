<?php
namespace Asper\JsonHandler;

use Asper\Datasource\ProbeCube;

class ProbeCubeConfig implements Handleable{

	use ResponseTrait;

	public function register(){
		return ['config.probecube'];
	}

	public function trigger(Array $params=[]){
		$data = (new ProbeCube())->loadConfig();

		$callback = isset($params['callback']) ? $params['callback'] : null;
		$this->setExpire( 24*60 );
		$this->response($data, $callback);
	}

}