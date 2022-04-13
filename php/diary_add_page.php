<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	}
	if (!isset($_SESSION['project']) || (isset($_SESSION['user']['permission']) && $_SESSION['user']['permission'] == 4)) {
		header("Location: index.php");
	}
	$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	mysqli_set_charset($conn,"utf8");
	$sql = "SELECT email, firstname, surname, company FROM db_user WHERE email='". $_SESSION['user']['email'] ."'";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$user = $row['email'];
		$username = "". $row['firstname'] ." ". $row['surname'] ."";
		$company = $row['company'];
		
		include 'get_company_info.php';
		$companyname = getCompanyInfo($company, "name");
		if ($companyname === false) {
			$companyname = $company;
		}
	} else {
		$conn->close();
		header("Location: index.php");
	}
	$sql = "SELECT * FROM db_project WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		
		$client = $row['client'];
		$projectnr = $row['number'];
		$projectname = $row['name'];
		$jobsite = $row['jobsite'];
	} else {
		$conn->close();
		header("Location: index.php");
	}
	$sql = "SELECT MAX(workday) AS max FROM db_project_diary WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $company ."'";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$workday = $row['max'];
		$workday = $workday + 1;
	} else {
		$workday = 1;
	}
	$jobtypes = array();
	$arraysize = 0;
	$sql = "SELECT * FROM db_project_diary_jobtype";
	$jobtype_result = $conn->query($sql);
	if ($jobtype_result->num_rows > 0) {
		while ($jobtype_row = $jobtype_result->fetch_assoc()) {
			$jobtypes[] = mb_substr($jobtype_row['type'], 0, null);
			$arraysize++;
		}
	}
	$conn->close();
	
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
						<h2>Dagboksformulär</h2>
					</div>
					<div class="right_column_content">
						<form action="diary_add.php" onsubmit="return validateForm()" method="post" name="add_form" id="form">
							<input type="hidden" name="client" value="<?php echo $client; ?>"/>
							<input type="hidden" name="author" value="<?php echo $user; ?>"/>
							<div class="form_info">
								<div class="form_row">
									<div class="form_col">
										<h5>Företag: </h5>
										<div>
											<input type="hidden" name="company" value="<?php echo $company; ?>" id="form_company"/>
											<input type="text" name="companyname" value="<?php echo $companyname; ?>" class="form_textbox" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Arbetsdag: </h5><p>*</p>
										<div>
											<input type="text" name="workday" value="<?php echo $workday; ?>" class="form_textbox" onkeypress="return isNumberKey(event)" onchange="return validateWorkday()" id="form_workday"/>
											<input type="hidden" value="<?php echo $workday; ?>" id="form_org_workday"/>
										</div>
									</div>
									
									<div class="form_col form_col2">
										<h5>Vecka: </h5>
										<div>
											<input type="text" name="week" value="<?php $date = new DateTime(date('Y-m-d')); 
												echo intval($date->format('W')); ?>" class="form_textbox" id="form_week" readonly/>
										</div>
									</div>
									
									<div class="form_col form_col2">
										<h5>Veckodag: </h5>
										<div>
											<input type="text" name="weekday" value="<?php echo weekday(date("N"));?>" class="form_textbox" id="form_weekday" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Datum: </h5><p>*</p>
										<div>
											<input type="date" name="date" value="<?php echo date('Y-m-d');?>"  class="form_textbox" id="form_date"/>
											<input type="hidden" value="<?php echo date('Y-m-d');?>" id="form_todays_date"/>
										</div>
									</div>
								</div>
									
								<div class="form_row">
									<div class="form_col">
										<h5>Projektnummer: </h5>
										<div>
											<input type="text" name="projectnr" value="<?php echo $projectnr; ?>" class="form_textbox" readonly/>
										</div>
									</div>
								
									<div class="form_col">
										<h5>Projektnamn: </h5>
										<div>
											<input type="text" name="projectname" value="<?php echo $projectname; ?>" class="form_textbox" readonly/>
										</div>
									</div>
									
									<div class="form_col form_col2">
										<h5>Arbetsledare: </h5><p>*</p>
										<div>
											<select name="supervisor" class="form_textbox" id="form_supervisor">
											<option value=""></option>
											<?php
												$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
												if ($conn->connect_error) {
													die("Connection failed: " . $conn->connect_error);
												}
												mysqli_set_charset($conn,"utf8");
												$sql = "SELECT db_project_member.user AS user, db_user.firstname AS fname, db_user.surname AS sname FROM db_project_member LEFT JOIN db_user ON db_project_member.user = db_user.email WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND title='Arbetsledare'";
												$result = $conn->query($sql);
												if ($result->num_rows > 0) {
													while ($row = $result->fetch_assoc()) {
														echo '<option value="'. $row['user'] .'">'. $row['fname'] .' '. $row['sname'] .'</option>';
													}
												}
												$conn->close();
											?>
											</select>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Arbetsplats: </h5><p>*</p>
										<div>
											<input type="text" name="jobsite" value="<?php echo $jobsite; ?>" class="form_textbox"/>
										</div>
									</div>						
								</div>
							</div>
							
							<div class="form_row">
								<h3>Arbeten: </h3>
								<input type="hidden" name="workrows" id="form_work_count" value="1"/>
								<div id="form_work_rows">
									<div class="form_work_row">
										<div class="form_work_title"><h5>Rubrik: </h5><p>*</p></div><div class="form_work_status"><h5>Status: </h5><p>*</p></div><br>
										1. <input type="text" name="crew_job0" class="form_textbox form_job_textbox" id="form_crew_job0" placeholder=""/><select name="job_status0" class="form_textbox form_statusbox" id="form_job_status0">
											<option value=""></option>
											<?php
												$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
												if ($conn->connect_error) {
													die("Connection failed: " . $conn->connect_error);
												}
												mysqli_set_charset($conn,"utf8");
												$sql = "SELECT * FROM db_project_diary_statustype";
												$result = $conn->query($sql);
												if ($result->num_rows > 0) {
													while ($row = $result->fetch_assoc()) {
														$type = mb_substr($row['type'], 0, null);
														echo '<option value="'. $row['index'] .'">'. $type .'</option>';
													}
												}
												$conn->close();
											?>
										</select><input type="button" onclick="removeJobField(0)" value="Ta bort arbete" class="form_job_remove_button" id="form_job_remove_button0"/><br>
										<div class="form_work_comment"><h5>Notering: </h5></div>
										<input type="text" name="crew_comments0" class="form_textbox form_job_comments_textbox" id="form_crew_comments0" placeholder=""/>
										<input type="hidden" name="crewrows0" id="form_crew_count0" value="1"/>
										<div class="form_crew_rows" id="form_crew_rows0">
											<div id="form_crew_row00">
												<div>
													<div class="form_crew_title"><h5>Arbetsstyrka: </h5><p>*</p></div><div class="form_crew_title"><h5>Namn: </h5><p>*</p></div><div id="form_work_time_title"><h5>Tid: </h5><p>*</p></div>
												</div>
												1. <select name="crew_type00" class="form_textbox" id="form_crew_type00">
													<option value="">-</option>
													<?php
														for ($i = 0; $i < $arraysize; $i++) {
															echo '<option value="'. $jobtypes[$i] .'">'. $jobtypes[$i] .'</option>';
														}
													?>
												</select><input type="text" name="crew_name00" id="form_crew_name00" class="form_textbox" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/><select name="crew_time00" class="form_textbox form_crew_time_textbox" id="form_crew_time00" onchange="updateTotalWorkTime(0)"><option value="">-</option>
													<?php
														for ($i = 0.5; $i < 24.5; $i += 0.5) {
															echo '<option value="'. $i .'">'. number_format($i, 1) .'</option>';
														}
													?>
												</select><input type="radio" name="crew_radio00" value="1" checked>Egen<input type="radio" name="crew_radio00" value="0">UE<input type="button" onclick="removeCrewField(0,0)" value="Ta bort rad" class="form_crew_remove_button" id="form_crew_remove_button00"/>
											</div>
										</div>
										<div class="crew_total_time">Summa Timmar: <input type="text" name="total_time" id="form_crew_total_time0" class="form_textbox form_borderless" readonly/></div>
										<input type="button" onclick="addCrewField(0)" value="Lägg till rad" class="form_crew_add_button" id="form_crew_add_button0"/>
									</div>
								</div>
								<input type="button" onclick="addJobField()" value="Lägg till arbete" class="form_job_add_button" id="form_job_add_button"/>
							</div>
							
							<div class="form_row form_abnorms">
								<h3>Avvikelser: </h3>
								<input type="hidden" name="abnormsrows" id="form_abnorms_count" value="1"/>
									<?php
										$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
										if ($conn->connect_error) {
											die("Connection failed: " . $conn->connect_error);
										}
										mysqli_set_charset($conn,"utf8");
										$sql = "SELECT MAX(id) AS id FROM db_project_abnormality WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
										$max_id_result = $conn->query($sql);
										$max_id_row = $max_id_result->fetch_assoc();
										if (!empty($max_id_row['id'])) {							
											$maxid = $max_id_row['id'];
										} else {
											$maxid = 1;
										}
										echo '<input type="hidden" id="form_abnorms_maxid" value="'. $maxid .'"/>';
										echo '<div id="form_abnorms_rows">';
										
										$sql = "SELECT * FROM (SELECT a.* FROM db_project_abnormality a INNER JOIN (SELECT client, number, name, id, MAX(workday) workday FROM db_project_abnormality WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $company ."' GROUP BY client, number, name, id) b ON a.client = b.client AND a.number = b.number AND a.name = b.name AND a.id = b.id AND a.workday = b.workday) z WHERE status<3";
										$result = $conn->query($sql);
										if ($result->num_rows > 0) {
											$count = 0;

											while ($row = $result->fetch_assoc()) {
												$sql = "SELECT date FROM db_project_diary WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $row['company'] ."' AND workday='". $row['workday'] ."'";
												$dateresult = $conn->query($sql);
												if ($dateresult->num_rows == 1) {
													$daterow = $dateresult->fetch_assoc();
													$date = $daterow['date'];
												} else {
													$date = '';
												}
												echo '<div class="form_work_row">';
												echo 	'<input type="hidden" name="abnorms_date'. $count .'" id="form_abnorms_date'. $count .'" value="'. $date .'"/>';
												echo 	'<div class="form_abnorms_title"><h5>Rubrik: </h5></div><div class="form_abnorms_title"><h5>Plats: </h5></div><div id="form_abnorms_comments_title"><h5>Noteringar: </h5></div><div id="form_abnorms_status_title"><h5>Status: </h5></div><br>';
												echo ''. $row['id'] .'. <input type="hidden" name="abnorms_nr'. $count .'" id="form_abnorms_numberbox'. $count .'" value="'. $row['id'] .'"/><input type="text" name="abnorms_header'. $count .'" id="form_abnorms_header'. $count .'" class="form_textbox" value="'. $row['header'] .'" readonly/><input type="text" name="abnorms_jobsite'. $count .'" id="form_abnorms_jobsite'. $count .'" class="form_textbox" value="'. $row['jobsite'] .'" readonly/><input type="text" name="abnorms_comments'. $count .'" class="form_abnorms_comments" maxlength="255" id="form_abnorms_comment'. $count .'" placeholder=""/><select name="abnorms_status'. $count .'" class="form_textbox form_statusbox" id="form_abnorms_status'. $count .'">';
												
												if ($row['status'] == 1 || $row['status'] == 2) {
													echo '<option value="2" selected>Pågående</option>';
													echo '<option value="3">Avslutad</option>';
												} else {
													echo '<option value="3" selected>Avslutad</option>';
												}
												echo '</select><div class="form_job_remove_button" id="form_abnorms_remove_button'. $count .'"></div><br>';
												if ($row['economic_consequence'] == 1) {
													echo '<input type="checkbox" value="true" class="form_echeckbox" id="form_echeckbox'. $count .'" onclick="return false;" checked/>Ekonomisk konsekvens';
												} else {
													echo '<input type="checkbox" value="true" class="form_echeckbox" id="form_echeckbox'. $count .'" onclick="return false;"/>Ekonomisk konsekvens';
												}
												if ($row['time_consequence'] == 1) {
													echo '<input type="checkbox" id="form_tcheckbox'. $count .'" onclick="return false;" checked/>Tidskonsekvens';
												} else {
													echo '<input type="checkbox" id="form_tcheckbox'. $count .'" onclick="return false;"/>Tidskonsekvens';
												}
												echo '<input type="hidden" name="abnormscrewrows'. $count .'" id="form_abnorms_crew_count'. $count .'" value="1"/>';
												echo 	'<div class="form_crew_rows" id="form_abnorms_crew_rows'. $count .'">';
												echo 		'<div id="form_abnorms_crew_row'. $count .'0">';
												echo 			'<div>
																	<div class="form_crew_rev_title"><h5>Arbetsstyrka: </h5></div><div class="form_crew_rev_title"><h5>Namn: </h5></div><div id="form_work_time_rev_title"><h5>Tid: </h5></div>
																</div>';
												echo 			'1. <select name="abnorms_crew_type'. $count .'0" class="form_textbox" id="form_abnorms_crew_type'. $count .'0"><option value="">-</option>';
												
												for ($i = 0; $i < $arraysize; $i++) {
													echo '<option value="'. $jobtypes[$i] .'">'. $jobtypes[$i] .'</option>';
												}
												echo 			'</select><input type="text" name="abnorms_crew_name'. $count .'0" id="form_abnorms_crew_name'. $count .'0" class="form_textbox" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/><select name="abnorms_crew_time'. $count .'0" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time'. $count .'0" onchange="updateTotalAbnormsTime('. $count .')"><option value="">-</option>';												
												for ($i = 0.5; $i < 24.5; $i += 0.5) {
													echo '<option value="'. $i .'">'. number_format($i, 1) .'</option>';
												}
												echo 			'</select><input type="radio" name="abnorms_crew_radio'. $count .'0" value="1" checked>Egen<input type="radio" name="abnorms_crew_radio'. $count .'0" value="0">UE<input type="button" onclick="removeAbnormsCrewField('. $count .',0)" value="Ta bort rad" class="form_crew_remove_button" id="form_abnorms_crew_remove_button'. $count .'0"/>';
												echo 		'</div>';
												echo 	'</div>';
												echo '<div class="crew_total_time">Summa Timmar: <input type="text" id="form_abnorms_total_time'. $count .'" class="form_textbox form_abnorms form_borderless" readonly/></div>';
												echo '<input type="button" onclick="addAbnormsCrewField('. $count .')" value="Lägg till rad" class="form_crew_add_button" id="form_abnorms_crew_add_button'. $count .'"/>';
												echo '</div>';
												$count++;
											}
										} else { // If $result->num_rows == 0
											echo
											'<div class="form_work_row">
												<div class="form_abnorms_title"><h5>Rubrik: </h5></div><div class="form_abnorms_title"><h5>Plats: </h5></div><div id="form_abnorms_comments_title"><h5>Noteringar: </h5></div><div id="form_abnorms_status_title"><h5>Status: </h5></div><br>
												'. $maxid .'. <input type="hidden" name="abnorms_nr0" id="form_abnorms_numberbox0" value="'. $maxid .'"/><input type="text" name="abnorms_header0" id="form_abnorms_header0" class="form_textbox" maxlength="255" placeholder=""/><input type="text" name="abnorms_jobsite0" id="form_abnorms_jobsite0" class="form_textbox" placeholder=""/><input type="text" name="abnorms_comments0" class="form_abnorms_comments" maxlength="255" id="form_abnorms_comment0" placeholder=""/><select name="abnorms_status0" class="form_textbox form_statusbox" id="form_abnorms_status0">
													<option value=""></option>
													<option value="1">Påbörjad</option>
													<option value="3">Avslutad</option>
												</select><input type="button" onclick="removeAbnormsField(0)" value="Ta Bort Avvikelse" class="form_job_remove_button" id="form_abnorms_remove_button0"/><br><input type="checkbox" name="abnorms_economic_checkbox0" value="true" class="form_echeckbox" id="form_echeckbox0"/>Ekonomisk konsekvens<input type="checkbox" name="abnorms_time_checkbox0" value="true" id="form_tcheckbox0"/>Tidskonsekvens
												<input type="hidden" name="abnormscrewrows0" id="form_abnorms_crew_count0" value="1"/>
												<div class="form_crew_rows" id="form_abnorms_crew_rows0">
													<div id="form_abnorms_crew_row00">
														<div>
															<div class="form_crew_rev_title"><h5>Arbetsstyrka: </h5></div><div class="form_crew_rev_title"><h5>Namn: </h5></div><div id="form_work_time_rev_title"><h5>Tid: </h5></div>
														</div>
														1. <select name="abnorms_crew_type00" class="form_textbox" id="form_abnorms_crew_type00">
															<option value="">-</option>';
											for ($i = 0; $i < $arraysize; $i++) {
												echo '<option value="'. $jobtypes[$i] .'">'. $jobtypes[$i] .'</option>';
											}
											echo 		'</select><input type="text" name="abnorms_crew_name00" id="form_abnorms_crew_name00" class="form_textbox" onchange="isLetterOrSpaceKey(this)" placeholder=" -"/><select name="abnorms_crew_time00" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time00" onchange="updateTotalAbnormsTime(0)"><option value="">-</option>';
											for ($i = 0.5; $i < 24.5; $i += 0.5) {
												echo '<option value="'. $i .'">'. number_format($i, 1) .'</option>';
											}
											echo 		'</select><input type="radio" name="abnorms_crew_radio00" value="1" checked>Egen<input type="radio" name="abnorms_crew_radio00" value="0">UE<input type="button" onclick="removeAbnormsCrewField(0,0)" value="Ta bort rad" class="form_crew_remove_button" id="form_abnorms_crew_remove_button00"/>
													</div>
												</div>
												<div class="crew_total_time">Summa Timmar: <input type="text" id="form_abnorms_total_time0" class="form_textbox form_abnorms form_borderless" readonly/></div>
												<input type="button" onclick="addAbnormsCrewField(0)" value="Lägg till rad" class="form_crew_add_button" id="form_abnorms_crew_add_button0"/>
											</div>';
										}
										echo '</div>';
										$conn->close();
									?>
								<input type="button" onclick="addAbnormsField()" value="Lägg till avvikelse" class="form_job_add_button" id="form_abnorms_add_button"/>
							</div>
							
							<div class="form_row">
								<h3>Övrigt: </h3>
								<input type="hidden" name="miscrows" id="form_misc_count" value="1"/>
								<div id="form_misc_rows">
									<div>
										<div>
											<div id="form_misc_category_title"><h5>Kategori: </h5></div>
											<div id="form_misc_comments_title"><h5>Notering: </h5></div>
										</div>
										1. <select name="misc_category0" class="form_textbox" id="form_misc_category0">
											<option value=""></option>
											<?php
												$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
												if ($conn->connect_error) {
													die("Connection failed: " . $conn->connect_error);
												}
												mysqli_set_charset($conn,"utf8");
												$sql = "SELECT * FROM db_project_diary_categorytype";
												$result = $conn->query($sql);
												if ($result->num_rows > 0) {
													while ($row = $result->fetch_assoc()) {
														$type = mb_substr($row['type'], 0, null);
														echo '<option value="'. $type .'">'. $type .'</option>';
													}
												}
												$conn->close();
											?>
										</select><input type="text" name="misc_comments0" id="form_misc_comments0" class="form_textbox form_misc_comments_textbox" maxlength="255" placeholder=""/><input type="button" onclick="removeMiscField(0)" value="Ta bort rad" class="form_job_remove_button" id="form_misc_remove_button0"/>
									</div>
								</div>
								<input type="button" onclick="addMiscField()" value="Lägg till rad" class="form_job_add_button" id="form_abnorms_add_button"/>
							</div>
							
							<div id="form_send_button_div">
								<input type="hidden" name="send" value="no" id="form_send_button_value"/>
								<?php
									echo '<input type="hidden" name="reviewer" value="'. $_SESSION['user']['email'] .'"/>';
									if ($_SESSION['user']['permission'] == 5) {
									    echo '<input type="submit" value="Spara" id="form_send_button" onclick="updateSend(0)"/>';
										echo '<input type="submit" value="Spara och skicka" id="form_send_button" onclick="updateSend(1)"/>';
									} else if ($_SESSION['user']['permission'] > 5) {
										echo '<input type="submit" value="Spara" id="form_send_button"/>';
									}
								?>
							</div>
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