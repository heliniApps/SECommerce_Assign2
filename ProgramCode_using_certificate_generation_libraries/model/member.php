<?php
include_once 'encryptionAlgo.php';
class MEMBER{

	const MEMBERFILE = '../data/member.xml';
	
	private static function findMemberById($id){
		$doc = new DOMDocument();
		$doc->load(self::MEMBERFILE);
		 $members = $doc->documentElement;
		 
		foreach($members->childNodes as $memberNode){
				
			if($memberNode->nodeName == '#text'){
				continue;
			}
				
			if($memberNode->getAttribute('id') == $id)
				return $memberNode;
		}
	}
	
	public static function getValueByIdOfNodeName($id, $nodeName){
		$memberElement = self::findMemberById($id);
				
		foreach($memberElement->childNodes as $member){
			if($member->nodeName == '#text')
				continue;

			if($member->nodeName == $nodeName){
				$val = $member->nodeValue;
				if($nodeName == "email"){
					$member_email = $member->nodeValue;
					/* 
					 * Decrypting the data in the database with the known key, 
					 * to tally with the received credentials. 
					 */
					$decryptedEmail = fnDecrypt($member_email, $GLOBALS['passKey']);
					$val = $decryptedEmail;
				}
				return $val;
			}
		}
	}
	
	public static function findMemberByEmailPassword($email, $password, $session){
		$doc = new DOMDocument();
		$doc->load(self::MEMBERFILE);
		$members = $doc->documentElement;
		$member_node = $doc->getElementsByTagName('member');
		
		foreach($members->childNodes as $member){
			
			if($member->nodeName == '#text'){
				continue;
			}
			
			foreach($member->childNodes as $member_element){

				if($member_element->nodeName != 'email' && $member_element->nodeName != 'password'){
					continue;
				}

				if($member_element->nodeName == 'email')
					$member_email = $member_element->nodeValue;
				if($member_element->nodeName == 'password')
					$member_pass = $member_element->nodeValue;	
			}
			
			/* 
			 * Decrypting the data in the database with the known key, 
			 * to tally with the received credentials. 
			 */
			$decryptedEmail = fnDecrypt($member_email, $GLOBALS['passKey']);
			$decryptedPass = fnDecrypt($member_pass, $GLOBALS['passKey']);

			if($email == $decryptedEmail && $password == $decryptedPass){
				$_SESSION['USER_ID'] = $member->getAttribute('id');
				$_SESSION['USER_IP'] = $_SERVER['REMOTE_ADDR'];
				MEMBER::updateSession($member, $doc, $session);
				return "ALLOWED";
			}
		}
	}
	
	private static function updateSession($memberNode, $doc, $session){
		$new_session_node = $doc->createElement('session');
		$new_session_text_node = $doc->createTextNode($session);
		$new_session_node->appendChild($new_session_text_node);
	
		foreach($memberNode->childNodes as $member_element){
			if($member_element->nodeName != 'session'){
				continue;
			}
			
			$memberNode->replaceChild($new_session_node, $member_element);
		}
		$doc->save('../data/member.xml');
	}
	
	public static function getAllMemberID(){
		$idArray = array();
		$doc = new DOMDocument();
		$doc->load(self::MEMBERFILE);
		 $members = $doc->documentElement;
		 
		foreach($members->childNodes as $memberNode){
				
			if($memberNode->nodeName == '#text'){
				continue;
			}
			array_push($idArray, $memberNode->getAttribute('id'));
		}
		return $idArray;
	}
	public static function checkSession($id, $session){
		$registeredSession = MEMBER::getValueByIdOfNodeName($id, "session");
		if($session != $registeredSession)
			return "NOT_ALLOWED";
	}
	
	public static function registerNewMember
		($name, $age, $address, $city, $state, $zip, $country, $email, $pass){
		$doc = new DOMDocument();
	    $doc->preserveWhiteSpace = false;
	    $doc->formatOutput = true;
        $doc->load(self::MEMBERFILE);
        $members = $doc->documentElement;
        $newMember  = $doc->createElement('member');
	    $members->appendChild($newMember);
	  
	    $new_id = uniqid();
        $newMember->setAttribute('id', $new_id);
	  
	    $new_name_node = $doc->createElement('name');
	    $new_name_node_text = $doc->createTextNode($name);
	    $new_name_node->appendChild($new_name_node_text);
	  
	    $new_age_node = $doc->createELement('age');
	    $new_age_node_text = $doc->createTextNode($age);
	    $new_age_node->appendChild($new_age_node_text);
	  
	    $new_address_node = $doc->createELement('address');
	    $new_address_node_text = $doc->createTextNode($address);
	    $new_address_node->appendChild($new_address_node_text);
	  
	    $new_city_node = $doc->createELement('city');
	    $new_city_node_text = $doc->createTextNode($city);
	    $new_city_node->appendChild($new_city_node_text);
	  
	    $new_state_node = $doc->createELement('state');
	    $new_state_node_text = $doc->createTextNode($state);
	    $new_state_node->appendChild($new_state_node_text);
	  
	    $new_zip_node = $doc->createELement('zip');
	    $new_zip_node_text = $doc->createTextNode($zip);
	    $new_zip_node->appendChild($new_zip_node_text);
	  
	    $new_country_node = $doc->createELement('country');
	    $new_country_node_text = $doc->createTextNode($country);
	    $new_country_node->appendChild($new_country_node_text);
	  
	   /* 
        * Encrypting the received data with the known key, to store in the database. 
	    */
	    $encryptedEmail = fnEncrypt($email, $GLOBALS['passKey']);
		$encryptedPass = fnEncrypt($pass, $GLOBALS['passKey']);
		$encryptedTransPass = fnEncrypt('3d49947784', $GLOBALS['passKey']);
		
		$new_email_node = $doc->createElement('email');
		$new_email_node_text = $doc->createTextNode($encryptedEmail);
		$new_email_node->appendChild($new_email_node_text);
	  
		$new_pass_node = $doc->createElement('password');
		$new_pass_node_text = $doc->createTextNode($encryptedPass);
		$new_pass_node->appendChild($new_pass_node_text);
		
		$new_trans_pass_node = $doc->createElement('trans-pass');
		$new_trans_pass_text = $doc->createTextNode($encryptedTransPass);
		$new_trans_pass_node->appendChild($new_trans_pass_text);
		
		$new_session_node = $doc->createElement('session');
		
		$newMember->appendChild($new_name_node);
		$newMember->appendChild($new_age_node);
		$newMember->appendChild($new_address_node);
		$newMember->appendChild($new_city_node);
		$newMember->appendChild($new_state_node);
		$newMember->appendChild($new_zip_node);
		$newMember->appendChild($new_country_node);
		$newMember->appendChild($new_email_node);
		$newMember->appendChild($new_pass_node);
		$newMember->appendChild($new_trans_pass_node);
		$newMember->appendChild($new_session_node);
	  
		$doc->save("../data/member.xml");
	}
}
?>