<?php
//Start session
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Team Plant</title>
    <link rel="stylesheet" type="text/css" href="style_fav.css">
    <link rel="stylesheet" type="text/css" href="Front-end/styles_favorites_dark.css" id="dark-mode">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600&display=swap" rel="stylesheet">
    <div class="toggle-container">

<label class="switch">
    <input type="checkbox" id="dark-mode-toggle">
    <span class="slider"></span>
</label>

<div class="light">DARK</div>
<div class="dark">LIGHT</div>
</div>


</head>
<body>
    <script>
        function toggleBlack(symbol) {
            var g_name = "g_" + symbol;
            var b_name = "b_" + symbol;
            var g_heart = document.getElementById(g_name);
            var b_heart = document.getElementById(b_name);

            b_heart.style.visibility = "visible";
            g_heart.style.visibility = "hidden";

        }
</script>
  <?php
  // based on original work from the PHP Laravel framework
  if (!function_exists('str_contains')) {
      function str_contains($haystack, $needle) {
          return $needle !== '' && mb_strpos($haystack, $needle) !== false;
      }
  }

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

        $habitCheck = True;


        for ($x = 0; $x < count($habit); $x++) {
            if (str_contains($habit[$x], "Forb/herb")){
                $curHabit = "Forb/herb";
            }
            if (str_contains($habit[$x], "Graminoid")){
                $curHabit = "Graminoid";
            }
            if (str_contains($habit[$x], "Tree")){
                $curHabit = "Tree";
            }
            if (str_contains($habit[$x], "Nonvascular")){
                $curHabit = "Nonvascular";
            }
            if (str_contains($habit[$x], "Lichenous")){
                $curHabit = "Lichenous";
            }
            if (str_contains($habit[$x], "Vine")){
                $curHabit = "Vine";
            }
            if (str_contains($habit[$x], "Shrub")){
                $curHabit = "Shrub";
            }
            if (str_contains($habit[$x], "Subshrub")){
                $curHabit = "Subshrub";
            }
        }


        if($curHabit === "N/A"){
          $habitCheck = False;
          $text = "No Similar Plants Could Be Found! Please try favoriting plants with a growth habit.";
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
        if(count($similarPlants) < 4){
            $habitCheck = False;
            $text = "No Similar Plants Could Be Found! Please try favoriting plants with a growth habit.";
        }
        }
    }else{
        $fav = False;
        $text = "No Favorites Yet!";
    }


    $num0 = rand(0, count($images)-1);
    $num1 = rand(0, count($images)-1);
    $num2 = rand(0, count($images)-1);
    $num3 = rand(0, count($images)-1);

    $profile_picture = "";

    $username = $_COOKIE['username'];
    $stmt = $conn->prepare("SELECT profile_picture FROM accounts WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($profile_picture);
    $stmt->fetch();
    $stmt->close();

    $conn->close();

?>

<div class="button-bar"> <!-- Button Bar -->
    <div class="logo">
        <img src="Front-end/images/logo.jpg" alt="Team Plant Logo">
        <span>Team Plant</span>
    </div>
    <nav>
        <ul>
            <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/">Home</a></li>
            <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/about_page.php">About</a></li>
            <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/favorites_page.php"><b>My Favorites</b></a></li>
            <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/settings_page.php">Account</a></li>
            <li><img class="circle_img" src="<?php echo $profile_picture; ?>" alt="Profile Picture" width="40px" height="40px"></li>
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
    <br><br><br><br>
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

                #<input type="hidden" name="unfavorite" style="position: absolute; top: 0; left: 0;" value="<?php echo $symbol[$i] ?&gt">

                #<img id="g_<?php echo $symbol[$i] ?&gt" onclick="toggleBlack('<?php echo $symbol[$i] ?&gt')" style="visibility: visible; position: absolute; top: 0; left: 0;" src="brokenheart.png" class="heart-button" alt="Unfavorite">

                #<img id="b_<?php echo $symbol[$i] ?&gt" style="visibility: hidden; position: absolute; top: 0; left: 0;" src="blackbrokenheart.png" class="heart-button" alt="Unfavorite">

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
                  <?php } # position: relative ?>
                  <td style="position: relative"><form method="post">
                    <input type="hidden" name="unfavorite" value="<?php echo $symbol[$i] ?>">

                    <input id="g_<?php echo $symbol[$i] ?>" onclick="toggleBlack('<?php echo $symbol[$i] ?>')" type="image" style="visibility: visible; position: absolute; top: 0; left: 0;" src="brokenheart.png" class="heart-button" alt="Unfavorite">

                    <input id="b_<?php echo $symbol[$i] ?>" type="image" style="visibility: hidden; position: absolute; top: 0; left: 0;" src="blackbrokenheart.png" class="heart-button" alt="Unfavorite">
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
      <br><br><br>
      <h1 style="font-family: 'Poppins', sans-serif; padding-left: 190px;"><?php echo $text; ?></h1>
      <br>
      <div>
      <?php if($fav && $habitCheck){ # BEGIN FAV IF - ONLY SHOW PLANTS IF THERE IS A FAVORITE AND HABIT?>
        <table class="pics">
            <tr>
              <?php if (is_array(getimagesize($images[$num0]))) { ?>
                <td><?php echo "<img src=$images[$num0] width=\"220\" height=\"220\">"; ?></td>
              <?php }else{ ?>
                <td><?php echo '<img src="noplantfound.jpg">'; ?></td>
              <?php } ?>
              <?php if (is_array(getimagesize($images[$num1]))) { ?>
                <td><?php echo "<img src=$images[$num1] width=\"220\" height=\"220\">"; ?></td>
              <?php }else{ ?>
                <td><?php echo '<img src="noplantfound.jpg">'; ?></td>
              <?php } ?>
              <?php if (is_array(getimagesize($images[$num2]))) { ?>
                <td><?php echo "<img src=$images[$num2] width=\"220\" height=\"220\">"; ?></td>
              <?php }else{ ?>
                <td><?php echo '<img src="noplantfound.jpg">'; ?></td>
              <?php } ?>
              <?php if (is_array(getimagesize($images[$num3]))) { ?>
                <td><?php echo "<img src=$images[$num3] width=\"220\" height=\"220\">"; ?></td>
              <?php }else{ ?>
                <td><?php echo '<img src="noplantfound.jpg">'; ?></td>
              <?php } ?>
            </tr>
            <tr>
                <td><?php echo "$similarPlants[$num0]";?></td>
                <td><?php echo "$similarPlants[$num1]";?></td>
                <td><?php echo "$similarPlants[$num2]";?></td>
                <td><?php echo "$similarPlants[$num3]";?></td>
            </tr>
        </table>
        <?php } # END FAV IF ?>
      </div>
    </footer>

    <footer><hr>
      <br><br><br>
      <h1 style="font-family: 'Poppins', sans-serif; padding-left: 190px;">Seed Vendors</h1>
      <br>
      <div class="text-box" style="padding-left: 190px;">
          <p class="source-sans-text">Purchase a diverse array of high-quality seeds, accompanied by detailed imformation and guidance for successful cultivation.</p>
          <br>
          <p style="font-family: 'Poppins', sans-serif;"><b>Best Overall:</b> <a href="https://www.burpee.com/" target="_blank">Burpee</a></p><br>
            <p style="font-family: 'Poppins', sans-serif;"><b>Best for Vegetables:</b> <a href="https://www.johnnyseeds.com/" target="_blank">Johnny's Select Seeds</a></p><br>
                <p style="font-family: 'Poppins', sans-serif;"><b>Best for Flowers:</b> <a href="https://www.edenbrothers.com/" target="_blank">Eden Brothers</a></p><br>
                    <p style="font-family: 'Poppins', sans-serif;"><b>Best Budget-Priced Seeds:</b> <a href="https://ferrymorse.com/" target="_blank">Ferry-Morse</a></p><br>
                        <p style="font-family: 'Poppins', sans-serif;"><b>Best for Direct Seeding:</b> <a href="https://parkseed.com/" target="_blank">Park Seed</a></p><br>
                            <p style="font-family: 'Poppins', sans-serif;"><b>Best Sowing Instructions:</b> <a href="https://www.botanicalinterests.com/" target="_blank">Botanical Interests</a></p><br>
                                <p style="font-family: 'Poppins', sans-serif;"><b>Best for Rate Seeds:</b> <a href="https://www.rareseeds.com/" target="_blank">Baker Creek Heirloom Seeds</a></p><br>
                                    <p style="font-family: 'Poppins', sans-serif;"><b>Best for Herbs:</b> <a href="https://www.reneesgarden.com/" target="_blank">Renee's Garden</a></p><br>
                                        <p style="font-family: 'Poppins', sans-serif;"><b>Best for Heirloom Seeds:</b> <a href="https://seedsavers.org/" target="_blank">Seed Savers Exchange</a></p><br>
                                            <p style="font-family: 'Poppins', sans-serif;"><b>Best Organic Seeds:</b> <a href="https://territorialseed.com/" target="_blank">Territorial Seed Company</a></p><br>
                                                <p style="font-family: 'Poppins', sans-serif;"><b>Source:</b> <a href="https://www.thepioneerwoman.com/home-lifestyle/gardening/g39682996/best-places-to-buy-seeds/" target="_blank">https://www.thepioneerwoman.com/home-lifestyle/gardening/g39682996/best-places-to-buy-seeds/</a></p><br><br><br>
        </div>
    </footer>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const favoritesLink = document.querySelector('a[href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/favorites_page.php"]');

        favoritesLink.addEventListener("click", function (event) {

            const darkModeEnabled = localStorage.getItem('darkMode') === 'enabled';

            if (!darkModeEnabled) {
                // Create an overlay container
                const overlayContainer = document.createElement("div");
                overlayContainer.style.position = "fixed";
                overlayContainer.style.top = "0";
                overlayContainer.style.left = "0";
                overlayContainer.style.width = "100%";
                overlayContainer.style.height = "100%";
                overlayContainer.style.backgroundColor = "#f4ffee";
                overlayContainer.style.zIndex = "9998"; // Adjust the z-index as needed

                // Create the header bar element
                const headerBar = document.createElement("div");
                headerBar.className = "button-bar2";
                headerBar.innerHTML = `
                    <div class="logo">
                        <img src="Front-end/images/logo2.png" alt="Team Plant Logo">
                        <span>Team Plant</span>
                    </div>
                `;

                headerBar.style.zIndex = "9999";

                // Append the header bar to the overlay container
                overlayContainer.appendChild(headerBar);

                // Create the loading gif element
                const loadingGif = document.createElement("img");
                loadingGif.src = "Front-end/images/green_style.gif";
                loadingGif.alt = "Loading GIF";

                // Apply styles to the loading gif
                loadingGif.style.position = "absolute";
                loadingGif.style.top = "50%";
                loadingGif.style.left = "50%";
                loadingGif.style.transform = "translate(-50%, -50%)";
                loadingGif.style.zIndex = "9999";
                loadingGif.style.width = "350px";
                loadingGif.style.height = "350px";

                // Append the loading gif to the overlay container
                overlayContainer.appendChild(loadingGif);

                const logo = document.createElement("img");
                logo.src = "Front-end/images/logo2.png";
                logo.alt = "Logo";

                // Apply styles to the logo
                logo.style.position = "absolute";
                logo.style.top = "50%";
                logo.style.left = "50%";
                logo.style.transform = "translate(-50%, -50%)";
                logo.style.zIndex = "9999";
                logo.style.width = "85px";
                logo.style.height = "85px";

                // Append the logo to the overlay container
                overlayContainer.appendChild(logo);

                // Create an array of strings
                const randomStrings = [
                    "Exploring the green wonders near you. Just a moment!",
                    "Cultivating the best matches for your zip code. Hang tight!",
                    "Harvesting the most suitable plants for your zip code. Almost done!",
                    "Growing personalized plant suggestions for your location. Patience, please!",
                    "Planting the perfect recommendations for your zip code. Almost there!",
                ];

                // Create a new element for the random string
                const randomStringElement = document.createElement("p");
                randomStringElement.style.position = "absolute";
                randomStringElement.style.top = "80%";
                randomStringElement.style.left = "50%";
                randomStringElement.style.transform = "translate(-50%, -50%)";
                randomStringElement.style.textAlign = "center";
                randomStringElement.style.fontFamily = "'Work Sans', sans-serif";
                randomStringElement.style.fontSize = "18px";
                randomStringElement.style.color = "#222";

                // Apply transition for a smooth fade effect
                randomStringElement.style.transition = "opacity 1s ease-in-out";

                // Append the random string element to the overlay container
                overlayContainer.appendChild(randomStringElement);

                // Index to keep track of the current string
                let currentIndex = 0;

                // Function to update the random string and initiate fade effect
                function updateRandomString() {
                    // Start the fade-out effect by setting opacity to 0
                    randomStringElement.style.opacity = 0;

                    // Wait for the fade-out animation to complete
                    setTimeout(() => {
                        // Update the random string
                        randomStringElement.textContent = randomStrings[currentIndex];

                        // Start the fade-in effect by setting opacity to 1
                        randomStringElement.style.opacity = 1;

                        // Increment the index for the next iteration
                        currentIndex = (currentIndex + 1) % randomStrings.length;

                        // Wait for a few seconds and repeat
                        setTimeout(updateRandomString, 3000); // Adjust the duration as needed
                    }, 1000); // Adjust the duration as needed
                }

                // Start the updateRandomString function
                updateRandomString();

                // Append the overlay container to the body
                document.body.appendChild(overlayContainer);

                // Disable scrolling on the original page
                document.body.style.overflow = "hidden";

            } else {

               // Create an overlay container
               const overlayContainer = document.createElement("div");
                overlayContainer.style.position = "fixed";
                overlayContainer.style.top = "0";
                overlayContainer.style.left = "0";
                overlayContainer.style.width = "100%";
                overlayContainer.style.height = "100%";
                overlayContainer.style.backgroundColor = "#73736c";
                overlayContainer.style.zIndex = "9998"; // Adjust the z-index as needed

                // Create the header bar element
                const headerBar = document.createElement("div");
                headerBar.className = "button-bar3";
                headerBar.innerHTML = `
                    <div class="logo">
                        <img src="Front-end/images/logo2.png" alt="Team Plant Logo">
                        <span>Team Plant</span>
                    </div>
                `;

                headerBar.style.zIndex = "9999";

                // Append the header bar to the overlay container
                overlayContainer.appendChild(headerBar);

                // Create the loading gif element
                const loadingGif = document.createElement("img");
                loadingGif.src = "Front-end/images/green_style.gif";
                loadingGif.alt = "Loading GIF";

                // Apply styles to the loading gif
                loadingGif.style.position = "absolute";
                loadingGif.style.top = "50%";
                loadingGif.style.left = "50%";
                loadingGif.style.transform = "translate(-50%, -50%)";
                loadingGif.style.zIndex = "9999";
                loadingGif.style.width = "350px";
                loadingGif.style.height = "350px";
                loadingGif.style.filter = "invert(80%)";

                // Append the loading gif to the overlay container
                overlayContainer.appendChild(loadingGif);

                const logo = document.createElement("img");
                logo.src = "Front-end/images/logo2.png";
                logo.alt = "Logo";

                // Apply styles to the logo
                logo.style.position = "absolute";
                logo.style.top = "50%";
                logo.style.left = "50%";
                logo.style.transform = "translate(-50%, -50%)";
                logo.style.zIndex = "9999";
                logo.style.width = "85px";
                logo.style.height = "85px";

                // Append the logo to the overlay container
                overlayContainer.appendChild(logo);

                // Create an array of strings
                const randomStrings = [
                    "Exploring the green wonders near you. Just a moment!",
                    "Cultivating the best matches for your zip code. Hang tight!",
                    "Harvesting the most suitable plants for your zip code. Almost done!",
                    "Growing personalized plant suggestions for your location. Patience, please!",
                    "Planting the perfect recommendations for your zip code. Almost there!",
                ];

                // Create a new element for the random string
                const randomStringElement = document.createElement("p");
                randomStringElement.style.position = "absolute";
                randomStringElement.style.top = "80%";
                randomStringElement.style.left = "50%";
                randomStringElement.style.transform = "translate(-50%, -50%)";
                randomStringElement.style.textAlign = "center";
                randomStringElement.style.fontFamily = "'Work Sans', sans-serif";
                randomStringElement.style.fontSize = "18px";
                randomStringElement.style.color = "#000000";

                // Apply transition for a smooth fade effect
                randomStringElement.style.transition = "opacity 1s ease-in-out";

                // Append the random string element to the overlay container
                overlayContainer.appendChild(randomStringElement);

                // Index to keep track of the current string
                let currentIndex = 0;

                // Function to update the random string and initiate fade effect
                function updateRandomString() {
                    // Start the fade-out effect by setting opacity to 0
                    randomStringElement.style.opacity = 0;

                    // Wait for the fade-out animation to complete
                    setTimeout(() => {
                        // Update the random string
                        randomStringElement.textContent = randomStrings[currentIndex];

                        // Start the fade-in effect by setting opacity to 1
                        randomStringElement.style.opacity = 1;

                        // Increment the index for the next iteration
                        currentIndex = (currentIndex + 1) % randomStrings.length;

                        // Wait for a few seconds and repeat
                        setTimeout(updateRandomString, 3000); // Adjust the duration as needed
                    }, 1000); // Adjust the duration as needed
                }

                // Start the updateRandomString function
                updateRandomString();

                // Append the overlay container to the body
                document.body.appendChild(overlayContainer);

                // Disable scrolling on the original page
                document.body.style.overflow = "hidden";
            }
        });
    });
    </script>

    <!-- script that listens for dark mode toggle -->

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const darkModeLink = document.getElementById('dark-mode');

        // Check the initial state of the toggle
        const initialDarkModeSetting = localStorage.getItem('darkMode');
        if (initialDarkModeSetting === 'enabled') {
            darkModeToggle.checked = true;
            enableDarkMode();
        } else {
            // Apply light mode styles when dark mode is not enabled
            disableDarkMode();
        }

        // Toggle dark mode when the switch is changed
        darkModeToggle.addEventListener('change', function () {
            if (darkModeToggle.checked) {
                enableDarkMode();
            } else {
                disableDarkMode();
            }
        });

        // Function to enable dark mode
        function enableDarkMode() {
            darkModeLink.removeAttribute('disabled');
            localStorage.setItem('darkMode', 'enabled');
        }

        // Function to disable dark mode
        function disableDarkMode() {
            darkModeLink.setAttribute('disabled', true);
            localStorage.setItem('darkMode', 'disabled');
        }
    });
    </script>

</body>
</html>
