<div class='navbar navbar-static-top' id="navigation" role="navigation">
	<div class="container">
		
		<div class="navbar-header" id="navbar-header">
			<button id='navbarButton' class='navbar-toggle pull-right' data-target='.navbar-ex1-collapse' data-toggle='collapse' type='button'>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
				<span class='icon-bar'></span>
			</button>
			<a id='title' class='navbar-brand'>Team Maker</a>
		</div> <!-- End of Navbar-Header -->

		

		<div class='navbar-collapse collapse navbar-ex1-collapse' id='collapsebox'>
		<?php if($db->isLoggedIn())  { 
		echo "
		
			<ul class='nav navbar-nav' id='leftmenu'>
				<li class='active'>
					<a href='groups.php'>Home</a>
				</li>
				<li class='dropdown'>
					<a href='#' class='dropdown-toggle' data-toggle='dropdown'>Your Teams<strong class='caret'></strong></a>
					<ul id='yourgroups' class='dropdown-menu'>";
				$username = $_SESSION['username'];
				$userID = $db->getUserID($username);
				$yourGroups = $db->getAllYourGroup($userID);
				while($row = mysql_fetch_array($yourGroups)) {
					$groupID = $row['groupID'];
					$groupname = $db->getGroupNameByID($groupID);
					echo '<li><a href="viewgroup.php?id=', $groupID,'">', $groupname,'</a></li>';
				}

		echo "	
					</ul>
				</li>
				<li>
					<a href='profile.php'>Profile</a>
				</li>
			</ul>
			<ul id='rightmenu' class='nav navbar-nav pull-right'>
				<li class='dropdown'>
					<a href='#' class='dropdown-toggle' data-toggle='dropdown'><span class='glyphicon glyphicon-user'></span> ".$_SESSION['username']."<strong class='caret'></strong></a>
					
					<ul class='dropdown-menu'>
						<li>
							<a href='#'><span class='glyphicon glyphicon-refresh'></span>Update Profile</a>
						</li>			
						<li class='divider'></li>
						<li>
							<a href='logout.php'><span class='glyphicon glyphicon-off'></span> Sign out</a>
						</li>
					</ul>
				</li>
			</ul>
		
		";
	} else {
		echo "
		<ul class='nav navbar-nav' id='leftmenu'>
			<li>
				<a href='index.php'>Home</a>
			</li>
			<li>
				<a href='login.php'>Login</a>
			</li>
			<li>
				<a href='register.php'>Register</a>
			</li>
		</ul>";
	}
	?>
		</div>
	</div>
</div>