<?php
namespace Asper\Datasource;
use Asper\GAEBucket;

class Independent extends Base {
	protected $defaultConfigPath = "/conf/indep.json";
	protected $configPath = "indep.config.json";
	protected $group = 'Indep';
	protected $urlTemplate = "https://api.thingspeak.com/channels/{{identity}}/feeds.json?results=1";
	protected $channel = [];

	public function exec(){
		$feeds = [];
		$lastUpdate = [];
		$config = $this->loadConfig();

		foreach($config as $site){
			if( isset($site['active']) && $site['active'] === false ){
				continue;
			}

			//fetch
			$url = str_replace('{{identity}}', $site['Channel_id'], $this->urlTemplate);
			$response = $this->fetchRemote($url);
			if($response === null){ continue; }

			$data = json_decode($response, true);

			//prepare fields mapping and transform data
			$this->channel = array_merge($site, $data['channel']);
			$this->fieldMapping = $site['Option'];

			//record last update for config
			$lastUpdate[$site['Channel_id']] = $data['channel']['updated_at'];
			

			$data = $this->processFeeds($data['feeds']);
			$feeds = array_merge($feeds, $data);
		}

		$this->updateConfigUpdateTimestamp($lastUpdate);

		$this->save($feeds);

		return $feeds;
	}

	protected function transform($row=[]){
		$data = [
			'Channel_id'=> $this->channel['id'],
			'SiteName' 	=> $this->channel['name'],
			'LatLng'	=> [
				'lat' => $this->channel['latitude'],
				'lng' => $this->channel['longitude'],
			],
			'SiteGroup' => $this->group,
			'Marker'	=> $this->channel['Maker'],
			'RawData'	=> $row,
			'Data'		=> [
				'Create_at' => $this->convertTimeToTZ($row['created_at'])
			]
		];

		return $data;
	}
	
	public function loadConfig(){
		$config = GAEBucket::load($this->configPath);

		if( !$config ){	//config not exist, use and copy default config
			if(file_exists(getenv("APP_PATH") . $this->defaultConfigPath)){
				$config = file_get_contents(getenv("APP_PATH") . $this->defaultConfigPath);
				GAEBucket::save($this->configPath, $config);
			}else{
				$msg = get_class($this) . " default config not exist.";
				$this->logger->error($msg);
				throw new \Exception($msg);
			}
		}

		$config = json_decode($config, true);
		return $config;
	}

	protected function updateConfigUpdateTimestamp($lastUpdate){
		$config = $this->loadConfig();

		foreach($config as $index => $site){
			$id = $site['Channel_id'];
			if( !isset($lastUpdate[$id]) ){ continue; }

			$update_at = $this->convertTimeToTZ($lastUpdate[$id]);
			$config[$index]['update_at'] = $update_at;
		}

		GAEBucket::save($this->configPath, json_encode($config));
		return true;
	}

	public function updateSiteConfig($channelID=null, $newConfig=[]){
		if( is_null($channelID) || !count($newConfig) ){
			return false;
		}

		$config = $this->loadConfig();

		foreach($config as $index => $site){
			if( $site['Channel_id'] != $channelID ){ continue; }

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
			if( $site['Channel_id'] == $channelID ){ 
				unset($config[$index]);
				GAEBucket::save($this->configPath, json_encode($config));
				return true;
			}
		}

		return false;
	}

}