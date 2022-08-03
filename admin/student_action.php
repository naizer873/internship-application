<?php

//student_action.php

include('../class/Application.php');

$object = new Application;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('student_first_name', 'student_last_name', 'student_email_address', 'student_phone_no', 'email_verify');

		$output = array();

		$main_query = "
		SELECT * FROM student_table ";

		$search_query = '';
		

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE student_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_email_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR student_phone_no LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR email_verify LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY student_id DESC ';
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
			$sub_array[] = $row["student_first_name"];
			$sub_array[] = $row["student_last_name"];
			$sub_array[] = $row["student_email_address"];
			$sub_array[] = $row["student_phone_no"];
			$status = '';
			if($row["email_verify"] == 'Yes')
			{
				$status = '<span class="badge badge-success">Yes</span>';
			}
			else
			{
				$status = '<span class="badge badge-danger">No</span>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["student_id"].'"><i class="fas fa-eye"></i></button>
			&nbsp;
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["student_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["student_id"].'"><i class="fas fa-times"></i></button>
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
			':student_email_address'	=>	$_POST["student_email_address"]
		);

		$object->query = "
		SELECT * FROM student_table 
		WHERE student_email_address = :student_email_address
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{
			$student_profile_image = '';
			if($_FILES['student_profile_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["student_profile_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["student_profile_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../images/' . $new_name;

					move_uploaded_file($_FILES['student_profile_image']['tmp_name'], $destination);

					$student_profile_image = $destination;
			    }
			}
			else
			{
				$character = $_POST["student_name"][0];
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
			    $student_profile_image = $path;
			}

			if($error == '')
			{
				$data = array(
					':student_email_address'			=>	$object->clean_input($_POST["student_email_address"]),
					':student_password'				=>	$_POST["student_password"],
					':student_name'					=>	$object->clean_input($_POST["student_name"]),
					#':student_profile_image'			=>	$student_profile_image,
					':student_phone_no'				=>	$object->clean_input($_POST["student_phone_no"]),
					':student_address'				=>	$object->clean_input($_POST["student_address"]),
					':student_date_of_birth'			=>	$object->clean_input($_POST["student_date_of_birth"]),
					#':student_degree'				=>	$object->clean_input($_POST["student_degree"]),
					':student_speciality'				=>	$object->clean_input($_POST["student_speciality"]),
					':student_status'				=>	'Active',
					':student_added_on'				=>	$object->now
				);

				$object->query = "
				INSERT INTO student_table 
				(student_email_address, student_password, student_name, student_profile_image, student_phone_no, student_address, student_date_of_birth, student_speciality, student_status, student_added_on) 
				VALUES (:student_email_address, :student_password, :student_name, :student_profile_image, :student_phone_no, :student_address, :student_date_of_birth,  :student_speciality, :student_status, :student_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">student Added</div>';
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
		SELECT * FROM student_table 
		WHERE student_id = '".$_POST["student_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['student_email_address'] = $row['student_email_address'];
			$data['student_password'] = $row['student_password'];
			$data['student_first_name'] = $row['student_first_name'];
			$data['student_last_name'] = $row['student_last_name'];
			$data['student_date_of_birth'] = $row['student_date_of_birth'];
			$data['student_gender'] = $row['student_gender'];
			$data['student_address'] = $row['student_address'];
			$data['student_phone_no'] = $row['student_phone_no'];
			$data['student_speciality'] = $row['student_speciality'];
			if($row['email_verify'] == 'Yes')
			{
				$data['email_verify'] = '<span class="badge badge-success">Yes</span>';
			}
			else
			{
				$data['email_verify'] = '<span class="badge badge-danger">No</span>';
			}
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		
		$company_id = '';

		if($_SESSION['type'] == 'Admin')
		{
			$company_id = $_POST["company_id"];
		}

		if($_SESSION['type'] == 'company')
		{
			$company_id = $_SESSION['admin_id'];
		}
		
		$data = array(
			':student_email_address'	=>	$_POST["student_email_address"],
			':student_id'			=>	$_POST['hidden_id'],
			':student_first_name'			=>	$_POST['student_first_name'],
			':student_last_name'			=>	$_POST['student_last_name'],
			':student_password'			=>	$_POST['student_password'],
			':student_phone_no'			=>	$_POST['student_phone_no'],
			':student_date_of_birth'			=>	$_POST['student_date_of_birth'],
			':student_address'			=>	$_POST['student_address'],
			':student_gender'			=>	$_POST['student_gender'],
			':student_speciality'			=>	$_POST['student_speciality'],
			':email_verify'			=>	$_POST['email_verify']
		);

		$object->query = "
		UPDATE student_table 
		SET student_id = :student_id, 
		student_email_address =: student_email_address,
		student_first_name =:student_first_name,
		student_last_name=::student_last_name,
		student_password = :student_password, 
		student_phone_no = :student_phone_no, 
		student_date_of_birth =: student_date_of_birth,
		student_address =: student_address,
		student_gender =: student_gender,
		student_speciality = :student_speciality, 
		email_verify =: email_verify
		 
		WHERE student_id = '".$_POST['hidden_id']."'
		";
		$object->execute($data);

		$success = '<div class="alert alert-success">Student Data Updated</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$data = array(
			':student_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE student_table 
		SET student_status = :student_status 
		WHERE student_id = '".$_POST["id"]."'
		";

		$object->execute($data);

		echo '<div class="alert alert-success">Class Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM student_table 
		WHERE student_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Student Data Deleted</div>';
	}
}

?>