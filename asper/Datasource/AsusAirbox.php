<?php
namespace Asper\Datasource;

class AsusAirbox extends Base {
	protected $baseUrl = "http://airbox.asuscloud.com/airbox/";
	protected $feedUrl = "messages/";
	protected $header = [
		"Prefix: 781463DA"
	];

	protected $group = 'Airbox_Asus';
	protected $fieldMapping = [
		'pm25' => 'Dust2_5',
		'humidity' => 'Humidity',
		'temperature' => 'Temperature',
	];

	public function exec(){
		$url = $this->baseUrl.$this->feedUrl;
		$response = $this->fetchRemote($url);
		if($response === null){ return false; }
		
		$data = json_decode($response, true);
		$data = $this->processFeeds($data);
		$this->save($data);

		return $data;
	}

	protected function transform($row=[]){
		$data = [
			'SiteName' 	=> $row['name'],
			'LatLng'	=> [
				'lat' => $row['lat'],
				'lng' => $row['lng'],
			],
			'SiteGroup' => $this->group,
			'Marker'	=> $this->group,
			'RawData'	=> $row,
			'Data'		=> [
				'Create_at' => $this->convertTimeToTZ($row['time'])
			]
		];

		return $data;
	}
}