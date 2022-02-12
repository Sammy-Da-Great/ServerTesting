<?php

if (!isSet($_GET,$_GET["teamNumber"],$_GET["eventCode"])) {
	$error = "Something went wrong, please try again. Error: BADREQUEST";
	include "index.php";
	exit;
}
$seasonYear = substr($_GET["eventCode"],0,4);
$path = "api/v1/viewTeam/".$seasonYear.".php";
if (file_exists($path)) {
	include $path;
} else {
	$error = "Something went wrong, please try again. Error: VIEWTEAMNOTFOUND";
	include "index.php";
	exit;
}
?>