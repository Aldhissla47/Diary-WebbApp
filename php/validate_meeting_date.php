<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (isset($_SESSION['project'])) {
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		if (!empty($_POST['series']) && !empty($_POST['date'])) {
			if ($_POST['date'] <= date('Y-m-d')) {
				$sql = "SELECT MAX(date) as date FROM db_project_meeting_protocol WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND series='". $_POST['series'] ."'";
				$result = $conn->query($sql);
				if ($result->num_rows > 0) {
					$row = $result->fetch_assoc();
					
					if ($_POST['date'] <= $row['date']) {
						echo '1';
					} else {
						echo '0';
					}
				} else {
					echo '0';
				}
			} else {
				echo '-1';
			}
		}
		$conn->close();
	}
?>