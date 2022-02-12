<?php

if (!isSet($_GET["exportType"])) {
	http_response_code(400);
	exit;
}

switch($_GET["exportType"]) {
	case "eventData":
		if (!isSet($_GET["eventKey"])) {
			http_response_code(400);
			exit;
		}
		
		$pathToEvent = __DIR__."/".$_GET["eventKey"];
		if (file_exists($pathToEvent)) {
			$zip = new ZipArchive;
			$zip->open($_GET["eventKey"].".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
			
			$combinedPitLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Robot Notes,Strategy for Power Ups,Strategy in General".PHP_EOL;
			$combinedStandLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Match Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Dropped Cube attempting Switch,Autonomous: Dropped Cube attempting Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Dropped Cube attempting Switch,Teleop: Dropped Cube attempting Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Teleop: Boost Used,Teleop: Force Used,Teleop: Levitate Used,Climb,General Notes,No Show,Died on Field".PHP_EOL;
			
			$files = scandir($pathToEvent);
			foreach($files as $filePart) {
				if (file_exists($pathToEvent."/".$filePart) && ($filePart != "." && $filePart != "..")) {
					if (count($pitDataRaw = file($pathToEvent."/".$filePart."/pitScout.json")) > 0) {
						$pitData = json_decode($pitDataRaw[0], true);
					
						$pitLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Robot Notes,Strategy for Power Ups,Strategy in General".PHP_EOL;
						$pitLine .= "\"".$pitData["ScouterName"]."\",\"".$pitData["ScouterTeamNumber"]."\",\"".$pitData["EventKey"]."\",\"".$pitData["TeamNumber"]."\",\"".$pitData["Pre_StartingPos"]."\",\"".$pitData["Auto_CrossedBaseline"]."\",\"".$pitData["Auto_PlaceSwitch"]."\",\"".$pitData["Auto_PlaceScale"]."\",\"".$pitData["Auto_Notes"]."\",\"".$pitData["Teleop_SwitchPlace"]."\",\"".$pitData["Teleop_ScalePlace"]."\",\"".$pitData["Teleop_ExchangeVisit"]."\",\"".$pitData["Teleop_Climb"]."\",\"".$pitData["Teleop_Notes"]."\",\"".$pitData["RobotNotes"]."\",\"".$pitData["Strategy_PowerUp"]."\",\"".$pitData["Strategy_General"]."\"".PHP_EOL;
						$combinedPitLine .= "\"".$pitData["ScouterName"]."\",\"".$pitData["ScouterTeamNumber"]."\",\"".$pitData["EventKey"]."\",\"".$pitData["TeamNumber"]."\",\"".$pitData["Pre_StartingPos"]."\",\"".$pitData["Auto_CrossedBaseline"]."\",\"".$pitData["Auto_PlaceSwitch"]."\",\"".$pitData["Auto_PlaceScale"]."\",\"".$pitData["Auto_Notes"]."\",\"".$pitData["Teleop_SwitchPlace"]."\",\"".$pitData["Teleop_ScalePlace"]."\",\"".$pitData["Teleop_ExchangeVisit"]."\",\"".$pitData["Teleop_Climb"]."\",\"".$pitData["Teleop_Notes"]."\",\"".$pitData["RobotNotes"]."\",\"".$pitData["Strategy_PowerUp"]."\",\"".$pitData["Strategy_General"]."\"".PHP_EOL;
						
						$zip->addFromString($filePart."/pitData.csv",$pitLine);
					}
					
					if (count($standDataRaw = file($pathToEvent."/".$filePart."/standScout.json")) > 0) {
						$standData = array();
						foreach ($standDataRaw as $standRaw) {
							$standDataTmp = json_decode($standRaw, true);
							if (!isSet($standDataTmp["Auto_DropSwitch"])) {
								$standDataTmp["Auto_DropSwitch"] = 0;
								$standDataTmp["Auto_DropScale"] = 0;
								$standDataTmp["Teleop_SwitchDrop"] = 0;
								$standDataTmp["Teleop_ScaleDrop"] = 0;
							}
							$standData[] = $standDataTmp;
						}
					
						$standLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Match Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Dropped Cube attempting Switch,Autonomous: Dropped Cube attempting Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Dropped Cube attempting Switch,Teleop: Dropped Cube attempting Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Teleop: Boost Used,Teleop: Force Used,Teleop: Levitate Used,Climb,General Notes,No Show,Died on Field".PHP_EOL;
						foreach ($standData as $singleStandData) {
							$standLine .= "\"".$singleStandData["ScouterName"]."\",\"".$singleStandData["ScouterTeamNumber"]."\",\"".$singleStandData["EventKey"]."\",\"".$singleStandData["TeamNumber"]."\",\"".$singleStandData["MatchNumber"]."\",\"".$singleStandData["Pre_StartingPos"]."\",\"".$singleStandData["Auto_CrossedBaseline"]."\",\"".$singleStandData["Auto_PlaceSwitch"]."\",\"".$singleStandData["Auto_PlaceScale"]."\",\"".$singleStandData["Auto_DropSwitch"]."\",\"".$singleStandData["Auto_DropScale"]."\",\"".$singleStandData["Auto_Notes"]."\",\"".$singleStandData["Teleop_SwitchPlace"]."\",\"".$singleStandData["Teleop_ScalePlace"]."\",\"".$singleStandData["Teleop_SwitchDrop"]."\",\"".$singleStandData["Teleop_ScaleDrop"]."\",\"".$singleStandData["Teleop_ExchangeVisit"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Teleop_Notes"]."\",\"".$singleStandData["Teleop_BoostUsed"]."\",\"".$singleStandData["Teleop_ForceUsed"]."\",\"".$singleStandData["Teleop_LevitateUsed"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Notes"]."\",\"".$singleStandData["Pre_NoShow"]."\",\"".$singleStandData["DOF"]."\"".PHP_EOL;
							$combinedStandLine .= "\"".$singleStandData["ScouterName"]."\",\"".$singleStandData["ScouterTeamNumber"]."\",\"".$singleStandData["EventKey"]."\",\"".$singleStandData["TeamNumber"]."\",\"".$singleStandData["MatchNumber"]."\",\"".$singleStandData["Pre_StartingPos"]."\",\"".$singleStandData["Auto_CrossedBaseline"]."\",\"".$singleStandData["Auto_PlaceSwitch"]."\",\"".$singleStandData["Auto_PlaceScale"]."\",\"".$singleStandData["Auto_DropSwitch"]."\",\"".$singleStandData["Auto_DropScale"]."\",\"".$singleStandData["Auto_Notes"]."\",\"".$singleStandData["Teleop_SwitchPlace"]."\",\"".$singleStandData["Teleop_ScalePlace"]."\",\"".$singleStandData["Teleop_SwitchDrop"]."\",\"".$singleStandData["Teleop_ScaleDrop"]."\",\"".$singleStandData["Teleop_ExchangeVisit"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Teleop_Notes"]."\",\"".$singleStandData["Teleop_BoostUsed"]."\",\"".$singleStandData["Teleop_ForceUsed"]."\",\"".$singleStandData["Teleop_LevitateUsed"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Notes"]."\",\"".$singleStandData["Pre_NoShow"]."\",\"".$singleStandData["DOF"]."\"".PHP_EOL;
						}
					
						$zip->addFromString($filePart."/standData.csv",$standLine);
					}
				}
			}
			
			$zip->addFromString("combinedStandData.csv",$combinedStandLine);
			$zip->addFromString("combinedPitData.csv",$combinedPitLine);
			
			$zip->close();
					
					header('Content-Type: application/zip');
					header('Content-disposition: attachment; filename='.$_GET["eventKey"].'.zip');
					header('Content-Length: ' . filesize($_GET["eventKey"].".zip"));
					readfile($_GET["eventKey"].".zip");
					unlink($_GET["eventKey"].".zip");
					exit;
		}
		break;
		
	case "teamData":
		if (!isSet($_GET["teamNumber"])) {
			http_response_code(400);
			exit;
		}
		
		$eventDirectories = glob('[0-9][0-9][0-9][0-9]*' , GLOB_ONLYDIR);
		$events = array();
		foreach($eventDirectories as $eventDirectory) {
			$teamDirs = glob($eventDirectory.'/[0-9]*', GLOB_ONLYDIR);
			foreach ($teamDirs as $teamDir) {
				if ($teamDir == $eventDirectory."/".$_GET["teamNumber"]) array_push($events, $teamDir);
			}
		}
		
		$zip = new ZipArchive;
		$zip->open($_GET["teamNumber"].".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
		
		$combinedPitLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Robot Notes,Strategy for Power Ups,Strategy in General".PHP_EOL;
		$combinedStandLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Match Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Dropped Cube attempting Switch,Autonomous: Dropped Cube attempting Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Dropped Cube attempting Switch,Teleop: Dropped Cube attempting Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Teleop: Boost Used,Teleop: Force Used,Teleop: Levitate Used,Climb,General Notes,No Show,Died on Field".PHP_EOL;
		
		foreach($events as $pathToFolder) {
		    $event = explode("/",$pathToFolder)[0];
			if (file_exists($pathToFolder)) {
					if (count($pitDataRaw = file($pathToFolder."/pitScout.json")) > 0) {
						$pitLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Robot Notes,Strategy for Power Ups,Strategy in General".PHP_EOL;
						foreach ($pitDataRaw as $pitDataLine)
						{
							$pitData = json_decode($pitDataLine, true);
							$pitLine .= "\"".$pitData["ScouterName"]."\",\"".$pitData["ScouterTeamNumber"]."\",\"".$pitData["EventKey"]."\",\"".$pitData["TeamNumber"]."\",\"".$pitData["Pre_StartingPos"]."\",\"".$pitData["Auto_CrossedBaseline"]."\",\"".$pitData["Auto_PlaceSwitch"]."\",\"".$pitData["Auto_PlaceScale"]."\",\"".$pitData["Auto_Notes"]."\",\"".$pitData["Teleop_SwitchPlace"]."\",\"".$pitData["Teleop_ScalePlace"]."\",\"".$pitData["Teleop_ExchangeVisit"]."\",\"".$pitData["Teleop_Climb"]."\",\"".$pitData["Teleop_Notes"]."\",\"".$pitData["RobotNotes"]."\",\"".$pitData["Strategy_PowerUp"]."\",\"".$pitData["Strategy_General"]."\"".PHP_EOL;
							$combinedPitLine .= "\"".$pitData["ScouterName"]."\",\"".$pitData["ScouterTeamNumber"]."\",\"".$pitData["EventKey"]."\",\"".$pitData["TeamNumber"]."\",\"".$pitData["Pre_StartingPos"]."\",\"".$pitData["Auto_CrossedBaseline"]."\",\"".$pitData["Auto_PlaceSwitch"]."\",\"".$pitData["Auto_PlaceScale"]."\",\"".$pitData["Auto_Notes"]."\",\"".$pitData["Teleop_SwitchPlace"]."\",\"".$pitData["Teleop_ScalePlace"]."\",\"".$pitData["Teleop_ExchangeVisit"]."\",\"".$pitData["Teleop_Climb"]."\",\"".$pitData["Teleop_Notes"]."\",\"".$pitData["RobotNotes"]."\",\"".$pitData["Strategy_PowerUp"]."\",\"".$pitData["Strategy_General"]."\"".PHP_EOL;
						}
						$zip->addFromString($event."/pitData.csv",$pitLine);
					}
					
					if (count($standDataRaw = file($pathToFolder."/standScout.json")) > 0) {
						$standData = array();
						foreach ($standDataRaw as $standRaw) {
							$standDataTmp = json_decode($standRaw, true);
							if (!isSet($standDataTmp["Auto_DropSwitch"])) {
								$standDataTmp["Auto_DropSwitch"] = 0;
								$standDataTmp["Auto_DropScale"] = 0;
								$standDataTmp["Teleop_SwitchDrop"] = 0;
								$standDataTmp["Teleop_ScaleDrop"] = 0;
							}
							$standData[] = $standDataTmp;
						}
					
						$standLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Match Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Dropped Cube attempting Switch,Autonomous: Dropped Cube attempting Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Dropped Cube attempting Switch,Teleop: Dropped Cube attempting Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Teleop: Boost Used,Teleop: Force Used,Teleop: Levitate Used,Climb,General Notes,No Show,Died on Field".PHP_EOL;
						foreach ($standData as $singleStandData) {
							$standLine .= "\"".$singleStandData["ScouterName"]."\",\"".$singleStandData["ScouterTeamNumber"]."\",\"".$singleStandData["EventKey"]."\",\"".$singleStandData["TeamNumber"]."\",\"".$singleStandData["MatchNumber"]."\",\"".$singleStandData["Pre_StartingPos"]."\",\"".$singleStandData["Auto_CrossedBaseline"]."\",\"".$singleStandData["Auto_PlaceSwitch"]."\",\"".$singleStandData["Auto_PlaceScale"]."\",\"".$singleStandData["Auto_DropSwitch"]."\",\"".$singleStandData["Auto_DropScale"]."\",\"".$singleStandData["Auto_Notes"]."\",\"".$singleStandData["Teleop_SwitchPlace"]."\",\"".$singleStandData["Teleop_ScalePlace"]."\",\"".$singleStandData["Teleop_SwitchDrop"]."\",\"".$singleStandData["Teleop_ScaleDrop"]."\",\"".$singleStandData["Teleop_ExchangeVisit"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Teleop_Notes"]."\",\"".$singleStandData["Teleop_BoostUsed"]."\",\"".$singleStandData["Teleop_ForceUsed"]."\",\"".$singleStandData["Teleop_LevitateUsed"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Notes"]."\",\"".$singleStandData["Pre_NoShow"]."\",\"".$singleStandData["DOF"]."\"".PHP_EOL;
							$combinedStandLine .= "\"".$singleStandData["ScouterName"]."\",\"".$singleStandData["ScouterTeamNumber"]."\",\"".$singleStandData["EventKey"]."\",\"".$singleStandData["TeamNumber"]."\",\"".$singleStandData["MatchNumber"]."\",\"".$singleStandData["Pre_StartingPos"]."\",\"".$singleStandData["Auto_CrossedBaseline"]."\",\"".$singleStandData["Auto_PlaceSwitch"]."\",\"".$singleStandData["Auto_PlaceScale"]."\",\"".$singleStandData["Auto_DropSwitch"]."\",\"".$singleStandData["Auto_DropScale"]."\",\"".$singleStandData["Auto_Notes"]."\",\"".$singleStandData["Teleop_SwitchPlace"]."\",\"".$singleStandData["Teleop_ScalePlace"]."\",\"".$singleStandData["Teleop_SwitchDrop"]."\",\"".$singleStandData["Teleop_ScaleDrop"]."\",\"".$singleStandData["Teleop_ExchangeVisit"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Teleop_Notes"]."\",\"".$singleStandData["Teleop_BoostUsed"]."\",\"".$singleStandData["Teleop_ForceUsed"]."\",\"".$singleStandData["Teleop_LevitateUsed"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Notes"]."\",\"".$singleStandData["Pre_NoShow"]."\",\"".$singleStandData["DOF"]."\"".PHP_EOL;
						}
					
						$zip->addFromString($event."/standData.csv",$standLine);
					}
			}
		}
		
		$zip->addFromString("combinedStandData.csv",$combinedStandLine);
		$zip->addFromString("combinedPitData.csv",$combinedPitLine);
		
		$zip->close();
					
		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename='.$_GET["teamNumber"].'.zip');
		header('Content-Length: ' . filesize($_GET["teamNumber"].".zip"));
		readfile($_GET["teamNumber"].".zip");
		unlink($_GET["teamNumber"].".zip");
		exit;
		
	case "teamAtEventData":
		if (!isSet($_GET["teamNumber"]) || !isSet($_GET["eventKey"])) {
			http_response_code(400);
			exit;
		}
		
		if (!file_exists($_GET["eventKey"]."/".$_GET["teamNumber"])) {
			http_response_code(404);
			exit;
		}
		$zip = new ZipArchive;
		$zip->open($_GET["teamNumber"]."-".$_GET["eventKey"].".zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
		$pathToFolder = $_GET["eventKey"]."/".$_GET["teamNumber"];
		if (count($pitDataRaw = file($pathToFolder."/pitScout.json")) > 0) {
			$pitData = json_decode($pitDataRaw[0], true);
				
			$pitLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Robot Notes,Strategy for Power Ups,Strategy in General".PHP_EOL;
			$pitLine .= "\"".$pitData["ScouterName"]."\",\"".$pitData["ScouterTeamNumber"]."\",\"".$pitData["EventKey"]."\",\"".$pitData["TeamNumber"]."\",\"".$pitData["Pre_StartingPos"]."\",\"".$pitData["Auto_CrossedBaseline"]."\",\"".$pitData["Auto_PlaceSwitch"]."\",\"".$pitData["Auto_PlaceScale"]."\",\"".$pitData["Auto_Notes"]."\",\"".$pitData["Teleop_SwitchPlace"]."\",\"".$pitData["Teleop_ScalePlace"]."\",\"".$pitData["Teleop_ExchangeVisit"]."\",\"".$pitData["Teleop_Climb"]."\",\"".$pitData["Teleop_Notes"]."\",\"".$pitData["RobotNotes"]."\",\"".$pitData["Strategy_PowerUp"]."\",\"".$pitData["Strategy_General"]."\"".PHP_EOL;
			$zip->addFromString("pitData.csv",$pitLine);
		}
					
		if (count($standDataRaw = file($pathToFolder."/standScout.json")) > 0) {
			$standData = array();
					foreach ($standDataRaw as $standRaw) {
						$standDataTmp = json_decode($standRaw, true);
						if (!isSet($standDataTmp["Auto_DropSwitch"])) {
							$standDataTmp["Auto_DropSwitch"] = 0;
							$standDataTmp["Auto_DropScale"] = 0;
							$standDataTmp["Teleop_SwitchDrop"] = 0;
							$standDataTmp["Teleop_ScaleDrop"] = 0;
						}
						$standData[] = $standDataTmp;
					}
					
			$standLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Match Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Dropped Cube attempting Switch,Autonomous: Dropped Cube attempting Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Dropped Cube attempting Switch,Teleop: Dropped Cube attempting Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Teleop: Boost Used,Teleop: Force Used,Teleop: Levitate Used,Climb,General Notes,No Show,Died on Field".PHP_EOL;
			foreach ($standData as $singleStandData) {
				$standLine .= "\"".$singleStandData["ScouterName"]."\",\"".$singleStandData["ScouterTeamNumber"]."\",\"".$singleStandData["EventKey"]."\",\"".$singleStandData["TeamNumber"]."\",\"".$singleStandData["MatchNumber"]."\",\"".$singleStandData["Pre_StartingPos"]."\",\"".$singleStandData["Auto_CrossedBaseline"]."\",\"".$singleStandData["Auto_PlaceSwitch"]."\",\"".$singleStandData["Auto_PlaceScale"]."\",\"".$singleStandData["Auto_DropSwitch"]."\",\"".$singleStandData["Auto_DropScale"]."\",\"".$singleStandData["Auto_Notes"]."\",\"".$singleStandData["Teleop_SwitchPlace"]."\",\"".$singleStandData["Teleop_ScalePlace"]."\",\"".$singleStandData["Teleop_SwitchDrop"]."\",\"".$singleStandData["Teleop_ScaleDrop"]."\",\"".$singleStandData["Teleop_ExchangeVisit"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Teleop_Notes"]."\",\"".$singleStandData["Teleop_BoostUsed"]."\",\"".$singleStandData["Teleop_ForceUsed"]."\",\"".$singleStandData["Teleop_LevitateUsed"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Notes"]."\",\"".$singleStandData["Pre_NoShow"]."\",\"".$singleStandData["DOF"]."\"".PHP_EOL;
			}
					
			$zip->addFromString("standData.csv",$standLine);
		}
		$zip->close();
				
		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename='.$_GET["teamNumber"]."-".$_GET["eventKey"].'.zip');
		header('Content-Length: ' . filesize($_GET["teamNumber"]."-".$_GET["eventKey"].".zip"));
		readfile($_GET["teamNumber"]."-".$_GET["eventKey"].".zip");
		unlink($_GET["teamNumber"]."-".$_GET["eventKey"].".zip");
		exit;
		
		break;
	
	case "allData":
		$zip = new ZipArchive;
		$zip->open("allData.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
		$eventDirs = glob('[0-9][0-9][0-9][0-9]*' , GLOB_ONLYDIR);
		
		$combinedPitLineTotal = "Scout's Name,Scout's Team Number,Event Key,Team Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Robot Notes,Strategy for Power Ups,Strategy in General".PHP_EOL;
		$combinedStandLineTotal = "Scout's Name,Scout's Team Number,Event Key,Team Number,Match Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Dropped Cube attempting Switch,Autonomous: Dropped Cube attempting Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Dropped Cube attempting Switch,Teleop: Dropped Cube attempting Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Teleop: Boost Used,Teleop: Force Used,Teleop: Levitate Used,Climb,General Notes,No Show,Died on Field".PHP_EOL;
		
		foreach($eventDirs as $eventDir) {
			
			$combinedPitLineEvent = "Scout's Name,Scout's Team Number,Event Key,Team Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Robot Notes,Strategy for Power Ups,Strategy in General".PHP_EOL;
			$combinedStandLineEvent = "Scout's Name,Scout's Team Number,Event Key,Team Number,Match Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Dropped Cube attempting Switch,Autonomous: Dropped Cube attempting Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Dropped Cube attempting Switch,Teleop: Dropped Cube attempting Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Teleop: Boost Used,Teleop: Force Used,Teleop: Levitate Used,Climb,General Notes,No Show,Died on Field".PHP_EOL;
		
			
			$teamDirs = glob($eventDir.'/[0-9]*', GLOB_ONLYDIR);
			foreach ($teamDirs as $teamDir) {
				if (count($pitDataRaw = file($teamDir."/pitScout.json")) > 0) {
					$pitData = json_decode($pitDataRaw[0], true);
				
					$pitLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Robot Notes,Strategy for Power Ups,Strategy in General".PHP_EOL;
					$pitLine .= "\"".$pitData["ScouterName"]."\",\"".$pitData["ScouterTeamNumber"]."\",\"".$pitData["EventKey"]."\",\"".$pitData["TeamNumber"]."\",\"".$pitData["Pre_StartingPos"]."\",\"".$pitData["Auto_CrossedBaseline"]."\",\"".$pitData["Auto_PlaceSwitch"]."\",\"".$pitData["Auto_PlaceScale"]."\",\"".$pitData["Auto_Notes"]."\",\"".$pitData["Teleop_SwitchPlace"]."\",\"".$pitData["Teleop_ScalePlace"]."\",\"".$pitData["Teleop_ExchangeVisit"]."\",\"".$pitData["Teleop_Climb"]."\",\"".$pitData["Teleop_Notes"]."\",\"".$pitData["RobotNotes"]."\",\"".$pitData["Strategy_PowerUp"]."\",\"".$pitData["Strategy_General"]."\"".PHP_EOL;
					$combinedPitLineTotal .= "\"".$pitData["ScouterName"]."\",\"".$pitData["ScouterTeamNumber"]."\",\"".$pitData["EventKey"]."\",\"".$pitData["TeamNumber"]."\",\"".$pitData["Pre_StartingPos"]."\",\"".$pitData["Auto_CrossedBaseline"]."\",\"".$pitData["Auto_PlaceSwitch"]."\",\"".$pitData["Auto_PlaceScale"]."\",\"".$pitData["Auto_Notes"]."\",\"".$pitData["Teleop_SwitchPlace"]."\",\"".$pitData["Teleop_ScalePlace"]."\",\"".$pitData["Teleop_ExchangeVisit"]."\",\"".$pitData["Teleop_Climb"]."\",\"".$pitData["Teleop_Notes"]."\",\"".$pitData["RobotNotes"]."\",\"".$pitData["Strategy_PowerUp"]."\",\"".$pitData["Strategy_General"]."\"".PHP_EOL;
					$combinedPitLineEvent .= "\"".$pitData["ScouterName"]."\",\"".$pitData["ScouterTeamNumber"]."\",\"".$pitData["EventKey"]."\",\"".$pitData["TeamNumber"]."\",\"".$pitData["Pre_StartingPos"]."\",\"".$pitData["Auto_CrossedBaseline"]."\",\"".$pitData["Auto_PlaceSwitch"]."\",\"".$pitData["Auto_PlaceScale"]."\",\"".$pitData["Auto_Notes"]."\",\"".$pitData["Teleop_SwitchPlace"]."\",\"".$pitData["Teleop_ScalePlace"]."\",\"".$pitData["Teleop_ExchangeVisit"]."\",\"".$pitData["Teleop_Climb"]."\",\"".$pitData["Teleop_Notes"]."\",\"".$pitData["RobotNotes"]."\",\"".$pitData["Strategy_PowerUp"]."\",\"".$pitData["Strategy_General"]."\"".PHP_EOL;
					$zip->addFromString($teamDir."/pitData.csv",$pitLine);
				}
					
				if (count($standDataRaw = file($teamDir."/standScout.json")) > 0) {
					$standData = array();
					foreach ($standDataRaw as $standRaw) {
						$standDataTmp = json_decode($standRaw, true);
						if (!isSet($standDataTmp["Auto_DropSwitch"])) {
							$standDataTmp["Auto_DropSwitch"] = 0;
							$standDataTmp["Auto_DropScale"] = 0;
							$standDataTmp["Teleop_SwitchDrop"] = 0;
							$standDataTmp["Teleop_ScaleDrop"] = 0;
						}
						$standData[] = $standDataTmp;
					}
					
					$standLine = "Scout's Name,Scout's Team Number,Event Key,Team Number,Match Number,Starting Position,Autonomous: Crossed Baseline,Autonomous: Placed Cube on Switch,Autonomous: Placed Cube on Scale,Autonomous: Dropped Cube attempting Switch,Autonomous: Dropped Cube attempting Scale,Autonomous: Notes,Teleop: Placed Cube on Switch,Teleop: Placed Cube on Scale,Teleop: Dropped Cube attempting Switch,Teleop: Dropped Cube attempting Scale,Teleop: Number of Exchange Visits,Teleop: Climb,Teleop: Notes,Teleop: Boost Used,Teleop: Force Used,Teleop: Levitate Used,Climb,General Notes,No Show,Died on Field".PHP_EOL;
					foreach ($standData as $singleStandData) {
						$standLine .= "\"".$singleStandData["ScouterName"]."\",\"".$singleStandData["ScouterTeamNumber"]."\",\"".$singleStandData["EventKey"]."\",\"".$singleStandData["TeamNumber"]."\",\"".$singleStandData["MatchNumber"]."\",\"".$singleStandData["Pre_StartingPos"]."\",\"".$singleStandData["Auto_CrossedBaseline"]."\",\"".$singleStandData["Auto_PlaceSwitch"]."\",\"".$singleStandData["Auto_PlaceScale"]."\",\"".$singleStandData["Auto_DropSwitch"]."\",\"".$singleStandData["Auto_DropScale"]."\",\"".$singleStandData["Auto_Notes"]."\",\"".$singleStandData["Teleop_SwitchPlace"]."\",\"".$singleStandData["Teleop_ScalePlace"]."\",\"".$singleStandData["Teleop_SwitchDrop"]."\",\"".$singleStandData["Teleop_ScaleDrop"]."\",\"".$singleStandData["Teleop_ExchangeVisit"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Teleop_Notes"]."\",\"".$singleStandData["Teleop_BoostUsed"]."\",\"".$singleStandData["Teleop_ForceUsed"]."\",\"".$singleStandData["Teleop_LevitateUsed"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Notes"]."\",\"".$singleStandData["Pre_NoShow"]."\",\"".$singleStandData["DOF"]."\"".PHP_EOL;
						$combinedStandLineTotal .= "\"".$singleStandData["ScouterName"]."\",\"".$singleStandData["ScouterTeamNumber"]."\",\"".$singleStandData["EventKey"]."\",\"".$singleStandData["TeamNumber"]."\",\"".$singleStandData["MatchNumber"]."\",\"".$singleStandData["Pre_StartingPos"]."\",\"".$singleStandData["Auto_CrossedBaseline"]."\",\"".$singleStandData["Auto_PlaceSwitch"]."\",\"".$singleStandData["Auto_PlaceScale"]."\",\"".$singleStandData["Auto_DropSwitch"]."\",\"".$singleStandData["Auto_DropScale"]."\",\"".$singleStandData["Auto_Notes"]."\",\"".$singleStandData["Teleop_SwitchPlace"]."\",\"".$singleStandData["Teleop_ScalePlace"]."\",\"".$singleStandData["Teleop_SwitchDrop"]."\",\"".$singleStandData["Teleop_ScaleDrop"]."\",\"".$singleStandData["Teleop_ExchangeVisit"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Teleop_Notes"]."\",\"".$singleStandData["Teleop_BoostUsed"]."\",\"".$singleStandData["Teleop_ForceUsed"]."\",\"".$singleStandData["Teleop_LevitateUsed"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Notes"]."\",\"".$singleStandData["Pre_NoShow"]."\",\"".$singleStandData["DOF"]."\"".PHP_EOL;
						$combinedStandLineEvent .= "\"".$singleStandData["ScouterName"]."\",\"".$singleStandData["ScouterTeamNumber"]."\",\"".$singleStandData["EventKey"]."\",\"".$singleStandData["TeamNumber"]."\",\"".$singleStandData["MatchNumber"]."\",\"".$singleStandData["Pre_StartingPos"]."\",\"".$singleStandData["Auto_CrossedBaseline"]."\",\"".$singleStandData["Auto_PlaceSwitch"]."\",\"".$singleStandData["Auto_PlaceScale"]."\",\"".$singleStandData["Auto_DropSwitch"]."\",\"".$singleStandData["Auto_DropScale"]."\",\"".$singleStandData["Auto_Notes"]."\",\"".$singleStandData["Teleop_SwitchPlace"]."\",\"".$singleStandData["Teleop_ScalePlace"]."\",\"".$singleStandData["Teleop_SwitchDrop"]."\",\"".$singleStandData["Teleop_ScaleDrop"]."\",\"".$singleStandData["Teleop_ExchangeVisit"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Teleop_Notes"]."\",\"".$singleStandData["Teleop_BoostUsed"]."\",\"".$singleStandData["Teleop_ForceUsed"]."\",\"".$singleStandData["Teleop_LevitateUsed"]."\",\"".$singleStandData["Post_Climb"]."\",\"".$singleStandData["Notes"]."\",\"".$singleStandData["Pre_NoShow"]."\",\"".$singleStandData["DOF"]."\"".PHP_EOL;
					}
					
					$zip->addFromString($teamDir."/standData.csv",$standLine);
				}
			}
			
			$zip->addFromString($eventDir."/combinedPitData.csv",$combinedPitLineEvent);
			$zip->addFromString($eventDir."/combinedStandData.csv",$combinedStandLineEvent);
		}
		
		$zip->addFromString("combinedPitData.csv",$combinedPitLineTotal);
		$zip->addFromString("combinedStandData.csv",$combinedStandLineTotal);
		
		$zip->close();
				
		header('Content-Type: application/zip');
		header('Content-disposition: attachment; filename=ExportedData.zip');
		header('Content-Length: ' . filesize("allData.zip"));
		readfile("allData.zip");
		unlink("allData.zip");
		exit;
		
		break;
		
	default:
		http_response_code(400);
		exit;
}
?>