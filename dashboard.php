<?php

//dashboard.php

include('class/Application.php');

$object = new Application;

include('header.php');

?>

<div class="container-fluid">
	<?php
	include('navbar.php');
	?>
	<br />
	<div class="card">
		<div class="card-header"><h4>Internship List</h4></div>
			<div class="card-body">
				<div class="table-responsive">
		      		<table class="table table-striped table-bordered" id="application_list_table">
		      			<thead>
			      			<tr>
			      				<th>Company Name</th>
			      				<th>Job Title</th>
			      				<th>Job Description</th>
			      				<th>Start Date</th>
								  <th>End Date</th>
				      			<th>Action</th>
			      			</tr>
			      		</thead>
			      		<tbody id="TableBody"></tbody>
			      	</table>
			    </div>
			</div>
		</div>
	</div>

</div>

<?php

include('footer.php');

?>

<div id="applicationModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="application_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Make Application</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div id="application_detail"></div>
                    <div class="form-group">
                    	<label><b>Briefly tell us more about yourself</b></label>
                    	<textarea name="reason_for_application" id="reason_for_application" class="form-control" required rows="5"></textarea>
                    </div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_company_id" id="hidden_company_id" />
          			<input type="hidden" name="hidden_internship_id" id="hidden_internship_id" />
          			<input type="hidden" name="action" id="action" value="make_application1" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-primary" value="Submit" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>


<script>

$(document).ready(function(){

	var dataTable = $('#application_list_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"action.php",
			method:"POST",
			type:"json",
			data:{action:"fetch_schedule1"}
		},
		"columnDefs":[
			{
                "targets": "_all",				
				"orderable":false
			},
		],
	});

	$(document).on('click', '.get_application', function(){

		var internship_id = $(this).data('internship_id');
		var company_id = $(this).data('company_id');

		$.ajax({
			url:"action.php",
			method:"POST",
			data:{action:'make_application', internship_id:internship_id},
			success:function(data)
			{
				$('#applicationModal').modal('show');
				$('#hidden_company_id').val(company_id);
				$('#hidden_internship_id').val(internship_id);
				$('#application_detail').html(data);
			}
		});

	});

	$('#application_form').parsley();

	$('#application_form').on('submit', function(event){

		event.preventDefault();

		if($('#application_form').parsley().isValid())
		{

			$.ajax({
				url:"action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:"json",
				beforeSend:function(){
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					$('#submit_button').val('Submit');
					if(data.error != '')
					{
						$('#form_message').html(data.error);
					}
					else
					{	
						window.location.href="application.php";
					}
				}
			})

		}

	})

});

</script>