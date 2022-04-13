<?php
	$server = 'localhost';
	session_start();
    if(isset($_SESSION['project']) && !empty($_POST['user'])) {
        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		$sql = "DELETE FROM db_project_member WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND user='". $_POST['user'] ."'";
		$conn->query($sql);
        $conn->close();
    }
	header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."&selector=member");
?>