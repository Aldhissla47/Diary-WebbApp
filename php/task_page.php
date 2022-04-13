<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	}
	if (!isset($_SESSION['project']) || $_SESSION['user']['permission'] > 4) {
		header("Location: index.php");
	}
	if (isset($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] === true) {
		$admin = true;
	} else {
		$admin = false;
	}
	$client = $_SESSION['project']['client'];
	$projectnr = $_SESSION['project']['number'];
	$projectname = $_SESSION['project']['name'];
	$user = $_SESSION['user']['email'];
	
	$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	mysqli_set_charset($conn,"utf8");
	
	$sql = "SELECT firstname, surname FROM db_user WHERE email='". $user ."'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();

	$fname = mb_substr($row['firstname'], 0, 2, 'utf-8');
	$sname = mb_substr($row['surname'], 0, 2, 'utf-8');
	
	$username = ''. $fname .''. mb_strtolower($sname, 'UTF-8') .'';
	$currentdate = date('Y-m-d');
	
	$sql = "SELECT MAX(id) AS id FROM db_project_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."'";
	$max_id_result = $conn->query($sql);
	$max_id_row = $max_id_result->fetch_assoc();
	if (!empty($max_id_row['id'])) {							
		$maxid = $max_id_row['id'];
	} else {
		$maxid = 1;
	}
	if (!empty($_POST['show'])) {
		$show = $_POST['show'];
	} else {
		$show = 1;
	}
	if (!empty($_POST['sort'])) {
		$sort = $_POST['sort'];
	} else {
		$sort = 1;
	}
	$emails = array();
	$names = array();
	$arraysize = 0;
	$sql = "SELECT db_project_member.permission, db_user.email, db_user.firstname, db_user.surname FROM db_project_member LEFT JOIN db_user ON db_project_member.user = db_user.email WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND permission<6";
	$member_result = $conn->query($sql);
	if ($member_result->num_rows > 0) {
		while ($member_row = $member_result->fetch_assoc()) {
			$fname = mb_substr($member_row['firstname'], 0, 2, 'utf-8');
			$sname = mb_substr($member_row['surname'], 0, 2, 'utf-8');
			$name = ''. $fname .''. mb_strtolower($sname, 'UTF-8') .'';
			
			$emails[] = $member_row['email'];
			$names[] = $name;
			$arraysize++;
		}
	}
	$conn->close();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Uppgiftslista </title>

		<link rel="stylesheet" type="text/css" href="css/_main.css">
		<link rel="stylesheet" type="text/css" href="css/task_page.css">
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
						<h2>Uppgiftslista</h2>
						<div class="task_selector">
							Visa:
							<?php
								$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
								if ($conn->connect_error) {
									die("Connection failed: " . $conn->connect_error);
								}
								mysqli_set_charset($conn,"utf8");
								
								$sql = "SELECT id FROM db_project_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."'";
								$result = $conn->query($sql);
								echo '<form action="task_page.php" method="post" name="form" class="selector_form">';
								echo '<select name="show" class="form_selector_box" onchange="this.form.submit()">';
								if ($result->num_rows > 0) {
									if ($show == 1) {
										echo '<option value="1" selected>Alla</option>';
										echo '<option value="2">Utförda</option>';
										echo '<option value="3">Ej utförda</option>';
									} else if ($show == 2) {
										echo '<option value="1">Alla</option>';
										echo '<option value="2" selected>Utförda</option>';
										echo '<option value="3">Ej utförda</option>';
									} else {
										echo '<option value="1">Alla</option>';
										echo '<option value="2">Utförda</option>';
										echo '<option value="3" selected>Ej utförda</option>';
									}
								} else {
									echo '<option value="1">Alla</option>';
								}
								echo '</select>';
							?>
						</div>
						<div class="task_selector">
							Sortering:
							<?php
								echo '<select name="sort" class="form_selector_box" onchange="this.form.submit()">';
								if ($sort == 1) {
									echo '<option value="1" selected>ID</option>';
									echo '<option value="2">Senast utförd</option>';
								} else {
									echo '<option value="1">ID</option>';
									echo '<option value="2" selected>Senast utförd</option>';
								}
								echo '</select>';
								echo '</form>';
							?>
						</div>
					</div>
					<div id="task_wrapper">
						<div id="tasks_header" class="task_row task_row_odd">
							<div class="task_column task_number_column"><p><u>ID</u></p></div>
							<div class="task_column task_small_column"><p><u>Kategori</u></p></div>
							<div class="task_column task_date_column"><p><u>Skapad</u></p></div>
							<div class="task_column task_large_column"><p><u>Fråga/Info</u></p></div>
							<div class="task_column task_small_column"><p><u>Ansvarig</u></p></div>
							<div class="task_column task_small_column"><p><u>Skapad Av</u></p></div>
							<div class="task_column task_date_column"><p><u>Senast utförd</u></p></div>
							<div class="task_column task_large_column"><p><u>Svar</u></p></div>
							<div class="task_column task_date_column"><p><u>Utförd</u></p></div>
							<div class="task_column task_small_column"><p><u>Utförd Av</u></p></div>
							<div class="task_column task_private_column"><p><u>Privat</u></p></div>
						</div>
						
						<form action="task_add.php" onsubmit="return validateForm()" method="post" name="form" id="form">
							<input type="hidden" name="client" value="<?php echo $client; ?>"/>
							<input type="hidden" name="number" value="<?php echo $projectnr; ?>"/>
							<input type="hidden" name="name" value="<?php echo $projectname; ?>"/>
							<input type="hidden" name="user" id="form_user" value="<?php echo $user; ?>"/>
							<input type="hidden" name="username" id="form_username" value="<?php echo $username; ?>"/>
							
							<div id="task_content">
								<input type="hidden" id="form_maxid" value="<?php if ($maxid == 1) { echo $maxid; } else { echo $maxid + 1; } ?>"/>
									<?php
										$sql = "SELECT * FROM db_project_task WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."'";
										if ($show == 2) {
											$sql .= " AND completed IS NOT NULL";
										} else if ($show == 3) {
											$sql .= " AND completed IS NULL";
										}
										if ($sort == 2) {
											$sql .= " ORDER BY ISNULL(deadline),deadline";
										}
										$result = $conn->query($sql);
										$taskcount = 0;
										if ($result->num_rows > 0) {
											$taskcount = $result->num_rows + 1;
											$count = 0;
											echo '<input type="hidden" name="taskcount" id="form_taskcount" value="'. $taskcount .'"/>';
											echo '<div id="task_rows">';
											
											while ($row = $result->fetch_assoc()) {
												if ($row['private'] == 0 || ($row['private'] == 1 && ($row['author'] == $user || $row['supervisor1'] == $user || $row['supervisor2'] == $user || $row['supervisor3'] == $user))) {
													$i = array_search($row['author'], $emails);
													if ($i !== false) {
														$authorname = $names[$i];
													} else {
														$authorname = '';
													}
													$i = array_search($row['worker'], $emails);
													if ($i !== false) {
														$workername = $names[$i];
													} else {
														$workername = '';
													}
													echo
													'<div id="task_row'. $count .'" class="task_row">
														<div class="task_column task_number_column"><input type="hidden" name="id'. $count .'" id="form_id'. $count .'" value="'. $row['id'] .'"/><p>'. $row['id'] .'</p></div>
														<div class="task_column task_small_column"><input type="text" class="task_row_box form_borderless" id="form_category'. $count .'" value="'. $row['category'] .'" readonly/></div>
														<div class="task_column task_date_column"><p>'. $row['created'] .'</p></div>
														<div class="task_column task_large_column"><input type="text" class="task_row_box_large form_borderless" id="form_question'. $count .'" value="'. $row['question'] .'" readonly/></div>
														<div class="task_column task_small_column"><input type="hidden" name="supervisor'. $count .'0" id="form_supervisor'. $count .'0" value="'. $row['supervisor1'] .'"/><input type="hidden" name="supervisor'. $count .'1" id="form_supervisor'. $count .'1" value="'. $row['supervisor2'] .'"/><input type="hidden" name="supervisor'. $count .'2" id="form_supervisor'. $count .'2" value="'. $row['supervisor3'] .'"/><p>';
													$supercount = 0;
													for ($i = 0; $i < 3; $i++) {
														if ($row['supervisor'. ($i + 1) .''] != '') {
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
															echo $row['supervisor'. ($i + 1) .''];
														}
													}	
													echo '</p></div>
														<div class="task_column task_small_column"><input type="hidden" id="form_author'. $count .'" value="'. $row['author'] .'"/><p>'. $authorname .'</p></div>';

													if ($row['completed'] == '') {
														if ($row['deadline'] != '' && $row['deadline'] <= $currentdate) {
															echo '<div class="task_column task_date_column red"><p>'. $row['deadline'] .'</p></div>';
														} else {
															echo '<div class="task_column task_date_column"><p>'. $row['deadline'] .'</p></div>';
														}
														if ($row['supervisor1'] == $user || $row['supervisor2'] == $user || $row['supervisor3'] == $user) {
															echo '<div class="task_column task_large_column"><input type="text" name="answer'. $count .'" class="task_row_box_large" id="form_answer'. $count .'" maxlength="255"/></div>';
														} else { // If supervisor != user
															echo '<div class="task_column task_large_column"></div>'; // answer
														}
														echo '<div class="task_column task_date_column"></div>'; // completed
														echo '<div class="task_column task_small_column"></div>'; // worker

														if ($row['author'] == $user) {
															if ($row['private'] == 1) {
																echo '<div class="task_column task_private_column"><input type="checkbox" name="private'. $count .'" class="task_row_checkbox" id="form_private'. $count .'" value="1" checked/></div>';
															} else {
																echo '<div class="task_column task_private_column"><input type="checkbox" name="private'. $count .'" class="task_row_checkbox" id="form_private'. $count .'" value="1"/></div>';
															}
														}
													} else { // If $row['completed'] != ''
														echo
															'<div class="task_column task_date_column"><p>'. $row['deadline'] .'</p></div>
															<div class="task_column task_large_column"><input type="text" class="task_row_box_large form_borderless" id="form_answer'. $count .'" value="'. $row['answer'] .'" readonly/></div>
															<div class="task_column task_date_column"><p>'. $row['completed'] .'</p></div>
															<div class="task_column task_small_column"><input type="hidden" id="form_worker'. $count .'" value="'. $row['worker'] .'"/><p>'. $workername .'</p></div>';
															
														if ($row['private'] == 1) {
															echo '<div class="task_column task_private_column"><input type="hidden" name="private'. $count .'" id="form_private'. $count .'" value="1"/></div>';
														} else {
															echo '<div class="task_column task_private_column"><input type="hidden" name="private'. $count .'" id="form_private'. $count .'" value="0"/></div>';
														}
													}
													echo '</div>';
													$count++;
												}
											}
											if ($show != 2) {
												echo '<div id="task_row'. $count .'" class="task_row">';
												echo '<div class="task_column task_number_column"><input type="hidden" name="id'. $count .'" id="form_id'. $count .'" value="'. ($maxid + 1) .'"/><p>'. ($maxid + 1) .'</p></div>
													<div class="task_column task_small_column"><input type="text" name="category'. $count .'" class="task_row_box" id="form_category'. $count .'" maxlength="40"/></div>
													<div class="task_column task_date_column"><input type="text" class="task_row_box" id="form_created'. $count .'" value="'. date('Y-m-d') .'" readonly/></div>
													<div class="task_column task_large_column"><input type="text" name="question'. $count .'" class="task_row_box_large" id="form_question'. $count .'" maxlength="255"/></div>
													<div class="task_column task_small_column"><select name="supervisor'. $count .'0" class="task_row_box task_row_dropbox" id="form_supervisor'. $count .'0" onchange="supervisorUpdate('. $count .')"><option value=""></option>';
												
												for ($i = 0; $i < $arraysize; $i++) {
													echo '<option value="'. $emails[$i] .'">'. $names[$i] .'</option>';
												}
												echo '</select></div>';
												echo '<div class="task_column task_small_column"><input type="hidden" name="author'. $count .'" id="form_author'. $count .'" value="'. $user .'"/><input type="text" class="task_row_box" id="form_authorname'. $count .'" value="'. $username .'" readonly/></div>
													<div class="task_column task_date_column"><input type="date" name="deadline'. $count .'" class="task_row_box" id="form_deadline'. $count .'" onchange="return validateDeadline('. $count .')"/></div>
													<div class="task_column task_large_column" id="task_answer_column'. $count .'"></div>
													<div class="task_column task_date_column" id="task_completed_column'. $count .'"></div>
													<div class="task_column task_small_column" id="task_worker_column'. $count .'"></div>
													<div class="task_column task_private_column"><input type="checkbox" name="private'. $count .'" class="task_row_checkbox" id="form_private'. $count .'" value="1"/></div>';
												echo '</div>';
											}
											echo '</div>';
										} else { // If $result->num_rows == 0
											if ($show != 2) {
												echo '<input type="hidden" name="taskcount" id="form_taskcount" value="1"/>';
												echo '<div id="task_rows">';
												echo '<div id="task_row0" class="task_row">';
												echo '<div class="task_column task_number_column"><input type="hidden" name="id0" id="form_id0" value="1"/><p>1</p></div>
													<div class="task_column task_small_column"><input type="text" name="category0" class="task_row_box" id="form_category0" maxlength="40"/></div>
													<div class="task_column task_date_column"><input type="text" class="task_row_box" id="form_created0" value="'. date('Y-m-d') .'" readonly/></div>
													<div class="task_column task_large_column"><input type="text" name="question0" class="task_row_box_large" id="form_question0" maxlength="255"/></div>
													<div class="task_column task_small_column"><select name="supervisor00" class="task_row_box task_row_dropbox" id="form_supervisor00" onchange="supervisorUpdate(0)"><option value=""></option>';
												
												for ($i = 0; $i < $arraysize; $i++) {
													echo '<option value="'. $emails[$i] .'">'. $names[$i] .'</option>';
												}
												echo '</select></div>';
												echo '<div class="task_column task_small_column"><input type="hidden" name="author0" id="form_author0" value="'. $user .'"/><input type="text" class="task_row_box" id="form_authorname0" value="'. $username .'" readonly/></div>
													<div class="task_column task_date_column"><input type="date" name="deadline0" class="task_row_box" id="form_deadline0" onchange="return validateDeadline(0)"/></div>
													<div class="task_column task_large_column" id="task_answer_column0"></div>
													<div class="task_column task_date_column" id="task_completed_column0"></div>
													<div class="task_column task_small_column" id="task_worker_column0"></div>
													<div class="task_column task_private_column" id="task_private_column0"></div>';
												echo '</div>';
												echo '</div>';
											}
										}
										$conn->close();
									?>
								<a name="bottomOfThePage"></a>
							</div>
							
							<?php
								if ($show != 2) {
									echo '<input type="button" value="Lägg till rad" onclick="addRow()" class="form_add_button" id="form_add_button"/>';
								}
							?>
							
							<div id="form_send_button_div">
								<?php
									if ($show != 2) {
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
		<script type="text/javascript" src="js/task_page.js"></script>
	</body>
</html>