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
      <h1>Login</h1>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 col-md-6 col-md-offset-3 center">
	  <form class="form-group" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
	    <p>Username: <input name="user_id" type="text"></p>
		  <p>Password: <input name="password" type="password"></p>
		  <input class="btn btn-primary btn-sm" type="submit" value="Submit">
	  </form>
	</div>
  </div>

  <?php
  	$user_id;
  	$password;
  	$msg;

  	if (isset($_POST["user_id"]) && !empty($_POST["user_id"])) {
      $user_id = $db->escape_string($_POST["user_id"]);
	  }

    if (isset($_POST["password"]) && !empty($_POST["password"])) {
      $password = $db->escape_string($_POST["password"]);
    }

    if (!isset($user_id) || !isset($password)) {
      $msg = "Please fill in username and password.";
    }

    if (isset($user_id) && isset($password)) {
      $query = 'SELECT u.hashed_password, u.salt, u.role FROM user u WHERE u.user_id = "' . $user_id . '";';
	  $res = $db->query($query);

	  if ($res) {
	  	if ($r = mysqli_fetch_row($res)) {
	  	  $hashed_password = $r[0];
	  	  $salt = $r[1];
	  	  $role = $r[2];
	  	  $currentHashed = crypt($password, $salt);
          if ($currentHashed === $hashed_password) {
          	if ($role == 0) {
         	  $_SESSION["user_id"] = $user_id;
         	  $_SESSION["role"] = $role;
          	  header("Location: admin.php");
          	  exit;
          	} else {
              $msg = "You do not have admin rights.";
          	}
          } else {
          	$msg = "Username or password is wrong. Please try again.";
          }
	  	} else {
          $msg = "Username or password is wrong. Please try again.";
        }
	  }
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