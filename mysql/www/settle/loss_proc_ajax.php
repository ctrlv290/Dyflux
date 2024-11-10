<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래형황 관련 Process
 */

//Page Info
$pageMenuIdx = 134;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode = $_POST["mode"];

$C_Settle = new Settle();
if($mode == "update_confirm") {

	$loss_idx = $_POST["loss_idx"];


	$tmp = $C_Settle->updateLossConfirm($loss_idx);

	$response["result"] = true;
}elseif($mode == "bank_customer_in"){

	$seller_idx = $_POST["seller_idx"];
	$date_start = $_POST["date_start"];
	$date_end = $_POST["date_end"];

	$_list = $C_Settle->getLossWidthBankCustomerIn($seller_idx, $date_start, $date_end);

	$response["result"] = true;
	$response["data"] = $_list;

}elseif($mode == "refund"){

	$seller_idx = $_POST["seller_idx"];
	$date_start = $_POST["date_start"];
	$date_end = $_POST["date_end"];

	$_list = $C_Settle->getLossWithLedgerRefund($seller_idx, $date_start, $date_end);

	$response["result"] = true;
	$response["data"] = $_list;

}

echo json_encode($response);