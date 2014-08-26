<?php
	include '../model/member.php';
	$name = $_POST['fullname'];
	$age = $_POST['age'];
	$address = $_POST['address'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zip = $_POST['zip'];
	$country = $_POST['country'];
	$email = $_POST['email'];
	$pass = $_POST['password'];
	
	$err = "";
	$pattern = '/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/';
	$num_pattern = "/^\d+$/";
	
	if($name == '')
		$err .= ($err == '' ? 'name=1' : '&name=1');
	if($age == "")
		$err .= ($err == '' ? 'age=1' : '&age=1');
	else if(!preg_match($num_pattern, $age))
		$err .= ($err == '' ? 'age=2' : '&age=2');
	if($address == "")
		$err .= ($err == '' ? 'addr=1' : '&addr=1');
	if($city == "")
		$err .= ($err == '' ? 'city=1' : '&city=1');
	if($state == "")
		$err .= ($err == '' ? 'state=1' : '&state=1');
	if($zip == "")
		$err .= ($err == '' ? 'zip=1' : '&zip=1');
	else if(!preg_match($num_pattern, $zip))
		$err .= ($err == '' ? 'zip=2' : '&zip=2');
	if($country == "")
		$err .= ($err == '' ? 'country=1' : '&country=1');
	if($email == "")
		$err .= ($err == '' ? 'email=1' : '&email=1');
	else if(!preg_match($pattern, $email))
		$err .= ($err == '' ? 'email=2' : '&email=2');
	if($pass == "")
		$err .= ($err == '' ? 'password=1' : '&password=1');
	else if(strlen($pass) < 6)
		$err .= ($err == '' ? 'password=2' : '&password=2');
	if($err != "")
		header('Location:../view/register.php?'.$err);
	else{
		MEMBER::registerNewMember($name, $age, $address, $city, $state, $zip, $country, $email, $pass);
		 header('Location:../view/regis_success.htm');
	}
	
?>