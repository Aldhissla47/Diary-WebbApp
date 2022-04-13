<?php
	session_start();
    unset($_SESSION['loggedin']);
    unset($_SESSION['user']);
	unset($_SESSION['project']);
	unset($_SESSION['selector']);
	session_destroy();
	header("Location: login_page.php");
?>