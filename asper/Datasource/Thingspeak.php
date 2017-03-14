<?php
namespace Asper\Datasource;

use Asper\GAEBucket;
use Asper\DateHelper;

class Thingspeak extends Base {
	protected $defaultConfigPath = null;
	protected $configPath = null;
	protected $config = null;

	protected $uniqueKey = 'Channel_id';
	protected $group = null;

	protected $feedUrlTemplate = "https://api.thingspeak.com/channels/{{identity}}/feeds.json";
	protected $channel = [];

	public function exec(){
		$feeds = [];
		$lastUpdate = [];
		$config = $this->loadConfig();
		$recordCountLog = [
			'filter'	=> 0,
			'valid' 	=> 0,
			'expire' 	=> 0,
			'total'		=> 0,
		];

		foreach($config as $site){
			if( isset($site['active']) && $site['active'] === false ){
				continue;
			}

			$feed = $this->queryLastest($site[$this->uniqueKey], true);

			//log
			$this->isCreatAtValid($feed) 
				? ($recordCountLog['valid']++) 
				: ($recordCountLog['expire']++);
			$recordCountLog['total']++;

			$feeds[] = $feed;

			//record last update for config
			$lastUpdate[$feed['uniqueKey']] = $feed['Data']['Create_at'];
		}

		$msg = 'processFeeds';
		$callee = $this->getCallee() ?: $msg;
		$this->logger->info($msg, $recordCountLog);
		$this->querylogger->getLogger($msg)->info($callee, $recordCountLog);

		$this->updateConfigUpdateTimestamp($lastUpdate);
		$this->logDiffUniqueKeys($feeds);
		$this->save($feeds);

		return $feeds;
	}

	protected function transform($row=[]){	//children implement
		return $row;
	}

	public function queryLastest($id, $includeRAW=false){
		$site = $this->getSiteConfig($id);
		if( is_null($site) ){ return []; }

		//fetch
		$url = str_replace('{{identity}}', $id, $this->feedUrlTemplate.'?results=1');
		$response = $this->fetchRemote($url);
		if($response === null){ continue; }

		$data = json_decode($response, true);

		//prepare fields mapping and transform data
		$this->channel = array_merge($site, $data['channel']);
		if( isset($site['Option']) ){
			$this->fieldMapping = $site['Option'];
		}
					
		//for uniquekey
		foreach($data['feeds'] as $index => $feed){
			$data['feeds'][$index][$this->uniqueKey] = (string)$site[$this->uniqueKey];
		}

		$this->enableLog(false); //temp disable
		$feeds = $this->processFeeds($data['feeds']);
		$this->enableLog(true);

		$feed = array_shift($feeds);

		if(!$includeRAW){
			unset($feed['RawData']);
		}
		
		return $feed;
	}

	public function queryHistory($id, $startTimestamp, $endTimestamp){
		$site 		= $this->getSiteConfig($id);
		if( is_null($site) ){ return []; }

		$startTZ 	= DateHelper::convertTimeToTZ($startTimestamp);
		$endTZ 		= DateHelper::convertTimeToTZ($endTimestamp);
		$startDate  = gmdate('Y-m-d%20H:i:s', strtotime($startTZ));
		$endDate  	= gmdate('Y-m-d%20H:i:s', strtotime($endTZ));
		$url 		= str_replace('{{identity}}', $id, $this->feedUrlTemplate);
		$url 		= sprintf($url.'?start=%s&end=%s', $startDate, $endDate);

		$response = $this->fetchRemote($url);
		if($response === null){ return []; }

		$data = json_decode($response, true);
		
		//prepare fields mapping and transform data
		$this->channel = array_merge($site, $data['channel']);
		if( isset($site['Option']) ){
			$this->fieldMapping = $site['Option'];
		}

		//transform to site feed format
		$this->enableLog(false);
		$feeds = $this->processFeeds($data['feeds']);
		$this->enableLog(true);
		
		return $this->convertFeedsToHistory($feeds);
	}


	public function loadConfig(){
		if( !is_null($this->config) ){ return $this->config; }

		$config = GAEBucket::load($this->configPath);

		if( !$config ){	//config not exist, use and copy default config
			if(file_exists(env("APP_PATH") . $this->defaultConfigPath)){
				$config = file_get_contents(env("APP_PATH") . $this->defaultConfigPath);
				GAEBucket::save($this->configPath, $config);
			}else{
				$msg = get_class($this) . " default config not exist.";
				$this->logger->error($msg);
				throw new \Exception($msg);
			}
		}

		$this->config = json_decode($config, true);
		return $this->config;
	}

	protected function updateConfigUpdateTimestamp($lastUpdate){
		$config = $this->loadConfig();

		foreach($config as $index => $site){
			$id = $site[$this->uniqueKey];
			if( !isset($lastUpdate[$id]) ){ continue; }

			$update_at = DateHelper::convertTimeToTZ($lastUpdate[$id]);
			$config[$index]['update_at'] = $update_at;
		}

		GAEBucket::save($this->configPath, json_encode($config));
		return true;
	}

	protected function getSiteConfig($id){
		$config = $this->loadConfig();

		foreach($config as $index => $site){
			if( $site[$this->uniqueKey] == $id ){ return $site; }
		}

		return null;
	}

	public function updateSiteConfig($channelID=null, $newConfig=[]){
		if( is_null($channelID) || !count($newConfig) ){
			return false;
		}

		$config = $this->loadConfig();

		foreach($config as $index => $site){
			if( $site[$this->uniqueKey] != $channelID ){ continue; }

			$config[$index] = array_merge($site, $newConfig);
		}

		GAEBucket::save($this->configPath, json_encode($config));
		return true;
	}

	public function addNewSite($newConfig = []){
		if( !count($newConfig) ){ return false; }

		$config = $this->loadConfig();
		$config[] = $newConfig;

		GAEBucket::save($this->configPath, json_encode($config));
		return true;
	}

	public function deleteSite($channelID){
		$config = $this->loadConfig();

		foreach($config as $index => $site){
			if( $site[$this->uniqueKey] == $channelID ){ 
				unset($config[$index]);
				GAEBucket::save($this->configPath, json_encode($config));
				return true;
			}
		}

		return false;
	}

}