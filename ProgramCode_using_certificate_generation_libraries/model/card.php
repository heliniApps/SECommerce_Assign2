<?php
include_once 'encryptionAlgo.php';
class CARD{

	const CARDFILE = '../data/cc_info.xml';
	
	private static function findCardByUserId($id){
		$doc = new DOMDocument();
		$doc->load(self::CARDFILE);
		$cards = $doc->documentElement;
	
		foreach($cards->childNodes as $cardNode){
				
			if($cardNode->nodeName == '#text'){
				continue;
			}
			
			foreach($cardNode->childNodes as $card){
				if($card->nodeName == '#text')
					continue;
				if(($card->nodeName == 'user_id') && ($card->nodeValue == $id))
					return $cardNode;
			}
		}
	}
	
	public static function getValueByIdWithNodeName($id, $nodeName){
		
		$cardElement = self::findCardByUserId($id);
		if(!isset($cardElement))
			return;
			
		foreach($cardElement->childNodes as $card){
			if($card->nodeName == '#text')
				continue;
			
			/* 
			 * Decrypting the data in the database with the known key.
			 */
			if($card->nodeName == $nodeName){
				if($card->nodeName == 'user_id') {
					return (string)$card->nodeValue;
				}
				$nodeValue = fnDecrypt((string)$card->nodeValue, $GLOBALS['passKey']);
				return $nodeValue;
			}				
		}		
	}
	
	public static function getCardInfo($userId){
		$number = CARD::getValueByIdWithNodeName($userId, "card_number");
		$cvv = CARD::getValueByIdWithNodeName($userId, "cvv");
		$date = CARD::getValueByIdWithNodeName($userId, "expiry_date");
		$name = CARD::getValueByIdWithNodeName($userId, "card_holder_name");
		
		$dataArray = array("number"=>$number, "cvv"=>$cvv, "date"=>$date, "name"=>$name);
		return $dataArray;
	}
	
	
	public static function registerNewCard($user_id, $cc_num, $cc_cvv, $cc_date, $cc_name){
		$doc = new DOMDocument();
		$doc->preserveWhiteSpace = false;
		$doc->formatOutput = true;
		$doc->load(self::CARDFILE);
		
		$cards = $doc->documentElement;
		$newCard = $doc->createElement('card');
		$cards->appendChild($newCard);
		
		$new_id = uniqid();
		$newCard->setAttribute('id', $new_id);
		
		$new_id_node = $doc->createElement('user_id');
		$new_id_node_text = $doc->createTextNode($user_id);
		$new_id_node->appendChild($new_id_node_text);
		
		/* 
	     * Encrypting the received data with the known key, to store in the database. 
		 */		
		$encryptedCCNum = fnEncrypt($cc_num, $GLOBALS['passKey']);
		$encryptedCVV = fnEncrypt($cc_cvv, $GLOBALS['passKey']);
		$encryptedExp = fnEncrypt($cc_date, $GLOBALS['passKey']);
		$encryptedName = fnEncrypt($cc_name, $GLOBALS['passKey']);
		$new_num_node = $doc->createElement('card_number');
		$new_num_node_text = $doc->createTextNode($encryptedCCNum);
		$new_num_node->appendChild($new_num_node_text);
		
		$new_cvv_node = $doc->createElement('cvv');
		$new_cvv_node_text = $doc->createTextNode($encryptedCVV);
		$new_cvv_node->appendChild($new_cvv_node_text);
		
		$new_date_node = $doc->createElement('expiry_date');
		$new_date_node_text = $doc->createTextNode($encryptedExp);
		$new_date_node->appendChild($new_date_node_text);
		
		$new_name_node = $doc->createElement('card_holder_name');
		$new_name_node_text = $doc->createTextNode($encryptedName);
		$new_name_node->appendChild($new_name_node_text);
		
		$newCard->appendChild($new_id_node);
		$newCard->appendChild($new_num_node);
		$newCard->appendChild($new_cvv_node);
		$newCard->appendChild($new_date_node);
		$newCard->appendChild($new_name_node);
		
		$doc->save('../data/cc_info.xml');
	}
}



?>