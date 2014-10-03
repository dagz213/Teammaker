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
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"
>	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link href="../../bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="../../css/bootstrap-glyphicons.css" rel="stylesheet">
	<script type="text/javascript" src="../../js/modernizr.custom.79639.js"></script> 
		
</head>
<body>
	<?php 
		$username = $_SESSION['username'];
		$yourUserID = $db-> getUserID($username);
	?>
	<?php include 'includes/menu.php'; ?>
	<div class="container" id="mainContainer">	
		<div class="alert alert-success alert-block fade in" id="successAlert">
			<button type="button" class="close" id="closeAlertMe">&times;</button>
			
			<form class="form-horizontal" id="createGroupForm">
				<div class="form-group">
					<label class="col-lg-2 control-label" for="inputName">Group Name</label>
					<div class="col-lg-4">
						<input type="hidden" name="creategroup" value="creategroup">
						<input name="groupname" class="form-control" placeholder="Group Name" type="text">
					</div>
				</div> <!-- End of Username form group -->
				
				<div class="form-group">
					<label class="col-lg-2 control-label" for="inputEmail">Group Description</label>
					<div class="col-lg-4">
						<textarea name="groupdescription" class="form-control" placeholder="Description" rows="6"></textarea>
					</div>
				</div> <!-- End of Password form group -->

				<div class="form-group">
					<label class="col-lg-2 control-label" for="inputEmail">Private or Public?</label>
					<div class="col-lg-4">
						<select name="groupstatus">
							<option value="Public" selected>Public</option>
							<option value="Private">Private</option>
						</select>
					</div>
				</div>

				<div class="form-group">
					<div class="col-lg-6">
						<div id="resultMessage"></div>
						<a href="" class="btn btn-success pull-right" id="cancelAlertMe" style="margin-left: 20px;">Cancel</a>
						<input class="btn btn-success pull-right" type="submit" value="Create">
					</div>
				</div> <!-- End of Submit Button form group -->
			</form> <!-- End of Create group form -->
		</div><!-- end alert -->

		<a href="" class="btn btn-large btn-primary" id="alertMe">Create Group</a>
		<hr />
		<div class="list-group">
		<?php 
			ob_start();
			$groups;
			$maxPerPage = 5;
			$count =  mysql_num_rows($db->getAllGroups());

			$pn = 0;
			if($count > 0) {
			$totalpages = ceil($count / $maxPerPage); 
			if(isset($_GET['page'])) {
				$pn = $_GET['page'];
			} else {
				$pn = 1;
			}

			if($pn < 1) {
				$pn = 1;
			} else if ($pn > $totalpages) {
				$pn = $totalpages;
			}

			$start = ($pn - 1) * $maxPerPage;

			if($db->getGroupsByPage($start, $maxPerPage)) {
				$groups = $db->getGroupsByPage($start, $maxPerPage);
			} else {

			}

			while($row = mysql_fetch_array($groups)) {
				$groupID = $row['groupID'];
				$groupname = $row['groupname'];
				$userID = $db->getLeaderID($groupID);
				$leaderName = $db->getLeaderName($userID);
				$groupCount = $db->getGroupCount($groupID);

				$maxMembers = 7;
				$maxGroups = 5;

				echo 
				'<div class="list-group-item">
					<div class="groupdescription">
						<h1 class="list-group-item-heading groupTitle">', $groupname,' (', $groupCount,')</h1>
						<p class="list-group-item-text groupList">', $row['groupdescription'],'
						</p>
						<p class="list-group-item-text groupList">Leader: ', $leaderName,'</p>
					</div> <!-- END OF GROUP DESCRIPTION -->
					<div class="groupbuttons">
				';
				//Group Buttons
				if($db->getYourGroupCount($yourUserID) <= $maxGroups) {

					if($db->checkIfInGroup($groupID, $yourUserID)) {
						//Already Joinged
						echo '<a href="" class="btn btn-large btn-primary" id="alreadyJoinedButton">Already Joined</a>';

					} else if($db->checkIfPending($yourUserID, $groupID)) {
						echo '<a href="#myModal" data-toggle="modal" data-target="#modalPendingCancel" role="button" id="', $groupID,'" class="pendingButton btn btn-large btn-primary"></a>';
					} else if($groupCount >= $maxMembers) {
						echo "They already have the max members";
					} else {
						echo '<a href="#myModal" data-toggle="modal" data-target="#modalJoin" role="button" id="', $groupID,'/', $groupname,'" class="btn btn-large btn-primary">Join</a>';
					}

				} else {
					echo "You Can't Join a group Anymore!";
				}
				echo '	
					</div> <!-- END OF GROUP BUTTONS -->
				</div>';
				}
			} else {
			}
		?>
		</div><!-- list-group -->
		<div id='pagination' class="row">
            <ul>
			<?php
				if($pn > 0) {
				$centerPages = "";
				$sub1 = $pn - 1;
				$sub2 = $pn - 2;
				$add1 = $pn + 1;
				$add2 = $pn + 2;
				//$centerPages .= "<li><a></a?</li>";
				if($pn == 1) {
					$centerPages .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a class="activepage">' . $pn . '</a></li>';
					if($totalpages > 1)
						$centerPages .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $add1 . '</a></li>';
					if (!($add2 > $totalpages)) {
						$centerPages .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $add2 . '</a></li>';
					}				
				} else if ($pn == $totalpages) {
					if (!($sub2 < 1)) {
						$centerPages .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $sub2 . '</a></li>';
					}
					$centerPages .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $sub1 . '</a></li>';
					$centerPages .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a class="activepage">' . $pn . '</a></li>';
				}  else if ($pn > 1 && $pn < $totalpages) {
					$centerPages .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $sub1 . '</a></li>';
					$centerPages .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a class="activepage">' . $pn . '</a></li>';
					$centerPages .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $add1 . '</a></li>';
				}

				echo "<li class='col-sm-2 col-md-2 col-lg-2'><a href='groups.php?page=";
				if(isset($_GET['page'])) {
					$previous = $_GET['page'] - 1;
				}
				if($_GET['page'] == 1) {
					$previous = 1;
				}
				echo "$previous'";

				if($_GET['page'] == 1) {
					echo "class='disabled'";
				} else {
					echo "class='enabled'";
				}

				echo "><< Previous</a></li>";

				echo $centerPages;

				echo "<li class='col-sm-2 col-md-2 col-lg-2'><a href='groups.php?page=";
	            if(isset($_GET['page'])) {
	                $next = $_GET['page'] + 1;
	            } else {
	            	$next = 2;
	            }
	            echo "$next'";
	            if($_GET['page'] == $totalpages) {
					echo "class='disabled'";
				}
	            echo ">Next >></a></li>"; 
	        }
			?>
			</ul>
		</div> <!-- End of Pagination -->
		
		<div class="modal fade" id="modalJoin">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title text-centered">What is your reason to join <span id="gname"></span></h4>
					</div><!-- end modal-header -->

					<div class="modal-body">
					<div>
						<form id="joinGroupForm" action="includes/actions.php" method="post">
							<input id="joingroup" type="hidden" name="joingroup">
							<textarea id="joinMessage" name="joinmessage" class="form-control" placeholder="Message" rows="6"></textarea>
							<input type="hidden" name="userid" value="<?php echo $yourUserID; ?>">
							<input class="btn btn-primary" type="submit" value="Join" />
						<button class="btn btn-primary" data-dismiss="modal" type="button">Cancel</button>
						</form>
					</div>
					</div><!-- end modal-footer -->

				</div><!-- end modal-content -->
			</div><!-- end modal-dialog -->
		</div><!-- END OF MODAL JOIN -->

		<div class="modal fade" id="modalPendingCancel">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title text-centered">Do you really want to cancel?</h4>
					</div><!-- end modal-header -->

					<div class="modal-body">
					<div>
						<form id="pendingCancelGroupForm" action="includes/actions.php" method="post">
							<input id="pendingcancelgroup" type="hidden" name="pendingcancelgroup">
							<input type="hidden" name="userid" value="<?php echo $yourUserID; ?>">
							<input class="btn btn-primary" type="submit" value="Yes" />
						<button class="btn btn-primary" data-dismiss="modal" type="button">No</button>
						</form>
					</div>
					</div><!-- end modal-footer -->

				</div><!-- end modal-content -->
			</div><!-- end modal-dialog -->
		</div><!-- END OF MODAL PENDING CANCEL -->
		<?php include 'includes/footer.php'; ?>
	</div> <!-- End of main container -->
	<script type="text/javascript" src="../../js/jquery.js"></script>	
	<script type="text/javascript" src="../../js/bootstrap.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>