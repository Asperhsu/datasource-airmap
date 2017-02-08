<?php
require("bootstrap.php");

use Asper\Datasource\Independent;
use Asper\Datasource\ProbeCube;

if( !isset($_SESSION['loginUser']) || !strlen($_SESSION['loginUser']) ){
	header("HTTP/1.0 550 Permission Denied");
}

$configType = strtolower($_POST['configType']);
switch($_GET['op']){
	case 'add':
		if( !permissionAccept(['operator', 'admin']) ){
			header("HTTP/1.0 550 Permission Denied");
		}

		
		$datasource = getDatasource($configType);
		$newConfig = collectField($configType);
		$datasource->addNewSite($newConfig);

		showAlert('success', 'add new ' . $configType . ' config success');
		header("location: /admin");
		break;
	case 'edit':
		if( !permissionAccept(['operator', 'admin']) ){
			header("HTTP/1.0 550 Permission Denied");
		}

		$datasource = getDatasource($configType);
		$newConfig = collectField($configType);

		$channelID = $_POST['key'];
		$datasource->updateSiteConfig($channelID, $newConfig);

		showAlert('success', 'edit ' . $configType . ' config success');
		header("location: /admin");
		break;
	case 'delete':
		if( !permissionAccept('admin') ){
			header("HTTP/1.0 550 Permission Denied");
		}

		$datasource = getDatasource($configType);
		$channelID = $_POST['key'];
		if(is_null($datasource)){ echo 'false'; exit; }

		echo $datasource->deleteSite($channelID) ? 'true' : 'false';
		break;
}

function getDatasource($configType){
	$datasource = null;
	$configType = strtolower($configType);
	if($configType == "independent"){ $datasource = new Independent(); }
	if($configType == "probecube")	{ $datasource = new ProbeCube(); }
	
	if(!isset($datasource)){ 
		showAlert('warning', 'system error, edit ' . $configType . 'config not success');
		header("location: /admin");
	}

	return $datasource;
}

function collectField($type){
	if($type == 'independent'){
		return [
			'Channel_id' 	=> $_POST['Channel_id'],
			'name' 			=> $_POST['name'],
			'Maker' 		=> $_POST['Maker'],
			'SiteGroup'		=> $_POST['Maker'],
			'active' 		=> $_POST['active'] == "true",
			'Option' 	=> [
				$_POST['option_Temperature']	=> 'Temperature',
				$_POST['option_Humidity'] 		=> 'Humidity',
				$_POST['option_Dust2_5'] 		=> 'Dust2_5',
			]
		];
	}

	if($type == 'probecube'){
		return [
			'Channel_id' 	=> $_POST['Channel_id'],
			'maker' 		=> $_POST['maker'],
			'active' 		=> $_POST['active'] == "true",
		];
	}

	return [];
}