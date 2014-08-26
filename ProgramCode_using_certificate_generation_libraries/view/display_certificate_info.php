<?php
	include_once '../model/generateKeys.php';
	
	// Instanciate class
	$keyGenerator = new generateKeys;
?>
<html>

	<head>
	</head>

	<body>
		
		<h1>Certificate Information</h1>
		<br />
		<a href="../controller/generateKeyAndCSR.php">Home Page</a> | 
		<a href="../controller/logout.php">Logout</a>
		<br /><br />
		
		<table border="1">
			<tr>
				<td></td>
				<td></td>
			</tr>			
		</table>
		
	</body>
</html>
