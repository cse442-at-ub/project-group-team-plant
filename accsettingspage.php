<!DOCTYPE html>
<head>
  <title>Account Settings Page</title>
</head>
<body>
  <h1><b>Change Username/Password</h1></b>
  <em><h4>To change your username or password, please enter your current information then
  your new username and/or password.</h4></em>

  <br><br>

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
    header('Refresh:0; Location: https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k//accsettingspage.php');
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

  <h4><b>Change Username</b></h4><br>

  <form method="post">
  Current Username: <input type="text" name="current_user">
  <br><br>
  Current Password: <input type="text" name="current_pass">
  <br><br>
  New Username: <input type="text" name="new_user">
  <br><br>
    <input type="submit" name="change_username" class="button" value="Change Username"/>
  </form>

  <br><br>

  <h4><b>Change Password</b></h4><br>

  <form method="post">
  Current Username: <input type="text" name="current_user">
  <br><br>
  Current Password: <input type="text" name="current_pass">
  <br><br>
  New Password: <input type="text" name="new_pass">
  <br><br>
    <input type="submit" name="change_password" class="button" value="Change Password"/>
  </form>

  <h4><b>Set Zip Code</b></h4><br>

  <form method="post">
  Enter Zip Code: <input type="text" name="new_zip">
  <br><br>
    <input type="submit" name="set_zip" class="button" value="Set Zip Code"/>
  </form>

</body>
</html>
