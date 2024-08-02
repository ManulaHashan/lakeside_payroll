<?php 
	session_start();
	unset($_SESSION["uid"]);
	session_commit();
	header("Location: ../index.php");	
?>