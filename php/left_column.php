<div class="left_column_content">
	<?php
		$server = 'misaw.se.mysql';
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		if (isset($_SESSION['project'])) {
			echo '<div class="left_column_projects">
					Meny
				</div>';
	
			echo
				'<div class="left_column_project_menu" id="left_column_project_info">
					<a class="link_button selection_button" href="project_page.php?client='. $_SESSION['project']['client'] .'&number='. $_SESSION['project']['number'] .'&name='. $_SESSION['project']['name'] .'&selector=info">Projektinfo</a>
				</div>';
				
			$sql = "SELECT * FROM db_project_member WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."'";
			$result = $conn->query($sql);
			echo
				'<div class="left_column_project_menu" id="left_column_project_members">
					<a class="link_button selection_button" href="project_page.php?client='. $_SESSION['project']['client'] .'&number='. $_SESSION['project']['number'] .'&name='. $_SESSION['project']['name'] .'&selector=member">Projektmedlemmar ('. $result->num_rows .')</a>
				</div>';
			
			if ($_SESSION['user']['permission'] < 6) {
				echo
					'<div class="left_column_project_menu" id="left_column_project_meeting">
						<a class="link_button selection_button" href="project_page.php?client='. $_SESSION['project']['client'] .'&number='. $_SESSION['project']['number'] .'&name='. $_SESSION['project']['name'] .'&selector=meeting">Mötesserier</a>
					</div>';
					
				if ($_SESSION['user']['permission'] < 5) {
					echo
						'<div class="left_column_project_menu" id="left_column_project_monthly_reports">
							<a class="link_button selection_button" href="project_page.php?client='. $_SESSION['project']['client'] .'&number='. $_SESSION['project']['number'] .'&name='. $_SESSION['project']['name'] .'&selector=monthly">Månadsrapporter</a>
						</div>';
					echo
						'<div class="left_column_project_menu" id="left_column_project_tasks">
							<a class="link_button selection_button" href="task_page.php">Uppgiftslista</a>
						</div>';
				}
				if ($_SESSION['user']['permission'] != 4) {
					if ($_SESSION['user']['permission'] < 4) {
						$sql = "SELECT * FROM db_project_diary WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND locked>0";
					} else if ($_SESSION['user']['permission'] > 4) {
						$sql = "SELECT * FROM db_project_diary WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $_SESSION['user']['company'] ."'";
					}
					$result = $conn->query($sql);
					echo
						'<div class="left_column_project_menu" id="left_column_project_diaries">
							<a class="link_button selection_button" href="project_page.php?client='. $_SESSION['project']['client'] .'&number='. $_SESSION['project']['number'] .'&name='. $_SESSION['project']['name'] .'&selector=diary">Dagböcker ('. $result->num_rows .')</a>
						</div>';
					
					if ($_SESSION['user']['permission'] < 4) {
						$sql = "SELECT a.* FROM db_project_abnormality a INNER JOIN (SELECT client, number, name, id, MAX(workday) workday, header, jobsite, comments, economic_consequence, time_consequence, status FROM db_project_abnormality WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND locked>0 GROUP BY client, number, name, id) b ON a.client = b.client AND a.number = b.number AND a.name = b.name AND a.id = b.id AND a.workday = b.workday";
					} else if ($_SESSION['user']['permission'] > 4) {
						$sql = "SELECT a.* FROM db_project_abnormality a INNER JOIN (SELECT client, number, name, id, MAX(workday) workday, header, jobsite, comments, economic_consequence, time_consequence, status FROM db_project_abnormality WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND company='". $_SESSION['user']['company'] ."' GROUP BY client, number, name, id) b ON a.client = b.client AND a.number = b.number AND a.name = b.name AND a.id = b.id AND a.workday = b.workday";
					}
					$result = $conn->query($sql);
					echo
						'<div class="left_column_project_menu" id="left_column_project_abnorms">
							<a class="link_button selection_button" href="project_page.php?client='. $_SESSION['project']['client'] .'&number='. $_SESSION['project']['number'] .'&name='. $_SESSION['project']['name'] .'&selector=abnorm">Avvikelser ('. $result->num_rows .')</a>
						</div>';
				}
			}
		} else { // If !isset($_SESSION['project'])
			echo '<div class="left_column_projects">
					Projekt
				</div>';

			$sql = "SELECT client, number, name FROM db_project_member WHERE user='". $_SESSION['user']['email'] ."'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				while ($row = $result->fetch_assoc()) {
					echo '<div class="left_column_project_name">';
					echo '<a class="link_button" href="project_page.php?client='. $row['client'] .'&number='. $row['number'] .'&name='. $row['name'] .'">'. $row['name'] .'</a>';
					echo '</div>';
				}
			}
		}
		$conn->close();
	?>
</div>
