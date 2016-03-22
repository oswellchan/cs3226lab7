<!DOCTYPE html>
<html lang="en">

<?php 
require_once 'connect.php';
global $db;
session_start();
?>

<head>
  <meta charset="utf-8">
  <title>Interactive Matching Activity</title>
  <link rel="stylesheet" href="css/style.css">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
</head>

<body>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-md-6 col-md-offset-3 center">
      <h1>Signup</h1>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-md-6 col-md-offset-3 center">
	  <form class="form-group" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	    <p>Username: <input name="user_id" type="text"></p>
		  <p>Password: <input name="password" type="password"></p>
      <p>Confirm Password: <input name="confirm_password" type="password"></p>
		  <input class="btn btn-primary btn-sm" type="submit" value="Submit">
	  </form>
	</div>
  </div>

  <?php
  	$user_id;
  	$password;
    $confirm_password;
  	$msg;
    $isMatched = false;

  	if (isset($_POST["user_id"]) && !empty($_POST["user_id"])) {
      $user_id = $db->escape_string($_POST["user_id"]);
      $msg = "Please fill in username";
	  }

    if (isset($_POST["password"]) && !empty($_POST["password"])) {
      $password = $db->escape_string($_POST["password"]);
      $msg = "Please fill in password";
    }

    if (isset($_POST["confirm_password"]) && !empty($_POST["confirm_password"])) {
      $confirm_password = $db->escape_string($_POST["confirm_password"]);
      $msg = "Please comfirm your password";
    }

    if (isset($password) && isset($confirm_password)) {
      if ($password === $confirm_password) {
        $isMatched = true;
      } else {
        $msg = "The two passwords do not match. Please reenter your password";
      }
    }

    if (isset($user_id) && $isMatched) {
      $salt = bin2hex(openssl_random_pseudo_bytes(120));

      $query = 'INSERT INTO USER VALUES ("'. $user_id .'", "' . crypt($password, $salt) . '", "' . $salt . '", 1)';
      $res = $db->query($query);
      $msg = "Account created! Play the game <a href='index.php'>now</a>!";  
    }
  ?>

  <?php if (isset($msg)) { ?>
  <div class="row">
    <div class="col-xs-12 col-md-6 col-md-offset-3 center">
    	<?php echo $msg ?>
    </div>
  </div>
  <?php } ?>
 </div>		      	 
  <script src="http://code.jquery.com/jquery-2.2.0.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>