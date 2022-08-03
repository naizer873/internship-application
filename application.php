<?php
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
		<span id="message"></span>
		<div class="card-header"><h4>My Application List</h4></div>
			<div class="card-body">
				<div class="table-responsive">
		      		<table  class="table table-striped  table-hover table-bordered" id="application_list_table">
		      			<thead>
			      			<tr>
			      				<th>Application No.</th>
			      				<th>Company Type</th>
			      				<th>Job Title</th>
			      				<th>Start Date</th>
			      				<th>End Date</th>
			      				<th>Application Status</th>
			      				<th>Cancel</th>
			      			</tr>
			      		</thead>
			      		<tbody></tbody>
			      	</table>
			    </div>
			</div>
		</div>
	</div>

</div>

<?php

include('footer.php');

?>


<script>

$(document).ready(function(){

	var dataTable = $('#application_list_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"action.php",
			method:"POST",
			dataType:"json",
			data:{action:'fetch_application'}
		},
		"columnDefs":[
			{
                "targets": "_all",				
				"orderable":false
			},
		],
	});

	$(document).on('click', '.cancel_application', function(){
		var application_id = $(this).data('id');
		if(confirm("Are you sure you want to cancel this application?"))
		{
			$.ajax({
				url:"action.php",
				method:"POST",
				data:{application_id:application_id, action:'cancel_application'},
				success:function(data)
				{
					$('#message').html(data);
					dataTable.ajax.reload();
					setTimeout(function(){
                        $('#message').html('');
                    }, 5000);
				}
			})
		}
	});
	

});

</script>