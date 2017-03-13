<?php
namespace Asper\Datasource;

use Asper\DateHelper;

class LASS extends Base {
	protected $feedUrl = "https://data.lass-net.org/data/last-all-lass.json";
	protected $deviceHistoryUrl = "https://pm25.lass-net.org/data/history-date.php?device_id=%s&date=%s";	//date fotmat:YYYY-mm-dd
	protected $deviceLastestUrl = "https://data.lass-net.org/data/last.php?device_id=%s";

	protected $group = 'LASS';
	protected $maker = 'LASS';
	protected $uniqueKey = 'device_id';
	protected $fieldMapping = [
		's_d0' => 'Dust2_5',
		's_d1' => 'PM10',
		's_d2' => 'PM1',
		's_h0' => 'Humidity',
		's_h2' => 'Humidity',
		's_t0' => 'Temperature',
	];

	public function exec(){
		$response = $this->fetchRemote();
		if($response === null){ return false; }
		
		$data = json_decode($response, true);
		if($data === null){
			$this->logger->warn("json decode failed");
			return false;
		}

		$data = $this->processFeeds($data['feeds']);
		$this->logDiffUniqueKeys($data);
		$this->save($data);

		return $data;
	}

	protected function transform($row=[]){
		$name = isset($row['SiteName']) 
				? $row['SiteName'] 
				: $row['device_id'];

		$data = [
			'SiteName' 	=> $name,
			'LatLng'	=> [
				'lat' => $row['gps_lat'],
				'lng' => $row['gps_lon'],
			],
			'SiteGroup' => $this->group,
			'Maker'		=> $this->maker,
			'Data'		=> [
				'Create_at' => DateHelper::convertTimeToTZ($row['timestamp'])
			]
		];

		return $data;
	}

	public function queryLastest($id, $includeRAW=false){
		$url = sprintf($this->deviceLastestUrl, $id);
		$response = $this->fetchRemote($url);
		if($response === null){ return []; }

		$data = json_decode($response, true);
		$dataFeeds = array_values($data['feeds'][0]);
		$site = $this->processFeeds($dataFeeds);
		
		if( !count($site) ){ return []; }

		$site = array_shift($site);
		if(!$includeRAW){
			unset($site['RawData']);
		}
		return $site;
	}

	public function queryHistory($id, $startTimestamp, $endTimestamp){
		$startTZ 	= DateHelper::convertTimeToTZ($startTimestamp);
		$endTZ 		= DateHelper::convertTimeToTZ($endTimestamp);
		$queryDates = DateHelper::calcDateRange($startTZ, $endTZ);
		$filter 	= function($site) use ($startTZ, $endTZ) {
			if( !isset($site['Data']['Create_at']) ){ 
				return true; 
			}

			return Filter::inTimeRange($site['Data']['Create_at'], $startTZ, $endTZ);
		};

		$feeds = [];
		foreach($queryDates as $date){
			$url = sprintf($this->deviceHistoryUrl, $id, $date);
			$response = $this->fetchRemote($url);
			if($response === null){ continue; }

			$data = json_decode($response, true);
			if( !isset($data['feeds'][0]) ){ continue; }

			$dataFeeds = array_shift(array_values($data['feeds'][0]));
			$data = $this->processFeeds($dataFeeds, $filter);
			$feeds = array_merge($feeds, $data);
		}

		//sort by create_at asc
		usort($feeds, function($a, $b){
			return strtotime($a['Data']['Create_at']) < strtotime($b['Data']['Create_at']) ? -1: 1;
		});

		return $this->convertFeedsToHistory($feeds);
	}

}