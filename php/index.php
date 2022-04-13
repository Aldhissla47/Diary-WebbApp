<?php
	$server = 'misaw.se.mysql';
	session_start();
	include 'get_company_info.php';
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] !== true) {		
		header("Location: login_page.php");
	}
	unset($_SESSION['project']);
	unset($_SESSION['user']['permission']);
	
	$currentdate = date('Y-m-d');
	
	function mergeNames($fname, $sname) {
		$fname = mb_substr($fname, 0, 2, 'utf-8');
		$sname = mb_substr($sname, 0, 2, 'utf-8');
		$name = ''. $fname .''. mb_strtolower($sname, 'UTF-8') .'';
		return $name;
	}
	function printInbox($client, $number, $name, $companynr, $company, $workday, $author, $reviewer, $date) {
		if (!empty($client) && !empty($number) && !empty($name) && !empty($companynr) && !empty($company) && !empty($workday) && !empty($author) && !empty($reviewer) && !empty($date)) {
			$cmpnr = str_replace('-', '', $companynr);
			echo '<div class="diary_column diary_small_column"><p>'. $number .'</p></div>';
			echo '<div class="diary_column"><p>'. $name .'</p></div>';
			echo '<div class="diary_column"><p><a target="_blank" href="https://www.allabolag.se/'. $cmpnr .'">'. $company .'</a></p></div>';
			echo '<div class="diary_column diary_small_column"><p>'. $workday .'</p></div>';
			echo '<div class="diary_column"><p>'. $author .'</p></div>';
			echo '<div class="diary_column"><p>'. $reviewer .'</p></div>';
			echo '<div class="diary_column diary_small_column"><p>'. $date .'</p></div>';
			echo '<div class="diary_column diary_tools_column">
					<div class="diary_tools_form">
						<a class="link_button selection_button" href="diary_edit_page.php?client='. $client .'&number='. $number .'&name='. $name .'&company='. $companynr .'&workday='. $workday .'">Granska</a>
					</div>
				</div>';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Index </title>

		<link rel="stylesheet" type="text/css" href="css/_main.css">
		<link rel="stylesheet" type="text/css" href="css/index.css">
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
					<div class="index_row" id="diaries">
						<div class="index_column_header">
							<h2>Dagböcker att granska</h2>
						</div>
						<div class="index_column_content">
							<?php
								$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
								if ($conn->connect_error) {
									die("Connection failed: " . $conn->connect_error);
								}
								mysqli_set_charset($conn,"utf8");
								
								echo
									'<div id="diary_content_header" class="inbox_row inbox_row_odd">
										<div class="diary_column diary_small_column"><p><u>Projectnummer</u></p></div>
										<div class="diary_column"><p><u>Projectnamn</u></p></div>
										<div class="diary_column"><p><u>Företag</u></p></div>
										<div class="diary_column diary_small_column"><p><u>Arbetsdag</u></p></div>
										<div class="diary_column"><p><u>Skapad av</u></p></div>
										<div class="diary_column"><p><u>Granskad av</u></p></div>
										<div class="diary_column diary_small_column"><p><u>Datum</u></p></div>
										<div class="diary_column diary_tools_column"><p></p></div>
									</div>';
								echo '<div id="inbox_content">';
								
								$sql = "SELECT db_project_diary.*, db_project_member.permission AS permission FROM db_project_member LEFT JOIN db_project_diary ON db_project_member.client = db_project_diary.client AND db_project_member.number = db_project_diary.number AND db_project_member.name = db_project_diary.name WHERE user='". $_SESSION['user']['email'] ."'";
								$result = $conn->query($sql);
								if ($result->num_rows > 0) {
									$count = 0;
									
									$numbers = array();
									$names = array();
									
									while ($row = $result->fetch_assoc()) {
										if ($row['permission'] != 4) {
											if ($row['reviewer'] !== '') {
												$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['reviewer'] ."'";
												$reviewer_result = $conn->query($sql);
												$reviewer_row = $reviewer_result->fetch_assoc();
												
												$reviewer = ''. $reviewer_row['firstname'] .' '. $reviewer_row['surname'] .'';
												$sql = "SELECT permission FROM db_project_member WHERE client='". $row['client'] ."' AND number='". $row['number'] ."' AND name='". $row['name'] ."' AND user='". $row['reviewer'] ."'";
											} else {									
												$reviewer = 'Ej Granskad';
												$sql = "SELECT permission FROM db_project_member WHERE client='". $row['client'] ."' AND number='". $row['number'] ."' AND name='". $row['name'] ."' AND user='". $row['author'] ."'";
											}
											$member_result = $conn->query($sql);
											$member_row = $member_result->fetch_assoc();
											
											$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['author'] ."'";
											$author_result = $conn->query($sql);
											$author_row = $author_result->fetch_assoc();
											$author = ''. $author_row['firstname'] .' '. $author_row['surname'] .'';
											
											$companynr = str_replace('-', '', $row['company']);
											$i = array_search($companynr, $numbers);
											if ($i !== false) {
												$company = $names[$i];
											} else {
												$company = getCompanyInfo($row['company'], "name");
												if ($company === false) {
													$company = $row['company'];
												} else {
													$numbers[] = $companynr;
													$names[] = $company;
												}
											}
											if ($row['permission'] < 4) {
												if ($row['locked'] == 1) {
													if ($count % 2 == 0) {
														echo '<div id="inbox_content_row'. $count .'" class="inbox_row">';
													} else {
														echo '<div id="inbox_content_row'. $count .'" class="inbox_row inbox_row_odd">';
													}
													printInbox($row['client'], $row['number'], $row['name'], $row['company'], $company, $row['workday'], $author, $reviewer, $row['date']);
													echo '</div>';
													$count = $count + 1;
												}
											} else if ($row['permission'] == 5) {
												if ($row['locked'] == 0 && $row['company'] == $_SESSION['user']['company']) {
													if ($count % 2 == 0) {
														echo '<div id="inbox_content_row'. $count .'" class="inbox_row">';
													} else {
														echo '<div id="inbox_content_row'. $count .'" class="inbox_row inbox_row_odd">';
													}
													printInbox($row['client'], $row['number'], $row['name'], $row['company'], $company, $row['workday'], $author, $reviewer, $row['date']);
													echo '</div>';
													$count = $count + 1;
												}
											} else {
												if ($row['locked'] == 0 && $row['company'] == $_SESSION['user']['company']) {
													if ($row['permission'] < $member_row['permission']) {
														if ($count % 2 == 0) {
															echo '<div id="inbox_content_row'. $count .'" class="inbox_row">';
														} else {
															echo '<div id="inbox_content_row'. $count .'" class="inbox_row inbox_row_odd">';
														}
														printInbox($row['client'], $row['number'], $row['name'], $row['company'], $company, $row['workday'], $author, $reviewer, $row['date']);
														echo '</div>';
														$count = $count + 1;
													}
												}
											}
										}
									}
									unset($numbers);
									unset($names);
								}
								echo '</div>';
								
								$conn->close();
							?>
						</div>
					</div>
					<div class="index_row" id="tasks">
						<div class="index_column_header" id="task_header">
							<h2>Mina uppgifter</h2>
						</div>					
						<div class="index_column_content" id="task_content">
							<?php
								$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
								if ($conn->connect_error) {
									die("Connection failed: " . $conn->connect_error);
								}
								mysqli_set_charset($conn,"utf8");
								
								echo
									'<div id="tasks_header" class="task_row task_row_odd">
										<div class="diary_column diary_small_column"><p><u>Projectnummer</u></p></div>
										<div class="diary_column diary_small_column"><p><u>Projectnamn</u></p></div>
										<div class="task_column task_number_column"><p><u>ID</u></p></div>
										<div class="task_column task_small_column"><p><u>Kategori</u></p></div>
										<div class="task_column task_small_column"><p><u>Skapad</u></p></div>
										<div class="task_column task_large_column"><p><u>Fråga/Info</u></p></div>
										<div class="task_column task_small_column"><p><u>Ansvarig</u></p></div>
										<div class="task_column task_small_column"><p><u>Skapad Av</u></p></div>
										<div class="task_column task_small_column"><p><u>Senast utförd</u></p></div>
										<div class="task_column task_large_column"><p><u>Svar</u></p></div>
										<div class="task_column task_private_column"><p><u>Privat</u></p></div>
									</div>
									<form action="task_index_edit.php" onsubmit="return validateForm()" method="post" name="form" id="form_content">';
									
								$sql = "SELECT * FROM db_project_task WHERE (supervisor1='". $_SESSION['user']['email'] ."' OR supervisor1='". $_SESSION['user']['email'] ."' OR supervisor1='". $_SESSION['user']['email'] ."') AND completed IS NULL ORDER BY ISNULL(deadline),deadline";
								$result = $conn->query($sql);
								if ($result->num_rows > 0) {
									$taskcount = $result->num_rows;
									$count = 0;
									echo '<input type="hidden" name="taskcount" id="form_taskcount" value="'. $taskcount .'"/>';
									echo '<input type="hidden" name="user" id="form_user" value="'. $_SESSION['user']['email'] .'"/>';
									echo '<div id="task_rows">';
									
									$emails = array();
									$names = array();
									
									while ($row = $result->fetch_assoc()) {
										$i = array_search($row['author'], $emails);
										if ($i !== false) {
											$authorname = $names[$i];
										} else {
											$emails[] = $row['author'];
											
											$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['author'] ."'";
											$name_result = $conn->query($sql);
											if ($name_result->num_rows == 1) {
												$name_row = $name_result->fetch_assoc();
												$name = mergeNames($name_row['firstname'], $name_row['surname']);
												$names[] = $name;
												$authorname = $name;
											} else {
												$names[] = "null";
												$authorname = 'null';
											}
										}
										echo
										'<div id="task_row'. $count .'" class="task_row">
											<div class="diary_column diary_small_column"><input type="hidden" name="client'. $count .'" id="form_client'. $count .'" value="'. $row['client'] .'"/><input type="hidden" name="number'. $count .'" id="form_number'. $count .'" value="'. $row['number'] .'"/><p>'. $row['number'] .'</p></div>
											<div class="diary_column diary_small_column"><input type="hidden" name="name'. $count .'" id="form_name'. $count .'" value="'. $row['name'] .'"/><p>'. $row['name'] .'</p></div>
											<div class="task_column task_number_column"><input type="hidden" name="id'. $count .'" id="form_id'. $count .'" value="'. $row['id'] .'"/><p>'. $row['id'] .'</p></div>
											<div class="task_column task_small_column"><input type="text" class="task_row_box form_borderless" id="form_category'. $count .'" value="'. $row['category'] .'" readonly/></div>
											<div class="task_column task_small_column"><p>'. $row['created'] .'</p></div>
											<div class="task_column task_large_column"><input type="text" class="task_row_box_large form_borderless" id="form_question'. $count .'" value="'. $row['question'] .'" readonly/></div>
											<div class="task_column task_small_column"><input type="hidden" id="form_supervisor'. $count .'0" value="'. $row['supervisor1'] .'"/><input type="hidden" id="form_supervisor'. $count .'1" value="'. $row['supervisor2'] .'"/><input type="hidden" id="form_supervisor'. $count .'2" value="'. $row['supervisor3'] .'"/><p>';
										$supercount = 0;
										for ($i = 0; $i < 3; $i++) {
											if ($row['supervisor'. ($i + 1) .''] != '') {
												$j = array_search($row['supervisor'. ($i + 1) .''], $emails);
												if ($j === false) {
													$emails[] = $row['supervisor'. ($i + 1) .''];
													
													$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['supervisor'. ($i + 1) .''] ."'";
													$name_result = $conn->query($sql);
													if ($name_result->num_rows == 1) {
														$name_row = $name_result->fetch_assoc();
														$names[] = mergeNames($name_row['firstname'], $name_row['surname']);
													} else {
														$names[] = "null";
													}
												}
												$supercount++;
											}
										}
										for ($i = 0; $i < $supercount; $i++) {
											$j = array_search($row['supervisor'. ($i + 1) .''], $emails);
											if ($j !== false) {
												echo $names[$j];
												
												if ($i != $supercount - 1) {
													echo ' / ';
												}
											} else {
												echo '';
											}
										}	
										echo '</p></div>
											<div class="task_column task_small_column"><input type="hidden" id="form_author'. $count .'" value="'. $row['author'] .'"/><p>'. $authorname .'</p></div>';
										
										if ($row['deadline'] != '' && $row['deadline'] <= $currentdate) {
											echo '<div class="task_column task_small_column red"><p>'. $row['deadline'] .'</p></div>';
										} else {
											echo '<div class="task_column task_small_column"><p>'. $row['deadline'] .'</p></div>';
										}
										echo '<div class="task_column task_large_column"><input type="text" class="task_row_box_large" name="answer'. $count .'" id="form_answer'. $count .'"/></div>';
										
										echo '<div class="task_column task_private_column"><input type="checkbox" class="task_row_checkbox" name="private'. $count .'" id="form_private'. $count .'" value="1"';
										if ($row['private'] == 1) {
											echo ' checked';
										}
										if ($row['author'] != $_SESSION['user']['email']) {
											echo ' onclick="return false;"';
										}
										echo '/></div>';
										
										echo '</div>';
										$count++;
									}
									echo '</div>';
									echo
										'<div id="form_send_button_div">
											<input type="submit" value="Spara" id="form_send_button"/>
										</div>';
								
									unset($emails);
									unset($names);
								}
								echo '</form>';
								
								$conn->close();
							?>
						</div>
					</div>
				</div>
            </div>

            <div class="footer"></div>

        </div> <!-- Wrapper End-->

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/basic_functions.js"></script>
	</body>
</html>