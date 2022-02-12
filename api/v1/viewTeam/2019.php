<?php

if (!isSet($_GET,$_GET["teamNumber"],$_GET["eventCode"])) {
	$error = "Something went wrong, please try again.";
	include "index.php";
	exit;
}
include_once "config.php";
if (isSet($_GET["showHiddenData"])) {
	if ($_GET["showHiddenData"] == $hiddenDataKey) {
		$showHiddenData = true;
	} else {
		$showHiddenData = false;
	}
} else {
	$showHiddenData = false;
}
ob_start();
include_once "api/v1/retrieveTeam.php";
header("Content-Type: text/html");
$result = json_decode(ob_get_clean(), true);

if (isSet($result["Error"])) {
	$error = $result["Error"];
	include "index.php";
	exit;
}

function arrayToString($array) {
	$string = "[";
	for($i = 0; $i < count($array); $i++) {
		$string .= "'".$array[$i]."',";
	}
	$string = substr($string,0, strlen($string)-1)."]";
	return $string;
}

function returnCorrectTd($windowTitle,$array) {
	if (count($array) > 1) {
		$allEqual = true;
		for($i=1;$i<count($array);$i++) {
			if ($array[0] == $array[$i])
				continue;
			$allEqual = false;
			break;
		}
		if ($allEqual) return $array[0];
		else return "<a onclick=\"openWindow('".$windowTitle."',".arrayToString($array).")\">Show All</a>";
	} else if (count($array) == 1) {
		return $array[0];
	} else {
		return "";
	}			
}

function returnCorrectTd2Arrays($windowTitle,$array1, $array2) {
	if (count($array1) > 1 && count($array2) > 1) {
		return "<a onclick=\"openWindow2Description('".$windowTitle."',".arrayToString($array1).",".arrayToString($array2).")\">Show All</a>";
	} else if (count($array1) > 1) {
		return returnCorrectTd($windowTitle,$array1);
	} else if (count($array1) == 1 && count($array2) >= 1) {
		return $array1[0]." - ".$array2[0];
	} else if (count($array1) == 1) {
		return $array1[0];
	} else {
		return "";
	}			
}
?>

<head>
<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.1.1.min.js"></script>
<script src="sortableTable/jquery.tablesorter.min.js"></script>
<script src="sortableTable/jquery.metadata.js"></script>
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
<title><?php echo $result["TeamNumber"]." at ".$result["EventName"]; ?> - ORF Scouting</title>
<style>
a {
	color: white;
	cursor: pointer;
}

a:hover,a.hover { text-decoration: underline; }

table, th, td {
    border: 1px solid white;
}

table.center {
	margin-left: auto;
	margin-right: auto;
	width: 65%;
}

#center {
	margin-left: auto;
	margin-right: auto;
	width: 65%;
}

p, h1, h3, td, th {
	color: white;
	text-align: center;
}

body {
	background-color: black;
}

td, th { 
	padding: 5px; 
}
</style>
<script>
$(function() {
	$(".sortable").tablesorter({ sortList: [[1,0]] });
});
function returnHome() {
	window.location.href = "index.php";
}

function getBackPage() {
	var url = window.location.href;
	var broken = url.split("/");
	var newUrl = broken[0];
	console.log(broken[0]);
	for (var i = 2; i < broken.length-1; i++) {
		newUrl = newUrl.concat("/",broken[i-1]);
	}
	return newUrl;
}

