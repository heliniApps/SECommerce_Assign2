<?php
	include_once ('generateKeys.php');
	
	class CAFunctions{
	
		const CRTFILE = '../data/certificate_data/ca_crt_info.xml';
		const CSRFILE = '../data/certificate_data/csr_info.xml';		
		
		public function generateCAKeyAndCertificate(){
			date_default_timezone_set('Australia/Melbourne');		
			$pkPath = 'user_keys/ca_private/private_' . date("Y-m-d_H-i") . '.key';
			$csrPath = 'user_keys/ca_csr/CA_' . date("Y-m-d_H-i") . '.csr';
			$crtPath = 'user_keys/ca_crt/CA_' . date("Y-m-d_H-i") . '.crt';
			
			$keyGenerator = new generateKeys;
			
			$privateKey = $keyGenerator->createPrivateKey($pkPath);
			$csr = $this->createCACSR($privateKey, $csrPath);
			$this->selfSignCACSR($csr, $privateKey, $crtPath);
			
			$this->writeCACertificateInfo($pkPath, $csrPath, $crtPath);
		}
		
		public function getAllCACSR(){
		
			$doc = new DOMDocument();
			$doc->load(self::CSRFILE);
			$keys = $doc->documentElement;
			
			$csrArray = array();
			$i = 0;
			
			foreach($keys->childNodes as $keyNode){				
				if($keyNode->nodeName == '#text'){
					continue;
				} else {
					$csrArray[$i] = $keyNode;
					$i++;
				}				
			}
			return $csrArray;
		}
		
		public function signClientCSR($csrPath, $clientCRTPath){
			// Read CA certificate and private key path from file.
			$certPathArr = $this->readCACertificateInfo();
			
			$caCertPath = 'file://' . $certPathArr['crt_file'];
			//$privateKey = array('file://' . $certPathArr['key_file'], "Pr%v@teK*yp@Ssw#rD");
			$privateKey = array('file://' . $certPathArr['key_file'], "Pr%v@teK*yp@Ssw#rD");			
			$csrPath = 'file://' . $csrPath;
			
			// Create and sign certificate for client, valid for 1 year.
			$clientCert = openssl_csr_sign($csrPath, $caCertPath, $privateKey, 365);			
			
			date_default_timezone_set('Australia/Melbourne');
			openssl_x509_export_to_file($clientCert, $clientCRTPath) or exit("Unable to save Client certificate.");
			
			// Show any errors that occurred here
			while (($e = openssl_error_string()) !== false) {
				echo $e . "\n";
			}
		}		
		
		public function changeCSRStatusById($id, $status){		
			$doc = new DOMDocument();
			$doc->load(self::CSRFILE);
			$requests = $doc->documentElement;
			
			// Variable to store csr file path.
			$csr_path = '';
			
			foreach($requests->childNodes as $keyNode){			
				if($keyNode->nodeName == '#text'){
					continue;
				}				
				if($keyNode->getAttribute('id') == trim($id)){				
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
			$doc->save(self::CSRFILE) or exit('Could not save the csr information..');
			
			return $csr_path;
		}
		
		private function createCACSR($privateKey, $outFilePath){
			// Array with CA information.			
			$dn = array(
				"countryName" => "AU",
				"stateOrProvinceName" => "Victoria",
				"localityName" => "Melbourne",
				"organizationName" => "Farefield Company",
				"organizationalUnitName" => "PHP Documentation Team",
				"commonName" => "farefield.com.au",
				"emailAddress" => "farefield@example.com",
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
		
		private function selfSignCACSR($csr, $privateKey, $crtPath){
			// Create the self signed certificate of the CA, to be valid for 5 years.
			$caCert = openssl_csr_sign($csr, null, $privateKey, 1825);
			openssl_x509_export_to_file($caCert, $crtPath) or exit("Unable to save CA certificate.");
			
			// Show any errors that occurred here
			while (($e = openssl_error_string()) !== false) {
				echo $e . "\n";
			}
		}
		
		private function writeCACertificateInfo($pkPath, $csrPath, $crtPath){
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->load(self::CRTFILE);
			
			$certificates = $doc->documentElement;
			$newCRT = $doc->createElement('certificate');
			$certificates->appendChild($newCRT);
			
			$newId = uniqid();
			$newCRT->setAttribute('id', $newId);
			
			$new_pkey_node = $doc->createElement('key_file');
			$new_pkey_node_text = $doc->createTextNode($pkPath);
			$new_pkey_node->appendChild($new_pkey_node_text);
			$newCRT->appendChild($new_pkey_node);
			
			$new_csrpath_node = $doc->createElement('csr_file');
			$new_csrpath_node_text = $doc->createTextNode($csrPath);
			$new_csrpath_node->appendChild($new_csrpath_node_text);
			$newCRT->appendChild($new_csrpath_node);
			
			$new_crtpath_node = $doc->createElement('crt_file');
			$new_crtpath_node_text = $doc->createTextNode($crtPath);
			$new_crtpath_node->appendChild($new_crtpath_node_text);
			$newCRT->appendChild($new_crtpath_node);
			
			$new_created_node = $doc->createElement('created_date');
			$new_created_node_text = $doc->createTextNode(date("Y-m-d H:i"));
			$new_created_node->appendChild($new_created_node_text);
			$newCRT->appendChild($new_created_node);
			
			$doc->save(self::CRTFILE) or exit('Could not save the Certificate information..');
		}

		private function readCACertificateInfo(){
			$doc = new DOMDocument();
			$doc->load(self::CRTFILE);
			$certificates = $doc->documentElement;
			
			// Array to hold private key and certificate paths.
			$crtPaths = array();
			
			foreach($certificates->childNodes as $certificate){
				if($certificate->nodeName == '#text'){
					continue;
				}
				foreach($certificate->childNodes as $crtElement){
					if($crtElement->nodeName == 'key_file'){
						$crtPaths['key_file'] = $crtElement->nodeValue;
					} else if($crtElement->nodeName == 'crt_file'){
						$crtPaths['crt_file'] = $crtElement->nodeValue;
					}
				}
			}
			return $crtPaths;
		}
		
		private function readFile($filePath){
			$fp = fopen($filePath, "r"); 
			$fileData = fread($fp, 8192); 
			fclose($fp);
			
			return $fileData;
		}
	}
	
?>