<?php
header("Content-Type: application/json");

$data = array();

if (!isSet($configLoaded)) {
include_once "../../config.php";
}

if (isSet($_GET["showHiddenData"])) {
	if ($_GET["showHiddenData"] == $hiddenDataKey) {
		$showHiddenData = true;
	} else {
		$showHiddenData = false;
	}
} else {
	$showHiddenData = false;
}

if (!isSet($_GET["teamNumber"])) {
	echo "{ \"Error\": \"Invalid request!\"}";
	http_response_code(400);
	exit;
} else {
	$data["TeamNumber"] = $_GET["teamNumber"];
}

if (!isSet($_GET["eventCode"])) {
	echo "{ \"Error\": \"Invalid request!\"}";
	http_response_code(400);
	exit;
} else {
	$data["EventCode"] = $_GET["eventCode"];
	$data["SeasonYear"] = substr($data["EventCode"],0,4);
}

$teamDataPath = $data["EventCode"]."/".$data["TeamNumber"];
	
if (!file_exists($teamDataPath)) {
	$teamDataPath = "api/v1/".$teamDataPath;
	if (!file_exists($teamDataPath)) {
		echo "{ \"Error\": \"Team data not found for the specified event!\" }";
		http_response_code(404);
		exit;
	}
}

if (filesize($teamDataPath."/pitScout.json")>0) {
	$file = fopen($teamDataPath."/pitScout.json","r");
    $data["Pit"] = json_decode(fread($file,filesize($teamDataPath."/pitScout.json")),true);
	
	$unneededData = array("App","Version","EventKey","TeamNumber","ScouterName");
	foreach($unneededData as $dataToRemove) {
		unset($data["Pit"][$dataToRemove]);
	}
	
    if (!$showHiddenData) {
        unset($data["Pit"]["NoAlliance"]);
    }
	
	fclose($file);
	
} else {
	$data["Pit"] = array(
		"Pre_StartingPos" => "Unknown",	
		"Auto_CrossedBaseline" => "Unknown",
		"Auto_Notes" => "Unknown",
		"Auto_PlaceSwitch" => "Unknown",
		"Auto_PlaceScale" => "Unknown",
		"Teleop_ScalePlace" => "Unknown",
		"Teleop_SwitchPlace" => "Unknown",
		"Teleop_ExchangeVisit" => "Unknown",
		"Teleop_Notes" => "Unknown",
		"RobotNotes" => "Unknown",
		"Teleop_Climb" => "Unknown",
		"Strategy_PowerUp" => "Unknown",
		"Strategy_General" => "Unknown",
    );
    if ($showHiddenData) {
        $data["Pit"]["NoAlliance"] = "Unknown";
    }
}

