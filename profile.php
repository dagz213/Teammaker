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
					<?php 
						if(isset($_GET['id']) && !empty($_GET['id'])) {
							$un = $_SESSION['username'];
							$yourUserID = $db->getUserID($un);
							$groupYouOwn = mysql_fetch_array($db->getGroupsYouOwn($yourUserID));
							$groupID = $groupYouOwn['groupID'];

							if($db->checkIfHasGroupANDLeader($yourUserID)) {

								if($db->checkIfInGroup($groupID, $userID)) {
									echo '<a href="#myModal" class="btn btn-large btn-primary">Already In Your Group</a>';
								} else if(!$db->checkIfAlreadyInvited($groupID, $userID)) {

									if($db->checkIfInviteLimit($groupID)) {
										echo '<a href="#myModal" class="btn btn-large btn-primary">Already Invite Limit</a>';
									} else {
										echo '<a href="#myModal" data-toggle="modal" data-target="#modalInvite" role="button" id="', $userID,'" class="profileButton btn btn-large btn-primary">Invite</a>';
									}

								} else {
									echo '<a href="#myModal" data-toggle="modal" data-target="#modalInviteCancel" role="button" id="', $userID,'" class="profileButton btn btn-large btn-primary">Already Invited</a>';
								}
							}
						} else {
							if($db->checkIfHasInvites($userID)) {
								echo "<h3>Invites: </h3>";
								echo "<form id='invitationForm'>";
								echo '<input type="hidden" name="userID" value="', $userID,'">';
								echo "<select id='inviteGroupID' name='groupID'>";
								$invites = $db->getAllYourInvites($userID);
								while($row = mysql_fetch_array($invites)) {
									$groupID = $row['groupID'];
									$groupname = $db->getGroupNameByID($groupID);
									echo '<option value="', $groupID,'">', $groupname,'</option>';
								}
								echo "</select>";
								echo '<input type="submit" id="acceptSubmit" name="accept" value="Accept" class="profileButton btn btn-large btn-primary">';
			        			echo '<input type="submit" id="refuseSubmit" name="refuse" value="Refuse" class="profileButton btn btn-large btn-primary">';
								echo "</form>";
							}
						}
					?>
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
						<img src="photos/nophoto.jpg">
						<button class="close" data-dismiss="modal">&times;</button>
					</div><!-- end modal-header -->
				</div><!-- end modal-content -->
			</div><!-- end modal-dialog -->
		</div><!-- end myModal -->

		<div class="modal fade" id="modalInvite">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<?php
							$groupsYouOwn = mysql_fetch_array($db->getGroupsYouOwn($yourUserID));
							$groupID = $groupsYouOwn['groupID'];
							$groupname = $db->getGroupNameByID($groupID);
						?>
						<h4 class="modal-title text-centered">Invite to group "<?php echo $groupname; ?>"?</h4>
					</div><!-- end modal-header -->

					<div class="modal-body">
					<div>
						<form id="inviteToGroupForm" action="includes/actions.php" method="post">
							<input id="userIDInvite" type="hidden" name="invitetogroup">
							<input type="hidden" name="groupID" value="<?php echo $groupID; ?>">
							<input class="btn btn-primary" type="submit" value="Invite" />
						<button class="btn btn-primary" data-dismiss="modal" type="button">Cancel</button>
						</form> <!-- END OF INVITE TO GROUP FORM -->
					</div>
					</div><!-- end modal-footer -->

				</div><!-- end modal-content -->
			</div><!-- end modal-dialog -->
		</div><!-- END OF MODAL INVITE -->

		<?php include 'includes/footer.php'; ?>
	</div> <!-- End of main container -->
	<script type="text/javascript" src="../../js/jquery.js"></script>	
	<script type="text/javascript" src="../../js/bootstrap.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>