
	<?php
		$allMemberIdArray = MEMBER::getAllMemberID();
		$count = 1;
		for($i= 0; $i < count($allMemberIdArray); $i++){
			$cust_id = $allMemberIdArray[$i];
			$name = MEMBER::getValueByIdOfNodeName($cust_id, "name");
			$ccNum =  CARD::getValueByIdWithNodeName($cust_id, "card_number");
			echo "<h3>Member Name: " .$name. "<br />";
			echo "Credit Card Number: " .$ccNum. "</h3>";
			echo "<table border='1' style='margin-top:-18px'>";
			echo "
				<tr>
					<th>Transaction ID</th>
					<th>Date</th>
					<th>Amount</th>
					<th>Status</th>
				</tr>
			";
			
			
			$logIdArray =  LOG::findAllLogByNum($ccNum);
			for($x=0; $x < count($logIdArray); $x++){
				$transId = $logIdArray[$x];
				$amount = LOG::findLogByidWithNodeName($transId, "amount");
				$date = LOG::findLogByidWithNodeName($transId, "date");
				$status = LOG::findLogByidWithNodeName($transId, "status");
				echo "<tr>";
				echo "<td>" .$transId. "</td>";
				echo "<td>" .$date. "</td>";
				echo "<td>" .$amount. "</td>";
				echo "<td>" .$status. "</td>";
				echo "</tr>";
			}
			echo "</table><br />";
		}
	?>