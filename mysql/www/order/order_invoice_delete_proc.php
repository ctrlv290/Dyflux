<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 송장일괄삭제(조회) 관련 Process
 */

//Page Info
$pageMenuIdx = 93;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = array();
$response["msg"] = "";

$mode = $_POST["mode"];

if($mode == "order_invoice_delete_all"){
	//송장일괄삭제 처리
	$invoice_date       = $_POST["invoice_date"];
	$invoice_time_start = $_POST["invoice_time_start"];
	$invoice_time_end   = $_POST["invoice_time_end"];
	$is_include_shipped = ($_POST["is_include_shipped"] == "Y") ? true : false;

	$C_Order = new Order();
	$rst = $C_Order -> deleteOrderInvoiceAll($invoice_date, $invoice_time_start, $invoice_time_end, $is_include_shipped);

	$response["result"] = true;
	$response["data"] = $rst;
}

echo json_encode($response);
?>