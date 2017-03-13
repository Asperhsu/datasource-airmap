<?php
namespace Asper\Datasource;

use Asper\LoggerFactory;
use Asper\GAEBucket;

class LassAnalysis {
	protected $data = null;

	public function __construct(){
		$this->logger = LoggerFactory::create()->getLogger('Datasource::'.get_class($this));
	}

	protected function fetchRemote($url){
		$contents = file_get_contents($url);
		$statusCode = substr($http_response_header[0], 9, 3);

		if($statusCode == 200){
			return $contents;
		}else{
			$this->logger->warn("fetchRemote error.", compact('statusCode', 'url', 'http_response_header'));
			return null;
		}
	}

	protected function save($data=[]){
		$path = get_class($this) . ".json";
		if( !count($data) ){ return false; }

		$this->data = $data;
		$data = json_encode($data);
		return GAEBucket::save($path, $data);
	}

	protected function load(){
		if( $this->data !== null ){
			return $this->data;
		}

		$path = get_class($this) . ".json";
		$data = GAEBucket::load($path);
		$this->data = $data === false ? [] : json_decode($data, true);
		
		return $this->data;
	}

}