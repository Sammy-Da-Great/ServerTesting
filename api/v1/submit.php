<?php 
$expectedFormInputCommon = array(
	"App",
	"Version",
	"ScouterName",
	"ScouterTeamNumber",
	"EventKey",
	"TeamNumber",
	"Pre_StartingPos",	
	"Auto_CrossedBaseline",
	"Auto_Notes",
	"Auto_Level1",
	"Auto_Level2",
	"Auto_BallCargoShip",
	"Auto_DiscCargoShip",
	"Auto_BallRocket",
	"Auto_DiscRocket",
	"Teleop_DiscCargoPlace",
	"Teleop_DiscRocketPlace",
	"Teleop_BallCargoPlace",
	"Teleop_BallRocketPlace",
	"Teleop_DiscDrop",
	"Teleop_BallDrop",
	"Teleop_Level1",
	"Teleop_Level2",
	"Teleop_Level3",
	"Teleop_PartnerAssisted",
	"Teleop_Notes",
);

$expectedFormInputStand = array(
	"Notes",
	"Pre_NoShow",
	"MatchNumber",
	"Teleop_DiscDrop",
	"Teleop_BallDrop",
	"Post_Climb",
	"DOF",
);

$expectedFormInputPit = array(
	"RobotNotes",
	"Teleop_Climb",
	"Strategy_PowerUp",
	"Strategy_General",
);
if (isSet($_POST["App"])) {
	$dataArray = array();
	foreach($expectedFormInputCommon as $input) {
		if (isSet($_POST[$input])) {
			if (seralizeString($_POST[$input]) !== false) {
				$dataArray[$input] = seralizeString($_POST[$input]);
			} else {
				http_response_code(400);
				exit;
				}
		} else {
			http_response_code(400);
			exit;
		}
	}
	
	include_once "../../config.php";
	
	$url = $TBAApiUrl.'/team/frc'.$_POST["TeamNumber"].'/event/'.$_POST["EventKey"].'/status';
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-TBA-Auth-Key: '.$TBAAuthKey));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_exec($ch);
	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($httpCode != 200) {
		http_response_code(400);
		exit;
	}
		if ($_POST["App"] == "stand") {
		foreach($expectedFormInputStand as $input) {
			if (isSet($_POST[$input])) {
				if (seralizeString($_POST[$input]) !== false) {
					$dataArray[$input] = seralizeString($_POST[$input]);
			} else {
				http_response_code(400);
				exit;
				}
		} else {
			http_response_code(400);
			exit;
		}
		}
	} elseif ($_POST["App"] == "pit") {
		foreach($expectedFormInputPit as $input) {
			if (isSet($_POST[$input])) {
				if (seralizeString($_POST[$input]) !== false) {
					$dataArray[$input] = seralizeString($_POST[$input]);
			} else {
				http_response_code(400);
				exit;
				}
		} else {
			http_response_code(400);
			exit;
		}
		}
	} else {
		http_response_code(400);
		exit;
	}

	$dataArray["NoAlliance"] = "N/A";
	$lineToAppend = json_encode($dataArray);
	
	$teamPath = getOrCreateTeamFolder($_POST["TeamNumber"], $_POST["EventKey"]);
	
	if ($_POST["App"] == "stand") {
		$dataPath = $teamPath."standScout.json";
		$lines = file($dataPath);
		if ($lines !== false) {
			foreach($lines as $line) {
				if ($lineToAppend == $line) {
					http_response_code(400);
					exit;
				}
			}
		}
	} else {
		$dataPath = $teamPath."pitScout.json";
		$lines = file($dataPath);
		if ($lines !== false) {
			foreach($lines as $line) {
				if ($lineToAppend == $line) {
					http_response_code(400);
					exit;
				}
			}
		}
	}
	$dataFile = fopen($dataPath,"a");
	fwrite($dataFile, $lineToAppend."\n");
	fclose($dataFile);
	
	echo $lineToAppend;
	
} else {
	http_response_code(400);
	exit;
}

function getOrCreateTeamFolder($teamNumber, $eventCode) {
	$path = $eventCode."/".$teamNumber;
	if (!file_exists($path)) {
		if (mkdir($path,0777,true)) {
			$filesToMake = array("standScout.json","pitScout.json");
			foreach($filesToMake as $file) {
				$newFile = fopen($path."/".$file,"w+");
				fclose($newFile);
			}
		} else {
			http_response_code(400);
		}
	}
	return $path."/";
}

function seralizeString($stringToValidate) {
	$newString = preg_replace('~(\\ | \n)~',' ', preg_replace('/\"/', '\'', $stringToValidate)); //Changes double quotes to single, and removes escaping characters.
	if ($newString != null) {
		return $newString;
	}
	return false;
}
?>
