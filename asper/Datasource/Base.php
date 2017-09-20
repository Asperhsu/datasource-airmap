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

	protected $LassRanking;
	protected $LassDeviceStatus;

	public function __construct(){
		$logName = 'Datasource::'.$this->group;
		$this->logger = LoggerFactory::create()->getLogger($logName);
		$this->querylogger = LoggerFactory::create('datastore', $logName);
	}

	/**
	 * method to fetch feed url
	 * @return Array [description]
	 */
	abstract public function exec();

	/**
	 * tranform row feed to feed format
	 * @param  array  $row row feed
	 * @return rray      feed
	 */
	abstract protected function transform($row=[]);

	/**
	 * return create at time is valid
	 * @param  array  $feed  feed
	 * @return boolean       valid status
	 */
	protected function isCreatAtValid($feed){
		return !(isset($feed['Data']['Create_at']) &&
			   Filter::timeGreaterThan($feed['Data']['Create_at']));
	}

	/**
	 * enable/disable log
	 * @param  boolean $flag true for enable
	 */
	protected function enableLog($flag){
		$this->enableLogger = (bool) $flag;
	}

	protected function getCallee(){
		$trace = debug_backtrace(); 
		$callee = isset($trace[2]) ? $trace[2]["class"].'::'.$trace[2]["function"] : null;
		return $callee;
	}

	/**
	 * process row feeds from remote, transform to feeds
	 * @param  array         $rawFeeds row rawFeeds
	 * @param  callable|null $filter   filter callback, false to pass feed
	 * @return array                   feeds
	 */
	protected function processFeeds(Array $rawFeeds, callable $filter=null){
		$feeds = [];
		$recordCountLog = [
			'filter'	=> 0,
			'valid' 	=> 0,
			'expire' 	=> 0,
			'total'		=> 0,
		];

		foreach($rawFeeds as $index => $rawFeed){
			$feed = $this->transform($rawFeed);
			if( !$feed ){ continue; }
			
			$fields = $this->fieldTransform($rawFeed);
			$uniqueKeyInfo = $this->appendUniqueKeyInfo($rawFeed);
			$feed = array_merge_recursive($feed, ['Data' => $fields, 'RawData' => $rawFeed], $uniqueKeyInfo);

			//filter, true for keep
			if( !is_null($filter) && !$filter($feed) ){
				$recordCountLog['filter']++;
				$recordCountLog['total']++;
				continue;
			}

			$feeds[] = $feed;

			//log count
			$this->isCreatAtValid($feed) 
				? ($recordCountLog['valid']++) 
				: ($recordCountLog['expire']++);
			$recordCountLog['total']++;
		}

		if($this->enableLogger){
			$msg = 'processFeeds';
			$callee = $this->getCallee() ?: $msg;

			$this->logger->info($msg, $recordCountLog);
			// $this->querylogger->getLogger($msg)->info($callee, $recordCountLog);
		}

		return $feeds;
	}

	/**
	 * append info to feed by uniquekey
	 * @param  Array  $rawFeed row feed
	 * @return array           addition infomation
	 */
	protected function appendUniqueKeyInfo(Array $rawFeed){
		if( $this->uniqueKey === null ){ return []; }

		$feed = [];
		$indexes = explode('.', $this->uniqueKey);
		
		//find uniquekey in rawData, fill into site
		$uniquekey = $rawFeed;
		foreach($indexes as $index){
			if( isset($uniquekey[$index]) ){
				$uniquekey = $uniquekey[$index];
			}
		}

		$feed['uniqueKey'] = $uniquekey;
		$feed['reliableRanking'] = LASSAnalysisFactory::getRankingInstance()->getRank($uniquekey);
		$feed['supposeStatus'] = LASSAnalysisFactory::getDeviceStatusInstance()->getStatus($uniquekey);

		return $feed;
	}

	/**
	 * transform measure data from raw feed defined in fieldMapping
	 * @param  array  $rawFeed       raw feed
	 * @param  array  $fieldMapping  fields reject into feed.data
	 * @return array                 feed.data
	 */
	protected function fieldTransform($rawFeed=[], Array $fieldMapping=null){
		$feedData = [];
		$fieldMapping = $fieldMapping ?: $this->fieldMapping;

		foreach($rawFeed as $fieldName => $fieldValue){
			if( !isset($fieldMapping[$fieldName]) ){ continue; }

			$newFieldName = $fieldMapping[$fieldName];
			$feedData[$newFieldName] = $fieldValue;
		}

		return $feedData;
	}

	/**
	 * fetch remote using file_get_contents(GAE)
	 * @param  [type] $url    [description]
	 * @param  string $method [description]
	 * @return [type]         [description]
	 */
	protected function fetchRemote($url=null, $method="GET"){
		$url = $url ?: $this->feedUrl;

		if( count($this->header) ){
			$opts = [
				'http'=> [
					'method' => $method,
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
			$this->enableLogger && $this->logger->warn("fetchRemote error.", compact('statusCode', 'url', 'http_response_header'));
			return null;
		}
	}

	/**
	 * save feeds to file
	 * @param  array  $feeds 
	 * @return boolean       
	 */
	protected function save($feeds=[]){
		$path = $this->group . '.json';
		if( !count($feeds) ){ return false; }

		$json = json_encode($feeds);
		return GAEBucket::save($path, $json);
	}

	/**
	 * load feeds
	 * @param  boolean       $includeRaw include raw data
	 * @param  callable|null $filter     return false to set feed is expire
	 * @return array                     [$valid, $expire]
	 */
	public function load($includeRaw=false, callable $filter=null){
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

			$isValid = is_null($filter) 
						? $this->isCreatAtValid($site) 
						: call_user_func($filter, $site);
			$isValid ? ($valid[] = $site) : ($expire[] = $site);
		}

		if($this->enableLogger){
			$this->logger->info("load sites count", [
				'valid' => count($valid), 
				'expire' => count($expire)
			]);
		}

		return compact('valid', 'expire');
	}

	/**
	 * query lastest feed by uniqueKey, default will find in exist feeds
	 * @param  string  $uniqueKey   
	 * @param  boolean $includeRaw 
	 * @return array               feed
	 */
	public function queryLastest($uniqueKey, $includeRaw=false){
		$data = $this->load($includeRaw);
		$feeds = array_merge($data['valid'], $data['expire']);

		foreach($feeds as $site){
			if($site['uniqueKey'] != $uniqueKey){ continue; }
			return $site;
		}

		return [];
	}

	/**
	 * query history feeds by uniqueKey (optional)
	 * @param  string $uniqueKey      
	 * @param  int    $startTimestamp
	 * @param  int    $endTimestamp  
	 * @return array                
	 */
	public function queryHistory($uniqueKey, $startTimestamp, $endTimestamp){
		return [];
	}

	/**
	 * convert feeds to history format
	 * @param  array $feeds  
	 * @return array        history
	 */
	protected function convertFeedsToHistory(Array $feeds){
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

	/**
	 * get uniqueKey from feeds
	 * @param  Array  $feeds 
	 * @return array        uniqueKeys
	 */
	protected function getUniqueKeys(Array $feeds){
		return array_map(function($feed){
			return $feed['uniqueKey'];
		}, $feeds);
	}

	/**
	 * find diff uiqueKeys with previous feeds
	 * @param  Array  $prevFeeds    previous feeds
	 * @param  Array  $currentFeeds fetched feeds
	 * @return Array                diff uniqueKeys
	 */
	protected function findFeedsDiffUniqueKeys(Array $prevFeeds, Array $currentFeeds){
		$diff = [ 'add' => [], 'remove' => [] ];
		$prevUniqueKeys = $this->getUniqueKeys($prevFeeds);
		$currentUniqueKeys = $this->getUniqueKeys($currentFeeds);
		
		foreach($currentUniqueKeys as $index => $key){
			$prevIndex = array_search($key, $prevUniqueKeys);
			if($prevIndex === false){
				$diff['add'][] = $key;
			}else{
				array_splice($prevUniqueKeys, $prevIndex, 1);
			}
		}
		$diff['remove'] = $prevUniqueKeys;
		
		return $diff;
	}

	/**
	 * find diff uiqueKeys with previous feeds, and save to log
	 * @param  Array  $newfeeds fetched feeds
	 * @return array            diff uniqueKeys
	 */
	protected function logDiffUniqueKeys(Array $newfeeds){
		$prevFeeds = $this->load();
		$prevFeeds = array_merge($prevFeeds['valid'], $prevFeeds['expire']);
		
		$diff = $this->findFeedsDiffUniqueKeys($prevFeeds, $newfeeds);
		
		if($this->enableLogger){
			$msg = "logDiffUniqueKeys";
			$this->logger->info($msg, $diff);
			// $this->querylogger->getLogger($msg)->info($msg, $diff);
		}

		return $diff;
	}

}