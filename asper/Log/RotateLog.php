<?php
namespace Asper\Log;

use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;

class RotateLog {

	protected $logger;

	public function __construct($filename, $maxFiles=0){
		$logger = new Logger('rotateLog');

		$formatter = new JsonFormatter();
		$handler = new RotatingFileHandler($filename, $maxFiles);
		$handler->setFormatter($formatter);
		
		$logger->pushHandler($handler);

		$this->logger = $logger;
	}

	public function getLogger($name=null){
		return $name === null ? 
				$this->logger : 
				$this->logger->withName($name);
	}
}

// $cronlogFilename = "cron.log";
// $logPath = GAEBucket::getRealFilePath($cronlogFilename);
// // echo $cronlogFilename;
// // echo $logPath;

// $logger = (new RotateLog($logPath))
// 			->getLogger('LogTest');
// $logger->info('test');

// var_dump(GAEBucket::load($cronlogFilename));