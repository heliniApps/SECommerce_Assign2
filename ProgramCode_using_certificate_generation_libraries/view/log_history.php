<?php
session_start();
include '../model/member.php';
include '../model/card.php';
include '../model/log.php';
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
	$mode = "USER";
	$cc_num= CARD::getValueByIdWithNodeName($user_id, "card_number");
	$idArray = LOG::findAllLogByNum($cc_num);
	$heading = "Here are your transaction history:";
}else{
	$status = "ALLOWED";
	$mode = "ADMIN";
	$heading = "Here are all transaction logs:";
}

$sessionTimeStatus =  SESSION::checkSessionTime($_SESSION['expire']);

if($status == "NOT_ALLOWED" || $sessionTimeStatus == "EXPIRED")
	header('Location:../controller/logout.php');
else
	$_SESSION['expire'] = SESSION::setExpiredTime();
	
?>
<html>
	<body>
		<h1><?php echo $heading;?></h1>
		<a href="./home.php">Back</a>

			<?php
				if($mode == "USER")
					include './user_table.php'; 
				else if($mode == "ADMIN")
					include './admin_table.php';
			?>
	</body>	
</html>