<?php
namespace Asper\JsonHandler;

class Fallback {

	use ResponseTrait;

	public function res(Array $params=[]){	
		$msg = "no specific json data";
		$this->responseError($msg, $params['callback']);
	}

}