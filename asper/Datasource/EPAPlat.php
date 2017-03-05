<?php
namespace Asper\Datasource;

class EPAPlat extends Base {
	protected $feedUrl = "http://taqm.g0v.asper.tw/plat.json";

	protected $group = 'EPA';
	protected $uniqueKey = 'RawData.SiteID';

	public function exec(){
		$response = $this->fetchRemote();
		if($response === null){ return false; }
		
		$data = json_decode($response, true);
		if($data === null){
			$this->logger->warn("json decode failed");
			return false;
		}

		$data = $this->processFeeds($data);
		$this->save($data);

		return $data;
	}

	protected function transform($row=[]){
		return $row;
	}
}