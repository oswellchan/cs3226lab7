<!DOCTYPE html>
<html lang="en">

<?php session_start();?>

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
			<div class="col-xs-12 col-md-6 col-md-offset-3">
				<div class="center"><p><h3>Score: <span id="score">0</span> Match: <span id="matches">0</span></h3></p></div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12 col-md-6 col-md-offset-3">
				<div class="center"><p>Message: <span id="msg"></span></p></div>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12 col-md-8 col-md-offset-2">
				<table id="tbl">
				</table>
			</div>
		</div>

		<!-- Button trigger modal -->
		<div class="row">
			<div class="col-xs-12 col-md-6 col-md-offset-3 center form-group">
	  			<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#myModal">
		  		Reset
				</button>
				<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#submitModal">
		  		Submit
				</button>
	  		</div>
		</div>

		<div class="row">
			<div class="col-xs-6 col-md-1 col-md-offset-5 form-group">
				<form method="post" action="signup.php">
					<input class="btn btn-success btn-sm align-right" type="submit" value="Student Signup">
  				</form>
			</div>
			<div class="col-xs-6 col-md-1 form-group">
				<form method="post" action="login.php">
					<input class="btn btn-danger btn-sm" type="submit" value="Admin Login">
  				</form>
			</div>
		</div>

		<div class="row">
			<div class="col-xs-12 col-md-6 col-md-offset-3">
	  			<p class="center">All images <a href="http://www.freepik.com">Designed by Freepik</a></p>
	  		</div>
		</div>

		<!-- Modal Reset -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		     	<h4><b>Generate</b></h4>
		      </div>
		      <div class="modal-body">
		      		<p>Generate new worksheet with</p>
					<b>Graph Id</b> = 
					<select id="graph">
						<option value="1">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
					</select>
		      </div>
		      <div class="modal-footer">
		      	<button type="button" class="btn btn-primary" id="btn" data-dismiss="modal">Generate</button>
		      </div>
		    </div>
		  </div>
		</div>

		<!-- Modal Submit -->
		<div class="modal fade" id="submitModal" tabindex="-1" role="dialog" aria-labelledby="submitModalLabel">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		     	<h4><b>Login</b></h4>
		      </div>
		      <div class="modal-body">
		      	<p>Please enter account details if you wish to register for high score. Leave the fields blank otherwise.</p>
		      	<p>Username: <input id="user_id" type="text"></p>
		   		<p>Password: <input id="password" type="password"></p>
		      </div>
		      <div class="modal-footer">
		      	<button type="button" class="btn btn-primary" id="submit" data-dismiss="modal">Submit</button>
		      </div>
		    </div>
		  </div>
		</div>

	<script src="http://code.jquery.com/jquery-2.2.0.min.js"></script>
		<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
	<script src="js/script.js"></script>
</body>
</html>