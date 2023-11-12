<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Plant</title>
    <link rel="stylesheet" type="text/css" href="Front-end/styles_home.css">
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
	
    //check if zipcode is set, if so set input
    $stmt = $conn->prepare("SELECT * FROM accounts WHERE username=?");
	$stmt->bind_param("s", $_COOKIE['username']);
	$stmt->execute();
	$result = $stmt->get_result();
    if($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $saved_zip = $row["zipcode"];
        }
    }
    if($saved_zip != null){
        $zip = htmlspecialchars($saved_zip, ENT_QUOTES, 'UTF-8');
    }else{
        $zip = htmlspecialchars($saved_zip, ENT_QUOTES, 'UTF-8');
    }
    $stmt->close();
	$conn->close();
	?>

<div id="loading-screen">
    <p>Loading...</p>
</div>

    <div class="button-bar"> <!-- Button Bar -->
        <div class="logo">
            <img src="Front-end/images/logo.jpg" alt="Team Plant Logo">
            <span>Team Plant</span>
        </div>
        <nav>
            <ul>
                <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k"><b>Home</b></a></li>
                <li><a href="#">About</a></li>
                <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/favorites_page.php">My Favorites</a></li>
                <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/settings_page.php">Account</a></li>
            </ul>
        </nav>

        <div class="text-boxes"> <!-- Text boxes above zip code box -->
            
            <div class="text-box">
              <p class="work-sans-text">Grow Local, Go Green,<br>Plant Perfect.</p>
            </div>
            <div class="text-box">
              <p class="source-sans-text">Discover the perfect plants tailored to your location with <br>our personalized plant recommendation tool.</p>

              <form id="recommendation-form" action="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/results_page.php" method="post">  <!-- Enter zip code box with post form -->
                <div class="zip-code-box">
                    <input type="text" id="zip-code-input" placeholder="Enter Zip Code" class="work-sans-text" name="zip" value=<?php echo $zip; ?>>
                    <button type="submit" name="recommend_button" class="button work-sans-text">GO!</button>
                    
                </div>
            </form>

            </div>
            
        </div>

        <div class="feature-box-container">
            <div class="feature-box">
                <h2 class="work-sans-text">Enter Location. <img src="Front-end/images/pin.png"></h2>
                <p class="work-sans-text">Start your plant journey by simply entering your zip code. This helps us recommend the perfect plants that thrive in your area’s unique conditions.</p>
            </div>
            <div class="feature-box">
                <h2 class="work-sans-text">Get Results. <img src="Front-end/images/results.png"></h2>
                <p class="work-sans-text">Discover a world of green possibilities! With a click of a button, you’ll receive a tailored list of plants that will flourish in your area.</p>
            </div>
            <div class="feature-box">
                <h2 class="work-sans-text">Save Favorites. <img src="Front-end/images/favorite.png"></h2>
                <p class="work-sans-text">Found your ideal plants? Save them as favorites for quick access and easy reference. Your personalized plant collection is just a click away.</p>
            </div>
        </div>

    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.getElementById("recommendation-form");

        form.addEventListener("submit", function () {
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
                    <img src="Front-end/images/logo.jpg" alt="Team Plant Logo">
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
                "Planting the perfect recommendations for your zip code. Almost there!"
            ];

            // Choose a random string from the array
            const randomString = randomStrings[Math.floor(Math.random() * randomStrings.length)];

            // Create a new element for the random string
            const randomStringElement = document.createElement("p");
            randomStringElement.textContent = randomString;
            randomStringElement.style.position = "absolute";
            randomStringElement.style.top = "80%";
            randomStringElement.style.left = "50%";
            randomStringElement.style.transform = "translate(-50%, -50%)";
            randomStringElement.style.textAlign = "center";
            randomStringElement.style.fontFamily = "'Work Sans', sans-serif";
            randomStringElement.style.fontSize = "18px";
            randomStringElement.style.color = "#222";

            // Append the random string element to the overlay container
            overlayContainer.appendChild(randomStringElement);

            // Append the overlay container to the body
            document.body.appendChild(overlayContainer);

            // Disable scrolling on the original page
            document.body.style.overflow = "hidden";

        });
    });
</script>

</body>
</html>