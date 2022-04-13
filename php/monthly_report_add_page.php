<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	}
	if (!isset($_SESSION['project']) || (isset($_SESSION['user']['permission']) && $_SESSION['user']['permission'] != 4)) {
		header("Location: index.php");
	}
	$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	mysqli_set_charset($conn,"utf8"); 
	$sql = "SELECT db_user.email, db_user.ssnumber, db_user.firstname, db_user.surname, db_user.phonenumber, db_user.company, db_project_member.title FROM db_project_member LEFT JOIN db_user ON db_project_member.user = db_user.email WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND user='". $_SESSION['user']['email'] ."'";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$user = $row['email'];
		$ssnumber = $row['ssnumber'];
		$username = "". $row['firstname'] ." ". $row['surname'] ."";
		$phonenumber = $row['phonenumber'];
		$company = $row['company'];
		$role = $row['title'];
		
		$sql = "SELECT program FROM db_project WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
		$program_result = $conn->query($sql);
		$program_row = $program_result->fetch_assoc();		
		$program = $program_row['program'];
		
		$client = $_SESSION['project']['client'];
		$projectnr = $_SESSION['project']['number'];
		$projectname = $_SESSION['project']['name'];
		
		$month = (int)date('m');
		$year = date('Y');
		
		include 'get_company_info.php';
		$companyname = getCompanyInfo($company, "name");
		if ($companyname === false) {
			$companyname = $company;
		}
	} else {
		$conn->close();
		header("Location: index.php");
	}
	$conn->close();
	
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
						<form action="monthly_report_add.php" onsubmit="return validateForm()" method="post" name="add_form" id="form">
							<input type="hidden" name="client" value="<?php echo $client; ?>"/>
							<div class="form_info">
								<div class="form_row">
									<div class="form_col">
										<h5>Skapare: </h5>
										<div>
											<input type="hidden" name="author" value="<?php echo $user; ?>"/>
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
										<h5>År: </h5><p>*</p>
										<div id="form_year_div">
											<select name="year" class="form_textbox" id="form_year" onchange="updateSelectMonth(this.value); validateDate();">
											<?php
												echo '<option value="'. $year .'" selected>'. $year .'</option>';
												for ($i = $year - 1; $i >= 2010; $i--) {
													echo '<option value="'. $i .'">'. $i .'</option>';
												}
											?>
											</select>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Månad: </h5><p>*</p>
										<div id="form_month_div">
											<select name="month" class="form_textbox" id="form_month" onchange="updateMonthRows(); validateDate();">
											<?php
												$max = (int)date('m');
												for ($i = 1; $i <= $max; $i++) {
													if ($i == $month) {
														echo '<option value="'. $i .'" selected>'. month($i) .'</option>';
													} else {
														echo '<option value="'. $i .'">'. month($i) .'</option>';
													}
												}
											?>
											</select>
										</div>
									</div>
									
									<div class="form_col">
										<h5>Beställare: </h5><p>*</p>
										<div>
											<input type="text" name="supervisor" class="form_textbox" id="form_supervisor" maxlength="80" onchange="isLetterOrSpaceKey(this)"/>
										</div>
									</div>
								</div>
							</div>
							
							<div class="form_row">
								<input type="hidden" name="daycount" id="form_month_day_count" value="<?php echo cal_days_in_month(CAL_GREGORIAN, $month, $year); ?>"/>
								<div class="form_day_title"><h5>Dag: </h5></div><div class="form_work_title"><h5>Utfört arbete/aktivitet: </h5><p>*</p></div><div class="form_inline"><h5>Tid (tim): </h5><p>*</p></div><br>
								<div id="form_month_rows">
									<?php
										for ($i = 0; $i < cal_days_in_month(CAL_GREGORIAN, $month, $year); $i++) {
											$weekday = date('N', strtotime(''. $year .'-'. $month .'-'. ($i + 1) .''));
											
											echo '<div id="form_month_row'. $i .'">';
											echo '<input type="text" name="month_day'. $i .'" class="form_textbox form_day_textbox" id="form_month_day'. $i .'" value="'. ($i + 1) .'" style="color: '. setTextColorRed($weekday, $month, ($i + 1)) .';" readonly/>';
											echo '<input type="text" class="form_textbox form_day_textbox" id="form_month_dayname'. $i .'" value="'. weekday($weekday) .'" style="color: '. setTextColorRed($weekday, $month, ($i + 1)) .';" readonly/>';
											echo '<input type="text" name="month_job'. $i .'" class="form_textbox form_work_textbox" id="form_month_job'. $i .'" maxlength="255"/>';
											echo '<input type="text" name="month_time'. $i .'" class="form_textbox form_time_textbox" id="form_month_time'. $i .'" onchange="return updateTotalTime()" onkeypress="return isFloatNumberKey(event)" maxlength="10"/>';
											if ($weekday == 1) {
												$ddate = ''. $year .'-'. $month .'-'. ($i + 1) .'';
												$date = new DateTime($ddate);
												$week = intval($date->format("W"));
												echo '<input type="text" value="v. '. $week .'" class="form_textbox form_borderless" id="form_week'. $i .'" readonly/>';
											} else {
												echo '<input type="text" value="" class="form_textbox form_borderless" id="form_week'. $i .'" readonly/>';
											}
											echo '</div>';
										}
									?>
								</div>
								<div>Summa Timmar: <input type="text" name="total_time" id="form_total_time" class="form_textbox form_borderless" readonly/></div>
							</div>
							
							<div id="form_send_button_div">
								<input type="hidden" name="send" value="no" id="form_send_button_value"/>
								<?php
									echo '<input type="submit" value="Spara" id="form_send_button" onclick="updateSend(0)"/>';
									echo '<input type="submit" value="Spara och skicka" id="form_send_button" onclick="updateSend(1)"/>';
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