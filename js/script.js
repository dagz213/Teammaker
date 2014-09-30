$(function() {
    
    $('#alertMe').click(function(e) {
        e.preventDefault();
        $('#successAlert').slideDown();   
    });

    $('#closeAlertMe').click(function(e) {
        e.preventDefault();
        $('.alert').css('display', 'none').css('margin-top', '20px');
    });

    $('#cancelAlertMe').click(function(e) {
        e.preventDefault();
        $('.alert').css('display', 'none').css('margin-top', '20px');
    });

    if($( window ).width() <= 768) {
        $('.numbers').css('display', 'none');
    } else {
        $('.numbers').css('display', 'block');
    }

    $( window ).resize(function() {
        if($( window ).width() <= 768) {
            $('.numbers').css('display', 'none');
        } else {
            $('.numbers').css('display', 'block');
        }
    });

});

/* REGISTER */
$('#registrationForm').submit(function(event) {
	/* Stop form from submitting normally */
	event.preventDefault();
	$('#resultMessage').html('');
	/* Get some values from elements on the page: */
	var values = $(this).serialize();
	/* Send the data using post and put the results in a div */
	 $.ajax({
        url: "includes/actions.php",
        type: "post",
        data: values,
        success: function(data){
            if(data === "Register Successful") {
                $('#resultMessage').css('color', 'green');
            } else {
                $('#resultMessage').css('color', 'red');   
            }
            $('#resultMessage').html(data);
        },
        error:function(){
            $("#resultMessage").html('Something went wrong with the request!');
        }
    });
});

/* LOGIN */
$('#loginForm').submit(function(event) {
	/* Stop form from submitting normally */
	event.preventDefault();
	$('#resultMessage').html('');
	/* Get some values from elements on the page: */
	var values = $(this).serialize();
	/* Send the data using post and put the results in a div */
	 $.ajax({
        url: "includes/actions.php",
        type: "post",
        data: values,
        success: function(data){
            if(data === "Login Successful") {
                $('#resultMessage').css('color', 'green');
                window.location.href = 'groups.php';
            } else {
                $('#resultMessage').css('color', 'red');   
            }
            $('#resultMessage').html(data);
        },
        error:function(){
            $("#resultMessage").html('Something went wrong with the request!');
        }
    });
});

/* CREATE GROUP */
$('#createGroupForm').submit(function(event) {
    event.preventDefault();
    $('#resultMessage').html('');
    var values = $(this).serialize();
     $.ajax({
        url: "includes/actions.php",
        type: "post",
        data: values,
        success: function(data){
            $('#resultMessage').html(data);
            setTimeout('window.location.href = "groups.php";', 1000);
        },
        error:function(){
            $("#resultMessage").html('Something went wrong with the request!');
        }
    });
});

$('#modalJoin').on('show.bs.modal', function(e) {
    
    var $modal = $(this),
        esseyId = e.relatedTarget.id;
    var string = esseyId.split('/');
   $modal.find('#gname').html(string[1]);
   $modal.find('#joingroup').val(string[0]);
});

$('#modalPendingCancel').on('show.bs.modal', function(e) {
    var $modal = $(this),
        esseyId = e.relatedTarget.id;
    var string = esseyId.split('/');
   $modal.find('#pendingcancelgroup').val(string[0]);
});