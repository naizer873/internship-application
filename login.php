<?php

//login.php

include('header.php');

include('class/Application.php');

$object = new Application;

?>

<div class="container">
	<div class="row justify-content-md-center">
		<div class="col col-md-4">
			<?php
			if(isset($_SESSION["success_message"]))
			{
				echo $_SESSION["success_message"];
				unset($_SESSION["success_message"]);
			}
			?>
			<span id="message"></span>
			<div class="card bg-light text-primary">
				<div class="card-header">Login</div>
				<div class="card-body">
					<form method="post" id="student_login_form">
						<div class="form-group">
							<label>Student Email Address</label>
							<input type="text" name="student_email_address" id="student_email_address" class="form-control" required autofocus data-parsley-type="email" data-parsley-trigger="keyup" placeholder="Email Address" />
						</div>
						<div class="form-group">
							<label>Student Password</label>
							<input type="password" name="student_password" id="student_password" class="form-control" required  data-parsley-trigger="keyup" placeholder="Password" />
						</div>
						<div class="form-group text-center">
							<input type="hidden" name="action" value="student_login" />
							<input type="submit" name="student_login_button" id="student_login_button" class="btn btn-success" value="Login" />
						</div>

						<div class="form-group text-center">
							<p><a href="register.php">Register</a></p>
						</div>
					</form>
					
				</div>
			</div>
		</div>
	</div>
</div>
<br><br>
<?php

include('footer.php');

?>


<script>

$(document).ready(function(){

	$('#student_login_form').parsley();

	$('#student_login_form').on('submit', function(event){

		event.preventDefault();

		if($('#student_login_form').parsley().isValid())
		{
			$.ajax({

				url:"action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:"json",
				beforeSend:function()
				{
					$('#student_login_button').attr('disabled', 'disabled');
				},
				success:function(data)
				{
					$('#student_login_button').attr('disabled', false);

					if(data.error != '')
					{
						$('#message').html(data.error);
					}
					else
					{
						window.location.href="dashboard.php";
					}
				}
			});
		}

	});

});



</script>