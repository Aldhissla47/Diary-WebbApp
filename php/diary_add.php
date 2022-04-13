<?php
	$server = 'misaw.se.mysql';
	$echo = false;
	
    if (!empty($_POST['reviewer']) && !empty($_POST['client']) && !empty($_POST['author']) && !empty($_POST['company']) && !empty($_POST['workday']) && !empty($_POST['date']) && !empty($_POST['projectnr']) && !empty($_POST['projectname']) && !empty($_POST['supervisor']) && !empty($_POST['jobsite']) && !empty($_POST['workrows']) && !empty($_POST['abnormsrows']) && !empty($_POST['miscrows']) && !empty($_POST['send'])) {

        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		$client = $_POST['client'];
        $number = $_POST['projectnr'];
        $name = $_POST['projectname'];
		$company = $_POST['company'];
		$author = $_POST['author'];
		$reviewer = '';
		
        // Protect from sql-injections
        $workday = stripslashes($_POST['workday']);
        $workday = $conn->real_escape_string($workday);

		$date = stripslashes($_POST['date']);
        $date = $conn->real_escape_string($date);
		
		$supervisor = stripslashes($_POST['supervisor']);
        $supervisor = $conn->real_escape_string($supervisor);
		
		$jobsite = stripslashes($_POST['jobsite']);
		str_replace('"', "'", $jobsite);
        $jobsite = $conn->real_escape_string($jobsite);
		
		$sql = "SELECT workday FROM db_project_diary WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND date='". $date ."'";
		if ($echo) {
			echo $sql;
			echo '<br><br>';
		}
		$result = $conn->query($sql);
		if ($result->num_rows > 0 || $date > date('Y-m-d')) {
			if (!$echo) {
				header("Location: diary_add_page.php");
			} else {
				echo '$result->num_rows > 0 or $date > '. date('Y-m-d') .'';
				echo '<br>';
			}
		} else {
			$success = true;
			if ($_POST['send'] == 'yes') {
				$locked = 1;
				$reviewer = $_POST['reviewer'];
			} else {
				$locked = 0;
			}
			$sql = "INSERT INTO db_project_diary (client, number, name, company, workday, author, supervisor, reviewer, locked, jobsite, date, clientcomments) VALUES ('". $client ."','". $number ."','". $name ."','". $company ."','". $workday ."','". $author ."','". $supervisor ."','". $reviewer ."','". $locked ."','". $jobsite ."','". $date ."','')";
			if ($echo) {
				echo $sql;
				echo '<br><br>';
			}
			if ($conn->query($sql)) {
				$insertedjobs = 0;
				for ($i = 0; $i < $_POST['workrows']; $i++) {
					if (!empty($_POST['crew_job'. $i .'']) && !empty($_POST['crewrows'. $i .'']) && !empty($_POST['job_status'. $i .''])) {
						// Protect from sql-injections
						$crewjob = stripslashes($_POST['crew_job'. $i .'']);
						$crewjob = $conn->real_escape_string($crewjob);
						
						$jobstatus = stripslashes($_POST['job_status'. $i .'']);
						$jobstatus = $conn->real_escape_string($jobstatus);
						
						$crewcomments = stripslashes($_POST['crew_comments'. $i .'']);
						str_replace('"', "'", $crewcomments);
						$crewcomments = $conn->real_escape_string($crewcomments);

						$jobid = $insertedjobs + 1;
						
						$sql = "INSERT INTO db_project_diary_job (client, number, name, company, workday, id, job, comments, status) VALUES ('". $client ."','". $number ."','". $name ."','". $company ."','". $workday ."','". $jobid ."','". $crewjob ."','". $crewcomments ."','". $jobstatus ."')";
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
						$insertedcrew = 0;
						for ($j = 0; $j < $_POST['crewrows'. $i .'']; $j++) {
							if (!empty($_POST['crew_type'. $i .''. $j .'']) && !empty($_POST['crew_name'. $i .''. $j .'']) && !empty($_POST['crew_time'. $i .''. $j .''])) {
								// Protect from sql-injections
								$crewtype = stripslashes($_POST['crew_type'. $i .''. $j .'']);
								$crewtype = $conn->real_escape_string($crewtype);
								
								$crewname = stripslashes($_POST['crew_name'. $i .''. $j .'']);
								str_replace('"', "'", $crewname);
								$crewname = $conn->real_escape_string($crewname);
								
								$crewtime = stripslashes($_POST['crew_time'. $i .''. $j .'']);
								$crewtime = $conn->real_escape_string($crewtime);
								
								$own = $_POST['crew_radio'. $i .''. $j .''];
								
								$crewid = $insertedcrew + 1;
								
								$sql = "INSERT INTO db_project_diary_crew (client, number, name, company, workday, jobid, crewid, fullname, jobtype, time, own) VALUES ('". $client ."','". $number ."','". $name ."','". $company ."','". $workday ."','". $jobid ."','". $crewid ."','". $crewname ."','". $crewtype ."','". $crewtime ."','". $own ."')";
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
								$insertedcrew++;
							}
						}
						if ($insertedcrew == 0) {
							$success = false;
							if ($echo) {
								echo 'break!<br>';
							}
							break 1;
						}
						$insertedjobs++;
						if ($echo) {
							echo 'insertedcrew: ';
							echo $insertedcrew;
							echo '<br><br>';
						}
					}
				}
				if ($echo) {
					echo 'insertedjobs: ';
					echo $insertedjobs;
					echo '<br>';
				}
				if ($success) {
					$insertedabnorms = 0;
					for ($i = 0; $i < $_POST['abnormsrows']; $i++) {				
						if (!empty($_POST['abnorms_nr'. $i .'']) && !empty($_POST['abnorms_header'. $i .'']) && !empty($_POST['abnorms_jobsite'. $i .'']) && !empty($_POST['abnorms_comments'. $i .'']) && !empty($_POST['abnorms_status'. $i .'']) && !empty($_POST['abnormscrewrows'. $i .''])) {
							// Protect from sql-injections
							$id = stripslashes($_POST['abnorms_nr'. $i .'']);
							$id = $conn->real_escape_string($id);
							
							$header = stripslashes($_POST['abnorms_header'. $i .'']);
							str_replace('"', "'", $header);
							$header = $conn->real_escape_string($header);
							
							$jobsite = stripslashes($_POST['abnorms_jobsite'. $i .'']);
							str_replace('"', "'", $jobsite);
							$jobsite = $conn->real_escape_string($jobsite);
							
							$comments = stripslashes($_POST['abnorms_comments'. $i .'']);
							str_replace('"', "'", $comments);
							$comments = $conn->real_escape_string($comments);
							
							$status = stripslashes($_POST['abnorms_status'. $i .'']);
							$status = $conn->real_escape_string($status);
							
							if (!empty($_POST['abnorms_economic_checkbox'. $i .''])) {
								$eco = 1;
							} else {
								$eco = 0;
							}
							if (!empty($_POST['abnorms_time_checkbox'. $i .''])) {
								$time = 1;
							} else {
								$time = 0;
							}
							$sql = "INSERT INTO db_project_abnormality (client, number, name, id, workday, company, header, jobsite, comments, economic_consequence, time_consequence, status, locked) VALUES ('". $client ."','". $number ."','". $name ."','". $id ."','". $workday ."','". $company ."','". $header ."','". $jobsite ."','". $comments ."','". $eco ."','". $time ."','". $status ."','". $locked ."')";
							if ($echo) {
								echo $sql;
								echo '<br><br>';
							}
							if (!$conn->query($sql)) {
								$success = false;
								if ($echo) {
									echo 'break!<br>';
								}
								break 1;
							}
							$insertedcrew = 0;
							for ($j = 0; $j < $_POST['abnormscrewrows'. $i .'']; $j++) {
								if (!empty($_POST['abnorms_crew_type'. $i .''. $j .'']) && !empty($_POST['abnorms_crew_name'. $i .''. $j .'']) && !empty($_POST['abnorms_crew_time'. $i .''. $j .''])) {
									// Protect from sql-injections
									$crewtype = stripslashes($_POST['abnorms_crew_type'. $i .''. $j .'']);
									$crewtype = $conn->real_escape_string($crewtype);
									
									$crewname = stripslashes($_POST['abnorms_crew_name'. $i .''. $j .'']);
									str_replace('"', "'", $crewname);
									$crewname = $conn->real_escape_string($crewname);
									
									$crewtime = stripslashes($_POST['abnorms_crew_time'. $i .''. $j .'']);
									$crewtime = $conn->real_escape_string($crewtime);
									
									$own = $_POST['abnorms_crew_radio'. $i .''. $j .''];
									
									$crewid = $insertedcrew + 1;
									
									$sql = "INSERT INTO db_project_abnormality_crew (client, number, name, id, workday, crewid, company, fullname, jobtype, time, own) VALUES ('". $client ."','". $number ."','". $name ."','". $id ."','". $workday ."','". $crewid ."','". $company ."','". $crewname ."','". $crewtype ."','". $crewtime ."','". $own ."')";
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
									$insertedcrew++;
								}
							}
							if ($insertedcrew == 0 && $status != 3) {
								$success = false;
								if ($echo) {
									echo 'break!<br>';
								}
								break 1;
							}
							$insertedabnorms++;
							if ($echo) {
								echo 'insertedcrew: ';
								echo $insertedcrew;
								echo '<br><br>';
							}
						}
					}
					if ($echo) {
						echo 'insertedabnorms: ';
						echo $insertedabnorms;
						echo '<br>';
					}
				}
				if ($success) {
					if ($insertedjobs == 0 && $insertedabnorms == 0) {
						$success = false;
					}
				}
				if ($success) {
					$insertedmiscs = 0;
					for ($i = 0; $i < $_POST['miscrows']; $i++) {				
						if (!empty($_POST['misc_category'. $i .'']) && !empty($_POST['misc_comments'. $i .''])) {
							// Protect from sql-injections
							$category = stripslashes($_POST['misc_category'. $i .'']);
							$category = $conn->real_escape_string($category);
							
							$comments = stripslashes($_POST['misc_comments'. $i .'']);
							str_replace('"', "'", $comments);
							$comments = $conn->real_escape_string($comments);
							
							$id = $insertedmiscs + 1;
							
							$sql = "INSERT INTO db_project_diary_misc (client, number, name, company, workday, id, category, comments) VALUES ('". $client ."','". $number ."','". $name ."','". $company ."','". $workday ."','". $id ."','". $category ."','". $comments ."')";
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
							$insertedmiscs++;
						}
					}
					if ($echo) {
						echo 'insertedmiscs: ';
						echo $insertedmiscs;
						echo '<br>';
					}
				}
			} else {
				$success = false;
			}
			if ($success) {
				if (!$echo) {
					header("Location: project_page.php?client=". $client ."&number=". $number ."&name=". $name ."&selector=diary");
				}
			} else {
				$sql1 = "DELETE FROM db_project_diary_misc WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $workday ."'";
				$conn->query($sql1);

				$sql2 = "DELETE FROM db_project_abnormality_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND id='". $id ."' AND workday='". $workday ."'";
				$conn->query($sql2);

				$sql3 = "DELETE FROM db_project_abnormality WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND id='". $id ."' AND workday='". $workday ."'";
				$conn->query($sql3);

				$sql4 = "DELETE FROM db_project_diary_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $workday ."'";
				$conn->query($sql4);

				$sql5 = "DELETE FROM db_project_diary_job WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $workday ."'";
				$conn->query($sql5);

				$sql6 = "DELETE FROM db_project_diary WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $workday ."'";
				$conn->query($sql6);

				if (!$echo) {
					header("Location: diary_add_page.php");
				} else {
					echo $sql1;
					echo '<br>';
					
					echo $sql2;
					echo '<br>';
					
					echo $sql3;
					echo '<br>';
					
					echo $sql4;
					echo '<br>';
					
					echo $sql5;
					echo '<br>';
					
					echo $sql6;
					echo '<br>';
				}
			}
		}
		$conn->close();
    } else {
		if (!$echo) {
			header("Location: diary_add_page.php");
		} else {
			echo 'Reviewer: ';
			echo $_POST['reviewer'];
			echo '<br>';
			
			echo 'Client: ';
			echo $_POST['client'];
			echo '<br>';
			
			echo 'Author: ';
			echo $_POST['author'];
			echo '<br>';
			
			echo 'Company: ';
			echo $_POST['company'];
			echo '<br>';
			
			echo 'Workday: ';
			echo $_POST['workday'];
			echo '<br>';
			
			echo 'Date: ';
			echo $_POST['date'];
			echo '<br>';
			
			echo 'Project nr: ';
			echo $_POST['projectnr'];
			echo '<br>';
			
			echo 'Project name: ';
			echo $_POST['projectname'];
			echo '<br>';
			
			echo 'Supervisor: ';
			echo $_POST['supervisor'];
			echo '<br>';
			
			echo 'Jobsite: ';
			echo $_POST['jobsite'];
			echo '<br>';
			
			echo 'Workrows: ';
			echo $_POST['workrows'];
			echo '<br>';
			
			echo 'Abnormsrows: ';
			echo $_POST['abnormsrows'];
			echo '<br>';
			
			echo 'Miscrows: ';
			echo $_POST['miscrows'];
			echo '<br>';
			
			echo 'Send: ';
			echo $_POST['send'];
			echo '<br>';
		}
    }
?>
