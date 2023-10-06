<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Plant</title>
    <link rel="stylesheet" type="text/css" href="Front-end/styles_results.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

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
                <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/login_page.php">Login</a></li>
                <li><a href="">My Favorites</a></li>
                <li><a href="https://www-student.cse.buffalo.edu/CSE442-542/2023-Fall/cse-442k/settings_page.php">Account</a></li>
            </ul>
        </nav>

        <div class="content">
            <div class="textbox">
                <h1>Results</h1>
                <p>A collection of plants hand-tailored to your exact location.</p>

                <form method="post" class="zip-code-form">
                    <input type="text" id="zip-code-input" placeholder="Enter Zip Code" class="work-sans-text" name="zip" value="">
                    <button type="submit" name="recommend_button" class="button work-sans-text">GO!</button>
                </form>
                
            </div>
        </div>
        

    </div>

</body>
</html>