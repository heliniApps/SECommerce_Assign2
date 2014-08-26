<?php
include '../model/card.php';
include '../model/member.php';
include '../model/session.php';
session_start();

if(!isset($_SESSION['expire']))
	$_SESSION['expire'] = SESSION::setExpiredTime();

$user_id = $_SESSION['USER_ID'];
$key = session_id();
$status = MEMBER::checkSession($user_id, $key);
$sessionTimeStatus =  SESSION::checkSessionTime($_SESSION['expire']);

if($status == "NOT_ALLOWED" || $sessionTimeStatus == "EXPIRED")
	header('Location:../controller/logout.php');
else
	$_SESSION['expire'] = SESSION::setExpiredTime();


$cardExist = CARD::getValueByIdWithNodeName($user_id, "user_id");
$disabled="";
$bgcolor = "";
if(isset($cardExist)){	
	$dataArray = CARD::getCardInfo($user_id);	
	
	$number = $dataArray['number'];
	$cvv = $dataArray["cvv"];
	$date = $dataArray["date"];
	$name = $dataArray["name"];	
	$found = "FOUND";
	$disabled = 'readonly="readonly"';
	$bgcolor = "style=' background-color: #cccccc'";
}else{
	$number = '';
	$cvv = '';
	$date = '';
	$name = '';
	$found = "NOT FOUND"; 
}
?>

<html>
	<head>
		<title>SEC Ass 01</title>
		<script type="text/javascript">
			function validate(){
				var err = "";
				var num = document.getElementById('num').value;
				var cvv = document.getElementById('cvv').value;
				var date = document.getElementById('date').value;
				var name = document.getElementById('name').value;
				var amount = document.getElementById('amount').value;
				var numPattern = /^\d+$/;
				
				if(num == "")
					err += "- Credit Card Number must not be empty\n";
				else if(!numPattern.test(num))
					err += "- Credit Card Number must be numbers only\n";
				if(cvv == "")
					err += "- CVV must not be empty\n";
				else if(!numPattern.test(cvv))
					err += "- CVV must be numbers only\n";
				else if(cvv.length != 3)
					err += "- CVV must be 3 numbers\n";
				if(date == "")
					err += "- Expiration date must not be empty\n";
				if(name == "")
					err += "- Name must not be empty\n";
				if(amount == "")
					err += "- Amount must not be empty\n";
				else if(!numPattern.test(amount))
					err += "- Amount must be numbers only";
				
				if(err != ""){
					alert(err);
					return false;
				}
				return true;
				
			}
		</script>
	</head>

	<body>
	<form method="POST" action="../controller/transaction.php" onSubmit="return validate()">
		<h1>First Time Here?</h1>
		<h2>We will store you credit card details securely in our server.</h2>
		<h2>So you don't need to fill-in credit card details each time you visit our page!</h2>
		<br /><br />
		<table>
			<tr>
				<td>Credit Card Number</td>
				<td>:</td>
				<td><input type="text" id="num" name="cc_number" value="<?php echo $number; ?>" <?php echo $disabled; echo $bgcolor; ?> />
				<?php 
					if(isset($_GET['num']) && $_GET['num'] == 1){ 
						echo '<td style="color:red">Credit Card Number is required</td>';
					}else if(isset($_GET['num']) && $_GET['num'] == 2) 
						echo '<td style="color:red">Credit Card Number must be numbers only</td>';
				?>
			</tr>
			<tr>
				<td>CVV</td>
				<td>:</td>
				<td><input type="text" id="cvv" name="cc_cvv" <?php echo $disabled; echo $bgcolor; ?> value="<?php echo $cvv; ?>"/>
			</tr>
			<tr>
				<td>Expiration Date</td>
				<td>:</td>
				<td>
					<?php if($found == 'NOT FOUND'){?>
					<select name="cc_expDate" id="date">
						<option value="0513">May 2013</option>
						<option value="0613">June 2013</option>
						<option value="0713">July 2013</option>
						<option value="0813">August 2013</option>
						<option value="0913">September 2013</option>
						<option value="1013">October 2013</option>
						<option value="1113">November 2013</option>
						<option value="1213">December 2013</option>
					</select>
					<?php }else{ ?>
					<input type="text" id="date" name="cc_expDate" <?php echo $disabled; echo $bgcolor; ?> value="<?php echo $date; ?>"/>
					<?php } ?>
				</td>
				
			</tr>
			<tr>
				<td>Name (As in card)</td>
				<td>:</td>
				<td><input type="text" id="name" name="cc_name" <?php echo $disabled; echo $bgcolor; ?> value="<?php echo $name; ?>"/>
			</tr>
			<tr>
				<td>Amount (in $AUD)</td>
				<td>:</td>
				<td><input type="text" id="amount" name="cc_amount"/>
			</tr>
			<tr>
				<td><a href="./home.php">Back</a></td>
				<td><input type="hidden" value="<?php echo $found; ?>" name="card_found"/></td>
				<td><input type="submit" value="Submit" />
			</tr>
		</table>
	</form>
	</body>
</html>