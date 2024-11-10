<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 배송일괄취소(조회) 관련 Process
 */

//Page Info
$pageMenuIdx = 85;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = array();
$response["msg"] = "";

$mode = $_POST["mode"];

if($mode == "order_shipped_cancel_all"){
	//배송일괄취소 처리
	$invoice_date       = $_POST["shipping_date"];
	$invoice_time_start = $_POST["shipping_time_start"];
	$invoice_time_end   = $_POST["shipping_time_end"];

	$C_Order = new Order();
	$rst = $C_Order -> updateOrderShippedCancelAll($invoice_date, $invoice_time_start, $invoice_time_end);

	$response["result"] = true;
	$response["data"] = $rst;
}

echo json_encode($response);
?>