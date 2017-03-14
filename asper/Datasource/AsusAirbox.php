<?php
namespace Asper\Datasource;

use Asper\DateHelper;

class AsusAirbox extends Base {
	protected $baseUrl = "https://airbox.asuscloud.com/airbox/";
	protected $feedUrl = "messages/";
	protected $deviceHistoryUrl = "device/%s/%s/%s";	//device/:id/:startTimestamp/:endTimeStamp

	protected $header = [
		"Prefix: 781463DA"
	];

	protected $group = 'Asus-Airbox';
	protected $maker = 'Asus';
	protected $uniqueKey = 'id';
	protected $fieldMapping = [
		'pm25' => 'Dust2_5',
		'humidity' => 'Humidity',
		'temperature' => 'Temperature',
	];

	public function exec(){
		$url = $this->baseUrl.$this->feedUrl;
		$response = $this->fetchRemote($url);
		if($response === null){ return false; }
		
		$data = json_decode($response, true);
		if($data === null){
			$this->logger->warn("json decode failed");
			return false;
		}

		$data = $this->processFeeds($data);
		$this->logDiffUniqueKeys($data);
		$this->save($data);

		return $data;
	}

	protected function transform($row=[]){
		$data = [
			'SiteName' 	=> $row['name'],
			'LatLng'	=> [
				'lat' => $row['lat'],
				'lng' => $row['lng'],
			],
			'SiteGroup' => $this->group,
			'Maker'		=> $this->maker,
			'Data'		=> [
				'Create_at' => DateHelper::convertTimeToTZ($row['time'])
			]
		];

		return $data;
	}

	public function queryHistory($uniqueKey, $startTimestamp, $endTimestamp){
		$startMs 	= strtotime(DateHelper::convertTimeToTZ($startTimestamp)) * 1000;
		$endMs		= strtotime(DateHelper::convertTimeToTZ($endTimestamp)) * 1000;
		$url 		= sprintf($this->baseUrl.$this->deviceHistoryUrl, $uniqueKey, $startMs, $endMs);

		$response = $this->fetchRemote($url);
		if($response === null){ return []; }

		$feeds = [];
		$data = json_decode($response, true);

		foreach($data as $row){
			$site = [
				'SiteName' 	=> '',	//doesnt no matter
				'LatLng'	=> '',	//doesnt no matter
				'SiteGroup' => $this->group,
				'Maker'		=> $this->maker,
				'Data'		=> [
					'Temperature' 	=> $row['s_t0'],
					'Humidity' 		=> $row['s_h0'],
					'Dust2_5' 		=> $row['s_d0'],
					'Create_at' 	=> DateHelper::convertTimeToTZ($row['time'])
				]
			];

			$feeds[] = $site;
		}

		return $this->convertFeedsToHistory($feeds);
	}
}