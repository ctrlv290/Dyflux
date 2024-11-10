<?php
//Init
include_once "../_init_.php";

$mode = $_GET["mode"];

$response = array();
$response["result"] = false;
$response["data"] = array();

if($mode == "PHP"){
	$_list = scandir("../_logs", SCANDIR_SORT_DESCENDING );
	$returnResult = array();

	foreach($_list as $l){
		if(substr($l, 0, 4) == "PHP_"){
			$returnResult[] = array("name" => str_replace(".log", "", str_replace("PHP_", "", $l)), "file" => $l);
		}
	}
	$response["result"] = true;
	$response["data"] = $returnResult;
}elseif($mode == "DB"){
	$_list = scandir("../_logs", SCANDIR_SORT_DESCENDING );
	$returnResult = array();

	foreach($_list as $l){
		if(substr($l, 0, 3) == "DB_"){
			$returnResult[] = array("name" => str_replace(".log", "", str_replace("DB_", "", $l)), "file" => $l);
		}
	}
	$response["result"] = true;
	$response["data"] = $returnResult;
}elseif($mode == "DEBUG"){
	$_list = scandir("../_logs", SCANDIR_SORT_DESCENDING );
	$returnResult = array();

	foreach($_list as $l){
		if(substr($l, 0, 6) == "DEBUG_"){
			$returnResult[] = array("name" => str_replace(".log", "", str_replace("DB_", "", $l)), "file" => $l);
		}
	}
	$response["result"] = true;
	$response["data"] = $returnResult;
}

echo json_encode($response);
?>