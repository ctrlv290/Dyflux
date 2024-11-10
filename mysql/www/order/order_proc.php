<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주 관련 Process
 */

//Page Info
$pageMenuIdx = 73;
//Init
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode = $_POST["mode"];

$C_Order = new Order();



if($mode == "order_delete_all") {
	$C_Order -> deleteOrderAll();

	$response["result"] = true;

}elseif($mode == "order_delete_one") {

	$seller_idx = $_POST["seller_idx"];
	$C_Order->deleteOrderOne($seller_idx);


	$response["result"] = true;
} elseif ($mode == "order_accept_checked_confirm") { //선택 접수 처리
	//pack_idx 를 기준으로 진행
	$order_accept_temp_list = $_POST["order_pack_idx_list"];
	$cnt = $C_Order->updateOrderAcceptWholeConfirm($order_accept_temp_list);

	$response["result"] = true;
	$response["data"] = $cnt;
	$response["order_count"] = count($order_accept_temp_list);
}

echo json_encode($response, true);
?>