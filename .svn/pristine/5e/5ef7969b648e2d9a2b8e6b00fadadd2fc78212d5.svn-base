<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 수수료관리 관련 Process
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

$C_Product = new Product();

if($mode == "check_market_product_no") {
	$seller_idx        = $_POST["seller_idx"];
	$market_product_no = $_POST["market_product_no"];

	$rst = $C_Product->dupCheckProductCommissionMarketProductNo($seller_idx, $market_product_no);

	if($rst){
		$response["result"] = true;
	}
}elseif($mode == "commission_delete"){

	$comm_idx = $_POST["comm_idx"];

	$rst = $C_Product->deleteProductCommission($comm_idx);

	if($rst){
		$response["result"] = true;
	}
}

echo json_encode($response);
?>