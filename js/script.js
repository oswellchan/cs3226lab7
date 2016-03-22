var isMouseDown = false;
var firstMouseX = -1;
var firstMouseY = -1;
var prevMouseX = -1;
var prevMouseY = -1;
var match = new Array();
var lastState = null;
var graph;
var jsonGraph;
var visibleGraph;
var score = 0;

$( document ).ready(function() {
	var array1 = ["cat.png", "cheetah.png", "cow.png", "dog.png", "donkey.png", "elephant.png", "fox.png", "linux.png", "moose.png", "pig.png", "rabbit.png", "sheep.png"];
	var array2 = ["apple.png", "banana.png", "brocolli.png", "cabbage.png", "carrot.png", "cheese.png", "drum.png", "egg.png", "fish.png", "meat.png", "mushroom.png", "sausage.png"];
	var arrayLeft = array1;
	var arrayRight = array2;

	var error_msg_mismatch = "The two pictures do not match! Please try again.";
	var error_msg_same_row = "The two pictures to be matched must be from different rows!";
	var best_score_msg = "<b>Congratulations!</b> You have the best score!"
	var weak_score_msg = "Your score was not good enough."
	var start_msg = "Let's Start! Good Luck.";
	var success_msg = "Congratulations!";
	var win_msg = "Congratulations! You Won!"
	var fail_msg = "Game Over! You lost. Try Again.";

	var challenge1 = $("#tbl");
	var msg = $("#msg");
	var scoreDisplay = $("#score");

	var clickedElement = null;

	var n = 2;
	var m = 2;
	var count = -1;
	var numMatches = -1;

	var canvas = null;

	msg.html(start_msg);
	resetTable(challenge1);
		
	score = 0;
	count = 0;
	match = new Array();

	var max = Math.max(n, m);
	for (i = 0; i < max; i++) {
		match.push(-1);
	}

	var matchList = new Array();

	var data = {
		"cmd": "generate",
		"graph_id" : 1
	};

	$.ajax({
		type: "POST",
		dataType: "json",
		url: "matching.php",
		data: data,
		success: function(data) {
			var n = data["N"];
			var m = data["M"];

			msg.html(start_msg);
			resetTable(challenge1);

			jsonGraph = data;
			var graph_data = generateGraph(jsonGraph["E"], parseInt(n), parseInt(m));
			graph = graph_data[0];
			visibleGraph = graph_data[1];
			generateChallengeWithJSON(challenge1, visibleGraph, graph, arrayLeft, arrayRight);
			scoreDisplay.html(score);
			$("#matches").html(count);
		}
	});
	
	if (true) {
		$('body').click(function(e) {
			if (e.target === null) {
				return;
			}

			//disable clicking when already matched
			if ($(e.target).parent().attr("class") === "mytd doneLeft" || $(e.target).parent().attr("class") === "mytd doneRight") {
				return;
			}

			if (clickedElement === null) {
				if (e.target.className === "imgLeft") {
					$(e.target).parent().addClass("selectedLeft");
					$(e.target).parent().removeClass("left");
					clickedElement = $(e.target);
				}

				if (e.target.className === "imgRight") {
					$(e.target).parent().addClass("selectedRight");
					$(e.target).parent().removeClass("right");
					clickedElement = $(e.target);
				}
			} else {
				if (e.target.className === "imgLeft") {
					if (clickedElement.attr('class') === "imgLeft") {

						msg.html(error_msg_same_row);

						clickedElement.parent().addClass("left");
						clickedElement.parent().removeClass("selectedLeft");
						clickedElement = null;
					}

					if (clickedElement!== null && clickedElement.attr('class') === "imgRight") {
						var leftIndex = parseInt($(e.target).attr("row"));
						var rightIndex = parseInt(clickedElement.attr("row"));
						var value = graph[leftIndex][rightIndex];
						if (value !== undefined) {
							clickedElement.parent().addClass("doneRight");
							clickedElement.parent().removeClass("selectedRight");
							$(e.target).parent().addClass("doneLeft");
							$(e.target).parent().removeClass("left");
							
							match[$(e.target).attr("row")] = clickedElement.attr("row");
							clickedElement = null;
							score += value;
							count++;

							matchList.push([visibleGraph[0][leftIndex], visibleGraph[1][rightIndex]]);

							msg.html(success_msg);
							$("#matches").html(count);
							scoreDisplay.html(score);

							drawLines(match);
							if (count == n) {
								msg.html(win_msg);
							}
						} else {
							clickedElement.parent().addClass("right");
							clickedElement.parent().removeClass("selectedRight");
							clickedElement = null;

							msg.html(error_msg_mismatch);
						}
					}
				}

				if (e.target.className === "imgRight") {
					if (clickedElement.attr('class') === "imgLeft") {
						var leftIndex = parseInt(clickedElement.attr("row"));
						var rightIndex = parseInt($(e.target).attr("row"));
						var value = graph[leftIndex][rightIndex];
						if (value !== undefined) {
							clickedElement.parent().addClass("doneLeft");
							clickedElement.parent().removeClass("selectedLeft");
							$(e.target).parent().addClass("doneRight");
							$(e.target).parent().removeClass("right");
							
							match[clickedElement.attr("row")] = $(e.target).attr("row");
							clickedElement = null;
							score += value;
							count++;

							matchList.push([visibleGraph[0][leftIndex], visibleGraph[1][rightIndex]]);

							msg.html(success_msg);
							$("#matches").html(count);
							scoreDisplay.html(score);

							drawLines(match);
							if (count == n) {
								msg.html(win_msg);
							}
						} else {
							clickedElement.parent().addClass("left");
							clickedElement.parent().removeClass("selectedLeft");
							clickedElement = null;

							msg.html(error_msg_mismatch);
						}
					}

					if (clickedElement!== null && clickedElement.attr('class') === "imgRight") {

						msg.html(error_msg_same_row);

						clickedElement.parent().addClass("right");
						clickedElement.parent().removeClass("selectedRight");
						clickedElement = null;
					}
				}
			}
		});
	}

	$("#submit").click(function() {
		var graph_id = $("#graph").val();
		var user_id = $("#user_id").val();
		var password = $("#password").val();

		$("#user_id").val("");
		$("#password").val("");

		var data = {
			"cmd": "submit",
			"graph_id" : graph_id,
			"solution": JSON.stringify(matchList),
			"user_id": user_id,
			"password": password
		};

		var allLeft = $(".left");
		for (i = 0; i < allLeft.length; i++) {
			$(allLeft[i]).removeClass("left");
			$($(allLeft[i]).children(".imgLeft")).removeClass("imgLeft");
		}

		var allRight = $(".right");
		for (i = 0; i < allRight.length; i++) {
			$(allRight[i]).removeClass("right");
			$($(allRight[i]).children(".imgRight")).removeClass("imgRight");
		}

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "matching.php",
			data: data,
			success: function(data) {
				console.log(data);
				var new_best = data["new_best"];
				var num_match = data["num_match"];
				var match_score = data["match_score"];
				var isAdded = data["added"];

				var feedback;

				if (new_best === 1) {
					feedback = best_score_msg;
				} else {
					feedback = weak_score_msg;
				}

				feedback += (' The <b class="blue">best answer(s)</b> has <b>' + data["num_match"] + '</b> matches and a score of <b>' + data["match_score"] + '</b>.');

				if (isAdded == 1) {
					feedback += ( 'Your answer has been recorded.');
				}

				msg.html(feedback);
			}
		});
	});

	$("#btn").click(function() {
		var graph_id = $("#graph").val();

		score = 0;
		count = 0;
		match = new Array();
		matchList = new Array();
		var data = {
			"cmd": "generate",
			"graph_id": graph_id
		};

		$.ajax({
			type: "POST",
			dataType: "json",
			url: "matching.php",
			data: data,
			success: function(data) {
				var n = data["N"];
				var m = data["M"];

				var max = Math.max(n, m);

				for (i = 0; i < max; i++) {
					match.push(-1);
				}

				resetTable(challenge1);

				jsonGraph = data;
				var graph_data = generateGraph(jsonGraph["E"], parseInt(n), parseInt(m));
				graph = graph_data[0];
				visibleGraph = graph_data[1];
				generateChallengeWithJSON(challenge1, visibleGraph, graph, arrayLeft, arrayRight);
				
				scoreDisplay.html(score);
				msg.html(start_msg);
			}
		});
	});
});



