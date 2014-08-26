<?php
	include_once '../model/CA_functions.php';
	
	// Instanciate class
	$caOperation = new CAFunctions;
?>
<html>

	<head>
	</head>

	<body>
		
		<h1>Certificate Signing Requests Information</h1>
		<br />
		<a href="../controller/CA_operation.php">Generate Key and Certificate</a> | 
		<a href="../controller/logout.php">Logout</a>
		<br /><br />
		
		<table border="1">
			<tr>
				<th>CSR Location</th>
				<th>Created Date</th>
				<th>Status</th>
			</tr>
			<?php
				$csrArray = $caOperation->getAllCACSR();
				$arrLength = count($csrArray);
				$csrLocation = "";
				$clientKeyId = "";
				
				for($x = 0; $x<$arrLength; $x++){
			?>
					<tr>
			<?php
					foreach($csrArray[$x]->childNodes as $keyElement){
						if($keyElement->nodeName == '#text')
							continue;
						
						if($keyElement->nodeName == 'key_id'){
							$clientKeyId = $keyElement->nodeValue;
							
						} else if(($keyElement->nodeName == 'status') && !($keyElement->nodeValue == 'Signed')){ 
			?>
							<td>
								<a href="../controller/CA_operation.php?process=sign&id=<?php echo $csrArray[$x]->getAttribute('id'); ?> &keyId=<?php echo $clientKeyId; ?>"> Sign Request</a>
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
