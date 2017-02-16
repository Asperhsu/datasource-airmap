<?php
namespace Asper\Datasource;

use Asper\DateHelper;

class Independent extends Thingspeak {
	protected $defaultConfigPath = "/conf/indep.json";
	protected $configPath = "indep.config.json";
	protected $group = 'Independent';

	protected function transform($row=[]){
		$data = [
			'SiteName' 	=> $this->channel['name'],
			'LatLng'	=> [
				'lat' => $this->channel['latitude'],
				'lng' => $this->channel['longitude'],
			],
			'SiteGroup' => $this->group,
			'Maker'		=> $this->channel['Maker'],
			'Data'		=> [
				'Create_at' => DateHelper::convertTimeToTZ($row['created_at'])
			]
		];

		return $data;
	}

}