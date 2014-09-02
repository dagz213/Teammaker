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
			$maxPerPage = 3;
			$count =  mysql_num_rows($db->getAllGroups());

			$page;
			$start;

			if(!isset($_GET['page'])) {
				$start = 0;
				$page = 0;
			} else {
				$start = ($_GET['page'] - 1) * $maxPerPage;
            	$page = $_GET['page'];
			}

			$groups = $db->getGroupsByPage($start, $maxPerPage);

			while($row = mysql_fetch_array($groups)) {
				$userID = $db->getLeaderID($row['groupID']);
				$leaderName = $db->getLeaderName($userID);
				echo '<a href="#" class="list-group-item">
					<h4 class="list-group-item-heading">', $row['groupname'],'</h4>
					<p class="list-group-item-text">', $row['groupdescription'],'</p>
					<p class="list-group-item-text">', $leaderName,'</p>
				</a>';
			}
		?>
		</div><!-- list-group -->
		<div id='pagination'>
            <ul>
			<?php
				$totalpages = ceil($count / $maxPerPage);

        		if($totalpages > 0) {
        			$previous;
        			$next;
					echo "<li><a href='groups.php?page=";
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

					if(!isset($_GET['page'])) {
		                $_GET['page'] = 1;
		            }

		            for($i = 1;$i <= $totalpages; $i++) {
		            	echo "<li><a href='groups.php?page=$i'";
		            	if($i == $_GET['page']) {
		            		echo "style='border: 1px solid #000099;'";
		            	}
		            	echo ">$i</a></li>";
		            }

		            echo "<li><a href='groups.php?page=";
		            if(isset($_GET['page'])) {
		                $next = $_GET['page'] + 1;
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
	</div> <!-- End of main container -->
	<script type="text/javascript" src="../../js/jquery.js"></script>	
	<script type="text/javascript" src="../../js/bootstrap.js"></script>
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>