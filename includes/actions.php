<?php 
	ini_set('display_errors',1);  
	error_reporting(E_ALL);

	require_once(__dir__ . '/DBHandler.php');
	$db = new DBHandler();

	/*****************************
			  POST ACTIONS
	*****************************/
	if(isset($_POST['registration']) && !empty($_POST['registration'])) {

		$username = $_POST['username'];
		$password = $_POST['password'];
		$confirmpassword = $_POST['confirmpassword'];
		$email = $_POST['email'];

		if(!empty($username) && !empty($password) && !empty($confirmpassword) && !empty($email)) {
			if ($password === $confirmpassword) {
				if($db->register($username, $password, $email)) {
					echo "Register Successful";
				} else {
					echo "Something went wrong with the process!";
				}
			} else {
				echo "Passwords do not match!";
			}
		} else {
			echo "Complete the form! Do not leave an empty box!";
		}

	} else if(isset($_POST['registerprofile']) && !empty($_POST['registerprofile'])) {
		$userID = $_POST['registerprofile'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		$gender = $_POST['gender'];
		$hobbies = $_POST['hobbies'];
		$skills = $_POST['skills'];
		$about = $_POST['about'];

		if(!empty($firstname) && !empty($lastname) && !empty($month) && 
			!empty($day) && !empty($year) && !empty($gender) && 
			!empty($skills) && !empty($about)) {

			$birthday = date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));
			if(!$db->checkIfHasProfile($userID)) {

				if($db->registerProfile($userID, $firstname, $lastname, $birthday, $gender, $hobbies, $skills, $about)) {
					echo "Register User Profile Successful";
				} else {
					echo "Something went wrong with user profile registration";
				}

			} else {
				echo "Already has User Profile";
			}
		} else {
			echo "Must fill the field with *";
		}
		
	} else if(isset($_POST['editprofile']) && !empty($_POST['editprofile'])) {
		$userID = $_POST['editprofile'];
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$month = $_POST['month'];
		$day = $_POST['day'];
		$year = $_POST['year'];
		$gender = $_POST['gender'];
		$hobbies = $_POST['hobbies'];
		$skills = $_POST['skills'];
		$about = $_POST['about'];

		$birthday = date("Y-m-d", mktime(0, 0, 0, $month, $day, $year));

		if($db->editProfile($userID, $firstname, $lastname, $birthday, $gender, $hobbies, $skills, $about)) {
			echo "Saved";
		} else {
			echo "Something went wrong with saving the changes";
		}

	} else if(isset($_POST['login']) && !empty($_POST['login'])) {

		$username = $_POST['username'];
		$password = $_POST['password'];
		
		if ($db->login($username, $password) === "1") {
			echo "No Account by that Username!";
		} else if ($db->login($username, $password) === "2") {
			echo "Wrong Username or Password";
		} else if ($db->login($username, $password) === "3") {
			$_SESSION['username'] = $username;
			echo "Login Successful/noregister";
		} else if($db->login($username, $password) === "4") {
			$_SESSION['username'] = $username;
			echo "Login Successful/register";
		}

	} else if(isset($_POST['creategroup']) && !empty($_POST['creategroup'])) {

		$groupname = $_POST['groupname'];
		$groupdescription = $_POST['groupdescription'];
		$groupstatus = $_POST['groupstatus'];
		
		if ($db->createGroup($groupname, $groupdescription, $groupstatus) === "1") {
			echo "Group by that name already exists!";
		} else if ($db->createGroup($groupname, $groupdescription, $groupstatus) === "2") {
			echo "Didn't create by some reason!";
		} else if($db->createGroup($groupname, $groupdescription, $groupstatus)) {
			$username = $_SESSION['username'];
			$gID = $db->getUserID($username);
			$uID = $db->getGroupID($groupname);
			if($db->createLeaderGroup($gID, $uID))
				echo "Create Group Successful";
			else 
				echo "Can't Create a Leader";
		}

	} else if(isset($_POST['deletegroup']) && !empty($_POST['deletegroup'])) {

		$groupID = $_POST['deletegroup'];
		if($db->deleteGroup($groupID) && $db->deleteMemberStatus($groupID)) {
			header("Location: ../groups.php");
		} else {
			echo "<script>alert('Something went wrong, Group Couldn't be deleted. Try again Later.');</script>";
			header("Location: ../groups.php");
		}

	} else if(isset($_POST['editgroup']) && !empty($_POST['editgroup'])) {

		$groupID = $_POST['editgroup'];
		$groupname = $_POST['groupname'];
		$groupdescription = $_POST['groupdescription'];

		if($db->editGroup($groupID, $groupname, $groupdescription)) {
			header("Location: ../viewgroup.php?id=$groupID");
		} else {
			echo "<script>alert('Something went wrong, Group Couldn't be edited. Try again Later.');</script>";
			header("Location: ../groups.php");
		}

	} else if(isset($_POST['joingroup']) && !empty($_POST['joingroup'])) {

		$groupID = $_POST['joingroup'];
		$userID = $_POST['userid'];
		$message = $_POST['joinmessage'];

		if($db->joinGroup($userID, $groupID, $message)) {
			header("Location: ../groups.php");
		} else {
			echo "<script>alert('Something went wrong, Couldn't join the group. Try again Later.');</script>";
			header("Location: ../groups.php");
		}

	} else if(isset($_POST['pendingcancelgroup']) && !empty($_POST['pendingcancelgroup'])) {

		$groupID = $_POST['pendingcancelgroup'];
		$userID = $_POST['userid'];

		if($db->cancelPending($userID, $groupID)) {
			header("Location: ../groups.php");
		} else {
			echo "<script>alert('Something went wrong, Couldn't cancel pending. Try again Later.');</script>";
		}

	}  else if(isset($_POST['kickMember']) && !empty($_POST['kickMember'])) {

		$groupID = $_POST['kickMember'];
		$userID = $_POST['userID'];

		if($db->kickMember($groupID, $userID)) {
			header("Location: ../viewgroup.php?id=$groupID");
		} else {
			echo "<script>alert('Something went wrong, Couldn't kick member. Try again Later.');</script>";
			header("Location: ../viewgroup.php?id=$groupID");
		}

	} else if (isset($_POST['invitetogroup']) && !empty($_POST['invitetogroup'])) {

		$groupID = $_POST['groupID'];
		$userID = $_POST['invitetogroup'];

		if($db->inviteToGroup($groupID, $userID)) {
			header("Location: ../profile.php?id=$userID");
		} else {
			echo "<script>alert('Something went wrong, Couldn't invite. Try again Later.');</script>";
			header("Location: ../profile.php?id=$userID");
		}
	} else if (isset($_POST['action']) && $_POST['action'] === 'postDiscussion') {
		$groupID = $_POST['groupID'];
		$userID = $_POST['userID'];
		$message = $_POST['message'];
		$now = date("Y-m-d H:i:s");

		if($db->postDiscussion($groupID, $userID, $message, $now)) {
			echo "POST SUCCESSFUL";
		} else {
			echo "POST FAILED";
		}

	} else if (isset($_POST['action']) && $_POST['action'] === 'postUserStatus') {
		$userID = $_POST['userID'];
		$message = $_POST['message'];
		$now = date("Y-m-d H:i:s");

		if($db->postStatus($userID, $message, $now)) {
			echo "POST SUCCESSFUL";
		} else {
			echo "POST FAILED";
		}

	} else if(isset($_POST['upload'])) {
		$base_directory = "../photos/";
		$userID = $_POST['userIDUpload'];
		$imageName = $_FILES['image']['name'];
		$imageTempName = $_FILES['image']['tmp_name'];

		if($_FILES['image']['name'] == "") {
			header("Location: ../profile.php");
		} else if($db->checkIfHasProfilePicture($userID)) {
			$ig = $db->getImageName($userID);
			if(unlink($base_directory.$ig)) {
				$db->deleteProfilePic($userID);
				move_uploaded_file($imageTempName, $base_directory."$imageName");
				if($db->uploadImage($userID, $imageName)) {
					header("Location: ../profile.php");
				}
			}
		} else {
			move_uploaded_file($imageTempName, $base_directory."$imageName");
			if($db->uploadImage($userID, $imageName)) {
				header("Location: ../profile.php");
			}
		}
	
	/*****************************
			  POST ACTIONS
	*****************************/
	/*****************************
			  GET ACTIONS
	*****************************/
	}  else if (isset($_GET['action']) && $_GET['action'] === 'getPending') {

		$userID = $_GET['userID'];
		$groupID = $_GET['groupID'];
		$message = $db->getReasonByUserID($userID, $groupID);
		echo $message;

	}  else if (isset($_GET['action']) && $_GET['action'] === 'kickMember') {

		$userID = $_GET['userID'];
		$groupID = $_GET['groupID'];
		if($db->kickMember($groupID, $userID)) {
			header("Location: ../viewgroup.php?id=$groupID");
		} else {
			echo "<script>alert('Something went wrong, Couldn't kick that member. Try again Later.');</script>";
			header("Location: ../groups.php");
		}

	}  else if (isset($_GET['member']) && $_GET['member'] === 'refuseMember') {

		$userID = $_GET['pendings'];
		$groupID = $_GET['optionGroupID'];
		
		if($db->cancelPending($userID, $groupID)) {
			echo "SUCCESS";
		} else {
			echo "FAIL";
		}

	} else if (isset($_GET['member']) && $_GET['member'] === 'acceptMember') {

		$userID = $_GET['pendings'];
		$groupID = $_GET['optionGroupID'];

		if($db->acceptMember($userID, $groupID)) {
			echo "SUCCESS";
		} else {
			echo "FAIL";
		}

	} else if (isset($_GET['invite']) && $_GET['invite'] === 'acceptInvite') {
		$groupID = $_GET['groupID'];
		$userID = $_GET['userID'];

		if($db->removeFromInvite($groupID, $userID)) {

			if($db->createMember($userID, $groupID)) {
				echo "SUCCESS";
			} else {
				echo "FAIL";
			}

		} else {
			echo "FAIL";
		}

	} else if (isset($_GET['invite']) && $_GET['invite'] === 'refuseInvite') {
		$groupID = $_GET['groupID'];
		$userID = $_GET['userID'];

		if($db->removeFromInvite($groupID, $userID)) {
			echo "SUCCESS";
		} else {
			echo "FAIL";
		}
	} else if (isset($_GET['action']) && $_GET['action'] === 'getPost') {
		$item_per_page = $itemPP;
		$page_number = $_GET["page"];
		$groupID = $_GET['groupID'];
		$position = ($page_number * $item_per_page);

		$posts = $db->getPostsWithLimit($groupID, $position, $item_per_page);
		$postCount = mysql_num_rows($posts);

		$message = "";
		if($postCount == 0) {
			echo "No Posts as of yet!<br />Be the one to post first!";
		} else if($postCount >= 1) {
			
			while($row = mysql_fetch_array($posts)) {
				$m = $row['message'];
				$username = $db->getLeaderName($row['userID']);
				$now = $row['now'];
				$message .= '<div class="list-group-item">';
				$message .= '<h2 class="list-group-item-heading">'.$m.'</h2>';
				$message .= '<p class="list-group-item-text">Posted By: '.$username.'</p>';
				$message .= '<p class="list-group-item-text">Posted on '.$now.'</p>';
				$message .= '</div>';
			}
			echo $message;
		}
	}  else if (isset($_GET['action']) && $_GET['action'] === 'getUserPost') {
		$item_per_page = $itemPP;
		$page_number = $_GET["page"];
		$userID = $_GET['userID'];

		$position = ($page_number * $item_per_page);

		$posts = $db->getUserPostsWithLimit($userID, $position, $item_per_page);
		$postCount = mysql_num_rows($posts);

		$message = "";
		if($postCount == 0) {
			echo "Hasn't posted anyting yet";
		} else if($postCount >= 1) {
			
			while($row = mysql_fetch_array($posts)) {
				$m = $row['message'];
				$username = $db->getLeaderName($row['userID']);
				$now = $row['now'];
				$message .= '<div class="list-group-item">';
				$message .= '<h2 class="list-group-item-heading">'.$m.'</h2>';
				$message .= '<p class="list-group-item-text">Posted By: '.$username.'</p>';
				$message .= '<p class="list-group-item-text">Posted on '.$now.'</p>';
				$message .= '</div>';
			}
			echo $message;
		}
	}
	/*****************************
			  GET ACTIONS
	*****************************/
?>