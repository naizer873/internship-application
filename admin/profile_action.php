<?php

include('../class/Application.php');

$object = new Application;

if($_POST["action"] == 'company_profile')
{
	sleep(2);

	$error = '';

	$success = '';

	$company_profile_image = '';

	$data = array(
		':company_email_address'	=>	$_POST["company_email_address"],
		':company_id'			=>	$_POST['hidden_id']
	);

	$object->query = "
	SELECT * FROM company_table 
	WHERE company_email_address = :company_email_address 
	AND company_id != :company_id
	";

	$object->execute($data);

	if($object->row_count() > 0)
	{
		$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
	}
	else
	{
		$company_profile_image = $_POST["hidden_company_profile_image"];

		if($_FILES['company_profile_image']['name'] != '')
		{
			$allowed_file_format = array("jpg", "png");

	    	$file_extension = pathinfo($_FILES["company_profile_image"]["name"], PATHINFO_EXTENSION);

	    	if(!in_array($file_extension, $allowed_file_format))
		    {
		        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
		    }
		    else if (($_FILES["company_profile_image"]["size"] > 2000000))
		    {
		       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
		    }
		    else
		    {
		    	$new_name = rand() . '.' . $file_extension;

				$destination = '../images/' . $new_name;

				move_uploaded_file($_FILES['company_profile_image']['tmp_name'], $destination);

				$company_profile_image = $destination;
		    }
		}

		if($error == '')
		{
			$data = array(
				':company_email_address'			=>	$object->clean_input($_POST["company_email_address"]),
				':company_password'				=>	$_POST["company_password"],
				':company_name'					=>	$object->clean_input($_POST["company_name"]),
				':company_profile_image'			=>	$company_profile_image,
				':company_phone_no'				=>	$object->clean_input($_POST["company_phone_no"]),
				':company_address'				=>	$object->clean_input($_POST["company_address"]),
				':company_type'				=>	$object->clean_input($_POST["company_type"])
			);

			$object->query = "
			UPDATE company_table  
			SET company_email_address = :company_email_address, 
			company_password = :company_password, 
			company_name = :company_name, 
			company_profile_image = :company_profile_image, 
			company_phone_no = :company_phone_no, 
			company_address = :company_address,   
			company_type = :company_type 
			WHERE company_id = '".$_POST['hidden_id']."'
			";
			$object->execute($data);

			$success = '<div class="alert alert-success">company Data Updated</div>';
		}			
	}

	$output = array(
		'error'					=>	$error,
		'success'				=>	$success,
		'company_email_address'	=>	$_POST["company_email_address"],
		'company_password'		=>	$_POST["company_password"],
		'company_name'			=>	$_POST["company_name"],
		'company_profile_image'	=>	$company_profile_image,
		'company_phone_no'		=>	$_POST["company_phone_no"],
		'company_address'		=>	$_POST["company_address"],
		'company_type'		=>	$_POST["company_type"],
	);

	echo json_encode($output);
}

if($_POST["action"] == 'admin_profile')
{
	sleep(2);

	$error = '';

	$success = '';

	$platform_logo = $_POST['hidden_platform_logo'];

	if($_FILES['platform_logo']['name'] != '')
	{
		$allowed_file_format = array("jpg", "png");

	    $file_extension = pathinfo($_FILES["platform_logo"]["name"], PATHINFO_EXTENSION);

	    if(!in_array($file_extension, $allowed_file_format))
		{
		    $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
		}
		else if (($_FILES["platform_logo"]["size"] > 2000000))
		{
		   $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
	    }
		else
		{
		    $new_name = rand() . '.' . $file_extension;

			$destination = '../images/' . $new_name;

			move_uploaded_file($_FILES['platform_logo']['tmp_name'], $destination);

			$platform_logo = $destination;
		}
	}

	if($error == '')
	{
		$data = array(
			':admin_email_address'			=>	$object->clean_input($_POST["admin_email_address"]),
			':admin_password'				=>	$_POST["admin_password"],
			':admin_name'					=>	$object->clean_input($_POST["admin_name"]),
			':platform_name'				=>	$object->clean_input($_POST["platform_name"]),
			':platform_address'				=>	$object->clean_input($_POST["platform_address"]),
			':platform_contact_no'			=>	$object->clean_input($_POST["platform_contact_no"]),
			':platform_logo'				=>	$platform_logo
		);

		$object->query = "
		UPDATE admin_table  
		SET admin_email_address = :admin_email_address, 
		admin_password = :admin_password, 
		admin_name = :admin_name, 
		platform_name = :platform_name, 
		platform_address = :platform_address, 
		platform_contact_no = :platform_contact_no, 
		platform_logo = :platform_logo 
		WHERE admin_id = '".$_SESSION["admin_id"]."'
		";
		$object->execute($data);

		$success = '<div class="alert alert-success">Admin Data Updated</div>';

		$output = array(
			'error'					=>	$error,
			'success'				=>	$success,
			'admin_email_address'	=>	$_POST["admin_email_address"],
			'admin_password'		=>	$_POST["admin_password"],
			'admin_name'			=>	$_POST["admin_name"], 
			'platform_name'			=>	$_POST["platform_name"],
			'platform_address'		=>	$_POST["platform_address"],
			'platform_contact_no'	=>	$_POST["platform_contact_no"],
			'platform_logo'			=>	$platform_logo
		);

		echo json_encode($output);
	}
	else
	{
		$output = array(
			'error'					=>	$error,
			'success'				=>	$success
		);
		echo json_encode($output);
	}
}

?>