<?php
namespace Asper;

use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Formatter\JsonFormatter;
use Asper\Log\GoogleCloudDatastoreHandler;

class LoggerFactory {
	private $logger;

	/**
	 * Private ctor so nobody else can instance it
	 */
	private function __construct($type, $name){
		$logger = new Logger('application');
		$formatter = new JsonFormatter();

		switch($type){
			default:
			case 'syslog':
				$handler = new SyslogHandler($name);
				break;
			case 'datastore':
				$handler = new GoogleCloudDatastoreHandler($name);
				break;
		}

		$handler->setFormatter($formatter);
		$logger->pushHandler($handler);

		$this->logger = $logger;
	}

	public static function create($type='syslog', $name='datasourceLog'){
		static $instances = [];

		$index = md5($type.$name);
		if( !isset($instances[$index]) ){
			$instances[$index] = new LoggerFactory($type, $name);
		}

		return $instances[$index];
	}

	public function getLogger($name=null){
		return $name === null ? 
				$this->logger : 
				$this->logger->withName($name);
	}
}