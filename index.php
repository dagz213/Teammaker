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
		<div class="list-group-item">
			<h4 class="list-group-item-heading">Description:</h4><br />
			<p class="list-group-item-text">Team Maker Web App is an app that can be used in a classroom, by group of people and by a company.</p>
			<p class="list-group-item-text">An app that people/students can use to make finding a group easier.</p>
			<br />
		</div>
		<div class="list-group-item">
			<h4 class="list-group-item-heading">Features:</h4><br />
			<p class="list-group-item-text"> - Register / Login</p>
			<p class="list-group-item-text"> - Create a team (once)</p>
			<p class="list-group-item-text"> - Name your own team and give a description</p>
			<p class="list-group-item-text"> - Edit/Delete your group</p>
			<p class="list-group-item-text"> - Join a team with a reason why</p>
			<p class="list-group-item-text"> - Accept a member</p>
			<p class="list-group-item-text"> - Refuse/Kick a member</p>
			<p class="list-group-item-text"> - Discussion inside your Team's page</p>
			<p class="list-group-item-text"> - Register your profile if you haven't done so.</p>
			<p class="list-group-item-text"> - Edit your profile</p>
			<p class="list-group-item-text"> - Have your own personal profile to define yourself</p>
			<p class="list-group-item-text"> - Have a profile picture</p>
			<p class="list-group-item-text"> - Ask/Invite a 5 people at a time to join your team</p>
			<p class="list-group-item-text"> - Refuse an invitation</p>
			<p class="list-group-item-text"> - You can have or join until 5 groups (but only 1 you can create or be a leader in) </p>
			<p class="list-group-item-text"> - Only 7 people in a team (1 Leader / 4 - 6 Members)</p>
			<p class="list-group-item-text"> - Private Messaging</p>
			<p class="list-group-item-text"> - Status/Wall</p>
			<br />
		</div>
		<?php include 'includes/footer.php'; ?>
	</div> <!-- End of main container -->
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>