<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	}
	if (!isset($_SESSION['project']) || (isset($_SESSION['user']['permission']) && $_SESSION['user']['permission'] > 3)) {
		header("Location: index.php");
	}
	$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	mysqli_set_charset($conn,"utf8");
	$sql = "SELECT email, firstname, surname, phonenumber, company FROM db_user WHERE email='". $_SESSION['user']['email'] ."'";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$user = $row['email'];
		$username = "". $row['firstname'] ." ". $row['surname'] ."";
		$phonenumber = $row['phonenumber'];
		$company = $row['company'];
		
		include 'get_company_info.php';
		$companyname = getCompanyInfo($company, "name");
		if ($companyname === false) {
			$companyname = $company;
		}
		$client = $_SESSION['project']['client'];
		$projectnr = $_SESSION['project']['number'];
		$projectname = $_SESSION['project']['name'];
		
		$types = array();
		$sql = "SELECT * FROM db_project_meeting_type";
		$type_result = $conn->query($sql);
		if ($type_result->num_rows > 0) {
			while ($type_row = $type_result->fetch_assoc()) {
				$types[] = $type_row['type'];
			}
		}
		if (!empty($_GET['series'])) {
			$seriesid = $_GET['series'];
			
			$sql = "SELECT * FROM db_project_meeting_series WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND id='". $seriesid ."'";
			$series_result = $conn->query($sql);
			if ($series_result->num_rows == 1) {
				$series_row = $series_result->fetch_assoc();
				
				$type = $types[$series_row['type'] - 1];
				$mainheader = $series_row['header'];
				
				$sql = "SELECT id FROM db_project_meeting_protocol WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."'";
				$id_result = $conn->query($sql);
				$meetingid = $id_result->num_rows + 1;
			} else {
				header("Location: project_page.php?client=". $client ."&number=". $projectnr ."&name=". $projectname ."");
			}
		} else {
			$sql = "SELECT id FROM db_project_meeting_series WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."'";
			$id_result = $conn->query($sql);
			$seriesid = $id_result->num_rows + 1;
			$meetingid = 1;
		}
	} else {
		$conn->close();
		header("Location: index.php");
	}
	$conn->close();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Mötesprotokoll </title>

		<link rel="stylesheet" type="text/css" href="css/_main.css">
		<link rel="stylesheet" type="text/css" href="css/meeting_page.css">
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
						<h2>Mötesprotokoll</h2>
					</div>
					<div class="right_column_content">
						<form action="meeting_add.php" onsubmit="return validateForm()" method="post" name="add_form" id="form">
							<input type="hidden" name="client" value="<?php echo $client; ?>"/>
							<input type="hidden" name="projectnr" value="<?php echo $projectnr; ?>"/>
							<input type="hidden" name="projectname" value="<?php echo $projectname; ?>"/>
							<input type="hidden" name="seriesid" value="<?php echo $seriesid; ?>" id="form_series_id"/>
							<input type="hidden" name="meetingid" value="<?php echo $meetingid; ?>" id="form_meeting_id"/>
							<div class="form_row">
								<div class="form_col" id="form_col1">
									<div class="form_row">
										<h5>Datum: </h5>
										<div>
											<input type="date" name="date" value="<?php echo date('Y-m-d');?>" class="form_textbox" id="form_date" onchange="return validateDate()"/>
										</div>
									</div>
									
									<div class="form_row">
										<h5>Handläggare: </h5>
										<div>
											<input type="text" value="<?php echo $username; ?>" class="form_textbox form_borderless" readonly/><br>
											<input type="text" value="<?php echo $phonenumber; ?>" class="form_textbox form_borderless" readonly/><br>
											<input type="text" name="author" value="<?php echo $user; ?>" class="form_textbox form_borderless" readonly/>
										</div>
									</div>
									
									<div id="form_info">
										<?php
											if ($meetingid == 1) {
												echo '<div><h5>Mötestyp: </h5><p>*</p></div>';
												echo '<select name="type" class="form_typebox" id="form_type"><option value=""></option>';
												
												for ($i = 0; $i < sizeof($types); $i++) {
													echo '<option value="'. ($i + 1) .'">'. $types[$i] .'</option>';
												}
												echo '</select> Nr. '. $meetingid .'<br>';
												echo '<div><h5>Rubrik: </h5><p>*</p></div>';
												echo '<textarea type="text" name="mainheader" maxlength="255" class="form_textbox" id="form_mainheader"></textarea><br>';
											} else {
												echo '<input type="hidden" name="type" value="'. $type.'" id="form_type"/>';
												echo '<div><h5>Rubrik: </h5></div>';
												echo '<textarea type="text" name="mainheader" maxlength="255" class="form_textbox" id="form_mainheader" readonly>'. $type .' nr '. $meetingid .' '. $mainheader .'</textarea><br>';
											}
										?>
										<div><h5>Tid: </h5><p>*</p></div>
										<input type="time" name="time" class="form_timebox" id="form_time"/>- <input type="time" name="time2" class="form_timebox" id="form_time2"/><br>
										<div><h5>Plats: </h5><p>*</p></div>
										<input type="text" name="jobsite" maxlength="120" class="form_textbox" id="form_jobsite"/>
									</div>
								</div>
								
								<div class="form_col">
									<div class="form_row">
										<h3>Närvarande </h3>
										<div>
											<div id="form_name_title"><h5>Namn: </h5><p>*</p></div><div id="form_company_title"><h5>Företag: </h5><p>*</p></div><div class="form_inline"><h5>Email: </h5><p>*</p></div>
										</div>
										<input type="hidden" name="presentcount" value="1" id="form_present_count"/>
										<div id="form_present">
											<div>
												1. <input type="text" name="present0" class="form_textbox" id="form_present0" value="<?php echo $username; ?>" readonly/><input type="text" name="company0" class="form_textbox" id="form_company0" value="<?php echo $companyname; ?>" readonly/><input type="text" name="email0" class="form_textbox" id="form_email0" value="<?php echo $user; ?>" readonly/>
											</div>
										</div>
										<input type="button" onclick="addPresentField()" value="Lägg till rad" class="form_add_button" id="form_present_add_button"/>
									</div>
									<div>
										<h3>Ej närvarande </h3>
										<div>
											<div id="form_name_title"><h5>Namn: </h5><p>*</p></div><div id="form_company_title"><h5>Företag: </h5><p>*</p></div><div class="form_inline"><h5>Email: </h5><p>*</p></div>
										</div>
										<?php
											$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
											if ($conn->connect_error) {
												die("Connection failed: " . $conn->connect_error);
											}
											mysqli_set_charset($conn,"utf8");
											$sql = "SELECT DISTINCT fullname, company, email FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". ($meetingid - 1) ."' AND email!='". $user ."'";
											$result = $conn->query($sql);
											
											$names = array();
											$emails = array();
											$arrsize = 0;
											
											if ($result->num_rows > 0) {
												$rows = $result->num_rows;
												echo '<input type="hidden" name="notpresentcount" value="'. $rows .'" id="form_not_present_count"/>';
												echo '<div id="form_not_present">';
												$count = 0;
												
												while ($row = $result->fetch_assoc()) {
													$names[] = $row['fullname'];
													$emails[] = $row['email'];
													$arrsize++;
													echo
														'<div>
															'. ($count + 1) .'. <input type="text" name="notpresent'. $count .'" class="form_textbox" id="form_not_present'. $count .'" value="'. $row['fullname'] .'" onchange="isLetterOrSpaceKey(this); updateSupervisors();"/><input type="text" name="notcompany'. $count .'" class="form_textbox" id="form_not_company'. $count .'" value="'. $row['company'] .'"/><input type="text" name="notemail'. $count .'" class="form_textbox" id="form_not_email'. $count .'" value="'. $row['email'] .'" onchange="updateSupervisors()"/><input type="button" onclick="moveToPresent('. $count .')" value="Närvarande" class="form_move_super_button" id="form_move_present_button'. $count .'"/>';
													if ($rows > 1) {
														echo '<input type="button" onclick="removeNotPresentField('. $count .')" value="Ta bort rad" class="form_remove_button" id="form_not_present_remove_button'. $count .'" style="display: inline;"/>';
													} else {
														echo '<input type="button" onclick="removeNotPresentField('. $count .')" value="Ta bort rad" class="form_remove_button" id="form_not_present_remove_button'. $count .'"/>';
													}
													echo '</div>';
													$count++;
												}
												echo '</div>';
											} else {
												echo
													'<input type="hidden" name="notpresentcount" value="1" id="form_not_present_count"/>
													<div id="form_not_present">
														<div>
															1. <input type="text" name="notpresent0" class="form_textbox" id="form_not_present0" onchange="isLetterOrSpaceKey(this); updateSupervisors();"/><input type="text" name="notcompany0" class="form_textbox" id="form_not_company0"/><input type="text" name="notemail0" class="form_textbox" id="form_not_email0" onchange="updateSupervisors()"/><input type="button" onclick="removeNotPresentField(0)" value="Ta bort rad" class="form_remove_button" id="form_not_present_remove_button0"/>
														</div>
													</div>';
											}
											$conn->close();
										?>
										<input type="button" onclick="addNotPresentField()" value="Lägg till rad" class="form_add_button" id="form_not_present_add_button"/>
									</div>
								</div>
							</div>
							
							<div class="form_row">
								<div class="content_row content_row_header" id="content_row_main_header">
									<div class="content_col content_small_col" id="content_nr_header"><u>Nr</u></div>
									<div class="content_col"><u>Rubrik</u></div>
									<div class="content_col content_supervisor_col"><u>Ansvarig</u></div>
									<div class="content_col content_tools_col"></div>
								</div>
								<?php
									if ($meetingid == 1) {
										$task_id = ''. $meetingid .'_0.1';
										echo
											'<input type="hidden" name="contentrows" id="form_content_count" value="1"/>
											<div id="form_content_rows">
												<div class="task_row" id="form_content_row0">
													<div class="content_row content_row_header">
														<div class="content_col content_small_col">0</div>
														<div class="content_col"><input type="text" name="header0" class="form_textbox form_headerbox" id="form_header0" maxlength="80"/></div>
														<div class="content_col content_supervisor_col"></div>
														<div class="content_col content_tools_col"><input type="button" onclick="removeHeaderField(0)" value="Ta bort rubrik" class="form_task_remove_button" id="form_header_remove_button0"/></div>
													</div>
													<input type="hidden" name="taskrows0" id="form_task_count0" value="1"/>
													<input type="hidden" name="maxid0" id="form_maxid0" value="2"/>
													<div id="form_content_task_rows0">
														<div class="content_row">
															<div class="content_col content_small_col"><input type="hidden" name="task_id00" id="form_task_id00" value="'. $task_id .'"/>'. $task_id .'</div>
															<div class="content_col"><input type="text" name="task00" class="form_textbox form_headerbox" id="form_task00" maxlength="255"/></div>
															<input type="hidden" id="form_super_count00" value="1"/>
															<div class="content_col content_supervisor_col" id="form_supervisor_col00">
																<select name="supervisor000" class="form_superbox" id="form_supervisor000">
																	<option value=""></option>
																	<option value="Info">Info</option>
																	<option value="Klart">Klart</option>
																	<option value="'. $user .'">'. $username .'</option>
																</select>
															</div>
															<div class="content_col content_tools_col" id="form_tools_col00">
																<input type="button" onclick="removeTaskField(0,0)" value="Ta bort rad" class="form_task_remove_button" id="form_task_remove_button00"/><input type="button" onclick="addSuperField(0,0)" value="Lägg till ansvarig" class="form_supervisor_add_button" id="form_supervisor_add_button00"/>
															</div>
														</div>
													</div>
													<input type="button" onclick="addTaskField(0)" value="Ny rad" class="form_task_add_button" id="form_task_add_button0"/>
												</div>
											</div>';
									} else {
										$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
										if ($conn->connect_error) {
											die("Connection failed: " . $conn->connect_error);
										}
										mysqli_set_charset($conn,"utf8");
										$sql = "SELECT DISTINCT id, text FROM db_project_meeting_header WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."'";
										$result = $conn->query($sql);
										if ($result->num_rows > 0) {
											echo '<input type="hidden" name="contentrows" id="form_content_count" value="'. $result->num_rows .'"/>';
											echo '<div id="form_content_rows">';
											$count = 0;
											
											while ($row = $result->fetch_assoc()) {
												$headerid = $row['id'];
												echo '<div class="task_row" id="form_content_row'. $count .'">
														<div class="content_row content_row_header">
															<div class="content_col content_small_col">'. $count .'</div>
															<div class="content_col"><input type="text" name="header'. $count .'" class="form_textbox form_headerbox" id="form_header'. $count .'" maxlength="80" value="'. $row['text'] .'"/></div>
															<div class="content_col content_supervisor_col"></div>
															<div class="content_col content_tools_col"><div id="form_header_remove_button'. $count .'"></div></div>
														</div>';

												$sql = "SELECT id, text FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". ($meetingid - 1) ."' AND header='". $headerid ."' AND supervisor1<>'Klart'";
												$task_result = $conn->query($sql);
												if ($task_result->num_rows > 0) {
													echo '<input type="hidden" name="taskrows'. $count .'" id="form_task_count'. $count .'" value="'. $task_result->num_rows .'"/>';
													echo '<input type="hidden" name="maxid'. $count .'" id="form_maxid'. $count .'" value="1"/>';
													echo '<div id="form_content_task_rows'. $count .'">';
													$taskcount = 0;
													
													while ($task_row = $task_result->fetch_assoc()) {
														$sql = "SELECT supervisor1, supervisor2, supervisor3 FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". ($meetingid - 1) ."' AND header='". $headerid ."' AND id='". $task_row['id'] ."'";
														$supervisor_result = $conn->query($sql);
														$supervisor_row = $supervisor_result->fetch_assoc();
														$supercount = 0;
														for ($i = 0; $i < 3; $i++) {
															if ($supervisor_row['supervisor'. ($i + 1) .''] != "") {
																$supercount++;
															}
														}
														$sql = "SELECT MIN(meeting) as meeting FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND header='". $headerid ."' AND id='". $task_row['id'] ."' AND text='". $task_row['text'] ."'";
														$min_result = $conn->query($sql);
														$min_row = $min_result->fetch_assoc();
														
														$task_id = $task_row['id'];
														
														echo
															'<div class="content_row">
																<div class="content_col content_small_col"><input type="hidden" name="task_id'. $count .''. $taskcount .'" id="form_task_id'. $count .''. $taskcount .'" value="'. $task_id .'"/>'. $task_id .'</div>
																<div class="content_col"><input type="text" name="task'. $count .''. $taskcount .'" class="form_textbox form_headerbox" id="form_task'. $count .''. $taskcount .'" maxlength="255" value="'. $task_row['text'] .'"/></div>
																<input type="hidden" id="form_super_count'. $count .''. $taskcount .'" value="'. $supercount .'"/>
																<div class="content_col content_supervisor_col" id="form_supervisor_col'. $count .''. $taskcount .'">';
														if ($supercount > 0) {
															for ($i = 0; $i < $supercount; $i++) {
																echo
																		'<select name="supervisor'. $count .''. $taskcount .''. $i .'" class="form_superbox" id="form_supervisor'. $count .''. $taskcount .''. $i .'">
																			<option value=""></option>';
																if ($supervisor_row['supervisor'. ($i + 1) .''] == "Info") {
																	echo	'<option value="Info" selected>Info</option>';
																} else {
																	echo	'<option value="Info">Info</option>';
																}
																if ($supervisor_row['supervisor'. ($i + 1) .''] == "Klart") {
																	echo	'<option value="Klart" selected>Klart</option>';
																} else {
																	echo	'<option value="Klart">Klart</option>';
																}
																if ($supervisor_row['supervisor'. ($i + 1) .''] == $user) {
																	echo	'<option value="'. $user .'" selected>'. $username .'</option>';
																} else {
																	echo	'<option value="'. $user .'">'. $username .'</option>';
																}
																for ($j = 0; $j < $arrsize; $j++) {
																	if ($supervisor_row['supervisor'. ($i + 1) .''] == $emails[$j]) {
																		echo '<option value="'. $emails[$j] .'" selected>'. $names[$j] .'</option>';
																	} else {
																		echo '<option value="'. $emails[$j] .'">'. $names[$j] .'</option>';
																	}
																}
																echo 	'</select>';
															}
														} else { // If $supercount == 0)
															echo
																		'<select name="supervisor'. $count .''. $taskcount .'0" class="form_superbox" id="form_supervisor'. $count .''. $taskcount .'0">
																			<option value=""></option>
																			<option value="Info">Info</option>
																			<option value="Klart">Klart</option>
																			<option value="'. $user .'">'. $username .'</option>';
															for ($i = 0; $i < $arrsize; $i++) {
																echo 		'<option value="'. $emails[$i] .'">'. $names[$i] .'</option>';
															}
															echo 		'</select>';
														}
														echo   '</div>
																<div class="content_col content_tools_col" id="form_tools_col'. $count .''. $taskcount .'">
																	<div id="form_task_remove_button'. $count .''. $taskcount .'"></div><input type="button" onclick="addSuperField('. $count .','. $taskcount .')" value="Lägg till ansvarig" class="form_supervisor_add_button" id="form_supervisor_add_button'. $count .''. $taskcount .'"/>';
																	for ($i = 1; $i < $supercount; $i++) {
																		echo '<input type="button" onclick="removeSuperField('. $count .','. $taskcount .','. $i .')" value="Ta bort ansvarig" class="form_supervisor_add_button" id="form_supervisor_remove_button'. $count .''. $taskcount .''. $i .'"/>';
																	}
														echo	'</div>
															</div>';
														$taskcount++;
													}
													echo '</div>';
												} else { // If $task_result->num_rows == 0
													$task_id = ''. $meetingid .'_'. $headerid .'.1';
													echo
														'<input type="hidden" name="taskrows'. $count .'" id="form_task_count'. $count .'" value="1"/>
														<input type="hidden" name="maxid'. $count .'" id="form_maxid'. $count .'" value="2"/>
														<div id="form_content_task_rows'. $count .'">
															<div class="content_row">
																<div class="content_col content_small_col"><input type="hidden" name="task_id'. $count .'0" id="form_task_id'. $count .'0" value="'. $task_id .'"/>'. $task_id .'</div>
																<div class="content_col"><input type="text" name="task'. $count .'0" class="form_textbox form_headerbox" id="form_task'. $count .'0" maxlength="255"/></div>
																<input type="hidden" id="form_super_count'. $count .'0" value="1"/>
																<div class="content_col content_supervisor_col" id="form_supervisor_col'. $count .'0">
																	<select name="supervisor'. $count .'00" class="form_superbox" id="form_supervisor'. $count .'00">
																		<option value=""></option>
																		<option value="Info">Info</option>
																		<option value="Klart">Klart</option>
																		<option value="'. $user .'">'. $username .'</option>';
													for ($i = 0; $i < $arrsize; $i++) {
														echo 			'<option value="'. $emails[$i] .'">'. $names[$i] .'</option>';
													}
													echo 			'</select>
																</div>
																<div class="content_col content_tools_col" id="form_tools_col'. $count .'0">
																	<input type="button" onclick="removeTaskField('. $count .',0)" value="Ta bort rad" class="form_task_remove_button" id="form_task_remove_button'. $count .'0"/><input type="button" onclick="addSuperField('. $count .',0)" value="Lägg till ansvarig" class="form_supervisor_add_button" id="form_supervisor_add_button'. $count .'0"/>
																</div>
															</div>
														</div>';
												}
												echo '<input type="button" onclick="addTaskField('. $count .')" value="Ny rad" class="form_task_add_button" id="form_task_add_button'. $count .'"/>';
												echo '</div>';
												$count++;
											}
											echo '</div>';
										} else { // If $result->num_rows == 0
											$task_id = ''. $meetingid .'_0.1';
											echo
												'<input type="hidden" name="contentrows" id="form_content_count" value="1"/>
												<div id="form_content_rows">
													<div class="task_row" id="form_content_row0">
														<div class="content_row content_row_header">
															<div class="content_col content_small_col">0</div>
															<div class="content_col"><input type="text" name="header0" class="form_textbox form_headerbox" id="form_header0" maxlength="80"/></div>
															<div class="content_col content_supervisor_col"></div>
															<div class="content_col content_tools_col"><input type="button" onclick="removeHeaderField(0)" value="Ta bort rubrik" class="form_task_remove_button" id="form_header_remove_button0"/></div>
														</div>
														<input type="hidden" name="taskrows0" id="form_task_count0" value="1"/>
														<input type="hidden" name="maxid0" id="form_maxid0" value="2"/>
														<div id="form_content_task_rows0">
															<div class="content_row">
																<div class="content_col content_small_col"><input type="hidden" name="task_id00" id="form_task_id00" value="'. $task_id .'"/>'. $task_id .'</div>
																<div class="content_col"><input type="text" name="task00" class="form_textbox form_headerbox" id="form_task00" maxlength="255"/></div>
																<input type="hidden" id="form_super_count00" value="1"/>
																<div class="content_col content_supervisor_col" id="form_supervisor_col00">
																	<select name="supervisor000" class="form_superbox" id="form_supervisor000">
																		<option value=""></option>
																		<option value="Info">Info</option>
																		<option value="Klart">Klart</option>
																		<option value="'. $user .'">'. $username .'</option>';
											for ($i = 0; $i < $arrsize; $i++) {
												echo 					'<option value="'. $emails[$i] .'">'. $names[$i] .'</option>';
											}
											echo					'</select>
																</div>
																<div class="content_col content_tools_col" id="form_tools_col00">
																	<input type="button" onclick="removeTaskField(0,0)" value="Ta bort rad" class="form_task_remove_button" id="form_task_remove_button00"/><input type="button" onclick="addSuperField(0,0)" value="Lägg till ansvarig" class="form_supervisor_add_button" id="form_supervisor_add_button00"/>
																</div>
															</div>
														</div>
														<input type="button" onclick="addTaskField(0)" value="Ny rad" class="form_task_add_button" id="form_task_add_button0"/>
													</div>
												</div>';
										}
										$conn->close();
									}
								?>
								<input type="button" onclick="addHeaderField()" value="Ny rubrik" class="form_add_button" id="form_header_add_button"/>
							</div>
							
							<div id="form_send_button_div">
								<input type="hidden" name="send" value="no" id="form_send_button_value"/>
								<input type="submit" value="Spara" id="form_send_button" onclick="updateSend(0)"/>
								<input type="submit" value="Spara och skicka" id="form_send_button" onclick="updateSend(1)"/>
							</div>
						</form>
					</div>
				</div>
            </div>

            <div class="footer"></div>
			
        </div> <!-- Wrapper End-->
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/basic_functions.js"></script>
		<script type="text/javascript" src="js/meeting_page.js"></script>
	</body>
</html>