function resetTable(challenge) {
	challenge.html("");
}

function generateRandomArrayPositions(n) {
	var output = [];

	var possibleValues = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11];

	for (var i = 0; i < n; i++) {
		var random = Math.floor(Math.random()*possibleValues.length);

		if (random === possibleValues.length) {
			random--;
		}

		output.push(possibleValues[random]);
		possibleValues.splice(random, 1);
	}

	return output;
}

function generateInputArray(arrayPositions, arrayInput) {
	var output = [];
	for (var i = 0; i < arrayPositions.length; i++) {
		output.push(arrayInput[arrayPositions[i]]);
	}

	return output;
}

function generateChallenge(challenge, category1, category2) {

	var array1 = [];
	var array2 = [];
	var answer = [];

	var htmlString = "";

	for(i=0; i < category1.length; i++) {
		array1.push(i);
		array2.push(i);
	}

	for(i = 0; i < category1.length - 2; i++) {

		var randomResult1 = -1;
		var randomResult2 = -1;

		var random1 = -1;
		var random2 = -1;

		while (randomResult1 === randomResult2) {
		 	random1 = Math.floor(Math.random()*array1.length);
		 	random2 = Math.floor(Math.random()*array2.length);

			if (random1 === array1.length) {
				random1--;
			}

			if (random2 === array2.length) {
				random2--;
			}

			randomResult1 = array1[random1];
			randomResult2 = array2[random2];
		}

		if (i === 0) {
			appendFirstRow(challenge, randomResult1, category1[randomResult1], randomResult2, category2[randomResult2], category1.length);
		} else {
			appendRow(challenge, randomResult1, category1[randomResult1], randomResult2, category2[randomResult2], i);	
		}

		answer[i] = 

		array1.splice(random1, 1);
		array2.splice(random2, 1);
	}

	if (array1[0] === array2[0] || array1[1] === array2[1]) {
		appendRow(challenge, array1[0], category1[array1[0]], array2[1], category2[array2[1]], category1.length - 2);
		appendRow(challenge, array1[1], category1[array1[1]], array2[0], category2[array2[0]], category1.length - 1);
	} else {
		appendRow(challenge, array1[0], category1[array1[0]], array2[0], category2[array2[0]], category1.length - 2);
		appendRow(challenge, array1[1], category1[array1[1]], array2[1], category2[array2[1]], category1.length - 1);
	}

	setTimeout(function(){
		$("#canvasCol").append(setCanvas($("#canvasCol").width(), $("#canvasCol").height()));
		var canvas = document.getElementById('cvs');
		lastState = canvas.toDataURL();
    	//canvas.addEventListener('mousemove', mouseMoveHandler, false);
	}, 200);
}

