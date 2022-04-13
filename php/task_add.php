<?php
	$server = 'misaw.se.mysql';
	$echo = false;
	
    if (!empty($_POST['client']) && !empty($_POST['number']) && !empty($_POST['name']) && !empty($_POST['user']) && !empty($_POST['taskcount'])) {

        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		$client = $_POST['client'];
        $number = $_POST['number'];
        $name = $_POST['name'];
		$user = $_POST['user'];
		$taskcount = $_POST['taskcount'];
		
		if ($echo) {
			echo 'Taskcount: ';
			echo $taskcount;
			echo '<br><br>';
		}
		$id = '';
		$category = '';
		$created = date('Y-m-d');
		$question = '';
		$supervisor = '';
		$author = $user;
		$deadline = '';
		$answer = '';
		$completed = '';
		$worker = '';
		$private = 0;
		
		$sql = "SELECT MAX(id) AS id FROM db_project_task WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."'";
		$max_id_result = $conn->query($sql);
		$max_id_row = $max_id_result->fetch_assoc();
		if (!empty($max_id_row['id'])) {							
			$maxid = $max_id_row['id'];
		} else {
			$maxid = 1;
		}
		$insertedtasks = 0;
		for ($i = 0; $i < $taskcount; $i++) {
			if (!empty($_POST['id'. $i .''])) {
				// Protect from sql-injections
				$id = $_POST['id'. $i .''];
				if ($id > $maxid) {
					if ($id - $maxid > 1) {
						$id = $maxid + 1;
					}
				}
				if (!empty($_POST['category'. $i .''])) {
					$category = stripslashes($_POST['category'. $i .'']);
					str_replace('"', "'", $category);
					$category = $conn->real_escape_string($category);
					$category = ucfirst($category);
				} else {
					$category = '';
				}
				if (!empty($_POST['question'. $i .''])) {
					$question = stripslashes($_POST['question'. $i .'']);
					str_replace('"', "'", $question);
					$question = $conn->real_escape_string($question);
					$question = ucfirst($question);
				} else {
					$question = '';
				}
				$supervisor = array();
				
				for ($j = 0; $j < 3; $j++) {
					if (!empty($_POST['supervisor'. $i .''. $j .''])) {
						// Protect from sql-injections
						$super = stripslashes($_POST['supervisor'. $i .''. $j .'']);
						$super = $conn->real_escape_string($super);
						
						$supervisor[$j] = $super;
					} else {
						$supervisor[$j] = '';
					}
				}
				if (!empty($_POST['deadline'. $i .''])) {
					$deadline = stripslashes($_POST['deadline'. $i .'']);
					$deadline = $conn->real_escape_string($deadline);
				} else {
					$deadline = '';
				}
				if (!empty($_POST['answer'. $i .''])) {
					$answer = stripslashes($_POST['answer'. $i .'']);
					str_replace('"', "'", $answer);
					$answer = $conn->real_escape_string($answer);
					$answer = ucfirst($answer);
					
					$completed = date('Y-m-d');
					$worker = $user;
				} else {
					$answer = '';
					$completed = '';
					$worker = '';
				}
				if (!empty($_POST['private'. $i .''])) {
					$private = $_POST['private'. $i .''];
				} else {
					$private = 0;
				}
				$sql = "SELECT * FROM db_project_task WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND id='". $id ."'";
				if ($echo) {
					echo $sql;
					echo '<br>';
				}
				$result = $conn->query($sql);
				if ($result->num_rows == 1) {
					if ($answer != '') {
						$sql = "UPDATE db_project_task SET answer='". $answer ."', completed='". $completed ."', worker='". $worker ."', supervisor1='". $supervisor[0] ."', supervisor2='". $supervisor[1] ."', supervisor3='". $supervisor[2] ."', private='". $private ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND id='". $id ."'";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						if (!$conn->query($sql)) {
							if ($echo) {
								echo 'Update break!<br>';
							}
							break 1;
						}
						$insertedtasks++;
						if ($echo) {
							echo '<br>';
						}
					}
				} else {
					if ($category != '' && $question != '' && $supervisor != '') {
						$sql = "INSERT INTO db_project_task (client, number, name, id, category, created, question, supervisor1, supervisor2, supervisor3, author, deadline, answer, completed, worker, private) VALUES ('". $client ."','". $number ."','". $name ."','". $id ."','". $category ."','". $created ."','". $question ."','". $supervisor[0] ."','". $supervisor[1] ."','". $supervisor[2] ."','". $author ."',". ($deadline == '' ? "NULL" : "'". $deadline ."'") .",'". $answer ."',". ($completed == '' ? "NULL" : "'". $completed ."'") .",'". $worker ."',". $private .")";
						if ($echo) {
							echo $sql;
							echo '<br>';
						}
						if (!$conn->query($sql)) {
							if ($echo) {
								echo 'Insert break!<br>';
							}
							break 1;
						}
						$insertedtasks++;
						$maxid++;
						if ($echo) {
							echo '<br>';
						}
					}
				}
			} else {
				if ($echo) {
					echo 'Id break!<br>';
				}
				break 1;
			}
		}
		if ($echo) {
			echo '<br>';
			echo 'Inserted/updated tasks: ';
			echo $insertedtasks;
			echo '<br>';
		} else {
			header("Location: task_page.php");
		}
		$conn->close();
    } else {
		if (!$echo) {
			header("Location: task_page.php");
		} else {
			echo 'Client: ';
			echo $_POST['client'];
			echo '<br>';
			
			echo 'Number: ';
			echo $_POST['number'];
			echo '<br>';
			
			echo 'Name: ';
			echo $_POST['name'];
			echo '<br>';
			
			echo 'User: ';
			echo $_POST['user'];
			echo '<br>';
			
			echo 'Taskcount: ';
			echo $_POST['taskcount'];
			echo '<br>';
		}
    }
?>
