<?php
namespace Asper\Datasource;

use Asper\LoggerFactory;
use Asper\GAEBucket;
use Asper\DateHelper;

abstract class Base {
	protected $logger;
	protected $querylogger;
	protected $enableLogger = true;

	protected $feedUrl = null;
	protected $header = [];

	protected $group = null;
	protected $uniqueKey = null;
	protected $fieldMapping = [];

	public function __construct(){
		$logName = 'Datasource::'.$this->group;
		$this->logger = LoggerFactory::create()->getLogger($logName);
		$this->querylogger = LoggerFactory::create('datastore', $logName);
	}

	abstract public function exec();

	abstract protected function transform($row=[]);

	protected function isValid($site){
		return !(isset($site['Data']['Create_at']) &&
			   Filter::timeGreaterThan($site['Data']['Create_at']));
	}

	protected function processFeeds($feeds, callable $filter=null){
		$data = [];
		$recordCountLog = [
			'filter'	=> 0,
			'valid' 	=> 0,
			'expire' 	=> 0,
			'total'		=> 0,
		];		
		$LassRanking = new LassRanking();
		$LassDeviceStatus = new LassDeviceStatus();

		foreach($feeds as $index => $row){
			$fields = $this->fieldTransform($row);
			$transformedData = $this->transform($row);
			if( !$transformedData ){ continue; }
			
			//find uniquekey in rawData, fill into site
			if($this->uniqueKey){
				$indexes = explode('.', $this->uniqueKey);
				
				$value = $row;
				foreach($indexes as $index){
					if( isset($value[$index]) ){
						$value = $value[$index];
					}
				}

				$transformedData['uniqueKey'] = $value;
				$transformedData['reliableRanking'] = $LassRanking->getRank($value);
				$transformedData['supposeStatus'] = $LassDeviceStatus->getStatus($value);
			}

			$transformedData['RawData'] = $row;
			$site = array_merge_recursive($transformedData, ['Data' => $fields]);

			//filter, true for keep
			if( !is_null($filter) && !$filter($site) ){
				$recordCountLog['filter']++;
				continue;
			}

			$data[] = $site;

			//log
			$this->isValid($site) 
				? ($recordCountLog['valid']++) 
				: ($recordCountLog['expire']++);
			$recordCountLog['total']++;
		}

		$msg = is_null($filter) ? 'processFeeds' : 'processFeeds with filter';
		if($this->enableLogger){
			$this->logger->info($msg, $recordCountLog);
			$this->querylogger->getLogger($msg)->info($msg, $recordCountLog);
		}

		return $data;
	}

	protected function fieldTransform($row=[], $fieldMapping=null){
		$data = [];
		if( is_null($fieldMapping) ){
			$fieldMapping = $this->fieldMapping;
		}

		foreach($row as $fieldName => $fieldValue){
			if( !isset($fieldMapping[$fieldName]) ){ continue; }

			$newFieldName = $fieldMapping[$fieldName];
			$data[$newFieldName] = $fieldValue;
		}

		return $data;
	}

	protected function fetchRemote($url=null){
		$url = $url ?: $this->feedUrl;

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
		$path = $this->group . '.json';
		if( !count($data) ){ return false; }

		$data = json_encode($data);
		return GAEBucket::save($path, $data);
	}

	public function load($includeRaw=false, callable $isValidCB=null){
		$path = $this->group . '.json';
		$valid = [];
		$expire = [];

		$data = GAEBucket::load($path);
		if($data){ 
			$data = json_decode($data, true); 
		}else{
			return compact('valid', 'expire');
		}

		foreach($data as $site){
			if( !$includeRaw && isset($site['RawData']) ){ unset($site['RawData']); }

			$isValid = is_null($isValidCB) 
						? $this->isValid($site) 
						: call_user_func($isValidCB, $site);
			$isValid ? ($valid[] = $site) : ($expire[] = $site);
		}

		$this->logger->info("load sites count", ['valid' => count($valid), 'expire' => count($expire)] );

		return compact('valid', 'expire');
	}

	public function queryLastest($id, $includeRaw=false){
		$data = $this->load($includeRaw);
		$feeds = array_merge($data['valid'], $data['expire']);

		foreach($feeds as $site){
			if($site['uniqueKey'] != $id){ continue; }
			return $site;
		}

		return [];
	}

	public function queryHistory($id, $startTimestamp, $endTimestamp){
		return [];	//optional implement
	}

	protected function convertFeedsToHistory($feeds){

		$history = [];
		foreach($feeds as $index => $feed){
			foreach($feed['Data'] as $type => $value){
				if( $type == 'Create_at' ){
					$history['isotimes'][$index] = DateHelper::convertTimeToTZ($value);
					continue;
				}

				$history[$type][$index] = intval($value);
			}
		}

		return $history;
	}
}