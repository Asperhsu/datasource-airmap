<?php
namespace Asper\Datasource;
use Asper\GAEBucket;

class ProbeCube extends Independent {
	protected $defaultConfigPath = "/conf/probecube.json";
	protected $configPath = "probecube.config.json";
	protected $group = 'ProbeCube';
	protected $fieldMapping = [
		'field5' => 'Dust2_5',
		'field2' => 'Humidity',
		'field1' => 'Temperature',
	];

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

			//prepare transform data
			$this->channel = array_merge($site, $data['channel']);

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
			'Marker'	=> $this->channel['maker'],
			'RawData'	=> $row,
			'Data'		=> [
				'Create_at' => $this->convertTimeToTZ($row['created_at'])
			]
		];

		return $data;
	}

}