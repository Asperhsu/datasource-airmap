<?php
namespace Asper\Datasource;

class Filter {
	
	/**
	 * time is over $greaterThanMins from now
	 * @param  string  $timeString      time string
	 * @param  integer $greaterThanMins max minutes
	 * @return boolean                   true for greater
	 */
	public static function timeGreaterThan($timeString='', $greaterThanMins=30){
		if( !strlen($timeString) ){ return false; }

		$time = strtotime($timeString);
		$gap = $greaterThanMins * 60;	//turn into secs

		return (bool)( (time() - $time) > $gap );
	}

}