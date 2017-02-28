<?php
namespace Asper\JsonHandler;

trait ResponseTrait{

	public function search(Array $data, $searchKey, $searchValue){
		$ret = [];
		foreach($data as $item){
			if( $item[$searchKey] == $searchValue ){
				$ret[] = $item;
			}
		}
		return $ret;
	}

	public function setExpire($secs = 1800){		
		header("Cache-Control: max-age={$secs}, must-revalidate"); 		
		header("Expires: " . gmdate("D, d M Y H:i:s", time() + $secs) . " GMT");
	}

	public function responseError($msg, $callback=null){
		$data = [
			'error' => $msg,
		];

		$this->response($data, $callback);		
	}

	public function response($data, $callback=null){
		if( $callback ){			
			$this->jsonpResponse($callback, $data);
		}else{
			$this->jsonResponse($data);
		}		
	}

	public function jsonpResponse($callback, $data){		
		$json = json_encode($data);		
		$response = sprintf("%s(%s)", $callback, $json);
		$this->jsonResponse($response);
	}

	public function jsonResponse($response){
		if( is_array($response) ){
			$response = json_encode($response);
		}
		
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
		header('Content-Type: application/json');
		echo $response;
	}
}