function generateChallengeWithJSON(challenge, table, graph, category1, category2) {

	var maxNum = table[0].length;

	for (i = 0; i < maxNum; i++) {
		var pic1 = null;
		var pic2 = null;
		if (table[0][i] !== undefined) {
			pic1 = category1[table[0][i]];
		}

		if (table[1][i] !== undefined) {
			pic2 = category2[table[1][i]];
		}

		if (i === 0) {
			appendFirstRow(challenge, 0, pic1, 0, pic2, maxNum);
		} else {
			appendRow(challenge, 0, pic1, 0, pic2, i);
		}
	}

	setTimeout(function(){
		$("#canvasCol").append(setCanvas($("#canvasCol").width(), $("#canvasCol").height()));
		var canvas = document.getElementById('cvs');
		var slots = Array();
		for (i = 0; i < maxNum * 2; i++) {
			slots.push(1);
		}

		for (i = 0; i < maxNum; i++) {
			for (j = 0; j < maxNum; j++) {
				var weight = graph[i][j];
				if (weight !== undefined) {
					drawStraightLine(i, j, maxNum, "green", 2);
					fillText(i, j, maxNum, "black", weight, slots);
				}
			}
		}
	}, 200);
}

function generateGraph(edgeList, n, m) {
	var maxNum = Math.max(n, m);
	var ans = createArray(maxNum, maxNum);
	var left = Array();
	var right = Array();
	var visibleGraph = createArray(2, maxNum);
	var leftCount = 0;
	var rightCount = 0;

	for (edge in edgeList) {
		var leftIndex = edgeList[edge][0];
		var rightIndex = edgeList[edge][1];
		if (left[leftIndex] === undefined) {
			left[leftIndex] = leftCount;
			visibleGraph[0][leftCount] = leftIndex;
			leftCount++;
		}

		if (right[rightIndex] === undefined) {
			right[rightIndex] = rightCount;
			visibleGraph[1][rightCount] = rightIndex;
			rightCount++;
		}

		var graphLeftIndex = left[leftIndex];
		var graphRightIndex = right[rightIndex];
		edgeList[edge][0] = graphLeftIndex;
		edgeList[edge][1] = graphRightIndex;

		ans[graphLeftIndex][graphRightIndex] = edgeList[edge][2];
	}

	return [ans, visibleGraph];
}

