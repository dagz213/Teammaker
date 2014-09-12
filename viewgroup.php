<?php 
	ob_start();
	ini_set('display_errors',1);  
	error_reporting(E_ALL);
	require_once(__dir__.'/includes/DBHandler.php');
	$db = new DBHandler();
	if(!$db->isLoggedIn()) {
		header('Location: index.php');
	}

	//Go back to groups.php if not a member and if the id is not set
	$groupID;
	if(isset($_GET['id']) && !empty($_GET['id'])) { 
		$groupID = $_GET['id'];
	} else {
		header('Location: groups.php');
	}
	$username = $_SESSION['username'];
	$userID = $db->getUserID($username);

	if(!$db->checkIfInGroup($groupID, $userID)) {
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
		
</head>
<body>
	<div class="container" id="mainContainer">
		<?php include 'includes/title.php'; ?>
		<?php include 'includes/menu.php'; ?>

		<?php 
			if(isset($_GET['id']) && !empty($_GET['id'])) { 
				$groupID = $_GET['id'];
				$group = $db->getGroupByID($groupID);
				$groupname = $group['groupname'];
				$leaderID = $db->getLeaderID($groupID);
				$leaderName = $db->getLeaderName($leaderID);
				$groupdescription = $group['groupdescription'];
				
		?>
				<div class="page-header">
					<h1><?php echo $groupname; ?><br /><small style="font-size: 20px; letter-spacing: 5px;">by <?php echo $leaderName; ?></small></h1>
					<?php if($db->checkIfLeader($groupID, $userID)) { ?>
					<div class="text-centered">
						<a href="" class="btn btn-large btn-primary" id="alertMe">Edit Group</a>
						<a href="#modalDelete" role="button" class="btn btn-large btn-primary" data-toggle="modal">Delete Group</a>
					</div>
					<?php } ?>
				</div>
				<div id="viewgroupcontent"class="row">
					<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
						<div class="page-header"><h2>Description:</h2></div>
						<p class="text-centered"><?php echo $groupdescription; ?></p>
					</div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3"></div>
					<div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
						<div class="page-header"><h2>Members:</h2></div>
						<h3 class="text-centered"><strong>Leader:</strong></h3>
						<h4 class="text-centered"><?php echo $leaderName; ?></h4>
						<h3 class="text-centered"><strong>Members:</strong></h3>
					</div>
				</div>
		<?php } ?>
		<div class="modal fade" id="modalDelete">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title text-centered">Do you really want to delete <?php echo $groupname;?></h4>
					</div><!-- end modal-header -->

					<div class="modal-body">
					<div>
						<form id="deleteGroupForm" action="includes/actions.php" method="post">
							<input type="hidden" name="deletegroup" value="<?php echo $groupID; ?>">
							<input class="btn btn-primary" type="submit" value="Delete" />
						
						<button class="btn btn-primary" data-dismiss="modal" type="button">Cancel</button>
						</form>
					</div>
					</div><!-- end modal-footer -->

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