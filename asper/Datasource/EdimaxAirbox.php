<?php
namespace Asper\Datasource;

class EdimaxAirbox extends Base {
	protected $baseUrl = "https://airbox.edimaxcloud.com/";
	protected $feedUrl = "devices";
	protected $historyUrl = "query_history_raw";
	private $token = "";

	protected $group = 'Airbox_Edimax';
	protected $fieldMapping = [
		'pm25' 	=> 'Dust2_5',
		't' 	=> 'Temperature',
		'h' 	=> 'Humidity',
	];

	public function __construct(){
		parent::__construct();

		$this->token = getenv("EDIMAX_AIRBOX_TOKEN");
		if( !strlen($this->token) ){
			$msg = "access token is not valid, please assign in .env";
			$this->logger->error($msg);
			throw new \Exception($msg);
		}
	}

	public function exec(){
		$url = implode("",  [
			$this->baseUrl,
			$this->feedUrl,
			'?token='.$this->token,
		]);
		$response = $this->fetchRemote($url);
		if($response === null){ return false; }
		
		$data = json_decode($response, true);
		$data = $this->processFeeds($data['devices']);
		$this->save($data);

		return $data;
	}

	protected function transform($row=[]){
		$data = [
			'SiteName' 	=> $row['name'],
			'LatLng'	=> [
				'lat' => $row['lat'],
				'lng' => $row['lon'],
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

	public function fetchDeviceHistory($id=null, $start=null, $end=null){
		if( is_null($id) || is_null($start) || is_null($end) ){
			return false;
		}

		$param = implode("&", [
			'token='.$this->token,
			'id='.$id,
			'start='.date('Y-m-d H:i:s', strtotime($start)),
			'end='.date('Y-m-d H:i:s', strtotime($end)),
		]);

		//do not use urlencode, only replace space to %20
		$param = str_replace(' ', '%20', $param);

		$url = implode("", [
			$this->baseUrl . $this->historyUrl,
			'?'.$param
		]);

		$response = $this->fetchRemote($url);
		if($response === null){ return false; }
		
		$data = json_decode($response, true);
		return $data;
	}
}