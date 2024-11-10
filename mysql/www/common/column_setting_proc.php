<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 항목설정 관련 Process
 */

//Page Info
$pageMenuIdx = 175;
//Init
include "../_init_.php";

//print_r($_POST);
$target                = $_POST["target"];
$mode                  = $_POST["mode"];
$saveData              = $_POST["saveData"];

$C_ColumnModel = new ColumnModel();
$rst = $C_ColumnModel -> saveColumnModel($target, $saveData);


$response = array();
$response["result"] = ($rst) ? true : false;


echo json_encode($response, true);
?>