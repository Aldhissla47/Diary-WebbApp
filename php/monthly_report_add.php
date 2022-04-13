<?php
	$server = 'misaw.se.mysql';
	$echo = false;
	
    if (!empty($_POST['client']) && !empty($_POST['author']) && !empty($_POST['supervisor']) && !empty($_POST['company']) && !empty($_POST['role']) && !empty($_POST['projectnr']) && !empty($_POST['projectname']) && !empty($_POST['year']) && !empty($_POST['month']) && !empty($_POST['daycount']) && !empty($_POST['send'])) {

        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		// Protect from sql-injections
		$supervisor = stripslashes($_POST['supervisor']);
        $supervisor = $conn->real_escape_string($supervisor);
		
		$year = stripslashes($_POST['year']);
        $year = $conn->real_escape_string($year);
		
		$month = stripslashes($_POST['month']);
        $month = $conn->real_escape_string($month);
		
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
		$role = $_POST['role'];
		
		$daycount = $_POST['daycount'];
		
		$sql = "SELECT month FROM db_project_monthly_report WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND author='". $author ."' AND year='". $year ."' AND month='". $month ."'";
		if ($echo) {
			echo $sql;
			echo '<br><br>';
		}
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			header("Location: monthly_report_add_page.php");
		} else {
			$success = true;
			if ($_POST['send'] == 'lock') {
				$locked = 2;
				$reviewer = $author;
			} else {
				$locked = 0;
			}
			$sql = "INSERT INTO db_project_monthly_report (client, number, name, author, year, month, program, role, supervisor, company, locked) VALUES ('". $client ."','". $number ."','". $name ."','". $author ."','". $year ."','". $month ."','". $program ."','". $role ."','". $supervisor ."','". $company ."','". $locked ."')";
			if ($echo) {
				echo $sql;
				echo '<br>';
			}
			if ($conn->query($sql)) {
				$inserteddays = 0;
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
						$inserteddays++;
					}
				}
				if ($echo) {
					echo 'inserteddays: ';
					echo $inserteddays;
					echo '<br>';
				}
				if ($inserteddays == 0) {
					$success = false;
				}
				if ($success) {
					if (!$echo) {
						header("Location: project_page.php?client=". $client ."&number=". $number ."&name=". $name ."&selector=monthly");
					}
				} else {
					$sql1 = "DELETE FROM db_project_monthly_report_day WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND year='". $year ."' AND month='". $month ."'";
					$conn->query($sql1);
					
					$sql2 = "DELETE FROM db_project_monthly_report WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND year='". $year ."' AND month='". $month ."'";
					$conn->query($sql2);
					
					if (!$echo) {
						header("Location: monthly_report_add_page.php");
					} else {
						echo $sql1;
						echo '<br>';
						
						echo $sql2;
						echo '<br>';
					}
				}
			} else {
				if (!$echo) {
					header("Location: monthly_report_add_page.php");
				}
			}
		}
		$conn->close();
    } else {
		if (!$echo) {
			header("Location: monthly_report_add_page.php");
		} else {
			echo 'Client: ';
			echo $_POST['client'];
			echo '<br>';
			
			echo 'Author: ';
			echo $_POST['author'];
			echo '<br>';
			
			echo 'Supervisor: ';
			echo $_POST['supervisor'];
			echo '<br>';
			
			echo 'Company: ';
			echo $_POST['company'];
			echo '<br>';
			
			echo 'Roll: ';
			echo $_POST['role'];
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
