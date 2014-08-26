<?php
session_start();
include '../model/member.php';
include '../model/log.php';

$username = $_POST['username'];
$password = $_POST['password'];
$user_session = session_id();
$err = "";
$pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/';

if($username == "")
	$err .= ($err == '' ? 'user=1' : '&user=1');
else if(!preg_match($pattern, $username))
	$err .= ($err == '' ? 'user=2' : '&user=2');
if($password == "")
	$err .= ($err == '' ? 'pass=1' : '&pass=1');

if($err != "" && $username != "admin")
	header('Location:../?' .$err);
else{
	if($username == "admin" && $password == "admin"){
		$_SESSION['admin'] = "ON";
		$login = "ALLOWED";
	}else{
		$login = MEMBER::findMemberByEmailPassword($username, $password, $user_session);
	}

	if($login == "ALLOWED"){
		date_default_timezone_set('Australia/Victoria');
		if(!isset($_SESSION['sessionID']) && $username != "admin"){
			$_SESSION['sessionID'] = uniqid();
			$date = date('d-M-Y, H:i:s');
			LOG::writeNewSessionLog($_SESSION['sessionID'], $_SESSION['USER_ID'], $date, $_SESSION['USER_IP']);		
		}
		header('Location:../view/home.php');
	}
	else{
		$err = "member=1";
		header('Location:../?' .$err);
	}
}


?>