<?php
namespace Asper\Datasource;

use Asper\LoggerFactory;
use Asper\GAEBucket;

abstract class Base {
	protected $httpAdapter = null;
	protected $logger;

	protected $url = "";
	protected $header = [];

	protected $group = '';
	protected $fieldMapping = [];

	public function __construct(){
		$this->logger = LoggerFactory::create()->getLogger($this->group);
	}

	abstract public function exec();

	abstract protected function transform($row=[]);

	protected function processFeeds($feeds){
		$data = [];

		foreach($feeds as $index => $row){
			$fields = $this->fieldTransform($row);
			$transformedData = $this->transform($row);

			if( isset($transformedData['Data']['Create_at']) &&
			    Filter::timeGreaterThan($transformedData['Data']['Create_at']) ){
				continue;
			}

			$data[] = array_merge_recursive($transformedData, ['Data' => $fields]);
		}

		return $data;
	}

	protected function convertTimeToTZ($string, $timezone=null){
		$dateTime = new \DateTime($string);

		if($timezone !== null){
			$timezone = new DateTimeZone($timezone);
			$dateTime->setTimezone($timezone);
		}

		return str_replace('+00:00', 'Z', gmdate('c', $dateTime->getTimestamp() ));
	}

	protected function fieldTransform($row=[]){
		$data = [];

		foreach($row as $fieldName => $fieldValue){
			if( !isset($this->fieldMapping[$fieldName]) ){ continue; }

			$newFieldName = $this->fieldMapping[$fieldName];
			$data[$newFieldName] = $fieldValue;
		}

		return $data;
	}

	protected function fetchRemote($url=null){
		$url = $url ?: $this->url;

		if( count($this->header) ){
			$opts = [
				'http'=> [
					'method' => "GET",
					'header' => implode("\r\n", $this->header)
				]
			];
			$context = stream_context_create($opts);
			return file_get_contents($url, false, $context);
		}

		return file_get_contents($url);
	}

	protected function save($data=[]){
		$path = $this->group . '.json';
		if( !count($data) ){ return false; }

		$data = json_encode($data);
		return GAEBucket::save($path, $data);
	}

	public function load(){
		$path = $this->group . '.json';

		$data = GAEBucket::load($path);
		if($data){ 
			$data = json_decode($data, true); 
		}else{
			return [];
		}

		$filterData = [];
		foreach($data as $site){
			if( isset($site['Data']['Create_at']) &&
				Filter::timeGreaterThan($site['Data']['Create_at']) ){
				continue;
			}
			$filterData[] = $site;
		}

		return $filterData;
	}
}