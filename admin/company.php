<?php

//company.php

include('../class/Application.php');

$object = new Application;

if(!$object->is_login())
{
    header("location:".$object->base_url."admin");
}

if($_SESSION['type'] != 'Admin')
{
    header("location:".$object->base_url."");
}

include('header.php');

?>

                    <!-- Page Heading -->Company Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
							
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-danger">Company List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_company" id="add_company" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="company_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Email Address</th>
                                            <th>Password</th>
                                            <th>Company Name</th>
                                            <th>Company Phone No.</th>
                                            <th>Company Type</th>
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

<div id="companyModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="company_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Company</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Company Email Address <span class="text-danger">*</span></label>
                                <input type="text" name="company_email_address" id="company_email_address" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Company Password <span class="text-danger">*</span></label>
                                <input type="password" name="company_password" id="company_password" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
		          		</div>
		          	</div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Company Name <span class="text-danger">*</span></label>
                                <input type="text" name="company_name" id="company_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Company Phone No. <span class="text-danger">*</span></label>
                                <input type="text" name="company_phone_no" id="company_phone_no" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Company Address </label>
                                <input type="text" name="company_address" id="company_address" class="form-control" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Company Type <span class="text-danger">*</span></label>
                                <input type="text" name="company_type" id="company_type" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Company Image <span class="text-danger">*</span></label>
                        <br />
                        <input type="file" name="company_profile_image" id="company_profile_image" />
                        <div id="uploaded_image"></div>
                        <input type="hidden" name="hidden_company_profile_image" id="hidden_company_profile_image" />
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

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Company Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="company_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

	var dataTable = $('#company_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"company_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[0, 1, 2, 4, 5, 6, 7],
				"orderable":false,
			},
		],
	});


	$('#add_company').click(function(){
		
		$('#company_form')[0].reset();

		$('#company_form').parsley().reset();

    	$('#modal_title').text('Add company');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#companyModal').modal('show');

    	$('#form_message').html('');

	});

	$('#company_form').parsley();

	$('#company_form').on('submit', function(event){
		event.preventDefault();
		if($('#company_form').parsley().isValid())
		{		
			$.ajax({
				url:"company_action.php",
				method:"POST",
				data: new FormData(this),
				dataType:'json',
                contentType: false,
                cache: false,
                processData:false,
				beforeSend:function()
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
						$('#companyModal').modal('hide');
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

		var company_id = $(this).data('id');

		$('#company_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"company_action.php",

	      	method:"POST",

	      	data:{company_id:company_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{

	 
                $('#company_email_address').val(data.company_email_address);
                $('#company_password').val(data.company_password);
                $('#company_name').val(data.company_name);
                $('#uploaded_image').html('<img src="'+data.company_profile_image+'" class="img-fluid img-thumbnail" width="150" />')
                $('#hidden_company_profile_image').val(data.company_profile_image);
                $('#company_phone_no').val(data.company_phone_no);
                $('#company_address').val(data.company_address);
                $('#company_type').val(data.company_type);

	        	$('#modal_title').text('Edit company');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#companyModal').modal('show');

	        	$('#hidden_id').val(company_id);

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

        		url:"company_action.php",

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

    $(document).on('click', '.view_button', function(){
        var company_id = $(this).data('id');

        $.ajax({

            url:"company_action.php",

            method:"POST",

            data:{company_id:company_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><td colspan="2" class="text-center"><img src="'+data.company_profile_image+'" class="img-fluid img-thumbnail" width="150" /></td></tr>';

                html += '<tr><th width="40%" class="text-right">Company Email Address</th><td width="60%">'+data.company_email_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Company Password</th><td width="60%">'+data.company_password+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Company Name</th><td width="60%">'+data.company_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Company Phone No.</th><td width="60%">'+data.company_phone_no+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Company Address</th><td width="60%">'+data.company_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Company Type</th><td width="60%">'+data.company_type+'</td></tr>';

                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#company_details').html(html);

            }

        })
    });

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"company_action.php",

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