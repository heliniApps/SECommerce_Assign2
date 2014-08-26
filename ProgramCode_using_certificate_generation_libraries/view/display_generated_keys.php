<?php
	include_once '../model/generateKeys.php';	
	
	// Instanciate class
	$keyGenerator = new generateKeys;
	$keyGenerator->pkeyEncDecryp();
?>
<html>

	<head>
	</head>

	<body>
		
		<h1>Certificate Information</h1>
		<br />
		<a href="../controller/generateKeyAndCSR.php">Generate Key and CSR</a> | 
		<a href="../controller/logout.php">Logout</a>
		<br />
		<a href="../view/CA_display_csr.php">Go to CA page</a>
		<br /><br />
		
		<table border="1">
			<tr>
				<th>Key Location</th>
				<th>CSR Location</th>
				<th>Validity</th>
				<th>Created Date</th>
				<th>Sign Request Status</th>
			</tr>
			<?php
				$keyArray = $keyGenerator->getAllCSR();
				$arrLength = count($keyArray);
				$csrLocation = "";
				
				for($x = 0; $x<$arrLength; $x++){
			?>
					<tr>
			<?php
					foreach($keyArray[$x]->childNodes as $keyElement){
						if($keyElement->nodeName == '#text')
							continue;
						
						if(($keyElement->nodeName == 'status') && !(($keyElement->nodeValue == 'Signed') || ($keyElement->nodeValue == 'Request Sent'))){ 
			?>
							<td>
								<a href="../controller/generateKeyAndCSR.php?process=sign&id=<?php echo $keyArray[$x]->getAttribute('id'); ?>"> Send Request</a>
							</td>
			<?php			
						} else {
			?>						
							<td>
			<?php
							if($keyElement->nodeName == 'csr_file'){
								$csrLocation = $keyElement->nodeValue;
							}
							echo $keyElement->nodeValue;
			?>						
							</td>
			<?php
						}
					}
				}
			?>			
		</table>
		
	</body>
</html>
