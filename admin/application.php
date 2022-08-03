<?php

//application.php

include('../class/Application.php');

$object = new Application;

if(!isset($_SESSION['admin_id']))
{
    header('location:'.$object->base_url.'');
}

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Application Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col-sm-6">
                            		<h6 class="m-0 font-weight-bold text-danger">Application List</h6>
                            	</div>
                            	
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="application_table">
                                    <thead>
                                        <tr>
                                            <th>Application No.</th>
                                            <th>Student Name</th>
                                            <?php
                                            if($_SESSION['type'] == 'Admin')
                                            {
                                            ?>
                                            <th>Company Type</th>
                                            <?php
                                            }
                                            ?>
                                            <th>Start Date</th>
                                            <th>Application Status</th>
                                            <th>View</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="edit_application_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">View Application Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div id="application_details"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hidden_application_id" id="hidden_application_id" />
                    <input type="hidden" name="action" value="change_application_status" />
                    <input type="submit" name="save_application" id="save_application" class="btn btn-primary" value="Save" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function(){

    fetch_data('no');

    function fetch_data(is_date_search, start_date='', end_date='')
    {
        var dataTable = $('#application_table').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "ajax" : {
                url:"application_action.php",
                type:"POST",
                data:{
                    is_date_search:is_date_search, start_date:start_date, end_date:end_date, action:'fetch'
                }
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
    }


    $(document).on('click', '.view_button', function(){

        var application_id = $(this).data('id');

        $.ajax({

            url:"application_action.php",

            method:"POST",

            data:{application_id:application_id, action:'fetch_single'},

            success:function(data)
            {
                $('#viewModal').modal('show');

                $('#application_details').html(data);

                $('#hidden_application_id').val(application_id);

            }

        })
    });

    $('.input-daterange').datepicker({
        todayBtn:'linked',
        format: "yyyy-mm-dd",
        autoclose: true
    });

    $('#search').click(function(){
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if(start_date != '' && end_date !='')
        {
            $('#application_table').DataTable().destroy();
            fetch_data('yes', start_date, end_date);
        }
        else
        {
            alert("Both Date is Required");
        }
    });

    $('#refresh').click(function(){
        $('#application_table').DataTable().destroy();
        fetch_data('no');
    });

    $('#edit_application_form').parsley();

    $('#edit_application_form').on('submit', function(event){
        event.preventDefault();
        if($('#edit_application_form').parsley().isValid())
        {       
            $.ajax({
                url:"application_action.php",
                method:"POST",
                data: $(this).serialize(),
                beforeSend:function()
                {
                    $('#save_application').attr('disabled', 'disabled');
                    $('#save_application').val('wait...');
                },
                success:function(data)
                {
                    $('#save_application').attr('disabled', false);
                    $('#save_application').val('Save');
                    $('#viewModal').modal('hide');
                    $('#message').html(data);
                    $('#application_table').DataTable().destroy();
                    fetch_data('no');
                    setTimeout(function(){
                        $('#message').html('');
                    }, 5000);
                }
            })
        }
    });

});
</script>