<?php
namespace Asper;

use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Formatter\JsonFormatter;

class LoggerFactory {
	private $logger;

	/**
	 * Private ctor so nobody else can instance it
	 */
	private function __construct(){
		$logger = new Logger('application');

		$formatter = new JsonFormatter();
		$handler = new SyslogHandler('datasourceLog');
		$handler->setFormatter($formatter);
		
		$logger->pushHandler($handler);

		$this->logger = $logger;
	}

	public static function create(){
		static $instance = null;

		if( $instance === null ){
			$instance = new LoggerFactory();
		}

		return $instance;
	}

	public function getLogger($name=null){
		return $name === null ? 
				$this->logger : 
				$this->logger->withName($name);
	}
}