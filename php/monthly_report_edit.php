<?php
	$server = 'misaw.se.mysql';
	$echo = false;
	
    if (!empty($_POST['client']) && !empty($_POST['author']) && !empty($_POST['supervisor']) && !empty($_POST['company']) && !empty($_POST['projectnr']) && !empty($_POST['projectname']) && !empty($_POST['year']) && !empty($_POST['month']) && !empty($_POST['daycount']) && !empty($_POST['send'])) {

        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		// Protect from sql-injections
		$supervisor = stripslashes($_POST['supervisor']);
        $supervisor = $conn->real_escape_string($supervisor);
		
		$client = $_POST['client'];
        $number = $_POST['projectnr'];
        $name = $_POST['projectname'];
		
		if (isset($_POST['program'])) {
			$program = $_POST['program'];
		} else {
			$program = '';
		}
		$company = $_POST['company'];
		$author = $_POST['author'];
		
		$year = $_POST['year'];
		$month = $_POST['month'];
		$daycount = $_POST['daycount'];
		
		$success = true;
		if ($_POST['send'] == 'no') {
			$locked = 0;
		} else if ($_POST['send'] == 'yes') {
			$locked = 1;
		} else {
			$locked = 2;
		}
		$sql = "SELECT * FROM db_project_monthly_report WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND author='". $author ."' AND year='". $year ."' AND month='". $month ."'";
		$backup_result = $conn->query($sql);
		
		$sql = "UPDATE db_project_monthly_report SET supervisor='". $supervisor ."', locked='". $locked ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND author='". $author ."' AND year='". $year ."' AND month='". $month ."'";
		if ($echo) {
			echo $sql;
			echo '<br>';
		}
		if ($conn->query($sql)) {
			$inserteddays = 0;
			
			$sql = "SELECT * FROM db_project_monthly_report_day WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND author='". $author ."' AND year='". $year ."' AND month='". $month ."'";
			$day_backup_result = $conn->query($sql);
			
			for ($i = 0; $i < $daycount; $i++) {
				if (!empty($_POST['month_day'. $i .'']) && !empty($_POST['month_job'. $i .'']) && !empty($_POST['month_time'. $i .''])) {
					// Protect from sql-injections
					$day = stripslashes($_POST['month_day'. $i .'']);
					$day = $conn->real_escape_string($day);
					
					$job = stripslashes($_POST['month_job'. $i .'']);
					str_replace('"', "'", $job);
					$job = $conn->real_escape_string($job);
					
					$time = stripslashes($_POST['month_time'. $i .'']);
					$time = $conn->real_escape_string($time);
					$time = number_format((float)$time, 1);
					
					$sql = "SELECT day FROM db_project_monthly_report_day WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND author='". $author ."' AND year='". $year ."' AND month='". $month ."' AND day='". $day ."'";
					$result = $conn->query($sql);
					
					if ($result->num_rows == 1) {
						$sql = "UPDATE db_project_monthly_report_day SET job='". $job ."', time='". $time ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND author='". $author ."' AND year='". $year ."' AND month='". $month ."' AND day='". $day ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						if (!$conn->query($sql)) {
							$success = false;
							if ($echo) {
								echo 'break!<br>';
							}
							break 1;
						}
					} else {
						$sql = "INSERT INTO db_project_monthly_report_day (client, number, name, author, year, month, day, job, time) VALUES ('". $client ."','". $number ."','". $name ."','". $author ."','". $year ."','". $month ."','". $day ."','". $job ."','". $time ."')";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						if (!$conn->query($sql)) {
							$success = false;
							if ($echo) {
								echo 'break!<br>';
							}
							break 1;
						}
					}
					$inserteddays++;
				}
			}
			if ($echo) {
				echo 'inserteddays: ';
				echo $inserteddays;
				echo '<br>';
			}
			if ($success) {
				if (!$echo) {
					header("Location: project_page.php?client=". $client ."&number=". $number ."&name=". $name ."&selector=monthly");
				}
			} else {
				$sql = "DELETE FROM db_project_monthly_report_day WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND year='". $year ."' AND month='". $month ."'";
				if ($echo) {
					echo $sql;
					echo '<br>';
				}
				$conn->query($sql);
				$sql = "DELETE FROM db_project_monthly_report WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND year='". $year ."' AND month='". $month ."'";
				if ($echo) {
					echo $sql;
					echo '<br>';
				}
				$conn->query($sql);
				
				$backup_result_row = $backup_result->fetch_assoc();
				$sql = "INSERT INTO db_project_monthly_report (client, number, name, author, year, month, role, company, locked) VALUES ('". $backup_result_row['client'] ."','". $backup_result_row['number'] ."','". $backup_result_row['name'] ."','". $backup_result_row['author'] ."','". $backup_result_row['year'] ."','". $backup_result_row['month'] ."','". $backup_result_row['role'] ."','". $backup_result_row['company'] ."','". $backup_result_row['locked'] ."')";
				if ($echo) {
					echo $sql;
					echo '<br>';
				}
				$conn->query($sql);
				
				while ($day_backup_result_row = $day_backup_result->fetch_assoc()) {
					$sql = "INSERT INTO db_project_monthly_report_day (client, number, name, author, year, month, day, job, time) VALUES ('". $day_backup_result_row['client'] ."','". $day_backup_result_row['number'] ."','". $day_backup_result_row['name'] ."','". $backup_result_row['author'] ."','". $day_backup_result_row['year'] ."','". $day_backup_result_row['month'] ."','". $day_backup_result_row['day'] ."','". $day_backup_result_row['job'] ."','". $day_backup_result_row['time'] ."')";
					if ($echo) {
						echo $sql;
						echo '<br>';
					}
					$conn->query($sql);
				}
				if (!$echo) {
					header("Location: monthly_report_edit_page.php?year=". $year ."&month=". $month ."");
				}
			}
		} else {
			if (!$echo) {
				header("Location: monthly_report_edit_page.php?year=". $year ."&month=". $month ."");
			}
		}
		$conn->close();
    } else {
		if (!$echo) {
			header("Location: monthly_report_edit_page.php?year=". $_POST['year'] ."&month=". $_POST['month'] ."");
		} else {
			echo 'Client: ';
			echo $_POST['client'];
			echo '<br>';
			
			echo 'Supervisor: ';
			echo $_POST['supervisor'];
			echo '<br>';
			
			echo 'Author: ';
			echo $_POST['author'];
			echo '<br>';
			
			echo 'Company: ';
			echo $_POST['company'];
			echo '<br>';
			
			echo 'Project nr: ';
			echo $_POST['projectnr'];
			echo '<br>';
			
			echo 'Project name: ';
			echo $_POST['projectname'];
			echo '<br>';
			
			echo 'Year: ';
			echo $_POST['year'];
			echo '<br>';
			
			echo 'Month: ';
			echo $_POST['month'];
			echo '<br>';
			
			echo 'Daycount: ';
			echo $_POST['daycount'];
			echo '<br>';
			
			echo 'Send: ';
			echo $_POST['send'];
			echo '<br>';
		}
    }
?>
