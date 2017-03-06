<?php
namespace Asper;

use Asper\LoggerFactory;
use Asper\GAEBucket;

class User {
	protected $configPath = "user.config.json";
	protected $config = null;

	public function __construct(){
		$this->logger = LoggerFactory::create()->getLogger('User');
	}

	public function loadConfig(){
		if( !is_null($this->config) ){ return $this->config; }

		$config = GAEBucket::load($this->configPath);
		if( !$config ){	//config not exist, use and copy default config
			if(file_exists(env("APP_PATH") . $this->defaultConfigPath)){
				$password = env('DEFAULT_ADMIN_PASSWORD');
				if( !strlen($password) ){
					throw new Exception("default admin password is not valid, please assign in .env");
				}

				$config = json_encode([
					'admin' => [
						'password' => sha1($password),
						'permission' => 'admin'
					]
				]);
				GAEBucket::save($this->configPath, $config);
			}else{
				$msg = get_class($this) . " default config not exist.";
				$this->logger->error($msg);
				throw new \Exception($msg);
			}
		}

		$this->config = json_decode($config, true);
		return $this->config;
	}

	public function getPermission($username){
		if( $this->isUserExist($username) ){
			$config = $this->loadConfig();
			return $config[$username]['permission'];
		}else{
			return null;
		}
	}

	protected function checkPermission($input){
		$accept = ["viewer", "operator", "admin"];
		return in_array($input, $accept) ? $input : "view";
	}

	public function login($loginName, $loginPasswd){
		if( $this->isUserExist($loginName) ){
			$config = $this->loadConfig();
			return $config[$loginName]['password'] == sha1($loginPasswd);
		}else{
			return false;
		}
	}

	public function isUserExist($loginName){

		$config = $this->loadConfig();
		return isset($config[$loginName]);
	}

	public function addUser($loginName, $loginPasswd, $loginPermission){
		if( $this->isUserExist($loginName) ){ 
			return false; 
		}

		$config = $this->loadConfig();
		$config[$loginName] = [
			'password' 	 => sha1($loginPasswd),
			'permission' => $this->checkPermission($loginPermission)
		];
		
		GAEBucket::save($this->configPath, json_encode($config));
		return true;
	}

	public function edit($username, $newPassword=null, $newPermission=null){
		if( is_null($newPassword) && is_null($newPermission) ){ 
			return false; 
		}
		if( !$this->isUserExist($username) ){ return false; }

		$config = $this->loadConfig();
		if( !is_null($newPassword) ){
			$config[$username]['password'] = sha1($newPassword);			
		}
		if( !is_null($newPermission) ){
			$config[$username]['permission'] = $this->checkPermission($newPermission);
		}
		
		GAEBucket::save($this->configPath, json_encode($config));
		return true;
	}

	public function delete($username=null){
		if( is_null($username) || 
			$username == 'admin' ||
			!$this->isUserExist($username) ){ 
			return false;
		}

		$config = $this->loadConfig();
		unset($config[$username]);

		GAEBucket::save($this->configPath, json_encode($config));
		return true;
	}
}