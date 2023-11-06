<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Plant</title>
    <link rel="stylesheet" type="text/css" href="Front-end/new_results.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <?php
  $server = "oceanus.cse.buffalo.edu";
	$user = "sepalutr";
	$pass = "50338448";
	$dbname = "cse442_2023_fall_team_k_db";
    //ORIGINAL CSS FILE WAS --->>>> styles_favorites.css
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


  $similarPlants = array();
  $images = array();

  $plants = array(); //COMMON NAME
  $latin = array(); //SCIENTIFIC NAME
  $habit = array();
  $rarity = array();
  $invasive = array();
  $symbol = array(); //SYMBOL

  $user = $_COOKIE['username'];
  $stmt = $conn->prepare("SELECT * FROM favorites WHERE username=?");
  $stmt->bind_param("s", $user);
  $stmt->execute();
  $result = $stmt->get_result();
  if($result->num_rows > 0) {
        $counter = 0;
		while($row = mysqli_fetch_array($result)) {
            $plants[$counter] = $row['common_name'];
            $latin[$counter] = $row['scientific_name'];
            $habit[$counter] = $row['growth_habit'];
            $rarity[$counter] = $row['rarity'];
            $invasive[$counter] = $row['invasive'];
            $symbol[$counter] = $row['symbol'];
            $counter++;
		}
    }else{
      echo "no favorites found";
    }

    $conn->close();

   $length = count($plants);





  function get_similar(){

    //NEW ARRAY TO STORE GROWTH HABIT OF CURRENT FAVORITES
    $currentHabits = array();

    $curHabit = $habit[0];

    //NEW ARRAY TO STORE SIMILAR PLANTS
    $tempSimilarPlants = array();

    //ARRAY TO STORE PICTURE LINKS
    $pictures = array();

    //GET SIMILAR PLANTS
    $newCount = 0;
    if (($growth_search = fopen("data/growth_habit.csv", "r")) !== FALSE) { //FIND GROWTH HABIT OF PLANTS
      while (($g_csv = fgetcsv($growth_search, 100000, ",")) !== FALSE) {
                  if($g_csv[4] == $curHabit){ //SYMBOL MATCH
                      $tempSimilarPlants[$newCount] = $g_csv[2];
                      $newLink = "https://plants.sc.egov.usda.gov/ImageLibrary/original/" . $g_csv[0] . "_001_php.jpg";
                      $pictures[$newCount] = $newLink;
                      $newCount++;
                  }
            }
    fclose($growth_search);
    }

    //CHOOSE 4 PLANTS FROM SIMILAR ARRAY AND APPEND NAMES + PICTURES
    $similarPlants[0] = $tempSimilarPlants[0];
    $similarPlants[1] = $tempSimilarPlants[1];
    $similarPlants[2] = $tempSimilarPlants[2];
    $similarPlants[3] = $tempSimilarPlants[3];
    $images[0] = $pictures[0];
    $images[1] = $pictures[1];
    $images[2] = $pictures[2];
    $images[3] = $pictures[3];

  }






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
            <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/favorites_page.php"><b>My Favorites</b></a></li>
            <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/settings_page.php">Account</a></li>
        </ul>
    </nav>
</div>

    <div class="text-boxes"> <!-- Text boxes above zip code box -->

        <div class="text-box">
          <p class="work-sans-text">My Favorites</p>
        </div>
        <div class="text-box">
          <p class="source-sans-text">Discover the perfect plants tailored to your location with our personalized plant recommendation tool.</p>

        </div>

    </div>
    <div class="table-container">
        <table class="styled-table">
            <tr class="header-row">
                <td class="multi-line">Common Name</td>
                <td class="multi-line">Scientific Name</td>
                <td class="multi-line">Growth Habit</td>
                <td class="multi-line">Rarity</td>
                <td class="multi-line">Invasive</td>
                <td class="multi-line">Plant Guide</td>
                <td class="multi-line">Fact Sheet</td>
                <td class="multi-line">Favorite</td>
            </tr>
            <?php
            for ($i = 0; $i < $length; $i++){
            ?>
            <?php $pgLink = "https://plants.usda.gov/DocumentLibrary/plantguide/pdf/pg_" . $symbol[$i] . ".pdf"; ?>
            <?php $fsLink = "https://plants.usda.gov/DocumentLibrary/factsheet/pdf/fs_" . $symbol[$i] . ".pdf"; ?>
            <tr class="<?php echo $i % 2 === 0 ? 'even-row' : 'odd-row'; ?>">
                <td class="multi-line"><?php echo "$plants[$i] <br>";?></td>
                <td class="multi-line"><?php echo "$latin[$i] <br>";?></td>
                <td class="multi-line"><?php echo "$habit[$i] <br>";?></td>
                <td class="multi-line"><?php echo "$rarity[$i] <br>";?></td>
                <td class="multi-line"><?php echo "$invasive[$i] <br>";?></td>
                <?php if (in_array("Content-Type: application/pdf", get_headers($pgLink))) { ?>
                  <td class="multi-line"><?php echo "<a href=$pgLink>Link Available</a>"; ?></td>
                <?php }else{ ?>
                  <td class="multi-line"><?php echo "Link Unavailable"; ?></td>
                <?php } ?>
                <?php if (in_array("Content-Type: application/pdf", get_headers($fsLink))) { ?>
                  <td class="multi-line"><?php echo "<a href=$fsLink>Link Available</a>"; ?></td>
                <?php }else{ ?>
                  <td class="multi-line"><?php echo "Link Unavailable"; ?></td>
                <?php } ?>
            </tr>
            <?php
            }
            ?>
        </table>
    </div>

    <br><br>

    <footer><hr>
      <h1>Plants You May Be Interested In</h1>
      <div>
        <p>
        <?php echo "<img src=\'$images[0]\'>"; ?>
        <?php echo "$similarPlants[0]";?>
        <?php echo "<img src=\'$images[1]\'>"; ?>
        <?php echo "$similarPlants[1]";?>
        <?php echo "<img src=\'$images[2]\'>"; ?>
        <?php echo "$similarPlants[2]";?>
        <?php echo "<img src=\'$images[3]\'>"; ?>
        <?php echo "$similarPlants[3]";?>
        </p>
      </div>
    </footer>


</body>
</html>
