<?php
namespace Asper\JsonHandler;

use Asper\Log\CronLogQuery;
use Asper\Memcached;

class DatasourceLog implements Handleable{

	use ResponseTrait;

	protected $mapping = [
		'lass-log' => 'LASS',
		'lass-4u-log' => 'LASS-4U',
		'lass-maps-log' => 'LASS-MAPS',
		'edimax-airbox-log' => 'Edimax-Airbox',
	];

	public function register(){
		return array_keys($this->mapping);
	}

	public function trigger(Array $params=[]){
		$memcached = new Memcached(env('MEMCACHED_PREFIX'));
		
		$data = $memcached->get($params['requestFile']);
		if($data === false){
			$datasource = $this->mapping[$params['requestFile']];

			$Query = new CronLogQuery($datasource);
			$Query->setTimeRange('-12 hours')->run();

			$count = $Query->getCount();
			$diff = $Query->getDiffUniqueKeys();

			$data = compact('count', 'diff');
			$memcached->set($params['requestFile'], json_encode($data), 60*60);
		}

		$callback = isset($params['callback']) ? $params['callback'] : null;
		$this->setExpire( 60*60 );
		$this->response($data, $callback);
	}

}