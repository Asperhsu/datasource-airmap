<?php
namespace Asper\Datasource;

class EPAPlat extends Base {
	protected $url = "http://taqm.g0v.asper.tw/plat.json";

	protected $group = 'EPA';
	protected $fieldMapping = [
		's_d0' => 'Dust2_5',
		's_d1' => 'PM10',
		's_d2' => 'PM1',
		's_h0' => 'Humidity',
		's_h2' => 'Humidity',
		's_t0' => 'Temperature',
	];

	public function exec(){
		$response = $this->fetchRemote();
		if($response === null){ return false; }
		
		$data = json_decode($response, true);
		$data = $this->processFeeds($data);
		$this->save($data);

		return $data;
	}

	protected function transform($row=[]){
		return $row;
	}
}