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

  <?php

  $ret = array();
  $numOfPlants = 0;
  $printPlants = array();
  $printLatin = array();
  $printSymbol = array();
  $printHabit = array();
  $new_output = null;



  if(array_key_exists("recommend_button", $_POST)) {
    $ret = zip_search();
  }
  $fips = "";
  $state_id = "";
  function zip_search(){
      $plants = array(); //COMMON NAME
      $latin = array(); //SCIENTIFIC NAME
      $symbol = array(); //SYMBOL
      $habit = array(); //GROWTH HABIT
      $zip = strval($_POST['zip']);
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
          print("The zipcode was not valid");
          return;
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
  }else{ //INVALID ZIPCODE
          print("The zipcode was not valid");
          return;
      }
      if (($growth_search = fopen("data/growth_habit.csv", "r")) !== FALSE) { //FIND GROWTH HABIT OF PLANTS
    while (($g_csv = fgetcsv($growth_search, 100000, ",")) !== FALSE) {
              for($i = 0; $i <= count($plants); $i++){
                  if($g_csv[0] == $symbol[$i]){ //SYMBOL MATCH
                      if(array_key_exists($i, $habit)){
                          $habit[$i] = $habit[$i] . ", " . $g_csv[4];
                          $printHabit[$i] = $printHabit[$i] . ", " . $g_csv[4];
                      }else{
                          $habit[$i] = $g_csv[4];
                          $printHabit[$i] = $g_csv[4];
                      }
                  }
              }
    }
  fclose($growth_search);
  }
      if(count($plants) == 0){ //NO RESULTS
          print_r("No results found");
          return;
      }
      $numOfPlants = count($plants);
      ?>
      <table>
        <tr>
          <td>COMMON NAME </td>
          <td>SCIENTIFIC NAME </td>
          <td>SYMBOL </td>
          <td>GROWTH HABIT </td>
          <td>PDF LINK </td>
        </tr>
        <tr>
          <?php foreach ($printPlants as $value) : ?>
          <td><?php echo "$value <br>";?></td>
          <?php endforeach; ?>
          <?php foreach ($printLatin as $value) : ?>
          <td><?php echo "$value <br>";?></td>
          <?php endforeach; ?>
          <?php foreach ($printSymbol as $value) : ?>
          <td><?php echo "$value <br>";?></td>
          <?php endforeach; ?>
          <?php foreach ($printHabit as $value) : ?>
          <td><?php echo "$value <br>";?></td>
          <?php endforeach; ?>
          <td><?php echo "nothing"; ?></td>
        </tr>
      </table>


      <?php
      //$num = 365;
      //print("COUNT:" . count($plants));
      //print($plants[$num].", ".$latin[$num].", ".$symbol[$num].", ".$habit[$num]);
      return array($plants, $latin, $symbol, $habit); //RETURN COMMON & SCIENTIFIC
}
/*//flatten array
$finalArray = array();
foreach(range(1,$numOfPlants) as $index){
  $newPlant = $ret[0][$index];
  $newLatin = $ret[1][$index];
  $newSymbol = $ret[2][$index];
  $newHabit = $ret[3][$index];
  if(empty($finalArray)){
    $finalArray = array($newPlant, $newLatin, $newSymbol, $newHabit);
  }else{
    array_push($finalArray, )
  }
}
*/


?>


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
