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
if($_POST["mode"] == "SalesAmount") {
	$_last = $C_Home->getLastestSalesAmount();
}elseif($_POST["mode"] == "SalesCount") {
	$_last = $C_Home->getLastestSalesCnt();
}elseif($_POST["mode"] == "CancelCount") {
	$_last = $C_Home->getLastestCancelCnt();
}

$response["result"] = false;
$response["data"] = $_last;

echo json_encode($response);
?>
