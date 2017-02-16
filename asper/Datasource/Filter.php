<?php
namespace Asper\Datasource;

class Filter {
	
	/**
	 * time is over $greaterThanMins from now
	 * @param  string  $timeString      time string
	 * @param  integer $greaterThanMins max minutes
	 * @return boolean                   true for greater
	 */
	public static function timeGreaterThan($timeString, $baseTime=null, $greaterThanMins=30){
		if( !strlen($timeString) ){ return false; }

		$time = strtotime($timeString);
		$gap = $greaterThanMins * 60;	//turn into secs

		$baseTime = is_null($baseTime) ? time() : strtotime($baseTime);

		return (bool)( ($baseTime - $time) > $gap );
	}

	public static function inTimeRange($timeString, $start, $end){
		if( !strlen($timeString) || !strlen($start) || !strlen($end) ){ return false; }

		$time 	= strtotime($timeString);
		$start	= strtotime($start);
		$end 	= strtotime($end);

		return $time >= $start && $time <= $end;
	}

}