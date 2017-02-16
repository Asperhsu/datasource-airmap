<?php
namespace Asper\Datasource;

use Asper\DateHelper;

class ProbeCube extends Thingspeak {
	protected $defaultConfigPath = "/conf/probecube.json";
	protected $configPath = "probecube.config.json";
	protected $group = 'ProbeCube';

	protected $fieldMapping = [
		'field5' => 'Dust2_5',
		'field2' => 'Humidity',
		'field1' => 'Temperature',
	];

	protected function transform($row=[]){
		$data = [
			'SiteName' 	=> $this->channel['name'],
			'LatLng'	=> [
				'lat' => $this->channel['latitude'],
				'lng' => $this->channel['longitude'],
			],
			'SiteGroup' => $this->group,
			'Maker'		=> $this->channel['maker'],
			'Data'		=> [
				'Create_at' => DateHelper::convertTimeToTZ($row['created_at'])
			]
		];

		return $data;
	}

}