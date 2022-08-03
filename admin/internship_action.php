<?php

//internship_action.php

include('../class/Application.php');

$object = new Application;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$output = array();

		if($_SESSION['type'] == 'Admin')
		{
			$order_column = array('company_table.company_name', 'internship_table.job_title', 'internship_table.job_description','internship_table.start_date', 'internship_table.end_date');
			$main_query = "
			SELECT * FROM internship_table 
			INNER JOIN company_table 
			ON company_table.company_id = internship_table.company_id 
			";

			$search_query = '';

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'WHERE company_table.company_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR internship_table.job_title LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR internship_table.job_description LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR internship_table.start_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR internship_table.end_date LIKE "%'.$_POST["search"]["value"].'%" ';

						}
		}
		else
		{
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
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY internship_table.internship_id DESC ';
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
			if($_SESSION['type'] == 'Admin')
			{
				$sub_array[] = html_entity_decode($row["company_name"]);
			}
			
			$sub_array[] = $row["job_title"];

			$sub_array[] = $row["job_description"];

			$sub_array[] = $row["start_date"];

			$sub_array[] = $row["end_date"];



			

			$status = '';
			if($row["internship_status"] == 'Active')
			{
				$status = '<button type="button" title="status_button" class="btn btn-primary btn-sm status_button" data-id="'.$row["internship_id"].'" data-status="'.$row["internship_status"].'">Active</button>';
			}
			else
			{
				$status = '<button type="button" title="status_button" class="btn btn-danger btn-sm status_button" data-id="'.$row["internship_id"].'" data-status="'.$row["internship_status"].'">Inactive</button>';
			}

			$sub_array[] = $status;

			$sub_array[] = '
			<div align="center">
			<button type="button" title="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["internship_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" title="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["internship_id"].'"><i class="fas fa-times"></i></button>
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
			':company_id'					=>	$company_id,
			':job_title'	=>	$_POST["job_title"],
			':job_description'		=>	$_POST["job_description"],
			':start_date'			=>$_POST["start_date"],
			':end_date'			=>	$_POST["end_date"]
			
		);

		$object->query = "
		INSERT INTO internship_table 
		(company_id, start_date, end_date, job_title, job_description) 
		VALUES (:company_id, :start_date, :end_date, :job_title, :job_description)
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">Internship Added Successfully</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM internship_table 
		WHERE internship_id = '".$_POST["internship_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['company_id'] = $row['company_id'];
			$data['job_title'] = $row['job_title'];
			$data['job_description'] = $row['job_description'];
			$data['start_date'] = $row['start_date'];
			$data['end_date'] = $row['end_date'];

			
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
			':company_id'					=>	$company_id,
			':job_title'	     =>	$_POST["job_title"],
			':job_description'		=>	$_POST["job_description"],
			':start_date'			=>	$_POST["start_date"],
			':end_date'			=>	$_POST["end_date"]

			
		);


		$object->query = "
		UPDATE internship_table 
		SET company_id = :company_id, 
		job_title = :job_title, 
		job_description = :job_description, 
		start_date = :start_date, 
		end_date = :end_date, 
		WHERE internship_id = '".$_POST['hidden_id']."'
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">Internship Data  Successfully Updated</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$data = array(
			':internship_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE internship_table 
		SET internship_status = :internship_status 
		WHERE internship_id = '".$_POST["id"]."'
		";

		$object->execute($data);

		echo '<div class="alert alert-success">company Schedule Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM internship_table 
		WHERE internship_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">company Schedule has been Deleted</div>';
	}
}

?>