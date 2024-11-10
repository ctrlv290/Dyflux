<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 분석 차트 팝업 관련 Process
 */

//Page Info
$pageMenuIdx = 284;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";


$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode = $_POST["mode"];

$C_Stock = new Stock();

if($mode == "get_chart") {

	$date_start         = $_POST["date_start"];
	$date_end           = $_POST["date_end"];
	$product_option_idx = $_POST["product_option_idx"];
	$stock_unit_price   = $_POST["stock_unit_price"];

	$_list = $C_Stock->getStockChartData($product_option_idx, $stock_unit_price, $date_start, $date_end);

	$response["data"] = $_list;
}

echo json_encode($response);
?>