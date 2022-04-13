<?php
	$server = 'misaw.se.mysql';
	session_start();
	include 'get_company_info.php';
    if(!empty($_POST['name']) && !empty($_POST['surname']) && !empty($_POST['phonenumber1']) && !empty($_POST['phonenumber2']) && !empty($_POST['email']) && !empty($_POST['company']) && !empty($_POST['title']) && !empty($_POST['permission'])) {

        $conn = new mysqli($server, "misaw_se", "kapeyAU6", "misaw_se");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
		mysqli_set_charset($conn,"utf8");
		
		// Protect from sql-injections and validations
		$ssnumber = stripslashes($_POST['ssnumber']);
		$ssnumber = $conn->real_escape_string($ssnumber);
		if (!preg_match('/^([0-9]{8})-([0-9]{4})$/', $ssnumber)) {
			$ssnumber = '';
		}
		$name = stripslashes($_POST['name']);
		$name = $conn->real_escape_string($name);
		$name = strtolower($name);
		if (!preg_match('/[a-zåäö]+$/i', $name)) {
			header("Location: project_member_new_page.php");
			//echo 'Förnamn';
		    //echo '<br>';
			$conn->close();
			die();
		}
		$name = ucfirst($name);
		
		$surname = stripslashes($_POST['surname']);
		$surname = $conn->real_escape_string($surname);
		$surname = strtolower($surname);
		if (!preg_match('/[a-zåäö]+$/i', $surname)) {
			header("Location: project_member_new_page.php");
			//echo 'Efternamn';
		    //echo '<br>';
			$conn->close();
			die();
		}
		$surname = ucfirst($surname);
		
		$number1 = stripslashes($_POST['phonenumber1']);
		$number1 = $conn->real_escape_string($number1);
		if (!preg_match('/^[0-9]+$/', $number1)) {
			header("Location: project_member_new_page.php");
			//echo 'Telefonnummer 1';
		    //echo '<br>';
			$conn->close();
			die();
		}		
		$number2 = stripslashes($_POST['phonenumber2']);
		$number2 = $conn->real_escape_string($number2);
		if (!preg_match('/^[0-9]+$/', $number2)) {
			header("Location: project_member_new_page.php");
			//echo 'Telefonnummer 2';
		    //echo '<br>';
			$conn->close();
			die();
		}		
		$number = $number1 . '-' . $number2;
		
		$email = stripslashes($_POST['email']);
		$email = $conn->real_escape_string($email);
		$email = strtolower($email);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			header("Location: project_member_new_page.php");
			//echo 'Email';
		    //echo '<br>';
			$conn->close();
			die();
		}
		$company = stripslashes($_POST['company']);
		$company = $conn->real_escape_string($company);
		if (!preg_match('/^([0-9]{6})-([0-9]{4})$/', $company)) {
			header("Location: project_member_new_page.php");
			//echo 'Företagsnummer';
		    //echo '<br>';
			$conn->close();
			die();
		} else {
			$companyname = getCompanyInfo($company, "name");
			if ($companyname === false) {
				header("Location: project_member_new_page.php");
				//echo 'Företagsnamn';
		        //echo '<br>';
				$conn->close();
				die();
			}
		}
		$title = stripslashes($_POST['title']);
		$title = $conn->real_escape_string($title);
		
		$permission = stripslashes($_POST['permission']);
		$permission = $conn->real_escape_string($permission);
		
		$pass = 'pass';
		
		$sql = "SELECT email FROM db_user WHERE email='". $_POST['email'] ."'";
		//echo $sql;
		//echo '<br>';
		$result = $conn->query($sql);
		if ($result->num_rows == 0) {
			$sql = "INSERT INTO db_user (email, ssnumber, firstname, surname, password, phonenumber, company, admin) VALUES ('". $email ."','". $ssnumber ."','". $name ."','". $surname ."','". $pass ."','". $number ."','". $company ."','0')";
			//echo $sql;
			//echo '<br>';
			if ($conn->query($sql)) {
				$sql = "INSERT INTO db_project_member (client, number, name, user, title, permission) VALUES ('". $_SESSION['project']['client'] ."','". $_SESSION['project']['number'] ."','". $_SESSION['project']['name'] ."','". $email ."','". $title ."','". $permission ."')";
				//echo $sql;
				//echo '<br>';
				if ($conn->query($sql)) {
					header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."&selector=member");
					$conn->close();
					die();
				}
			} else {
				header("Location: project_member_new_page.php");
				$conn->close();
				die();
			}
		} else {
			$sql = "SELECT user FROM db_project_member WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND user='". $_POST['email'] ."'";
			//echo $sql;
			//echo '<br>';
			$result = $conn->query($sql);
			if ($result->num_rows == 0) {
				$sql = "INSERT INTO db_project_member (client, number, name, user, title, permission) VALUES ('". $_SESSION['project']['client'] ."','". $_SESSION['project']['number'] ."','". $_SESSION['project']['name'] ."','". $email ."','". $title ."','". $permission ."')";
				//echo $sql;
				//echo '<br>';
				$conn->query($sql);				
			}
			header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."&selector=member");
		}
        $conn->close();
    } else {
        header("Location: project_member_new_page.php");
    }
?>