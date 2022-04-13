<?php
	$server = 'localhost';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	} else {
		if (!isset($_SESSION['user']['isAdmin']) || $_SESSION['user']['isAdmin'] === false) {
			header("Location: index.php");
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Nytt Projekt </title>

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
						<h2>Nytt Projekt</h2>
					</div>
					<div class="right_column_content">
						<form action="project_new.php" onsubmit="return validateForm()" method="post" name="form" id="project_form">						
							<h5>Projektnummer: </h5><p>*</p>
							<div class="row">
								<input type="text" name="number" class="form_textbox" maxlength="20"/>
							</div>
							<h5>Projektnamn: </h5><p>*</p>
							<div class="row">
								<input type="text" name="name" class="form_textbox"maxlength="30"/>
							</div>
							<h5>Arbetsplats: </h5><p>*</p>
							<div class="row">
								<input type="text" name="jobsite" class="form_textbox" maxlength="40"/>
							</div>
							<h5>Program: </h5>
							<div class="row">
								<input type="text" name="program" class="form_textbox" maxlength="60"/>
							</div>
							<div>
								<input type="submit" value="Skapa"/>
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