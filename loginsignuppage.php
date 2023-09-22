<!DOCTYPE html>
<head>
  <title>Login/Sign-Up Page</title>
</head>
<body>
  <h1><b>Login or Sign-up to our website.</h1></b>
  <em><h4>To login, fill out the fields under the login section and then click the button login to complete.
  To sign-up, fill our the fields for a new username and new password under the sign-up section and use the button to complete.</h4></em>

  <br><br>

  <h4><b>LOGIN</b></h4><br>

  Username: <input type="text" name="current_user" value="<?php echo $current_user;?>">

  <br><br>

  Password: <input type="text" name="current_pass" value="<?php echo $current_pass;?>">

  <br><br>

  <form action="runPHPhere.php">
    <input type="Submit" name="LOGIN" value="LOGIN" onclick="runPHP()" />
  </form>

  <br><br>

  <h4><b>SIGN-UP</b></h4><br>

  New Username: <input type="text" name="new_user" value="<?php echo $new_user;?>">

  <br><br>

  New Password: <input type="text" name="new_pass" value="<?php echo $new_pass;?>">

  <br><br>

  <form action="runPHPhere.php">
    <input type="Submit" name="SIGN-UP" value="SIGN-UP" onclick="runPHP()" />
  </form>

</body>
</html>