function onLoad() {
	$("#ShareLink").attr("href",window.location.href);
	$("#ShareLink").html(window.location.href);
	
	var type = "<?php if (isSet($result["Media"][$result["Media"]["Preferred"]]["type"])) echo $result["Media"][$result["Media"]["Preferred"]]["type"]; else echo "error"; ?>";
	var key = "<?php if (isSet($result["Media"][$result["Media"]["Preferred"]]["type"])) {
		switch ($result["Media"][$result["Media"]["Preferred"]]["type"]) {
		case "imgur":
			echo $result["Media"][$result["Media"]["Preferred"]]["foreign_key"];
			break;

		case "cdphotothread":
			echo $result["Media"][$result["Media"]["Preferred"]]["details"]["image_partial"];
			break;
			
		case "avatar":
			echo "data:image/jpeg;base64,".$result["Media"][$result["Media"]["Preferred"]]["details"]["base64Image"];
			break;
		}
	} else {
		echo "error";
	}
	?>";
	
	switch (type) {
		case "imgur":
			$("#logo").attr("src","http://imgur.com/"+key+".png");
			break;
				
		case "cdphotothread":
			$("#logo").attr("src","http://www.chiefdelphi.com/media/img/"+key);
			break;
			
		case "avatar":
			$("#logo").attr("src",key);
			break;
			
		default:
			break;
	}
}

function openWindow(type, description) {
	var newWindow = window.open("","Description"+type+description+Math.random(),"width=500,height=500,left=50");
	newWindow.document.write("<body style=\"background-color:black;text-align:center;color:white;\"><h2>"+type+"</h2><br/>");
	for(var i = 0; i < description.length; i++) {
		newWindow.document.write("<p>"+description[i]+"</p><br/>");
	}
	newWindow.document.write("<button onclick=\"window.close()\">Close</button></body>");
}

