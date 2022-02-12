<?php
ini_set('max_execution_time', 900); //In seconds
header("Content-Type: application/json");

include "../../config.php";

$cacheDir = __DIR__."/Cache/";
if (!file_exists($cacheDir)) {
	mkdir($cacheDir);
}

if (file_exists($cacheDir."/fullResponse.json")) {
	$cachedFile = file($cacheDir."/fullResponse.json");
	if (time() - $cachedFile[0] < 300 && !isSet($_GET["forceRefresh"])) {
		echo $cachedFile[1];
		exit;
	} else {
		unlink($cacheDir."/fullResponse.json");
	}
}

function curlRequest($url,$httpHeaders) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $httpHeaders);
	$rawResult = curl_exec($ch);
	$result = array();
	
	$headersString = trim(substr($rawResult,0,curl_getinfo($ch, CURLINFO_HEADER_SIZE)));
	$headersArray = explode("\n",$headersString);
	foreach($headersArray as $header) {
		$parts = explode(": ",$header);
		if (isSet($parts[1])) {
		$result["header"][strtolower($parts[0])] = $parts[1];
		} else {
			$result["header"][strtolower($parts[0])] = $parts[0];
		}
	}
	
	$result["body"] = trim(substr($rawResult,curl_getinfo($ch, CURLINFO_HEADER_SIZE)));
	$result["http_code"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	
	curl_close($ch);
	
	return $result;
}

$data = array();
$data["CurrentVersion"] = ["2018","2","0"];

$events = array();
#Request 1: District Events
$districtEventsCacheDir = $cacheDir."EventInfo/";
if (!file_exists($districtEventsCacheDir)) {
	mkdir($districtEventsCacheDir);
}

$httpHeader = array('X-TBA-Auth-Key: '.$TBAAuthKey);
if (file_exists($districtEventsCacheDir."districtEvents.json")) {
	$file = file($districtEventsCacheDir."districtEvents.json", FILE_IGNORE_NEW_LINES);
	$lastModified = $file[0];
	$cachedJSON = $file[1];
	$httpHeader[1] = "If-Modified-Since: ".trim($lastModified);
}

$url1 = $TBAApiUrl.'/district/'.$seasonYear.$districtKey.'/events/simple';
$result1 = curlRequest($url1,$httpHeader);
if ($result1["http_code"] == 304) { //From cache, nothing's changed.
	$events += json_decode($cachedJSON); 
} elseif ($result1["http_code"] != 200) { //Something went wrong, give cached data if possible, error entry otherwise.
	if (isSet($cachedJSON)) {
		$events += json_decode($cachedJSON);
	} else {
		$events[] = array(
		"city" => "Error",
		"country" => "ERR",
		"district" => array (
			"abbreviation" => "ERR",
			"display_name" => "Error",
			"key" => "0000err",
			"year" => 0000
		),
		"end_date" => "0000-00-00",
		"event_code" => "error",
		"event_type" => 0,
		"key" => "0000error",
		"name" => "An error has occurred, please try again later.",
		"start_date" => "0000-00-00",
		"state_prov" => "ER",
		"year" => 0000
		);
	}
} else { //New data!
	$events += json_decode($result1["body"]);
	$dataToWrite = $result1["header"]["last-modified"]."\n".str_replace("\n","",$result1["body"]);
	file_put_contents($districtEventsCacheDir."districtEvents.json",$dataToWrite);
}
if (isSet($cachedJSON)) unset($cachedJSON);
$worldCmpEventKeys = array("carv","gal","hop","new","roe","tur","tes","dar","dal","cur","cars","arc");
$worldCmpCacheDir = $cacheDir."EventInfo/worldCmp/";
if (!file_exists($worldCmpCacheDir)) {
	mkdir($worldCmpCacheDir,0777,true);
}
foreach($worldCmpEventKeys as $cmpKey) {
	$httpHeader = array('X-TBA-Auth-Key: '.$TBAAuthKey);
	if (file_exists($worldCmpCacheDir.$seasonYear.$cmpKey.".json")) {
		$file = file($worldCmpCacheDir.$seasonYear.$cmpKey.".json", FILE_IGNORE_NEW_LINES);
		$lastModified = $file[0];
		$cachedJSON = $file[1];
		$httpHeader[1] = "If-Modified-Since: ".$lastModified;
	}
	$url1Cmp = $TBAApiUrl.'/event/'.$seasonYear.$cmpKey.'/simple';
	$result1Cmp = curlRequest($url1Cmp,$httpHeader);
	if ($result1Cmp["http_code"] == 304) { //Nothing changed! Use the cached data!
		$events[] = json_decode($cachedJSON); 
	} elseif ($result1Cmp["http_code"] != 200) { //Something went wrong, give cached data if possible, error entry otherwise.
		if (isSet($cachedJSON)) {
		$events[] = json_decode($cachedJSON);
		} else {
		$events = $events + array(
			"city" => "Error",
			"country" => "ERR",
			"district" => array (
				"abbreviation" => "ERR",
				"display_name" => "Error",
				"key" => "0000err",
				"year" => 0000
			),
			"end_date" => "0000-00-00",
			"event_code" => "error",
			"event_type" => 0,
			"key" => "0000error",
			"name" => "An error has occurred, please try again later.",
			"start_date" => "0000-00-00",
			"state_prov" => "ER",
			"year" => 0000
			);
		}
	} else { //New data!
		$events[] = json_decode($result1Cmp["body"]);
		$dataToWrite = $result1Cmp["header"]["last-modified"]."\n".str_replace("\n","",$result1Cmp["body"]);
		file_put_contents($worldCmpCacheDir.$seasonYear.$cmpKey.".json",$dataToWrite);
	}
}
if (isSet($cachedJSON)) unset($cachedJSON);

