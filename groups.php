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
	<div class="container" id="mainContainer">
		<?php include 'includes/title.php'; ?>
		<?php include 'includes/menu.php'; ?>

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
			</form> <!-- End of login form -->
		</div><!-- end alert -->

		<a href="" class="btn btn-large btn-primary" id="alertMe">Create Group</a>
		<hr />
		<div class="list-group">
		<?php 
			ob_start();
			$maxPerPage = 5;
			$count =  mysql_num_rows($db->getAllGroups());

			$pn;

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

			$groups = $db->getGroupsByPage($start, $maxPerPage);

			while($row = mysql_fetch_array($groups)) {
				$userID = $db->getLeaderID($row['groupID']);
				$leaderName = $db->getLeaderName($userID);
				echo 
				'<div class="list-group-item">
					<h1 class="list-group-item-heading">', $row['groupname'],'</h1>
					<p class="list-group-item-text">', $row['groupdescription'],'</p>
					<p class="list-group-item-text">', $leaderName,'</p>
				</div>';
			}
		?>
		</div><!-- list-group -->
		<div id='pagination' class="row">
            <ul>
			<?php
				
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
	            } 
	            echo "$next'";
	            if($_GET['page'] == $totalpages) {
					echo "class='disabled'";
				}
	            echo ">Next >></a></li>"; 
			?>
			</ul>
		</div> <!-- End of Pagination -->
		<?php include 'includes/footer.php'; ?>
	</div> <!-- End of main container -->
	<script type="text/javascript" src="../../js/jquery.js"></script>	
	<script type="text/javascript" src="../../js/bootstrap.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>