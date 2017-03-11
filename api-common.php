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
	return defined($index) ? constant($index) : false;
}

function fetchMyJson($method="GET", $id=null, $content=null){
	if( in_array($method, ["GET", "PUT"]) && is_null($id) ){
		return false;
	}

	$url = "https://api.myjson.com/bins";
	if( !is_null($id) ){ $url .= "/$id"; }

	$opts = [
		'http'=> [
			'method' => $method,
			'header'  => 'Content-type: application/json; charset=utf-8',
		]
	];

	if( in_array($method, ["PUT", "POST"]) && !is_null($content) ){
		$opts['http']['content'] = $content;
	}

	$context = stream_context_create($opts);
	return file_get_contents($url, false, $context);
}