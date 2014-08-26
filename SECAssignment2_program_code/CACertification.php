<?php

class CAFunctions{

	public function ownerEncryption($ownerPrivateKey, $ownerPublicKey){
	
		$ownerInfo = "countryName => AU,stateOrProvinceName => Victoria,localityName => Carlton,organizationName => Simth and Sams,organizationalUnitName => PHP Documentation Team,commonName => SandS.com.au,emailAddress => SandS@example.com";
		$seperator = "<br /><br /><br />";
		$content = $ownerInfo . $seperator . $ownerPublicKey;
		
		// Creating the MAC value of the message content.
		$hashVal = hash("md4", $content);

		$encHash = "";
		openssl_private_encrypt($hashVal, $encHash, $ownerPrivateKey);
		
		$fileStr = $ownerInfo . $seperator . $ownerPublicKey . $seperator . $encHash;
		file_put_contents("OwnerInfo.txt", $fileStr);
		
		$ownerArr = array("content"=>$content,"encHash"=>$encHash);
		return $ownerArr; 
	}

	public function CADecryption($ownerArr, $ownerPublicKey){
		
		$verification_hash = "";
		$encHash = "";
		$decryptedHash = "";
		
		foreach($ownerArr as $key=>$value){
			
			if($key == "content"){
				$verification_hash = hash("md4", $value);
			} else if($key == "encHash"){
				$encHash = $value;
			}
		}
		
		// Decrypting the encrypted hash.
		openssl_public_decrypt($encHash, $decryptedHash, $ownerPublicKey);
		
		if($verification_hash == $decryptedHash){
			echo "Owner signature successfully verified.<br /><br />";
			return true;
		}
		return false;
	}

	public function CASigning($CAPrivateKey, $ownerArr, $CAPublicKey){
		$CACertInfo = "Issued To -> SandS.com.au<br />Issued By -> FairField Authorities Inc.<br />Valid From -> May 2013<br />Valid To   -> May 2014";
		$seperator = "<br /><br /><br />";
		$fileContent = $CACertInfo . $seperator . $CAPublicKey;
		
		// Get the hash value of file content
		$CAHash = hash("md4", $fileContent);
		// Encrypt the hash value
		$encHash = "";
		openssl_private_encrypt($CAHash, $encHash, $CAPrivateKey);
		
		$fileStr = $CACertInfo . $seperator . $CAPublicKey . $seperator . "CA Finger Print: " . $encHash;
		file_put_contents("CACert.txt", $fileStr);
		
		$CAArr = array("content"=>$fileContent,"encHash"=>$encHash,"printContent"=>$fileStr);
		return $CAArr;
	}

	public function ownerVerifyCertificate($CAArr, $CAPublicKey){

		$verification_hash = "";
		$encHash = "";
		$decryptedHash = "";
		$printContent = "";
		
		foreach($CAArr as $key=>$value){
			
			if($key == "content"){
				$verification_hash = hash("md4", $value);
			} else if($key == "encHash"){
				$encHash = $value;
			} else if ($key == "printContent"){
				$printContent = $value;
			}
		}
		// Decrypting the encrypted hash.
		openssl_public_decrypt($encHash, $decryptedHash, $CAPublicKey);
		
		if($verification_hash == $decryptedHash){
			echo "CA signature successfully verified.. <br /><br />";
			echo "CERTIFICATE INFORMATION <br />";
			echo $printContent;
			return true;
		}
		return false;
	}
}

?>