<?php


//internship.php

include('../class/Application.php');

$object = new Application;
if(!$object->is_login())
{
    header("location:".$object->base_url."admin");
}

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Internship Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-danger">Internship List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_exam" id="add_internship" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="internship_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <?php
                                            if($_SESSION['type'] == 'Admin')
                                            {
                                            ?>
                                            <th>Company Name</th>
                                            <?php
                                            }
                                            ?>
                                            <th>Job Title</th>
                                            <th>Job Description</th>
                                            <th>Start Date</th>
                                            <th>End Date </th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<div id="internshipModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="internship_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Internship</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <?php
                    if($_SESSION['type'] == 'Admin')
                    {
                    ?>
                    <div class="form-group">
                        <label>Select company</label>
                        <select name="company_id" id="company_id" class="form-control" required>
                            <option value="">Select company</option>
                            <?php
                            $object->query = "
                            SELECT * FROM company_table 
                            WHERE company_status = 'Active' 
                            ORDER BY company_name ASC
                            ";

                            $result = $object->get_result();

                            foreach($result as $row)
                            {
                                echo '
                                <option value="'.$row["company_id"].'">'.$row["company_name"].'</option>
                                ';
                            }
                            ?>
                        </select>
                    </div>
                    <?php
                    }
                    ?>
                    <div class="form-group">
                        <label>Job Title</label>
                        <div class="input-group">
                            <div class="input-group-prepend_date">
                               
                            </div>
                            <input type="text" name="job_title" id="job_title" class="form-control" required data-parsley-trigger="keyup" />
                        </div>
                    </div>
		          	<div class="form-group">
		          		<label>Job Description</label>
                        <div class="input-group">
                            <div class="input-group-prepend_date">
                                
                            </div>
		          		    <textarea type="text" name="job_description" id="job_description" class="form-control " required data-parsley-trigger="keyup"></textarea>
                        </div>
		          	</div>

                    <div class="form-group">
                        <label>Start date</label>
                        <div class="input-group">
                            <div class="input-group-prepend_date">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar"></i></span>
                            </div>
                            <input type="text" name="start_date" id="start_date" class="form-control"   required readonly />
                        </div>
                    </div>
                    <div class="form-group">
                        <label>End date</label>
                        <div class="input-group">
                            <div class="input-group-prepend_date">
                                <span class="input-group-text" id="basic-addon1"><i class="fas fa-calendar"></i></span>
                            </div>
                            <input type="text" name="end_date" id="end_date" class="form-control" required readonly/>
                        </div>
                    </div>               
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-3JRrEUwaCkFUBLK1N8HehwQgu8e23jTH4np5NHOmQOobuC4ROQxFwFgBLTnhcnQRMs84muMh0PnnwXlPq5MGjg==" crossorigin="anonymous" />

<script>
$(document).ready(function(){

	var dataTable = $('#internship_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"internship_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
                <?php
                if($_SESSION['type'] == 'Admin')
                {
					?>
					"targets": "_all",
					<?php
                }
                else
                {
					?>
					"targets": "_all",
					<?php
                }
                ?>
				
    			
				"orderable":false,
			},
		],
	});



    $('#start_date').datepicker({
        
        format: "yyyy-mm-dd",
        autoclose: true
    });

    $('#end_date').datepicker({

        format: "yyyy-mm-dd",
        autoclose: true
    });

    $("#start_date").on("change.datepicker", function (e) {
        console.log('test');
        $('#end_date').datepicker('minDate', e.date);
    });

    $("#end_date").on("change.datepicker", function (e) {
        $('#start_date').datepicker('maxDate', e.date);
    });

	$('#add_internship').click(function(){
		
		$('#internship_form')[0].reset();

		$('#internship_form').parsley().reset();

    	$('#modal_title').text('Add Internship Data');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#internshipModal').modal('show');

    	$('#form_message').html('');

	});

	$('#internship_form').parsley();

	$('#internship_form').on('submit', function(event){
		event.preventDefault();
		if($('#internship_form').parsley().isValid())
		{		
			$.ajax({
				url:"internship_action.php",
				method:"POST",
				data:$(this).serialize(),
				dataType:'json',
				beforeSend_date:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					if(data.error != '')
					{
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
						$('#internshipModal').modal('hide');
						$('#message').html(data.success);
						dataTable.ajax.reload();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
		}
	});

	$(document).on('click', '.edit_button', function(){

		var internship_id = $(this).data('id');

		$('#internship_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"internship_action.php",

	      	method:"POST",

	      	data:{internship_id:internship_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{
                <?php
                if($_SESSION['type'] == 'Admin')
                {
                ?>
                $('#company_id').val(data.company_id);
                <?php
                }
                ?>
	        	$('#job_title').val(data.job_title);
				$('#job_description').val(data.job_description);

                $('#start_date').val(data.start_date);

                $('#end_date').val(data.end_date);

	        	$('#modal_title').text('Edit Internship Data');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#internshipModal').modal('show');

	        	$('#hidden_id').val(internship_id);

	      	}

	    })

	});

	$(document).on('click', '.status_button', function(){
		var id = $(this).data('id');
    	var status = $(this).data('status');
		var next_status = 'Active';
		if(status == 'Active')
		{
			next_status = 'Inactive';
		}
		if(confirm("Are you sure you want to "+next_status+" it?"))
    	{

      		$.ajax({

        		url:"internship_action.php",

        		method:"POST",

        		data:{id:id, action:'change_status', status:status, next_status:next_status},

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

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"internship_action.php",

        		method:"POST",

        		data:{id:id, action:'delete'},

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