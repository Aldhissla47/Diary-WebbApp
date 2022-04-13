<?php
	$server = 'misaw.se.mysql';
	$echo = false;
	
    if (!empty($_POST['client']) && !empty($_POST['projectnr']) && !empty($_POST['projectname']) && !empty($_POST['seriesid']) && !empty($_POST['meetingid']) && !empty($_POST['date']) && !empty($_POST['author']) && !empty($_POST['type']) && !empty($_POST['mainheader']) && !empty($_POST['time']) && !empty($_POST['time2']) && !empty($_POST['jobsite']) && !empty($_POST['presentcount']) && !empty($_POST['notpresentcount']) && !empty($_POST['contentrows']) && !empty($_POST['send'])) {

        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		$client = $_POST['client'];
        $projectnr = $_POST['projectnr'];
        $projectname = $_POST['projectname'];
		$seriesid = $_POST['seriesid'];
		$meetingid = $_POST['meetingid'];
		$author = $_POST['author'];
		$type = $_POST['type'];
		$date = $_POST['date'];
		
        // Protect from sql-injections
        $mainheader = stripslashes($_POST['mainheader']);
        $mainheader = $conn->real_escape_string($mainheader);

		$time = stripslashes($_POST['time']);
        $time = $conn->real_escape_string($time);
		
		$time2 = stripslashes($_POST['time2']);
        $time2 = $conn->real_escape_string($time2);
		
		$jobsite = stripslashes($_POST['jobsite']);
        $jobsite = $conn->real_escape_string($jobsite);
		
		$success = true;
		$insertedseries = false;
		
		$sql = "SELECT id FROM db_project_meeting_series WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND id='". $seriesid ."'";
		if ($echo) {
			echo $sql;
			echo '<br><br>';
		}
		$result = $conn->query($sql);
		if ($result->num_rows == 0) {
			$sql = "INSERT INTO db_project_meeting_series (client, number, name, id, author, date, type, header) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $seriesid ."','". $author ."','". $date ."','". $type ."','". $mainheader ."')";
			if ($echo) {
				echo $sql;
				echo '<br><br>';
			}
			if (!$conn->query($sql)) {
				if (!$echo) {
					$success = false;
				} else {
					echo 'INSERT INTO db_project_meeting_series failed!';
					echo '<br>';
				}
			} else {
				$insertedseries = true;
			}
		}
		if ($success) {
			$sql = "SELECT id FROM db_project_meeting_protocol WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND id='". $meetingid ."'";
			if ($echo) {
				echo $sql;
				echo '<br><br>';
			}
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				if (!$echo) {
					header("Location: meeting_add_page.php");
				} else {
					echo '$result->num_rows > 0';
					echo '<br>';
				}
			} else {
				if ($_POST['send'] == 'no') {
					$send = 0;
				} else if ($_POST['send'] == 'yes') {
					$send = 1;
				} else {
					$send = 2;
				}
				$sql = "INSERT INTO db_project_meeting_protocol (client, number, name, series, id, author, date, time, time2, jobsite, locked) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $seriesid ."','". $meetingid ."','". $author ."','". $date ."','". $time ."','". $time2 ."','". $jobsite ."','". $send ."')";
				if ($echo) {
					echo $sql;
					echo '<br><br>';
				}
				if ($conn->query($sql)) {
					$insertedpresents = 0;
					for ($i = 0; $i < $_POST['presentcount']; $i++) {
						if (!empty($_POST['present'. $i .'']) && !empty($_POST['company'. $i .'']) && !empty($_POST['email'. $i .''])) {
							// Protect from sql-injections
							$name = stripslashes($_POST['present'. $i .'']);
							$name = $conn->real_escape_string($name);
							
							$company = stripslashes($_POST['company'. $i .'']);
							$company = $conn->real_escape_string($company);
							
							$email = stripslashes($_POST['email'. $i .'']);
							$email = $conn->real_escape_string($email);
							$email = strtolower($email);
							if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
								$success = false;
								if ($echo) {
									echo 'Email break!<br>';
								}
								break 1;
							}
							$presentid = $insertedpresents + 1;
							
							$sql = "INSERT INTO db_project_meeting_present (client, number, name, series, meeting, id, fullname, company, email, present) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $seriesid ."','". $meetingid ."','". $presentid ."','". $name ."','". $company ."','". $email ."',1)";
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
							$insertedpresents++;
						}
					}
					if ($echo) {
						echo 'insertedpresents: ';
						echo $insertedpresents;
						echo '<br><br>';
					}
					if ($insertedpresents == 0) {
						$success = false;
					} else {
						$insertednotpresents = 0;
						for ($i = 0; $i < $_POST['notpresentcount']; $i++) {
							if (!empty($_POST['notpresent'. $i .'']) && !empty($_POST['notcompany'. $i .'']) && !empty($_POST['notemail'. $i .''])) {
								// Protect from sql-injections
								$name = stripslashes($_POST['notpresent'. $i .'']);
								$name = $conn->real_escape_string($name);
								
								$company = stripslashes($_POST['notcompany'. $i .'']);
								$company = $conn->real_escape_string($company);
								
								$email = stripslashes($_POST['notemail'. $i .'']);
								$email = $conn->real_escape_string($email);
								$email = strtolower($email);
								if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
									$success = false;
									if ($echo) {
										echo 'Email break!<br>';
									}
									break 1;
								}
								$presentid = $insertedpresents + $insertednotpresents + 1;
								
								$sql = "INSERT INTO db_project_meeting_present (client, number, name, series, meeting, id, fullname, company, email, present) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $seriesid ."','". $meetingid ."','". $presentid ."','". $name ."','". $company ."','". $email ."',0)";
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
								$insertednotpresents++;
							}
						}
						if ($echo) {
							echo 'insertednotpresents: ';
							echo $insertednotpresents;
							echo '<br><br>';
						}
						$insertedheaders = 0;
						for ($i = 0; $i < $_POST['contentrows']; $i++) {				
							if (!empty($_POST['taskrows'. $i .'']) && !empty($_POST['header'. $i .''])) {
								// Protect from sql-injections
								$header = stripslashes($_POST['header'. $i .'']);
								$header = $conn->real_escape_string($header);
								
								$sql = "INSERT INTO db_project_meeting_header (client, number, name, series, meeting, id, text) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $seriesid ."','". $meetingid ."','". $insertedheaders ."','". $header ."')";
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
								$insertedtasks = 0;
								for ($j = 0; $j < $_POST['taskrows'. $i .'']; $j++) {
									if (!empty($_POST['task_id'. $i .''. $j .''] && !empty($_POST['task'. $i .''. $j .''])) {
										// Protect from sql-injections
										$task = stripslashes($_POST['task'. $i .''. $j .'']);
										$task = $conn->real_escape_string($task);
										
										$taskid = stripslashes($_POST['task_id'. $i .''. $j .'']);
										$taskid = $conn->real_escape_string($taskid);
										
										$supervisor = array('','','');
										$supercount = 0;
										
										for ($z = 0; $z < 3; $z++) {
											if (!empty($_POST['supervisor'. $i .''. $j .''. $z .''])) {
												// Protect from sql-injections
												$super = stripslashes($_POST['supervisor'. $i .''. $j .''. $z .'']);
												$super = $conn->real_escape_string($super);
												
												if ($super == 'Klart' || $super == 'Info') {
													$supervisor[0] = $super;
													$supervisor[1] = '';
													$supervisor[2] = '';
													$supercount = 1;
													break;
												} else {
													$supervisor[$z] = $super;
													$supercount++;
												}
											} else {
												$supervisor[$z] = '';
											}
										}
										if ($supercount == 0) {
											$success = false;
											if ($echo) {
												echo 'Supercount break!<br>';
											}
											break 2;
										}
										$sql = "INSERT INTO db_project_meeting_task (client, number, name, series, meeting, header, id, text, supervisor1, supervisor2, supervisor3) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $seriesid ."','". $meetingid ."','". $insertedheaders ."','". $taskid ."','". $task ."','". $supervisor[0] ."','". $supervisor[1] ."','". $supervisor[2] ."')";
										if ($echo) {
											echo $sql;
											echo '<br>';
										}
										if (!$conn->query($sql)) {
											$success = false;
											if ($echo) {
												echo 'break!<br>';
											}
											break 2;
										}
										$insertedtasks++;
									}
								}
								if ($insertedtasks == 0) {
									$success = false;
									if ($echo) {
										echo 'break!<br>';
									}
									break 1;
								}
								$insertedheaders++;
							}
							if ($echo) {
								echo '<br>';
							}
						}
						if ($echo) {
							echo 'insertedheaders: ';
							echo $insertedheaders;
							echo '<br><br>';
						}
						if ($insertedheaders == 0) {
							$success = false;
						}
					}
				} else {
					$success = false;
				}
				if ($success) {
					if (!$echo) {
						header("Location: project_page.php?client=". $client ."&number=". $projectnr ."&name=". $projectname ."&selector=meeting");
					}
				} else {
					$sql1 = "DELETE FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."'";
					$conn->query($sql1);

					$sql2 = "DELETE FROM db_project_meeting_header WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."'";
					$conn->query($sql2);

					$sql3 = "DELETE FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."'";
					$conn->query($sql3);

					$sql4 = "DELETE FROM db_project_meeting_protocol WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND id='". $meetingid ."'";
					$conn->query($sql4);
					
					if ($insertedseries) {
						$sql5 = "DELETE FROM db_project_meeting_series WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND id='". $seriesid ."'";
						$conn->query($sql5);
					}
					if (!$echo) {
						header("Location: meeting_add_page.php");
					} else {
						echo $sql1;
						echo '<br>';
						
						echo $sql2;
						echo '<br>';
						
						echo $sql3;
						echo '<br>';
						
						echo $sql4;
						echo '<br>';
						
						if ($insertedseries) {
							echo $sql5;
							echo '<br>';
						}
					}
				}
			}
		} else {
			if (!$echo) {
				header("Location: meeting_add_page.php");
			}
		}
		$conn->close();
    } else {
		if (!$echo) {
			header("Location: meeting_add_page.php");
		} else {
			echo 'Client: ';
			echo $_POST['client'];
			echo '<br>';
			
			echo 'Project nr: ';
			echo $_POST['projectnr'];
			echo '<br>';
			
			echo 'Project name: ';
			echo $_POST['projectname'];
			echo '<br>';
	
			echo 'Series ID: ';
			echo $_POST['seriesid'];
			echo '<br>';
	
			echo 'Meeting ID: ';
			echo $_POST['meetingid'];
			echo '<br>';
			
			echo 'Date: ';
			echo $_POST['date'];
			echo '<br>';
			
			echo 'Author: ';
			echo $_POST['author'];
			echo '<br>';
			
			echo 'Type: ';
			echo $_POST['type'];
			echo '<br>';
			
			echo 'Main header: ';
			echo $_POST['mainheader'];
			echo '<br>';
			
			echo 'Time: ';
			echo $_POST['time'];
			echo '<br>';
			
			echo 'Time2: ';
			echo $_POST['time2'];
			echo '<br>';
			
			echo 'Jobsite: ';
			echo $_POST['jobsite'];
			echo '<br>';
			
			echo 'Present count: ';
			echo $_POST['presentcount'];
			echo '<br>';
			
			echo 'Not present count: ';
			echo $_POST['notpresentcount'];
			echo '<br>';
			
			echo 'Content rows: ';
			echo $_POST['contentrows'];
			echo '<br>';
			
			echo 'Send: ';
			echo $_POST['send'];
			echo '<br>';
		}
    }
?>
