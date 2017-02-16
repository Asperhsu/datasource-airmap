<?php
namespace Asper;

class DateHelper {
	
	static public function convertTimeToTZ($string){
		if(is_numeric($string)){
			$dateTime = new \DateTime();
			$dateTime->setTimestamp($string);
		}else{
			$dateTime = new \DateTime($string);			
		}

		return str_replace('+00:00', 'Z', gmdate('c', $dateTime->getTimestamp() ));
	}

	static public function calcDateRange($start, $end){
		$queryDates = [];
		$startDate 	= date('Y-m-d', is_numeric($start) ? $start : strtotime($start));
		$endDate 	= date('Y-m-d', is_numeric($end)   ? $end   : strtotime($end));

		if($startDate == $endDate){
			$queryDates[] = $startDate;
		}else{
			$daysBetween = ceil(abs(strtotime($endDate) - strtotime($startDate)) / 86400);
			for($i=0; $i<=$daysBetween; $i++){
				$queryDates[] = date('Y-m-d', strtotime($startDate) + 86400*$i);
			}
		}

		return $queryDates;
	}

}