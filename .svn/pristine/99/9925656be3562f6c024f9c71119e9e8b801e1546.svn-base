<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 페이지 - 알람 관련 Process
 */

//Page Info
$pageMenuIdx = 206;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode = $_POST["mode"];

$C_Order = new Order();
$C_CS = new CS();

if($mode == "get_my_alarm_list") {
	$_list = $C_CS -> getMyAlarmList();

	$response["data"] = array();

	$response["result"] = true;
	if($_list){
		$response["data"] = $_list;
	}
}elseif($mode == "clear_my_alarm_list"){

	//print_r($_POST);
	$rst = $C_CS -> clearMyAlarm($_POST["cs_alarm_idx"]);
	$response["result"] = true;
}


echo json_encode($response, true);
?>