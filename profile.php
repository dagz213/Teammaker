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
<body onload="getUserPosts()">
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
		$un = $_SESSION['username'];
		$yourUserID = $db->getUserID($un);
	?>
	<div class="container" id="mainContainer">
		<div class="row">
		<?php 
		// TO DETERMINE THE PHOTO
		$photoName;
		if($db->checkIfHasProfilePicture($userID)) {
			$photoName = $db->getImageName($userID);
		} else {
			$photoName = "nophoto.jpg";
		}

		$totalPages = ceil($db->getUserPostCountByUserID($userID) / $itemPP);
		?>
			<div id="leftSide" class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<a  href="#myModal" data-toggle="modal" data-target="#modalProfilePic" role="button"><img src="photos/
				<?php 
					echo $photoName;
				?>" id="profilepic"></a>

				<div id="profilepersonalinfo">

					<?php 
						if(isset($_GET['id']) && !empty($_GET['id'])) {
							$groupYouOwn = $db->getGroupsYouOwn($yourUserID);
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

							if($yourUserID !== $userID) {
								echo '<a href="#myModal" data-toggle="modal" data-target="#modalMessage" role="button" class="messageButton btn btn-large btn-primary">Message</a>';
							}
						} else {
							if($db->checkIfHasInvites($userID)) {
								echo "<h3>Invites: </h3>";
								echo "<form id='invitationForm'>";
								echo '<input type="hidden" name="userID" value="', $userID,'">';
								echo "<select id='inviteGroupID' name='groupID'>";
								$invites = $db->getAllYourInvites($userID);
								foreach($invites as $row) {
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
					<h3>Name: <span id="try"></span></h3>
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
				<hr />
				<div id="postUserID" style="display: none;"><?php echo $userID; ?></div>
				<div id="postTotalPages" style="display: none;"><?php echo $totalPages; ?></div>
				<?php if((!isset($_GET['id']) && empty($_GET['id'])) || $_GET['id'] === $yourUserID) { ?>
				<form id="postUserStatusForm" method="post">
					<input type="hidden" name="action" value="postUserStatus">
					<input type="hidden" name="userID" value="<?php echo $userID; ?>">
					<textarea id="postStatus" name="message" placeholder="What's on your mind?"></textarea>
					<input type="submit" id="postSubmit" name="postUserSubmit" value="Post" class="btn btn-large btn-primary">
				</form>
				<?php } ?>
				<div id="userPosts">
					
				</div>
				<div align="center">
					<button class="load_more btn btn-large btn-primary" id="loadUserPost">Load More</button>
					<div class="animation_image" style="display:none;"><img src="ajax-loader.gif"> Loading...</div>
				</div>
			</div> <!-- END OF RIGHT SIDE -->

		</div> <!-- END OF ROW -->

		<div class="modal fade" id="modalProfilePic">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<img src="photos/<?php echo $photoName; ?>">
						<button class="close" data-dismiss="modal">&times;</button>
					</div><!-- end modal-header -->

				</div><!-- end modal-content -->
				<?php if((!isset($_GET['id']) && empty($_GET['id'])) || $_GET['id'] === $yourUserID) { ?>
				<div class="modalFooter">
					<form action="includes/actions.php" method="post" enctype="multipart/form-data">
						<input type="hidden" name="userIDUpload" value="<?php echo $yourUserID; ?>">
						<span class="floatleft">Upload Photo:</span><input id="imageForm" type="file" name="image" class="floatleft">
						<input type="submit" name="upload" value="Upload Profile Picture" class="floatleft">
					</form>
				</div>
				<?php } ?>
			</div><!-- end modal-dialog -->
		</div><!-- end myModal -->

		<div class="modal fade" id="modalInvite">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<?php
							$groupsYouOwn = $db->getGroupsYouOwn($yourUserID);
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

		<div class="modal fade" id="modalMessage">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title text-centered">New Message</h4>
					</div><!-- end modal-header -->

					<div class="modal-body">
					<div>
						<form id="privateMessageForm">
							<input type="hidden" name="action" value="privateMessage">
							<input type="hidden" name="yourUserID" value="<?php echo $yourUserID; ?>">
							<input type="hidden" name="to" value="<?php echo $userID; ?>">
							<textarea id="privateMessageTextArea" name="message" placeholder="What's your message?"></textarea>
							<div class="resultMessageModal text-centered"><span id="resultMessage"></span></div>
							<input class="btn btn-primary" type="submit" value="Send" />
						<button class="btn btn-primary" data-dismiss="modal" type="button">Close</button>
						</form> <!-- END OF MESSAGE FORM -->
					</div>
					</div><!-- end modal-footer -->

				</div><!-- end modal-content -->
			</div><!-- end modal-dialog -->
		</div><!-- END OF MODAL MESSAGE -->

		<?php include 'includes/footer.php'; ?>
	</div> <!-- End of main container -->
	<script type="text/javascript" src="../../js/jquery.js"></script>	
	<script type="text/javascript" src="../../js/bootstrap.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>