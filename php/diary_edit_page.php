<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	}
	if (!empty($_GET['company']) && !empty($_GET['workday'])) {
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		$locked = 2;
		$inbox = 'no';
		
		if (!empty($_GET['client']) && !empty($_GET['number']) && !empty($_GET['name'])) {
			$sql = "SELECT permission FROM db_project_member WHERE client='". $_GET['client'] ."' AND number='". $_GET['number'] ."' AND name='". $_GET['name'] ."' AND user='". $_SESSION['user']['email'] ."'";
			$result = $conn->query($sql);
			if ($result->num_rows == 1) {
				$row = $result->fetch_assoc();
				$_SESSION['project']['client'] = $_GET['client'];
				$_SESSION['project']['number'] = $_GET['number'];
				$_SESSION['project']['name'] = $_GET['name'];
				$_SESSION['user']['permission'] = $row['permission'];
				$inbox = 'yes';
			} else {
				$conn->close();
				die();
				header("Location: index.php");
			}
		}
		if (!isset($_SESSION['project']) || (isset($_SESSION['user']['permission']) && $_SESSION['user']['permission'] == 4)) {
			$conn->close();
			die();
			header("Location: index.php");
		}
		$sql = "SELECT * FROM db_project_diary WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $_GET['company'] ."' AND workday='". $_GET['workday'] ."'";
		$result = $conn->query($sql);
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			
			$client = $row['client'];
			$projectnr = $row['number'];
			$projectname = $row['name'];
			$workday = $row['workday'];
			$date = $row['date'];
			$reviewer = $row['reviewer'];
			$locked = $row['locked'];
			$jobsite = $row['jobsite'];
			$company = $row['company'];
			$clientcomments = $row['clientcomments'];
			
			$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['author'] ."'";
			$member_result = $conn->query($sql);
			$member_row = $member_result->fetch_assoc();
			
			$authorname = "". $member_row['firstname'] ." ". $member_row['surname'] ."";
			
			$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['supervisor'] ."'";
			$member_result = $conn->query($sql);
			$member_row = $member_result->fetch_assoc();
			
			$supervisor = $row['supervisor'];
			$supervisorname = "". $member_row['firstname'] ." ". $member_row['surname'] ."";

			include 'get_company_info.php';
			$companyname = getCompanyInfo($company, "name");
			if ($companyname === false) {
				$companyname = $company;
			}
		} else {
			$conn->close();
			die();
			header("Location: index.php");
		}
		if ($_SESSION['user']['permission'] < 4) {
			if ($locked == 1) {
				$reviewer = true;
			} else {
				$reviewer = false;
			}
		} else if ($_SESSION['user']['permission'] == 5) {
			if ($locked == 0) {
				$reviewer = true;
			} else {
				$reviewer = false;
			}
		} else {
			if ($reviewer !== '') {
				$sql = "SELECT permission FROM db_project_member WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND user='". $reviewer ."'";
			} else {
				$sql = "SELECT permission FROM db_project_member WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND user='". $author ."'";
			}
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			if ($_SESSION['user']['permission'] < $row['permission'] && $locked == 0) {
				$reviewer = true;
			} else {
				$reviewer = false;
			}
		}
		$statustypes = array();
		$statusarraysize = 0;
		$sql = "SELECT * FROM db_project_diary_statustype";
		$statusresult = $conn->query($sql);
		if ($statusresult->num_rows > 0) {
			while ($statusrow = $statusresult->fetch_assoc()) {
				$statustypes[] = $statusrow['type'];
				$statusarraysize++;
			}
		}
		$jobtypes = array();
		$jobarraysize = 0;
		$sql = "SELECT * FROM db_project_diary_jobtype";
		$jobtype_result = $conn->query($sql);
		if ($jobtype_result->num_rows > 0) {
			while ($jobtype_row = $jobtype_result->fetch_assoc()) {
				$jobtypes[] = mb_substr($jobtype_row['type'], 0, null);
				$jobarraysize++;
			}
		}
		$conn->close();
	} else {
		header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."");
	}
	
	function weekday($arg1) {
		$day = array('Måndag', 'Tisdag', 'Onsdag', 'Torsdag', 'Fredag', 'Lördag', 'Söndag');
		$i = $arg1 - 1;
		return $day[$i];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Formulär </title>

		<link rel="stylesheet" type="text/css" href="css/_main.css">
		<link rel="stylesheet" type="text/css" href="css/diary_page.css">
	</head>
	
	<body>
        <div class="wrapper"> <!--Wrapper Start-->		
            <div class="header">
				<?php
					include 'header.php';
				?>
            </div>
            <div class="content">
				<div class="left_column">
					<?php
						include 'left_column.php';
					?>
				</div>
				<div class="right_column">
					<div class="right_column_header">
						<?php
							if ($reviewer) {
								echo '<h2>Granska Dagboksformulär</h2>';
							} else {
								echo '<h2>Dagboksformulär</h2>';
							}
						?>
					</div>
					<div class="right_column_content">
						<form action="diary_edit.php" onsubmit="return validateForm()" method="post" name="edit_form" id="form">
							<input type="hidden" name="client" value="<?php echo $client; ?>"/>
							<input type="hidden" name="inbox" value="<?php echo $inbox; ?>"/>
							<div class="form_info">
								<div class="form_row">
									<div class="form_col">
										<h5>Företag: </h5>
										<div>
											<input type="hidden" name="company" value="<?php echo $company; ?>" id="form_company"/>
											<input type="text" name="companyname" value="<?php echo $companyname; ?>" class="form_textbox <?php if (!$reviewer) { echo 'form_borderless'; } ?>" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Arbetsdag: </h5> <?php if ($reviewer) { echo '<p>*</p>'; } ?>
										<div>
											<input type="hidden" name="org_workday" value="<?php echo $workday; ?>" id="form_org_workday"/>
											<input type="text" name="workday" value="<?php echo $workday; ?>" id="form_workday" class="form_textbox <?php if (!$reviewer) { echo 'form_borderless'; } ?>" onkeypress="return isNumberKey(event)" onchange="return validateWorkday()" <?php if (!$reviewer) { echo 'readonly'; } ?>/>
										</div>
									</div>
									
									<div class="form_col form_col2">
										<h5>Vecka: </h5>
										<div>
											<input type="text" name="week" value="<?php $week = new DateTime($date); echo intval($week->format('W')); ?>" class="form_textbox <?php if (!$reviewer) { echo 'form_borderless'; } ?>" id="form_week" readonly/>
										</div>
									</div>
									
									<div class="form_col form_col2">
										<h5>Veckodag: </h5>
										<div>
											<input type="text" name="weekday" value="<?php echo weekday(date("N")); ?>" class="form_textbox <?php if (!$reviewer) { echo 'form_borderless'; } ?>" id="form_weekday" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Datum: </h5> <?php if ($reviewer) { echo '<p>*</p>'; } ?>
										<div>
											<?php
												if (!$reviewer) {
													echo '<input type="text" name="date" value="'. date($date) .'"  class="form_textbox form_borderless" id="form_date" readonly/>';
												} else {
													echo '<input type="hidden" name="org_date" value="'. date($date) .'" id="form_todays_date"/>';
													echo '<input type="date" name="date" value="'. date($date) .'"  class="form_textbox" id="form_date"/>';
												}											
											?>
										</div>
									</div>
								</div>
									
								<div class="form_row">
									<div class="form_col">
										<h5>Projektnummer: </h5>
										<div>
											<input type="text" name="projectnr" value="<?php echo $projectnr; ?>" class="form_textbox <?php if (!$reviewer) { echo 'form_borderless'; } ?>" readonly/>
										</div>
									</div>
								
									<div class="form_col">
										<h5>Projektnamn: </h5>
										<div>
											<input type="text" name="projectname" value="<?php echo $projectname; ?>" class="form_textbox <?php if (!$reviewer) { echo 'form_borderless'; } ?>" readonly/>
										</div>
									</div>
									
									<div class="form_col form_col2">
										<?php
											if ($reviewer) {
												echo
												'<h5>Arbetsledare: </h5><p>*</p>
												<div>
													<select name="supervisor" class="form_textbox" id="form_supervisor">
													<option value=""></option>';
												$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
												if ($conn->connect_error) {
													die("Connection failed: " . $conn->connect_error);
												}
												mysqli_set_charset($conn,"utf8");
												$sql = "SELECT db_project_member.user AS user, db_user.firstname AS fname, db_user.surname AS sname FROM db_project_member LEFT JOIN db_user ON db_project_member.user = db_user.email WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND title='Arbetsledare'";
												$result = $conn->query($sql);
												if ($result->num_rows > 0) {
													while ($row = $result->fetch_assoc()) {
														if ($row['user'] == $supervisor) {
															echo '<option value="'. $row['user'] .'" selected>'. $row['fname'] .' '. $row['sname'] .'</option>';
														} else {
															echo '<option value="'. $row['user'] .'">'. $row['fname'] .' '. $row['sname'] .'</option>';
														}
													}
												}
												$conn->close();
												echo
													'</select>
												</div>';
											} else {
												echo
												'<h5>Arbetsledare: </h5>
												<div>
													<input type="text" value="'. $supervisorname .'" class="form_textbox" id="form_supervisor" readonly/>
												</div>';
											}
										?>
									</div>
									
									<div class="form_col">
										<h5>Arbetsplats: </h5> <?php if ($reviewer) { echo '<p>*</p>'; } ?>
										<div>
											<input type="hidden" name="org_jobsite" value="<?php echo $jobsite; ?>"/>
											<input type="text" name="jobsite" value="<?php echo $jobsite; ?>" class="form_textbox <?php if (!$reviewer) { echo 'form_borderless'; } ?>" <?php if (!$reviewer) { echo 'readonly'; } ?>/>
										</div>
									</div>						
								</div>
							</div>
							
							<div class="form_row">
								<h3>Arbeten: </h3>
								<?php
									$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
									if ($conn->connect_error) {
										die("Connection failed: " . $conn->connect_error);
									}
									mysqli_set_charset($conn,"utf8");
									$sql = "SELECT * FROM db_project_diary_job WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND company='". $company ."' AND workday='". $workday ."'";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										$workrows = $result->num_rows;
										echo '<input type="hidden" name="workrows" id="form_work_count" value="'. $workrows .'"/>';
										echo '<div id="form_work_rows">';
										
										while ($row = $result->fetch_assoc()) {
											$jobnr = $row['id'];
											$jobid = $jobnr - 1;
											
											echo '<div class="form_work_row">';
											if ($reviewer) {
												echo '<div class="form_work_title"><h5>Rubrik: </h5><p>*</p></div><div class="form_work_status"><h5>Status: </h5><p>*</p></div><br>';
												echo ''. $jobnr .'. <input type="text" name="crew_job'. $jobid .'" class="form_textbox form_job_textbox" id="form_crew_job'. $jobid .'" value="'. $row['job'] .'" placeholder=""/><select name="job_status'. $jobid .'" class="form_textbox form_statusbox" id="form_job_status'. $jobid .'"><option value=""></option>';
												
												for ($i = 0; $i < $statusarraysize; $i++) {
													if (($i + 1) == $row['status']) {
														echo '<option value="'. ($i + 1) .'" selected>'. $statustypes[$i] .'</option>';
													} else {
														echo '<option value="'. ($i + 1) .'">'. $statustypes[$i] .'</option>';
													}
												}
												echo '</select>';
												if ($workrows > 1) {
													echo '<input type="button" onclick="removeJobField('. $jobid .')" value="Ta bort arbete" class="form_job_remove_button" id="form_job_remove_button'. $jobid .'" style="display: inline;"/><br>';
												} else {
													echo '<input type="button" onclick="removeJobField('. $jobid .')" value="Ta bort arbete" class="form_job_remove_button" id="form_job_remove_button'. $jobid .'"/><br>';
												}
												echo '<div class="form_work_comment"><h5>Notering: </h5></div>';
												echo '<input type="text" name="crew_comments'. $jobid .'" class="form_textbox form_job_comments_textbox" id="form_crew_comments'. $jobid .'" value="'. $row['comments'] .'" placeholder=""/>';
												
												$sql = "SELECT * FROM db_project_diary_crew WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND company='". $company ."' AND workday='". $workday ."' AND jobid='". $jobnr ."'";
												$crewresult = $conn->query($sql);
												if ($crewresult->num_rows > 0) {
													$crewrows = $crewresult->num_rows;
													echo '<input type="hidden" name="crewrows'. $jobid .'" id="form_crew_count'. $jobid .'" value="'. $crewrows .'"/>';
													echo '<div class="form_crew_rows" id="form_crew_rows'. $jobid .'">';
													$totaltime = 0;
													
													while ($crewrow = $crewresult->fetch_assoc()) {
														$crewnr = $crewrow['crewid'];
														$crewid = $crewnr - 1;
														
														echo '<div id="form_crew_row'. $jobid .''. $crewid .'">';
														if ($crewnr == 1) {
															echo '<div><div class="form_crew_title"><h5>Arbetsstyrka: </h5><p>*</p></div><div class="form_crew_title"><h5>Namn: </h5><p>*</p></div><div id="form_work_time_title"><h5>Tid: </h5><p>*</p></div></div>';
														}
														echo ''. $crewnr .'. <select name="crew_type'. $jobid .''. $crewid .'" class="form_textbox" id="form_crew_type'. $jobid .''. $crewid .'"><option value="">-</option>';
														
														for ($i = 0; $i < $jobarraysize; $i++) {
															if ($jobtypes[$i] == $crewrow['jobtype']) {
																echo '<option value="'. $jobtypes[$i] .'" selected>'. $jobtypes[$i] .'</option>';
															} else {
																echo '<option value="'. $jobtypes[$i] .'">'. $jobtypes[$i] .'</option>';
															}
														}
														echo '</select>';
														echo '<input type="text" name="crew_name'. $jobid .''. $crewid .'" id="form_crew_name'. $jobid .''. $crewid .'" class="form_textbox" onchange="isLetterOrSpaceKey(this)" value="'. $crewrow['fullname'] .'" placeholder=" -"/><select name="crew_time'. $jobid .''. $crewid .'" class="form_textbox form_crew_time_textbox" id="form_crew_time'. $jobid .''. $crewid .'"><option value="">-</option>';
														for ($i = 0.5; $i < 24.5; $i += 0.5) {
															if ($i == $crewrow['time']) {
																echo '<option value="'. $i .'" selected>'. number_format($i, 1) .'</option>';
																$totaltime = $totaltime + $i;
															} else {
																echo '<option value="'. $i .'">'. number_format($i, 1) .'</option>';
															}
														}
														echo '</select>';
														
														$own = $crewrow['own'];
														if ($own == 1) {
															echo '<input type="radio" name="crew_radio'. $jobid .''. $crewid .'" value="1" checked>Egen<input type="radio" name="crew_radio'. $jobid .''. $crewid .'" value="0">UE';
														} else {
															echo '<input type="radio" name="crew_radio'. $jobid .''. $crewid .'" value="1">Egen<input type="radio" name="crew_radio'. $jobid .''. $crewid .'" value="0" checked>UE';
														}
														
														if ($crewrows > 1) {
															echo '<input type="button" onclick="removeCrewField('. $jobid .','. $crewid .')" value="Ta bort rad" class="form_crew_remove_button" id="form_crew_remove_button'. $jobid .''. $crewid .'" style="display: inline;"/>';
														} else {
															echo '<input type="button" onclick="removeCrewField('. $jobid .','. $crewid .')" value="Ta bort rad" class="form_crew_remove_button" id="form_crew_remove_button'. $jobid .''. $crewid .'"/>';
														}
														echo '</div>';
													}
													echo '</div>';
													echo '<div class="crew_total_time">Summa Timmar: <input type="text" name="total_time" id="form_crew_total_time'. $jobid .'" class="form_textbox form_borderless" value="'; if ($totaltime > 0) { echo number_format($totaltime, 1); } echo '" readonly/></div>';
													echo '<input type="button" onclick="addCrewField('. $jobid .')" value="Lägg till rad" class="form_crew_add_button" id="form_crew_add_button'. $jobid .'"/>';
												}
											} else { // if NOT reviewer
												echo '<div class="form_work_title"><h5>Rubrik: </h5></div><div class="form_work_status"><h5>Status: </h5></div><br>';
												
												if ($row['status'] <= $statusarraysize) {
													$status = $statustypes[$row['status'] - 1];
												} else {
													$status = '';
												}
												echo ''. $jobnr .'. <input type="text" class="form_textbox form_job_textbox form_borderless" id="form_crew_job'. $jobid .'" value="'. $row['job'] .'" readonly/><input type="text" class="form_textbox form_work_status_textbox form_borderless" id="form_job_status'. $jobid .'" value="'. $status .'" readonly/>';
												echo '<div class="form_work_comment"><h5>Notering: </h5></div>';
												echo '<input type="text" class="form_textbox form_job_comments_textbox form_borderless" id="form_crew_comments'. $jobid .'" value="'. $row['comments'] .'" readonly/>';
											
												$sql = "SELECT * FROM db_project_diary_crew WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND company='". $company ."' AND workday='". $workday ."' AND jobid='". $jobnr ."'";
												$crewresult = $conn->query($sql);
												if ($crewresult->num_rows > 0) {
													$crewrows = $crewresult->num_rows;
													echo '<input type="hidden" name="crewrows'. $jobid .'" id="form_crew_count'. $jobid .'" value="'. $crewrows .'"/>';
													echo '<div class="form_crew_rows" id="form_crew_rows'. $jobid .'">';
													$totaltime = 0;
													
													while ($crewrow = $crewresult->fetch_assoc()) {
														$crewnr = $crewrow['crewid'];
														$crewid = $crewnr - 1;
														$totaltime = $totaltime + $crewrow['time'];
														
														echo '<div id="form_crew_row'. $jobid .''. $crewid .'">';
														if ($crewnr == 1) {
															echo '<div><div class="form_crew_title"><h5>Arbetsstyrka: </h5></div><div class="form_crew_title"><h5>Namn: </h5></div><div id="form_work_time_title"><h5>Tid: </h5></div></div>';
														}
														echo ''. $crewnr .'. <input type="text" class="form_textbox form_borderless" id="form_crew_type'. $jobid .''. $crewid .'" value="'. $crewrow['jobtype'] .'" readonly>';
														echo '<input type="text" id="form_crew_name'. $jobid .''. $crewid .'" class="form_textbox form_borderless" value="'. $crewrow['fullname'] .'" readonly/><input type="text" class="form_textbox form_crew_time_textbox form_borderless" id="form_crew_time'. $jobid .''. $crewid .'" value="'. number_format($crewrow['time'], 1) .'" readonly>';
														
														$own = $crewrow['own'];
														if ($own == 1) {
															echo 'Egen';
														} else {
															echo 'UE';
														}
														echo '</div>';
													}
													echo '</div>';
													echo '<div class="crew_total_time">Summa Timmar: <input type="text" name="total_time" id="form_crew_total_time'. $jobid .'" class="form_textbox form_borderless" value="'; if ($totaltime > 0) { echo number_format($totaltime, 1); } echo '" readonly/></div>';
												}
											}
											echo '</div>';
										}
										echo '</div>';
									} else { // if ($result->num_rows == 0)
										if ($reviewer) {
											echo '<input type="hidden" name="workrows" id="form_work_count" value="1"/>';
											echo '<div id="form_work_rows">';
											echo 	'<div class="form_work_row">';
											echo 		'<div class="form_work_title"><h5>Rubrik: </h5><p>*</p></div><div class="form_work_status"><h5>Status: </h5><p>*</p></div><br>';
											echo 		'1. <input type="text" name="crew_job0" class="form_textbox form_job_textbox" id="form_crew_job0" placeholder=""/><select name="job_status0" class="form_textbox form_statusbox" id="form_job_status0"><option value=""></option>';
											
											for ($i = 0; $i < $statusarraysize; $i++) {
												echo '<option value="'. ($i + 1) .'">'. $statustypes[$i] .'</option>';
											}
											echo 		'</select>';
											echo 		'<input type="button" onclick="removeJobField(0)" value="Ta bort arbete" class="form_job_remove_button" id="form_job_remove_button0"/><br>';
											echo 		'<div class="form_work_comment"><h5>Notering: </h5></div>';
											echo 		'<input type="text" name="crew_comments0" class="form_textbox form_job_comments_textbox" id="form_crew_comments0" placeholder=""/>';
											
											echo 		'<input type="hidden" name="crewrows0" id="form_crew_count0" value="1"/>';
											echo 		'<div class="form_crew_rows" id="form_crew_rows0">';
											
											echo 			'<div id="form_crew_row00">';
											echo 				'<div><div class="form_crew_title"><h5>Arbetsstyrka: </h5><p>*</p></div><div class="form_crew_title"><h5>Namn: </h5><p>*</p></div><div id="form_work_time_title"><h5>Tid: </h5><p>*</p></div></div>';
											echo 				'1. <select name="crew_type00" class="form_textbox" id="form_crew_type00"><option value="">-</option>';
											
											for ($i = 0; $i < $jobarraysize; $i++) {
												echo 				'<option value="'. $jobtypes[$i] .'">'. $jobtypes[$i] .'</option>';
											}
											echo 				'</select>';
											echo 				'<input type="text" name="crew_name00" id="form_crew_name00" class="form_textbox" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/><select name="crew_time00" class="form_textbox form_crew_time_textbox" id="form_crew_time00"><option value="">-</option>';
											
											for ($i = 0.5; $i < 24.5; $i += 0.5) {
												echo 			'<option value="'. $i .'">'. number_format($i, 1) .'</option>';
											}
											echo 				'</select>';

											echo 				'<input type="radio" name="crew_radio00" value="1" checked>Egen<input type="radio" name="crew_radio00" value="0">UE';
											
											echo 				'<input type="button" onclick="removeCrewField(0,0)" value="Ta bort rad" class="form_crew_remove_button" id="form_crew_remove_button00"/>';
											echo 			'</div>';
											echo 		'</div>';
											echo 		'<div class="crew_total_time">Summa Timmar: <input type="text" name="total_time" id="form_crew_total_time0" class="form_textbox form_borderless" readonly/></div>';
											echo 		'<input type="button" onclick="addCrewField(0)" value="Lägg till rad" class="form_crew_add_button" id="form_crew_add_button0"/>';
											echo 	'</div>';
											echo '</div>';
										} else { // if (!$reviewer)
											echo '<input type="hidden" name="workrows" id="form_work_count" value="0"/>';
											echo '<div id="form_work_rows">';
											echo 'Inga arbeten inlagda';
											echo '</div>';
										}
									}
									$conn->close();
									if ($reviewer) {
										echo '<input type="button" onclick="addJobField()" value="Lägg till arbete" class="form_job_add_button" id="form_job_add_button"/>';
									}
								?>
							</div>

							<div class="form_row form_abnorms">
								<h3>Avvikelser: </h3>
								<?php
									$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
									if ($conn->connect_error) {
										die("Connection failed: " . $conn->connect_error);
									}
									mysqli_set_charset($conn,"utf8");
									
									$sql = "SELECT MAX(id) AS id FROM db_project_abnormality WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
									$max_id_result = $conn->query($sql);
									$max_id_row = $max_id_result->fetch_assoc();							
									$maxid = $max_id_row['id'];
									
									$sql = "SELECT * FROM db_project_abnormality WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND company='". $company ."' AND workday='". $workday ."'";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										$abnormsrows = $result->num_rows;
										echo '<input type="hidden" name="abnormsrows" id="form_abnorms_count" value="'. $abnormsrows .'"/>';
										echo '<input type="hidden" id="form_abnorms_maxid" value="'. $maxid .'"/>';
										echo '<div id="form_abnorms_rows">';
										$abnormsid = 0;
										
										while ($row = $result->fetch_assoc()) {
											$abnormsnr = $row['id'];
											
											echo '<div class="form_work_row">';
											if ($reviewer) {
												echo '<div class="form_abnorms_title"><h5>Rubrik: </h5></div><div class="form_abnorms_title"><h5>Plats: </h5></div><div id="form_abnorms_comments_title"><h5>Noteringar: </h5></div><div id="form_abnorms_status_title"><h5>Status: </h5></div><br>';
												echo ''. $abnormsnr .'. <input type="hidden" name="abnorms_nr'. $abnormsid .'" id="form_abnorms_numberbox'. $abnormsid .'" value="'. $abnormsnr .'"/><input type="text" name="abnorms_header'. $abnormsid .'" id="form_abnorms_header'. $abnormsid .'" class="form_textbox" value="'. $row['header'] .'" maxlength="255" placeholder=""/><input type="text" name="abnorms_jobsite'. $abnormsid .'" id="form_abnorms_jobsite'. $abnormsid .'" class="form_textbox" value="'. $row['jobsite'] .'" placeholder=""/><input type="text" name="abnorms_comments'. $abnormsid .'" class="form_abnorms_comments" maxlength="255" id="form_abnorms_comment'. $abnormsid .'" value="'. $row['comments'] .'" placeholder=""/><select name="abnorms_status'. $abnormsid .'" id="form_abnorms_status'. $abnormsid .'"><option value=""></option>';												
												if ($row['status'] == 1) {
													echo '<option value="1" selected>Påbörjad</option>';
													echo '<option value="3">Avslutad</option>';
												} else if ($row['status'] == 2) {
													echo '<option value="2" selected>Pågående</option>';
													echo '<option value="3">Avslutad</option>';
												} else {
													echo '<option value="2">Pågående</option>';
													echo '<option value="3" selected>Avslutad</option>';
												}
												echo '</select><div class="form_job_remove_button" id="form_abnorms_remove_button'. $abnormsid .'"></div><br>';

												if ($row['economic_consequence'] == 1) {
													echo '<input type="checkbox" name="abnorms_economic_checkbox'. $abnormsid .'" value="true" class="form_echeckbox" id="form_echeckbox'. $abnormsid .'" checked/>Ekonomisk konsekvens';
												} else {
													echo '<input type="checkbox" name="abnorms_economic_checkbox'. $abnormsid .'" value="true" class="form_echeckbox" id="form_echeckbox'. $abnormsid .'"/>Ekonomisk konsekvens';
												}
												if ($row['time_consequence'] == 1) {
													echo '<input type="checkbox" name="abnorms_time_checkbox'. $abnormsid .'" value="true" id="form_tcheckbox'. $abnormsid .'" checked/>Tidskonsekvens';
												} else {
													echo '<input type="checkbox" name="abnorms_time_checkbox'. $abnormsid .'" value="true" id="form_tcheckbox'. $abnormsid .'"/>Tidskonsekvens';
												}
												$sql = "SELECT * FROM db_project_abnormality_crew WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND id='". $abnormsnr ."' AND workday='". $workday ."'";
												$crewresult = $conn->query($sql);
												if ($crewresult->num_rows > 0) {
													$crewrows = $crewresult->num_rows;
													echo '<input type="hidden" name="abnormscrewrows'. $abnormsid .'" id="form_abnorms_crew_count'. $abnormsid .'" value="'. $crewrows .'"/>';
													echo '<div class="form_crew_rows" id="form_abnorms_crew_rows'. $abnormsid .'">';
													$totaltime = 0;
													
													while ($crewrow = $crewresult->fetch_assoc()) {
														$crewnr = $crewrow['crewid'];
														$crewid = $crewnr - 1;
														
														echo '<div id="form_abnorms_crew_row'. $abnormsid .''. $crewid .'">';
														if ($crewnr == 1) {
															echo '<div><div class="form_crew_rev_title"><h5>Arbetsstyrka: </h5></div><div class="form_crew_rev_title"><h5>Namn: </h5></div><div id="form_work_time_rev_title"><h5>Tid: </h5></div></div>';
														}
														echo ''. $crewnr .'. <select name="abnorms_crew_type'. $abnormsid .''. $crewid .'" class="form_textbox" id="form_abnorms_crew_type'. $abnormsid .''. $crewid .'"><option value="">-</option>';
														
														for ($i = 0; $i < $jobarraysize; $i++) {
															if ($jobtypes[$i] == $crewrow['jobtype']) {
																echo '<option value="'. $jobtypes[$i] .'" selected>'. $jobtypes[$i] .'</option>';
															} else {
																echo '<option value="'. $jobtypes[$i] .'">'. $jobtypes[$i] .'</option>';
															}
														}
														echo '</select>';
														echo '<input type="text" name="abnorms_crew_name'. $abnormsid .''. $crewid .'" id="form_abnorms_crew_name'. $abnormsid .''. $crewid .'" class="form_textbox" onchange="isLetterOrSpaceKey(this)" value="'. $crewrow['fullname'] .'" placeholder=" -"/><select name="abnorms_crew_time'. $abnormsid .''. $crewid .'" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time'. $abnormsid .''. $crewid .'"><option value="">-</option>';
														for ($i = 0.5; $i < 24.5; $i += 0.5) {
															if ($i == $crewrow['time']) {
																echo '<option value="'. $i .'" selected>'. number_format($i, 1) .'</option>';
																$totaltime = $totaltime + $i;
															} else {
																echo '<option value="'. $i .'">'. number_format($i, 1) .'</option>';
															}
														}
														echo '</select>';
														
														$own = $crewrow['own'];
														if ($own == 1) {
															echo '<input type="radio" name="abnorms_crew_radio'. $abnormsid .''. $crewid .'" value="1" checked>Egen<input type="radio" name="abnorms_crew_radio'. $abnormsid .''. $crewid .'" value="0">UE';
														} else {
															echo '<input type="radio" name="abnorms_crew_radio'. $abnormsid .''. $crewid .'" value="1">Egen<input type="radio" name="abnorms_crew_radio'. $abnormsid .''. $crewid .'" value="0" checked>UE';
														}
														
														if ($crewrows > 1) {
															echo '<input type="button" onclick="removeAbnormsCrewField('. $abnormsid .','. $crewid .')" value="Ta bort rad" class="form_crew_remove_button" id="form_abnorms_crew_remove_button'. $abnormsid .''. $crewid .'" style="display: inline;"/>';
														} else {
															echo '<input type="button" onclick="removeAbnorms_CrewField('. $abnormsid .','. $crewid .')" value="Ta bort rad" class="form_crew_remove_button" id="form_abnorms_crew_remove_button'. $abnormsid .''. $crewid .'"/>';
														}
														echo '</div>';
													}
													echo '</div>';
													echo '<div class="crew_total_time">Summa Timmar: <input type="text" id="form_abnorms_total_time'. $abnormsid .'" class="form_textbox form_abnorms form_borderless" value="'; if ($totaltime > 0) { echo number_format($totaltime, 1); } echo '" readonly/></div>';
													echo '<input type="button" onclick="addAbnormsCrewField('. $abnormsid .')" value="Lägg Till Rad" class="form_crew_add_button" id="form_abnorms_crew_add_button'. $abnormsid .'"/>';
												} else { // If no crew rows where found
													echo '<input type="hidden" name="abnormscrewrows'. $abnormsid .'" id="form_abnorms_crew_count'. $abnormsid .'" value="1"/>
														<div class="form_crew_rows" id="form_abnorms_crew_rows'. $abnormsid .'">
															<div id="form_abnorms_crew_row'. $abnormsid .'0">
																<div>
																	<div class="form_crew_rev_title"><h5>Arbetsstyrka: </h5></div><div class="form_crew_rev_title"><h5>Namn: </h5></div><div id="form_work_time_rev_title"><h5>Tid: </h5></div>
																</div>
																1. <select name="abnorms_crew_type'. $abnormsid .'0" class="form_textbox" id="form_abnorms_crew_type'. $abnormsid .'0">
																	<option value="">-</option>';
													for ($i = 0; $i < $jobarraysize; $i++) {
														echo '<option value="'. $jobtypes[$i] .'">'. $jobtypes[$i] .'</option>';
													}
													echo 		'</select><input type="text" name="abnorms_crew_name'. $abnormsid .'0" id="form_abnorms_crew_name'. $abnormsid .'0" class="form_textbox" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/><select name="abnorms_crew_time'. $abnormsid .'0" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time'. $abnormsid .'0"><option value="">-</option>';
													for ($i = 0.5; $i < 24.5; $i += 0.5) {
														echo '<option value="'. $i .'">'. number_format($i, 1) .'</option>';
													}
													echo 		'</select><input type="radio" name="abnorms_crew_radio'. $abnormsid .'0" value="1" checked>Egen<input type="radio" name="abnorms_crew_radio'. $abnormsid .'0" value="0">UE<input type="button" onclick="removeAbnormsCrewField('. $abnormsid .',0)" value="Ta bort rad" class="form_crew_remove_button" id="form_abnorms_crew_remove_button'. $abnormsid .'0"/>
															</div>
														</div>
														<div class="crew_total_time">Summa Timmar: <input type="text" id="form_abnorms_total_time'. $abnormsid .'" class="form_textbox form_abnorms form_borderless" readonly/></div>
														<input type="button" onclick="addAbnormsCrewField('. $abnormsid .')" value="Lägg till rad" class="form_crew_add_button" id="form_abnorms_crew_add_button'. $abnormsid .'"/>';
												}
											} else { // If NOT reviewer
												echo '<div class="form_abnorms_title"><h5>Rubrik: </h5></div><div class="form_abnorms_title"><h5>Plats: </h5></div><div id="form_abnorms_comments_title"><h5>Noteringar: </h5></div><div id="form_abnorms_status_title"><h5>Status: </h5></div><br>';
												
												if ($row['status'] <= $statusarraysize) {
													$status = $statustypes[$row['status'] - 1];
												} else {
													$status = '';
												}
												echo ''. $abnormsnr .'. <input type="text" id="form_abnorms_header'. $abnormsid .'" class="form_textbox" value="'. $row['header'] .'" readonly/><input type="text" id="form_abnorms_jobsite'. $abnormsid .'" class="form_textbox" value="'. $row['jobsite'] .'" readonly/><input type="text" class="form_abnorms_comments" id="form_abnorms_comment'. $abnormsid .'" value="'. $row['comments'] .'" readonly/><input type="text" class="form_textbox form_work_status_textbox" id="form_abnorms_status'. $abnormsid .'" value="'. $status .'" readonly/><br>';
												if ($row['economic_consequence'] == 1) {
													echo '<input type="checkbox" value="true" class="form_echeckbox" id="form_echeckbox'. $abnormsid .'" onclick="return false;" checked/>Ekonomisk konsekvens';
												} else {
													echo '<input type="checkbox" value="true" class="form_echeckbox" id="form_echeckbox'. $abnormsid .'" onclick="return false;"/>Ekonomisk konsekvens';
												}
												if ($row['time_consequence'] == 1) {
													echo '<input type="checkbox" id="form_tcheckbox'. $abnormsid .'" onclick="return false;" checked/>Tidskonsekvens';
												} else {
													echo '<input type="checkbox" id="form_tcheckbox'. $abnormsid .'" onclick="return false;"/>Tidskonsekvens';
												}
												$sql = "SELECT * FROM db_project_abnormality_crew WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND id='". $abnormsnr ."' AND workday='". $workday ."'";
												$crewresult = $conn->query($sql);
												if ($crewresult->num_rows > 0) {
													$crewrows = $crewresult->num_rows;
													echo '<input type="hidden" name="abnormscrewrows'. $abnormsid .'" id="form_abnorms_crew_count'. $abnormsid .'" value="'. $crewrows .'"/>';
													echo '<div class="form_crew_rows" id="form_abnorms_crew_rows'. $abnormsid .'">';
													$totaltime = 0;
													
													while ($crewrow = $crewresult->fetch_assoc()) {
														$crewnr = $crewrow['crewid'];
														$crewid = $crewnr - 1;
														$totaltime = $totaltime + $crewrow['time'];
														
														echo '<div id="form_abnorms_crew_row'. $abnormsid .''. $crewid .'">';
														if ($crewnr == 1) {
															echo '<div><div class="form_crew_show_title"><h5>Arbetsstyrka: </h5></div><div class="form_crew_show_title"><h5>Namn: </h5></div><div id="form_work_time_title"><h5>Tid: </h5></div></div>';
														}
														echo ''. $crewnr .'. <input type="text" class="form_textbox" id="form_abnorms_crew_type'. $abnormsid .''. $crewid .'" value="'. $crewrow['jobtype'] .'" readonly>';
														echo '<input type="text" id="form_abnorms_crew_name'. $abnormsid .''. $crewid .'" class="form_textbox" value="'. $crewrow['fullname'] .'" readonly/><input type="text" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time'. $abnormsid .''. $crewid .'" value="'. number_format($crewrow['time'], 1) .'" readonly>';
														
														$own = $crewrow['own'];
														if ($own == 1) {
															echo 'Egen';
														} else {
															echo 'UE';
														}
														echo '</div>';
													}
													echo '</div>';
													echo '<div class="crew_total_time">Summa Timmar: <input type="text" id="form_abnorms_total_time'. $abnormsid .'" class="form_textbox form_abnorms form_borderless" value="'; if ($totaltime > 0) { echo number_format($totaltime, 1); } echo '" readonly/></div>';
												}
											}
											echo '</div>';
											$abnormsid++;
										}
										echo '</div>';
									} else { // If no abnorms rows where found
										if ($reviewer) {
											echo '<input type="hidden" name="abnormsrows" id="form_abnorms_count" value="1"/>';
											echo '<input type="hidden" id="form_abnorms_maxid" value="'. $maxid .'"/>';
											echo '<div id="form_abnorms_rows">';
											echo	'<div class="form_work_row">
														<div class="form_abnorms_title"><h5>Rubrik: </h5></div><div class="form_abnorms_title"><h5>Plats: </h5></div><div id="form_abnorms_comments_title"><h5>Noteringar: </h5></div><div id="form_abnorms_status_title"><h5>Status: </h5></div><br>
														'. $maxid .'. <input type="hidden" name="abnorms_nr0" id="form_abnorms_numberbox0" value="'. $maxid .'"/><input type="text" name="abnorms_header0" id="form_abnorms_header0" class="form_textbox" maxlength="255" placeholder=""/><input type="text" name="abnorms_jobsite0" id="form_abnorms_jobsite0" class="form_textbox" placeholder=""/><input type="text" name="abnorms_comments0" class="form_abnorms_comments" maxlength="255" id="form_abnorms_comment0" placeholder=""/><select name="abnorms_status0" class="form_textbox form_statusbox" id="form_abnorms_status0">
															<option value=""></option>
															<option value="1">Påbörjad</option>
															<option value="3">Avslutad</option>
														</select><input type="button" onclick="removeAbnormsField(0)" value="Ta bort avvikelse" class="form_job_remove_button" id="form_abnorms_remove_button0"/><br><input type="checkbox" name="abnorms_economic_checkbox0" value="true" class="form_echeckbox" id="form_echeckbox0"/>Ekonomisk konsekvens<input type="checkbox" name="abnorms_time_checkbox0" value="true" id="form_tcheckbox0"/>Tidskonsekvens
														<input type="hidden" name="abnormscrewrows0" id="form_abnorms_crew_count0" value="1"/>
														<div class="form_crew_rows" id="form_abnorms_crew_rows0">
															<div id="form_abnorms_crew_row00">
																<div>
																	<div class="form_crew_rev_title"><h5>Arbetsstyrka: </h5></div><div class="form_crew_rev_title"><h5>Namn: </h5></div><div id="form_work_time_rev_title"><h5>Tid: </h5></div>
																</div>
																1. <select name="abnorms_crew_type00" class="form_textbox" id="form_abnorms_crew_type00">
																	<option value="">-</option>';
													for ($i = 0; $i < $jobarraysize; $i++) {
														echo '<option value="'. $jobtypes[$i] .'">'. $jobtypes[$i] .'</option>';
													}
													echo 		'</select><input type="text" name="abnorms_crew_name00" id="form_abnorms_crew_name00" class="form_textbox" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/><select name="abnorms_crew_time00" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time00"><option value="">-</option>';
													for ($i = 0.5; $i < 24.5; $i += 0.5) {
														echo '<option value="'. $i .'">'. number_format($i, 1) .'</option>';
													}
													echo 		'</select><input type="radio" name="abnorms_crew_radio00" value="1" checked>Egen<input type="radio" name="abnorms_crew_radio00" value="0">UE<input type="button" onclick="removeAbnormsCrewField(0,0)" value="Ta bort rad" class="form_crew_remove_button" id="form_abnorms_crew_remove_button00"/>
															</div>
														</div>
														<div class="crew_total_time">Summa Timmar: <input type="text" id="form_abnorms_total_time0" class="form_textbox form_abnorms form_borderless" readonly/></div>
														<input type="button" onclick="addAbnormsCrewField(0)" value="Lägg till rad" class="form_crew_add_button" id="form_abnorms_crew_add_button0"/>
													</div>';
											echo '</div>';
										} else { // If NOT reviewer
											echo '<input type="hidden" name="abnormsrows" id="form_abnorms_count" value="0"/>';
											echo '<div id="form_abnorms_rows">';
											echo 'Inga avvikelser inlagda';
											echo '</div>';
										}
									}
									$conn->close();
									if ($reviewer) {
										echo '<input type="button" onclick="addAbnormsField()" value="Lägg till avvikelse" class="form_job_add_button" id="form_abnorms_add_button"/>';
									}
								?>
							</div>

							<div class="form_row">
								<h3>Övrigt: </h3>
								<?php
									$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
									if ($conn->connect_error) {
										die("Connection failed: " . $conn->connect_error);
									}
									mysqli_set_charset($conn,"utf8");
									
									$categorytypes = array();
									$categoryarraysize = 0;
									$sql = "SELECT * FROM db_project_diary_categorytype";
									$categoryresult = $conn->query($sql);
									if ($categoryresult->num_rows > 0) {
										while ($categoryrow = $categoryresult->fetch_assoc()) {
											$categorytypes[] = mb_substr($categoryrow['type'], 0, null);
											$categoryarraysize++;
										}
									}
									$sql = "SELECT * FROM db_project_diary_misc WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND company='". $company ."' AND workday='". $workday ."'";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										$miscrows = $result->num_rows;
										echo '<input type="hidden" name="miscrows" id="form_misc_count" value="'. $miscrows .'"/>';
										echo '<div id="form_misc_rows">';
										
										while ($row = $result->fetch_assoc()) {
											$miscnr = $row['id'];
											$miscid = $miscnr - 1;
											
											echo '<div>';
											if ($miscnr == 1) {
												echo '<div><div id="form_misc_category_title"><h5>Kategori: </h5></div><div id="form_misc_comments_title"><h5>Notering: </h5></div></div>';
											}
											if ($reviewer) {
												echo ''. $miscnr .'. <select name="misc_category'. $miscid .'" class="form_textbox" id="form_misc_category'. $miscid .'"><option value=""></option>';

												for ($i = 0; $i < $categoryarraysize; $i++) {
													if ($categorytypes[$i] === $row['category']) {
														echo '<option value="'. $categorytypes[$i] .'" selected>'. $categorytypes[$i] .'</option>';
													} else {
														echo '<option value="'. $categorytypes[$i] .'">'. $categorytypes[$i] .'</option>';
													}
												}
												echo '</select>';
												echo '<input type="text" name="misc_comments'. $miscid .'" id="form_misc_comments'. $miscid .'" class="form_textbox form_misc_comments_textbox" value="'. $row['comments'] .'" maxlength="255" placeholder=""/>';
												
												if ($miscrows > 1) {
													echo '<input type="button" onclick="removeMiscField('. $miscid .')" value="Ta bort rad" class="form_job_remove_button" id="form_misc_remove_button'. $miscid .'" style="display: inline;"/>';
												} else {
													echo '<input type="button" onclick="removeMiscField('. $miscid .')" value="Ta bort rad" class="form_job_remove_button" id="form_misc_remove_button'. $miscid .'"/>';
												}
											} else { // If NOT reviewer
												echo ''. $miscnr .'. <input type="text" class="form_textbox form_borderless" id="form_misc_category'. $miscid .'" value="'. $row['category'] .'" readonly>';
												echo '<input type="text" id="form_misc_comments'. $miscid .'" class="form_textbox form_misc_comments_textbox form_borderless" value="'. $row['comments'] .'" readonly/>';
											}
											echo '</div>';
										}
										echo '</div>';
										if ($reviewer) {
											echo '<input type="button" onclick="addMiscField()" value="Lägg till rad" class="form_job_add_button" id="form_abnorms_add_button"/>';
										}
									} else { // If no misc rows where found
										if ($reviewer) {
											echo '<input type="hidden" name="miscrows" id="form_misc_count" value="1"/>';
											echo '<div id="form_misc_rows">';
											echo 	'<div>';
											echo 		'<div><div id="form_misc_category_title"><h5>Kategori: </h5></div><div id="form_misc_comments_title"><h5>Notering: </h5></div></div>';
											echo 		'1. <select name="misc_category0" class="form_textbox" id="form_misc_category0"><option value=""></option>';

											for ($i = 0; $i < $categoryarraysize; $i++) {
												echo '<option value="'. $categorytypes[$i] .'">'. $categorytypes[$i] .'</option>';
											}
											echo 		'</select>';
											echo 		'<input type="text" name="misc_comments0" id="form_misc_comments0" class="form_textbox form_misc_comments_textbox" value="" maxlength="255" placeholder=""/><input type="button" onclick="removeMiscField(0)" value="Ta bort rad" class="form_job_remove_button" id="form_misc_remove_button0"/>';
											echo '	</div>';
											echo '</div>';
											echo '<input type="button" onclick="addMiscField()" value="Lägg till rad" class="form_job_add_button" id="form_abnorms_add_button"/>';
										} else { // If NOT reviewer
											echo '<input type="hidden" name="miscrows" id="form_misc_count" value="0"/>';
											echo '<div id="form_misc_rows">';
											echo 'Inga övriga fält inlagda';
											echo '</div>';
										}
									}
									$conn->close();
								?>
							</div>
							
							<div id="form_comments">
								<h5>Beställarens Kommentarer: </h5>
								<div>
									<?php
										if ($_SESSION['user']['permission'] < 4 && $reviewer) {
											echo '<textarea type="text" name="clientcomments" maxlength="255" class="form_textbox" id="form_comments_box">'. $clientcomments .'</textarea>';
										} else {
											echo '<textarea type="text" name="clientcomments" maxlength="255" class="form_textbox form_borderless" id="form_comments_box" readonly>'. $clientcomments .'</textarea>';
										}
									?>
								</div>
							</div>
							
							<?php
								if ($reviewer) {
									echo '<div id="form_send_button_div">
											<input type="hidden" name="reviewer" value="'. $_SESSION['user']['email'] .'"/>
											<input type="hidden" name="send" value="no" id="form_send_button_value"/>';
									if ($_SESSION['user']['permission'] < 4) {
										echo '<input type="submit" value="Spara och lås" id="form_send_button" onclick="updateSend(2)"/>';
									} else if ($_SESSION['user']['permission'] == 5) {
										echo '<input type="submit" value="Spara" id="form_send_button" onclick="updateSend(0)"/>';
										echo '<input type="submit" value="Spara och skicka" id="form_send_button" onclick="updateSend(1)"/>';
									} else {
										echo '<input type="submit" value="Spara" id="form_send_button"/>';
									}
									echo '</div>';
								}
							?>
						</form>
					</div>
				</div>
            </div>

            <div class="footer"></div>
			
        </div> <!-- Wrapper End-->
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/basic_functions.js"></script>
		<script type="text/javascript" src="js/diary_page.js"></script>
	</body>
</html>