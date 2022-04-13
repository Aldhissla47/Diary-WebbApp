<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	} else {
		if (!isset($_SESSION['user']['isAdmin']) || !isset($_SESSION['project']) || !isset($_SESSION['user']['permission']) || $_SESSION['user']['isAdmin'] === false || $_SESSION['user']['permission'] != 1) {
			header("Location: index.php");
		}
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Projekt </title>

		<link rel="stylesheet" type="text/css" href="css/_main.css">
		<link rel="stylesheet" type="text/css" href="css/project_member_page.css">
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
						<h2>Ny Projektmedlem</h2>
					</div>
					<div class="right_column_content">
						<form action="project_member_new.php" onsubmit="return validateForm()" method="post" name="form" id="form">
							<h5>Personnummer: </h5>
							<div class="row">
								<input type="text" name="ssnumber" class="form_textbox" maxlength="13" placeholder="ååååmmdd-xxxx" onkeypress="return isHyphenOrNumberKey(event)"/>
							</div>
							<h5>Förnamn: </h5><p>*</p>
							<div class="row">
								<input type="text" name="name" class="form_textbox" maxlength="20" onchange="isLetterKey(this)"/>
							</div>
							<h5>Efternamn: </h5><p>*</p>
							<div class="row">
								<input type="text" name="surname" class="form_textbox" maxlength="20" onchange="isLetterKey(this)"/>
							</div>
							<h5>Telefonnummer: </h5><p>*</p>
							<div class="row">
								<input type="text" name="phonenumber1" class="form_textbox" id="numbox1" maxlength="3" placeholder="070" onkeypress="return isNumberKey(event)"/>-
								<input type="text" name="phonenumber2" class="form_textbox" id="numbox2" maxlength="7" placeholder="1234567" onkeypress="return isNumberKey(event)"/>
							</div>
							<h5>Emailadress: </h5><p>*</p>
							<div class="row">
								<input type="text" name="email" class="form_textbox" maxlength="255" placeholder="email@email.com"/>
							</div>
							<h5>Företag: (Organisationsnummer) </h5><p>*</p>
							<div class="row">
								<input type="text" name="company" id="form_company" class="form_textbox" maxlength="11" placeholder="123456-1234" onkeypress="return isHyphenOrNumberKey(event)" onchange="return validateCompanyNumber()"/>
								<input type="text" name="companyname" id="form_company_name" class="form_textbox" readonly/>
							</div>
							<h5>Titel: </h5><p>*</p>
							<div class="row">
								<select name="title" class="form_textbox">
									<?php
										$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
										if ($conn->connect_error) {
											die("Connection failed: " . $conn->connect_error);
										}
										mysqli_set_charset($conn,"utf8");
										$sql = "SELECT * FROM db_project_member_title";
										$result = $conn->query($sql);
										if ($result->num_rows > 0){
											while ($row = $result->fetch_assoc()) {
												$title = mb_substr($row['title'], 0, null);
												echo '<option value="'. $title .'">'. $title .'</option>';
											}
										} else {
											echo '0 results';
										}
										$conn->close();
									?>
								</select>
							</div>
							<h5>Behörighet: </h5><p>*</p>
							<div class="row">
								<select name="permission" class="form_textbox">
									<?php
										$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
										if ($conn->connect_error) {
											die("Connection failed: " . $conn->connect_error);
										}
										mysqli_set_charset($conn,"utf8");
										$sql = "SELECT * FROM db_project_permission";
										$result = $conn->query($sql);
										if ($result->num_rows > 0) {
											while ($row = $result->fetch_assoc()) {
												$type = mb_substr($row['type'], 0, null);
												echo '<option value="'. $row['id'] .'">'. $type .'</option>';
											}
										} else {
											echo '0 results';
										}
										$conn->close();
									?>
								</select>
							</div>
							<div>
								<input type="submit" value="Lägg Till"/>
							</div>
						</form>
					</div>
				</div>
            </div>

            <div class="footer"></div>
			
        </div> <!-- Wrapper End-->
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script type="text/javascript" src="js/basic_functions.js"></script>
		<script type="text/javascript" src="js/project_member_new_page.js"></script>
	</body>
</html>