#Only sync events that are not near now. (1 week before and 3 days after)
$nowDT = new DateTime();
$data["Events"] = array();
foreach ($events as $event) {
	if (!is_object($event)) {
		continue;
	}
	$eventStartDT = DateTime::CreateFromFormat("Y-m-d", $event->start_date);
	$eventStartDT->modify("1 week ago");
	
	$eventEndDT = DateTime::CreateFromFormat("Y-m-d", (string)$event->end_date);
	$eventEndDT->modify("+3 days");
	
	if ($eventStartDT < $nowDT && $nowDT < $eventEndDT) {
		$data["Events"][] = $event;
	}
}

#Request 2: Teams for each event
$teamAtEventCacheDir = $cacheDir."TeamList/";
if (!file_exists($teamAtEventCacheDir)) {
	mkdir($teamAtEventCacheDir);
}
$data["TeamsByEvent"] = array();
foreach ($data["Events"] as $event) {
	$httpHeader = array('X-TBA-Auth-Key: '.$TBAAuthKey);
	if (file_exists($teamAtEventCacheDir.($event->key).".json")) {
		$file = file($teamAtEventCacheDir.($event->key).".json", FILE_IGNORE_NEW_LINES);
		$lastModified = $file[0];
		$cachedJSON = $file[1];
		$httpHeader[1] = "If-Modified-Since: ".$lastModified;
	}
    $url2 = $TBAApiUrl.'/event/'.($event->key).'/teams/simple';
    $result2 = curlRequest($url2,$httpHeader);
	
	$tmpData = array("EventKey"=> $event->key);
	if ($result2["http_code"] == 304) {
		$tmpData["TeamList"] = json_decode($cachedJSON); 
	} elseif ($result2["http_code"] != 200) { //Something went wrong, give cached data if possible, error entry otherwise.
		if (isSet($cachedJSON)) {
		$tmpData["TeamList"] = json_decode($cachedJSON); 
		} else {
		$tmpData["TeamList"] = array(
			"key" => "frc0000",
			"team_number" => 0000,
			"nickname" => "Error",
			"name" => "An Error has occurred, please try again later.",
			"city" => "Error",
			"state_prov" => "ERR",
			"country" => "ERR"
			);
		}
	} else { //New data!
		$tmpData["TeamList"] = json_decode($result2["body"]);
		$dataToWrite = $result2["header"]["last-modified"]."\n".str_replace("\n","",$result2["body"]);
		file_put_contents($teamAtEventCacheDir.($event->key).".json", $dataToWrite);
	}
	$data["TeamsByEvent"][] = $tmpData;
	unset($tmpData);
}
if (isSet($cachedJSON)) unset($cachedJSON);

#Request 3: Matches for each team for each event.
$teamMatchesCacheDir = $cacheDir."teamMatches/";
if (!file_exists($teamMatchesCacheDir)) {
	mkdir($teamMatchesCacheDir);
}
$data["EventMatches"] = array();	
foreach ($data["TeamsByEvent"] as $eventData) {
	$event = (object) array ("key" => $eventData["EventKey"]);
	
	$teamMatchesEventCacheDir = $teamMatchesCacheDir."/".($event->key)."/";
	if (!file_exists($teamMatchesEventCacheDir)) {
		mkdir($teamMatchesEventCacheDir);
	}
	
	foreach($eventData["TeamList"] as $team) {
		$tmpDataTeam = array();
	
		$httpHeader = array('X-TBA-Auth-Key: '.$TBAAuthKey);
		if (file_exists($teamMatchesEventCacheDir.($team->key).".json")) {
			$file = file($teamMatchesEventCacheDir.($team->key).".json", FILE_IGNORE_NEW_LINES);
			$lastModified = $file[0];
			$cachedJSON = $file[1];
			$httpHeader[1] = "If-Modified-Since: ".$lastModified;
		}
		$url3 = $TBAApiUrl.'/team/'.$team->key.'/event/'.($event->key).'/matches/keys';
		$result3 = curlRequest($url3,$httpHeader);
	
		$tmpDataTeam = array( "EventKey" => $event->key , "TeamNumber" => $team->team_number);
		if ($result3["http_code"] == 304) {
			$tmpDataTeam["Matches"] = json_decode($cachedJSON); 
		} elseif ($result3["http_code"] != 200) { //Something went wrong, give cached data if possible, error entry otherwise.
			if (isSet($cachedJSON)) {
				$tmpDataTeam["Matches"] = json_decode($cachedJSON); 
			} else {
			$tmpDataTeam["Matches"] = array("0000error_er0");	
			}
		} else { //New data!
			$tmpDataTeam["Matches"] = json_decode($result3["body"]);
			$dataToWrite = $result3["header"]["last-modified"]."\n".str_replace("\n","",$result3["body"]);
			file_put_contents($teamMatchesEventCacheDir.($team->key).".json", $dataToWrite);
		}	
		
		if (count($tmpDataTeam["Matches"]) == 0) {
			$tmpDataTeam["Matches"][] = "0000error_er0";
		}
	
		$data["EventMatches"][] = $tmpDataTeam;
	}
}
if (isSet($cachedJSON)) unset($cachedJSON);	
unset($event, $tmpDataTeam);

$responseCache = fopen($cacheDir."/fullResponse.json","w"); //Cache full response so it can be used in the next 5 minutes.
fwrite($responseCache,time()."\n".json_encode($data));
fclose($responseCache);
echo json_encode($data);
?>
