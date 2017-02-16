<?php
require("vendor/autoload.php");
date_default_timezone_set('Asia/Taipei');

putenv("APP_PATH=".__DIR__);

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}