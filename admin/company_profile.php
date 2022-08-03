<?php

include('../class/Application.php');

$object = new Application;

if(!$object->is_login())
{
    header("location:".$object->base_url."");
}

if($_SESSION['type'] != 'company')
{
    header("location:".$object->base_url."");
}

$object->query = "
    SELECT * FROM company_table
    WHERE company_id = '".$_SESSION["admin_id"]."'
    ";

$result = $object->get_result();

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Profile</h1>

                    <!-- DataTales Example -->
                    
                    <form method="post" id="profile_form" enctype="multipart/form-data">
                        <div class="row"><div class="col-md-10"><span id="message"></span><div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-danger">Profile</h6>
                                    </div>
                                    <div clas="col" align="right">
                                        <input type="hidden" name="action" value="company_profile" />
                                        <input type="hidden" name="hidden_id" id="hidden_id" />
                                        <button type="submit" name="edit_button" id="edit_button" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!--<div class="row">
                                    <div class="col-md-6">!-->
                                        <span id="form_message"></span>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>company Email Address <span class="text-danger">*</span></label>
                                                    <input type="text" name="company_email_address" id="company_email_address" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label>company Password <span class="text-danger">*</span></label>
                                                    <input type="password" name="company_password" id="company_password" class="form-control" required  data-parsley-trigger="keyup" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>company Name <span class="text-danger">*</span></label>
                                                    <input type="text" name="company_name" id="company_name" class="form-control" required data-parsley-trigger="keyup" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label>company Phone No. <span class="text-danger">*</span></label>
                                                    <input type="text" name="company_phone_no" id="company_phone_no" class="form-control" required  data-parsley-trigger="keyup" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>company Address </label>
                                                    <input type="text" name="company_address" id="company_address" class="form-control" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label>company Date of Birth </label>
                                                    <input type="text" name="company_date_of_birth" id="company_date_of_birth" readonly class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>company Degree <span class="text-danger">*</span></label>
                                                    <input type="text" name="company_degree" id="company_degree" class="form-control" required data-parsley-trigger="keyup" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label>company Speciality <span class="text-danger">*</span></label>
                                                    <input type="text" name="company_expert_in" id="company_expert_in" class="form-control" required  data-parsley-trigger="keyup" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>company Image <span class="text-danger">*</span></label>
                                            <br />
                                            <input type="file" name="company_profile_image" id="company_profile_image" />
                                            <div id="uploaded_image"></div>
                                            <input type="hidden" name="hidden_company_profile_image" id="hidden_company_profile_image" />
                                        </div>
                                    <!--</div>
                                </div>!-->
                            </div>
                        </div></div></div>
                    </form>
                <?php
                include('footer.php');
                ?>

<script>
$(document).ready(function(){

    $('#company_date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });

    <?php
    foreach($result as $row)
    {
    ?>
    $('#hidden_id').val("<?php echo $row['company_id']; ?>");
    $('#company_email_address').val("<?php echo $row['company_email_address']; ?>");
    $('#company_password').val("<?php echo $row['company_password']; ?>");
    $('#company_name').val("<?php echo $row['company_name']; ?>");
    $('#company_phone_no').val("<?php echo $row['company_phone_no']; ?>");
    $('#company_address').val("<?php echo $row['company_address']; ?>");
    $('#company_date_of_birth').val("<?php echo $row['company_date_of_birth']; ?>");
    $('#company_degree').val("<?php echo $row['company_degree']; ?>");
    $('#company_expert_in').val("<?php echo $row['company_expert_in']; ?>");
    
    $('#uploaded_image').html('<img src="<?php echo $row["company_profile_image"]; ?>" class="img-thumbnail" width="100" /><input type="hidden" name="hidden_company_profile_image" value="<?php echo $row["company_profile_image"]; ?>" />');

    $('#hidden_company_profile_image').val("<?php echo $row['company_profile_image']; ?>");
    <?php
    }
    ?>

    $('#company_profile_image').change(function(){
        var extension = $('#company_profile_image').val().split('.').pop().toLowerCase();
        if(extension != '')
        {
            if(jQuery.inArray(extension, ['png','jpg']) == -1)
            {
                alert("Invalid Image File");
                $('#company_profile_image').val('');
                return false;
            }
        }
    });

    $('#profile_form').parsley();

	$('#profile_form').on('submit', function(event){
		event.preventDefault();
		if($('#profile_form').parsley().isValid())
		{		
			$.ajax({
				url:"profile_action.php",
				method:"POST",
				data:new FormData(this),
                dataType:'json',
                contentType:false,
                processData:false,
				beforeSend:function()
				{
					$('#edit_button').attr('disabled', 'disabled');
					$('#edit_button').html('wait...');
				},
				success:function(data)
				{
					$('#edit_button').attr('disabled', false);
                    $('#edit_button').html('<i class="fas fa-edit"></i> Edit');

                    $('#company_email_address').val(data.company_email_address);
                    $('#company_password').val(data.company_password);
                    $('#company_name').val(data.company_name);
                    $('#company_phone_no').val(data.company_phone_no);
                    $('#company_address').text(data.company_address);
                    $('#company_date_of_birth').text(data.company_date_of_birth);
                    $('#company_degree').text(data.company_degree);
                    $('#company_expert_in').text(data.company_expert_in);
                    if(data.company_profile_image != '')
                    {
                        $('#uploaded_image').html('<img src="'+data.company_profile_image+'" class="img-thumbnail" width="100" />');

                        $('#user_profile_image').attr('src', data.company_profile_image);
                    }

                    $('#hidden_company_profile_image').val(data.company_profile_image);
						
                    $('#message').html(data.success);

					setTimeout(function(){

				        $('#message').html('');

				    }, 5000);
				}
			})
		}
	});

});
</script>