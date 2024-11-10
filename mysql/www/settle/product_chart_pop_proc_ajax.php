<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 일별상품별통계 차트 팝업 Process
 */

//Page Info
$pageMenuIdx = 285;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";


$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode = $_POST["mode"];

$C_Settle = new Settle();

if($mode == "get_chart") {

	$date_start                    = $_POST["date_start"];
	$date_end                      = $_POST["date_end"];
	$product_option_idx            = $_POST["product_option_idx"];
	$product_option_purchase_price = $_POST["product_option_purchase_price"];
	$seller_idx                    = $_POST["seller_idx"];

	$_list = $C_Settle->getProductChartData($date_start, $date_end, $product_option_idx, $product_option_purchase_price, $seller_idx);

	if($_list) {
		$response["result"] = true;
		$response["data"] = $_list;
	}else{

	}
}elseif($mode == "get_chart_month") {

	$date_start_year               = $_POST["date_start_year"];
	$date_start_month              = $_POST["date_start_month"];
	$date_end_year                 = $_POST["date_end_year"];
	$date_end_month                = $_POST["date_end_month"];
	$product_option_idx            = $_POST["product_option_idx"];
	$product_option_purchase_price = $_POST["product_option_purchase_price"];
	$seller_idx                    = $_POST["seller_idx"];

	$date_start = date("Y-m-d", strtotime($date_start_year . "-" . make2digit($date_start_month) . "-01"));
	$date_end = date("Y-m-t", strtotime($date_end_year . "-" . make2digit($date_end_month) . "-01"));

	$_list = $C_Settle->getProductChartMonthlyData($date_start, $date_end, $product_option_idx, $product_option_purchase_price, $seller_idx);

	if ($_list) {
		$response["result"] = true;
		$response["data"]   = $_list;
	} else {

	}
}
echo json_encode($response);
?>