<!DOCTYPE html>
<head>
  <title>Account Settings Page</title>
</head>
<body>
  <h1><b>Change Username/Password</h1></b>
  <em><h4>To change your username or password, please enter your current information then
  your new username and/or password.</h4></em>
    <br>
    <?php
    $server = "oceanus.cse.buffalo.edu";
    $user = "sepalutr";
    $pass = "50338448";
    $dbname = "cse442_2023_fall_team_k_db";

    $conn = new mysqli($server, $user, $pass, $dbname);

    if ($conn->connection_error) {
        die("Connection failed: " . $conn->connection_error);
    }

    if(array_key_exists("signup", $_POST)) {
        signup($conn);
    }else if(array_key_exists("login", $_POST)) {
        login($conn);
    }else if(array_key_exists("signout", $_POST)) {
        signout($conn);
    }else {
        if(isset($_COOKIE['username'])){
            if(verify_cookie($conn, $_COOKIE['username'], $_COOKIE['password'])){
                echo "Signed in as " . $_COOKIE['username'];
                login_account($conn,$_COOKIE['username'],$_COOKIE['password']);
            }else{
                echo "Invalid authentication cookie.";
                setcookie ("username", $username, time()-(60*60), '/');
                setcookie ("password", $password, time()-(60*60), '/');
            }
        }
    }

    function change_username($conn, $username, $password){
      $username = strval($_POST['current_user']);
      $password = strval($_POST['current_pass']);
      $newusername = strval($_POST['new_user']);
  
      if ($newusername == ""){
              echo "Invalid input, please enter a different username.";
              return;
      }
  
      if ($newusername == $username){
            echo "Invalid input, please enter a different username.";
            return;
      }
  
      $stmt = $conn->prepare("UPDATE accounts SET username=? WHERE username=?");
      $stmt->bind_param("ss", $newusername, $username);
      $stmt->execute();
  
      $stmt->close();
    }

    ?>

    <h4><b>Change Username</b></h4><br>

    <form method="post">
    Current Username: <input type="text" name="current_user">
    <br><br>
    Current Password: <input type="text" name="current_pass">
    <br><br>
    New Username: <input type="text" name="new_user">
    <br><br>
      <input type="submit" name="change_username" class="button" value="Change Username"/>
    </form>
  
    <br><br>
  
    <h4><b>Change Password</b></h4><br>
  
    <form method="post">
    Current Username: <input type="text" name="current_user">
    <br><br>
    Current Password: <input type="text" name="current_pass">
    <br><br>
    New Password: <input type="text" name="new_pass">
    <br><br>
      <input type="submit" name="change_password" class="button" value="Change Password"/>
    </form>

</body>
</html>