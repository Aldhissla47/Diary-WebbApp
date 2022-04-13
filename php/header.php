<?php
	$server = 'misaw.se.mysql';
?>
<div class="header_home" onclick="homebutton()">
	<div class="header_home_text">Min Översikt</div>
</div>

<div class="header_projects">
	<?php
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		if (isset($_SESSION['project'])) {
			echo '<button onclick="header_projects_dropdown()" class="header_dropbutton">Projekt ('. $_SESSION['project']['name'] .')<i class="arrow down"></i></button>';
		} else {
			echo '<button onclick="header_projects_dropdown()" class="header_dropbutton">Projekt <i class="arrow down"></i></button>';
		}
		echo '<div class="header_dropdown_content" id="header_projects_dropdown_content">';
	
		$sql = "SELECT client, number, name FROM db_project_member WHERE user='". $_SESSION['user']['email'] ."'";
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			while ($row = $result->fetch_assoc()) {
				echo '<a href="project_page.php?client='. $row['client'] .'&number='. $row['number'] .'&name='. $row['name'] .'">'. $row['name'] .'</a>';
			}
		} else {
			echo 'Inga Projekt';
		}
		echo '</div>';
	?>
</div>

<div class="header_tools">
	<button onclick="header_tools_dropdown()" class="header_dropbutton">Verktyg<i class="arrow down"></i></button>
	<div class="header_dropdown_content" id="header_tools_dropdown_content">
		<?php
			if (isset($_SESSION['user']['isAdmin']) && $_SESSION['user']['isAdmin'] === true) {
				echo '<a href="project_new_page.php">Skapa projekt</a>';
			}
			if (isset($_SESSION['project'])) {
				$sql = "SELECT permission FROM db_project_member WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND user='". $_SESSION['user']['email'] ."'";
				$result = $conn->query($sql);
				if ($result->num_rows == 1) {
					$row = $result->fetch_assoc();
					if ($row['permission'] < 5) {
						if ($row['permission'] < 4) {
							if ($row['permission'] == 1) {
								echo '<a href="project_member_new_page.php">Lägg till projektmedlem</a>';
							}
							echo '<a href="meeting_add_page.php">Skapa ny mötesserie</a>';
						} else {
							echo '<a href="monthly_report_add_page.php">Skapa ny månadsrapport</a>';
						}
						echo '<a href="task_page.php#bottomOfThePage">Lägg till ny uppgift</a>';
					} else {
						echo '<a href="diary_add_page.php">Skapa dagboksinlägg</a>';
					}
				}
			}
		?>
	</div>
</div>

<div class="header_profile_icon">
	<?php
		if(isset($_SESSION['user'])) {
			$sql = "SELECT firstname, surname FROM db_user WHERE email='". $_SESSION['user']['email'] ."'";
			$result = $conn->query($sql);

			if ($result->num_rows == 1){
				$row = $result->fetch_assoc();
				
				$fname = mb_substr($row['firstname'], 0, 1);
				$sname = mb_substr($row['surname'], 0, 1);
				
				echo '<p><b>'. $fname .''. $sname .'</b></p>';
			} else {
				echo 'NULL';
			}
		}
		$conn->close();
	?>
</div>

<div class="header_logout">
	<form method="post" action="logout.php" style="height: 100%;">
		<input type="submit" class="header_logout_button" value="Logga ut"/>
	</form>
</div>
