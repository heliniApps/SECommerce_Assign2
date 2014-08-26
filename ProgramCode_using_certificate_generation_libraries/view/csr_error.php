<html>
<?php 
	if((isset($_GET['error_type'])) && ($_GET['error_type'] == 'csr_request_error')){
?>
		<h1>Error in sending Certificate Signing Request.. <br />Please try again.</h1>
<?php
	} else if((isset($_GET['error_type'])) && ($_GET['error_type'] == 'csr_sign_error')){
?>
		<h1>Error in signing the client CSR.. <br />Please try again.</h1>
<?php
	}
?>
<br />
<h3><a href="../display_generated_keys.php">Back to Previous Page</a></h3>
<h3><a href="../">Back to Home Page</a></h3>

</html>