function openWindow2Description(type, description1, description2) {
	var newWindow = window.open("","Description"+type+description1+description2+Math.random(),"width=500,height=500,left=50");
	newWindow.document.write("<body style=\"background-color:black;text-align:center;color:white;\"><h2>"+type+"</h2><br/>");
	for(var i = 0; i < description1.length; i++) {
		if (description2[i] != undefined) {
			newWindow.document.write("<p>"+description1[i]+" - "+description2[i]+"</p><br/>");
		} else {
			newWindow.document.write("<p>"+description1[i]+"</p><br/>");
		}
	}
	newWindow.document.write("<button onclick=\"window.close()\">Close</button></body>");
}
</script>
</head>
<body onload="onLoad()">
<h1 style="text-align:center"><?php echo $result["TeamName"]." (".$result["TeamNumber"].") at ".$result["EventName"]; ?></h1>
<img id="logo" src="/logo.png" style="display: block;margin: 0 auto; border: 1px solid white; width: 70%"/>
<h3 style="text-align:center">Quick Facts:</h3>
<table class="center">
<tr><td>Team Number:</td><td colspan="2"><a target="_blank" href="index.php?input=<?php echo $result["TeamNumber"].(($showHiddenData) ? "&showHiddenData=".$hiddenDataKey: ""); ?>"><?php echo $result["TeamNumber"] ?></a> (<a target="_blank" href="<?php echo "https://thebluealliance.com/team/".$result["TeamNumber"]."/".$result["SeasonYear"]; ?>">View on The Blue Alliance</a>)</td></tr>
<tr><td>Event Key:</td><td colspan="2"><a target="_blank" href="index.php?input=<?php echo $result["EventCode"].(($showHiddenData) ? "&showHiddenData=".$hiddenDataKey: ""); ?>"><?php echo $result["EventCode"]; ?></a> (<a target="_blank" href=<?php echo "\"https://www.thebluealliance.com/event/".$result["EventCode"]."\"" ?>>View on The Blue Alliance</a>)</td></tr>
<tr><td>Team@Event Status:</td><td colspan="2"><?php echo $result["TeamStatusString"]; ?></td></tr>
<tr><td>Starting Position:</td><td>Pit: <?php echo $result["Pit"]["Pre_StartingPos"]; ?></td><td>Average: See table below</td></tr>
<tr><td>Autonomous:</td><td>Pit: Baseline: <?php echo $result["Pit"]["Auto_CrossedBaseline"]; ?><br/>Score at Switch: <?php echo $result["Pit"]["Auto_PlaceSwitch"]; ?><br/>Score at Scale: <?php echo $result["Pit"]["Auto_PlaceScale"]; ?></td><td>Average: See table below</td></tr>
<tr><td>Additional Autonomous Notes:</td><td colspan="2"><?php echo $result["Pit"]["Auto_Notes"]; ?></td></tr>

<tr><td>Discs in Cargo Ship:</td><td>Pit: <?php echo $result["Pit"]["Teleop_SwitchPlace"]; ?></td><td>Average: <?php echo $result["Stand"]["AvgDiscCSPlaces"]; ?></td></tr>
<tr><td>Discs in Rocket:</td><td>Pit: <?php echo $result["Pit"]["Teleop_SwitchPlace"]; ?></td><td>Average: <?php echo $result["Stand"]["AvgBallCSPlaces"]; ?></td></tr>
<tr><td>Balls in Cargo Ship:</td><td>Pit: <?php echo $result["Pit"]["Teleop_SwitchPlace"]; ?></td><td>Average: <?php echo $result["Stand"]["AvgDiscRPlaces"]; ?></td></tr>
<tr><td>Balls in Cargo Ship:</td><td>Pit: <?php echo $result["Pit"]["Teleop_SwitchPlace"]; ?></td><td>Average: <?php echo $result["Stand"]["AvgDiscRPlaces"]; ?></td></tr>
	
<tr><td>Disc drops per match:</td><td>Pit: N/A</td><td>Average: <?php echo $result["Stand"]["AvgDiscDrops"]; ?></td></tr>
<tr><td>Ball drops per match:</td><td>Pit: N/A</td><td>Average: <?php echo $result["Stand"]["AvgBallDrops"]; ?></td></tr>
	
<tr><td>Additional Teleoperated Notes:</td><td colspan="2"><?php echo $result["Pit"]["Teleop_Notes"]; ?></td></tr>
<tr><td>Climb:</td><td>Pit: <?php echo $result["Pit"]["Teleop_Climb"] ?></td><td>Average: See table below</td></tr>
<tr><td>General Strategy:</td><td colspan="2"><?php echo $result["Pit"]["Strategy_General"]; ?></td></tr>
<tr><td>Robot Notes:</td><td colspan="2"><?php echo $result["Pit"]["RobotNotes"]; ?></td></tr>
<?php if ($showHiddenData) echo "<tr><td>No Alliance:</td><td>Pit: ".$result["Pit"]["NoAlliance"]."</td><td>Average: See table below</td></tr>" ?>
</table>
<p></p>
<h3 style="text-align:center">Raw Data</h3>
<table class="sortable" id = "center">
<thead><tr><th><a>Scouter Name</a></th><th><a>Match Number</a></th><th><a>No Show</a></th><th><a>Starting Position</a></th><th><a>Auto - Baseline</a></th><th><a>Auto - Discs in CS</a></th><th><a>Auto - Discs in R</a></th><th><a>Auto - Balls in CS</a></th><th><a>Auto - Balls in R</a></th><th><a>Auto - Notes</a></th><th><a>Teleop - Discs in CS</a></th><th><a>Teleop - Discs in R</a></th><th><a>Teleop - Balls in CS</a></th><th><a>Teleop - Balls in R</a></th><th><a>Teleop - Disc Drops</a></th><th><a>Teleop - Ball Drops</a></th><th><a>Teleop - Notes</a></th><th><a>Climb</a></th><th><a>Died On Field</a></th><th><a>General Notes</a></th><?php if ($showHiddenData) echo "<th><a>No Alliance></a></th>" ?></tr></thead>
<tbody>
<?php
foreach ($result["Stand"]["Matches"] as $match) {
	if ($match == null || $match[0] == null) continue;
	$processedMatch = array();
	$keys = array_keys($match[0]);
	foreach ($match as $oneScout) {
		foreach ($keys as $key) {
			if (!array_key_exists($key,$processedMatch)) $processedMatch[$key] = array();
			$processedMatch[$key][] = $oneScout[$key];
		}
	}
	//echo "<td>".returnCorrectTd2Arrays("Scouts for ".$processedMatch["TeamNumber"][0]." for Match ".$processedMatch["MatchNumber"][0],$processedMatch["ScouterName"],$processedMatch["ScouterTeamNumber"])."</td><td><a href=\"https://thebluealliance.com/match/".$result["EventCode"]."_qm".$processedMatch["MatchNumber"][0]."\" target=\"_blank\">".$processedMatch["MatchNumber"][0]."</a></td><td>".returnCorrectTd("No Show by ".$processedMatch["TeamNumber"][0]." for Match ".$processedMatch["MatchNumber"][0],$processedMatch["Pre_NoShow"])."</td><td>".returnCorrectTd("Starting positions for ".$processedMatch["TeamNumber"][0]." for Match ".$processedMatch["MatchNumber"][0],$processedMatch["Pre_StartingPos"])."</td><td>".returnCorrectTd("Baseline crosses in Auto for ".$processedMatch["TeamNumber"][0]." for Match ".$processedMatch["MatchNumber"][0],$processedMatch["Auto_CrossedBaseline"])."</td><td>".returnCorrectTd("Power Cube placed on Switch in Auto by ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Auto_PlaceSwitch"])."</td><td>".returnCorrectTd("Power Cube placed on Scale in Auto by ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Auto_PlaceScale"])."</td><td>".returnCorrectTd("Power Cube dropped while attempting Switch in Auto by ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Auto_DropSwitch"])."</td><td>".returnCorrectTd("Power Cube dropped while attempting Scale in Auto by ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Auto_DropScale"])."</td><td>".returnCorrectTd("Autonomous Notes for ".$processedMatch["TeamNumber"][0]." for Match ".$processedMatch["MatchNumber"][0],$processedMatch["Auto_Notes"])."</td><td>".returnCorrectTd("Switch visits in Teleop by ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Teleop_SwitchPlace"])."</td><td>".returnCorrectTd("Scale visits in Teleop by ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Teleop_ScalePlace"])."</td><td>".returnCorrectTd("Power Cubes dropped attempting Switch in Teleop by ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Teleop_SwitchDrop"])."</td><td>".returnCorrectTd("Power Cubes dropped attempting Scale in Teleop by ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Teleop_ScaleDrop"])."</td><td>".returnCorrectTd("Exchange Zone Visits by ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Teleop_ExchangeVisit"])."</td><td>".returnCorrectTd("Teleop Notes for ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Teleop_Notes"])."</td><td>".returnCorrectTd("Boost used by ".$processedMatch["TeamNumber"][0]."\'s alliance in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Teleop_BoostUsed"])."</td><td>".returnCorrectTd("Force used by ".$processedMatch["TeamNumber"][0]."\'s alliance in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Teleop_ForceUsed"])."</td><td>".returnCorrectTd("Levitate used by ".$processedMatch["TeamNumber"][0]."\'s alliance in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Teleop_LevitateUsed"])."</td><td>".returnCorrectTd("Climb status for ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Post_Climb"])."</td><td>".returnCorrectTd("DOFs for ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["DOF"])."</td><td>".returnCorrectTd("General Notes for ".$processedMatch["TeamNumber"][0]." in Match ".$processedMatch["MatchNumber"][0],$processedMatch["Notes"])."</td>".(($showHiddenData) ? "<td>".returnCorrectTd("No Alliance markings for ".$processedMatch["TeamNumber"][0]." for Match ".$processedMatch["MatchNumber"][0],$processedMatch["NoAlliance"])."</td>" : "")."</tr>\n";
}
?>
</tbody>
</table>
<p></p>
<p><a href="api/v1/exportData.php?exportType=teamAtEventData&teamNumber=<?php echo $result["TeamNumber"]; ?>&eventKey=<?php echo $result["EventCode"]; ?>" target="_blank">Download Data</a></p>
<p>Link for sharing: <a id="ShareLink" href="http://orfscoutingservice.azurewebsites.net/index.php?input=<?php echo $result["TeamNumber"]; ?>">http://orfscoutingservice.azurewebsites.net/index.php?input=<?php echo $result["TeamNumber"]; ?></a></p><br/>
<div style="text-align:center;"><input type="button" style="font-size: 20;" onclick="returnHome()" value="Go Back"></div><br/>
</body></html>
