<?php
namespace Asper;

use Asper\LoggerFactory;

class GAEBucket {
	
	public static function getLogger(){
		return LoggerFactory::create()->getLogger('GAEBucket');
	}

	public static function getBucketPath(){
		$path = getenv('GAE_BUCKET');
		if( !strlen($path) ){
			throw new Exception("GAE bucket path is not valid, please assign in .env");
		}

		if( strpos($path, "gs://") === false ){
			$path = "gs://" . $path;
		}

		return $path;
	}

	public static function save($path, $data){
		$logger = self::getLogger();
		$bucketPath = self::getBucketPath();

		$fullPath = implode('/', [$bucketPath, $path]);
		$result = file_put_contents($fullPath, $data);
		
		if($result === false){
			$logger->warn("save ".$fullPath." error");
			return false;
		}else{
			$logger->info("save ".$fullPath." success, total bytes: ".$result);
			return true;
		}
	}

	public static function load($path){
		$logger = self::getLogger();
		$bucketPath = self::getBucketPath();

		$fullPath = implode('/', [$bucketPath, $path]);

		if(file_exists($fullPath)){
			$result = file_get_contents($fullPath);			
		}else{
			$logger->warn($fullPath . " not exist");
			return false;
		}
		
		if($result === false){
			$logger->warn("load ".$fullPath." error");
			return false;
		}else{
			$logger->info("load ".$fullPath." success");
			return $result;
		}
	}

}