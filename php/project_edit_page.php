<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	} else {
		if (!isset($_SESSION['user']['isAdmin']) || $_SESSION['user']['isAdmin'] === false || !isset($_SESSION['project'])) {
			header("Location: index.php");
		}
	}
	$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}
	mysqli_set_charset($conn,"utf8");
	$sql = "SELECT program, jobsite FROM db_project WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
	$result = $conn->query($sql);
	if ($result->num_rows == 1) {
		$row = $result->fetch_assoc();
		$program = $row['program'];
		$jobsite = $row['jobsite'];
	} else {
		header("Location: index.php");
	}
	$conn->close();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Projekt </title>

		<link rel="stylesheet" type="text/css" href="css/_main.css">
		<link rel="stylesheet" type="text/css" href="css/project_new_page.css">
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
						<h2>Redigera Projekt</h2>
					</div>
					<div class="right_column_content">
						<form action="project_edit.php" onsubmit="return validateForm()" method="post" name="form" id="project_form">						
							<h5>Arbetsplats: </h5><p>*</p>
							<div class="row">
								<input type="text" name="jobsite" class="form_textbox" value="<?php echo $jobsite; ?>" maxlength="40"/>
							</div>
							<h5>Program: </h5>
							<div class="row">
								<input type="text" name="program" class="form_textbox" value="<?php echo $program; ?>" maxlength="60"/>
							</div>
							<div>
								<input type="submit" value="Spara"/>
							</div>
						</form>
					</div>
				</div>
            </div>

            <div class="footer"></div>
			
        </div> <!-- Wrapper End-->
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/basic_functions.js"></script>
	</body>
</html>