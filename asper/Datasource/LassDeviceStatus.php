<?php
namespace Asper\Datasource;

use Asper\DateHelper;

class LassDeviceStatus extends LassAnalysis {
	const STATUS_INDOOR = "indoor|malfunction";
	const STATUS_LONGTERM_POLLUTION = "longterm-pollution|malfunction";
	const STATUS_SHORTTERM_POLLUTION = "shortterm-pollution";
	/**
	 * malfunction manual
	 * "type-1": "non-detectable", 代表鄰居太少無從比較
	 * "type-2": "spatially greater", 代表接近長時間的固定污染源或機器故障
	 * "type-3": "spatially less" 代表可能為室內機或機器故障
	 * rate <= 0.3 is false, other is true
	 */
	protected $malfunctionUrl = "https://data.lass-net.org/data/device_malfunction_daily.json";

	protected $pollutionUrl = "https://data.lass-net.org/data/device_pollution.json";

	public function exec(){
		$data = $this->execMalfunction();

		$STpollution = $this->execShortTermPollution();
		foreach($STpollution as $uniqueKey => $status){
			if( isset($data[$uniqueKey]) ){
				$data[$uniqueKey] .= "|" . $status;
			}else{
				$data[$uniqueKey] = $status;
			}
		}

		$this->save($data);

		return $data;
	}

	protected function execMalfunction(){
		$response = $this->fetchRemote($this->malfunctionUrl);
		if($response === null){ return false; }

		$data = [];
		$recordCountLog = [
			'total' => 0,
			'longterm-pollution' => 0,
			'indoor' => 0,
		];

		$malfunctions = json_decode($response, true);
		if($malfunctions === null){
			$this->logger->info('response can not decode');
			return false;
		}

		foreach($malfunctions['feeds'] as $feed){
			$key = $feed['device_id'];

			if( self::isBelong($feed["2"]) ){
				$data[$key] = self::STATUS_LONGTERM_POLLUTION;
				$recordCountLog['longterm-pollution']++;
			}
			if( self::isBelong($feed["3"]) ){
				$data[$key] = self::STATUS_INDOOR;
				$recordCountLog['indoor']++;
			}

			$recordCountLog['total']++;
		}

		$msg = "fetch malfunction results";
		$this->logger->info($msg, $recordCountLog);

		return $data;
	}

	protected function execShortTermPollution(){
		$response = $this->fetchRemote($this->pollutionUrl);
		if($response === null){ return false; }

		$data = [];
		$total = 0;

		$pollutions = json_decode($response, true);
		if($pollutions === null){
			$this->logger->info('response can not decode');
			return false;
		}
		
		foreach($pollutions['feeds'] as $feed){
			$key = $feed['device_id'];
			$data[$key] = self::STATUS_SHORTTERM_POLLUTION;

			$total++;
		}

		$msg = "fetch short term pollution results";
		$this->logger->info($msg, compact('total'));

		return $data;
	}

	public static function isBelong($rate){
		return $rate <= 0.3 ? false : true;
	}

	public function getStatus($uniqueKey){
		$data = $this->load();

		if( !isset($data[$uniqueKey]) ){
			return null;
		}

		return $data[$uniqueKey];
	}

}