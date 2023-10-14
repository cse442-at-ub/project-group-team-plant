<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Plant</title>
    <link rel="stylesheet" type="text/css" href="Front-end/styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

<?php
	$server = "oceanus.cse.buffalo.edu";
	$user = "aidannas";
	$pass = "50136076";
	$dbname = "cse442_2023_fall_team_k_db";

	$conn = new mysqli($server, $user, $pass, $dbname);

	if ($conn->connection_error) {
		die("Connection failed: " . $conn->connection_error);
	}

	if(array_key_exists("change_username", $_POST)) {
		change_username($conn);
	}else if(array_key_exists("change_password", $_POST)) {
		change_password($conn);
	}else if(array_key_exists("set_zip", $_POST)) {
		set_zip($conn);
	}else if(array_key_exists("sign_out", $_POST)) {
		sign_out($conn);
	}else {
		if(isset($_COOKIE['username'])){
			if(verify_cookie($conn, $_COOKIE['username'], $_COOKIE['password'], $_COOKIE['auth'])){
				echo "Signed in as " . $_COOKIE['username'];
				login_account($conn,$_COOKIE['username'],$_COOKIE['password']);
			}else{
				echo "Invalid authentication cookie.";
				setcookie ("username", $username, time()-(60*60), '/');
				setcookie ("password", $password, time()-(60*60), '/');
				setcookie ("auth", $password, time()-(60*60), '/');
			}
		}
	}

  function change_username($conn){
    $username = strval($_POST['current_user']);
		$password = strval($_POST['current_pass']);
    $newusername = strval($_POST['new_user']);

    if ($newusername == ""){
			echo "Invalid input, please enter a different username.";
			return;
    }

    if ($newusername == $username){
  		echo "Invalid input, please enter a different username.";
  		return;
    }

    $stmt = $conn->prepare("UPDATE accounts SET username=? WHERE username=?");
		$stmt->bind_param("ss", $newusername, $username);
		$stmt->execute();
    $stmt->close();
    login_account($conn, $newusername, $password);
    header('Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k//accsettingspage.php');
  }

  function change_password($conn){
    $username = strval($_POST['current_user']);
	$password = strval($_POST['current_pass']);
    $newpassword = strval($_POST['new_pass']);

    if ($newpassword == ""){
			echo "Invalid input, please enter a different password.";
			return;
    }
	
	$stmt = $conn->prepare("SELECT * FROM accounts WHERE username=?");
	$stmt->bind_param("s", $username);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	if($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$username = $row["username"];
			$hash_pass = $row["password"];
		}
		if(password_verify(strval($password), strval($hash_pass))){
			//CORRECT PASSWORD
			$hashedpassword = strval(password_hash($newpassword, PASSWORD_DEFAULT));
			$stmt = $conn->prepare("UPDATE accounts SET password=? WHERE username=?");
			$stmt->bind_param("ss", $hashedpassword, $username);
			$stmt->execute();
			$stmt->close();
			login_account($conn, $username, $newpassword);
		} else {
			echo "Password is incorrect.";
		}
	} else {
		echo "Username not found.";
	}
    //header('Refresh:0; Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k//accsettingspage.php');
  }

  function set_zip($conn){
    $zipcode = strval($_POST['new_zip']);
	if(isset($_COOKIE['username'])){
		if(verify_cookie($conn, $_COOKIE['username'], $_COOKIE['password'], $_COOKIE['auth'])){
			//LOGGED IN
			if(strlen($zipcode) != 5 OR !is_numeric($zipcode)){
				echo "Invalid zip code. Please try again.";
				return;
			}
			$username = $_COOKIE['username'];
			$password = $_COOKIE['password'];
			$stmt = $conn->prepare("UPDATE accounts SET zipcode=? WHERE username=?");
			$stmt->bind_param("ss", $zipcode, $username);
			$stmt->execute();
			$stmt->close();
			login_account($conn, $username, $password);
			echo "Zipcode " . $zipcode . " saved.";
		}
	}else{
			//NOT LOGGED IN
			echo "NOT SIGNED IN";
	}
  }

  function sign_out($conn){
	setcookie("username", "", time()-3600, '/');
	setcookie("password", "", time()-3600, '/');
	setcookie("auth", "", time()-3600, '/');
	header('Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/login_page.php');
	echo "Signed out of account.";
	}

  function login_account($conn,$username,$password){
		setcookie("username", $username, time()+(60*60), '/');
		setcookie("password", $password, time()+(60*60), '/');
	}

	function verify_cookie($conn, $username, $password, $auth){
		$stmt = $conn->prepare("SELECT * FROM accounts WHERE username=?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$input_user = $row["username"];
				$hash_pass = $row["password"];
				$table_auth = $row["auth"];
			}
			if(password_verify(strval($password), strval($hash_pass)) AND $table_auth == $auth){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
		return false;
		$stmt->close();
	}

	$conn->close();
	?>


<div class="bg-image"></div> <!-- Background image -->

<div class="button-bar"> <!-- Button Bar -->
	<div class="logo">
		<img src="Front-end/images/logo.jpg" alt="Team Plant Logo">
		<span>Team Plant</span>
	</div>
	<nav>
		<ul>
			<li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/home_page.php">Home</a></li>
			<li><a href="">About</a></li>
			<li><a href="">My Favorites</a></li>
			<li><a href=""><b>Account</b></a></li>
		</ul>
	</nav>

	<div class="content">
		<div class="textbox">
			<h1>Account Settings</h1>
			<p>Change your profile and account settings</p>
		</div>

		<div class="settings">
			<div class="green-box">

				<h2>Change Username</h2>
				<form method="post">
					
					<div class="input-container">
						<label>Current Username</label>
						<input type="text" placeholder="Enter your current username" name="current_user">
					</div>
					<div class="input-container">
						<label>Current Password</label>
						<input type="password" placeholder="Enter your current password" name="current_pass">
					</div>
					<div class="input-container">
						<label>New Username</label>
						<input type="text" placeholder="Enter your new username" name="new_user">
					</div>
					<div class="input-container">
						<button type="submit" class="save-button" name="change_username">Change Username</button>
					</div>
				</form>
			</div>

			<div class="green-box">

				<h2>Change Password</h2>
				<form method="post">
					
					<div class="input-container">
						<label>Current Username</label>
						<input type="text" placeholder="Enter your current username" name="current_user">
					</div>
					<div class="input-container">
						<label>Current Password</label>
						<input type="password" placeholder="Enter your current password" name="current_pass">
					</div>
					<div class="input-container">
						<label>New Password</label>
						<input type="password" placeholder="Enter your new password" name="new_pass">
					</div>
					<div class="input-container">
						<button type="submit" class="save-button" name="change_password">Change Password</button>
					</div>
				</form>
			</div>

			<div class="green-box">

				<h2>Change Zipcode</h2>
				<form method="post">
					
					<div class="input-container">
						<label>Enter ZIP Code</label>
						<input type="text" placeholder="Enter your current ZIP code" name="new_zip">
					</div>
					<div class="input-container">
						<button type="submit" class="save-button" name="set_zip">Set ZIP Code</button>
					</div>
				</form>
				
			</div>

			<div class="green-box">

				<h2>Sign Out Here</h2>
				<form method="post">
					
					<div class="input-container">
						<button type="submit" class="save-button" name="sign_out">Sign Out</button>
					</div>
				</form>
			</div>

		</div>

	</div>

</div>

</body>
</html>