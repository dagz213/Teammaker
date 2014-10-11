<?php 
	ob_start();
	ini_set('display_errors',1);  
	error_reporting(E_ALL);
	require_once(__dir__.'/includes/DBHandler.php');
	$db = new DBHandler();

	if(!$db->isLoggedIn()) {
		header('Location: index.php');
	}

	$db->makeSeed();
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
		<div class="page-header">
		   <h2>Groups</h2>
		</div>
		<div class="list-group">
		<?php 
			//Variables
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
						<p class="list-group-item-text groupList">Leader: <a href="profile.php?id=', $userID,'">', $leaderName,'</a></p>
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
			}
		?>
		</div><!-- list-group -->
		<div class="row pag">
            <ul>
			<?php
				if($pn > 0) {
				$centerPages = "";
				$sub1 = $pn - 1;
				$sub2 = $pn - 2;
				$add1 = $pn + 1;
				$add2 = $pn + 2;

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

				if($totalpages > 1) {
					//get PagePeople for Group's Pagination
		            if(isset($_GET['pagePeople']))
		            	$pagePeople = $_GET['pagePeople'];
		            else {
		            	$pagePeople = 1;
		            }

					echo "<li class='col-sm-2 col-md-2 col-lg-2'><a href='groups.php?page=";
					if(isset($_GET['page'])) {
						$previous = $_GET['page'] - 1;
					}
					if($_GET['page'] == 1) {
						$previous = 1;
					}
					echo "$previous&pagePeople=$pagePeople'";

					if($totalpages == 1) {
						echo "class='displaynone'";
					} else if($_GET['page'] == 1 || !isset($_GET['page'])) {
						echo "class='disabled'";
					}

					echo "><< Previous</a></li>";
				}
				echo $centerPages;

				echo "<li class='col-sm-2 col-md-2 col-lg-2'><a href='?page=";
	            if(isset($_GET['page'])) {
	                $next = $_GET['page'] + 1;
	            } else {
	            	$next = 2;
	            }

	            echo "$next&pagePeople=$pagePeople'";

	            if($totalpages == 1) {
					echo "class='displaynone'";
				} else if($_GET['page'] == $totalpages) {
					echo "class='disabled'";
				}
	            echo ">Next >></a></li>"; 
	        }
			?>
			</ul>
		</div> <!-- End of Pagination Groups -->

		<!-- START OF PEOPLE PAGE -->
		<div class="page-header">
		   <h2>People</h2>
		</div>
		<div id="peoplePage" class="list-group">
		<?php 
			$people;
			$countPeople =  mysql_num_rows($db->getAllPeople());

			$pnPeople = 0;
			if($countPeople > 0) {
			$totalpagesPeople = ceil($countPeople / $maxPerPage); 
			if(isset($_GET['pagePeople'])) {
				$pnPeople = $_GET['pagePeople'];
			} else {
				$pnPeople = 1;
			}

			if($pnPeople < 1) {
				$pnPeople = 1;
			} else if ($pnPeople > $totalpagesPeople) {
				$pnPeople = $totalpagesPeople;
			}

			$startPeople = ($pnPeople - 1) * $maxPerPage;
			$seed = $_SESSION['seed'];

			if($db->getPeopleByPage($seed, $startPeople, $maxPerPage)) {
				$people = $db->getPeopleByPage($seed, $startPeople, $maxPerPage);
			} else {

			}

			while($row = mysql_fetch_array($people)) {
				$userID = $row['userID'];
				$leaderName = $db->getLeaderName($userID);
				$about = $row['about'];
				$skills = $row['skills'];

				echo 
				'<div class="list-group-item">
					<div class="groupdescription">
						<h1 class="list-group-item-heading groupTitle"><a href="profile.php?id=', $userID,'">', $leaderName,'</a></h1>
						<p class="list-group-item-text groupList">About: ', $about,'</p>
						<p class="list-group-item-text groupList">Skills: ', $skills,'</a></p>
					</div> <!-- END OF GROUP DESCRIPTION -->
					<div class="groupbuttons">';
					
					if($db->checkIfHasGroupANDLeader($yourUserID)) {
						if(!$db->checkIfAlreadyInvited($groupID, $userID)) {
							if($db->checkIfInviteLimit($groupID)) {
								echo '<a href="#myModal" class="btn btn-large btn-primary">Already Invite Limit</a>';
							} else {
								echo '<a href="#myModal" data-toggle="modal" data-target="#modalInvite" role="button" id="', $userID,'" class="btn btn-large btn-primary">Invite</a>';
							}
						} else {
							echo '<a href="#myModal" data-toggle="modal" data-target="#modalInviteCancel" role="button" id="', $userID,'" class="btn btn-large btn-primary">Already Invited</a>';
						}
					}
				echo '
					</div> <!-- END OF INVITE BUTTONS -->
				</div>
				';
				}
			}
				
		?>
		</div><!-- list-group -->
		<div class="row pag">
            <ul>
			<?php
				if($pnPeople > 0) {
				$centerPagesPeople = "";
				$sub1People = $pnPeople - 1;
				$sub2People = $pnPeople - 2;
				$add1People = $pnPeople + 1;
				$add2People = $pnPeople + 2;
				//$centerPages .= "<li><a></a?</li>";
				if($pnPeople == 1) {
					$centerPagesPeople .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a class="activepage">' . $pnPeople . '</a></li>';
					if($totalpagesPeople > 1)
						$centerPagesPeople .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $add1People . '</a></li>';
					if (!($add2People > $totalpagesPeople)) {
						$centerPagesPeople .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $add2People . '</a></li>';
					}				
				} else if ($pnPeople == $totalpagesPeople) {
					if (!($sub2People < 1)) {
						$centerPagesPeople .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $sub2People . '</a></li>';
					}
					$centerPagesPeople .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $sub1People . '</a></li>';
					$centerPagesPeople .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a class="activepage">' . $pnPeople . '</a></li>';
				}  else if ($pnPeople > 1 && $pnPeople < $totalpagesPeople) {
					$centerPagesPeople .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $sub1People . '</a></li>';
					$centerPagesPeople .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a class="activepage">' . $pnPeople . '</a></li>';
					$centerPagesPeople .= '<li class="col-sm-1 col-md-1 col-lg-1 numbers"><a>' . $add1People . '</a></li>';
				}

				//get page for People's Pagination
	            if(isset($_GET['page']))
	            	$page = $_GET['page'];
	            else {
	            	$page = 1;
	            }

	           	if($totalpagesPeople > 1) {
					echo "<li class='col-sm-2 col-md-2 col-lg-2'><a href='groups.php?pagePeople=";
					if(isset($_GET['pagePeople'])) {
						$previousPeople = $_GET['pagePeople'] - 1;
					}
					if($_GET['pagePeople'] == 1) {
						$previousPeople = 1;
					}
					echo "$previousPeople&page=$page'";

					if($totalpagesPeople == 1) {
						echo "class='displaynone'";
					} else if($_GET['pagePeople'] == 1 || !isset($_GET['pagePeople'])) {
						echo "class='disabled'";
					}
					echo "><< Previous</a></li>";
				}

				

				echo $centerPagesPeople;

				echo "<li class='col-sm-2 col-md-2 col-lg-2'><a href='?pagePeople=";
	            if(isset($_GET['pagePeople'])) {
	                $nextPeople = $_GET['pagePeople'] + 1;
	            } else {
	            	$nextPeople = 2;
	            }

	            echo "$nextPeople&page=$page'";

	            if($totalpagesPeople == 1) {
					echo "class='displaynone'";
				} else if($_GET['pagePeople'] == $totalpagesPeople) {
					echo "class='disabled'";
				}
	            echo ">Next >></a></li>"; 
	        }
			?>
			</ul>
		</div> <!-- End of Pagination Groups -->
		
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