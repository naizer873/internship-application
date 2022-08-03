<?php

//Application.php

class Application
{
	public $base_url = 'http://localhost/internship-application/';
	public $connect;
	public $query;
	public $statement;
	public $now;

	public function __construct()
	{
		$this->connect = new PDO("mysql:host=localhost;dbname=internship", "root", "");

		date_default_timezone_set('Africa/Nairobi');

		session_start();

		$this->now = date("Y-m-d H:i:s",  STRTOTIME(date('h:i:sa')));
	}

	function execute($data = null)
	{
		$this->statement = $this->connect->prepare($this->query);
		if($data)
		{
			$this->statement->execute($data);
		}
		else
		{
			$this->statement->execute();
		}		
	}

	function row_count()
	{
		return $this->statement->rowCount();
	}

	function statement_result()
	{
		return $this->statement->fetchAll();
	}

	function get_result()
	{
		return $this->connect->query($this->query, PDO::FETCH_ASSOC);
	}

	function is_login()
	{
		if(isset($_SESSION['admin_id']))
		{
			return true;
		}
		return false;
	}

	function is_master_user()
	{
		if(isset($_SESSION['user_type']))
		{
			if($_SESSION["user_type"] == 'Master')
			{
				return true;
			}
			return false;
		}
		return false;
	}

	function clean_input($string)
	{
	  	$string = trim($string);
	  	$string = stripslashes($string);
	  	$string = htmlspecialchars($string);
	  	return $string;
	}

	function Generate_application_no()
	{
		$this->query = "
		SELECT MAX(application_number) as application_number FROM application_table 
		";

		$result = $this->get_result();

		$application_number = 0;

		foreach($result as $row)
		{
			$application_number = $row["application_number"];
		}

		if($application_number > 0)
		{
			return $application_number + 1;
		}
		else
		{
			return '1000';
		}
	}

	function get_total_internship()
	{
		$this->query = "
		SELECT * FROM internship_table 
	
		";
		$this->execute();
		return $this->row_count();
	}

	function get_total_active_internship()
	{
		$this->query = "
		SELECT * FROM internship_table 
		WHERE internship_status = 'Active'
		";
		$this->execute();
		return $this->row_count();
	}

	function get_total_company()
	{
		$this->query = "
		SELECT * FROM company_table 
		";
		$this->execute();
		return $this->row_count();
	}

	function get_total_application()
	{
		$this->query = "
		SELECT * FROM application_table 
		";
		$this->execute();
		return $this->row_count();
	}

	function get_total_student()
	{
		$this->query = "
		SELECT * FROM student_table 
		";
		$this->execute();
		return $this->row_count();
	}

	

	function Get_class_name($class_id)
	{
		$this->query = "
		SELECT class_name FROM class_srms 
		WHERE class_id = '$class_id'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["class_name"];
		}
	}

	function Get_Class_subject($class_id)
	{
		$this->query = "
		SELECT subject_name FROM subject_srms 
		WHERE class_id = '$class_id' 
		AND subject_status = 'Enable'
		";
		$result = $this->get_result();
		$data = array();
		foreach($result as $row)
		{
			$data[] = $row["subject_name"];
		}
		return $data;
	}

	function Get_user_name($user_id)
	{
		$this->query = "
		SELECT * FROM user_srms 
		WHERE user_id = '".$user_id."'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			if($row['user_type'] != 'Master')
			{
				return $row["user_name"];
			}
			else
			{
				return 'Master';
			}
		}
	}

	function Get_exam_name($exam_id)
	{
		$this->query = "
		SELECT exam_name FROM exam_srms 
		WHERE exam_id = '$exam_id'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["exam_name"];
		}
	}

	
	function Get_total_classes()
	{
		$this->query = "
		SELECT COUNT(class_id) as Total 
		FROM class_srms 
		WHERE class_status = 'Enable'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}

	function Get_total_subject()
	{
		$this->query = "
		SELECT COUNT(subject_id) as Total 
		FROM subject_srms 
		WHERE subject_status = 'Enable'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}

	/*function Get_total_student()
	{
		$this->query = "
		SELECT COUNT(student_id) as Total 
		FROM student_srms 
		WHERE student_status = 'Enable'
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}
*/
	function Get_total_exam()
	{
		$this->query = "
		SELECT COUNT(exam_id) as Total 
		FROM exam_srms 
		WHERE exam_status = 'Enable' 
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}

	function Get_total_result()
	{
		$this->query = "
		SELECT COUNT(result_id) as Total 
		FROM result_srms 
		";
		$result = $this->get_result();
		foreach($result as $row)
		{
			return $row["Total"];
		}
	}

}


?>