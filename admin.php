<!DOCTYPE html>
<html lang="en">

<?php 
require_once 'connect.php';
global $db;
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] != 0) {
  header("Location: login.php");
  exit;
}
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
      <h2>Admin</h2>
    </div>
  </div>

  <div class="row">
	<div class="col-xs-6 col-md-1 col-md-offset-5 form-group">
	  <form method="post" action="reset.php">
		<input class="btn btn-danger btn-sm align-right" type="submit" value="Reset Scores">
  	  </form>
	</div>
	<div class="col-xs-6 col-md-1 form-group">
	  <form method="post" action="logout.php">
		<input class="btn btn-danger btn-sm" type="submit" value="Logout">
  	  </form>
	</div>
  </div>
  
  <div class="row">
    <div class="col-xs-12 col-md-6 col-md-offset-3 center">
  	  <ul class="nav nav-tabs nav-tabs-center">
        <li class="active"><a data-toggle="tab" href="#users">Users</a></li>
        <li><a data-toggle="tab" href="#scores">High Scores</a></li>
      </ul>

      <div class="tab-content">
        <div id="users" class="tab-pane fade in active">
          <?php
            $query = "SELECT u.user_id, u.role FROM user u ORDER BY u.user_id ASC";
            $colNames = ["#", "Username", "Role"];
            createDynamicTable($query, $colNames, $db, 1);
          ?>
        </div>
        <div id="scores" class="tab-pane fade">
          <?php
            $query = "SELECT s1.graph_id, s1.user_id, s1.num_matched, s1.score, s1.date_created FROM solution s1 WHERE s1.num_matched >= ALL(SELECT s2.num_matched FROM solution s2 WHERE s1.graph_id = s2.graph_id) AND s1.score >= ALL(SELECT s2.score FROM solution s2 WHERE s1.graph_id = s2.graph_id AND s1.num_matched=s2.num_matched) AND s1.date_created <= ALL(SELECT s2.date_created FROM solution s2 WHERE s1.graph_id = s2.graph_id AND s1.num_matched=s2.num_matched AND s1.score=s2.score) ORDER BY s1.graph_id;";
            $colNames = ["#", "Graph ID", "Username", "No. of Matches", "Score", "Date"];
            createDynamicTable($query, $colNames, $db, -1);
          ?>
        </div>
      </div>
    </div>
  </div>
</div>

  <script src="http://code.jquery.com/jquery-2.2.0.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>

<?php
  function createDynamicTable($query, $colNames, $db, $roleCol) {
  	echo '<div class="table-responsive">
      	    <table class="table">
             <thead>
          	   <tr>';
          foreach($colNames as $colName) {
          	echo '<th class="center">' . $colName . '</th>';
          }
    echo '</tr>
     	</thead>
        <tbody>';

    $i = 0;
	$res = $db->query($query);

	if (!$res) exit("There is a MySQL error, exiting this script");
  	while ($r = mysqli_fetch_row($res)) { // important command
  	  $i++;
  	  echo "<tr><td>" . $i . "</td>"; // echo first column
      for ($j = 0; $j < count($r); $j++) {
      	$content = $r[$j];
      	if ($j === $roleCol) {
      		if ($r[$j] == 0) {
      			$content = "Admin";
      		} else {
      			$content = "Student";
      		}
      	}
      	echo "<td>" . $content . "</td>";
      }
   	  echo "</tr>";
  	}
        
    echo '</tbody>
      </table>
    </div>';
  }
?>