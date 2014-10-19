<?php 
	ob_start();
	ini_set('display_errors',1);  
	error_reporting(E_ALL);
	require_once(__dir__.'/includes/DBHandler.php');
	$db = new DBHandler();
	if(!$db->isLoggedIn()) {
		header('Location: index.php');
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Wyncoding - Teammaker - Home</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">	
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../../css/bootstrap-glyphicons.css" rel="stylesheet">
	<script type="text/javascript" src="../../js/modernizr.custom.79639.js"></script> 
		
</head>
<body onload="getMessageList()">
	<?php include 'includes/menu.php'; ?>
	<?php 
		$un = $_SESSION['username'];
		$yourUserID = $db->getUserID($un);
	?>
	<div class="container" id="mainContainer">
		<div class="row">
			<div id="yourUserID" style="display:none;"><?php echo $yourUserID; ?></div>
			<div id="inboxLeftPanel" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<div class="page-header">
					<h1 class="text-centered">Messages</h1>
				</div>
				<div id="messageList" class="list-group">
					<!--
					<a class="list-group-item messages">
						<h1 class="list-group-item-heading">Marlon Sidlacan</h1>
						<h4>on: July 31</h4>
					</a> -->
				</div>
			</div>
			<div id="inboxRightPanel" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
				
			</div>
		</div>
	</div> <!-- End of main container -->
	<?php include 'includes/footer.php'; ?>
	<script type="text/javascript" src="../../js/jquery.js"></script>	
	<script type="text/javascript" src="../../js/bootstrap.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>