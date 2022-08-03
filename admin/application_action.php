<?php
//Application_action.php

include('../class/Application.php');

$object = new Application;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$output = array();

		if($_SESSION['type'] == 'Admin')
		{
			$order_column = array('application_table.application_number', 'student_table.student_first_name', 'company_table.company_name', 'application_table.application_date',  'application_table.status');
			$main_query = "
			SELECT * FROM application_table  
			INNER JOIN company_table 
			ON company_table.company_id = application_table.company_id 
			INNER JOIN internship_table 
			ON internship_table.internship_id = application_table.internship_id 
			INNER JOIN student_table 
			ON student_table.student_id = application_table.student_id 
			";

			$search_query = '';

			if($_POST["is_date_search"] == "yes")
			{
			 	$search_query .= 'WHERE internship_table.start_date BETWEEN "'.$_POST["start_date"].'" AND "'.$_POST["end_date"].'" AND (';
			}
			else
			{
				$search_query .= 'WHERE ';
			}

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'application_table.application_number LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR student_table.student_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR student_table.student_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR company_table.company_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR internship_table.start_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR application_table.status LIKE "%'.$_POST["search"]["value"].'%" ';
			}
			if($_POST["is_date_search"] == "yes")
			{
				$search_query .= ') ';
			}
			else
			{
				$search_query .= '';
			}
		}
		else
		{
			$order_column = array('application_table.application_number', 'student_table.student_first_name', 'internship_table.start_date', 'application_table.status');

			$main_query = "
			SELECT * FROM application_table 
			INNER JOIN internship_table 
			ON internship_table.internship_id = application_table.internship_id 
			INNER JOIN student_table 
			ON student_table.student_id = application_table.student_id 
			";

			$search_query = '
			WHERE application_table.company_id = "'.$_SESSION["admin_id"].'" 
			';

			if($_POST["is_date_search"] == "yes")
			{
			 	$search_query .= 'AND internship_table.start_date BETWEEN "'.$_POST["start_date"].'" AND "'.$_POST["end_date"].'" ';
			}
			else
			{
				$search_query .= '';
			}

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'AND (application_table.application_number LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR student_table.student_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR student_table.student_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR internship_table.start_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR application_table.status LIKE "%'.$_POST["search"]["value"].'%") ';
			}
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY application_table.application_id DESC ';
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

			$sub_array[] = $row["student_first_name"] . ' ' . $row["student_last_name"];

			if($_SESSION['type'] == 'Admin')
			{
				$sub_array[] = $row["company_type"];
			}
			$sub_array[] = $row["start_date"];

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

			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["application_id"].'"><i class="fas fa-eye"></i></button>
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

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM application_table 
		WHERE application_id = '".$_POST["application_id"]."'
		";

		$application_data = $object->get_result();

		foreach($application_data as $application_row)
		{

			$object->query = "
			SELECT * FROM student_table 
			WHERE student_id = '".$application_row["student_id"]."'
			";

			$student_data = $object->get_result();

			$object->query = "
			SELECT * FROM internship_table 
			INNER JOIN company_table 
			ON company_table.company_id = internship_table.company_id 
			WHERE internship_table.internship_id = '".$application_row["internship_id"]."'
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
				<tr>
					<th width="40%" class="text-right">Application No.</th>
					<td>'.$application_row["application_number"].'</td>
				</tr>
			';
			foreach($internship_data as $internship_row)
			{
				$html .= '
				<tr>
					<th width="40%" class="text-right">Company Name</th>
					<td>'.$internship_row["company_name"].'</td>
				</tr>
				';
			}

			$html .= '

				<tr>
					<th width="40%" class="text-right">Student Application Summary</th>
					<td>'.$application_row["reason_for_application"].'</td>
				</tr>
			';

			if($application_row["status"] != 'Cancel')
			{
				if($_SESSION['type'] == 'Admin')
				{
					if($application_row['student_come_for_interview'] == 'Yes')
					{
						if($application_row["status"] == 'Successful')
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">Student come for Interview</th>
									<td>Yes</td>
								</tr>
								<tr>
									<th width="40%" class="text-right">Company Comment</th>
									<td>'.$application_row["company_comment"].'</td>
								</tr>
							';
						}
						else
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">Student come for interview</th>
									<td>
										<select name="student_come_for_interview" id="student_come_for_interview" class="form-control" required>
											<option value="">Select</option>
											<option value="Yes" selected>Yes</option>
										</select>
									</td>
								</tr
							';
						}
					}
					else
					{
						$html .= '
							<tr>
								<th width="40%" class="text-right">Student come for interview</th>
								<td>
									<select name="student_come_for_interview" id="student_come_for_interview" class="form-control" required>
										<option value="">Select</option>
										<option value="Yes">Yes</option>
									</select>
								</td>
							</tr
						';
					}
				}

				if($_SESSION['type'] == 'company')
				{
					if($application_row["student_come_for_interview"] == 'Yes')
					{
						
						if($application_row["status"] == 'Successful')
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">Company Comment</th>
									<td>
										<textarea name="company_comment" id="company_comment" class="form-control" rows="8" required>'.$application_row["company_comment"].'</textarea>
									</td>
								</tr
							';
						}
						else
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">Company Comment</th>
									<td>
										<textarea name="company_comment" id="company_comment" class="form-control" rows="8" required></textarea>
									</td>
								</tr
							';
						}
					}
				}

				
			
			}

			$html .= '
			</table>
			';
		}

		echo $html;
	}

	if($_POST['action'] == 'change_application_status')
	{
		if($_SESSION['type'] == 'Admin')
		{
			$data = array(
				':status'							=>	'In Review',
				':student_come_for_interview'		=>	'Yes',
				':application_id'					=>	$_POST['hidden_application_id']
			);

			$object->query = "
			UPDATE application_table 
			SET status = :status, 
			student_come_for_interview = :student_come_for_interview 
			WHERE application_id = :application_id
			";

			$object->execute($data);

			echo '<div class="alert alert-success">Application Status change to In Review</div>';
		}

		if($_SESSION['type'] == 'company')
		{
			$data = array(
				':status'							=>	'In Review',
				':student_come_for_interview'		=>	'Yes',
				':application_id'					=>	$_POST['hidden_application_id']
			);

			$object->query = "
			UPDATE application_table 
			SET status = :status, 
			student_come_for_interview = :student_come_for_interview 
			WHERE application_id = :application_id
			";

			$object->execute($data);

			echo '<div class="alert alert-success">Application Status change to In Review</div>';
		}


		if($_SESSION['type'] == 'company')
		{
			if(isset($_POST['company_comment']))
			{
				$data = array(
					':status'							=>	'Successful',
					':company_comment'					=>	$_POST['company_comment'],
					':application_id'					=>	$_POST['hidden_application_id']
				);

				$object->query = "
				UPDATE application_table 
				SET status = :status, 
				company_comment = :company_comment 
				WHERE application_id = :application_id
				";

				$object->execute($data);

				echo '<div class="alert alert-success">Application Successful</div>';
			}
		}
	}
	

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM internship_table 
		WHERE internship_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Company Internship has been Deleted</div>';
	}
}

?>