<?php

//verify.php

include('header.php');

include('class/Application.php');

$object = new Application;

if(isset($_GET["code"]))
{
	$object->query = "
	UPDATE student_table 
	SET email_verify = 'Yes' 
	WHERE student_verification_code = '".$_GET["code"]."'
	";

	$object->execute();

	$_SESSION['success_message'] = '<div class="alert alert-success">You Email has been verify, now you can login into system</div>';

	header('location:login.php');
}

include('footer.php');

?>