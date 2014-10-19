var track_click = 0;
var total_pages = $("#postTotalPages").text();

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
  //getMessageList();  
});

/* GET INITIALIZE POSTS */
function getPosts() {
    var groupID = $("#postGroupID").text();
    $('#posts').empty();
    track_click = 0;
    $.ajax({
        url: "includes/actions.php",
        type: "get",
        data: "action=getPost&groupID=" + groupID + "&page=" + track_click,
        success: function(data){
            $('#posts').append(data);
            track_click++;
        },
        error:function(){
            alert('Something went wrong to the request');
        }
    });

    if(total_pages == 0) {
        $("#loadGroupPost").attr("disabled", "disabled");
         $("#loadGroupPost").text("No More Posts");
    }
}

/* GET GROUP DISCUSSION WHEN CLICKED  */
$("#loadGroupPost").click(function(e){
    var groupID = $("#postGroupID").text();
    $(this).hide(); //hide load more button on click
    $('.animation_image').show(); //show loading image

    //make sure user clicks are still less than total pages
    if(track_click <= total_pages) {
        $.ajax({
            url: "includes/actions.php",
            type: "get",
            data: "action=getPost&groupID=" + groupID + "&page=" + track_click,
            success: function(data){

                $("#loadGroupPost").show(); //bring back load more button

                $('#posts').append(data);

                $("html, body").animate({scrollTop: $("#loadGroupPost").offset().top}, 500);

                $('.animation_image').hide(); //hide loading image once data is received

                track_click++;
            },
            error:function(){
                alert(thrownError); //alert any HTTP error
                $("#loadGroupPost").show(); //bring back load more button
                $('.animation_image').hide(); //hide loading image once data is received
            }
        });
    }

    if(track_click >= total_pages - 1) {
        //reached end of the page yet? disable load button
        $("#loadGroupPost").attr("disabled", "disabled");
         $("#loadGroupPost").text("No More Posts");
    }    
});

/* GET INITIALIZE USER POSTS */
function getUserPosts() {
    var userID = $("#postUserID").text();
    $("#userPosts").empty();
    track_click = 0;
    $.ajax({
        url: "includes/actions.php",
        type: "get",
        data: "action=getUserPost&userID=" + userID + "&page=" + track_click,
        success: function(data){
            $('#userPosts').append(data);
            track_click++;
        },
        error:function(){
            alert('Something went wrong to the request');
        }
    });

    if(total_pages == 0) {
        $("#loadUserPost").attr("disabled", "disabled");
         $("#loadUserPost").text("No More Posts");
    }
}

/* GET USER POSTS WHEN CLICKED  */
$("#loadUserPost").click(function(e){
    var userID = $("#postUserID").text();
    $(this).hide(); //hide load more button on click
    $('.animation_image').show(); //show loading image

    //make sure user clicks are still less than total pages
    if(track_click <= total_pages) {
        $.ajax({
            url: "includes/actions.php",
            type: "get",
            data: "action=getUserPost&userID=" + userID + "&page=" + track_click,
            success: function(data){

                $("#loadUserPost").show(); //bring back load more button

                $('#userPosts').append(data);

                $("html, body").animate({scrollTop: $("#loadUserPost").offset().top}, 500);

                $('.animation_image').hide(); //hide loading image once data is received

                track_click++;
            },
            error:function(){
                alert(thrownError); //alert any HTTP error
                $("#loadUserPost").show(); //bring back load more button
                $('.animation_image').hide(); //hide loading image once data is received
            }
        });
    }

    if(track_click >= total_pages - 1) {
        //reached end of the page yet? disable load button
        $(".#loadUserPost").attr("disabled", "disabled");
         $(".#loadUserPost").text("No More Posts");
    }   
});

/* POST USER POSTS */
$('#postUserStatusForm').submit(function(event) {
    event.preventDefault();
    var values = $(this).serialize();
    $("#postStatus").val("");
     $.ajax({
        url: "includes/actions.php",
        type: "post",
        data: values,
        success: function(data){
            if(data === 'POST SUCCESSFUL') {
                getUserPosts();
            } else if(data === 'POST FAILED') {
                alert("POST FAILED");
            } else {
                alert("Connection has died, try again later");
            }
        },
        error:function(){
           alert('Something went wrong with the request!');
        }
    });
});

/* REGISTER */
$('#registrationForm').submit(function(event) {
	/* Stop form from submitting normally */
	event.preventDefault();
	$('#resultMessage').empty();
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
	$('#resultMessage').empty();
	/* Get some values from elements on the page: */
	var values = $(this).serialize();
	/* Send the data using post and put the results in a div */
	 $.ajax({
        url: "includes/actions.php",
        type: "post",
        data: values,
        success: function(data){
            var string = data.split('/');
            if(string[0] === "Login Successful") {
                $('#resultMessage').css('color', 'green');
                if(string[1] === "noregister") {
                    window.location.href = 'groups.php';
                } else if (string[1] === "register") {
                    window.location.href = 'registerprofile.php';
                } 
            } else { 
                $('#resultMessage').css('color', 'red');   
            }
            $('#resultMessage').html(string[0]);
        },
        error:function(){
            $("#resultMessage").html('Something went wrong with the request!');
        }
    });
});

