<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	}
	if (!isset($_SESSION['project']) || (isset($_SESSION['user']['permission']) && $_SESSION['user']['permission'] > 5)) {
		header("Location: index.php");
	}
	if (!empty($_GET['series']) && !empty($_GET['id'])) {
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		$client = $_SESSION['project']['client'];
		$projectnr = $_SESSION['project']['number'];
		$projectname = $_SESSION['project']['name'];
		$user = $_SESSION['user']['email'];
		
		$seriesid = $_GET['series'];
		$meetingid = $_GET['id'];
		
		$sql = "SELECT * FROM db_project_meeting_series WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND id='". $seriesid ."'";
		$result = $conn->query($sql);
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			
			$mainheader = $row['header'];
			$type = $row['type'];
			
			$sql = "SELECT * FROM db_project_meeting_type";
			$typeresult = $conn->query($sql);
			if ($typeresult->num_rows > 0) {
				while ($typerow = $typeresult->fetch_assoc()) {
					if ($type == $typerow['id']) {
						$type = $typerow['type'];
					}
				}
			}
			$sql = "SELECT id, email FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."' AND email='". $user ."'";
			$presentresult = $conn->query($sql);
			if ($presentresult->num_rows > 0) {
				$sql = "SELECT * FROM db_project_meeting_protocol WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND id='". $meetingid ."'";
				$meeting_result = $conn->query($sql);
				if ($result->num_rows == 1) {
					$meeting_row = $meeting_result->fetch_assoc();
					
					$date = $meeting_row['date'];
					$time = $meeting_row['time'];
					$time2 = $meeting_row['time2'];
					$jobsite = $meeting_row['jobsite'];
					$sent = $meeting_row['locked'];
					
					$sql = "SELECT email, firstname, surname, phonenumber FROM db_user WHERE email='". $meeting_row['author'] ."'";
					$author_result = $conn->query($sql);
					if ($author_result->num_rows == 1) {
						$author_row = $author_result->fetch_assoc();
						$author = $author_row['email'];
						$authorname = "". $author_row['firstname'] ." ". $author_row['surname'] ."";
						$phonenumber = $author_row['phonenumber'];
					} else {
						header("Location: project_page.php?client=". $client ."&number=". $projectnr ."&name=". $projectname ."");
					}
					if ($author == $_SESSION['user']['email'] && $sent != 2) {
						$edit = true;
					} else {
						$edit = false;
					}
				} else {
					header("Location: project_page.php?client=". $client ."&number=". $projectnr ."&name=". $projectname ."");
				}
			} else {
				header("Location: project_page.php?client=". $client ."&number=". $projectnr ."&name=". $projectname ."");
			}
		} else {
			header("Location: project_page.php?client=". $client ."&number=". $projectnr ."&name=". $projectname ."");
		}
		$conn->close();
	} else {
		header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."");
	}
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
						<h2>
							<?php
								if ($edit) {
									echo 'Redigera mötesprotokoll';
								} else {
									echo 'Mötesprotokoll';
								}
							?>
						</h2>
					</div>
					<div class="right_column_content">
						<form action="meeting_edit.php" onsubmit="return validateForm()" method="post" name="edit_form" id="form">
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
											<input type="text" value="<?php echo $date;?>" class="form_textbox form_borderless" id="form_date" readonly/>
										</div>
									</div>
									
									<div class="form_row">
										<h5>Handläggare: </h5>
										<div>
											<input type="text" value="<?php echo $authorname; ?>" class="form_textbox form_borderless" readonly/><br>
											<input type="text" value="<?php echo $phonenumber; ?>" class="form_textbox form_borderless" readonly/><br>
											<input type="text" name="author" value="<?php echo $author; ?>" class="form_textbox form_borderless" readonly/>
										</div>
									</div>
									
									<div id="form_info">
										<div><h5>Rubrik: </h5></div>
										<textarea type="text" class="form_textbox form_borderless" id="form_mainheader" readonly><?php echo ''. $type .' nr '. $meetingid .': '. $mainheader .''; ?></textarea><br>
										<?php
											if ($edit) {
												echo
													'<div><h5>Tid: </h5><p>*</p></div>
													<input type="time" name="time" class="form_timebox" id="form_time" value="'. $time .'"/>- <input type="time" name="time2" class="form_timebox" id="form_time2" value="'. $time2 .'"/><br>
													<div><h5>Plats: </h5><p>*</p></div>
													<input type="text" name="jobsite" maxlength="120" class="form_textbox" id="form_jobsite" value="'. $jobsite .'"/>';
											} else {
												echo
												'<div><h5>Tid: </h5></div>
												'. $time .' - '. $time2 .'<br>
												<div><h5>Plats: </h5></div>
												'. $jobsite .'';
											}
										?>
										
									</div>
								</div>
								
								<div class="form_col">
									<div class="form_row">
										<h3>Närvarande </h3>
										<?php
											$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
											if ($conn->connect_error) {
												die("Connection failed: " . $conn->connect_error);
											}
											mysqli_set_charset($conn,"utf8");
											
											$names = array();
											$emails = array();
											$arrsize = 0;
											
											$sql = "SELECT * FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."' AND present='1'";
											$result = $conn->query($sql);
											if ($result->num_rows > 0) {
												$rows = $result->num_rows;
												$count = 0;
												if ($edit) {
													echo
														'<div>
															<div id="form_name_title"><h5>Namn: </h5><p>*</p></div><div id="form_company_title"><h5>Företag: </h5><p>*</p></div><div class="form_inline"><h5>Email: </h5><p>*</p></div>
														</div>';
													echo '<input type="hidden" name="presentcount" value="'. $rows .'" id="form_present_count"/>';
													echo '<div id="form_present">';
													while ($row = $result->fetch_assoc()) {
														$names[] = $row['fullname'];
														$emails[] = $row['email'];
														$arrsize++;
														echo
															'<div>
																'. ($count + 1) .'. <input type="text" name="present'. $count .'" class="form_textbox" id="form_present'. $count .'" onchange="isLetterOrSpaceKey(this); updateSupervisors();" maxlength="80" placeholder="Förnamn Efternamn" value="'. $row['fullname'] .'"/><input type="text" name="company'. $count .'" class="form_textbox" id="form_company'. $count .'" maxlength="80" value="'. $row['company'] .'"/><input type="text" name="email'. $count .'" class="form_textbox" id="form_email'. $count .'" maxlength="255" value="'. $row['email'] .'" onchange="updateSupervisors()"/>';
														if ($rows > 1 && $count != 0) {
															echo '<input type="button" onclick="removePresentField('. $count .')" value="Ta bort rad" class="form_remove_button" id="form_present_remove_button'. $count .'" style="display: inline;"/>';
														} else {
															echo '<input type="button" onclick="removePresentField('. $count .')" value="Ta bort rad" class="form_remove_button" id="form_present_remove_button'. $count .'"/>';
														}
														echo '</div>';
														$count++;
													}
													echo '</div>';
													echo '<input type="button" onclick="addPresentField()" value="Lägg till rad" class="form_add_button" id="form_present_add_button"/>';
												} else { // If !$edit
													echo
														'<div>
															<div id="form_name_edit_title"><h5>Namn: </h5></div><div id="form_company_edit_title"><h5>Företag: </h5></div><div class="form_inline"><h5>Email: </h5></div>
														</div>';
													echo '<input type="hidden" value="'. $rows .'" id="form_present_count"/>';
													echo '<div id="form_present">';
													while ($row = $result->fetch_assoc()) {
														$names[] = $row['fullname'];
														$emails[] = $row['email'];
														$arrsize++;
														echo
															'<div>
																'. ($count + 1) .'. <input type="text" class="form_textbox form_borderless" id="form_present'. $count .'" value="'. $row['fullname'] .'" readonly/><input type="text" class="form_textbox form_borderless" id="form_company'. $count .'" value="'. $row['company'] .'" readonly/><input type="text" class="form_textbox form_borderless" id="form_email'. $count .'" value="'. $row['email'] .'" readonly/>
															</div>';
														$count++;
													}
													echo '</div>';
												}
											}
										?>
									</div>
									<div>
										<h3>Ej närvarande </h3>
										<?php
											$sql = "SELECT * FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."' AND present='0'";
											$result = $conn->query($sql);
											if ($result->num_rows > 0) {
												$rows = $result->num_rows;
												$count = 0;
												if ($edit) {
													echo
														'<div>
															<div id="form_name_title"><h5>Namn: </h5><p>*</p></div><div id="form_company_title"><h5>Företag: </h5><p>*</p></div><div class="form_inline"><h5>Email: </h5><p>*</p></div>
														</div>';
													echo '<input type="hidden" name="notpresentcount" value="'. $rows .'" id="form_not_present_count"/>';
													echo '<div id="form_not_present">';
													while ($row = $result->fetch_assoc()) {
														$names[] = $row['fullname'];
														$emails[] = $row['email'];
														$arrsize++;
														echo
															'<div>
																'. ($count + 1) .'. <input type="text" name="notpresent'. $count .'" class="form_textbox" id="form_not_present'. $count .'" onchange="isLetterOrSpaceKey(this); updateSupervisors();" maxlength="80" placeholder="Förnamn Efternamn" value="'. $row['fullname'] .'"/><input type="text" name="notcompany'. $count .'" class="form_textbox" id="form_not_company'. $count .'" maxlength="80" value="'. $row['company'] .'"/><input type="text" name="notemail'. $count .'" class="form_textbox" id="form_not_email'. $count .'" maxlength="255" value="'. $row['email'] .'" onchange="updateSupervisors()"/><input type="button" onclick="removeNotPresentField('. $count .')" value="Ta bort rad" class="form_remove_button" id="form_not_present_remove_button'. $count .'" style="display: inline;"/>
															</div>';
														$count++;
													}
													echo '</div>';
													echo '<input type="button" onclick="addNotPresentField()" value="Lägg till rad" class="form_add_button" id="form_not_present_add_button"/>';
												} else { // If !$edit
													echo
														'<div>
															<div id="form_name_edit_title"><h5>Namn: </h5></div><div id="form_company_edit_title"><h5>Företag: </h5></div><div class="form_inline"><h5>Email: </h5></div>
														</div>';
													echo '<input type="hidden" value="'. $rows .'" id="form_not_present_count"/>';
													echo '<div id="form_not_present">';
													while ($row = $result->fetch_assoc()) {
														$names[] = $row['fullname'];
														$emails[] = $row['email'];
														$arrsize++;
														echo
															'<div>
																'. ($count + 1) .'. <input type="text" class="form_textbox form_borderless" id="form_not_present'. $count .'" value="'. $row['fullname'] .'" readonly/><input type="text" class="form_textbox form_borderless" id="form_not_company'. $count .'" value="'. $row['company'] .'" readonly/><input type="text" class="form_textbox form_borderless" id="form_not_email'. $count .'" value="'. $row['email'] .'" readonly/>
															</div>';
														$count++;
													}
													echo '</div>';
												}
											} else { // If $result->num_rows == 0
												if ($edit) {
													echo
														'<div>
															<div id="form_name_title"><h5>Namn: </h5><p>*</p></div><div id="form_company_title"><h5>Företag: </h5><p>*</p></div><div class="form_inline"><h5>Email: </h5><p>*</p></div>
														</div>';
													echo '<input type="hidden" name="notpresentcount" value="1" id="form_not_present_count"/>';
													echo '<div id="form_not_present">';
													echo
														'<div>
															1. <input type="text" name="notpresent0" class="form_textbox" id="form_not_present0" onchange="isLetterOrSpaceKey(this); updateSupervisors();" maxlength="80" placeholder="Förnamn Efternamn"/><input type="text" name="notcompany0" class="form_textbox" id="form_not_company0" maxlength="80"/><input type="text" name="notemail0" class="form_textbox" id="form_not_email0" maxlength="255" onchange="updateSupervisors()"/><input type="button" onclick="removeNotPresentField(0)" value="Ta bort rad" class="form_remove_button" id="form_not_present_remove_button0" style="display: inline;"/>
														</div>';
													echo '</div>';
													echo '<input type="button" onclick="addNotPresentField()" value="Lägg till rad" class="form_add_button" id="form_not_present_add_button"/>';
												}
											}
											$conn->close();
										?>
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
									$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
									if ($conn->connect_error) {
										die("Connection failed: " . $conn->connect_error);
									}
									mysqli_set_charset($conn,"utf8");
									
									$sql = "SELECT * FROM db_project_meeting_header WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."'";
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										echo '<input type="hidden" name="contentrows" id="form_content_count" value="'. $result->num_rows .'"/>';
										echo '<div id="form_content_rows">';
										$count = 0;
										
										if ($edit) {
											while ($row = $result->fetch_assoc()) {
												echo '<div class="task_row" id="form_content_row'. $count .'">';
												echo 	'<div class="content_row content_row_header">';
												echo		'<div class="content_col content_small_col">'. $count .'</div>';
												echo		'<div class="content_col"><input type="text" name="header'. $count .'" class="form_textbox form_headerbox" id="form_header'. $count .'" maxlength="80" value="'. $row['text'] .'"/></div>';
												echo		'<div class="content_col content_supervisor_col"></div>';
												echo		'<div class="content_col content_tools_col"><input type="button" onclick="removeHeaderField('. $count .')" value="Ta Bort Rubrik" class="form_task_remove_button" id="form_header_remove_button'. $count .'"/></div>';
												echo 	'</div>';
												
												$sql = "SELECT id, text FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."' AND header='". $row['id'] ."'";
												$id_result = $conn->query($sql);
												$maxid = 1;
												
												while ($id_row = $id_result->fetch_assoc()) {
													$sql = "SELECT MIN(meeting) as meeting FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND header='". $row['id'] ."' AND id='". $id_row['id'] ."' AND text='". $id_row['text'] ."'";
													$min_result = $conn->query($sql);
													$min_row = $min_result->fetch_assoc();
													
													if ($min_row['meeting'] == $meetingid) {
														$maxid++;
													}
												}
												$sql = "SELECT * FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."' AND header='". $row['id'] ."'";
												$task_result = $conn->query($sql);
												if ($task_result->num_rows > 0) {
													echo '<input type="hidden" name="taskrows'. $count .'" id="form_task_count'. $count .'" value="'. $task_result->num_rows .'"/>';
													echo '<input type="hidden" name="maxid'. $count .'" id="form_maxid'. $count .'" value="'. $maxid .'"/>';
													echo '<div id="form_content_task_rows'. $count .'">';
													$taskcount = 0;
													
													while ($task_row = $task_result->fetch_assoc()) {
														$sql = "SELECT MIN(meeting) as meeting FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND header='". $row['id'] ."' AND id='". $task_row['id'] ."' AND text='". $task_row['text'] ."'";
														$min_result = $conn->query($sql);
														$min_row = $min_result->fetch_assoc();

														$task_id = $task_row['id'];
														
														echo '<div class="content_row">';
														echo 	'<div class="content_col content_small_col"><input type="hidden" name="task_id'. $count .''. $taskcount .'" id="form_task_id'. $count .''. $taskcount .'" value="'. $task_id .'"/>'. $task_id .'</div>';
														echo 	'<div class="content_col"><input type="text" name="task'. $count .''. $taskcount .'" class="form_textbox form_headerbox" id="form_task'. $count .''. $taskcount .'" maxlength="255" value="'. $task_row['text'] .'"/></div>';
														$supercount = 0;
														for ($i = 0; $i < 3; $i++) {
															if ($task_row['supervisor'. ($i + 1) .''] != '') {
																$supercount++;
															}
														}
														if ($supercount > 0) {
															echo 	'<input type="hidden" id="form_super_count'. $count .''. $taskcount .'" value="'. $supercount .'"/>';
															echo 	'<div class="content_col content_supervisor_col" id="form_supervisor_col'. $count .''. $taskcount .'">';
															for ($i = 0; $i < $supercount; $i++) {
																echo '<select name="supervisor'. $count .''. $taskcount .''. $i .'" class="form_superbox" id="form_supervisor'. $count .''. $taskcount .''. $i .'"><option value=""></option>';
																	if ($task_row['supervisor'. ($i + 1) .''] == 'Info') {
																		echo '<option value="Info" selected>Info</option>';
																		echo '<option value="Klart">Klart</option>';
																	} else if ($task_row['supervisor'. ($i + 1) .''] == 'Klart') {
																		echo '<option value="Info">Info</option>';
																		echo '<option value="Klart" selected>Klart</option>';
																	} else {
																		echo '<option value="Info">Info</option>';
																		echo '<option value="Klart">Klart</option>';
																	}
																	for ($j = 0; $j < $arrsize; $j++) {
																		if ($task_row['supervisor'. ($i + 1) .''] == $emails[$j]) {
																			echo '<option value="'. $emails[$j] .'" selected>'. $names[$j] .'</option>';
																		} else {
																			echo '<option value="'. $emails[$j] .'">'. $names[$j] .'</option>';
																		}
																	}
																echo '</select>';
															}
														} else { // If $supercount == 0
															echo 	'<input type="hidden" id="form_super_count'. $count .''. $taskcount .'" value="1"/>';
															echo 	'<div class="content_col content_supervisor_col" id="form_supervisor_col'. $count .''. $taskcount .'">';
															echo '<select name="supervisor'. $count .''. $taskcount .'0" class="form_superbox" id="form_supervisor'. $count .''. $taskcount .'0"><option value=""></option><option value="Info">Info</option><option value="Klart">Klart</option>';
																for ($j = 0; $j < $arrsize; $j++) {
																	echo '<option value="'. $emails[$j] .'">'. $names[$j] .'</option>';
																}
															echo '</select>';
														}
														echo	'</div>';
														echo		'<div class="content_col content_tools_col" id="form_tools_col'. $count .''. $taskcount .'">';
														if ($min_row['meeting'] == $meetingid) {
															echo		'<input type="button" onclick="removeTaskField('. $count .','. $taskcount .')" value="Ta bort rad" class="form_task_remove_button" id="form_task_remove_button'. $count .''. $taskcount .'"/>';
														} else {
															echo		'<div id="form_task_remove_button'. $count .''. $taskcount .'"></div>';
														}
														echo 			'<input type="button" onclick="addSuperField('. $count .','. $taskcount .')" value="Lägg till ansvarig" class="form_supervisor_add_button" id="form_supervisor_add_button'. $count .''. $taskcount .'"/>';
														echo		'</div>';
														echo '</div>';
														$taskcount++;
													}
													echo '</div>';
													echo '<input type="button" onclick="addTaskField('. $count .')" value="Ny rad" class="form_task_add_button" id="form_task_add_button'. $count .'"/>';
												} else {
													echo '<input type="hidden" name="taskrows'. $count .'" id="form_task_count'. $count .'" value="0"/>';
												}
												echo '</div>';
												$count++;
											}
										} else { // If !$edit
											while ($row = $result->fetch_assoc()) {
												echo '<div class="task_row" id="form_content_row'. $count .'">';
												echo 	'<div class="content_row content_row_header">';
												echo		'<div class="content_col content_small_col">'. $count .'</div>';
												echo		'<div class="content_col"><input type="text" class="form_textbox form_headerbox content_row_header form_edit_box form_borderless" id="form_header'. $count .'" value="'. $row['text'] .'" readonly/></div>';
												echo		'<div class="content_col content_supervisor_col"></div>';
												echo		'<div class="content_col content_tools_col"></div>';
												echo 	'</div>';
												
												$sql = "SELECT * FROM db_project_meeting_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $seriesid ."' AND meeting='". $meetingid ."' AND header='". $row['id'] ."'";
												$task_result = $conn->query($sql);
												if ($task_result->num_rows > 0) {
													echo '<input type="hidden" id="form_task_count'. $count .'" value="'. $task_result->num_rows .'"/>';
													echo '<div id="form_content_task_rows'. $count .'">';
													$taskcount = 0;
													
													while ($task_row = $task_result->fetch_assoc()) {
														echo '<div class="content_row">';
														echo 	'<div class="content_col content_small_col">'. $task_row['id'] .'</div>';
														echo 	'<div class="content_col"><input type="text" class="form_textbox form_headerbox form_borderless form_edit_box" id="form_task'. $count .''. $taskcount .'" value="'. $task_row['text'] .'" readonly/></div>';
														$supercount = 0;
														for ($i = 0; $i < 3; $i++) {
															if ($task_row['supervisor'. ($i + 1) .''] != '') {
																$supercount++;
															}
														}
														echo 	'<input type="hidden" id="form_super_count'. $count .''. $taskcount .'" value="'. $supercount .'"/>';
														echo 	'<div class="content_col content_supervisor_col" id="form_supervisor_col'. $count .''. $taskcount .'">';
														
														echo '<div class="form_edit_box" id="form_supervisor'. $count .''. $taskcount .'">';
														for ($i = 0; $i < $supercount; $i++) {
															$j = array_search($task_row['supervisor'. ($i + 1) .''], $emails);
															if ($j !== false) {
																$string = explode(" ", $names[$j]);
																$fname = mb_substr($string[0], 0, 2, 'utf-8');
																$sname = mb_substr($string[1], 0, 2, 'utf-8');
																$sname = mb_strtolower($sname, 'utf-8');
																$string = ''. $fname .''. $sname .'';
															} else {
																if ($task_row['supervisor'. ($i + 1) .''] == "Klart") {
																	$string = 'Klart';
																} else if ($task_row['supervisor'. ($i + 1) .''] == "Info") {
																	$string = 'Info';
																} else {
																	$string = '';
																}
															}
															echo $string;
															if ($i != $supercount - 1) {
																echo ' / ';
															}
														}
														echo '</div>';
														
														echo	'</div>';
														echo		'<div class="content_col content_tools_col" id="form_tools_col'. $count .''. $taskcount .'"></div>';
														echo '</div>';
														$taskcount++;
													}
													echo '</div>';
												} else {
													echo '<input type="hidden" id="form_task_count'. $count .'" value="0"/>';
													echo '<div class="content_row"></div>';
												}
												echo '</div>';
												$count++;
											}
										}
										echo '</div>';
										if ($edit) {
											echo '<input type="button" onclick="addHeaderField()" value="Ny rubrik" class="form_add_button" id="form_header_add_button"/>';
										}
									}
									$conn->close();
								?>
							</div>
							
							<div id="form_send_button_div">
								<?php
									if ($edit) {
										echo '<input type="hidden" name="send" value="no" id="form_send_button_value"/>';
										if ($sent == 0) {
											echo '<input type="submit" value="Spara" id="form_send_button" onclick="updateSend(0)"/>';
											echo '<input type="submit" value="Spara och skicka" id="form_send_button" onclick="updateSend(1)"/>';
										} else if ($sent == 1) {
											echo '<input type="submit" value="Spara" id="form_send_button" onclick="updateSend(1)"/>';
											echo '<input type="submit" value="Spara och lås" id="form_send_button" onclick="updateSend(2)"/>';
										}
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
		<script type="text/javascript" src="js/meeting_page.js"></script>
	</body>
</html>