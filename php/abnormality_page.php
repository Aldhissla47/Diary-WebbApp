<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	}
	if (!isset($_SESSION['project']) || (isset($_SESSION['user']['permission']) && $_SESSION['user']['permission'] == 4)) {
		header("Location: index.php");
	}
	if (!empty($_GET['id'])) {
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		$sql = "SELECT a.* FROM db_project_abnormality a INNER JOIN (SELECT client, number, name, id, MAX(workday) workday, header, jobsite, comments, economic_consequence, time_consequence, status FROM db_project_abnormality WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND id='". $_GET['id'] ."' GROUP BY client, number, name, id) b ON a.client = b.client AND a.number = b.number AND a.name = b.name AND a.id = b.id AND a.workday = b.workday";
		$result = $conn->query($sql);
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			
			$client = $row['client'];
			$projectnr = $row['number'];
			$projectname = $row['name'];
			$id = $_GET['id'];
			$company = $row['company'];
			$header = $row['header'];
			$jobsite = $row['jobsite'];
			
			if ($row['economic_consequence'] == 0) {
				$economic = 'Nej';
			} else {
				$economic = 'Ja';
			}
			if ($row['time_consequence'] == 0) {
				$time = 'Nej';
			} else {
				$time = 'Ja';
			}			
			$sql = "SELECT * FROM db_project_diary_statustype";
			$statusresult = $conn->query($sql);
			if ($statusresult->num_rows > 0) {
				$status = $row['status'];
				while ($statusrow = $statusresult->fetch_assoc()) {
					if ($statusrow['index'] === $row['status']) {
						$status = $statusrow['type'];
					}
				}
			}
			if ($row['locked'] == 0) {
				$locked = 'Nej';
			} else if ($row['locked'] == 1) {
				$locked = 'Skickad';
			} else {
				$locked = 'Ja';
			}
			include 'get_company_info.php';
			$companyname = getCompanyInfo($company, "name");
			if ($companyname === false) {
				$companyname = $company;
			}
			$statusarr = array();
			$arraysize = 0;
			$sql = "SELECT * FROM db_project_diary_statustype";
			$statusresult = $conn->query($sql);
			if ($statusresult->num_rows > 0) {
				while ($statusrow = $statusresult->fetch_assoc()) {
					$statusarr[] = $statusrow['type'];
					$arraysize++;
				}
			}
		} else {
			$conn->close();
			//echo $sql;
			header("Location: index.php");
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

		<title> Avvikelse </title>

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
						<h2>Avvikelse</h2>
					</div>
					<div class="right_column_content">
						<div id="form">
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
										<h5>ID: </h5>
										<div>
											<input type="text" name="id" value="<?php echo $id; ?>" class="form_textbox" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Rubrik: </h5>
										<div>
											<input type="text" name="header" value="<?php echo $header; ?>" class="form_abnorms_comments" readonly/>
										</div>
									</div>
								</div>
									
								<div class="form_row">
									<div class="form_col">
										<h5>Arbetsplats: </h5>
										<div>
											<input type="text" name="jobsite" value="<?php echo $jobsite; ?>" class="form_textbox" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Status: </h5>
										<div>
											<input type="text" name="status" value="<?php echo $status; ?>" class="form_textbox" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Låst: </h5>
										<div>
											<input type="text" name="locked" value="<?php echo $locked; ?>" class="form_textbox" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Tidskonsekvens: </h5>
										<div>
											<input type="text" name="time" value="<?php echo $time; ?>" class="form_textbox" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Ekonomisk Konsekvens: </h5>
										<div>
											<input type="text" name="economic" value="<?php echo $economic; ?>" class="form_textbox" readonly/>
										</div>
									</div>
								</div>
							</div>

							<div class="form_row">
								<?php
									$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
									if ($conn->connect_error) {
										die("Connection failed: " . $conn->connect_error);
									}
									mysqli_set_charset($conn,"utf8");
									
									$sql = "SELECT db_project.*, db_user.email, db_user.firstname, db_user.surname, db_user.company FROM db_project LEFT JOIN db_user ON db_project.client = db_user.email WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
									
									if ($_SESSION['user']['permission'] < 4) {
										$sql = "SELECT * FROM db_project_abnormality WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND company='". $company ."' AND id='". $id ."' AND locked>0";
									} else if ($_SESSION['user']['permission'] > 4) {
										$sql = "SELECT * FROM db_project_abnormality WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND company='". $company ."' AND id='". $id ."'";
									}
									$result = $conn->query($sql);
									if ($result->num_rows > 0) {
										$abnormsrows = $result->num_rows;
										echo '<input type="hidden" name="abnormsrows" id="form_abnorms_count" value="'. $abnormsrows .'"/>';
										echo '<div id="form_abnorms_rows">';
										$count = 0;
										
										while ($row = $result->fetch_assoc()) {
											$workday = $row['workday'];
											
											$sql = "SELECT date FROM db_project_diary WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND company='". $company ."' AND workday='". $workday ."'";
											$dateresult = $conn->query($sql);
											if ($dateresult->num_rows == 1) {
												$daterow = $dateresult->fetch_assoc();
												$date = $daterow['date'];
											} else {
												$date = 'null';
											}
											echo '<div class="form_work_row">';
											echo '<div id="form_abnorms_rev_workday_title"><h5>Arbetsdag: </h5></div><div id="form_abnorms_rev_comments_title"><h5>Noteringar: </h5></div><div id="form_abnorms_status_title"><h5>Status: </h5></div><br>';
											echo '<input type="text" name="abnorms_workday'. $count .'" class="form_textbox" id="form_abnorms_workdaybox'. $count .'" value="'. $workday .'" readonly/><input type="text" name="abnorms_comments'. $count .'" class="form_abnorms_comments" id="form_abnorms_comment'. $count .'" value="'. $row['comments'] .'" readonly/><input type="text" name="abnorms_status'. $count .'" class="form_textbox form_work_status_textbox" id="form_abnorms_status'. $count .'" value="'. $statusarr[$row['status'] - 1] .'" readonly> '. $date .'<br>';

											$sql = "SELECT * FROM db_project_abnormality_crew WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND id='". $id ."' AND workday='". $workday ."'";
											$crewresult = $conn->query($sql);
											if ($crewresult->num_rows > 0) {
												$crewrows = $crewresult->num_rows;
												echo '<input type="hidden" name="abnormscrewrows'. $count .'" id="form_abnorms_crew_count'. $count .'" value="'. $crewrows .'"/>';
												echo '<div class="form_crew_rows" id="form_abnorms_crew_rows'. $count .'">';
												$totaltime = 0;
												
												while ($crewrow = $crewresult->fetch_assoc()) {
													$crewnr = $crewrow['crewid'];
													$crewid = $crewnr - 1;
													$totaltime = $totaltime + $crewrow['time'];
													
													echo '<div id="form_abnorms_crew_row'. $count .''. $crewid .'">';
													if ($crewnr == 1) {
														echo '<div>
																<div class="form_crew_show_title"><h5>Arbetsstyrka: </h5></div><div class="form_crew_show_title"><h5>Namn: </h5></div><div id="form_work_time_title"><h5>Tid: </h5></div>
															</div>';
													}
													echo ''. $crewnr .'. <input type="text" name="abnorms_crew_type'. $count .''. $crewid .'" class="form_textbox" id="form_abnorms_crew_type'. $count .''. $crewid .'" value="'. $crewrow['jobtype'] .'" readonly>';
													echo '<input type="text" name="abnorms_crew_name'. $count .''. $crewid .'" id="form_abnorms_crew_name'. $count .''. $crewid .'" class="form_textbox" value="'. $crewrow['fullname'] .'" readonly/><input type="text" name="abnorms_crew_time'. $count .''. $crewid .'" class="form_textbox form_crew_time_textbox" id="form_abnorms_crew_time'. $count .''. $crewid .'" value="'. number_format($crewrow['time'], 1) .'" readonly>';
													
													$own = $crewrow['own'];
													if ($own == 1) {
														echo 'Egen';
													} else {
														echo 'UE';
													}
													echo '</div>';
												}
												echo '</div>';
												echo '<div class="crew_total_time">Summa Timmar: <input type="text" id="form_abnorms_total_time'. $count .'" class="form_textbox form_borderless" value="'; if ($totaltime > 0) { echo number_format($totaltime, 1); } echo '" readonly/></div>';
											}
											echo '</div>';
											$count++;
										}
										echo '</div>';
									}
									$conn->close();
								?>
							</div>
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