<?php

if (!isSet($error)) {
	$error = "";
}
$events = array();
$teams = array();
$inputTeam = false;
$inputEvent = false;
$input = "";

if (isSet($_GET["input"])) {
	$input = $_GET["input"];
}
include_once "config.php";
$showHiddenData = isset($_GET["showHiddenData"]);
if (strtolower($input) == $hiddenDataKey && !$showHiddenData) {
	$input = "";
	$error = "Hidden data now shown.";
	$showHiddenData = true;
} else if (strtolower($input) == $hiddenDataKey && $showHiddenData) {
	$input = "";
	$error = "Hidden data now hidden.";
	$showHiddenData = false;
}

if ($error == "" && $input != "") {
	$eventDirectories = glob('api/v1/*' , GLOB_ONLYDIR);
	foreach($eventDirectories as $fileName) {
		if (explode("/", $fileName)[2] == $input) {
			$inputTeam = false;
			$inputEvent = true;
			$teams = glob('api/v1/'.$input.'/*', GLOB_ONLYDIR);
			break;
		}
		$teamDirectories = glob($fileName."/*", GLOB_ONLYDIR);
		foreach($teamDirectories as $teamFolder) {
			if (explode("/",$teamFolder)[3] == $input) {
				$inputEvent = false;
				$inputTeam = true;
				if (substr(explode("/",$teamFolder)[2],0,4) == $seasonYear) {
					array_push($events, $teamFolder);
				}
			}
		}
	}
	if (count($events) == 0 && count($teams) == 0) {
	$error = "Team number or Event data not found for the current season! There may not be scouting data yet!";
	}
}

include "config.php";
	
function getNameEventCode($code) {	
	global $TBAAuthKey, $TBAApiUrl;
	$urlPrefix = $TBAApiUrl.'/event/';
	$urlSuffix = '/simple';
	
	$url = $urlPrefix.$code.$urlSuffix;
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-TBA-Auth-Key: '.$TBAAuthKey));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec($ch),true);
	if (isSet($result["name"])) {
		return $result["name"];
	} else {
		return $code;
	}
	curl_close($ch);
}

$teamNameAtEvent = array();
function getNameTeamNumber($teamNumber, $event) {
	global $TBAAuthKey, $TBAApiUrl, $teamNameAtEvent;
	if (count($teamNameAtEvent) == 0) {
		$url = $TBAApiUrl.'/event/'.$event.'/teams/simple';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-TBA-Auth-Key: '.$TBAAuthKey));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec($ch),true);
		curl_close($ch);
		foreach ($result as $team) {
			$teamNameAtEvent[$team["team_number"]] = $team["nickname"];
		}
	}
	
	if (isSet($teamNameAtEvent[$teamNumber])) {
		return $teamNameAtEvent[$teamNumber];
	}
	else {
		return $teamNumber;
	}
}
?>

<head>
<link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
<link rel="icon" href="favicon.ico" type="image/x-icon">
<title>ORF Scouter</title>
<style>
body {
	background-color: black;
}
img {
	width: 25%;
}
p {
	font-size: 25;
}
p, h1, h2, h3 {
	color: white;
}

div {
    position: absolute;
    top:0;
    bottom: 0;
    left: 0;
    right: 0;

    margin: auto;
	text-align: center;
}
a {
	color: white;
	cursor: pointer;
}

a:hover,a.hover { text-decoration: underline; }
</style><script>
function onLoad() {
	<?php if ($error == "" && $input != "") {
	echo "document.getElementById(\"input\").defaultValue = '".$input."';";
	}
	?>
}
</script></head><body>
<div>
	<img src="logo.png"/>
	<?php
	if (!(count($events) > 0) && !(count($teams) >0)) {
	echo "<script>onLoad();</script><h1>Welcome to ORF's Scouting Data Viewer!</h1><form method=\"get\" action=\"index.php\">
	<p>Team Number or Event Key:</p>
	<p><input style=\"font-size: 20; text-align:center;\" id=\"input\" name=\"input\" type=\"text\"></input></p>".
	(($showHiddenData) ? "<input style=\"display:none\" id=\"showHiddenData\" name=\"showHiddenData\" type=\"text\" value=\"".$hiddenDataKey."\"></input>" : "")
	."<p><input style=\"font-size: 20;\" type=\"submit\"></input></p>
	</form><br/><p><a href=\"api/v1/exportData.php?exportType=allData\" target=\"_blank\">Download All Data</a>";
	}
	?>
	
	<p><?php echo $error ?></p>
	<?php
	if (count($events) > 0 && $error == "" && $inputTeam && !$inputEvent) {
		echo "<h1>Team ".$input." has been scouted at these events this season:</h1>";
		echo "<form action=\"viewTeam.php\" method=\"get\"> <input type=\"text\" style=\"display:none\" name=\"teamNumber\" value=\"".$input."\"></input>";
		if ($showHiddenData) echo "<input type=\"text\" style=\"display:none\" name=\"showHiddenData\" value=\"".$hiddenDataKey."\"></input>";
		echo "<select style= \"font-size: 1cm;\" name=\"eventCode\">";
		foreach($events as $event) {
			$eventCode = explode("/",$event)[2];
			if (substr($eventCode,0,4) == $seasonYear) {
				echo "<option value=\"".$eventCode."\"'>".getNameEventCode($eventCode)."</option>";
			}
		}
		
		echo "</select><input style=\"font-size: 0.85cm;margin-left: 50;margin-top: 50;\" type=\"submit\" value=\"View Data\"></input></form><br/>";
		echo "<br/><p><button style=\"font-size: 20;\" onClick='window.location.href=\"index.php\"'>Go Back</button><br/>";
		echo "<p><a href=\"api/v1/exportData.php?exportType=teamData&teamNumber=".$input."\" target=\"_blank\">Download All Data for ".$input."</a>";
	}
	
	if (count($teams) > 0 && $error == "" && !$inputTeam && $inputEvent) {
		echo "<h1>These teams have been scouted from the event \"".getNameEventCode($input)."\":</h1>";
		echo "<form action=\"viewTeam.php\" method=\"get\"> <input type=\"text\" style=\"display:none\" name=\"eventCode\" value=\"".$input."\"></input>";
		if ($showHiddenData) echo "<input type=\"text\" style=\"display:none\" name=\"showHiddenData\" value=\"".$hiddenDataKey."\"></input>";
		echo "<select style= \"font-size: 1cm;\" name=\"teamNumber\">";
		foreach($teams as $team) {
			$teamNumber = explode("/",$team)[3];
			if (getNameTeamNumber($teamNumber, $input) != $teamNumber) {
				echo "<option value=\"".$teamNumber."\"'>".$teamNumber." - ".getNameTeamNumber($teamNumber, $input)."</option>";
			}
		}
		
		echo "</select><input style=\"font-size: 0.85cm;margin-left: 50;margin-top: 50;\" type=\"submit\" value=\"View Data\"></input></form><br/>";
		echo "<p><button style=\"font-size: 20;\" onClick='window.location.href=\"index.php\"'>Go Back</button><br/>";
		echo "<p><a href=\"api/v1/exportData.php?exportType=eventData&eventKey=".$input."\" target=\"_blank\">Download All Data for this Event</a>";
	}
	?>
</div></body>