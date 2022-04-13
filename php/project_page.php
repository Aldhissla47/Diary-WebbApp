<?php
	$server = 'misaw.se.mysql';
	session_start();
	include 'get_company_info.php';
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] !== true) {		
		header("Location: login_page.php");
	}
	if (!empty($_GET['client']) && !empty($_GET['number']) && !empty($_GET['name'])) {
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		$sql = "SELECT permission FROM db_project_member WHERE client='". $_GET['client'] ."' AND number='". $_GET['number'] ."' AND name='". $_GET['name'] ."' AND user='". $_SESSION['user']['email'] ."'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$_SESSION['user']['permission'] = $row['permission'];			
			$_SESSION['project']['client'] = $_GET['client'];
			$_SESSION['project']['number'] = $_GET['number'];
			$_SESSION['project']['name'] = $_GET['name'];
		} else {
			header("Location: index.php");
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
		$conn->close();
	} else {
		header("Location: index.php");
	}
	if (!empty($_GET['selector'])) {
		$_SESSION['selector'] = $_GET['selector'];
	} else {
		$_SESSION['selector'] = 'info';
	}
	if (!empty($_POST['year'])) {
		$monthly_year = $_POST['year'];
	} else {
		$monthly_year = date('Y');
	}
	
	function month($arg1) {
		$month = array('Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December');
		$i = $arg1 - 1;
		return $month[$i];
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Index </title>

		<link rel="stylesheet" type="text/css" href="css/_main.css">
		<link rel="stylesheet" type="text/css" href="css/project_page.css">
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
							switch ($_SESSION['selector']) {
								case "info" :	echo '<h2>Projektinfo</h2>';
												if (isset($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] === true) {
													echo '<div>';
													echo 	'<a class="link_button selection_button" href="project_edit_page.php">Redigera</a>';
													echo '</div>';
												}
												break;
								case "member" :	echo '<h2>Projektmedlemmar</h2>';
												break;
								case "diary" :	echo '<h2>Dagböcker</h2>';
												break;
								case "abnorm" :	echo '<h2>Avvikelser</h2>';
												break;
								case "monthly" :echo '<h2>Månadsrapporter</h2>';
												$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
												if ($conn->connect_error) {
													die("Connection failed: " . $conn->connect_error);
												}
												mysqli_set_charset($conn,"utf8");
												if ($_SESSION['user']['permission'] == 4) {
													$sql = "SELECT DISTINCT year FROM db_project_monthly_report WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND author='". $_SESSION['user']['email'] ."'";
												} else if ($_SESSION['user']['permission'] < 4) {
													$sql = "SELECT DISTINCT year FROM db_project_monthly_report WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND locked>0";
												}
												$result = $conn->query($sql);
												echo '<form action="project_page.php?client='. $_SESSION['project']['client'] .'&number='. $_SESSION['project']['number'] .'&name='. $_SESSION['project']['name'] .'&selector=monthly" method="post" name="form" id="form">';
												echo '<select name="year" class="form_textbox" id="form_monthly_year" onchange="this.form.submit()">';
												if ($result->num_rows > 0) {
													while ($row = $result->fetch_assoc()) {
														if ($row['year'] == $monthly_year) {
															echo '<option value="'. $row['year'] .'" selected>'. $row['year'] .'</option>';
														} else {
															echo '<option value="'. $row['year'] .'">'. $row['year'] .'</option>';
														}
													}
												} else {
													echo '<option value=""></option>';
												}
												$conn->close();
												echo '</select>';
												echo '</form>';
												break;
								case "meeting" :echo '<h2>Mötesserier</h2>';
												break;
								default :	echo '<h2>null</h2>';
							}
						?>
					</div>
					<div class="right_column_content">
						<?php
							if (isset($_SESSION['selector'])) {
								$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
								if ($conn->connect_error) {
									die("Connection failed: " . $conn->connect_error);
								}
								mysqli_set_charset($conn,"utf8");
								
								if ($_SESSION['selector'] === "info") {
									$sql = "SELECT db_project.*, db_user.email, db_user.firstname, db_user.surname, db_user.company FROM db_project LEFT JOIN db_user ON db_project.client = db_user.email WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
									$result = $conn->query($sql);
									if ($result->num_rows == 1){
										$row = $result->fetch_assoc();
										$company = getCompanyInfo($row['company'], "name");
										$companynr = str_replace('-', '', $row['company']);
										if ($company !== false) {											
											echo '<div class="project_info">Datasamordnare: '. $row['firstname'] .' '. $row['surname'] .' <a target="_blank" href="https://www.allabolag.se/'. $companynr .'">'. $company .'</a></div>';
										} else {
											echo '<div class="project_info">Datasamordnare: '. $row['firstname'] .' '. $row['surname'] .' <a target="_blank" href="https://www.allabolag.se/'. $companynr .'">'. $row['company'] .'</a></div>';
										}
										echo 
											'<div class="project_info">Email: '. $row['email'] .'</div>
											<div class="project_info">Projektnummer: '. $row['number'] .'</div>
											<div class="project_info">Projektnamn: '. $row['name'] .'</div>
											<div class="project_info">Program: '. $row['program'] .'</div>
											<div class="project_info">Arbetsplats: '. $row['jobsite'] .'</div>
											<div class="project_info">Skapad: '. $row['created'] .'</div>';
									} else {
										echo '<div>Finns ingen projektinfo tillgänglig</div>';
									}
								} else if ($_SESSION['selector'] === "member") {
									$sql = "SELECT db_user.*, db_project_member.title AS title, db_project_member.permission AS permission FROM db_project_member LEFT JOIN db_user ON db_project_member.user = db_user.email WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
									$result = $conn->query($sql);
									
									echo '<div id="project_content_member_header" class="project_row project_row_odd">
											<div class="project_column project_member_column"><p><u>Namn</u></p></div>
											<div class="project_column project_member_column"><p><u>Företag</u></p></div>';
									if (isset($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] == true) {
										echo '<div class="project_column project_member_column"><p><u>Behörighet/Titel</u></p></div>';
									} else {
										echo '<div class="project_column project_member_column"><p><u>Titel</u></p></div>';
									}		
									echo 	'<div class="project_column project_member_column"><p><u>Email</u></p></div>
											<div class="project_column project_member_column"><p><u>Telefonnummer</u></p></div>
											<div class="project_column project_tools_column"><p></p></div>
										</div>';
									echo '<div id="project_content">';
									if ($result->num_rows > 0) {
										$rowcount = $result->num_rows;
										$count = 0;										
										$numbers = array();
										$names = array();
										
										while ($row = $result->fetch_assoc()) {
											if ($count % 2 == 0) {
												echo '<div id="project_content_row'. $count .'" class="project_row">';
											} else {
												echo '<div id="project_content_row'. $count .'" class="project_row project_row_odd">';
											}
											echo        '<div class="project_column project_member_column"><p>'. $row['firstname'] .' '. $row['surname'] .'</p></div>';
											
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
											echo        '<div class="project_column project_member_column"><p><a target="_blank" href="https://www.allabolag.se/'. $companynr .'">'. $company .'</a></p></div>';
											if (isset($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] == true) {
												$sql = "SELECT type FROM db_project_permission WHERE id='". $row['permission'] ."'";
												$permission_result = $conn->query($sql);
												$permission_row = $permission_result->fetch_assoc();
												echo    '<div class="project_column project_member_column"><p>'. $permission_row['type'] .' - '. $row['title'] .'</p></div>';
											} else {
												echo    '<div class="project_column project_member_column"><p>'. $row['title'] .'</p></div>';
											}
											echo        '<div class="project_column project_member_column"><p>'. $row['email'] .'</p></div>';
											echo        '<div class="project_column project_member_column"><p>'. $row['phonenumber'] .'</p></div>';
											echo        '<div class="project_column project_tools_column">';
											
											if ($_SESSION['user']['permission'] == 1 && $_SESSION['user']['email'] !== $row['email'] && $_SESSION['project']['client'] !== $row['email']) {
												echo        '<form method="post" onsubmit="return validateRemoveForm('. $count .')" class="project_tools_form" action="project_member_remove.php">
														        <input type="hidden" name="user" value="'. $row['email'] .'"/>
														        <input type="hidden" name="name" id="project_member_remove_form_name'. $count .'" value="'. $row['firstname'] .' '. $row['surname'] .'"/>
														        <input type="submit" class="link_button selection_button" value="Ta bort"/>
													        </form>
													        <div class="project_tools_form">
														        <a class="link_button selection_button" href="project_member_edit_page.php?user='. $row['email'] .'">Ändra</a>
													        </div>';
											}
											echo       '</div>';
											echo '</div>';
											$count++;
										}
										unset($numbers);
										unset($names);
									}
									echo '</div>';
								} else if ($_SESSION['selector'] === "diary") {
									echo
										'<div id="project_content_diary_header" class="project_row project_row_odd">
											<div class="project_column project_diary_column"><p><u>Skapad av</u></p></div>
											<div class="project_column project_diary_column"><p><u>Företag</u></p></div>
											<div class="project_column project_small_column"><p><u>Arbetsdag</u></p></div>
											<div class="project_column project_diary_column"><p><u>Arbetsplats</u></p></div>
											<div class="project_column project_small_column"><p><u>Datum</u></p></div>
											<div class="project_column project_diary_column"><p><u>Granskad av</u></p></div>
											<div class="project_column project_small_column"><p><u>Låst</u></p></div>
											<div class="project_column project_tools_column"><p></p></div>
										</div>';
									echo '<div id="project_content">';
									if ($_SESSION['user']['permission'] > 4) {
										$sql = "SELECT * FROM db_project_diary WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $_SESSION['user']['company'] ."'";
										$highprio = false;
									} else if ($_SESSION['user']['permission'] < 4) {
										$sql = "SELECT * FROM db_project_diary WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND locked>0";
										$highprio = true;
									}
									$result = $conn->query($sql);										
									if ($result->num_rows > 0) {
										$rowcount = $result->num_rows;
										$count = 0;									
										if (!$highprio) {
											$companynr = str_replace('-', '', $_SESSION['user']['company']);
											$company = getCompanyInfo($_SESSION['user']['company'], "name");
											if ($company === false) {
												$company = $_SESSION['user']['company'];
											}
										}
										$numbers = array();
										$names = array();
										
										while ($row = $result->fetch_assoc()) {
											if ($row['reviewer'] !== '') {
												$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['reviewer'] ."'";
												$reviewer_result = $conn->query($sql);
												$reviewer_row = $reviewer_result->fetch_assoc();
												
												$reviewer = ''. $reviewer_row['firstname'] .' '. $reviewer_row['surname'] .'';
												
												$sql = "SELECT permission FROM db_project_member WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND user='". $row['reviewer'] ."'";
											} else {
												$sql = "SELECT permission FROM db_project_member WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND user='". $row['author'] ."'";
											
												$reviewer = 'Ej Granskad';
											}
											$member_result = $conn->query($sql);
											$member_row = $member_result->fetch_assoc();
											
											$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['author'] ."'";
											$author_result = $conn->query($sql);
											$author_row = $author_result->fetch_assoc();
											
											if ($highprio) {
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
											}
											if ($count % 2 == 0) {
												echo '<div id="project_content_row'. $count .'" class="project_row">';
											} else {
												echo '<div id="project_content_row'. $count .'" class="project_row project_row_odd">';
											}
											if ($row['locked'] == 0) {
												$locked = 'Nej';
											} else if ($row['locked'] == 1) {
												$locked = 'Skickad';
											} else {
												$locked = 'Ja';
											}
											echo '<div class="project_column project_diary_column"><p>'. $author_row['firstname'] .' '. $author_row['surname'] .'</p></div>';
											echo '<div class="project_column project_diary_column"><p><a target="_blank" href="https://www.allabolag.se/'. $companynr .'">'. $company .'</a></p></div>';
											echo '<div class="project_column project_small_column"><p>'. $row['workday'] .'</p></div>';
											echo '<div class="project_column project_diary_column"><p>'. $row['jobsite'] .'</p></div>';
											echo '<div class="project_column project_small_column"><p>'. $row['date'] .'</p></div>';
											echo '<div class="project_column project_diary_column"><p>'. $reviewer .'</p></div>';
											echo '<div class="project_column project_small_column"><p>'. $locked .'</p></div>';
											echo '<div class="project_column project_tools_column">
													<div class="project_tools_form">';
											if ($_SESSION['user']['permission'] < 4) {
												if ($row['locked'] == 1) {
													echo '<a class="link_button selection_button" href="diary_edit_page.php?company='. $row['company'] .'&workday='. $row['workday'] .'">Granska</a>';
												} else {
													echo '<a class="link_button selection_button" href="diary_edit_page.php?company='. $row['company'] .'&workday='. $row['workday'] .'">Visa</a>';
												}
											} else if ($_SESSION['user']['permission'] == 5) {
												if ($row['locked'] == 0) {
													echo '<a class="link_button selection_button" href="diary_edit_page.php?company='. $row['company'] .'&workday='. $row['workday'] .'">Granska</a>';
												} else {
													echo '<a class="link_button selection_button" href="diary_edit_page.php?company='. $row['company'] .'&workday='. $row['workday'] .'">Visa</a>';
												}
											} else {
												if ($_SESSION['user']['permission'] < $member_row['permission'] && $row['locked'] == 0) {
													echo '<a class="link_button selection_button" href="diary_edit_page.php?company='. $row['company'] .'&workday='. $row['workday'] .'">Granska</a>';
												} else {
													echo '<a class="link_button selection_button" href="diary_edit_page.php?company='. $row['company'] .'&workday='. $row['workday'] .'">Visa</a>';
												}
											}
											echo 	'</div>
												</div>';
												
											echo '</div>';
											$count++;
										}
										unset($numbers);
										unset($names);
									} else {
										echo '<div id="project_content_row1" class="project_row">
												<div class="project_column project_diary_column"><p></p></div>
												<div class="project_column project_diary_column"><p></p></div>
												<div class="project_column project_small_column"><p></p></div>
												<div class="project_column project_diary_column"><p></p></div>
												<div class="project_column project_small_column"><p></p></div>
												<div class="project_column project_diary_column"><p></p></div>
												<div class="project_column project_small_column"><p></p></div>
												<div class="project_column project_tools_column">
													<div class="project_tools_form"></div>
												</div>
											</div>';
									}
									echo '</div>';
								} else if ($_SESSION['selector'] === "abnorm") {
									echo
										'<div id="project_content_abnorms_header" class="project_row project_row_odd">
											<div class="project_column project_number_column"><p><u>ID</u></p></div>
											<div class="project_column project_abnorm_column"><p><u>Företag</u></p></div>
											<div class="project_column project_abnorm_column"><p><u>Rubrik</u></p></div>
											<div class="project_column project_abnorm_column"><p><u>Plats</u></p></div>
											<div class="project_column project_small_column project_consequence_header"><p><u>Ekonomisk konsekvens</u></p></div>
											<div class="project_column project_small_column project_consequence_header"><p><u>Tids- konsekvens</u></p></div>
											<div class="project_column project_small_column"><p><u>Status</u></p></div>
											<div class="project_column project_small_column"><p><u>Låst</u></p></div>
											<div class="project_column project_tools_column"><p></p></div>
										</div>';
									echo '<div id="project_content">';
										
									if ($_SESSION['user']['permission'] < 4) {
										$sql = "SELECT a.* FROM db_project_abnormality a INNER JOIN (SELECT client, number, name, id, MAX(workday) workday, header, jobsite, comments, economic_consequence, time_consequence, status FROM db_project_abnormality WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND locked>0 GROUP BY client, number, name, id) b ON a.client = b.client AND a.number = b.number AND a.name = b.name AND a.id = b.id AND a.workday = b.workday";
									} else if ($_SESSION['user']['permission'] > 4) {
										$sql = "SELECT a.* FROM db_project_abnormality a INNER JOIN (SELECT client, number, name, id, MAX(workday) workday, header, jobsite, comments, economic_consequence, time_consequence, status FROM db_project_abnormality WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $_SESSION['user']['company'] ."' GROUP BY client, number, name, id) b ON a.client = b.client AND a.number = b.number AND a.name = b.name AND a.id = b.id AND a.workday = b.workday";
									}
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										$rowcount = $result->num_rows;
										$count = 0;
										$numbers = array();
										$names = array();
										
										while ($row = $result->fetch_assoc()) {
											$id = $row['id'];
											
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
											if ($row['locked'] == 0) {
												$locked = 'Nej';
											} else if ($row['locked'] == 1) {
												$locked = 'Skickad';
											} else {
												$locked = 'Ja';
											}
											if ($count % 2 == 0) {
												echo '<div id="project_content_row'. $count .'" class="project_row">';
											} else {
												echo '<div id="project_content_row'. $count .'" class="project_row project_row_odd">';
											}
											echo '<div class="project_column project_number_column"><p>'. $id .'</p></div>';
											echo '<div class="project_column project_abnorm_column"><p><a target="_blank" href="https://www.allabolag.se/'. $companynr .'">'. $company .'</a></p></div>';
											echo '<div class="project_column project_abnorm_column"><p>'. $row['header'] .'</p></div>';
											echo '<div class="project_column project_abnorm_column"><p>'. $row['jobsite'] .'</p></div>';
											
											if ($row['economic_consequence'] == 1) {
												$economic = 'Ja';
											} else {
												$economic = 'Nej';
											}
											if ($row['time_consequence'] == 1) {
												$time = 'Ja';
											} else {
												$time = 'Nej';
											}
											echo '<div class="project_column project_small_column"><p>'. $economic .'</p></div>';
											echo '<div class="project_column project_small_column"><p>'. $time .'</p></div>';
											
											if ($row['status'] <= $statusarraysize) {
												$status = $statustypes[$row['status'] - 1];
											} else {
												$status = '';
											}
											echo '<div class="project_column project_small_column"><p>'. $status .'</p></div>';
											echo '<div class="project_column project_small_column"><p>'. $locked .'</p></div>';
											echo '<div class="project_column project_tools_column">
													<div class="project_tools_form">
														<a class="link_button selection_button" href="abnormality_page.php?id='. $row['id'] .'">Visa</a>
													</div>
												</div>';
												
											echo '</div>';
											$count++;
										}
										unset($numbers);
										unset($names);
									} else {
										echo '<div id="project_content_row1" class="project_row project_last_row">
												<div class="project_column project_number_column"><p></p></div>
												<div class="project_column project_abnorm_column"><p></p></div>
												<div class="project_column project_small_column"><p></p></div>
												<div class="project_column project_abnorm_column"><p></p></div>
												<div class="project_column project_abnorm_column"><p></p></div>
												<div class="project_column project_small_column"><p></p></div>
												<div class="project_column project_small_column"><p></p></div>
												<div class="project_column project_small_column"><p></p></div>
												<div class="project_column project_tools_column">
													<div class="project_tools_form"></div>
												</div>
											</div>';
									}
									echo '</div>';
								} else if ($_SESSION['selector'] === "monthly") {
									echo
										'<div id="project_content_monthly_header" class="project_row project_row_odd">
											<div class="project_column project_small_column"><p><u>Månad</u></p></div>
											<div class="project_column project_diary_column"><p><u>Skapad av</u></p></div>
											<div class="project_column project_small_column"><p><u>Låst</u></p></div>
											<div class="project_column project_tools_column"><p></p></div>
										</div>';
									echo '<div id="project_content">';
									
									if ($_SESSION['user']['permission'] == 4) {
										$sql = "SELECT year, month, author, locked FROM db_project_monthly_report WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND author='". $_SESSION['user']['email'] ."' AND year='". $monthly_year ."'";
									} else if ($_SESSION['user']['permission'] < 4) {
										$sql = "SELECT year, month, author, locked FROM db_project_monthly_report WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND year='". $monthly_year ."' AND locked>0";
									}
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										$rowcount = $result->num_rows;
										$count = 0;
										
										while ($row = $result->fetch_assoc()) {
											$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['author'] ."'";
											$author_result = $conn->query($sql);
											$author_row = $author_result->fetch_assoc();
											
											$author = ''. $author_row['firstname'] .' '. $author_row['surname'] .'';
											
											if ($row['locked'] == 0) {
												$locked = 'Nej';
											} else if ($row['locked'] == 1) {
												$locked = 'Skickad';
											} else {
												$locked = 'Ja';
											}
											if ($count % 2 == 0) {
												echo '<div id="project_content_row'. $count .'" class="project_row">';
											} else {
												echo '<div id="project_content_row'. $count .'" class="project_row project_row_odd">';
											}
											echo '<div class="project_column project_small_column"><p>'. month($row['month']) .'</p></div>';
											echo '<div class="project_column project_diary_column"><p>'. $author .'</p></div>';
											echo '<div class="project_column project_small_column"><p>'. $locked .'</div>';
											echo '<div class="project_column project_tools_column">
													<div class="project_tools_form">';
														if ($_SESSION['user']['permission'] == 4 && $row['locked'] < 2) {
															echo '<a class="link_button selection_button" href="monthly_report_edit_page.php?author='. $row['author'] .'&year='. $row['year'] .'&month='. $row['month'] .'">Redigera</a>';
														} else {
															echo '<a class="link_button selection_button" href="monthly_report_edit_page.php?author='. $row['author'] .'&year='. $row['year'] .'&month='. $row['month'] .'">Visa</a>';
														}
											echo '	</div>
												</div>';
											echo '</div>';
											$count++;
										}
										
									}
									echo '</div>';
								} else if ($_SESSION['selector'] === "meeting") {
									echo
										'<div id="project_content_meeting_header" class="project_row project_row_odd">
											<div class="project_column project_number_column"><p><u>Nr</u></p></div>
											<div class="project_column project_small_column"><p><u>Skapad</u></p></div>
											<div class="project_column project_small_column"><p><u>Mötestyp</u></p></div>
											<div class="project_column project_large_column"><p><u>Rubrik</u></p></div>
											<div class="project_column project_diary_column"><p><u>Handläggare</u></p></div>
											<div class="project_column project_tools_column"><p></p></div>
										</div>';
									echo '<div id="project_content">';
									
									if ($_SESSION['user']['permission'] < 6) {
										$sql = "SELECT * FROM db_project_meeting_series WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
										$result = $conn->query($sql);
										if ($result->num_rows > 0) {
											$count = 0;
											
											$types = array();
											
											$sql = "SELECT * FROM db_project_meeting_type";
											$type_result = $conn->query($sql);
											if ($type_result->num_rows > 0) {
												while ($type_row = $type_result->fetch_assoc()) {
													$types[] = $type_row['type'];
												}
											}
											while ($row = $result->fetch_assoc()) {
												$sql = "SELECT meeting, id, email, present FROM db_project_meeting_present WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND series='". $row['id'] ."' AND email='". $_SESSION['user']['email'] ."'";
												$present_result = $conn->query($sql);
												if ($present_result->num_rows > 0) {
													$sql = "SELECT firstname, surname FROM db_user WHERE email='". $row['author'] ."'";
													$author_result = $conn->query($sql);
													$author_row = $author_result->fetch_assoc();
													
													$type = $types[$row['type'] - 1];
													
													if ($count % 2 == 0) {
														echo '<div id="project_content_row'. $count .'" class="project_row">';
													} else {
														echo '<div id="project_content_row'. $count .'" class="project_row project_row_odd">';
													}
													echo
														'<div class="project_column project_number_column"><p>'. $row['id'] .'</p></div>
														<div class="project_column project_small_column"><p>'. $row['date'] .'</p></div>
														<div class="project_column project_small_column"><p>'. $type .'</p></div>
														<div class="project_column project_large_column"><p>'. $row['header'] .'</p></div>
														<div class="project_column project_diary_column"><p>'. $author_row['firstname'] .' '. $author_row['surname'] .'</p></div>
														<div class="project_column project_tools_column">
															<div class="project_tools_form">
																<a class="link_button selection_button" href="meeting_page.php?series='. $row['id'] .'">Visa</a>';
													if ($row['author'] == $_SESSION['user']['email']) {
														echo
																'<a class="link_button selection_button" href="meeting_add_page.php?series='. $row['id'] .'">Lägg till möte</a>';
													}
													echo 	'</div>
														</div>';
													echo '</div>';
													$count++;
												}
											}
										}
									}
									echo '</div>';
								}
								$conn->close();
							}
						?>
					</div>
				</div>
            </div>

            <div class="footer"></div>

        </div> <!-- Wrapper End-->
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/basic_functions.js"></script>
		<script type="text/javascript" src="js/project_page.js"></script>
	</body>
</html>