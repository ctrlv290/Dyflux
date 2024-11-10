<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 관리 관련 Process
 */

//Page Info
$pageMenuIdx = 73;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["msg"] = "";

$C_Order = new Order();

$mode                            = $_POST["mode"];
$seller_idx                      = $_POST["seller_idx"];
$order_format_default_idx        = $_POST["order_format_default_idx"];
$order_format_seller_idx         = $_POST["order_format_seller_idx"];
$order_format_seller_header_name = $_POST["order_format_seller_header_name"];

if($mode == "save") {
	foreach($order_format_default_idx as $key => $val)
	{
		$rst = $C_Order -> saveOrderFormatSeller($seller_idx, $order_format_default_idx[$key],  $order_format_seller_idx[$key],  $order_format_seller_header_name[$key]);
	}

	$response["result"] = true;
}

echo json_encode($response, true);
?>

