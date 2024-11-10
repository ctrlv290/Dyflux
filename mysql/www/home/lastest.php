<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Home - 최근현황 - 그래프 데이터 반환
 */

//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$C_Home = new Home();
$_last = $C_Home->getLastSaleStatistics($_POST["mode"]);

$response["result"] = false;
$response["data"] = $_last;

echo json_encode($response);
?>
