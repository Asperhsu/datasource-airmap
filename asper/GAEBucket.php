<?php
namespace Asper;

use Asper\LoggerFactory;

class GAEBucket {
	public static $bucketPath = null;
	
	public static function getLogger(){
		return LoggerFactory::create()->getLogger('GAEBucket');
	}

	public static function getBucketPath(){
		if( !is_null(self::$bucketPath) ){
			return self::$bucketPath;
		}

		$bucketPath = getenv('GAE_BUCKET');

		if( !strlen($bucketPath) ){
			throw new \Exception("GAE bucket path is not valid, please assign in .env");
		}

		if( strpos($bucketPath, "gs://") === false ){
			$bucketPath = "gs://" . $bucketPath;
		}

		self::$bucketPath = $bucketPath;
		return $bucketPath;
	}

	public static function getRealFilePath($path){
		$bucketPath = self::getBucketPath();
		$fullPath = implode('/', [$bucketPath, $path]);
		return $fullPath;
	}

	public static function save($path, $data){
		$logger = self::getLogger();
		$fullPath = self::getRealFilePath($path);

		$time_start = microtime(true); 
		$result = file_put_contents($fullPath, $data);
		$logger->info('save file spend time in seconds: ' . (microtime(true) - $time_start));
		
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
		$fullPath = self::getRealFilePath($path);

		if(file_exists($fullPath)){
			$time_start = microtime(true); 
			$result = file_get_contents($fullPath);		
			$logger->info('load file spend time in seconds: ' . (microtime(true) - $time_start));	
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

	public static function exist($path){
		$logger = self::getLogger();
		$fullPath = self::getRealFilePath($path);
		return file_exists($fullPath) ? $fullPath : false;
	}

	public static function createWhenNotExist($path){
		$fullPath = self::exist($path);

		if($fullPath === false){
			self::save($path, '');
			return self::getRealFilePath($path);
		}
		return $fullPath;
	}

}