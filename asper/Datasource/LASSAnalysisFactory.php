<?php
namespace Asper\Datasource;

use Asper\LoggerFactory;
use Asper\GAEBucket;

class LASSAnalysisFactory {
	public static $LassRanking;
	public static $LassDeviceStatus;

	private function __construct(){

	}

	public static function getRankingInstance(){
		if(!self::$LassRanking){
			self::$LassRanking = new LassRanking();
		}

		return self::$LassRanking;
	}

	public static function getDeviceStatusInstance(){
		if(!self::$LassDeviceStatus){
			self::$LassDeviceStatus = new LassDeviceStatus();
		}

		return self::$LassDeviceStatus;
	}

}