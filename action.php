<?php
//action.php

include('class/Application.php');

$object = new Application;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'check_login')
	{
		if(isset($_SESSION['student_id']))
		{
			echo 'dashboard.php';
		}
		else
		{
			echo 'login.php';
		}
	}

	if($_POST['action'] == 'student_register')
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
			$success = '<div class="alert alert-success">Registered Successfully</div>';
			$student_verification_code = md5(uniqid());
			$data = array(
				':student_email_address'		=>	$object->clean_input($_POST["student_email_address"]),
				':student_password'				=>	$_POST["student_password"],
				':student_first_name'			=>	$object->clean_input($_POST["student_first_name"]),
				':student_last_name'			=>	$object->clean_input($_POST["student_last_name"]),
				':student_date_of_birth'		=>	$object->clean_input($_POST["student_date_of_birth"]),
				':student_gender'				=>	$object->clean_input($_POST["student_gender"]),
				':student_address'				=>	$object->clean_input($_POST["student_address"]),
				':student_phone_no'				=>	$object->clean_input($_POST["student_phone_no"]),
				':student_speciality'			=>	$object->clean_input($_POST["student_speciality"]),
				':student_added_on'				=>	$object->now,
				':student_verification_code'	=>	$student_verification_code,
				':email_verify'					=>	'Yes'
			);

			$object->query = "
			INSERT INTO student_table 
			(student_email_address, student_password, student_first_name, student_last_name, student_date_of_birth, student_gender, student_address, student_phone_no, student_speciality, student_added_on, student_verification_code, email_verify) 
			VALUES (:student_email_address, :student_password, :student_first_name, :student_last_name, :student_date_of_birth, :student_gender, :student_address, :student_phone_no, :student_speciality, :student_added_on, :student_verification_code, :email_verify)
			";

			$object->execute($data);
		}
	
		$output = array(
			
			'error'		=>	$error,
			'success'	=>	$success

			
		);
		
		echo json_encode($output);
	}

	if($_POST['action'] == 'student_login')
	{
		$error = '';

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

			$result = $object->statement_result();

			foreach($result as $row)
			{
				if($row["email_verify"] == 'Yes')
				{
					if($row["student_password"] == $_POST["student_password"])
					{
						$_SESSION['student_id'] = $row['student_id'];
						$_SESSION['student_name'] = $row['student_first_name'] . ' ' . $row['student_last_name'];
					}
					else
					{
						$error = '<div class="alert alert-danger">Wrong Password</div>';
					}
				}
				else
				{
					$error = '<div class="alert alert-danger">Please first verify your email address</div>';
				}
			}
		}
		else
		{
			$error = '<div class="alert alert-danger">Wrong Email Address</div>';
		}

		$output = array(
			'error'		=>	$error
		);

		echo json_encode($output);

	}


	if($_POST['action'] == 'fetch_schedule1')
	{
		$output = array();

		$order_column = array('company_table.company_name', 'internship_table.job_title', 'internship_table.job_description', 'internship_table.start_date', 'internship_table.end_date');
		
		$main_query = "
		SELECT * FROM internship_table 
		INNER JOIN company_table 
		ON company_table.company_id = internship_table.company_id 

		
		";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'AND ( company_table.company_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR internship_table.job_title LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR internship_table.job_description LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR internship_table.start_date LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR internship_table.end_date LIKE "%'.$_POST["search"]["value"].'%" )';
		}
		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY internship_table.start_date ASC ';
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

		$object->query = $main_query . $search_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();


		foreach($result as $row)
		{
			$sub_array = array();

			$sub_array[] = $row["company_name"];

			$sub_array[] = $row["job_title"];

			$sub_array[] = $row["job_description"];

			$sub_array[] = $row["start_date"];

			$sub_array[] = $row["end_date"];


			$sub_array[] = '
			

			<div align="center">
			<button type="button" name="get_application" class="btn btn-primary btn-sm get_application" data-company_id="'.$row["company_id"].'" data-internship_id="'.$row["internship_id"].'">Apply</button>
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
/*
	if($_POST['action'] == 'fetch_schedule2')
	{
		$output = array();

		$order_column = array( 'job_title', 'job_description','start_date', 'end_date');
		$main_query = "
		SELECT * FROM internship_table 
		";

		$search_query = '
		WHERE company_id = "'.$_SESSION["admin_id"].'" AND 
		';
		if(isset($_POST["search"]["value"]))
		{
			$search_query .= '(job_title LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR job_description LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR start_date LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR end_date LIKE "%'.$_POST["search"]["value"].'%") ';


		}
		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY internship_table.start_date ASC ';
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

		$object->query = $main_query . $search_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();


		foreach($result as $row)
		{
			$sub_array = array();

			$sub_array[] = $row["company_name"];

			$sub_array[] = $row["job_title"];

			$sub_array[] = $row["job_description"];

			$sub_array[] = $row["start_date"];

			$sub_array[] = $row["end_date"];

			$sub_array[] = '
			

			<div align="center">
			<button type="button" name="get_application" class="btn btn-primary btn-sm get_application" data-company_id="'.$row["company_id"].'" data-internship_id="'.$row["internship_id"].'">Apply</button>
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
	*/
	if($_POST['action'] == 'edit_profile')
	{
		$data = array(
			':student_password'			=>	$_POST["student_password"],
			':student_first_name'		=>	$_POST["student_first_name"],
			':student_last_name'		=>	$_POST["student_last_name"],
			':student_date_of_birth'	=>	$_POST["student_date_of_birth"],
			':student_gender'			=>	$_POST["student_gender"],
			':student_address'			=>	$_POST["student_address"],
			':student_phone_no'			=>	$_POST["student_phone_no"],
			':student_speciality'	=>	$_POST["student_speciality"]
		);

		$object->query = "
		UPDATE student_table  
		SET student_password = :student_password, 
		student_first_name = :student_first_name, 
		student_last_name = :student_last_name, 
		student_date_of_birth = :student_date_of_birth, 
		student_gender = :student_gender, 
		student_address = :student_address, 
		student_phone_no = :student_phone_no, 
		student_speciality = :student_speciality 
		WHERE student_id = '".$_SESSION['student_id']."'
		";

		$object->execute($data);

		$_SESSION['success_message'] = '<div class="alert alert-success">Profile Data Updated</div>';

		echo 'done';
	}

	if($_POST['action'] == 'make_application')
	{
		$object->query = "
		SELECT * FROM student_table 
		WHERE student_id = '".$_SESSION["student_id"]."'
		";

		$student_data = $object->get_result();

		$object->query = "
		SELECT * FROM internship_table 
		INNER JOIN company_table 
		ON company_table.company_id = internship_table.company_id 
		WHERE internship_table.internship_id = '".$_POST["internship_id"]."'
		";

		$internship_data = $object->get_result();

		$html = '
		<h4 class="text-center">Student Details</h4>
		<table class="table">
		';

		foreach($student_data as $student_row)
		{
			$html .= '
			<tr>
				<th width="40%" class="text-right">Student Name</th>
				<td>'.$student_row["student_first_name"].' '.$student_row["student_last_name"].'</td>
			</tr>
			<tr>
				<th width="40%" class="text-right">Contact No.</th>
				<td>'.$student_row["student_phone_no"].'</td>
			</tr>
			<tr>
				<th width="40%" class="text-right">Address</th>
				<td>'.$student_row["student_address"].'</td>
			</tr>
			';
		}

		$html .= '
		</table>
		<hr />
		<h4 class="text-center">Application Details</h4>
		<table class="table">
		';
		foreach($internship_data as $internship_row)
		{
			$html .= '
			<tr>
				<th width="40%" class="text-right">Company Name</th>
				<td>'.$internship_row["company_name"].'</td>
			</tr>
			<tr>
				<th width="40%" class="text-right">Start Date</th>
				<td>'.$internship_row["start_date"].'</td>
			</tr>
			<tr>
				<th width="40%" class="text-right">End Date</th>
				<td>'.$internship_row["end_date"].'</td>
			</tr>
			';
		}

		$html .= '
		</table>';
		echo $html;
	}

	if($_POST['action'] == 'make_application1')
	{
		$error = '';
		$data = array(
			':student_id'			=>	$_SESSION['student_id'],
			':internship_id'	=>	$_POST['hidden_internship_id']
		);

		$object->query = "
		SELECT * FROM application_table 
		WHERE student_id = :student_id 
		AND internship_id = :internship_id
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">You have already applied for this position, kindly check for the application status in your portal.</div>';
		}
		else
		{
			$object->query = "
			SELECT * FROM internship_table 
			WHERE internship_id = '".$_POST['hidden_internship_id']."'
			";

			$schedule_data = $object->get_result();

			$object->query = "
			SELECT COUNT(application_id) AS total FROM application_table 
			WHERE internship_id = '".$_POST['hidden_internship_id']."' 
			";

			$application_data = $object->get_result();

			$status = '';

			$application_number = $object->Generate_application_no();

			
			$data = array(
				':company_id'				=>	$_POST['hidden_company_id'],
				':student_id'				=>	$_SESSION['student_id'],
				':internship_id'		=>	$_POST['hidden_internship_id'],
				':application_number'		=>	$application_number,
				':reason_for_application'	=>	$_POST['reason_for_application'],
				
				':status'					=>	'Applied'
			);

			$object->query = "
			INSERT INTO application_table 
			(company_id, student_id, internship_id ,application_number, reason_for_application, status) 
			VALUES (:company_id, :student_id, :internship_id, :application_number, :reason_for_application, :status)
			";

			$object->execute($data);

			$_SESSION['application_message'] = '<div class="alert alert-success">Your application has been <b>'.$status.'</b> with application No. <b>'.$application_number.'</b></div>';
		}
		echo json_encode(['error' => $error]);
		
	}



	if($_POST['action'] == 'fetch_application')
	{
		$output = array();

		$order_column = array('application_table.application_number','company_table.company_name', 'internship_table.job_title', 'internship_table.start_date', 'internship_table.end_date', 'application_table.status');
		
		$main_query = "
		SELECT * FROM application_table  
		INNER JOIN company_table 
		ON company_table.company_id = application_table.company_id 
		INNER JOIN internship_table 
		ON internship_table.internship_id = application_table.internship_id 
		
		";

		$search_query = '
		WHERE application_table.student_id = "'.$_SESSION["student_id"].'" 
		';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'AND ( application_table.application_number LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR company_table.company_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR internship_table.job_title LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR internship_table.start_date LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR internship_table.end_date LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR application_table.status LIKE "%'.$_POST["search"]["value"].'%") ';
		}
		
		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY application_table.application_id ASC ';
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

		$object->query = $main_query . $search_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();

			$sub_array[] = $row["application_number"];

			$sub_array[] = $row["company_type"];

			$sub_array[] = $row["job_title"];			

			$sub_array[] = $row["start_date"];

			$sub_array[] = $row["end_date"];

			$status = '';

			if($row["status"] == 'Applied')
			{
				$status = '<span class="badge badge-warning">' . $row["status"] . '</span>';
			}

			if($row["status"] == 'In Review')
			{
				$status = '<span class="badge badge-primary">' . $row["status"] . '</span>';
			}

			if($row["status"] == 'Successful')
			{
				$status = '<span class="badge badge-success">' . $row["status"] . '</span>';
			}

			if($row["status"] == 'Cancel')
			{
				$status = '<span class="badge badge-danger">' . $row["status"] . '</span>';
			}

			$sub_array[] = $status;
			$sub_array[] = '<button type="button" name="cancel_application" class="btn btn-danger btn-sm cancel_application" data-id="'.$row["application_id"].'"><i class="fas fa-times"></i></button>';

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

	if($_POST['action'] == 'cancel_application')
	{
		$data = array(
			':status'			=>	'Cancel',
			':application_id'	=>	$_POST['application_id']
		);
		$object->query = "
		UPDATE application_table 
		SET status = :status 
		WHERE application_id = :application_id
		";
		$object->execute($data);
		echo '<div class="alert alert-success">Your Application has been Canceled</div>';
	}
}



?>