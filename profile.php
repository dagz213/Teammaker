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
<body>
	<?php include 'includes/menu.php'; ?>
	<?php 
		$userID;
		if(isset($_GET['id']) && !empty($_GET['id'])) {
			$userID = $_GET['id'];
		} else {
			$username = $_SESSION['username'];
			$userID = $db->getUserID($username);
		}
		$user = $db->getUserByID($userID);
	?>
	<div class="container" id="mainContainer">
		<div class="row">

			<div id="leftSide" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<a  href="#myModal" data-toggle="modal" data-target="#modalProfilePic" role="button"><img src="photos/
				<?php 
					echo "nophoto.jpg";
				?>" id="profilepic"></a>
				<div id="profilepersonalinfo">
					<h3>Name: </h3>
					<h4><?php echo $db->getLeaderName($userID); ?></h4>
					<h3>Email: </h3>
					<h4><?php echo $user['email']; ?></h4>
					<h3>Birthday: </h3>
					<h4><?php echo $user['birthday']; ?></h4>
					<h3>Gender: </h3>
					<h4><?php if($user['gender'] == 'm') echo "Male"; else echo "Female";  ?></h4>
					<h3>Hobbies: </h3>
					<h4><?php echo $user['hobbies']; ?></h4>
					<h3>About: </h3>
					<h4><?php echo $user['about']; ?></h4>
				</div> <!-- END OF PROFILE PERSONAL INFO-->

			</div> <!-- END OF LEFT SIDE -->

			<div id="rightSide" class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
				<div id="bio">
					<div class="bioHeader text-centered">Skills:</div>
					<div class="bioBody text-centered">ekisfugfdujsfgbkujsyhgbf khjbf kgdhfjsgbkljsbfglos bflduhfjs gbldjhf gbljsdhf gb</div>
				</div>
			</div> <!-- END OF RIGHT SIDE -->

		</div> <!-- END OF ROW -->

		<div class="modal fade" id="modalProfilePic">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<img src="photos/nophoto.png">
						<button class="close" data-dismiss="modal">&times;</button>
					</div><!-- end modal-header -->
				</div><!-- end modal-content -->
			</div><!-- end modal-dialog -->
		</div><!-- end myModal -->

		<?php include 'includes/footer.php'; ?>
	</div> <!-- End of main container -->
	<script type="text/javascript" src="../../js/jquery.js"></script>	
	<script type="text/javascript" src="../../js/bootstrap.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>