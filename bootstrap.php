<?php
require("vendor/autoload.php");
include("env.php");

date_default_timezone_set('Asia/Taipei');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}