<?php
namespace Asper\Log;

use Google\Cloud\Datastore\DatastoreClient;
use Asper\DateHelper;

class CronLogQuery {
	protected $datastore;
	protected $query;

	protected $kind;
	protected $data;

	public function __construct($kind){
		$this->datastore = new DatastoreClient(['projectId' => env('PROJECT_ID')]);

		$this->kind = $kind;
	}

	public function setTimeRange($range){
		$this->query = $this->datastore->query()
			->kind('Datasource::' . $this->kind)
			->filter('datetime.date', '>=', date('Y-m-d H:i', strtotime($range)))
			->order('datetime.date');

		return $this;
	}

	public function run(){
		$result = $this->datastore->runQuery($this->query);

		$data = [
			'count' => [],
			'diffUniqueKeys' => [],
		];
		
		foreach ($result as $key => $entity) {
			$date = DateHelper::convertTimeToTZ($entity['datetime']['date']);
			if( $entity['channel'] == "processFeeds" && strpos($entity['message'], 'exec') !== false ){
				$data['count'][] = [
					'utc' => $date,
					'total' => $entity['context']['total'],
				];
			}

			if($entity['channel'] == "logDiffUniqueKeys"){
				$data['diffUniqueKeys'][] = [
					'utc' => $date,
					'add' => implode(", ", $entity['context']['add']),
					'remove' => implode(", ", $entity['context']['remove']),
				];
			}
		}

		$this->data = $data;

		return $this;
	}

	public function getCount(){
		return $this->data['count'];
	}

	public function getDiffUniqueKeys(){
		return $this->data['diffUniqueKeys'];
	}

}