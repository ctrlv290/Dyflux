<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 페이지 -CS 내역 관련 Process
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

$C_CS = new CS();

if($mode == "get_seller_format") {
	//판매처 포맷 가져오기

	$seller_idx = $_POST["seller_idx"];

	$rst = $C_CS -> getSellerCancelFormat($seller_idx);

	if($rst) {
		$response["result"] = true;
		$response["data"]   = $rst;
	}

}elseif($mode == "update_seller_format"){

	$seller_idx          = $_POST["seller_idx"];
	$cancel_date         = $_POST["cancel_date"];
	$market_order_no     = $_POST["market_order_no"];
	$order_name          = $_POST["order_name"];
	$market_product_no   = $_POST["market_product_no"];
	$market_product_name = $_POST["market_product_name"];
	$reason              = $_POST["reason"];
	$order_idx           = $_POST["order_idx"];
	$return_invoice_no   = $_POST["return_invoice_no"];

	$rst = $C_CS -> updateSellerCancelFormat($seller_idx, $cancel_date, $market_order_no, $order_name, $market_product_no, $market_product_name, $reason, $order_idx, $return_invoice_no);


	$response["result"] = true;
}
echo json_encode($response, true);
?>