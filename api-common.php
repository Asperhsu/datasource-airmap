<?php
function show_550(){
	header("HTTP/1.0 550 Permission Denied");
	echo "550 Permission Denied";
	exit;
}

function isAuthorized(){
	$token = isset($_GET['token']) ? $_GET['token'] : '';
	if( !strlen($token) ){ return false; }
	$tokens = env('ACCEPT_TOKENS') ?: '';
	$tokens = explode(",", $tokens);

	return in_array($token, $tokens);
}

function env($index){
	return @getenv($index, true) ?: getenv($index);
}