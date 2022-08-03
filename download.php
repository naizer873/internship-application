<style>
@media print {
  .noPrint{
    display:none;
  }
}
h2,h4{
  color:blue;
}


@media print {
	@page {
		margin-top: 0;
		margin-bottom: 0;
	}
	body {
		padding-top: 72px;
		padding-bottom: 72px ;
	}
}

</style>
<h2>
<?php

//download.php
include('class/Application.php');

$object = new Application;
require_once('class/pdf.php');

if(isset($_GET["id"]))
{
	$html = '<table border="0" cellpadding="5" cellspacing="5" width="100%">';

	$object->query = "
	SELECT platform_name, platform_address, platform_contact_no, platform_logo 
	FROM admin_table
	";

	$platform_data = $object->get_result();

	foreach($platform_data as $platform_row)
	{
		$html .= '<tr><td align="center">';
		if($platform_row['platform_logo'] != '')
		{
			$html .= '<img src="'.substr($platform_row['platform_logo'], 3).'" /><br />';
		}
		$html .= '<h2 align="center">'.$platform_row['platform_name'].'</h2>
		<p align="center">'.$platform_row['platform_address'].'</p>
		<p align="center"><b>Contact No. - </b>'.$platform_row['platform_contact_no'].'</p></td></tr>
		';
	}
	

	$html .= "
	<tr><td><hr /></td></tr>
	<tr><td>
	";

	$object->query = "
	SELECT * FROM appointment_table 
	WHERE appointment_id = '".$_GET["id"]."'
	";

	$appointment_data = $object->get_result();

	foreach($appointment_data as $appointment_row)
	{

		$object->query = "
		SELECT * FROM patient_table 
		WHERE patient_id = '".$appointment_row["patient_id"]."'
		";

		$patient_data = $object->get_result();

		$object->query = "
		SELECT * FROM doctor_schedule_table 
		INNER JOIN doctor_table 
		ON doctor_table.doctor_id = doctor_schedule_table.doctor_id 
		WHERE doctor_schedule_table.doctor_schedule_id = '".$appointment_row["doctor_schedule_id"]."'
		";

		$doctor_schedule_data = $object->get_result();
		
		$html .= '
		<h4 align="center">Patient Details</h4>
		<table border="0" cellpadding="5" cellspacing="5" width="100%">';

		foreach($patient_data as $patient_row)
		{
			$html .= '<tr><th width="50%" align="right">Patient Name</th><td>'.$patient_row["patient_first_name"].' '.$patient_row["patient_last_name"].'</td></tr>
			<tr><th width="50%" align="right">Contact No.</th><td>'.$patient_row["patient_phone_no"].'</td></tr>
			<tr><th width="50%" align="right">Address</th><td>'.$patient_row["patient_address"].'</td></tr>';
		}

		$html .= '</table><br /><hr />
		<h4 align="center">Appointment Details</h4>
		<table border="0" cellpadding="5" cellspacing="5" width="100%">
			<tr>
				<th width="50%" align="right">Appointment No.</th>
				<td>'.$appointment_row["appointment_number"].'</td>
			</tr>
		';
		foreach($doctor_schedule_data as $doctor_schedule_row)
		{
			$html .= '
			<tr>
				<th width="50%" align="right">Doctor Name</th>
				<td>'.$doctor_schedule_row["doctor_name"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Appointment Date</th>
				<td>'.$doctor_schedule_row["doctor_schedule_date"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Appointment Day</th>
				<td>'.$doctor_schedule_row["doctor_schedule_day"].'</td>
			</tr>
				
			';
		}

		$html .= '
			<tr>
				<th width="50%" align="right">Appointment Time</th>
				<td>'.$appointment_row["appointment_time"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Reason for Appointment</th>
				<td>'.$appointment_row["reason_for_appointment"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Student Come for Interview</th>
				<td>'.$appointment_row["patient_come_into_platform"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Doctor Comment</th>
				<td>'.$appointment_row["doctor_comment"].'</td>
			</tr>
		</table>
			';
	}

	$html .= '
			</td>
		</tr>
	</table>';

	echo $html;
}
?>
</h2>

<Center><a style="color:green; font-size:30px" onclick="window.print();" class="text-success noPrint"><u>
Print </u>
</a>
</Center>