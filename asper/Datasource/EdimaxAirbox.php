<?php
namespace Asper\Datasource;

use Asper\DateHelper;

class EdimaxAirbox extends Base {
	protected $baseUrl = "https://airbox.edimaxcloud.com/";
	protected $feedUrl = "devices";
	protected $deviceHistoryUrl = "query_history_raw?token=%s&id=%s&start=%s&end=%s";	//date format: YYYY-mm-dd%20H:i:s
	private $token = "";

	protected $group = 'Edimax-Airbox';
	protected $maker = 'Edimax';
	protected $uniqueKey = 'id';
	protected $fieldMapping = [
		'pm25' 	=> 'Dust2_5',
		't' 	=> 'Temperature',
		'h' 	=> 'Humidity',
	];

	public function __construct(){
		parent::__construct();
		
		$this->token = env('EDIMAX_AIRBOX_TOKEN');
		if( $this->token === false ){
			$msg = "access token is not valid, please assign in env.php. " . var_export($this->token, true);
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
		if($data === null){
			$this->logger->warn("json decode failed");
			return false;
		}

		$data = $this->processFeeds($data['devices']);
		$this->logDiffUniqueKeys($data);
		$this->save($data);

		return $data;
	}

	protected function transform($row=[]){
		if($row['type'] == "lass-airbox"){ return false; }

		$data = [
			'SiteName' 	=> $row['name'],
			'LatLng'	=> [
				'lat' => $row['lat'],
				'lng' => $row['lon'],
			],
			'SiteGroup' => $this->group,
			'Maker'		=> $this->maker,
			'Data'		=> [
				'Create_at' => DateHelper::convertTimeToTZ($row['time'])
			]
		];

		return $data;
	}

	public function queryHistory($id, $startTimestamp, $endTimestamp){
		$startTZ 	= DateHelper::convertTimeToTZ($startTimestamp);
		$endTZ		= DateHelper::convertTimeToTZ($endTimestamp);
		$startDate  = date('Y-m-d%20H:i:s', strtotime($startTZ));
		$endDate  	= date('Y-m-d%20H:i:s', strtotime($endTZ));
		
		$url 		= sprintf($this->baseUrl.$this->deviceHistoryUrl, $this->token, $id, $startDate, $endDate);

		$response = $this->fetchRemote($url);
		if($response === null){ return []; }

		$feeds = [];
		$data = json_decode($response, true);

		foreach($data['entries'] as $row){
			$site = [
				'SiteName' 	=> '',	//doesnt no matter
				'LatLng'	=> '',	//doesnt no matter
				'SiteGroup' => $this->group,
				'Maker'		=> $this->maker,
				'Data'		=> [
					'Temperature' 	=> $row['s_t0'],
					'Humidity' 		=> $row['s_h0'],
					'Dust2_5' 		=> $row['s_d0'],
					'Create_at' 	=> DateHelper::convertTimeToTZ($row['time'])
				]
			];

			$feeds[] = $site;
		}

		return $this->convertFeedsToHistory($feeds);
	}
}