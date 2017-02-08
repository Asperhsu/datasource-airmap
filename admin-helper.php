<?php
require("bootstrap.php");

function showAlert($class='info', $text=''){
	$_SESSION['alert'] = [
		'class' => $class,
		'text'  => $text,
	];
}

function permissionAccept($accept){
	if( !is_array($accept) ){
		$accept = [$accept];
	}

	return in_array($_SESSION['loginPermission'], $accept);
}