<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 주문일괄삭제 관련 Process
 */

//Page Info
$pageMenuIdx = 76;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = array();
$response["msg"] = "";

$mode = $_POST["mode"];

if($mode == "order_batch_delete_seller_idx"){
	//주문일괄삭제

	$seller_idx = $_POST["seller_idx"];
	$order_date = $_POST["order_date"];
	$order_time_start = $_POST["order_time_start"];
	$order_time_end = $_POST["order_time_end"];

	$C_Order = new Order();
	$rst = $C_Order -> deleteOrderBatchDelete($seller_idx, $order_date, $order_time_start, $order_time_end);

	$response["result"] = true;
	$response["data"] = $rst;

}

echo json_encode($response);
?>