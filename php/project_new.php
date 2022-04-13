<?php
	$server = 'misaw.se.mysql';
	session_start();
    if(isset($_SESSION['user']) && !empty($_POST['number']) && !empty($_POST['name']) && !empty($_POST['jobsite'])) {
        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
        // Protect from sql-injections
        $number = stripslashes($_POST['number']);
        $number = $conn->real_escape_string($number);

        $name = stripslashes($_POST['name']);
        $name = $conn->real_escape_string($name);
		
		$client = $_SESSION['user']['email'];
		
        $jobsite = stripslashes($_POST['jobsite']);
		str_replace('"', "'", $jobsite);
        $jobsite = $conn->real_escape_string($jobsite);
		
		$program = stripslashes($_POST['program']);
		str_replace('"', "'", $program);
        $program = $conn->real_escape_string($program);
		
		$date = date('Y-m-d');
		
        $sql = "INSERT INTO db_project (client, number, name, program, jobsite, created) VALUES ('". $client ."','". $number ."','". $name ."','". $program ."','". $jobsite ."','". $date ."')";

        if ($conn->query($sql)) {
			$sql = "INSERT INTO db_project_member (client, number, name, user, title, permission) VALUES ('". $client ."','". $number ."','". $name ."','". $client ."','Datasamordnare', '1')";
            if ($conn->query($sql)) {
				header("Location: project_page.php?client=". $client ."&number=". $number ."&name=". $name ."");
			}
        } else {
            header("Location: project_new_page.php");
        }
        $conn->close();
		header("Location: project_page.php?client=". $client ."&number=". $number ."&name=". $name ."");
    }
    else {
        header("Location: project_new_page.php");
    }
?>