<?php
	$server = 'misaw.se.mysql';
	$echo = false;
	
    if (!empty($_POST['reviewer']) && !empty($_POST['client']) && !empty($_POST['company']) && !empty($_POST['workday']) && !empty($_POST['date']) && !empty($_POST['projectnr']) && !empty($_POST['projectname']) && !empty($_POST['supervisor']) && !empty($_POST['jobsite']) && !empty($_POST['workrows']) && !empty($_POST['org_workday']) && !empty($_POST['org_date']) && !empty($_POST['org_jobsite']) && !empty($_POST['send']) && !empty($_POST['inbox'])) {
        
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		$reviewer = $_POST['reviewer'];
		$client = $_POST['client'];
        $number = $_POST['projectnr'];
        $name = $_POST['projectname'];
		$company = $_POST['company'];
		$org_workday = $_POST['org_workday'];
		$org_date = $_POST['org_date'];
		$org_jobsite = $_POST['org_jobsite'];
		$send = $_POST['send'];
		
		$inbox = $_POST['inbox'];
		
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
		
		$clientcomments = stripslashes($_POST['clientcomments']);
		str_replace('"', "'", $clientcomments);
        $clientcomments = $conn->real_escape_string($clientcomments);
		
		$success = true;
		$fail = 0;
		
		if ($send == 'no') {
			$locked = 0;
		} else if ($send == 'yes') {
			$locked = 1;
		} else {
			$locked = 2;
		}
		if ($date > date('Y-m-d')) {
			if (!$echo) {
				header("Location: diary_edit_page.php?company=". $company ."&workday=". $org_workday ."");
			} else {
				echo '$date > '. date('Y-m-d') .'';
				echo '<br>';
			}
		} else {
			$sql = "SELECT * FROM db_project_diary WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."'";
			$diary_backup_result = $conn->query($sql);

			$sql = "UPDATE db_project_diary SET workday='". $workday ."', supervisor='". $supervisor ."', reviewer='". $reviewer ."', locked='". $locked ."', jobsite='". $jobsite ."', date='". $date ."', clientcomments='". $clientcomments ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."'";
			if ($echo) {
				echo $sql;
				echo '<br><br>';
			}
			if ($conn->query($sql)) {
				$sql = "SELECT * FROM db_project_diary_job WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."'";
				$job_backup_result = $conn->query($sql);
				
				$sql = "SELECT * FROM db_project_diary_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."'";
				$job_crew_backup_result = $conn->query($sql);
				
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
						
						$sql = "SELECT id FROM db_project_diary_job WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."' AND id='". $jobid ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$result = $conn->query($sql);
						if ($result->num_rows > 0) {
							$sql = "UPDATE db_project_diary_job SET workday='". $workday ."', job='". $crewjob ."', comments='". $crewcomments ."', status='". $jobstatus ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."' AND id='". $jobid ."'";
							if ($echo) {
								echo $sql;
								echo '<br>';
							}
							if (!$conn->query($sql)) {
								$success = false;
								if ($echo) {
									echo 'break!<br>';
								}
								$fail = 1;
								break 1;
							}
						} else {
							$sql = "INSERT INTO db_project_diary_job (client, number, name, company, workday, id, job, comments, status) VALUES ('". $client ."','". $number ."','". $name ."','". $company ."','". $workday ."','". $jobid ."','". $crewjob ."','". $crewcomments .",'". $jobstatus ."')";
							if ($echo) {
								echo $sql;
								echo '<br>';
							}
							if (!$conn->query($sql)) {
								$success = false;
								if ($echo) {
									echo 'break!<br>';
								}
								$fail = 1;
								break 1;
							}
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
								
								$crewid = $j + 1;
								
								$sql = "SELECT crewid FROM db_project_diary_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."' AND jobid='". $jobid ."' AND crewid='". $crewid ."'";
								if ($echo) {
									echo $sql;
									echo '<br>';
								}
								$crew_result = $conn->query($sql);
								if ($crew_result->num_rows > 0) {
									$sql = "UPDATE db_project_diary_crew SET workday='". $workday ."', fullname='". $crewname ."', jobtype='". $crewtype ."', time='". $crewtime ."', own='". $own ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."' AND jobid='". $jobid ."' AND crewid='". $crewid ."'";
									if ($echo) {
										echo $sql;
										echo '<br>';
									}
									if (!$conn->query($sql)) {
										$success = false;
										if ($echo) {
											echo 'break!<br>';
										}
										$fail = 1;
										break 2;
									}
								} else {
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
										$fail = 1;
										break 2;
									}
								}
								$insertedcrew++;
								if ($echo) {
									echo '<br>';
								}
							} else {
								$success = false;
								if ($echo) {
									echo 'break!<br>';
								}
								$fail = 1;
								break 2;
							}
						}
						$insertedjobs++;
						
						$j++;
						$sql = "DELETE FROM db_project_diary_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."' AND jobid='". $jobid ."' AND crewid>'". $j ."'";
						$conn->query($sql);
						if ($echo) {
							echo $sql;
							echo '<br>';
							
							echo 'insertedcrew: ';
							echo $insertedcrew;
							echo '<br>';
						}
					}
				}
				if ($echo) {
					echo 'insertedjobs: ';
					echo $insertedjobs;
					echo '<br>';
				}
				if ($i == $_POST['workrows']) {
					$i++;
					$sql = "DELETE FROM db_project_diary_job WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."' AND id>'". $i ."'";
					$conn->query($sql);
					if ($echo) {
						echo $sql;
						echo '<br>';
					}
				}
				if ($success) {
					$sql = "SELECT * FROM db_project_abnormality WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."'";
					$abnorms_backup_result = $conn->query($sql);
					
					$sql = "SELECT * FROM db_project_abnormality_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND workday='". $org_workday ."' AND company='". $company ."'";
					$abnorms_crew_backup_result = $conn->query($sql);
					
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
							$sql = "UPDATE db_project_abnormality SET workday='". $workday ."', header='". $header ."', jobsite='". $jobsite ."', comments='". $comments ."', economic_consequence='". $eco ."', time_consequence='". $time ."', status='". $status ."', locked='". $locked ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND id='". $id ."' AND workday='". $org_workday ."'";
							if ($echo) {
								echo $sql;
								echo '<br>';
							}
							if (!$conn->query($sql)) {
								$success = false;
								if ($echo) {
									echo 'break!<br>';
								}
								$fail = 2;
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
									
									$sql = "SELECT crewid FROM db_project_abnormality_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND id='". $id ."' AND workday='". $workday ."' AND crewid='". $crewid ."'";
									if ($echo) {
										echo $sql;
										echo '<br>';
									}
									$crew_result = $conn->query($sql);
									if ($crew_result->num_rows > 0) {
										$sql = "UPDATE db_project_abnormality_crew SET fullname='". $crewname ."', jobtype='". $crewtype ."', time='". $crewtime ."', own='". $own ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND id='". $id ."' AND workday='". $workday ."' AND company='". $company ."' AND crewid='". $crewid ."'";
										if ($echo) {
											echo $sql;
											echo '<br>';
										}
										if (!$conn->query($sql)) {
											$success = false;
											if ($echo) {
												echo 'break!<br>';
											}
											$fail = 2;
											break 2;
										}
									} else {
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
											$fail = 2;
											break 2;
										}
									}
									$insertedcrew++;
									if ($echo) {
										echo '<br>';
									}
								}
							}
							if ($insertedcrew == 0 && $status != 3) {
								$success = false;
								if ($echo) {
									echo 'break!<br>';
								}
								$fail = 2;
								break 1;
							}
							$insertedabnorms++;
							
							$j++;
							$sql = "DELETE FROM db_project_abnormality_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND id='". $id ."' AND workday='". $workday ."' AND company='". $company ."' AND crewid>'". $j ."'";
							$conn->query($sql);
							if ($echo) {
								echo $sql;
								echo '<br>';
								
								echo 'insertedcrew: ';
								echo $insertedcrew;
								echo '<br>';
							}
						}
					}
					if ($echo) {
						echo 'insertedabnorms: ';
						echo $insertedabnorms;
						echo '<br>';
					}
				}
				if ($insertedjobs == 0 && $insertedabnorms == 0) {
					$success = false;
					$fail = 2;
				}
				if ($success) {
					$sql = "SELECT * FROM db_project_diary_misc WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."'";
					$miscs_backup_result = $conn->query($sql);
					
					$insertedmiscs = 0;
					for ($i = 0; $i < $_POST['miscrows']; $i++) {				
						if (!empty($_POST['misc_category'. $i .'']) && !empty($_POST['misc_comments'. $i .''])) {
							// Protect from sql-injections
							$category = stripslashes($_POST['misc_category'. $i .'']);
							$category = $conn->real_escape_string($category);
							
							$comments = stripslashes($_POST['misc_comments'. $i .'']);
							str_replace('"', "'", $comments);
							$comments = $conn->real_escape_string($comments);
							
							$id = $i + 1;
							
							$sql = "SELECT id FROM db_project_diary_misc WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."' AND id='". $id ."'";
							if ($echo) {
								echo $sql;
								echo '<br>';
							}
							$misc_result = $conn->query($sql);
							if ($misc_result->num_rows > 0) {
								$sql = "UPDATE db_project_diary_misc SET workday='". $workday ."', category='". $category ."', comments='". $comments ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."' AND id='". $id ."'";
								if ($echo) {
									echo $sql;
									echo '<br>';
								}
								if (!$conn->query($sql)) {
									$success = false;
									if ($echo) {
										echo 'break!<br>';
									}
									$fail = 3;
									break 1;
								}
							} else {
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
									$fail = 3;
									break 1;
								}
							}
							$insertedmiscs++;
						}
					}
					if ($echo) {
						echo 'insertedmiscs: ';
						echo $insertedmiscs;
						echo '<br>';
					}
					if ($i == $_POST['miscrows']) {
						$i++;
						$sql = "DELETE FROM db_project_diary_misc WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $org_workday ."' AND id>'". $i ."'";
						$conn->query($sql);
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
					}
				}
			} else {
				$success = false;
			}
			if ($success) {
				if (!$echo) {
					if ($inbox == 'yes') {
						header("Location: index.php");
					} else {
						header("Location: project_page.php?client=". $client ."&number=". $number ."&name=". $name ."&selector=diary");
					}
				}
			} else { // If NOT success
				if ($fail > 2) { // failed after miscs
					if ($miscs_backup_result->num_rows > 0) {
						$sql = "DELETE FROM db_project_diary_misc WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $workday ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$conn->query($sql);
						while ($miscs_backup_row = $miscs_backup_result->fetch_assoc()) {
							$sql = "INSERT INTO db_project_diary_misc (client, number, name, company, workday, id, category, comments) VALUES ('". $miscs_backup_row['client'] ."','". $miscs_backup_row['number'] ."','". $miscs_backup_row['name'] ."','". $miscs_backup_row['company'] ."','". $miscs_backup_row['workday'] ."','". $miscs_backup_row['id'] ."','". $miscs_backup_row['category'] ."','". $miscs_backup_row['comments'] ."')";
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
				if ($fail > 1) { // failed after abnorms
					if ($abnorms_crew_backup_result->num_rows > 0) {
						$sql = "DELETE FROM db_project_abnormality_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND workday='". $workday ."' AND company='". $company ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$conn->query($sql);
						while ($abnorms_crew_backup_row = $abnorms_crew_backup_result->fetch_assoc()) {
							$sql = "INSERT INTO db_project_abnormality_crew (client, number, name, id, workday, crewid, company, fullname, jobtype, time, own) VALUES ('". $abnorms_crew_backup_row['client'] ."','". $abnorms_crew_backup_row['number'] ."','". $abnorms_crew_backup_row['name'] ."','". $abnorms_crew_backup_row['id'] ."','". $abnorms_crew_backup_row['workday'] ."','". $abnorms_crew_backup_row['crewid'] ."','". $abnorms_crew_backup_row['company'] ."','". $abnorms_crew_backup_row['fullname'] ."','". $abnorms_crew_backup_row['jobtype'] ."','". $abnorms_crew_backup_row['time'] ."','". $abnorms_crew_backup_row['own'] ."')";
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
					if ($abnorms_backup_result->num_rows > 0) {
						$sql = "DELETE FROM db_project_abnormality WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $workday ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$conn->query($sql);
						while ($abnorms_backup_row = $abnorms_backup_result->fetch_assoc()) {
							$sql = "INSERT INTO db_project_abnormality (client, number, name, id, workday, company, header, jobsite, comments, economic_consequence, time_consequence, status, locked) VALUES ('". $abnorms_backup_row['client'] ."','". $abnorms_backup_row['number'] ."','". $abnorms_backup_row['name'] ."','". $abnorms_backup_row['id'] ."','". $abnorms_backup_row['workday'] ."','". $abnorms_backup_row['company'] ."','". $abnorms_backup_row['header'] ."','". $abnorms_backup_row['jobsite'] ."','". $abnorms_backup_row['comments'] ."','". $abnorms_backup_row['economic_consequence'] ."','". $abnorms_backup_row['time_consequence'] ."','". $abnorms_backup_row['status'] ."','". $abnorms_backup_row['locked'] ."')";
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
				if ($fail > 0) { // failed after jobs
					if ($job_crew_backup_result->num_rows > 0) {
						$sql = "DELETE FROM db_project_diary_crew WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $workday ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$conn->query($sql);
						while ($job_crew_backup_row = $job_crew_backup_result->fetch_assoc()) {
							$sql = "INSERT INTO db_project_diary_crew (client, number, name, company, workday, jobid, crewid, fullname, jobtype, time, own) VALUES ('". $job_crew_backup_row['client'] ."','". $job_crew_backup_row['number'] ."','". $job_crew_backup_row['name'] ."','". $job_crew_backup_row['company'] ."','". $job_crew_backup_row['workday'] ."','". $job_crew_backup_row['jobid'] ."','". $job_crew_backup_row['crewid'] ."','". $job_crew_backup_row['fullname'] ."','". $job_crew_backup_row['jobtype'] ."','". $job_crew_backup_row['time'] ."','". $job_crew_backup_row['own'] ."')";
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
					if ($job_backup_result->num_rows > 0) {
						$sql = "DELETE FROM db_project_diary_job WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $workday ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$conn->query($sql);
						while ($job_backup_row = $job_backup_result->fetch_assoc()) {
							$sql = "INSERT INTO db_project_diary_job (client, number, name, company, workday, id, job, comments, status) VALUES ('". $job_backup_row['client'] ."','". $job_backup_row['number'] ."','". $job_backup_row['name'] ."','". $job_backup_row['company'] ."','". $job_backup_row['workday'] ."','". $job_backup_row['id'] ."','". $job_backup_row['job'] ."','". $job_backup_row['comments'] .",'". $job_backup_row['status'] ."')";
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
					if ($diary_backup_result->num_rows == 1) {
						$diary_backup_row = $diary_backup_result->fetch_assoc();
						$sql = "UPDATE db_project_diary SET workday='". $diary_backup_row['workday'] ."', supervisor='". $diary_backup_row['supervisor'] ."', reviewer='". $diary_backup_row['reviewer'] ."', locked='". $diary_backup_row['locked'] ."', jobsite='". $diary_backup_row['jobsite'] ."', date='". $diary_backup_row['date'] ."', clientcomments='". $diary_backup_row['clientcomments'] ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND company='". $company ."' AND workday='". $workday ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						$conn->query($sql);
					}
				}
				if (!$echo) {
					header("Location: diary_edit_page.php?company=". $company ."&workday=". $org_workday ."");
				}
			}
		}
        $conn->close();
    } else {
		if (!$echo) {
			header("Location: diary_edit_page.php?company=". $_POST['company'] ."&workday=". $_POST['org_workday'] ."");
		} else {			
			echo 'Reviewer: ';
			echo $_POST['reviewer'];
			echo '<br>';
			
			echo 'Client: ';
			echo $_POST['client'];
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
			
			echo 'Original workday: ';
			echo $_POST['org_workday'];
			echo '<br>';
			
			echo 'Original date: ';
			echo $_POST['org_date'];
			echo '<br>';
			
			echo 'Original jobsite: ';
			echo $_POST['org_jobsite'];
			echo '<br>';
			
			echo 'Send: ';
			echo $_POST['send'];
			echo '<br>';
			
			echo 'Inbox: ';
			echo $_POST['inbox'];
			echo '<br>';
		}
    }
?>
