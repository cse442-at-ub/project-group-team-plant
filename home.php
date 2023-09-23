<!DOCTYPE html>
<html>
<body>
<h1>Team Plant Home Page</h1><br>



Input a zip-code, soil-composition and/or elevation below then press the button to be recommended<br>
plants based on the inputs, if an input is left blank then that input will not be apart of the search.<br><br>
Zip-code: <input type="text" name="zip" value="<?php echo $zip;?>"><br><br>

Soil-composition: <input type="text" name="soil" value="<?php echo $soil;?>"><br><br>

Elevation: <input type="text" name="elev" value="<?php echo $elev;?>"><br><br>

<form method="post">
    <input type="submit" name="recommend_button"
            class="button" value="Search Recommendations" />
</form>

</body>
</html>
