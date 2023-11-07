<?php
//Start session
session_start();
?>

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

    if(!array_key_exists("fav_page", $_SESSION)) {
        $_SESSION['fav_page'] = 1;
    }
    if(array_key_exists("next_page", $_POST)) {
        $_SESSION['fav_page']++;
    }
    if(array_key_exists("prev_page", $_POST)) {
        $_SESSION['fav_page']--;
        if($_SESSION['fav_page'] < 1){
            $_SESSION['fav_page'] = 1;
        }
    }
    if(array_key_exists("unfavorite", $_POST)){
        $unfav_symbol = $_POST['unfavorite'];
        $user = $_COOKIE['username'];
        $stmt = $conn->prepare("DELETE FROM favorites WHERE username=? AND symbol=?");
        $stmt->bind_param("ss", $user, $unfav_symbol);
        $stmt->execute();
        $stmt->close();
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
		while($row = mysqli_fetch_array($result)) {
            $plants[] = $row['common_name'];
            $latin[] = $row['scientific_name'];
            $habit[] = $row['growth_habit'];
            $rarity[] = $row['rarity'];
            $invasive[] = $row['invasive'];
            $symbol[] = $row['symbol'];
		}
        $text = "Plants You May Be Interested In";
        $fav = True;
        
        $length = count($plants);

        $similarPlants = array();
        $images = array();
        
        $curHabit = $habit[0];
        
        
        for ($x = 0; $x < count($habit); $x++) {
            if ($habit[$x] == "Forb/herb"){
                $curHabit = $habit[$x];
            }
            if ($habit[$x] == "Graminoid"){
                $curHabit = $habit[$x];
            }
            if ($habit[$x] == "Tree"){
                $curHabit = $habit[$x];
            }
            if ($habit[$x] == "Nonvascular"){
                $curHabit = $habit[$x];
            }
            if ($habit[$x] == "Lichenous"){
                $curHabit = $habit[$x];
            }
            if ($habit[$x] == "Vine"){
                $curHabit = $habit[$x];
            }
            if ($habit[$x] == "Shrub"){
                $curHabit = $habit[$x];
            }
            if ($habit[$x] == "Subshrub"){
                $curHabit = $habit[$x];
            }
        }
        
        //GET SIMILAR PLANTS
        if (($growth_search = fopen("data/growth_habit.csv", "r")) !== FALSE) { //FIND GROWTH HABIT OF PLANTS
            while (($g_csv = fgetcsv($growth_search, 100000, ",")) !== FALSE) {
                        if($g_csv[4] == $curHabit){ //SYMBOL MATCH
                            $similarPlants[] = $g_csv[2];
                            $newLink = "https://plants.sc.egov.usda.gov/ImageLibrary/original/" . $g_csv[0] . "_001_php.jpg";
                            $images[] = $newLink;
                        }
            }
        fclose($growth_search);
        }
    }else{
        $fav = False;
        $text = "No Favorites Yet!";
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
    <?php if($fav){ # BEGIN FAV IF - ONLY SHOW TABLE IF THERE IS A FAVORITE ?>
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
                <td class="multi-line">Unfavorite</td>
            </tr>
            <?php
            $count = sizeof($symbol);
            $max_page = ceil($count/10);
            $page = $_SESSION['fav_page'];
            if($page > $max_page){
                $_SESSION['fav_page'] = $max_page;
                $page = $max_page;
            }
            $min = ($page - 1) * 10;
            $max = $min + 10;
            if(($page*10 - $count) > 0){
                $max = ($count - ($page-1)*10)+$min;
            }
            for ($i = $min; $i < $max; $i++){



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
                  <td><form method="post">
                <input type="submit" name="unfavorite" value="<?php echo $symbol[$i] ?>">	      
                </form></td>	  
            </tr>
            <?php
            }
            ?>
        </table>
    </div>

    <br><br>
            <div style="display: flex; align-items: center; justify-content: center;">
                        <?php if($page > 1){ ?>
            <form method="post">
                <button type="submit" name="prev_page" class="button work-sans-text square-button">&lt;</button>
            </form>
            <?php } else { ?>
            <div class="button non-clickable-button"></div>
            <?php } ?>

            <p class="source-sans-text" style="text-align: center; padding-left: 20px; padding-right: 20px;">Page <?php echo $page; ?></p>

            <?php if($page < $max_page){ ?>
            <form method="post">
                <button type="submit" name="next_page" class="button work-sans-text square-button">&gt;</button>
            </form>
            <?php } else { ?>
            <div class="button non-clickable-button"></div>
            <?php } ?>

      </div>
    <br><br>
    <?php } # END FAV IF ?>

    <footer><hr>
      <h1><?php echo $text; ?></h1>
      <div>
      <?php if($fav){ # BEGIN FAV IF - ONLY SHOW PLANTS IF THERE IS A FAVORITE ?>
        <table>
            <tr>
                <td><?php echo "<img src=\'$images[0]\'>"; ?></td>
                <td><?php echo "<img src=\'$images[1]\'>"; ?></td>
                <td><?php echo "<img src=\'$images[2]\'>"; ?></td>
                <td><?php echo "<img src=\'$images[3]\'>"; ?></td>
            </tr>
            <tr>
                <td><?php echo "$similarPlants[0]";?></td>
                <td><?php echo "$similarPlants[1]";?></td>
                <td><?php echo "$similarPlants[2]";?></td>
                <td><?php echo "$similarPlants[3]";?></td>
            </tr>
        </table>
        <?php } # END FAV IF ?>
      </div>
    </footer>


</body>
</html>
