<?php
namespace Asper\Datasource;

use Asper\DateHelper;

class LassRanking extends LassAnalysis {
	protected $url = "https://data.lass-net.org/data/device_ranking.json";

	public function exec(){
		$response = $this->fetchRemote($this->url);
		if($response === null){ return false; }

		$data = [];
		$recordCountLog = [
			'total' => 0,
			'ranking' => [0, 0, 0, 0, 0 ,0],
		];

		$rankings = json_decode($response, true);
		if($rankings === null){
			$this->logger->info('response can not decode');
			return false;
		}

		foreach($rankings['feeds'] as $ranking){
			$key = $ranking['device_id'];
			$ranking = self::ranking2Level($ranking['ranking']);

			$data[$key] = [
				'name'		=> $ranking['SiteName'],
				'source'	=> $ranking['source'],
				'LatLng'	=> [
					'lat'	=> $ranking['gps_lat'],
					'lng'	=> $ranking['gps_lon'],
				],
				'ranking' 	=> $ranking,
				'update_at' => DateHelper::convertTimeToTZ($ranking['timestamp']),
			];

			if( !is_null($ranking) ){
				$recordCountLog['ranking'][$ranking]++;
			}

			$recordCountLog['total']++;
		}

		$this->save($data);

		$msg = "fetch device ranking results";
		$this->logger->info($msg, $recordCountLog);

		return $data;
	}

	public static function ranking2Level($ranking){
		if( $ranking < 0.5 ){ return 0; }
		if( $ranking >= 0.5 && $ranking < 0.6 ){ return 1; }
		if( $ranking >= 0.6 && $ranking < 0.7 ){ return 2; }
		if( $ranking >= 0.7 && $ranking < 0.8 ){ return 3; }
		if( $ranking >= 0.8 && $ranking < 0.9 ){ return 4; }
		if( $ranking >= 0.9 && $ranking <= 1 ){ return 5; }

		return null;
	}

	public function getRank($uniqueKey){
		$data = $this->load();

		if( !isset($data[$uniqueKey]) ){
			return null;
		}

		return $data[$uniqueKey]['ranking'];
	}

	public function getInfo($uniqueKey){
		$data = $this->load();

		if( !isset($data[$uniqueKey]) ){
			return null;
		}

		return $data[$uniqueKey];
	}

}