<?php
	include_once '../model/generateKeys.php';
	
	// Instanciate class
	$keyGenerator = new generateKeys;
	
	if(!isset($_GET["process"])) {
		// Create private/public key pair.
		$keyGenerator->generateKeyAndCSR();
		// Redirect to the display certificates page.
		header('Location:../view/display_generated_keys.php');
	
	} else if ((isset($_GET["process"])) && ($_GET["process"] == "sign")){
		$keyRecordId = $_GET["id"];
		if(!isset($keyRecordId)){
			header('Location:../view/csr_error.php');
			
		} else {			
			$csrPath = $keyGenerator->changeKeyCSRStatusById($keyRecordId, 'Request Sent');
			$keyGenerator->writeCSRToFile($keyRecordId, $csrPath);
			
			header('Location:../view/display_generated_keys.php');
		}		
	}
	
?>