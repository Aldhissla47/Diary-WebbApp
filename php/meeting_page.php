<?php
	$server = 'misaw.se.mysql';
	session_start();
	if (!isset($_SESSION['loggedin']) && $_SESSION['loggedin'] != true) {		
		header("Location: login_page.php");
	}
	if (!isset($_SESSION['project']) || (isset($_SESSION['user']['permission']) && $_SESSION['user']['permission'] > 5)) {
		header("Location: index.php");
	}
	if (!empty($_GET['series'])) {
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		$sql = "SELECT * FROM db_project_meeting_series WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND id='". $_GET['series'] ."'";
		$result = $conn->query($sql);
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			
			$client = $row['client'];
			$projectnr = $row['number'];
			$projectname = $row['name'];
			
			$series = $_GET['series'];
			$author = $row['author'];
			$date = $row['date'];
			$header = $row['header'];
			
			$types = array();								
			$sql = "SELECT * FROM db_project_meeting_type";
			$type_result = $conn->query($sql);
			if ($type_result->num_rows > 0) {
				while ($type_row = $type_result->fetch_assoc()) {
					$types[] = $type_row['type'];
				}
			}
			$type = $types[$row['type'] - 1];
			
			$sql = "SELECT firstname, surname FROM db_user WHERE email='". $author ."'";
			$userresult = $conn->query($sql);
			$userrow = $userresult->fetch_assoc();
			$authorname = ''. $userrow['firstname'] .' '. $userrow['surname'] .'';
			
			$sql = "SELECT meeting, id, email FROM db_project_meeting_present WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."' AND email='". $_SESSION['user']['email'] ."'";
			$presentresult = $conn->query($sql);
			if ($presentresult->num_rows > 0) {
				$meetings = array();
				
				while ($presentrow = $presentresult->fetch_assoc()) {
					$meetings[] = $presentrow['meeting'];
				}
			} else {
				header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."");
			}
		} else {
			header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."");
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

		<title> Mötesserie </title>

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
						<h2>Mötesserie nr <?php echo $series; ?></h2>
					</div>
					<div class="right_column_content">
						<div id="form">
							<div class="form_row">
								<div class="form_col">
									<h5>Skapad: </h5>
									<div>
										<input type="text" name="date" value="<?php echo $date; ?>" class="form_textbox_small" readonly/>
									</div>
								</div>
								
								<div class="form_col">
									<h5>Handläggare: </h5>
									<div>
										<input type="hidden" name="author" value="<?php echo $author; ?>" id="form_author"/>
										<input type="text" name="authorname" value="<?php echo $authorname; ?>" class="form_textbox" readonly/>
									</div>
								</div>
								
								<div class="form_col">
									<h5>Email: </h5>
									<div>
										<input type="text" name="email" value="<?php echo $author; ?>" class="form_textbox" readonly/>
									</div>
								</div>
							</div>
								
							<div class="form_row">
								<div class="form_col">
									<h5>Mötestyp: </h5>
									<div>
										<input type="text" name="type" value="<?php echo $type; ?>" class="form_textbox_small" readonly/>
									</div>
								</div>
								
								<div class="form_col">
									<h5>Rubrik: </h5>
									<div>
										<input type="text" name="header" value="<?php echo $header; ?>" class="form_textbox_large" readonly/>
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
								
								echo
									'<div id="meeting_content_header" class="meeting_row meeting_row_odd">
										<div class="meeting_column meeting_small_column"><p><u>Möte</u></p></div>
										<div class="meeting_column meeting_small_column"><p><u>Datum</u></p></div>
										<div class="meeting_column meeting_large_column"><p><u>Plats</u></p></div>
										<div class="meeting_column meeting_small_column"><p><u>Tid1</u></p></div>
										<div class="meeting_column meeting_small_column"><p><u>Tid2</u></p></div>
										<div class="meeting_column meeting_small_column"><p><u>Låst</u></p></div>';
								if ($author == $_SESSION['user']['email']) {
									echo '<div class="meeting_column meeting_tools_column"><p><a class="link_button selection_button" href="meeting_add_page.php?series='. $series .'" style="margin: 0;">Lägg till möte</a></p></div>';
								} else {
									echo '<div class="meeting_column meeting_tools_column"><p></p></div>';
								}
								echo '</div>';
								echo '<div id="meeting_content">';
								
								$sql = "SELECT id, author, date, time, time2, jobsite, locked FROM db_project_meeting_protocol WHERE client='". $client ."' AND number='". $projectnr ."' AND name='". $projectname ."' AND series='". $series ."'";
								$result = $conn->query($sql);
								if ($result->num_rows > 0) {
									$count = 0;
									
									while ($row = $result->fetch_assoc()) {
										$i = array_search($row['id'], $meetings);
										if ($i !== false) {
											if ($row['locked'] == 0) {
												$locked = 'Nej';
											} else if ($row['locked'] == 1) {
												$locked = 'Skickad';
											} else {
												$locked = 'Ja';
											}
											if ($count % 2 == 0) {
												echo '<div id="meeting_content_row'. $count .'" class="meeting_row">';
											} else {
												echo '<div id="meeting_content_row'. $count .'" class="meeting_row meeting_row_odd">';
											}
											echo
												'<div class="meeting_column meeting_small_column"><p>'. $row['id'] .'</p></div>
												<div class="meeting_column meeting_small_column"><p>'. $row['date'] .'</p></div>
												<div class="meeting_column meeting_large_column"><p>'. $row['jobsite'] .'</p></div>
												<div class="meeting_column meeting_small_column"><p>'. $row['time'] .'</p></div>
												<div class="meeting_column meeting_small_column"><p>'. $row['time2'] .'</p></div>
												<div class="meeting_column meeting_small_column"><p>'. $locked .'</p></div>
												<div class="meeting_column meeting_tools_column">
													<div class="meeting_tools_form">
														<a class="link_button selection_button" href="meeting_edit_page.php?series='. $series .'&id='. $row['id'] .'">Visa</a>
													</div>
												</div>';
											echo '</div>';
											$count++;
										}
									}
								}
								echo '</div>';
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