/* REGISTER PROFILE */
$('#profileRegistrationForm').submit(function(event) {
    /* Stop form from submitting normally */
    event.preventDefault();
    $('#resultMessage').empty();
    /* Get some values from elements on the page: */
    var values = $(this).serialize();
    /* Send the data using post and put the results in a div */
     $.ajax({
        url: "includes/actions.php",
        type: "post",
        data: values,
        success: function(data){
            if(data === "Register User Profile Successful") {
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

/* EDIT PROFILE FORM */
$('#editProfileForm').submit(function(event) {
    /* Stop form from submitting normally */
    event.preventDefault();
    $('#resultMessage').empty();
    /* Get some values from elements on the page: */
    var values = $(this).serialize();
    /* Send the data using post and put the results in a div */
     $.ajax({
        url: "includes/actions.php",
        type: "post",
        data: values,
        success: function(data){
            if(data === "Saved") {
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

/* CREATE GROUP */
$('#createGroupForm').submit(function(event) {
    event.preventDefault();
    $('#resultMessage').empty();
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

//to get The reason of joinging
function showUser(str) {
    var groupID = $('#optionGroupID').val();
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState==4 && xmlhttp.status==200) {
            document.getElementById("message").innerHTML=xmlhttp.responseText;
        }
    }

    xmlhttp.open("GET","includes/actions.php?action=getPending&userID=" + str + "&groupID=" + groupID,true);
    xmlhttp.send();
}

var clkBtn = "";
$('input[type="submit"]').click(function(evt) {
    clkBtn = evt.target.name;
});


$('#pendingForm').submit(function(event) {
    event.preventDefault();
    var btnName = clkBtn;
    $('#resultMessage').empty();
    var values = $(this).serialize();
    var groupID = $('#optionGroupID').val();
    var memberString = "";
    if(btnName === 'accept') {
        memberString = "acceptMember";
    } else if(btnName === 'refuse') {
        memberString = "refuseMember";
    }

    $.ajax({
        url: "includes/actions.php",
        type: "GET",
        data: "member=" + memberString + "&" + values,
        success: function(data){
            if(data === "SUCCESS") {
                setTimeout('window.location.href = "viewgroup.php?id=' + groupID + '";', 0);
            } else if(data === "FAIL") {

            }
        },
        error:function(){
            $("#resultMessage").html('Something went wrong with the request!');
        }
    });
});

$('#invitationForm').submit(function(event) {
    event.preventDefault();
    var btnName = clkBtn;
    $('#resultMessage').empty();
    var values = $(this).serialize();
    var groupID = $("#inviteGroupID").val();

    var inviteString = "";
    if(btnName === 'accept') {
        inviteString = "acceptInvite";
    } else if(btnName === 'refuse') {
        inviteString = "refuseInvite";
    }

    $.ajax({
        url: "includes/actions.php",
        type: "GET",
        data: "invite=" + inviteString + "&" + values,
        success: function(data){
            if(data === "SUCCESS") {
                setTimeout('window.location.href = "viewgroup.php?id=' + groupID + '";', 0);
            } else if(data === "FAIL") {
                alert("Something Failed");
            }
        },
        error:function(){
            $("#resultMessage").html('Something went wrong with the request!');
        }
    });
});

$('#modalKick').on('show.bs.modal', function(e) {
    var $modal = $(this),
        esseyId = e.relatedTarget.id;
    var string = esseyId.split('/');
   $modal.find('#kickname').html(string[1]);
   $modal.find('#userID').val(string[0]);
});

$('#modalInvite').on('show.bs.modal', function(e) {
    var $modal = $(this),
        esseyId = e.relatedTarget.id;
   $modal.find('#userIDInvite').val(esseyId);
});

/* Private Messaging */
$('#privateMessageForm').submit(function(event) {
    event.preventDefault();
    $('#resultMessage').empty();
    var values = $(this).serialize();
     $.ajax({
        url: "includes/actions.php",
        type: "post",
        data: values,
        success: function(data){
            if(data === "Sent!") {
                $('#resultMessage').css('color', 'green');
            } else {
                $('#resultMessage').css('color', 'red');   
            }
            $('#resultMessage').html(data);
        },
        error:function(){

        }
    });
});

/************************************
*                                   *
*               INBOX               *
*                                   *
*************************************/

function getMessageList() {
    var yourUserID = $("#yourUserID").text();
    $.get(
        "includes/actions.php",
        { "action" : "getMessageList", "yourUserID" : yourUserID },
        function(data) {
            $("#messageList").append(data);
        }
    );
}

/************************************
*                                   *
*               INBOX               *
*                                   *
*************************************/