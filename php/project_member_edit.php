<?php
	$server = 'misaw.se.mysql';
	session_start();
	include 'get_company_info.php';
    if(!empty($_POST['name']) && !empty($_POST['surname']) && !empty($_POST['phonenumber1']) && !empty($_POST['phonenumber2']) && !empty($_POST['oldemail']) && !empty($_POST['email']) && !empty($_POST['company']) && !empty($_POST['title']) && !empty($_POST['permission'])) {

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
		if (!preg_match('/^[a-zåäö]+$/', $name)) {
			header("Location: project_member_new_page.php");
			$conn->close();
			die();
		}
		$name = ucfirst($name);
		
		$surname = stripslashes($_POST['surname']);
		$surname = $conn->real_escape_string($surname);
		$surname = strtolower($surname);
		if (!preg_match('/^[a-zåäö]+$/', $surname)) {
			header("Location: project_member_new_page.php");
			$conn->close();
			die();
		}
		$surname = ucfirst($surname);

		$number1 = stripslashes($_POST['phonenumber1']);
		$number1 = $conn->real_escape_string($number1);
		if (!preg_match('/^[0-9]+$/', $number1)) {
			header("Location: project_member_new_page.php");
			$conn->close();
			die();
		}		
		$number2 = stripslashes($_POST['phonenumber2']);
		$number2 = $conn->real_escape_string($number2);
		if (!preg_match('/^[0-9]+$/', $number2)) {
			header("Location: project_member_new_page.php");
			$conn->close();
			die();
		}		
		$number = $number1 . '-' . $number2;
		
		$oldemail = $_POST['oldemail'];
		
		$email = stripslashes($_POST['email']);
		$email = $conn->real_escape_string($email);
		$email = strtolower($email);
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			header("Location: project_member_new_page.php");
			$conn->close();
			die();
		}
		$company = stripslashes($_POST['company']);
		$company = $conn->real_escape_string($company);
		if (!preg_match('/^([0-9]{6})-([0-9]{4})$/', $company)) {
			header("Location: project_member_new_page.php");
			$conn->close();
			die();
		} else {
			$companyname = getCompanyInfo($company, "name");
			if ($companyname === false) {
				header("Location: project_member_new_page.php");
				$conn->close();
				die();
			}
		}
		$title = stripslashes($_POST['title']);
		$title = $conn->real_escape_string($title);
		
		$permission = stripslashes($_POST['permission']);
		$permission = $conn->real_escape_string($permission);
		
		$sql = "UPDATE db_user SET email='". $email ."', ssnumber='". $ssnumber ."', firstname='". $name ."', surname='". $surname ."', phonenumber='". $number ."', company='". $company ."' WHERE email='". $oldemail ."'";
		//echo $sql;
		//echo '<br>';
		
		$result = $conn->query($sql);
		if ($result->num_rows == 0) {			
			$sql = "UPDATE db_project_member SET user='". $email ."', title='". $title ."', permission='". $permission ."' WHERE client='". $_SESSION['project']['client'] ."' AND number='". $_SESSION['project']['number'] ."' AND name='". $_SESSION['project']['name'] ."' AND user='". $oldemail ."'";
			//echo $sql;
			//echo '<br>';
			
			if ($conn->query($sql)) {
				header("Location: project_page.php?client=". $_SESSION['project']['client'] ."&number=". $_SESSION['project']['number'] ."&name=". $_SESSION['project']['name'] ."&selector=member");
			} else {
				header("Location: project_member_edit_page.php");
			}
		} else {
			header("Location: project_member_edit_page.php");
		}
        $conn->close();
    } else {
        header("Location: project_member_edit_page.php");
    }
?>