if (filesize($teamDataPath."/standScout.json")>0) {
	$file = fopen($teamDataPath."/standScout.json","r");
	$rawLine = explode("\n",fread ($file,filesize($teamDataPath."/standScout.json")));
	$LowPlace = 0;
	$HighPlace = 0;
	$LowDrop = 0;
	$HighDrop = 0;
    $Exchange = 0;
    $matchData = array();
	foreach($rawLine as $line) {
        $json = json_decode($line,true);
		if (!isSet($json["MatchNumber"])) continue;
        if (!$showHiddenData) {
            unset($json["NoAlliance"]);
        }
		if ($json["Auto_BallCargoShip"] == "Placed") $json["Auto_BallCargoShip"] = 1;
		else if ($json["Auto_BallCargoShip"] == "Did not place") $json["Auto_BallCargoShip"] = 1;
		if ($json["Auto_BallRocket"] == "Placed") $json["Auto_BallRocket"] = 1;
		else if ($json["Auto_BallRocket"] == "Did not place") $json["Auto_BallRocket"] = 1;
		if ($json["Auto_DiscCargoShip"] == "Placed") $json["Auto_DiscCargoShip"] = 1;
		else if ($json["Auto_DiscCargoShip"] == "Did not place") $json["Auto_DiscCargoShip"] = 1;
		if ($json["Auto_DiscRocket"] == "Placed") $json["Auto_DiscRocket"] = 1;
		else if ($json["Auto_DiscRocket"] == "Did not place") $json["Auto_DiscRocket"] = 1;
		
		if (!isSet($json["Auto_DropSwitch"])) {
			$json["Teleop_DiscDrop"] = 0;
			$json["Teleop_BallDrop"] = 0;
		}
		
        $matchData[$json["MatchNumber"]][] = $json;
		$DiscCargoShip += $json["Auto_DiscCargoShip"] + $json["Teleop_DiscCargoPlace"];
		$DiscRocket += $json["Auto_DiscRocket"] + $json["Teleop_DiscRocketPlace"];
		
		$BallCargoShip += $json["Auto_BallCargoShip"] + $json["Teleop_BallCargoShipPlace"];
		$BallRocket += $json["Auto_BallRocket"] + $json["Teleop_BallRocketPlace"];
		
		$DiscDrop += $json[ $json["Teleop_DiscDrop"];
		$BallDrop += $json[$json["Teleop_BallDrop"];
        
    }
    $data["Stand"] = array(
        "Matches" => $matchData,
        "AvgDiscCSPlaces" => $DiscCargoShip/(count($rawLine)-1),
        "AvgBallCSPlaces" => $BallCargoShip/(count($rawLine)-1),
	"AvgDiscRPlaces" => $DiscRocket/(count($rawLine)-1),
        "AvgBallRPlaces" => $BallRocket/(count($rawLine)-1),
	"AvgDiscDrops" => $DiscDrop/(count($rawLine)-1),
        "AvgBallDrops" => $BallDrop/(count($rawLine)-1),
    );
	fclose($file);
} else {
    $data["Stand"] = array(
        "Matches" => array(),
        "AvgExchangeVisits" => "Unknown",
        "AvgSwitchPlaces" => "Unknown",
        "AvgScalePlaces" => "Unknown",
		"AvgSwitchDrops" => "Unknown",
        "AvgScaleDrops" => "Unknown",
    );
}

$url1 = $TBAApiUrl.'/event/'.$data["EventCode"].'/simple';
$ch1 = curl_init($url1);
curl_setopt($ch1, CURLOPT_HTTPHEADER, array('X-TBA-Auth-Key: '.$TBAAuthKey));
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
$result1 = json_decode(curl_exec($ch1),true);
if (isSet($result1["name"])) {
	$data["EventName"] = $result1["name"];
} else {
	$data["EventName"] = "Error getting event name.";
}
curl_close($ch1);

$url2 = $TBAApiUrl.'/team/frc'.$data["TeamNumber"].'/simple';
$ch2 = curl_init($url2);
curl_setopt($ch2, CURLOPT_HTTPHEADER, array('X-TBA-Auth-Key: '.$TBAAuthKey));
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
$result2 = json_decode(curl_exec($ch2),true);
if (isSet($result2["nickname"])) {
	$data["TeamName"] = $result2["nickname"];
} else {
    $data["TeamName"] = "Error getting team nickname.";
}
curl_close($ch2);

$url3 = $TBAApiUrl.'/team/frc'.$data["TeamNumber"].'/event/'.$data["EventCode"].'/status';
$ch3 = curl_init($url3);
curl_setopt($ch3, CURLOPT_HTTPHEADER, array('X-TBA-Auth-Key: '.$TBAAuthKey));
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
$result3 = json_decode(curl_exec($ch3),true);
if (isSet($result3["overall_status_str"])) {
	$data["TeamStatusString"] = $result3["overall_status_str"];
} else {
	$data["TeamStatusString"] = "Status Unavailable";
}
curl_close($ch3);

$url4 = $TBAApiUrl.'/team/frc'.$data["TeamNumber"]."/media/".$data["SeasonYear"];
$ch4 = curl_init($url4);
curl_setopt($ch4, CURLOPT_HTTPHEADER, array('X-TBA-Auth-Key: '.$TBAAuthKey));
curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
$result4 = json_decode(curl_exec($ch4),true);
$data["Media"] = $result4;
curl_close($ch4);

$perferred = -1;
for ($i = 0; $i < count($result4) && $perferred == -1; $i++) {
	if ($result4[$i]["preferred"] == true) $perferred = $i;
}
if ($perferred == -1) $perferred = 0;

$data["Media"]["Preferred"] = $perferred;

echo json_encode($data);
?>
