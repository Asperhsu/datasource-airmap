<?php
namespace Asper\Log;

use Monolog\Logger;
use Monolog\Handler\AbstractSyslogHandler;
use Google\Cloud\Datastore\DatastoreClient;

/**
 * Logs to Google Cloud Datastore service.
 *
 * usage example:
 *
 *   $log = new Logger('application');
 *   $syslog = new SyslogHandler('myfacility', 'local6');
 *   $formatter = new LineFormatter("%channel%.%level_name%: %message% %extra%");
 *   $syslog->setFormatter($formatter);
 *   $log->pushHandler($syslog);
 *
 * @author Asper <asperwon@gmail.com>
 */

class GoogleCloudDatastoreHandler extends AbstractSyslogHandler
{
	protected $datastore;
	protected $tasks = [];
	protected $ident;
	/**
	 * @param string  $ident
	 * @param mixed   $facility
	 * @param int     $level    The minimum logging level at which this handler will be triggered
	 * @param Boolean $bubble   Whether the messages that are handled can bubble up the stack or not
	 */
	public function __construct($ident, $facility = LOG_USER, $level = Logger::DEBUG, $bubble = true)
	{
		parent::__construct($facility, $level, $bubble);
		
		$this->datastore = new DatastoreClient([
			'projectId' => env('PROJECT_ID')
		]);

		$this->ident = $ident;
	}

	 /**
	 * {@inheritdoc}
	 */
	public function close()
	{
		$this->datastore->upsertBatch($this->tasks);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function write(array $record)
	{
		$msg = (string) $record['formatted'];
		$info = json_decode($msg, true);
		if($info === null){
			$info = compact('msg');
		}

		$key = $this->datastore->key($this->ident);
		$task = $this->datastore->entity($key, $info);
		$this->tasks[] = $task;
	}
}