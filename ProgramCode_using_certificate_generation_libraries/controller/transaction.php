<?php
	include '../model/card.php';
	include '../model/log.php';
	include '../model/member.php';

	session_start();

	$user_id = $_SESSION['USER_ID'];
	$number = $_POST['cc_number'];
	$cvv = $_POST['cc_cvv'];
	$date = $_POST['cc_expDate'];
	$name = $_POST['cc_name'];
	$amount = $_POST['cc_amount'];
	$found = $_POST['card_found'];

	$num_pattern = "/^\d+$/";
	$err = "";

	if($number == "")
			$err .= ($err == '' ? 'num=1' : '&num=1');
	else if(!preg_match($num_pattern, $number))
		$err .= ($err == '' ? 'num=2' : '&num=2');
		
	if($cvv == "")
			$err .= ($err == '' ? 'cvv=1' : '&cvv=1');
	else if(!preg_match($num_pattern, $cvv))
		$err .= ($err == '' ? 'cvv=2' : '&cvv=2');
	else if(strlen($cvv) !=3 )
		$err .= ($err == '' ? 'cvv=3' : '&cvv=3');
		
	if($name == "")
			$err .= ($err == '' ? 'name=1' : '&name=1');

	if($amount == "")
			$err .= ($err == '' ? 'amount=1' : '&amount=1');		
	else if(!preg_match($num_pattern, $amount))
		$err .= ($err == '' ? 'amount=2' : '&amount=2');
		
	if($err != "")
		header('Location:../view/cc_form.php?'.$err);
	else{
		/* Checking whether the credit card is blacklisted.
		 * If so, redirects the user to the respective error page. */ 
		 $cardStatus = LOG::findLogByCCNumAndStatus($number);
		 
		if($cardStatus == 'CARD_DECLINED'){
			header('Location:../view/trans_blacklisted.php');
			return;
		}
		// Logging the user's credit card details. 
		if($found == "NOT FOUND")
			CARD::registerNewCard($user_id, $number, $cvv, $date, $name);

		// Retrieving transaction password.
		$transPass = MEMBER::getValueByIdOfNodeName($user_id, 'trans-pass');
		$decTransPass = fnDecrypt($transPass, $GLOBALS['passKey']);
			
		$data = array(
			'custid' => 's3223632',
			'password' => $decTransPass,
			'demo' => 'y',
			'action' => 'sale',
			'media' => 'cc',
			'cc' => $number,
			'exp' => $date,
			'amount' => $amount,
			'name' => $name
			);
			
		$submit_url = "http://goanna.cs.rmit.edu.au/~ronvs/TCLinkGateway/process.php";
		$curlChannel = curl_init($submit_url);
		curl_setopt($curlChannel, CURLOPT_POST, true);
		curl_setopt($curlChannel, CURLOPT_POSTFIELDS, $data);
		curl_setopt($curlChannel, CURLOPT_SSL_VERIFYPEER, 0);

		/*As referenced from http://www.jonasjohn.de/snippets/php/curl-example.htm */
		 
		 curl_setopt($curlChannel, CURLOPT_HEADER, 0);
		 
			// Should cURL return or print out the data? (true = return, false = print)
			curl_setopt($curlChannel, CURLOPT_RETURNTRANSFER, true);
		 
			// Timeout in seconds
			curl_setopt($curlChannel, CURLOPT_TIMEOUT, 10);
			
		/*End reference*/

		$result =  curl_exec($curlChannel);
		$result=unserialize($result);
		curl_close($curlChannel);
		date_default_timezone_set('Australia/Victoria');
		$date = date('d-M-Y, H:i:s');

		// Retrieving the status of result and decline type.
		$status = $result['status'];
		$declineType = $result['declinetype'];

		// Logging the transaction information.
		if($status == 'approved'){
			LOG::writeLog($result['transid'], $number, $amount, $date, $status);
			header('Location:../view/trans_success.php');
			
		}else if($status == 'baddata'){
			$message = $status ."; ". $result['errortype'] . " at ". $result['offenders'];
			LOG::writeLog(uniqid(), $number, $amount, $date, $message);
			header('Location:../view/trans_fail.php');
			
		}else if(($status == 'decline') && (($declineType == 'blacklist')||($declineType == 'fraud')||($declineType == 'velocity'))){
			$statusType = $status . "; " . $declineType;
			LOG::writeLog(uniqid(), $number, $amount, $date, $statusType);
			header('Location:../view/trans_blacklisted.php');
			
		}else {
			LOG::writeLog(uniqid(), $number, $amount, $date, $status);
			echo $status;
			print_r($result); 
		}
	}


?>