<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (isset($_SESSION['project']) && !empty($_POST['company'])) {
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		if (!empty($_POST['date'])) {
			if ($_POST['date'] <= date('Y-m-d')) {
				$sql = "SELECT workday FROM db_project_diary WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $_POST['company'] ."' AND date='". $_POST['date'] ."'";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					echo $result->num_rows;
				} else {
					echo '0';
				}
			} else {
				echo '-1';
			}
		} else if (!empty($_POST['workday'])) {
			$sql = "SELECT workday FROM db_project_diary WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $_POST['company'] ."' AND workday='". $_POST['workday'] ."'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				echo $result->num_rows;
			} else {
				echo '0';
			}
		}
		$conn->close();
	}
?>