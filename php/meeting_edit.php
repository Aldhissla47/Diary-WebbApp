<?php
	$server = 'misaw.se.mysql';
	$echo = false;
	
    if (!empty($_POST['client']) && !empty($_POST['projectnr']) && !empty($_POST['projectname']) && !empty($_POST['seriesid']) && !empty($_POST['meetingid']) && !empty($_POST['author']) && !empty($_POST['time']) && !empty($_POST['time2']) && !empty($_POST['jobsite']) && !empty($_POST['presentcount']) && !empty($_POST['notpresentcount']) && !empty($_POST['contentrows']) && !empty($_POST['send'])) {

        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		$client = $_POST['client'];
        $projectnr = $_POST['projectnr'];
        $projectname = $_POST['projectname'];
		$series = $_POST['seriesid'];
		$meeting = $_POST['meetingid'];
		$author = $_POST['author'];
		
        // Protect from sql-injections
		$time = stripslashes($_POST['time']);
        $time = $conn->real_escape_string($time);
		
		$time2 = stripslashes($_POST['time2']);
        $time2 = $conn->real_escape_string($time2);
		
		$jobsite = stripslashes($_POST['jobsite']);
        $jobsite = $conn->real_escape_string($jobsite);
		
		$success = true;
		$fail = 0;
		if ($_POST['send'] == 'no') {
			$send = 0;
		} else if ($_POST['send'] == 'yes') {
			$send = 1;
		} else {
			$send = 2;
		}
		$sql = "SELECT * FROM db_project_meeting_protocol WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND id='". $meeting ."'";
		$protocol_backup_result = $conn->query($sql);
		
		$sql = "UPDATE db_project_meeting_protocol SET time='". $time ."', time2='". $time2 ."', jobsite='". $jobsite ."', locked='". $send ."' WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND id='". $meeting ."'";
		if ($echo) {
			echo $sql;
			echo '<br><br>';
		}
		if ($conn->query($sql)) {
			$sql = "SELECT * FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."'";
			$present_backup_result = $conn->query($sql);
		
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
						$fail = 1;
						if ($echo) {
							echo 'Email break!<br>';
						}
						break 1;
					}
					$presentid = $i + 1;
					
					$sql = "SELECT id FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND id='". $presentid ."'";
					if ($echo) {
						echo $sql;
						echo '<br>';
					}
					$result = $conn->query($sql);
					if ($result->num_rows > 0) {
						$sql = "UPDATE db_project_meeting_present SET fullname='". $name ."', company='". $company ."', email='". $email ."', present=1 WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND id='". $presentid ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						if (!$conn->query($sql)) {
							$success = false;
							$fail = 1;
							if ($echo) {
								echo 'break!<br>';
							}
							break 1;
						}
					} else {
						$sql = "INSERT INTO db_project_meeting_present (client, number, name, series, meeting, id, fullname, company, email, present) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $series ."','". $meeting ."','". $presentid ."','". $name ."','". $company ."','". $email ."',1)";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						if (!$conn->query($sql)) {
							$success = false;
							$fail = 1;
							if ($echo) {
								echo 'break!<br>';
							}
							break 1;
						}
					}
					$insertedpresents++;
				} else {
					$success = false;
					$fail = 1;
					if ($echo) {
						echo 'break!<br>';
					}
					break 1;
				}
			}
			$i++;
			$sql = "DELETE FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND id>'". $i ."' AND present='1'";
			$conn->query($sql);
			if ($echo) {
				echo $sql;
				echo '<br>';

				echo 'insertedpresents: ';
				echo $insertedpresents;
				echo '<br><br>';
			}
			if ($success) {
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
							$fail = 1;
							if ($echo) {
								echo 'Email break!<br>';
							}
							break 1;
						}
						$presentid = $insertedpresents + $i + 1;
						
						$sql = "SELECT id FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND id='". $presentid ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							$sql = "UPDATE db_project_meeting_present SET fullname='". $name ."', company='". $company ."', email='". $email ."', present=0 WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND id='". $presentid ."'";
							if ($echo) {
								echo $sql;
								echo '<br>';
							}
							if (!$conn->query($sql)) {
								$success = false;
								$fail = 1;
								if ($echo) {
									echo 'break!<br>';
								}
								break 1;
							}
						} else {
							$sql = "INSERT INTO db_project_meeting_present (client, number, name, series, meeting, id, fullname, company, email, present) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $series ."','". $meeting ."','". $presentid ."','". $name ."','". $company ."','". $email ."',0)";
							if ($echo) {
								echo $sql;
								echo '<br>';
							}
							if (!$conn->query($sql)) {
								$success = false;
								$fail = 1;
								if ($echo) {
									echo 'break!<br>';
								}
								break 1;
							}
						}
						$insertednotpresents++;
					} else {
						$success = false;
						$fail = 1;
						if ($echo) {
							echo 'break!<br>';
						}
						break 1;
					}
				}
				$sql = "DELETE FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND id>'". ($insertedpresents + $insertednotpresents) ."'";
				$conn->query($sql);
				if ($echo) {
					echo $sql;
					echo '<br>';

					echo 'insertednotpresents: ';
					echo $insertednotpresents;
					echo '<br><br>';
				}
			}
			if ($success) {
				$sql = "SELECT * FROM db_project_meeting_header WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."'";
				$header_backup_result = $conn->query($sql);
				
				$sql = "SELECT * FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."'";
				$task_backup_result = $conn->query($sql);
			
				$insertedheaders = 0;
				for ($i = 0; $i < $_POST['contentrows']; $i++) {
					if (!empty($_POST['taskrows'. $i .'']) && !empty($_POST['header'. $i .''])) {
						// Protect from sql-injections
						$header = stripslashes($_POST['header'. $i .'']);
						$header = $conn->real_escape_string($header);
						
						$sql = "SELECT id FROM db_project_meeting_header WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND id='". $i ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							$sql = "UPDATE db_project_meeting_header SET text='". $header ."' WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND id='". $i ."'";
							if ($echo) {
								echo $sql;
								echo '<br>';
							}
							if (!$conn->query($sql)) {
								$success = false;
								$fail = 2;
								if ($echo) {
									echo 'break!<br>';
								}
								break 1;
							}
						} else { // If $result->num_rows == 0
							$sql = "INSERT INTO db_project_meeting_header (client, number, name, series, meeting, id, text) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $series ."','". $meeting ."','". $insertedheaders ."','". $header ."')";
							if ($echo) {
								echo $sql;
								echo '<br>';
							}
							if (!$conn->query($sql)) {
								$success = false;
								$fail = 2;
								if ($echo) {
									echo 'break!<br>';
								}
								break 1;
							}
						}
						$insertedtasks = 0;
						for ($j = 0; $j < $_POST['taskrows'. $i .'']; $j++) {
							if (!empty($_POST['task_id'. $i .''. $j .'']) && !empty($_POST['task'. $i .''. $j .''])) {
								// Protect from sql-injections
								$task = stripslashes($_POST['task'. $i .''. $j .'']);
								$task = $conn->real_escape_string($task);
								
								$taskid = stripslashes($_POST['task_id'. $i .''. $j .'']);
								$taskid = $conn->real_escape_string($taskid);
								
								$supervisor = array();
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
									$fail = 2;
									if ($echo) {
										echo 'Supercount break!<br>';
									}
									break 2;
								}
								$sql = "SELECT id FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND header='". $i ."' AND id='". $taskid ."'";
								if ($echo) {
									echo $sql;
									echo '<br>';
								}
								$result = $conn->query($sql);
								if ($result->num_rows > 0) {
									$sql = "UPDATE db_project_meeting_task SET text='". $task ."', supervisor1='". $supervisor[0] ."', supervisor2='". $supervisor[1] ."', supervisor3='". $supervisor[2] ."' WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND header='". $i ."' AND id='". $taskid ."'";
									if ($echo) {
										echo $sql;
										echo '<br>';
									}
									if (!$conn->query($sql)) {
										$success = false;
										$fail = 2;
										if ($echo) {
											echo 'break!<br>';
										}
										break 2;
									}
								} else { // If $result->num_rows == 0
									$sql = "INSERT INTO db_project_meeting_task (client, number, name, series, meeting, header, id, text, supervisor1, supervisor2, supervisor3) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $series ."','". $meeting ."','". $insertedheaders ."','". $taskid ."','". $task ."','". $supervisor[0] ."','". $supervisor[1] ."','". $supervisor[2] ."')";
									if ($echo) {
										echo $sql;
										echo '<br>';
									}
									if (!$conn->query($sql)) {
										$success = false;
										$fail = 2;
										if ($echo) {
											echo 'break!<br>';
										}
										break 2;
									}
								}
								$insertedtasks++;
								
								if ($send == 2) {
									if ($echo) {
										echo '<br>';
									}
									if ($supervisor[0] == 'Klart' || $supervisor[1] == 'Klart' || $supervisor[2] == 'Klart') {
										$sql = "SELECT MAX(meeting) AS meeting FROM `db_project_meeting_task` WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting!='". $meeting ."' AND header='". $insertedheaders ."' AND text='". $task ."'";
										if ($echo) {
											echo $sql;
											echo '<br>';
										}
										$max_result = $conn->query($sql);
										if ($max_result->num_rows == 1) {
											$max_row = $max_result->fetch_assoc();
											
											$sql = "SELECT id, supervisor1, supervisor2, supervisor3, completed FROM db_project_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND category='Serie: ". $series ." Möte: ". $max_row['meeting'] ."' AND question='". $task ."'";
											if ($echo) {
												echo $sql;
												echo '<br>';
											}
											$result = $conn->query($sql);
											if ($result->num_rows == 1) {
												$row = $result->fetch_assoc();
												if ($row['completed'] == null) {
													$sql = "UPDATE db_project_task SET answer='Klart vid möte: ". $meeting ."', completed='". date('Y-m-d') ."', worker='". $row['supervisor1'] ."' WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND id='". $row['id'] ."'";
													if ($echo) {
														echo $sql;
														echo '<br>';
													}
													if (!$conn->query($sql)) {
														if ($echo) {
															echo 'Update failed!';
															echo '<br>';
														}
													}
												}
											}
										}
									} else {
										$sql = "SELECT permission, title FROM db_project_member WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND (user='". $supervisor[0] ."' OR user='". $supervisor[1] ."' OR user='". $supervisor[2] ."')";
										if ($echo) {
											echo $sql;
											echo '<br>';
										}
										$result = $conn->query($sql);
										if ($result->num_rows > 0) {
											$sql = "SELECT id, supervisor1, supervisor2, supervisor3, completed FROM db_project_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND category='Serie: ". $series ." Möte: ". $meeting ."' AND question='". $task ."'";
											if ($echo) {
												echo $sql;
												echo '<br>';
											}
											$result = $conn->query($sql);
											if ($result->num_rows == 0) {
												$sql = "SELECT MAX(id) AS id FROM db_project_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."'";
												if ($echo) {
													echo $sql;
													echo '<br>';
												}
												$max_result = $conn->query($sql);
												if ($max_result->num_rows > 0) {
													$max_row = $max_result->fetch_assoc();
													$maxid = $max_row['id'] + 1;
												} else {
													$maxid = 1;
												}
												$sql = "INSERT INTO db_project_task (client, number, name, id, category, created, question, supervisor1, supervisor2, supervisor3, author, deadline, answer, completed, worker, private) VALUES ('". $client ."','". $projectnr ."','". $projectname ."','". $maxid ."','Serie: ". $series ." Möte: ". $meeting ."','". date('Y-m-d') ."','". $task ."','". $supervisor[0] ."','". $supervisor[1] ."','". $supervisor[2] ."','". $author ."',NULL,'',NULL,'',0)";
												if ($echo) {
													echo $sql;
													echo '<br>';
												}
												if (!$conn->query($sql)) {
													if ($echo) {
														echo 'Insert failed!';
														echo '<br>';
													}
												}
											}
										}
									}
								}
							} else { // If empty($_POST['task'. $i .''. $j .''])
								$success = false;
								$fail = 2;
								if ($echo) {
									echo 'break!<br>';
								}
								break 2;
							}
						}
						$j++;
						$sql = "DELETE FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND header='". $i ."' AND id>'". $j ."'";
						$conn->query($sql);
						if ($echo) {
							echo $sql;
							echo '<br>';
							
							echo 'insertedtasks: ';
							echo $insertedtasks;
							echo '<br>';
						}
						$insertedheaders++;
					} else { // If empty($_POST['taskrows'. $i .'']) && empty($_POST['header'. $i .''])
						$success = false;
						$fail = 2;
						if ($echo) {
							echo 'break!<br>';
						}
						break 1;
					}
					if ($echo) {
						echo '<br>';
					}
				}
				$i++;
				$sql = "DELETE FROM db_project_meeting_header WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."' AND id>'". $i ."'";
				$conn->query($sql);
				if ($echo) {
					echo $sql;
					echo '<br>';
					
					echo 'insertedheaders: ';
					echo $insertedheaders;
					echo '<br>';
				}
			}
		} else { // If !$conn->query($sql)
			$success = false;
		}
		if ($success) {
			if (!$echo) {
				header("Location: meeting_page.php?series=". $series ."");
			}
		} else {
			if ($fail > 1) { // failed after headers
				if ($task_backup_result->num_rows > 0) {
					$sql = "DELETE FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."'";
					if ($echo) {
						echo $sql;
						echo '<br>';
					}
					$conn->query($sql);
					while ($task_backup_row = $task_backup_result->fetch_assoc()) {
					$sql = "INSERT INTO db_project_meeting_task (client, number, name, series, meeting, header, id, text, supervisor1, supervisor2, supervisor3) VALUES ('". $task_backup_row['client'] ."','". $task_backup_row['number'] ."','". $task_backup_row['name'] ."','". $task_backup_row['series'] ."','". $task_backup_row['meeting'] ."','". $task_backup_row['header'] ."','". $task_backup_row['id'] ."','". $task_backup_row['text'] ."','". $task_backup_row['supervisor1'] ."','". $task_backup_row['supervisor2'] ."','". $task_backup_row['supervisor3'] ."')";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$conn->query($sql);
					}
					if ($echo) {
						echo '<br>';
					}
				}
				if ($header_backup_result->num_rows > 0) {
					$sql = "DELETE FROM db_project_meeting_header WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."'";
					if ($echo) {
						echo $sql;
						echo '<br>';
					}
					$conn->query($sql);
					while ($header_backup_row = $header_backup_result->fetch_assoc()) {
						$sql = "INSERT INTO db_project_meeting_header (client, number, name, series, meeting, id, text) VALUES ('". $header_backup_row['client'] ."','". $header_backup_row['number'] ."','". $header_backup_row['name'] ."','". $header_backup_row['series'] ."','". $header_backup_row['meeting'] ."','". $header_backup_row['id'] ."','". $header_backup_row['text'] ."')";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$conn->query($sql);
					}
					if ($echo) {
						echo '<br>';
					}
				}
			}
			if ($fail > 0) { // failed after presents
				if ($present_backup_result->num_rows > 0) {
					$sql = "DELETE FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND meeting='". $meeting ."'";
					if ($echo) {
						echo $sql;
						echo '<br>';
					}
					$conn->query($sql);
					while ($present_backup_row = $present_backup_result->fetch_assoc()) {
						$sql = "INSERT INTO db_project_meeting_present (client, number, name, series, meeting, id, fullname, company, email) VALUES ('". $present_backup_row['client'] ."','". $present_backup_row['number'] ."','". $present_backup_row['name'] ."','". $present_backup_row['series'] ."','". $present_backup_row['meeting'] ."','". $present_backup_row['id'] ."','". $present_backup_row['fullname'] ."','". $present_backup_row['company'] ."','". $present_backup_row['email'] ."')";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$conn->query($sql);
					}
					if ($echo) {
						echo '<br>';
					}
				}
				if ($protocol_backup_result->num_rows == 1) {
					$protocol_backup_row = $protocol_backup_result->fetch_assoc();
					$sql = "UPDATE db_project_meeting_protocol SET time='". $protocol_backup_row['time'] ."', time2='". $protocol_backup_row['time2'] ."', jobsite='". $protocol_backup_row['jobsite'] ."', locked='". $protocol_backup_row['locked'] ."' WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND id='". $meeting ."'";
					if ($echo) {
						echo $sql;
						echo '<br>';
					}
					$conn->query($sql);
				}
			}
			if (!$echo) {
				header("Location: meeting_edit_page.php?". $id ."");
			} else {
				echo $sql1;
				echo '<br>';
				
				echo $sql2;
				echo '<br>';
				
				echo $sql3;
				echo '<br>';
				
				echo $sql4;
				echo '<br>';
			}
		}
		$conn->close();
    } else {
		if (!$echo) {
			header("Location: meeting_edit_page.php?". $_POST['meetingid'] ."");
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
			
			echo 'Author: ';
			echo $_POST['author'];
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
