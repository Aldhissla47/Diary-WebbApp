<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (isset($_SESSION['project']) && !empty($_POST['year']) && !empty($_POST['month'])) {
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		$sql = "SELECT year FROM db_project_monthly_report WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND author='". $_SESSION['user']['email'] ."' AND year='". $_POST['year'] ."' AND month='". $_POST['month'] ."'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			echo $result->num_rows;
		} else {
			echo '0';
		}
		$conn->close();
	}
?>
