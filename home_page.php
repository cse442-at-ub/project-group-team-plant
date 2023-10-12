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

    <div class="bg-image"></div>  <!-- Background image --> 

    <div class="button-bar"> <!-- Button Bar -->
        <div class="logo">
            <img src="Front-end/images/logo.jpg" alt="Team Plant Logo">
            <span>Team Plant</span>
        </div>
        <nav>
            <ul>
                <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/home_page.php"><b>Home</b></a></li>
                <li><a href="#">About</a></li>
                <li><a href="#">My Favorites</a></li>
                <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/settings_page.php">Account</a></li>
            </ul>
        </nav>

        <div class="text-boxes"> <!-- Text boxes above zip code box -->
            
            <div class="text-box">
              <p class="work-sans-text">Grow Local, Go Green,<br>Plant Perfect.</p>
            </div>
            <div class="text-box">
              <p class="source-sans-text">Discover the perfect plants tailored to your location with <br>our personalized plant recommendation tool.</p>

              <form method="post">  <!-- Enter zip code box with post form -->
                <div class="zip-code-box">
                    <input type="text" id="zip-code-input" placeholder="Enter Zip Code" class="work-sans-text" name="zip" value="">
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

</body>
</html>