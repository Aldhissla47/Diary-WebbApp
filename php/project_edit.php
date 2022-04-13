<?php
	$server = 'misaw.se.mysql';
	session_start();
    if(isset($_SESSION['project']) && !empty($_POST['jobsite'])) {
        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
        // Protect from sql-injections
        $jobsite = stripslashes($_POST['jobsite']);
		str_replace('"', "'", $jobsite);
        $jobsite = $conn->real_escape_string($jobsite);
		
		$program = stripslashes($_POST['program']);
		str_replace('"', "'", $program);
        $program = $conn->real_escape_string($program);
		
		$client = $_SESSION['project']['client'];
		$number = $_SESSION['project']['number'];
		$name = $_SESSION['project']['name'];
		
        $sql = "UPDATE db_project SET jobsite='". $jobsite ."', program='". $program ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."'";

        if (!$conn->query($sql)) {
			header("Location: project_edit_page.php");
        } else {
			header("Location: project_page.php?client=". $client ."&number=". $number ."&name=". $name ."");
		}
        $conn->close();
    }
    else {
        header("Location: project_edit_page.php");
    }
?>