<?php
	$server = 'misaw.se.mysql';
	$echo = false;
	
    if (!empty($_POST['taskcount']) && !empty($_POST['user'])) {

        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		$user = $_POST['user'];
		$taskcount = $_POST['taskcount'];
		$completed = date('Y-m-d');
		
		if ($echo) {
			echo 'Taskcount: ';
			echo $taskcount;
			echo '<br><br>';
		}
		$updatedtasks = 0;
		for ($i = 0; $i < $taskcount; $i++) {
			if (!empty($_POST['client'. $i .'']) && !empty($_POST['number'. $i .'']) && !empty($_POST['name'. $i .'']) && !empty($_POST['id'. $i .'']) && !empty($_POST['answer'. $i .''])) {
				$client = $_POST['client'. $i .''];
				$number = $_POST['number'. $i .''];
				$name = $_POST['name'. $i .''];
				$id = $_POST['id'. $i .''];
				
				if (!empty($_POST['private'. $i .''])) {
					$private = $_POST['private'. $i .''];
				} else {
					$private = 0;
				}
				// Protect from sql-injections
				$answer = stripslashes($_POST['answer'. $i .'']);
				str_replace('"', "'", $answer);
				$answer = $conn->real_escape_string($answer);
				
				$sql = "UPDATE db_project_task SET answer='". $answer ."', completed='". $completed ."', worker='". $user ."', private='". $private ."' WHERE client='". $client ."' AND number='". $number ."' AND name='". $name ."' AND id='". $id ."'";
				if ($echo) {
					echo $sql;
					echo '<br>';
				}
				if (!$conn->query($sql)) {
					if ($echo) {
						echo 'Update failed for task: '. $i .'<br>';
					}
				} else {
					$updatedtasks++;
				}
				if ($echo) {
					echo '<br>';
				}
			}
		}
		if ($echo) {
			echo '<br>';
			echo 'Updated tasks: ';
			echo $updatedtasks;
			echo '<br>';
		} else {
			header("Location: index.php");
		}
		$conn->close();
    } else {
		if (!$echo) {
			header("Location: index.php");
		} else {
			echo 'Taskcount: ';
			echo $_POST['taskcount'];
			echo '<br>';
			
			echo 'User: ';
			echo $_POST['user'];
			echo '<br>';
		}
    }
?>
