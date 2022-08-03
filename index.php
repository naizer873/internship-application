<style>
#cover {
	height: 390px;

  	background-color:grey;
  	background-position: center;
  	background-image: url("images/intern10.jpg");
	background-repeat: no-repeat;
	background-size: 1600px 310px;
}
	</style>
<?php

//dashboard.php

include('class/Application.php');

$object = new Application;

include('header.php');

?>

<div class="m-2"id="cover"></div>

<?php

include('footer.php');

?>

<div id="applicationModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="application_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Make application</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div id="application_detail"></div>
                    <div class="form-group">
                    	<label><b>Upload your cv:</b></label>
                    	<textarea name="reason_for_application" id="reason_for_application" class="form-control" required rows="5"></textarea>
                    </div>
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_company_id" id="hidden_company_id" />
          			<input type="hidden" name="hidden_internship_id" id="hidden_internship_id" />
          			<input type="hidden" name="action" id="action" value="apply_application" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="apply" />
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
		type:"POST",
		data:{action:'fetch_schedule1'}
	},
	"columnDefs":[
		{
			"targets":[6],				
			"orderable":false,
		},
	],
});


$(document).ready(function(){
	$(document).on('click', '.get_application', function(){
		var action = 'check_login';
		var internship_id = $(this).data('id');
		$.ajax({
			url:"action.php",
			method:"POST",
			data:{action:action, internship_id:internship_id},
			success:function(data)
			{
				window.location.href=data;
			}
		})
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
			$('#submit_button').val('apply');
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