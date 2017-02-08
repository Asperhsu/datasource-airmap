<?php
namespace Asper\Firebase;
use Kreait\Firebase\Configuration;
use Kreait\Firebase\Firebase;
use Ivory\HttpAdapter\FileGetContentsHttpAdapter;

final class Factory {

	/**
	 * Private ctor so nobody else can instance it
	 */
	private function __construct(){
		
	}
	
	public static function create(){
		static $instance = null;

		if( $instance === null ){
			$instance = self::getSDK();
		}

		return $instance;
	}

	private static function getSDK(){
		$dbUrl = getenv('FIREBASE_DATABASE_URL');
		$serviceAccount = getenv('FIREBASE_SERVICE_ACCOUNT_JSON');

		//check necessary config
		if( !strlen($dbUrl) ){ 
			throw new Exception("Require ENV FIREBASE_DATABASE_URL"); 
		}
		if( !strlen($serviceAccount) ){ 
			throw new Exception("Require ENV FIREBASE_SERVICE_ACCOUNT_JSON"); 
		}

		$config = new Configuration();

		//get auth json string, remove "'" from env
		$authjson = str_replace("'", "", $serviceAccount);
		$authData = json_decode($authjson, true);
		$config->setAuthConfigFile($authData);

		//GAE using file_get_contents, overwrite original
		$http = new FileGetContentsHttpAdapter();
		$config->setHttpAdapter($http);
		// print_r($config);

		$firebase = new Firebase($dbUrl, $config);

		return $firebase;
	}	
}