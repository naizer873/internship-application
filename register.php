<?php

include('header.php');

include('class/Application.php');

$object = new Application;


include('header.php');

?>
<body >
<div class="container">
	<div class="row justify-content-md-center">
		<div class="col col-md-6">
			<span id="message"></span>
			<div class="card bg-light text-primary">
				<div class="card-header">Register Student</div>
				<div class="card-body">
					<form method="post" id="student_register_form">
						<div class="form-group">
							<label>Student Email Address<span class="text-danger">*</span></label>
							<input type="text" name="student_email_address" id="student_email_address" class="form-control" required autofocus data-parsley-type="email" data-parsley-trigger="keyup" />
						</div>
						<div class="form-group">
							<label>Student Password<span class="text-danger">*</span></label>
							<input type="password" name="student_password" id="student_password" class="form-control" required  data-parsley-trigger="keyup" />
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Student First Name<span class="text-danger">*</span></label>
									<input type="text" name="student_first_name" id="student_first_name" class="form-control" required  data-parsley-trigger="keyup" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Student Last Name<span class="text-danger">*</span></label>
									<input type="text" name="student_last_name" id="student_last_name" class="form-control" required  data-parsley-trigger="keyup" />
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Student Date of Birth<span class="text-danger">*</span></label>
									<input type="text" name="student_date_of_birth" id="student_date_of_birth" class="form-control" required  data-parsley-trigger="keyup" readonly />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Student Gender<span class="text-danger">*</span></label>
									<select name="student_gender" id="student_gender" class="form-control">
										<option value="Male">Male</option>
										<option value="Female">Female</option>
										<option value="Other">Other</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Student Contact No.<span class="text-danger">*</span></label>
									<input type="text" name="student_phone_no" id="student_phone_no" class="form-control" required  data-parsley-trigger="keyup" />
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Student Speciality<span class="text-danger">*</span></label>
									<select name="student_speciality" id="student_speciality" class="form-control">
										<option value="it">IT</option>
										<option value="business">Business</option>
										<option value="education">Education</option>
										<option value="laws">Laws</option>
										<option value="medicine">Medicine</option>
										<option value="hospitality">Hospitality</option>
										<option value="media">Media</option>
										
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>Student Complete Address<span class="text-danger">*</span></label>
							<textarea name="student_address" id="student_address" class="form-control" required data-parsley-trigger="keyup"></textarea>
						</div>
						<div class="form-group text-center">
							<input type="hidden" name="action" value="student_register" />
							<input type="submit" name="student_register_button" id="student_register_button" class="btn btn-success" value="Register" />
						</div>

						<div class="form-group text-center">
							<p><a href="login.php">Login</a></p>
						</div>
					</form>
				</div>
			</div>
			<br />
			<br />
		</div>
	</div>
</div>

<?php

include('footer.php');

?>

<script>

$(document).ready(function(){

	$('#student_date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });

	$('#student_register_form').parsley();

	$('#student_register_form').on('submit', function(event){

		event.preventDefault();

		if($('#student_register_form').parsley().isValid())
		{
			$.ajax({
				url:"action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				beforeSend:function(){
					$('#student_register_button').attr('disabled', 'disabled');
				},
				success:function(data)
				{
					$('#student_register_button').attr('disabled', false);
					$('#student_register_form')[0].reset();
					if(data.error !== '')
					{
						$('#message').html(data.error);
					}
					if(data.success != '')
					{
						$('#message').html(data.success);
					}
				}
			});
		}

	});

});

</script>
</body>