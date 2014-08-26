<table border="1">
<tr>
	<th>No.</th>
	<th>Transaction ID</th>
	<th>Date</th>
	<th>Amount ($AUD)</th>
	<th>Status</th>
</tr>
			
<?php
	for($i = 0; $i < count($idArray); $i++){
		$amount = LOG::findLogByIdWithNodeName($idArray[$i], "amount");
		$date = LOG::findLogByIdWithNodeName($idArray[$i], "date");
		$status = LOG::findLogByIdWithNodeName($idArray[$i], "status");
					
		echo "<tr>";
		echo "<td>" . ($i+1). "</td>";
		
		if(strlen($idArray[$i])<14)
			echo "<td>" . '-' . "</td>";
		else
			echo "<td>" . $idArray[$i]. "</td>";
			
		echo "<td>" .$date. "</td>";
		echo "<td>" .$amount. "</td>";
		echo "<td>" .$status. "</td>";
		echo "</tr>";
	}
?>
</table>