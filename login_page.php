<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Plant</title>
    <link rel="stylesheet" type="text/css" href="styles_login.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
<?php
	$server = "oceanus.cse.buffalo.edu";
	$user = "sepalutr";
	$pass = "50338448";
	$dbname = "cse442_2023_fall_team_k_db";
	
	$conn = new mysqli($server, $user, $pass, $dbname);
	
	if ($conn->connection_error) {
		die("Connection failed: " . $conn->connection_error);
	}
	
	if (array_key_exists("signup", $_POST)) {
		signup($conn);
	} else if (array_key_exists("login", $_POST)) {
		login($conn);
	} else if (array_key_exists("signout", $_POST)) {
		signout($conn);
	} else {
		if (isset($_COOKIE['username'])){
			if (verify_cookie($conn, $_COOKIE['username'], $_COOKIE['auth'])){
				echo "Signed in as " . $_COOKIE['username'];
				header("Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k");
				login_account($conn,$_COOKIE['username'],$_COOKIE['password']);
			} else {
				echo "Invalid authentication cookie.";
				setcookie ("username", $username, time()-(60*60), '/');
				setcookie ("auth", $password, time()-(60*60), '/');
			}
		}
	}
	
	function signup($conn){ //CREATE ACCOUNT FROM FORMS
		if(isset($_COOKIE['username'])){
			echo "Already signed in as " . $_COOKIE['username'];
			header("Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k");
			return;
		}
		$username = htmlspecialchars(strval($_POST['new_user']), ENT_QUOTES, 'UTF-8');
		$password = htmlspecialchars(strval($_POST['new_pass']), ENT_QUOTES, 'UTF-8');
		//header('Refresh:0; Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/loginsignuppage.php');
		if ($username == ""){
			echo "A valid username was not entered.";
			return;
		}else if($password == ""){
			echo "A valid password was not entered.";
			return;
		}
		$stmt = $conn->prepare("SELECT * FROM accounts WHERE username=?");
		$stmt->bind_param("s", $username_check);
		$username_check = strval($username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0) {
			echo "Username already taken.";
		} else {
			$stmt->close();
			$stmt = $conn->prepare("INSERT INTO accounts (username, password) VALUES (?, ?)");
			$stmt->bind_param("ss", $username, $password_hashed);
			$username = strval($username);
			$password = strval($password);
			$password_hashed = strval(password_hash($password, PASSWORD_DEFAULT));
			$stmt->execute();
			echo "Account successfully created";
			$stmt->close();
			//LOGIN HERE
			login_account($conn,$username,$password);
		}
	}
	function login($conn){ //CHECK LOGIN FORMS
		if(isset($_COOKIE['username'])){
			echo "Already signed in as " . $_COOKIE['username'];
			header("Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k");
			return;
		}
		$username = htmlspecialchars(strval($_POST['log_user']), ENT_QUOTES, 'UTF-8');
		$password = htmlspecialchars(strval($_POST['log_pass']), ENT_QUOTES, 'UTF-8');
		//header('Refresh:0; Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/loginsignuppage.php');
		$stmt = $conn->prepare("SELECT * FROM accounts WHERE username=?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$username = $row["username"];
				$hash_pass = $row["password"];
			}
			if(password_verify(strval($password), strval($hash_pass))){
				echo "Now signed in as " . $username;
				//LOGIN HERE
				login_account($conn,$username,$password);
				header("Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k");
			} else {
				echo "Password is incorrect.";
			}
		} else {
			echo "Username not found.";
		}
		$stmt->close();
				
	}
	function login_account($conn,$username,$password){ //LOGIN FUNCTIONALITY
		setcookie("username", $username, time()+(60*60), '/');
		//setcookie("password", $password, time()+(60*60), '/');
		$rand_num = rand();
		$auth = password_hash($rand_num,PASSWORD_DEFAULT);
		setcookie("auth", $auth, time()+(60*60), '/');
		$stmt = $conn->prepare("UPDATE accounts SET auth = ? WHERE username = ?");
		$stmt->bind_param("ss", $auth, $username);
		$stmt->execute();
	}
	
	//DEPRECATED
	/*
	function signout($conn){
		setcookie("username", "", time()-3600, '/');
		setcookie("password", "", time()-3600, '/');
		//header('Refresh:0; Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/loginsignuppage.php');
		echo "Signed out of account.";
	}*/
	
	function verify_cookie($conn, $username, $auth){
		$stmt = $conn->prepare("SELECT * FROM accounts WHERE username=?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$input_user = $row["username"];
				$table_auth = $row["auth"];
			}
			if($table_auth == $auth){
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
			<li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/">Home</a></li>
			<li><a href="">About</a></li>
			<li><a href="">My Favorites</a></li>
			<li><a href=""><b>Account</b></a></li>
		</ul>
	</nav>

    <div class="text-boxes"> <!-- Text boxes above zip code box -->
            
        <div class="text-box">
          <p class="work-sans-text" style="margin-top: 40px;">Login or Sign Up</p>
        </div>
        <div class="text-box">
          <p class="source-sans-text">Get started by creating a new account or log in to access your existing account.</p>
        </div>

        <div class="settings-box">
          <form method="post">
          <h2 class="work-sans-text">Login</h2>
          <div class="setting">
              <label for="username">Username:</label>
              <input type="text" id="username" placeholder="Enter your username" name="log_user">
          </div>
          <div class="setting">
              <label for="current-password">Password:</label>
              <input type="password" id="current-password" placeholder="Enter your password" name="log_pass">
          </div>
          <button type=submit class="green-button" name="login">Login</button>
        </form>
      </div>


      <div class="settings-box">
        <form method="post">
        <h2 class="work-sans-text">Create New Account</h2>
        <div class="setting">
            <label for="username">New Username:</label>
            <input type="text" id="username" placeholder="Enter your new username" name="new_user">
        </div>
        <div class="setting">
            <label for="current-password">New Password:</label>
            <input type="password" id="current-password" placeholder="Enter your new password" name="new_pass">
        </div>
        <button type="submit" class="green-button" name="signup">Sign Up</button>
      </form>
    </div>
        
    </div>

</div>


</body>
</html>