<?php

//company_action.php

include('../class/Application.php');

$object = new Application;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('company_name', 'company_status');

		$output = array();

		$main_query = "
		SELECT * FROM company_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE company_email_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR company_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR company_phone_no LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR company_type LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR company_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR company_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY company_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->query = $main_query . $search_query . $order_query;

		$object->execute();

		$filtered_rows = $object->row_count();

		$object->query .= $limit_query;

		$result = $object->get_result();

		$object->query = $main_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();
			$sub_array[] = '<img src="'.$row["company_profile_image"].'" class="img-thumbnail" width="75" />';
			$sub_array[] = $row["company_email_address"];
			$sub_array[] = $row["company_password"];
			$sub_array[] = $row["company_name"];
			$sub_array[] = $row["company_phone_no"];
			$sub_array[] = $row["company_type"];
			$status = '';
			if($row["company_status"] == 'Active')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["company_id"].'" data-status="'.$row["company_status"].'">Active</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["company_id"].'" data-status="'.$row["company_status"].'">Inactive</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["company_id"].'"><i class="fas fa-eye"></i></button>
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["company_id"].'"><i class="fas fa-edit"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["company_id"].'"><i class="fas fa-times"></i></button>
			</div>
			';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);

	}

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';

		$data = array(
			':company_email_address'	=>	$_POST["company_email_address"]
		);

		$object->query = "
		SELECT * FROM company_table 
		WHERE company_email_address = :company_email_address
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{
			$company_profile_image = '';
			if($_FILES['company_profile_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["company_profile_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valid file. jpg, png</div>";
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
			else
			{
				$character = $_POST["company_name"][0];
				$path = "../images/". time() . ".png";
				$image = imagecreate(200, 200);
				$red = rand(0, 255);
				$green = rand(0, 255);
				$blue = rand(0, 255);
			    imagecolorallocate($image, 230, 230, 230);  
			    $textcolor = imagecolorallocate($image, $red, $green, $blue);
			    imagettftext($image, 100, 0, 55, 150, $textcolor, '../font/arial.ttf', $character);
			    imagepng($image, $path);
			    imagedestroy($image);
			    $company_profile_image = $path;
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
					':company_type'				=>	$object->clean_input($_POST["company_type"]),
					':company_status'				=>	'Active',
					':company_added_on'				=>	$object->now
				);

				$object->query = "
				INSERT INTO company_table 
				(company_email_address, company_password, company_name, company_profile_image, company_phone_no, company_address,  company_type, company_status, company_added_on) 
				VALUES (:company_email_address, :company_password, :company_name, :company_profile_image, :company_phone_no, :company_address,  :company_type, :company_status, :company_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Company Added</div>';
			}
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM company_table 
		WHERE company_id = '".$_POST["company_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['company_email_address'] = $row['company_email_address'];
			$data['company_password'] = $row['company_password'];
			$data['company_name'] = $row['company_name'];
			$data['company_profile_image'] = $row['company_profile_image'];
			$data['company_phone_no'] = $row['company_phone_no'];
			$data['company_address'] = $row['company_address'];
			$data['company_type'] = $row['company_type'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

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

				$success = '<div class="alert alert-success">Company Data Updated</div>';
			}			
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$data = array(
			':company_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE company_table 
		SET company_status = :company_status 
		WHERE company_id = '".$_POST["id"]."'
		";

		$object->execute($data);

		echo '<div class="alert alert-success">Class Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM company_table 
		WHERE company_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Company Data Deleted</div>';
	}
}

?>