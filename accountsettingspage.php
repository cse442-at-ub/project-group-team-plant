<!DOCTYPE html>
<head>
  <title>Account Settings Page</title>
</head>
<body>
  <h1><b>Change Username/Password</h1></b>
  <em><h4>To change your username or password, please enter your current information then
  your new username and/or password.</h4></em>

  <br><br>

  Current Username: <input type="text" name="current_user" value="<?php echo $current_user;?>">

  <br><br>

  Current Password: <input type="text" name="current_pass" value="<?php echo $current_pass;?>">

  <br><br>

  New Username: <input type="text" name="new_user" value="<?php echo $new_user;?>">

  <br><br>

  New Password: <input type="text" name="new_pass" value="<?php echo $new_pass;?>">

  <br><br>

  <form action="runPHPhere.php">
    <input type="Submit" name="Submit" value="Submit" onclick="runPHP()" />
  </form>

</body>
</html>
