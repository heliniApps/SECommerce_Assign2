<?php
// Forcing the use of HTTPS, if the client is trying to use HTTP. 
if($_SERVER["HTTPS"] != "on")
{
	header('Location: https://localhost/sec_ass02/index.php');
}
	
session_start();
if(isset($_SESSION['USER_IP']))
	header('Location:./view/home.php');
?>

<html>
	<head>
		<title>SEC Ass 01</title>
		<script type="text/javascript">
			function validate(){
				var username = document.getElementById('user').value;
				var password = document.getElementById('pass').value;
				var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/;
				var err ="";
				
				if(username == "")
					err += "- Email is required\n";
				else if(!pattern.test(username))
					err += "- Email must be in correct format\n";
				
				if(password == "")
					err += "- Password is required\n";
					
				if(err != ""){
					alert(err);
					return false;
				}
				return true;
				
			}
		</script>
	</head>
	<body>
	<form method="post" action="./controller/process.php">
		<table>
			<tr>
				<td>Email</td>
				<td>:</td>
				<td><input type="text" name="username" id="user"/></td>
				<?php 
					if(isset($_GET['user']) && $_GET['user'] == 1){ 
						echo '<td style="color:red">Email is required</td>';
					}else if(isset($_GET['user']) && $_GET['user'] == 2) 
						echo '<td style="color:red">Email must be in correct format</td>';
				?>
			</tr>
			
			<tr>
				<td>Password</td>
				<td>:</td>
				<td><input type="password" name="password" id="pass"/></td>
				<?php if(isset($_GET['pass']) && $_GET['pass'] == 1) echo '<td style="color:red">Password is required</td>';?>
				<?php if(isset($_GET['member']) && $_GET['member'] == 1) echo '<td style="color:red">Incorrect combination of Email and Password</td>';?>
			</tr>
			
			<tr>
				<td></td>
				<td></td>
				<td></td>
			</tr>
			
			<tr>
				<td><a href="./view/register.php">Register</a></td>
				<td></td>
				<td><input type="submit" value="Submit"/>	
			</tr>
		</table>
	</form>
	</body>
</html>