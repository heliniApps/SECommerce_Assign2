<?php
	include_once '../model/generateKeys.php';
	include_once '../model/CA_functions.php';
	
	// Instanciate class
	$caOperation = new CAFunctions;
	$keyGenerator = new generateKeys;
	
	if(!isset($_GET["process"])) {
		// Create private/public key pair.
		$caOperation->generateCAKeyAndCertificate();
		// Redirect to the display certificates page.
		header('Location:../view/CA_display_csr.php');
	
	} else if ((isset($_GET["process"])) && ($_GET["process"] == "sign")){
		$csrRecordId = $_GET["id"];
		$keyRecordId = $_GET["keyId"];
		
		if(!isset($keyRecordId) || !isset($csrRecordId)){
			header('Location:../view/csr_error.php');
			
		} else {
			// Get client CSR path and change the CA record status to 'signed'.		
			$clientCSRPath = $caOperation->changeCSRStatusById($csrRecordId, "Signed");			
			// Change the client record status to 'signed'.
			$keyGenerator->changeKeyCSRStatusById($keyRecordId, "Signed");
			
			// Create and sign the client certificate.
			$certPath = 'user_keys/crt/SandS_' . date("Y-m-d_H-i") . '.crt';
			$caOperation->signClientCSR($clientCSRPath, $certPath);
			
			header('Location:../view/CA_display_csr.php');
		}		
	}
	
?>