function getMousePos(canvas, evt) {
    var rect = canvas.getBoundingClientRect();
        return {
        	x: (evt.clientX-rect.left)/(rect.right-rect.left)*canvas.width,
			y: (evt.clientY-rect.top)/(rect.bottom-rect.top)*canvas.height
    };
}

function appendRow(table, value1, pic1, value2, pic2, index) {
	table.append(setPicAtLeftRow(value1, pic1, index) + setPicAtRightRow(value2, pic2, index));
}

function appendFirstRow(table, value1, pic1, value2, pic2, n) {
	table.append(setPicAtLeftRow(value1, pic1, 0) + setPicAtRightRowWithCanvas(value2, pic2, n));
}

function setPicAtLeftRow(value, pic, index) {
	var leftHtml = '<tr><td';
	if (pic !== null) {
		leftHtml += ' class="mytd left"><img draggable="false" data="'+ value +'" row="' + index + '" class="imgLeft" src="img/' + pic + '"';
	}
	return leftHtml  + '></td>';
}

function setPicAtRightRow(value, pic, index) {
	var rightHtml = '<td';
	if (pic !== null) {
		rightHtml += ' class="mytd right"><img draggable="false" data="'+ value +'" row="' + index + '" class="imgRight" src="img/' + pic + '"';
	}
	return rightHtml + '></td></tr>';
}

function setPicAtRightRowWithCanvas(value, pic, n) {
	return '"></td><td id="canvasCol" rowspan="' + n + '"></td><td class="mytd right"><img draggable="false" data="'+ value +'" row="0" class="imgRight" src="img/' + pic + '"></td></tr>';
}

function setCanvas(width, height) {
	return '<canvas id="cvs" width="'+ width +'px" height="'+ height +'px"></canvas>';
}

function drawStraightLine(l, r, n, color, width) {
	var canvas = document.getElementById('cvs');
    var context = canvas.getContext('2d');

    var canvasHeight = $(canvas).height();
    var canvasWidth = $(canvas).width();

    var startX = ((l + 0.5)/n) * canvasHeight;
    var endX = ((parseInt(r) + 0.5)/n) * canvasHeight;

    context.beginPath();
    context.moveTo(0, startX);
	context.lineTo(canvasWidth, endX);
	context.strokeStyle = color;
	context.lineWidth = width;
	context.stroke();
	context.closePath();
}

