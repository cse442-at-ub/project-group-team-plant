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
  <script>
    function showFilters() {
      if(document.getElementById("hiddenselect").style.display === "none"){
        document.getElementById('hiddenselect').style.display = "block";
      } else {
        document.getElementById('hiddenselect').style.display = "none";
      }
    }
  </script>
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

  if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle) {
      return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
  }
  $ret = array();
  $numOfPlants = 0;
  $printPlants = array();
  $printLatin = array();
  $printSymbol = array();
  $printHabit = array();
  $rarity = array(); //RARITY
  $invasive = array(); //INVASIVE
  $new_output = null;
  $text = "";


  if(array_key_exists("recommend_button", $_POST)) {
    $text = zip_search();
  }
  $fips = "";
  $state_id = "";
  function zip_search(){ 
      $plants = array(); //COMMON NAME
      $latin = array(); //SCIENTIFIC NAME
      $symbol = array(); //SYMBOL
      $habit = array(); //GROWTH HABIT
      $zip = strval($_POST['zip']);
      $text = "Results for";
      if (($zip_search = fopen("data/uszips.csv", "r")) !== FALSE) { //FIND FIPS & STATE FROM ZIP
        while (($csv = fgetcsv($zip_search, 100000, ",")) !== FALSE) {
          if(strval($csv[0]) == strval($zip)){
            $fips = $csv[10];
            $state_id = $csv[4];
            break;
          }
        }
        fclose($zip_search);
      }else{ //INVALID ZIP
          $text = "The zipcode ". $zip ." was invalid";
          return $text;
      }
  $state_csv = strval($state_id) . ".csv";
  $us_fips = "US" . strval($fips);
  if (($plant_search = fopen("data/" . $state_csv, "r")) !== FALSE) { //FIND PLANTS FROM FIP
    while (($p_csv = fgetcsv($plant_search, 100000, ",")) !== FALSE) {
      if(strval($p_csv[4]) == strval($us_fips)){ //CORRECT ZIP IN CSV
                  if($p_csv[3] != ""){
                      $plants[] = $p_csv[3];
                      $latin[] = $p_csv[2];
                      $symbol[] = $p_csv[0];
                      $printPlants[] = $p_csv[3];
                      $printLatin[] = $p_csv[2];
                      $printSymbol[] = $p_csv[0];
                  }
      }
    }
  fclose($plant_search);
  //print("PLANT");
  }else{ //INVALID ZIPCODE (WRONG ZIP/STATE)
          $text = "The zipcode ". $zip ." was invalid";
          return $text;
  }
  if(count($plants) == 0){ //NO RESULTS
    $text = "No Results for ". $zip;
    return $text;
  }
      if (($growth_search = fopen("data/growth_habit.csv", "r")) !== FALSE) { //FIND GROWTH HABIT OF PLANTS
    while (($g_csv = fgetcsv($growth_search, 100000, ",")) !== FALSE) {
              for($i = 0; $i <= count($symbol)-1; $i++){
                  if($g_csv[0] == $symbol[$i]){ //SYMBOL MATCH
                      if(array_key_exists($i, $habit)){
                        if(str_contains($habit[$i], $g_csv[4])){
                        }else{
                          $habit[$i] = $habit[$i] . ", " . $g_csv[4];
                          $printHabit[$i] = $printHabit[$i] . ", " . $g_csv[4];
                        }
                      }else{
                          $habit[$i] = $g_csv[4];
                          $printHabit[$i] = $g_csv[4];
                      }
                  }
              }
    }
  fclose($growth_search);
  }
  //print("GROWTH");
      if (($rarity_search = fopen("data/rarity.csv", "r")) !== FALSE) { //FIND RARITY OF PLANTS
    while (($r_csv = fgetcsv($rarity_search, 100000, ",")) !== FALSE) {
              for($j = 0; $j <= count($symbol)-1; $j++){
                  if($r_csv[0] == $symbol[$j]){ //SYMBOL MATCH
                        //CHECK IF "Endangered" OR "Threatened"
                        if(str_contains($r_csv[4], "Threatened")){
                            $rarity[$j] = "Threatened";
                        }
                        elseif(str_contains($r_csv[4], "Endangered")){
                            $rarity[$j] = "Endangered";
                        }
                  }
              }
    }
  fclose($rarity_search);
  }
      if (($inv_search = fopen("data/invasive.csv", "r")) !== FALSE) { //FIND IF INVASIVE
    while (($i_csv = fgetcsv($inv_search, 100000, ",")) !== FALSE) {
              for($i = 0; $i <= count($plants); $i++){
                  if($i_csv[0] == $symbol[$i]){ //SYMBOL MATCH
                      //CHECK IF "Invasive"
                      if(str_contains($i_csv[4], "Invasive")){
                          $invasive[$i] = "Invasive";
                      }
                      if(str_contains($i_csv[4], "Potentially Invasive")){
                          $invasive[$i] = "Potentially Invasive";
                      }
                  }
              }
    }
  fclose($inv_search);
  }
      $numOfPlants = count($plants);
      for($i = 0; $i <= count($plants)-1; $i++){
        if(!array_key_exists($i, $printHabit) || $printHabit[$i] == ""){
          $printHabit[$i] = "N/A";
        }
        if(!array_key_exists($i, $rarity) || $rarity[$i] == ""){
          $rarity[$i] = "N/A";
        }
        if(!array_key_exists($i, $invasive) || $invasive[$i] == ""){
          $invasive[$i] = "N/A";
        }
      }
      $filter = array();
      //FIND ALL INDEX's
      for($i = 0; $i <= count($plants)-1; $i++){
        if(array_key_exists($i, $plants)){
          $filter[$i] = $i;
        }
      }

      //MANUAL FILTERS FOR TESTING
      //$_POST["habit"] = "Forb/herb";
      //$_POST['invasive'] = "Invasive";
      //$_POST["rarity"] = "Endangered";

      //CHECK IF FILTERS USED
      if(array_key_exists("habit", $_POST)) {
        $habit_check = strval($_POST["habit"]);
        if($habit_check !== "None"){
          $filter = array();
        }
      }
      if(array_key_exists("rarity", $_POST)) {
        $rarity_check = strval($_POST["rarity"]);
        if($rarity_check !== "None"){
          $filter = array();
        }
      }
      if(array_key_exists("invasive", $_POST)) {
        $invasive_check = strval($_POST["invasive"]);
        if($invasive_check !== "None"){
          $filter = array();
        }
      }

      //FILTERS ITEMS
      if(array_key_exists("habit", $_POST)) {
        $habit_filter = strval($_POST["habit"]);
        if($habit_filter !== "None"){
          for($i = 0; $i <= $numOfPlants-1; $i++){
            if(str_contains($habit[$i], $habit_filter)){
              $filter[$i] = $i;
            }
          }
        }
      }
      $filter_temp = array();
      if(array_key_exists("rarity", $_POST)) {
        $rarity_filter = strval($_POST["rarity"]);
        if($rarity_filter !== "None"){
          if(count($filter) <= 0){
            for($i = 0; $i <= $numOfPlants-1; $i++){
              if(str_contains($rarity[$i], $rarity_filter)){
                $filter_temp[$i] = $i;
              }
            }
          }else{
            foreach($filter as $i){
              if(str_contains($rarity[$i], $rarity_filter)){
                $filter_temp[$i] = $i;
              }
            }
          }
          $filter = $filter_temp;
        }
      }
      $filter_temp = array();
      if(array_key_exists("invasive", $_POST)) {
        $invasive_filter = strval($_POST["invasive"]);
        if($invasive_filter !== "None"){
          if(count($filter) <= 0){
            for($i = 0; $i <= $numOfPlants-1; $i++){
              if($invasive[$i] == $invasive_filter){
                $filter_temp[$i] = $i;
              }
            }
          }else{
            foreach($filter as $i){
              if($invasive[$i] == $invasive_filter){
                $filter_temp[$i] = $i;
              }
            }
          }
          $filter = $filter_temp;
        }
      }

      if(count($filter) == 0){ //NO RESULTS
        $text = "No Results for ". $zip;
        return $text;
      }
      $text = "Results for ". $zip;
      ?>
      <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
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
        <?php $count = 0; 
        foreach($filter as $i) : 
        $count++;
        if($count == 20){
          break;
        }?>
        <?php $pgLink = "https://plants.usda.gov/DocumentLibrary/plantguide/pdf/pg_" . $printSymbol[$i] . ".pdf"; ?>
        <?php $fsLink = "https://plants.usda.gov/DocumentLibrary/factsheet/pdf/fs_" . $printSymbol[$i] . ".pdf"; ?>
        <tr class="<?php echo $i % 2 === 0 ? 'even-row' : 'odd-row'; ?>">
            <td class="multi-line"><?php echo "$printPlants[$i] <br>";?></td>
            <td class="multi-line"><?php echo "$printLatin[$i] <br>";?></td>
            <td class="multi-line"><?php echo "$printHabit[$i] <br>";?></td>
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
            <td><input type="image" src="Front-end/images/heart.png" alt="Favorite" class="heart-button"/></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>


      <?php
      return $text;
}
?>

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
			<li><a href="">Account</a></li>
		</ul>
	</nav>

    <div class="text-boxes"> <!-- Text boxes above zip code box -->
            
        <div class="text-box">
          <p class="work-sans-text">Results</p>
        </div>
        <div class="text-box">
          <p class="source-sans-text">A collection of plants hand-tailored to your exact location.</p>
        </div>
        <button type="button" id="filters" style="background-color: #000000; padding: 10px 20px; color: white" class="button" onclick="showFilters()">Filters</button>
        <form method="post" class="zip-code-box">
            <input type="text" id="zip-code-box-input" placeholder="Enter Zip Code" class="work-sans-text" name="zip" value="">
            
            <div id="hiddenselect" style="display:none;">
            <h1 style="font-size: 15px;">
            <label for="habit">Growth habit filter:</label>
            <select name="habit" id="habit">
            <option value="None">None</option>
            <option value="Forb/herb">Forb/herb</option>
            <option value="Graminoid">Graminoid</option>
            <option value="Lichenous">Lichenous</option>
            <option value="Nonvascular">Nonvascular</option>
            <option value="Shrub">Shrub</option>
            <option value="Subshrub">Subshrub</option>
            <option value="Tree">Tree</option>
            <option value="Vine">Vine</option>
            </select>
            <br>
            <label for="rarity">Rarity filter:</label>
            <select name="rarity" id="rarity">
            <option value="None">None</option>
            <option value="Endangered">Endangered</option>
            <option value="Threatened">Threatened</option>
            </select>
            <br>
            <label for="invasive">Invasive filter:</label>
            <select name="invasive" id="invasive">
            <option value="None">None</option>
            <option value="Invasive">Invasive</option>
            <option value="Potentially Invasive">Potentially Invasive</option>
            </select>
            </h1>
            </div>

            <button type="submit" style="margin-left: 10px" name="recommend_button" class="button work-sans-text">GO!</button>

        </form>
        <div class="text-box">
        <br><br><br><br><br><br>
          <p class="work-sans-text"><?php echo $text; ?></p>
        </div>
        </div>
        
    </div>
</div>


</div>


</body>
</html>