<?php
namespace Asper\Datasource;

class LASS extends Base {
	protected $url = "https://data.lass-net.org/data/last-all-lass.json";

	protected $group = 'LASS';
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
		$data = $this->processFeeds($data['feeds']);
		$this->save($data);

		return $data;
	}

	protected function transform($row=[]){
		$name = isset($row['SiteName']) 
				? $row['SiteName'] 
				: $row['device_id'];

		$data = [
			'SiteName' 	=> $name,
			'LatLng'	=> [
				'lat' => $row['gps_lat'],
				'lng' => $row['gps_lon'],
			],
			'SiteGroup' => $this->group,
			'Marker'	=> $this->group,
			'RawData'	=> $row,
			'Data'		=> [
				'Create_at' => $this->convertTimeToTZ($row['timestamp'])
			]
		];

		return $data;
	}
}