function drawCurvyLine(l, r, n, color, width) {
	var canvas = document.getElementById('cvs');
    var context = canvas.getContext('2d');

    var canvasHeight = $(canvas).height();
    var canvasWidth = $(canvas).width();

    var startX = ((l + 0.5)/n) * canvasHeight;
    var endX = ((parseInt(r) + 0.5)/n) * canvasHeight;

    context.beginPath();
    context.moveTo(0, startX);
	context.bezierCurveTo(canvasWidth/2, startX, canvasWidth/2, endX, canvasWidth, endX);
	context.strokeStyle = color;
	context.lineWidth = width;
	context.stroke();
	context.closePath();
}

function drawLines(match) {
	for (i = 0; i < match.length; i++) {
		if (match[i] !== -1) {
			drawLine(i, match[i], match.length, "red", 4);
		}
	}
}

function drawLine(l, r, n, color, weight) {
	if (Math.abs(l - r) <= 1) {
		drawStraightLine(l, r, n, color, weight);
	} else {
		drawCurvyLine(l, r, n, color, weight);
	}
}

function fillText(l, r, n, color, text, slots) {
	var canvas = document.getElementById('cvs');
    var context = canvas.getContext('2d');

    var canvasHeight = $(canvas).height();
    var canvasWidth = $(canvas).width();

    var startY = ((l + 0.5)/n) * canvasHeight;
    var endY = ((parseInt(r) + 0.5)/n) * canvasHeight;

    context.font="bold 20px Segoe UI";

    var slotIndex = (r - l) + l * 2;
    var numAlreadyTaken = slots[slotIndex];
    var offsetX = 0;

    if (slotIndex%2 === 0) {
    	offsetX = numAlreadyTaken/2 * canvasWidth*0.15;
    } else {
    	offsetX = -(Math.ceil(numAlreadyTaken/2) * canvasWidth*0.15);
    }

    var gradient = (endY - startY) / canvasWidth;
    var offsetY = gradient * offsetX;

    slots[slotIndex] = numAlreadyTaken + 1;
    context.fillText(text, canvasWidth/2 + offsetX, startY + (endY - startY)/2 + offsetY);
}

function mouseMoveHandler(evt) {

	if (!isMouseDown) {
		return;
	}

	var canvas = document.getElementById('cvs');
    
    if (canvas === null) {
    	return;
    }

    var mousePos = getMousePos(canvas, evt);
    var context = canvas.getContext('2d');

    if (firstMouseX < 0 && firstMouseY < 0) {

    	if (mousePos.x / $(canvas).width() < 0.1  && mousePos.x / $(canvas).width() > 0.9) {
    		return;
    	}

    	firstMouseX = mousePos.x;
    	firstMouseY = mousePos.y;

    	prevMouseX = mousePos.x;
    	prevMouseY = mousePos.y;

    	context.beginPath();
    	context.moveTo(mousePos.x, mousePos.y);
    	return;
    }

    context.lineTo(mousePos.x, mousePos.y);
    context.moveTo(mousePos.x, mousePos.y);
    context.stroke();
    prevMouseX = mousePos.x;
    prevMouseY = mousePos.y;
}

function getElementGivenMousePos(x, y, n, canvas) {
	var array;

	if (x / canvas.width() < 0.1) {
		array = $(".imgLeft");
	} else {
		array = $(".imgRight")
	}

    var index = Math.floor(parseInt(y) / (parseInt(canvas.height())/n));

    if (index === n) {
    	index--;
    }

    return $(array[index]);
}

function detectmob() { 
 if( navigator.userAgent.match(/Android/i)
 || navigator.userAgent.match(/webOS/i)
 || navigator.userAgent.match(/iPhone/i)
 || navigator.userAgent.match(/iPad/i)
 || navigator.userAgent.match(/iPod/i)
 || navigator.userAgent.match(/BlackBerry/i)
 || navigator.userAgent.match(/Windows Phone/i)
 ){
    return true;
  }
 else {
    return false;
  }
}

function createArray(length) {
    var arr = new Array(length || 0),
        i = length;

    if (arguments.length > 1) {
        var args = Array.prototype.slice.call(arguments, 1);
        while(i--) arr[length-1 - i] = createArray.apply(this, args);
    }

    return arr;
}
