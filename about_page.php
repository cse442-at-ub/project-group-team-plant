<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Team Plant</title>
    <link rel="stylesheet" type="text/css" href="styles_about.css">
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

    // checks for valid cookie, if none, redirect to login page
    if (!isset($_COOKIE['username']) || !isset($_COOKIE['auth']) || !verify_cookie($conn, $_COOKIE['username'], $_COOKIE['auth'])) {
        header("Location: login_page.php");
        exit();
    }

    $conn->close();
?>

<!--this is the stuff for the tip bar links-->
<div class="button-bar"> <!-- Button Bar -->
	<div class="logo">
		<img src="Front-end/images/logo.jpg" alt="Team Plant Logo">
		<span>Team Plant</span>
	</div>
	<nav>
		<ul>
			<li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/">Home</a></li>
			<li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/about_page.php">About</a></li>
			<li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/favorites_page.php">My Favorites</a></li>
			<li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/settings_page.php"><b>Account</b></a></li>
		</ul>
	</nav>
</div> 

<div class="row">
  <div class="column">
    <div class="card">
      <img src="andrew_pic.jpg" alt="Andrew" style="width:100%">
      <div class="container">
        <h2>Andrew Larock</h2>
        <p>Andrew is a 4th year CS student from Buffalo.</p>
        <p>adlarock@buffalo.edu</p>
      </div>
    </div>
  </div>

<div class="row">
  <div class="column">
    <div class="card">
      <img src="sam_pic.jpg" alt="Sam" style="width:100%">
      <div class="container">
        <h2>Sam Palutro</h2>
        <p>Sam is a 4th year CS student born and raised in Buffalo, New York, I am in my last semester of my Computer Science degree. I have a brother and sister and a pet cat named Kenny. I am not interested in plants..</p>
        <p>adlarock@buffalo.edu</p>
      </div>
    </div>
  </div>

<div class="row">
  <div class="column">
    <div class="card">
      <img src="aidan_pic.jpg" alt="Aidan" style="width:100%">
      <div class="container">
        <h2>Aidan Nasca</h2>
        <p>Aidan is a 4th year CS student from Buffalo.</p>
        <p>aidannas@buffalo.edu</p>
      </div>
    </div>
  </div>

<div class="row">
  <div class="column">
    <div class="card">
      <img src="brandon_pic.jpg" alt="Brandon" style="width:100%">
      <div class="container">
        <h2>Brandon Kent</h2>
        <p>Brandon is a 4th year CS student from Buffalo. I am married with 4 kids and a former Marine.</p>
        <p>bjkent4@buffalo.edu</p>
      </div>
    </div>
  </div>


</body>
</html>