<html>
	<h2>CA Certification</h2>
	
	<?php

		include_once ('CACertification.php');

		// Instanciate class
		$CAFunctions = new CAFunctions;

		$config = array(
						"digest_alg" => "sha512",
						"private_key_bits" => 1024,
						"private_key_type" => OPENSSL_KEYTYPE_RSA,
					);
					
		/* Creating owner private key */
		$ownerPrivateKey = openssl_pkey_new($config);

		// Save the private key
		openssl_pkey_export_to_file($ownerPrivateKey, "private.key");
		openssl_pkey_export($ownerPrivateKey, $ownerPkStr);
		// Get owner public key
		$pubKey = openssl_pkey_get_details($ownerPrivateKey);
		$ownerPublicKey = $pubKey['key'];
		file_put_contents("public.key", $ownerPublicKey);

		/* Creating CA private key */
		$CAPrivateKey = openssl_pkey_new($config);

		// Save the private key
		openssl_pkey_export_to_file($CAPrivateKey, "private.key");
		openssl_pkey_export($CAPrivateKey, $CAPkStr);
		// Get owner public key
		$pubKey = openssl_pkey_get_details($CAPrivateKey);
		$CAPublicKey = $pubKey['key'];
		file_put_contents("public.key", $CAPublicKey);

		// Owner creates his request
		$ownerArr = $CAFunctions->ownerEncryption($ownerPrivateKey, $ownerPublicKey);
		// CA verifies the site Owner
		$ownerStatus = $CAFunctions->CADecryption($ownerArr, $ownerPublicKey);

		if (status == false){
			echo "Signature verification unsuccessful.\n\n";
			return;
		} else {
			//	CA signs the request.
			$CAArr = $CAFunctions->CASigning($CAPrivateKey, $ownerArr, $CAPublicKey);
			// Owner recieves the certificate
			$CAFunctions->ownerVerifyCertificate($CAArr, $CAPublicKey);
		}

	?>
</html>