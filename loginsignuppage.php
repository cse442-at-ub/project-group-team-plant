<!DOCTYPE html>
<head>
  <title>Login/Sign-Up Page</title>
</head>
<body>
  <h1><b>Login or Sign-up to our website.</h1></b>
  <em><h4>To login, fill out the fields under the login section and then click the button login to complete.
  To sign-up, fill our the fields for a new username and new password under the sign-up section and use the button to complete.</h4></em>
	<br>
	<?php
	$server = "oceanus.cse.buffalo.edu";
	$user = "sepalutr";
	$pass = "50338448";
	$dbname = "cse442_2023_fall_team_k_db";
	
	$conn = new mysqli($server, $user, $pass, $dbname);
	
	if ($conn->connection_error) {
		die("Connection failed: " . $conn->connection_error);
	}
	
	if(array_key_exists("signup", $_POST)) {
		signup($conn);
	}else if(array_key_exists("login", $_POST)) {
		login($conn);
	}else if(array_key_exists("signout", $_POST)) {
		signout($conn);
	}else {
		if(isset($_COOKIE['username'])){
			if(verify_cookie($conn, $_COOKIE['username'], $_COOKIE['password'])){
				echo "Signed in as " . $_COOKIE['username'];
				login_account($conn,$_COOKIE['username'],$_COOKIE['password']);
			}else{
				echo "Invalid authentication cookie.";
				setcookie ("username", $username, time()-(60*60), '/');
				setcookie ("password", $password, time()-(60*60), '/');
			}
		}
	}
	
	function signup($conn){ //CREATE ACCOUNT FROM FORMS
		if(isset($_COOKIE['username'])){
			echo "Already signed in as " . $_COOKIE['username'];
			return;
		}
		$username = strval($_POST['new_user']);
		$password = strval($_POST['new_pass']);
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
			return;
		}
		$username = strval($_POST['log_user']);
		$password = strval($_POST['log_pass']);
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
		setcookie("password", $password, time()+(60*60), '/');
	}
	
	function signout($conn){
		setcookie("username", "", time()-3600, '/');
		setcookie("password", "", time()-3600, '/');
		header('Refresh:0; Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/loginsignuppage.php');
		echo "Signed out of account.";
	}
	
	function verify_cookie($conn, $username, $password){
		$stmt = $conn->prepare("SELECT * FROM accounts WHERE username=?");
		$stmt->bind_param("s", $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
				$input_user = $row["username"];
				$hash_pass = $row["password"];
			}
			if(password_verify(strval($password), strval($hash_pass))){
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
  <h4><b>LOGIN HERE</b></h4><br>

  <form method="post">
  Username: <input type="text" name="log_user">
  <br><br>
  Password: <input type="text" name="log_pass">
  <br><br>
    <input type="submit" name="login" class="button" value="Log-In" />
  </form>

  <br><br>

  <h4><b>SIGN-UP</b></h4><br>
  
  <form method="post">
  New Username: <input type="text" name="new_user">
  <br><br>
  New Password: <input type="text" name="new_pass">
  <br><br>
    <input type="submit" name="signup" class="button" value="Sign-Up" />
  </form>
  
   <form method="post">
  <br><br>
    <input type="submit" name="signout" class="button" value="Sign-Out" />
  </form>


</body>
</html>
