<?php
	include_once 'encryptionAlgo.php';
	
	class LOG{
		
		const LOGFILE = '../data/log_info.xml';
		const SESSIONFILE = '../data/session_info.xml';
		
		public static function writeNewSessionLog($session_id, $user_id, $login_time, $ip){
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->load(self::SESSIONFILE);
			
			$sessions = $doc->documentElement;
			$newSession = $doc->createElement('session');
			$newSession->setAttribute('id', $session_id);
			$sessions->appendChild($newSession);
			
			$new_userid_node = $doc->createElement('user_id');
			$new_userid_node_text = $doc->createTextNode($user_id);
			$new_userid_node->appendChild($new_userid_node_text);
			
			$new_logintime_node = $doc->createElement('login_time');
			$new_logintime_node_text = $doc->createTextNode($login_time);
			$new_logintime_node->appendChild($new_logintime_node_text);
			
			$new_ip_node = $doc->createElement('user_ip');
			$new_ip_node_text = $doc->createTextNode($ip);
			$new_ip_node->appendChild($new_ip_node_text);
			
			$newSession->appendChild($new_userid_node);
			$newSession->appendChild($new_logintime_node);
			$newSession->appendChild($new_ip_node);

			$doc->save('../data/session_info.xml');
		}
		
		public static function appendSession($session_id, $newNodeName, $newNodeValue){
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->load(self::SESSIONFILE);
			
			$sessions = $doc->documentElement;
			foreach($sessions->childNodes as $sessionNode){
				$found = "NOT_FOUND";
				if($sessionNode->nodeName == '#text'){
					continue;
				}
				if($sessionNode->getAttribute('id') == $session_id){
					$found = "FOUND";
					break;
				}
			}
			
			if($found == "FOUND"){
					$newElement = $doc->createElement($newNodeName);
					$newElementText = $doc->createTextNode($newNodeValue);
					$newElement->appendChild($newElementText);
					$sessionNode->appendChild($newElement);
					$doc->save('../data/session_info.xml');
					return;
			}
		}
		
		public static function getAllSessionId(){
			$sessionidArray = array();
			$doc = new DOMDocument();
			$doc->load(self::SESSIONFILE);
			$sessions = $doc->documentElement;
		
			foreach($sessions->childNodes as $sessionNode){
					
				if($sessionNode->nodeName == '#text'){
					continue;
				}
				array_push($sessionidArray, $sessionNode->getAttribute('id'));
			}
			return $sessionidArray;
		}
		
		private static function findSessionById($id){
			$doc = new DOMDocument();
			$doc->load(self::SESSIONFILE);
			$sessions = $doc->documentElement;
		
			foreach($sessions->childNodes as $sessionNode){
					
				if($sessionNode->nodeName == '#text'){
					continue;
				}
				if($sessionNode->getAttribute('id') == $id)
					return $sessionNode;
			}
		}
		
		public static function findSessionByIdWithNodeName($id, $nodeName){
			$sessionElement = self::findSessionById($id);
			if(!isset($sessionElement))
				return;
			foreach($sessionElement->childNodes as $session){
				if($session->nodeName == '#text')
					continue;

				if($session->nodeName == $nodeName)
					return $session->nodeValue;
			}
		}
		public static function writeLog($transId, $ccNum, $amount, $date, $status){
			$doc = new DOMDocument();
			$doc->preserveWhiteSpace = false;
			$doc->formatOutput = true;
			$doc->load(self::LOGFILE);
			
			$logs = $doc->documentElement;
			$newLog = $doc->createElement('log');
			$logs->appendChild($newLog);
			
			/* 
			 * Encrypting the received data with the known key, to store in the database. 
			 */
			$encTransId = fnEncrypt($transId, $GLOBALS['passKey']);
			$encCCNum = fnEncrypt($ccNum, $GLOBALS['passKey']);
			
			$new_transID_node = $doc->createElement('transaction_id');
			$new_transID_node_text = $doc->createTextNode($encTransId);
			$new_transID_node->appendChild($new_transID_node_text);
			
			$new_ccID_node = $doc->createElement('cc_num');
			$new_ccID_node_text = $doc->createTextNode($encCCNum);
			$new_ccID_node->appendChild($new_ccID_node_text);
			
			$new_amount_node = $doc->createElement('amount');
			$new_amount_node_text = $doc->createTextNode($amount);
			$new_amount_node->appendChild($new_amount_node_text);
			
			$new_date_node = $doc->createElement('date');
			$new_date_node_text = $doc->createTextNode($date);
			$new_date_node->appendChild($new_date_node_text);
			
			$new_status_node = $doc->createElement('status');
			$new_status_node_text = $doc->createTextNode($status);
			$new_status_node->appendChild($new_status_node_text);
			
			$newLog->appendChild($new_transID_node);
			$newLog->appendChild($new_ccID_node);
			$newLog->appendChild($new_amount_node);
			$newLog->appendChild($new_date_node);
			$newLog->appendChild($new_status_node);
			
			$doc->save('../data/log_info.xml');
		}
		
		public static function findAllLogByNum($cc_num){
			$idArray = array();
			$doc = new DOMDocument();
			$doc->load(self::LOGFILE);
			$logs = $doc->documentElement;
		
			foreach($logs->childNodes as $logNode){
				$found = "NOT_FOUND";
				
				if($logNode->nodeName == '#text'){
					continue;
				}				
				foreach($logNode->childNodes as $log){										
					if($log->nodeName == '#text')
						continue;
						
					if($log->nodeName == 'cc_num'){
						$nodeValue = fnDecrypt((string)$log->nodeValue, $GLOBALS['passKey']);
						
						if ($nodeValue == $cc_num)
							$found = "FOUND";
					}
				}
				if($found == 'FOUND'){
					$encTransId = $logNode->getElementsByTagName('transaction_id')->item(0)->nodeValue;
					$transId = fnDecrypt((string)$encTransId, $GLOBALS['passKey']);				
					array_push($idArray, $transId);
				}
			}
			return $idArray;
		}
		
		private static function findLogById($id){
			$doc = new DOMDocument();
			$doc->load(self::LOGFILE);
			$logs = $doc->documentElement;
		
			foreach($logs->childNodes as $logNode){
					
				if($logNode->nodeName == '#text'){
					continue;
				}				
				foreach($logNode->childNodes as $log){					
					if($log->nodeName == '#text')
						continue;					
					
					if($log->nodeName == 'transaction_id'){
						$nodeValue = fnDecrypt((string)$log->nodeValue, $GLOBALS['passKey']);
						
						if ($nodeValue == $id)
							return $logNode;
					}						
				}
			}
		}
		
		public static function findLogByidWithNodeName($id, $nodeName){
			$logElement = self::findLogById($id);
			if(!isset($logElement))
				return;
			foreach($logElement->childNodes as $log){
				if($log->nodeName == '#text')
					continue;

				if($log->nodeName == $nodeName)
					return $log->nodeValue;
			}
		}
		
		public static function findLogByCCNumAndStatus($cc_num){
			$doc = new DOMDocument();
			$doc->load(self::LOGFILE);
			$logs = $doc->documentElement;
		
			foreach($logs->childNodes as $logNode){
				$cc_found = "NOT_FOUND";
				$status_found = "NOT_FOUND";
				
				if($logNode->nodeName == '#text'){
					continue;
				}
				
				foreach($logNode->childNodes as $log){
					if($log->nodeName == '#text')
						continue;
					
					if($log->nodeName == 'cc_num'){
						$nodeValue = fnDecrypt((string)$log->nodeValue, $GLOBALS['passKey']);
						
						if ($nodeValue == $cc_num)
							$cc_found = "FOUND";
					}
						
					if($log->nodeName == 'status'){
						$nVal = (string)$log->nodeValue;
						if(($nVal == 'decline; blacklist') || ($nVal == 'decline; fraud') || ($nVal == 'decline; velocity')){
							$status_found = "FOUND";
						}					
					}						
				}
				if($cc_found == "FOUND" && $status_found == "FOUND"){
					return "CARD_DECLINED"; 
				}
			}
			return "CARD_APPROVED";
		}
	}
?>