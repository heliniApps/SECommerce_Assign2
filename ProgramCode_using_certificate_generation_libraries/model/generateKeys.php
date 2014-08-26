<?php
	set_include_path(get_include_path() . PATH_SEPARATOR . '../phpseclib');
	
	include ('../phpseclib/Crypt/RSA.php');
	
	class generateKeys{

		const KEYFILE = '../data/certificate_data/key_info.xml';
		const CSRFILE = '../data/certificate_data/csr_info.xml';
		
		public function createPrivateKey($outFilePath){
			// The configuration array to generate a 1024 bit rsa private key.
			$config = array(
				"digest_alg" => "sha512",
				"private_key_bits" => 1024,
				"private_key_type" => OPENSSL_KEYTYPE_RSA,
			);    
			// Create the private and public key
			$privateKey = openssl_pkey_new($config);
			
			// Password to protect the stored private key.
			$password = "Pr%v@teK*yp@Ssw#rD";
			// Store the private key in a file.
			date_default_timezone_set('Australia/Melbourne');	
			//openssl_pkey_export_to_file($privateKey, $outFilePath, $password) or exit("Unable to save private key to file..");
			openssl_pkey_export_to_file($privateKey, $outFilePath) or exit("Unable to save private key to file..");
			
			// Show any errors that occurred.
			while(($e = openssl_error_string()) !== false){
				echo $e . "\n";
			}

			return $privateKey;
		}
		
		private function createCSR($privateKey, $outFilePath){
			// Array with web site owner information.			
			$dn = array(
				"countryName" => "AU",
				"stateOrProvinceName" => "Victoria",
				"localityName" => "Carlton",
				"organizationName" => "Simth and Sams",
				"organizationalUnitName" => "PHP Documentation Team",
				"commonName" => "localhost",
				"emailAddress" => "wez@example.com",
			);
			
			// Generate the certificate signing request
			$csr = openssl_csr_new($dn, $privateKey);
			
			// Export csr to a file.
			date_default_timezone_set('Australia/Melbourne');
			openssl_csr_export_to_file($csr, 
									$outFilePath) or exit("Unable to save CSR to file..");
			
			// Show any errors that occurred here
			while (($e = openssl_error_string()) !== false) {
				echo $e . "\n";
			}
			
			return $csr;
		}
		
		/**
			Generates the private key and the certificate signing request, by calling necessary functions.
			Then saves the file locations to the data file.
		*/
		public function generateKeyAndCSR(){
			$pkPath = 'user_keys/private/private_' . date("Y-m-d_H-i") . '.key';
			$csrPath = 'user_keys/csr/SandS_' . date("Y-m-d_H-i") . '.csr';
			$privateKey = $this->createPrivateKey($pkPath);
			$csr = $this->createCSR($privateKey, $csrPath);
			$this->writeKeyLocationToFile($pkPath, $csrPath);
		}
		
		/**
			Saves the private key and csr file locations, to the data file.
		*/
		private function writeKeyLocationToFile($pkPath, $csrPath){
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->load(self::KEYFILE);
			
			$keys = $doc->documentElement;
			$newKey = $doc->createElement('key');
			$keys->appendChild($newKey);
			
			$new_id = uniqid();
			$newKey->setAttribute('id', $new_id);
			
			$new_pk_node = $doc->createElement('private_key');
			$new_pk_node_text = $doc->createTextNode($pkPath);
			$new_pk_node->appendChild($new_pk_node_text);
			$newKey->appendChild($new_pk_node);
			
			$new_csr_node = $doc->createElement('csr_file');
			$new_csr_node_text = $doc->createTextNode($csrPath);
			$new_csr_node->appendChild($new_csr_node_text);
			$newKey->appendChild($new_csr_node);
			
			$new_validity_node = $doc->createElement('validity');
			$new_validity_node_text = $doc->createTextNode('valid');
			$new_validity_node->appendChild($new_validity_node_text);
			$newKey->appendChild($new_validity_node);		
			
			$new_created_node = $doc->createElement('created_date');
			$new_created_node_text = $doc->createTextNode(date("Y-m-d H:i"));
			$new_created_node->appendChild($new_created_node_text);
			$newKey->appendChild($new_created_node);
			
			$new_status_node = $doc->createElement('status');
			$new_status_node_text = $doc->createTextNode('not-signed');
			$new_status_node->appendChild($new_status_node_text);
			$newKey->appendChild($new_status_node);
			
			$doc->save(self::KEYFILE) or exit('Could not save the key information..');			
		}
		
		/**
			Retrieve information of all Keys, from the data file.
		*/
		public function getAllCSR(){
		
			$doc = new DOMDocument();
			$doc->load(self::KEYFILE);
			$keys = $doc->documentElement;
			
			$keyArray = array();
			$i = 0;
			
			foreach($keys->childNodes as $keyNode){				
				if($keyNode->nodeName == '#text'){
					continue;
				} else {
					$keyArray[$i] = $keyNode;
					$i++;
				}				
			}
			return $keyArray;
		}
		
		public function changeKeyCSRStatusById($id, $status){	
			$doc = new DOMDocument();
			$doc->load(self::KEYFILE);
			$keys = $doc->documentElement;
			
			// Variable to store csr file path.
			$csr_path = '';
			
			foreach($keys->childNodes as $keyNode){
				if($keyNode->nodeName == '#text'){
					continue;
				}				
				if($keyNode->getAttribute('id') == $id){
				
					foreach($keyNode->childNodes as $keyElement){						
						if($keyElement->nodeName == 'csr_file'){
							$csr_path = $keyElement->nodeValue;
						}
						if($keyElement->nodeName == 'status'){
							$keyElement->nodeValue = $status;
							break;
						}
					}
					break;
				}
			}			
			$doc->save(self::KEYFILE) or exit('Could not save the key information..');
			
			return $csr_path;
		}
		
		public function writeCSRToFile($keyId, $csrPath){
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->load(self::CSRFILE);
			
			$requests = $doc->documentElement;
			$newCSR = $doc->createElement('csr');
			$requests->appendChild($newCSR);
			
			$newId = uniqid();
			$newCSR->setAttribute('id', $newId);
			
			$new_keyId_node = $doc->createElement('key_id');
			$new_keyId_node_text = $doc->createTextNode($keyId);
			$new_keyId_node->appendChild($new_keyId_node_text);
			$newCSR->appendChild($new_keyId_node);
			
			$new_path_node = $doc->createElement('csr_file');
			$new_path_node_text = $doc->createTextNode($csrPath);
			$new_path_node->appendChild($new_path_node_text);
			$newCSR->appendChild($new_path_node);
			
			$new_created_node = $doc->createElement('created_date');
			$new_created_node_text = $doc->createTextNode(date("Y-m-d H:i"));
			$new_created_node->appendChild($new_created_node_text);
			$newCSR->appendChild($new_created_node);
			
			$new_status_node = $doc->createElement('status');
			$new_status_node_text = $doc->createTextNode('not-signed');
			$new_status_node->appendChild($new_status_node_text);
			$newCSR->appendChild($new_status_node);

			$doc->save(self::CSRFILE) or exit('Could not save the CSR information..');
		}
		
		public function pkeyEncDecryp(){
		
			// Array with web site owner information.			
			$dn = array(
				"countryName" => "AU",
				"stateOrProvinceName" => "Victoria",
				"localityName" => "Carlton",
				"organizationName" => "Simth and Sams",
				"organizationalUnitName" => "PHP Documentation Team",
				"commonName" => "SandS.com.au",
				"emailAddress" => "SandS@example.com",
			);
			
			$pubKeyFile = "../mykey.pub";
			$fr = fopen($pubKeyFile, "r") or exit ("Cannot open file.");
			$pubKeyData = fread($fr, filesize($pubKeyFile));
			fclose($fr);
			
			$fw = fopen("../CSR.csr", "w");
			fwrite($fw, print_r($dn, true) . "\n");
			fwrite($fw, $pubKeyData);
			fclose($fw);
			
			$privateKey = openssl_pkey_get_private("file://user_keys/mykey.pem");
			
			$csrFile = "../CSR.csr";
			$frCSR = fopen($csrFile, "r") or exit ("Cannot open file.");
			$CSRcontent =fread($frCSR, filesize($csrFile));
			fclose($frCSR);
			
			openssl_pkey_export($privateKey, $pkstr);
			//$iv = substr($pkstr, 16, 16);
			//openssl_private_encrypt($CSRcontent, $encdata, $pkstr);
			//$encdata = openssl_encrypt($CSRcontent, 'aes-256-cbc', $pkstr, true, $iv);
			//echo $encdata;
			
			//$iv2 = substr($pubKeyData, 16, 16);
			//$decryptData = openssl_decrypt($encdata, 'aes-256-cbc', $pkstr, true, $iv);
			//echo "\n\n" . $decryptData;
		}		
	}
	
?>