<?php
	ob_start();
	require_once(__dir__.'/includes/DBHandler.php');
	$db = new DBHandler();
	session_destroy();
	header("Location: index.php");
?>