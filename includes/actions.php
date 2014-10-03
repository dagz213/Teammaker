<?php 
	ini_set('display_errors',1);  
	error_reporting(E_ALL);

	require_once(__dir__ . '/DBHandler.php');
	$db = new DBHandler();

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
	} else if(isset($_POST['login']) && !empty($_POST['login'])) {
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		if ($db->login($username, $password) === "1") {
			echo "No Account by that Username!";
		} else if ($db->login($username, $password) === "2") {
			echo "Wrong Username/Password";
		} else if($db->login($username, $password)) {
			$_SESSION['username'] = $username;
			echo "Login Successful";
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
	}
?>