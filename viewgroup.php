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
<body onload="getPost()">
	<?php include 'includes/menu.php'; ?>
	<div class="container" id="mainContainer">

		<?php 
			$username = $_SESSION['username'];
			$yourUserID = $db-> getUserID($username);
			if(isset($_GET['id']) && !empty($_GET['id'])) { 
				$groupID = $_GET['id'];
				$group = $db->getGroupByID($groupID);
				$groupname = $group['groupname'];
				$leaderID = $db->getLeaderID($groupID);
				$leaderName = $db->getLeaderName($leaderID);
				$groupdescription = $group['groupdescription'];
				$pendingCount = $db->getRequestCount($groupID);
				
		?>
				<div class="page-header">
					<h1 id="groupname" class="text-centered"><?php echo $groupname; ?><br /><small>by <?php echo $leaderName; ?></small></h1>
					<?php if($db->checkIfLeader($groupID, $userID)) { ?>
					<div class="text-centered">
						<a href="#modalEdit" role="button" class="btn btn-large btn-primary" data-toggle="modal">Edit Group</a>
						<a href="#modalDelete" role="button" class="btn btn-large btn-primary" data-toggle="modal">Delete Group</a>
					</div>
					<?php } ?>
				</div>
				<div id="viewgroupcontent" class="row">

					<div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
						<div class="sectionTitle text-centered"><h2>Description:</h2></div>
						<hr />
						<p class="text-centered"><?php echo $groupdescription; ?></p>
						<hr />
						<div id="groupdiscussion">
							<h1 class="text-centered">DISCUSSION</h1>
							<form id="postDiscussionForm" method="post">
								<input type="hidden" name="action" value="postDiscussion">
								<input type="hidden" name="groupID" value="<?php echo $groupID; ?>">
								<input type="hidden" name="userID" value="<?php echo $yourUserID; ?>">
								<textarea id="postMessage" name="message" placeholder="Post or Reply"></textarea>
								<input type="submit" id="postSubmit" name="postSubmit" value="Post" class="btn btn-large btn-primary">
							</form>
							<div id="postGroupID" style="display: none;"><?php echo $groupID; ?></div>
							<div id="posts" class="list-group">
								
							</div>
						</div> <!-- END OF GROUP DISCUSSION -->
					</div> <!-- END OF VIEW GROUP CONTENT LEFT SIDE -->

					<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
						<div class="sectionTitle text-centered"><h2>Members:</h2></div>
						<hr />
						<h3 class="text-centered"><strong>Leader:</strong></h3>
						<hr />
						<h4 class="text-centered"><a href="profile.php?id=<?php echo $leaderID; ?>"><?php echo $leaderName; ?></a></h4>
						<hr />
						<h3 class="text-centered"><strong>Members:</strong></h3>
						<hr />
						<?php 
	        				$result = $db->getMembers($groupID);
	        				while($member = mysql_fetch_array($result)) {
	        					$userID = $member['userID'];
	        					$user = $db->getLeaderName($userID);
	        					echo '
	        						<h4 class="text-centered"><a href="profile.php?id=', $userID,'">', $user,'</a>';
	        					if($db->checkIfLeader($groupID, $yourUserID)) {
	        						echo '(<a href="#myModal" data-toggle="modal" data-target="#modalKick" role="button" id="', $userID,'/', $user,'">Kick?</a>)';
	        					}
	        					echo '
	        						</h4>
	        					';
	        				}
	        			?>
						<hr />
						<?php if($db->checkIfLeader($groupID, $yourUserID)) { ?>
						<h3 class="text-centered"><strong>Pending:</strong></h3>
						<hr />
						<form id="pendingForm" method="post">
							<input type="hidden" name="optionGroupID" id="optionGroupID" value="<?php echo $groupID; ?>">
			        		<select name="pendings" id="pendings" onchange="showUser(this.value);">
			        			<option value="" selected="true">Select the best candidate to accept: <?php echo $pendingCount; ?></option>
			        			<?php 
			        				$result = $db->getPendings($groupID);
			        				while($pendings = mysql_fetch_array($result)) {
			        					$userID = $pendings['userID'];
			        					$user = $db->getLeaderName($userID);
			        					echo '
			        						<option value="', $userID,'">', $user,'</option>
			        					';
			        				}
			        			?>
			        		</select>
			        		<h3 class="text-centered"><strong>Reason of joining:</strong></h3>
			        		<h4 id="message" class="text-centered"></h4>
			        		<input type="submit" id="acceptSubmit" name="accept" value="Accept" class="btn btn-large btn-primary acceptButton">
			        		<input type="submit" id="refuseSubmit" name="refuse" value="Refuse" class="btn btn-large btn-primary">
			        	</form>
			        	<div id="resultMessage"></div>
						<hr />
						<?php } ?>
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

		<div class="modal fade" id="modalEdit">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title text-centered">Edit <?php echo $groupname;?></h4>
					</div><!-- end modal-header -->

					<div class="modal-body">
					<div>
						<form id="editGroupForm" action="includes/actions.php" method="post">
							<input id="editGroupName" type="text" name="groupname" value="<?php echo $groupname;?>">
							<textarea id="editGroupDescription" name="groupdescription" class="form-control" rows="6"><?php echo $groupdescription;?></textarea>
							<input type="hidden" name="editgroup" value="<?php echo $groupID; ?>">
							<input class="btn btn-primary" type="submit" value="Edit" />
						<button class="btn btn-primary" data-dismiss="modal" type="button">Cancel</button>
						</form>
					</div>
					</div><!-- end modal-footer -->

				</div><!-- end modal-content -->
			</div><!-- end modal-dialog -->
		</div><!-- end myModal -->

		<div class="modal fade" id="modalKick">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title text-centered">Are you sure to kick <span id="kickname"></span> </h4>
					</div><!-- end modal-header -->

					<div class="modal-body">
					<div>
						<form id="kickMemberForm" action="includes/actions.php" method="post">
							<input type="hidden" name="kickMember" value="<?php echo $groupID; ?>">
							<input type="hidden" id="userID" name="userID">
							<input class="btn btn-primary" type="submit" value="Kick" />
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