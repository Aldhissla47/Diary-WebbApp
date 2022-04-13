<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width" , initial-scale=1 />

		<title> Login </title>

		<link rel="stylesheet" type="text/css" href="css/_main.css">
		<link rel="stylesheet" type="text/css" href="css/login_page.css">
	</head>
	
	<body>
        <div class="wrapper"> <!--Wrapper Start-->	
            <div class="header"></div>

			<div class="content">
				<div class="left_column" style="background-color: white;"></div>
				
				<div class="right_column">
					<div class="right_column_header">
						<h2>Logga In</h2>
					</div>					
					<div class="right_column_content">
						<form method="post" action="login.php">
							<div id="email">
								<p>Email:</p>
								<input type="text" name="email" id="email_box" />
							</div>
							<div id="password">
								<p>Password:</p>
								<input type="password" name="password" id="password_box" />
							</div>
							<input type="submit" id="login_button" value="Login" />
						</form>
					</div>
				</div>                    
			</div>

            <div class="footer"></div>
			
        </div> <!-- Wrapper End-->
	</body>
</html>