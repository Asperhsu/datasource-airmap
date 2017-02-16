<?php
namespace Asper\JsonHandler;

use Asper\Datasource\Factory;

class Datasource implements Handleable{

	use ResponseTrait;

	public function register(){
		return [
			'lass', 			'lass-expire', 
			'lass-4u', 			'lass-4u-expire',
			'lass-maps', 		'lass-maps-expire',
			'edimax-airbox', 	'edimax-airbox-expire', 
			'asus-airbox', 		'asus-airbox-expire', 
			'epa', 				'epa-expire',
			'independent',		'independent-expire',
			'probecube',		'probecube-expire',
		];
	}

	public function trigger(Array $params=[]){
		$dsName = str_replace("-expire", '', $params['requestFile']);
		$index = strpos($params['requestFile'], "-expire") === false ? 'valid' : 'expire';
		$includeRAW = isset($params['raw']) ? (bool)$params['raw'] : false;
		
		$Datasource = Factory::make($dsName);
		
		$data = [];
		if( !is_null($Datasource) ){
			$data = $Datasource->load($includeRAW)[$index];
		}

		$callback = isset($params['callback']) ? $params['callback'] : null;
		$this->setExpire( 5*60 );
		$this->response($data, $callback);
	}

}