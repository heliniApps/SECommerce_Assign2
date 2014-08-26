<?php 
session_start();
include '../model/member.php';
include '../model/session.php';

if(!isset($_SESSION['expire']))
	$_SESSION['expire'] = SESSION::setExpiredTime();

if(!isset($_SESSION['admin'])){
	$user_id = $_SESSION['USER_ID'];
	$key = session_id();
	
	if(!isset($user_id)){
		header('Location:../');
	}
	
	$status = MEMBER::checkSession($user_id, $key);
	$welcome_message = "Welcome To Home Page!";
	$mode = "USER";
}else{
	$status = "ALLOWED";
	$welcome_message  = "Welcome Admin!";
	$mode = "ADMIN";
}



$sessionTimeStatus =  SESSION::checkSessionTime($_SESSION['expire']);

if($status == "NOT_ALLOWED" || $sessionTimeStatus == "EXPIRED")
	header('Location:../controller/logout.php');
else
	$_SESSION['expire'] = SESSION::setExpiredTime();

?>
<html>
<body>

	<h1><?php echo $welcome_message; ?></h1>
	<h2>How can we help you?</h2>
	<ul>
		<?php if($mode == "USER"){?>
		<li><a href="./cc_form.php">Make Transaction</a></li>
		<?php }?>
		<li><a href="./log_history.php">View Transaction history</a></li>
		<?php if($mode =="ADMIN"){?>
		<li><a href="./session_history.php">View Session History</a></li>
		<li><a href="./display_generated_keys.php">View Certificate Information</a></li>
		<?php } ?>
	</ul>
	<a href="../controller/logout.php">Logout</a>
</body>
</html>