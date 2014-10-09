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
	<script type="text/javascript" src="../../js/jquery.js"></script>	
	<script type="text/javascript" src="../../js/bootstrap.js"></script>	
</head>
<body>
	<?php include 'includes/menu.php'; ?>
	<?php
		$username = $_SESSION['username'];
		$userID = $db->getUserID($username);
		$user = $db->getUserByID($userID);
		$time = strtotime($user['birthday']);
	?>
	<div class="container" id="mainContainer">
		<div class="page-header">
		   <h2>Edit Profile</h2>
		</div>
		<form class="form-horizontal" id="editProfileForm">
			<div class="form-group">
				<label class="col-lg-2 control-label">First Name:</label>
				<div class="col-lg-6">
					<input type="hidden" name="editprofile" value="<?php echo $userID; ?>">
					<input name="firstname" class="form-control" id="inputUsername" type="text" value="<?php echo $user['firstname']; ?>">
				</div>
			</div> <!-- End of First Name form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">Last Name:</label>
				<div class="col-lg-6">
					<input name="lastname" class="form-control" id="inputUsername" value="<?php echo $user['lastname']; ?>"type="text">
				</div>
			</div> <!-- End of Last Name form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">Birthday:</label>
				<div class="col-md-1 col-sm-1 col-md-1 col-lg-1">
					<select name="month" id="month" class="form-control">
						<?php 
							$month = date("m", $time);
							for($i = 1; $i <= 12; $i++) {
								echo '<option value="', $i,'"';
								if($i == $month) {
									echo 'selected="selected"';
								}
								echo '>', $i,'</option>';
							}
						?>
					</select>
				</div>
				<div class="col-md-1 col-sm-1 col-md-1 col-lg-1">
					<select name="day" id="day" class="form-control">
					<?php
						$day = date("d", $time);
						for($i = 1; $i <= 31; $i++) {
							echo '<option value="', $i,'"';
							if($i == $day) {
								echo 'selected="selected"';
							}
							echo '>', $i,'</option>';
						}
					?>
					</select>
				</div>
				<div class="col-md-2 col-sm-2 col-md-2 col-lg-2">
					<select name="year" id="year" class="form-control">
					<?php 
						$startingYear = 1935;
						$currentYear = date("Y");
						$bday = date("Y", $time);

						for($i = $startingYear; $i < $currentYear; $i++) {
							echo '<option value="', $i,'"';
							if($i == $bday) {
								echo 'selected="selected"';
							}
							echo '>', $i,'</option>';
						}
					?>
					</select>
				</div>
			</div> <!-- End of Gender form group -->
			<div class="form-group">
				<label class="col-lg-2"></label>
				<label class="col-lg-5">(Month / Day / Year)</label>
			</div>
			<div class="form-group">
				<label class="col-lg-2 control-label">Gender:</label>
				<div class="col-md-2 col-sm-2 col-md-2 col-lg-2">
					<select name="gender" class="form-control">
					<?php
						$gender;
						if($user['gender'] == 'm') $gender = 1; else if($user['gender'] == 'f') $gender = 2;
						for($i = 1; $i <= 2; $i++) {
							echo '<option ';
							if($i == $gender) echo 'selected="selected"';
							echo ' value="';
							if($i == 1) echo 'm'; else if($i == 2) echo 'f';
							echo '">';
							if($i == 1) echo "Male"; else if($i == 2) echo "Female";
							echo '</option>';
						}
					?>
					</select>
				</div>
			</div> <!-- End of Gender form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">Hobbies (Separate with commas):</label>
				<div class="col-lg-6">
					<input name="hobbies" class="form-control" value="<?php echo $user['hobbies']; ?>" type="text">
				</div>
			</div> <!-- End of Last Name form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">Skills (Separate with commas):</label>
				<div class="col-lg-6">
					<input name="skills" class="form-control" value="<?php echo $user['skills']; ?>" type="text">
				</div>
			</div> <!-- End of Last Name form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">About:</label>
				<div class="col-lg-6">
					<textarea name="about" class="form-control" rows="6"><?php echo $user['about']; ?></textarea>
				</div>
			</div> <!-- End of Password form group -->

			<div class="form-group">
				<div id="resultMessage"></div>
				<div class="col-lg-8">
					<input name="submitRegistration" class="btn btn-success pull-right" type="submit" value="Save">
				</div>
			</div> <!-- End of Submit Button form group -->
		</form> <!-- End of register form -->
		<?php include 'includes/footer.php'; ?>
	</div> <!-- End of main container -->
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>