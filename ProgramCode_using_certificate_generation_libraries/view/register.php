
<?php
// Forcing the use of HTTPS, if the client is trying to use HTTP. 
if($_SERVER["HTTPS"] != "on")
{
    header("Location: https://localhost/sec_ass02/view/register.php");	
} 
// Setting the session.
session_start();
$ses_id = session_id();

// Setting the session ID to a cookie.
setcookie("userSession", $ses_id, time()+3600);
?>
<html>
	<head>
		<title>SEC Ass 01</title>
		<script type="text/javascript">
			function validate(){
				var err = "";
				var name = document.getElementById('fullname').value;
				var age = Number(document.getElementById('age').value);
				var address = document.getElementById('address').value;
				var city = document.getElementById('city').value;
				var state = document.getElementById('state').value;
				var zip = document.getElementById('zip').value;
				var country = document.getElementById('country').value;
				var email = document.getElementById('email').value;
				var password = document.getElementById('password').value;
				
				var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z.]{2,5}$/;
				var num = /^\d+$/;
				//var regExp = new RegExp(pattern);
				if(name == "")
					err += "- Name must not be empty\n";
				if(age == "")
					err += "- Age must not be empty\n";
				else if(!num.test(age))
					err += "- Age must be numbers only\n	";
				if(address == "")
					err += "- Address must not be empty\n";
				if(city == "")
					err += "- City must not be empty\n";
				if(state == "")
					err += "- State must not be empty\n";
				if(zip == "")
					err += "- Zip code must not be empty\n";
				else if(!num.test(zip))
					err += "- Zip code must be numbers only\n";
				if(country == "")
					err += "- Country must not be empty\n";
				if(email == "")
					err += "- Email must not be empty\n";
				else if(!pattern.test(email))
					err+= "- Email must be in correct format\n";
				if(password == "")
					err += "- Password must not be empty\n";
				else if(password.length < 6)
					err += "- Password must be 6 character minimum\n";
				
				if(err != ""){
					alert(err);
					return false;
				}
				return true;
				
			}
		</script>
	</head>
	<body>
	<form name="registerForm" method="post" action="../controller/register_member.php" onSubmit="return validate();">
		<table>
			<tr>
				<td>Full Name</td>
				<td>:</td>
				<td><input type="text" name="fullname" id="fullname"/></td>
				<?php if(isset($_GET['name']) && $_GET['name'] == 1) echo '<td style="color:red">Name is required</td>';?>
			</tr>
			<tr>
				<td>Age</td>
				<td>:</td>
				<td><input type="text" name="age" id="age"/></td>
				<?php 
					if(isset($_GET['age']) && $_GET['age'] == 1){ 
						echo '<td style="color:red">Age is required</td>';
					}else if(isset($_GET['age']) && $_GET['age'] == 2) 
						echo '<td style="color:red">Age must be numbers only</td>';
				?>
			</tr>
			
			<tr>
				<td>Address</td>
				<td>:</td>
				<td><input type="text" name="address" id="address"/></td>
				<?php if(isset($_GET['addr']) && $_GET['addr'] == 1) echo '<td style="color:red">Address is required</td>';?>
			</tr>
			
			<tr>
				<td>City</td>
				<td>:</td>
				<td><input type="text" name="city" id="city"/></td>
				<?php if(isset($_GET['city']) && $_GET['city'] == 1) echo '<td style="color:red">City is required</td>';?>
			</tr>
			
			<tr>
				<td>State</td>
				<td>:</td>
				<td><input type="text" name="state" id="state"/></td>
				<?php if(isset($_GET['state']) && $_GET['state'] == 1) echo '<td style="color:red">State is required</td>';?>
			</tr>
			
			<tr>
				<td>Zip Code</td>
				<td>:</td>
				<td><input type="text" name="zip"id="zip"/></td>
				<?php 
					if(isset($_GET['zip']) && $_GET['zip'] == 1){ 
						echo '<td style="color:red">Zip Code is required</td>';
					}else if(isset($_GET['zip']) && $_GET['zip'] == 2) 
						echo '<td style="color:red">Zip Code must be numbers only</td>';
				?>
			</tr>
			
			<tr>
				<td>Country</td>
				<td>:</td>
				<td><input type="text" name="country" id="country"/></td>
				<?php if(isset($_GET['country']) && $_GET['country'] == 1) echo '<td style="color:red">Country is required</td>';?>
			</tr>
			
			<tr>
				<td>Email</td>
				<td>:</td>
				<td><input type="text" name="email" id="email"/></td>
				<?php 
					if(isset($_GET['email']) && $_GET['email'] == 1){ 
						echo '<td style="color:red">Email is required</td>';
					}else if(isset($_GET['email']) && $_GET['email'] == 2) 
						echo '<td style="color:red">Email must be in correct format</td>';
				?>
			</tr>
			
			<tr>
				<td>Password</td>
				<td>:</td>
				<td><input type="password" name="password" id="password"/></td>
				<?php 
					if(isset($_GET['password']) && $_GET['password'] == 1){ 
						echo '<td style="color:red">Password is required</td>';
					}else if(isset($_GET['password']) && $_GET['password'] == 2) 
						echo '<td style="color:red">Password must be 6 character minimum</td>';
				?>
			</tr>
			
			<tr>
				<td><a href="../">Back</a></td>
				<td></td>
				<td><input type="submit" value="Submit"/>	
			</tr>
		</table>
	</form>
	</body>
</html>