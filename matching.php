<?php
require_once 'connect.php';
global $db;
session_start();

$graph = [
	["N"=>2,"M"=>2,"E"=>[[11,2,30],[0,8,98],[0,2,20]]],
	["N"=>2,"M"=>2,"E"=>[[6,9,96],[6,4,10],[10,9,10],[10,4,80]]],
	["N"=>3,"M"=>2,"E"=>[[5,1,35],[3,6,63],[8,1,23],[8,6,16]]],
	["N"=>3,"M"=>4,"E"=>[[11,0,30],[11,11,16],[11,5,33],[7,8,98],[7,0,23],[4,0,89],[4,5,20]]],
	["N"=>4,"M"=>5,"E"=>[[8,2,27],[8,0,75],[1,2,10],[1,9,83],[2,2,46],[2,10,13],[2,1,35],[9,10,50],[9,9,26]]],
	["N"=>5,"M"=>6,"E"=>[[5,5,63],[5,0,53],[9,7,57],[9,8,49],[9,11,53],[4,0,96],[4,7,37],[4,8,16],[3,6,65],[3,5,13],[6,11,50]]],
	["N"=>6,"M"=>7,"E"=>[[11,0,96],[11,8,10],[11,3,76],[10,0,47],[10,8,13],[10,10,26],[9,7,47],[9,3,43],[9,1,36],[8,5,21],[5,5,67],[5,1,33],[4,0,91],[4,7,24],[4,10,42]]
	],
	["N"=>8,"M"=>8,"E"=>[[3,10,13],[3,5,25],[3,8,34],[6,6,67],[6,9,80],[6,11,76],[5,10,33],[5,2,17],[5,8,24],[9,4,45],[9,9,67],[0,2,13],[2,5,25],[2,11,39],[1,2,14],[1,6,78],[7,8,95]]],
	["N"=>10,"M"=>10,"E"=>[[11,1,24],[11,3,36],[11,8,17],[10,0,48],[10,4,92],[9,2,58],[9,9,64],[9,7,57],[8,5,26],[7,1,27],[7,8,98],[7,7,34],[6,3,7],[6,10,9],[4,5,12],[4,7,15],[3,2,8],[3,4,11],[2,8,26],[2,10,63],[1,1,17],[1,8,78]]]
];	

if (is_ajax()) { 
	if (isset($_POST["cmd"]) && !empty($_POST["cmd"])) {
		$cmd = $_POST["cmd"];
    	switch($cmd) {
      		case "generate":
      			if (isset($_POST["graph_id"]) && !empty($_POST["graph_id"])) {
      				$graph_id = (int)$_POST["graph_id"];
      			}

      			echo json_encode($graph[$graph_id - 1]);
      			break;
      		case "submit":
      			if (isset($_POST["graph_id"]) && !empty($_POST["graph_id"])) {
      				$graph_id = (int)$db->escape_string($_POST["graph_id"]);
      			}

      			if (isset($_POST["solution"]) && !empty($_POST["solution"])) {
      				$user_solution = json_decode($_POST["solution"]);
      			}

      			$graph_id = $graph_id - 1;

      			$score = verifySolution($graph_id, $graph, $user_solution);

				$data = [];
				$data["graph_id"] = $graph_id;
				$data["new_best"] = 0;
				$data["num_match"] = count($user_solution);
				$data["match_score"] = $score;

				if ($score < 0) {
					echo "Error: Invalid matching";
				} else {
				    $query = "SELECT S1.NUM_MATCHED, MAX(S1.SCORE) FROM SOLUTION S1 WHERE S1.GRAPH_ID = ". $graph_id ." AND S1.NUM_MATCHED >= ALL(SELECT S2.NUM_MATCHED FROM SOLUTION S2 WHERE S1.GRAPH_ID = S2.GRAPH_ID) GROUP BY S1.NUM_MATCHED;";
				    $res = $db->query($query);

				    if (!$res) exit("There is a MySQL error, exiting this script");

				    $r = mysqli_fetch_row($res);
				    $best_num_match = (int)$r[0];
				    $best_score = (int)$r[1];

				    if (count($user_solution) > $best_num_match) {
				    	$data["new_best"] = 1;
				    } else if (count($user_solution) == $best_num_match && $score > $best_score) {
				    	$data["new_best"] = 1;
				    } else {
				    	$data["num_match"] = $best_num_match;
				    	$data["match_score"] = $best_score;
				    }

				    $user_id;
				    $password;

				    
				    if (isset($_POST["user_id"]) && !empty($_POST["user_id"])) {
      					$user_id = $db->escape_string($_POST["user_id"]);
      				}

      				if (isset($_POST["password"]) && !empty($_POST["password"])) {
      					$password = $db->escape_string($_POST["password"]);
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
						  			$query = "INSERT INTO SOLUTION (GRAPH_ID, USER_ID, NUM_MATCHED, SCORE, DATE_CREATED) VALUES (" . $graph_id . ",'" . $user_id . "'," . count($user_solution) . "," . $score . ", NOW());";
				    				$res = $db->query($query);
				    				if ($res) {
				    					$data["added"] = 1;
				    					$_SESSION["user_id"] = $user_id;
         	  							$_SESSION["role"] = $role;
				    				}
						  		}
						    }
						}
				    }

				    echo json_encode($data);
				    $db->close();
				}
      			
      			break;
    	}
	}
}

function is_ajax() {
	return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function generate($N,$M) {
	$result = array("N"=>$N, "M"=>$M);
	$edgeList = array();

	$max = max($N, $M);

	for ($i = 0; $i < $N; $i++) {
		$randomMatch = mt_rand(0, $M - 1);
		$weight = mt_rand(1, 100);
		$mandatoryEdge = array($i, $randomMatch, $weight);

		for ($j = 0; $j < $M; $j++) {
			if ($j !== $randomMatch) {
				if (mt_rand(0, $max + 1) < 1) {
					$weight = mt_rand(1, 100);
					$edge = array($i, $j, $weight);
					$edgeList[] = $edge;
				}
			} else {
				$edgeList[] = $mandatoryEdge;
			}
		}
	}

	$result["E"] = $edgeList;

	return $result;
}

function verifySolution($graph_id, $graph, $solution) {
	$graphScheme = $graph[$graph_id]["E"];
	$prevLeft = -1;
	$prevRight = -1;
	$score = 0;

	foreach ($solution as $s) {
		$left = $s[0];
		$right = $s[1];
		$matchIsFound = false;

		for ($i = 0; $i < count($graphScheme); $i++) {
			if ($prevLeft === $graphScheme[$i][0]) {
				$graphScheme[$i][0] = -1;
			}

			if ($prevRight === $graphScheme[$i][1]) {
				$graphScheme[$i][1] = -1;
			}

			$leftIsMatch = $left === $graphScheme[$i][0];
			$rightIsMatch = $right === $graphScheme[$i][1];

			if ($leftIsMatch && $rightIsMatch) {
				$prevLeft = $graphScheme[$i][0];
				$prevRight = $graphScheme[$i][1];
				$matchIsFound = true;
				$score += $graphScheme[$i][2];
			}
		}

		if (!$matchIsFound) {
			return -1;
		}	
	}

	return $score;
}
?>