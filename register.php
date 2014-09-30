<?php 
	ob_start();
	ini_set('display_errors',1);  
	error_reporting(E_ALL);
	require_once(__dir__.'/includes/DBHandler.php');
	$db = new DBHandler();
	
	if($db->isLoggedIn()) {
		header('Location: groups.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Wyncoding - Teammaker - Home</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
>	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../../css/bootstrap-glyphicons.css" rel="stylesheet">
	<script type="text/javascript" src="../../js/modernizr.custom.79639.js"></script> 
	<script type="text/javascript" src="../../js/jquery.js"></script>	
	<script type="text/javascript" src="../../js/bootstrap.js"></script>	
</head>
<body>
	<?php include 'includes/menu.php'; ?>
	<div class="container" id="mainContainer">
		<div class="page-header">
		   <h2>Register</h2>
		</div>
		<form class="form-horizontal" id="registrationForm">
				<div class="form-group">
					<label class="col-lg-7 control-label" for="inputName">No Sending Email Verifation for Simplicity purposes (but I already have the script for that)</label>
				</div>
				<div class="form-group">
					<label class="col-lg-1 control-label" for="inputName">Userame</label>
					<div class="col-lg-6">
						<input type="hidden" name="registration" value="registration">
						<input name="username" class="form-control" id="inputUsername" placeholder="Username" type="text">
					</div>
				</div> <!-- End of Username form group -->
				
				<div class="form-group">
					<label class="col-lg-1 control-label" for="inputEmail">Password</label>
					<div class="col-lg-6">
						<input name="password" class="form-control" id="inputPassword" placeholder="Password" type="password">
					</div>
				</div> <!-- End of Password form group -->
				
				<div class="form-group">
					<label class="col-lg-1 control-label" for="inputEmail">Confirm Password</label>
					<div class="col-lg-6">
						<input name="confirmpassword" class="form-control" id="inputConfirmPassword" placeholder="Confirm Password" type="password">
					</div>
				</div> <!-- End of Confirm Password form group -->

				<div class="form-group">
					<label class="col-lg-1 control-label" for="inputEmail">Email</label>
					<div class="col-lg-6">
						<input name="email" class="form-control" id="inputEmail" placeholder="Email" type="email">
					</div>
				</div> <!-- End of Email form group -->

				<div class="form-group">
					<div class="col-lg-7">
						<div id="resultMessage"></div>
						<input name="submitRegistration" class="btn btn-success pull-right" type="submit" value="Register">
					</div>
				</div> <!-- End of Submit Button form group -->
			</form> <!-- End of register form -->
			<?php include 'includes/footer.php'; ?>
	</div> <!-- End of main container -->
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>