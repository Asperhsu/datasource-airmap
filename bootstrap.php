<?php
require("vendor/autoload.php");
date_default_timezone_set('Asia/Taipei');

include("env.php");
if( isset($env) && count($env) ){
	foreach($env as $key => $value){
		putenv($key . "=" . $value);
	}
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}