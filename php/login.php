<?php
	$server = 'misaw.se.mysql';
	if(!empty($_POST['email']) && !empty($_POST['password'])) {
		$conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
		if ($conn->connect_error) {
			die("Connection failed: " . $conn->connect_error);
		}
		mysqli_set_charset($conn,"utf8");
		
		// Protect from sql-injections
		$email = stripslashes($_POST['email']);
		$email = $conn->real_escape_string($email);
		$email = strtolower($email);
				
		$password = stripslashes($_POST['password']);
		$password = $conn->real_escape_string($password);

		$sql = "SELECT * FROM db_user WHERE email='". $email ."' and password='". $password ."'";
		$result = $conn->query($sql);

		if ($result->num_rows == 1){
			$row = $result->fetch_assoc();
			session_start();
			$_SESSION['loggedin'] = true;
			$_SESSION['user']['email'] = $email;
			$_SESSION['user']['company'] = $row['company'];
			if ($row['admin'] == 1) {
				$_SESSION['user']['isAdmin'] = true;
			}			
            header("Location: index.php");
		} else {
            header("Location: login_page.php");
        }
        $conn->close();
	}
	else {
		header("Location: login_page.php");
	}
?>