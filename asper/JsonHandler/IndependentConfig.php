<?php
namespace Asper\JsonHandler;

use Asper\Datasource\Independent;

class IndependentConfig implements Handleable{

	use ResponseTrait;

	public function register(){
		return ['config.independent'];
	}

	public function trigger(Array $params=[]){
		$data = (new Independent())->loadConfig();

		$callback = isset($params['callback']) ? $params['callback'] : null;
		$this->setExpire( 24*60 );
		$this->response($data, $callback);
	}

}