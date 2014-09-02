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
	<div class="container" id="mainContainer">
		<?php include 'includes/title.php'; ?>
		<?php include 'includes/menu.php'; ?>
		<div class="page-header">
		   <h2>Login</h2>
		</div>
		<form class="form-horizontal" id="loginForm">
			<div class="form-group">
				<label class="col-lg-1 control-label" for="inputName">Userame</label>
				<div class="col-lg-4">
					<input type="hidden" name="login" value="login">
					<input name="username" class="form-control" id="inputUsername" placeholder="Username" type="text">
				</div>
			</div> <!-- End of Username form group -->
			
			<div class="form-group">
				<label class="col-lg-1 control-label" for="inputEmail">Password</label>
				<div class="col-lg-4">
					<input name="password" class="form-control" id="inputPassword" placeholder="Password" type="password">
				</div>
			</div> <!-- End of Password form group -->

			<div class="form-group">
				<div class="col-lg-5">
					<div id="resultMessage"></div>
					<input class="btn btn-success pull-right" type="submit" value="Login">
				</div>
			</div> <!-- End of Submit Button form group -->
		</form> <!-- End of login form -->
	</div> <!-- End of main container -->
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>