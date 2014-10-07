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
	?>
	<div class="container" id="mainContainer">
		<div class="page-header">
		   <h2>Register Profile</h2>
		</div>
		<form class="form-horizontal" id="profileRegistrationForm">
			<div class="form-group">
				<label class="col-lg-7 control-label">Register your profile for this page only take once after you log in</label>
			</div>
			<div class="form-group">
				<label class="col-lg-2 control-label">First Name: *</label>
				<div class="col-lg-6">
					<input type="hidden" name="registerprofile" value="<?php echo $userID; ?>">
					<input name="firstname" class="form-control" id="inputUsername" placeholder="First Name" type="text">
				</div>
			</div> <!-- End of First Name form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">Last Name: *</label>
				<div class="col-lg-6">
					<input name="lastname" class="form-control" id="inputUsername" placeholder="Last Name" type="text">
				</div>
			</div> <!-- End of Last Name form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">Birthday: *</label>
				<div class="col-md-1 col-sm-1 col-md-1 col-lg-1">
					<select name="month" id="month" class="form-control">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
					</select>
				</div>
				<div class="col-md-1 col-sm-1 col-md-1 col-lg-1">
					<select name="day" id="day" class="form-control">
						<option value="1">1</option><option value="2">2</option><option value="3">3</option>
						<option value="4">4</option><option value="5">5</option><option value="6">6</option>
						<option value="7">7</option><option value="8">8</option><option value="9">9</option>
						<option value="10">10</option><option value="11">11</option><option value="12">12</option>
						<option value="13">13</option><option value="14">14</option><option value="15">15</option>
						<option value="16">16</option><option value="17">17</option><option value="18">18</option>
						<option value="19">19</option><option value="20">20</option><option value="21">21</option>
						<option value="22">22</option><option value="23">23</option><option value="23">23</option>
						<option value="24">24</option><option value="24">24</option><option value="25">25</option>
						<option value="26">26</option><option value="27">27</option><option value="28">28</option>
						<option value="29">29</option><option value="30">30</option><option value="31">31</option>
					</select>
				</div>
				<div class="col-md-2 col-sm-2 col-md-2 col-lg-2">
					<select name="year" id="year" class="form-control">
					<?php 
						$startingYear = 1935;
						$currentYear = date("Y");
						$cs = ceil(($currentYear - $startingYear) / 2);
						$median = $startingYear + $cs;

						for($i = $startingYear; $i < $currentYear; $i++) {
							echo '<option value="', $i,'"';
							if($i == $median) {
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
				<label class="col-lg-2 control-label">Gender: *</label>
				<div class="col-md-2 col-sm-2 col-md-2 col-lg-2">
					<select name="gender" class="form-control">
						<option value="m">Male</option>
						<option value="f">Female</option>
					</select>
				</div>
			</div> <!-- End of Gender form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">Hobbies (Separate with commas):</label>
				<div class="col-lg-6">
					<input name="hobbies" class="form-control" placeholder="Hobbies" type="text">
				</div>
			</div> <!-- End of Last Name form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">Skills (Separate with commas): *</label>
				<div class="col-lg-6">
					<input name="skills" class="form-control" placeholder="Skills" type="text">
				</div>
			</div> <!-- End of Last Name form group -->

			<div class="form-group">
				<label class="col-lg-2 control-label">About: *</label>
				<div class="col-lg-6">
					<textarea name="about" class="form-control" placeholder="About" rows="6"></textarea>
				</div>
			</div> <!-- End of Password form group -->

			<div class="form-group">
				<div id="resultMessage"></div>
				<div class="col-lg-6">
					<a href="groups.php" class="btn btn-success pull-right">Skip</a>
				</div>
				<div class="col-lg-2">
					<input name="submitRegistration" class="btn btn-success pull-right" type="submit" value="Register Profile">
				</div>
			</div> <!-- End of Submit Button form group -->
		</form> <!-- End of register form -->
		<?php include 'includes/footer.php'; ?>
	</div> <!-- End of main container -->
	<script type="text/javascript" src="js/script.js"></script>
</body>
</html>