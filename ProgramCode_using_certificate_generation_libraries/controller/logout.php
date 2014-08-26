<?php
include '../model/log.php';
session_start();

if(!isset($_SESSION['admin'])){
	date_default_timezone_set('Australia/Victoria');
	$date = date('d-M-Y, H:i:s');
	LOG::appendSession($_SESSION['sessionID'] , "logout_time", $date);
}
session_destroy();
header('Location:../view/session_expired.htm');
?>