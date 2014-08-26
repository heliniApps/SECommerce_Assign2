<?php
session_start();
include '../model/member.php';
include '../model/log.php';

if(!isset($_SESSION['expire']))
	$_SESSION['expire'] = SESSION::setExpiredTime();
	
$sessionidArray = LOG::getAllSessionId();
?>

<html>
		<body>
			<h1>User Session History:</h1>
			<a href="./home.php">Back</a>
			<table border="1">
				<tr>
					<th>Username</th>
					<th>Login Time</th>
					<th>Ip Address</th>
					<th>Logout Time</th>
				</tr>
				<?php 
					for($i = 0; $i < count($sessionidArray); $i++){
						$sessionid = $sessionidArray[$i];
						$userid = LOG::findSessionByIdWithNodeName($sessionid, "user_id");
						$username = MEMBER::getValueByIdOfNodeName($userid, "email");
						$loginTime = LOG::findSessionByIdWithNodeName($sessionid, "login_time");
						$ip = LOG::findSessionByIdWithNodeName($sessionid, "user_ip");
						$logout = LOG::findSessionByIdWithNodeName($sessionid, "logout_time");
						
						echo "<tr>";
						echo "<td>" .$username. "</td>";
						echo "<td>" .$loginTime ."</td>";
						echo "<td>" .$ip. "</td>";
						echo "<td>" .$logout. "</td>";
						echo "</tr>";
					}
				?>
			</table>
		</body
</html>