<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	}
	if (!empty($_GET['author']) &&!empty($_GET['year']) && !empty($_GET['month'])) {
		if (!isset($_SESSION['project']) || (isset($_SESSION['user']['permission']) && $_SESSION['user']['permission'] > 4)) {
			header("Location: index.php");
		}
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		$sql = "SELECT * FROM db_project_monthly_report WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND author='". $_GET['author'] ."' AND year='". $_GET['year'] ."' AND month='". $_GET['month'] ."'";
		$result = $conn->query($sql);
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			
			$sql = "SELECT email, ssnumber, firstname, surname, phonenumber FROM db_user WHERE email='". $row['author'] ."'";
			$user_result = $conn->query($sql);
			if ($result->num_rows == 1) {
				$user_row = $user_result->fetch_assoc();
				$user = $user_row['email'];
				$ssnumber = $user_row['ssnumber'];
				$username = "". $user_row['firstname'] ." ". $user_row['surname'] ."";
				$phonenumber = $user_row['phonenumber'];
			} else {
				$conn->close();
				die();
				header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."");
			}
			$sql = "SELECT program FROM db_project WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
			$program_result = $conn->query($sql);
			$program_row = $program_result->fetch_assoc();		
			$program = $program_row['program'];
			
			$client = $row['client'];
			$projectnr = $row['number'];
			$projectname = $row['name'];			
			$company = $row['company'];
			$author = $row['author'];
			$supervisor = $row['supervisor'];
			$role = $row['role'];
			
			$locked = $row['locked'];
			
			$year = $row['year'];
			$month = $row['month'];
			
			include 'get_company_info.php';
			$companyname = getCompanyInfo($company, "name");
			if ($companyname === false) {
				$companyname = $company;
			}
			$reviewer = false;
			if ($locked < 2 && $_SESSION['user']['permission'] == 4) {
				$reviewer = true;
			}
		} else {
			$conn->close();
			header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."");
		}
		$conn->close();
	} else {
		header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."");
	}
	
	function month($arg1) {
		$month = array('Januari', 'Februari', 'Mars', 'April', 'Maj', 'Juni', 'Juli', 'Augusti', 'September', 'Oktober', 'November', 'December');
		$i = $arg1 - 1;
		return $month[$i];
	}
	function weekday($arg1) {
		$day = array('Mån', 'Tis', 'Ons', 'Tor', 'Fre', 'Lör', 'Sön');
		$i = $arg1 - 1;
		return $day[$i];
	}
	function setTextColorRed($weekday, $month, $day) {
		if ($weekday == 7 || ($month == 1 && $day == 1) || ($month == 12 && ($day == 25 || $day == 26))) {
			return 'red';
		} else {
			return 'black';
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Formulär </title>

		<link rel="stylesheet" type="text/css" href="css/_main.css">
		<link rel="stylesheet" type="text/css" href="css/monthly_report_page.css">
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
						<h2>Månadsformulär</h2>
					</div>
					<div class="right_column_content">
						<form action="monthly_report_edit.php" onsubmit="return validateForm()" method="post" name="edit_form" id="form">
							<input type="hidden" name="client" value="<?php echo $client; ?>"/>
							<div class="form_info">
								<div class="form_row">
									<div class="form_col">
										<h5>Skapare: </h5>
										<div>
											<input type="hidden" name="author" value="<?php echo $author; ?>"/>
											<input type="text" name="username" value="<?php echo $username; ?>" class="form_textbox" id="form_username" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Telefonnummer: </h5>
										<div>
											<input type="text" name="phonenumber" value="<?php echo $phonenumber; ?>"  class="form_textbox" id="form_phonenumber" readonly/>
										</div>
									</div>
									
									<?php
										if ($ssnumber != '') {
											echo '<div class="form_col">
													<h5>Personnummer: </h5>
													<div>
														<input type="text" name="ssnumber" value="'. $ssnumber .'"  class="form_textbox" id="form_ssnumber" readonly/>
													</div>
												</div>';
										}
									?>
									
									<div class="form_col">
										<h5>Företag: </h5>
										<div>
											<input type="hidden" name="company" value="<?php echo $company; ?>" id="form_company"/>
											<input type="text" name="companyname" value="<?php echo $companyname; ?>" class="form_textbox" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Roll: </h5>
										<div>
											<input type="text" name="role" value="<?php echo $role; ?>"  class="form_textbox" id="form_role" readonly/>
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
									
									<?php
										if ($program != '') {
											echo '<div class="form_col">
													<h5>Program: </h5>
													<div>
														<input type="text" name="program" value="'. $program .'"  class="form_textbox" id="form_program" readonly/>
													</div>
												</div>';
										}
									?>
									
									<div class="form_col">
										<h5>År: </h5>
										<div>
											<input type="text" name="year" value="<?php echo $year; ?>" class="form_textbox" id="form_year" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Månad: </h5>
										<div>
											<input type="hidden" name="month" value="<?php echo $month; ?>" id="form_month"/>
											<input type="text" name="monthname" value="<?php echo month($month); ?>" class="form_textbox" id="form_monthname" readonly/>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Beställare: </h5><?php if ($reviewer) { echo '<p>*</p>'; } ?>
										<div>
											<input type="text" name="supervisor" class="form_textbox" id="form_supervisor" value="<?php echo $supervisor; ?>" maxlength="80" onchange="isLetterOrSpaceKey(this)" <?php if (!$reviewer) { echo 'readonly'; } ?>/>
										</div>
									</div>
								</div>
							</div>
							
							<div class="form_row">
								<input type="hidden" name="daycount" id="form_month_day_count" value="<?php echo cal_days_in_month(CAL_GREGORIAN, $month, $year); ?>"/>
								<?php
									if ($reviewer) {
										echo '<div class="form_day_title"><h5>Dag: </h5></div><div class="form_work_title"><h5>Utfört arbete/aktivitet: </h5><p>*</p></div><div class="form_inline"><h5>Tid (tim): </h5><p>*</p></div><br>';
									} else {
										echo '<div class="form_day_title"><h5>Dag: </h5></div><div class="form_work_title_show"><h5>Utfört arbete/aktivitet: </h5></div><div class="form_inline"><h5>Tid (tim): </h5></div><br>';
									}
								?>
								<div id="form_month_rows">
									<?php
										$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
										if ($conn->connect_error) {
											die("Connection failed: " . $conn->connect_error);
										}
										mysqli_set_charset($conn,"utf8");

										$sql = "SELECT * FROM db_project_monthly_report_day WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND author='". $author ."' AND year='". $year ."' AND month='". $month ."'";
										$result = $conn->query($sql);
										$total_time = 0;
										if ($result->num_rows > 0) {
											$count = 0;
											$day = 1;
											$maxcount = cal_days_in_month(CAL_GREGORIAN, $month, $year);
											
											if ($reviewer) {
												while ($row = $result->fetch_assoc()) {
													$weekday = date('N', strtotime(''. $year .'-'. $month .'-'. $day .''));
													
													if ($day == $row['day']) {
														echo '<div id="form_month_row'. $count .'">';
														echo '<input type="text" name="month_day'. $count .'" class="form_textbox form_day_textbox" id="form_month_day'. $count .'" value="'. $day .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_dayname'. $count .'" value="'. weekday($weekday) .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" name="month_job'. $count .'" class="form_textbox form_work_textbox" id="form_month_job'. $count .'" maxlength="255" value="'. $row['job'] .'"/>';
														echo '<input type="text" name="month_time'. $count .'" class="form_textbox form_time_textbox" id="form_month_time'. $count .'" value="'. number_format($row['time'], 1) .'" onchange="return updateTotalTime()" onkeypress="return isFloatNumberKey(event)" maxlength="10"/>';
														if ($weekday == 1) {
															$ddate = ''. $year .'-'. $month .'-'. $day .'';
															$date = new DateTime($ddate);
															$week = intval($date->format("W"));
															echo '<input type="text" value="v. '. $week .'" class="form_textbox form_borderless" id="form_week'. $count .'" readonly/>';
														}
														echo '</div>';
														$total_time = $total_time + $row['time'];
													} else {
														for ($i = $day; $i < $row['day']; $i++, $count++, $day++) {
															$weekday = date('N', strtotime(''. $year .'-'. $month .'-'. $day .''));
															echo '<div id="form_month_row'. $count .'">';
															echo '<input type="text" name="month_day'. $count .'" class="form_textbox form_day_textbox" id="form_month_day'. $count .'" value="'. $day .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
															echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_dayname'. $count .'" value="'. weekday($weekday) .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
															echo '<input type="text" name="month_job'. $count .'" class="form_textbox form_work_textbox" id="form_month_job'. $count .'" maxlength="255"/>';
															echo '<input type="text" name="month_time'. $count .'" class="form_textbox form_time_textbox" id="form_month_time'. $count .'" onchange="return updateTotalTime()" onkeypress="return isFloatNumberKey(event)" maxlength="10"/>';
															if ($weekday == 1) {
																$ddate = ''. $year .'-'. $month .'-'. $day .'';
																$date = new DateTime($ddate);
																$week = intval($date->format("W"));
																echo '<input type="text" value="v. '. $week .'" class="form_textbox form_borderless" id="form_week'. $count .'" readonly/>';
															}
															echo '</div>';
														}
														$weekday = date('N', strtotime(''. $year .'-'. $month .'-'. $day .''));
														echo '<div id="form_month_row'. $count .'">';
														echo '<input type="text" name="month_day'. $count .'" class="form_textbox form_day_textbox" id="form_month_day'. $count .'" value="'. $day .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_dayname'. $count .'" value="'. weekday($weekday) .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" name="month_job'. $count .'" class="form_textbox form_work_textbox" id="form_month_job'. $count .'" maxlength="255" value="'. $row['job'] .'"/>';
														echo '<input type="text" name="month_time'. $count .'" class="form_textbox form_time_textbox" id="form_month_time'. $count .'" value="'. number_format($row['time'], 1) .'" onchange="return updateTotalTime()" onkeypress="return isFloatNumberKey(event)" maxlength="10"/>';
														if ($weekday == 1) {
															$ddate = ''. $year .'-'. $month .'-'. $day .'';
															$date = new DateTime($ddate);
															$week = intval($date->format("W"));
															echo '<input type="text" value="v. '. $week .'" class="form_textbox form_borderless" id="form_week'. $count .'" readonly/>';
														}
														echo '</div>';
														$total_time = $total_time + $row['time'];
													}
													$count++;
													$day++;
												}
												if ($count != $maxcount) {
													for ($i = $count; $i < $maxcount; $i++, $day++, $count++) {
														$weekday = date('N', strtotime(''. $year .'-'. $month .'-'. $day .''));
														echo '<div id="form_month_row'. $count .'">';
														echo '<input type="text" name="month_day'. $count .'" class="form_textbox form_day_textbox" id="form_month_day'. $count .'" value="'. $day .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_dayname'. $count .'" value="'. weekday($weekday) .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" name="month_job'. $count .'" class="form_textbox form_work_textbox" id="form_month_job'. $count .'" maxlength="255"/>';
														echo '<input type="text" name="month_time'. $count .'" class="form_textbox form_time_textbox" id="form_month_time'. $count .'" onchange="return updateTotalTime()" onkeypress="return isFloatNumberKey(event)" maxlength="10"/>';
														if ($weekday == 1) {
															$ddate = ''. $year .'-'. $month .'-'. $day .'';
															$date = new DateTime($ddate);
															$week = intval($date->format("W"));
															echo '<input type="text" value="v. '. $week .'" class="form_textbox form_borderless" id="form_week'. $count .'" readonly/>';
														}
														echo '</div>';
													}
												}
											} else { // If NOT reviewer
												while ($row = $result->fetch_assoc()) {
													$weekday = date('N', strtotime(''. $year .'-'. $month .'-'. $day .''));
													
													if ($day == $row['day']) {
														echo '<div id="form_month_row'. $count .'">';
														echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_day'. $count .'" value="'. $day .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_dayname'. $count .'" value="'. weekday($weekday) .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" class="form_textbox form_work_textbox" id="form_month_job'. $count .'" value="'. $row['job'] .'" readonly/>';
														echo '<input type="text" class="form_textbox form_time_textbox" id="form_month_time'. $count .'" value="'. number_format($row['time'], 1) .'" readonly/>';
														if ($weekday == 1) {
															$ddate = ''. $year .'-'. $month .'-'. $day .'';
															$date = new DateTime($ddate);
															$week = intval($date->format("W"));
															echo '<input type="text" value="v. '. $week .'" class="form_textbox form_borderless" id="form_week'. $count .'" readonly/>';
														}
														echo '</div>';
														$total_time = $total_time + $row['time'];
													} else {
														for ($i = $day; $i < $row['day']; $i++, $count++, $day++) {
															$weekday = date('N', strtotime(''. $year .'-'. $month .'-'. $day .''));
															echo '<div id="form_month_row'. $count .'">';
															echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_day'. $count .'" value="'. $day .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
															echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_dayname'. $count .'" value="'. weekday($weekday) .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
															echo '<input type="text" class="form_textbox form_work_textbox" id="form_month_job'. $count .'" readonly/>';
															echo '<input type="text" class="form_textbox form_time_textbox" id="form_month_time'. $count .'" readonly/>';
															if ($weekday == 1) {
																$ddate = ''. $year .'-'. $month .'-'. $day .'';
																$date = new DateTime($ddate);
																$week = intval($date->format("W"));
																echo '<input type="text" value="v. '. $week .'" class="form_textbox form_borderless" id="form_week'. $count .'" readonly/>';
															}
															echo '</div>';
														}
														$weekday = date('N', strtotime(''. $year .'-'. $month .'-'. $day .''));
														echo '<div id="form_month_row'. $count .'">';
														echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_day'. $count .'" value="'. $day .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_dayname'. $count .'" value="'. weekday($weekday) .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" class="form_textbox form_work_textbox" id="form_month_job'. $count .'" value="'. $row['job'] .'" readonly/>';
														echo '<input type="text" class="form_textbox form_time_textbox" id="form_month_time'. $count .'" value="'. number_format($row['time'], 1) .'" readonly/>';
														if ($weekday == 1) {
															$ddate = ''. $year .'-'. $month .'-'. $day .'';
															$date = new DateTime($ddate);
															$week = intval($date->format("W"));
															echo '<input type="text" value="v. '. $week .'" class="form_textbox form_borderless" id="form_week'. $count .'" readonly/>';
														}
														echo '</div>';
														$total_time = $total_time + $row['time'];
													}
													$count++;
													$day++;
												}
												if ($count != $maxcount) {
													for ($i = $count; $i < $maxcount; $i++, $day++, $count++) {
														$weekday = date('N', strtotime(''. $year .'-'. $month .'-'. $day .''));
														echo '<div id="form_month_row'. $count .'">';
														echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_day'. $count .'" value="'. $day .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_dayname'. $count .'" value="'. weekday($weekday) .'" style="color: '. setTextColorRed($weekday, $month, $day) .';" readonly/>';
														echo '<input type="text" class="form_textbox form_work_textbox" id="form_month_job'. $count .'" readonly/>';
														echo '<input type="text" class="form_textbox form_time_textbox" id="form_month_time'. $count .'" readonly/>';
														if ($weekday == 1) {
															$ddate = ''. $year .'-'. $month .'-'. $day .'';
															$date = new DateTime($ddate);
															$week = intval($date->format("W"));
															echo '<input type="text" value="v. '. $week .'" class="form_textbox form_borderless" id="form_week'. $count .'" readonly/>';
														}
														echo '</div>';
													}
												}
											}
										}
										$conn->close();
									?>
								</div>
								<?php
									echo '<div>Summa Timmar: <input type="text" name="total_time" id="form_total_time" class="form_textbox form_borderless" value="'. number_format($total_time, 1) .'" readonly/></div>';
								?>
							</div>
							
							<div id="form_send_button_div">
								<?php
									if ($reviewer) {
										echo '<input type="hidden" name="send" value="no" id="form_send_button_value"/>';
										if ($locked != 1) {
											echo '<input type="submit" value="Spara" id="form_send_button" onclick="updateSend(0)"/>';
											echo '<input type="submit" value="Spara och skicka" id="form_send_button" onclick="updateSend(1)"/>';
										} else {
											echo '<input type="submit" value="Spara" id="form_send_button" onclick="updateSend(1)"/>';
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
		<script type="text/javascript" src="js/monthly_report_page.js"></script>
	</body>
</html>