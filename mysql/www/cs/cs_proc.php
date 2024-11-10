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
$response["order_pack_idx"] = "";

$mode = $_POST["mode"];

$C_Order = new Order();
$C_CS = new CS();

if($mode == "set_one_confirm") {
	//CS 1건 확인 처리

	$cs_idx = $_POST["cs_idx"];

	$rst = $C_CS -> setCSConfirm($cs_idx);

	$response["result"] = true;

}elseif($mode == "set_all_confirm"){

	//CS 일괄 확인 처리

	$order_pack_idx = $_POST["order_pack_idx"];
	$include_auto = $_POST["include_auto"];

	$rst = $C_CS -> setCSConfirmByOrderPackIdx($order_pack_idx, $include_auto);

	$response["result"] = true;

}elseif($mode == "delete_one_cs"){

	//CS 개별 삭제
	$cs_idx = $_POST["cs_idx"];

	$rst = $C_CS -> deleteCS($cs_idx);

	$response["result"] = true;

}elseif($mode == "insert_cs"){

	//CS 이력 남기기

	$order_idx          = $_POST["order_idx"];
	$order_pack_idx     = $_POST["order_pack_idx"];
	$order_matching_idx = $_POST["order_matching_idx"];
	$product_idx        = $_POST["product_idx"];
	$product_option_idx = $_POST["product_option_idx"];
	$cs_type            = $_POST["cs_type"];
	$cs_msg             = $_POST["cs_msg"];
	$cs_file1           = $_POST["cs_file1"];
	$cs_file2           = $_POST["cs_file2"];
	$cs_file3           = $_POST["cs_file3"];
	$cs_file4           = $_POST["cs_file4"];
	$cs_file5           = $_POST["cs_file5"];
	$set_alert          = $_POST["set_alert"];
	$set_alert_date     = $_POST["set_alert_date"];
	$set_alert_time_h   = $_POST["set_alert_time_h"];
	$set_alert_time_m   = $_POST["set_alert_time_m"];
	$cs_file1           = $_POST["cs_file1"];
	$cs_file2           = $_POST["cs_file2"];
	$cs_file3           = $_POST["cs_file3"];
	$cs_file4           = $_POST["cs_file4"];
	$cs_file5           = $_POST["cs_file5"];
	$cs_file_array      = array($cs_file1, $cs_file2, $cs_file3, $cs_file4, $cs_file5);

	$set_alert_datetime = $set_alert_date . " " . $set_alert_time_h . ":" . $set_alert_time_m . ":00";

	$rst = $C_CS -> insertCSOne($order_idx, $order_pack_idx, $order_matching_idx, $product_idx, $product_option_idx, "N", "NORMAL", $cs_type, $cs_msg, $set_alert, $set_alert_datetime, $cs_file_array);

	$response["result"] = true;
}elseif($mode == "selected_insert_cs") {

	//선택 CS 등록

	$order_idx_list = $_POST["idx_list"];
	$cs_msg         = $_POST["comment"];

	foreach ($order_idx_list as $ord) {
		$order_idx      = $ord["order_idx"];
		$order_pack_idx = $ord["order_pack_idx"];

		$rst = $C_CS->insertCSOne($order_idx, $order_pack_idx, 0, 0, 0, "N", "NORMAL", "", $cs_msg, "", "", "");
	}

	$response["result"] = true;

}elseif($mode == "set_list_confirm") {
	//CS 여러건 확인 처리

	$cs_idx_list = $_POST["cs_idx_list"];

	foreach($cs_idx_list as $cs_idx) {
		$rst = $C_CS->setCSConfirm($cs_idx);
	}

	$response["result"] = true;
}elseif($mode == "set_return_confirm") {

	//반품 완료 처리
	$return_idx    = $_POST["return_idx"];
	$paid_site     = $_POST["paid_site"];
	$paid_pack     = $_POST["paid_pack"];
	$paid_account  = $_POST["paid_account"];
	$unpaid_amount = $_POST["unpaid_amount"];

	$rst = $C_CS->setReturnConfirm($return_idx, $paid_site, $paid_pack, $paid_account, $unpaid_amount);

	$response["result"] = true;
}elseif($mode == "set_return_list_confirm") {
	//반품 선택 완료 처리
	if(isset($_POST["return_confirm_list"])) {
		$return_confirm_list = $_POST["return_confirm_list"];


		foreach($return_confirm_list as $return) {

			$return_idx    = $return["return_idx"];
			$paid_site     = $return["paid_site"];
			$paid_pack     = $return["paid_pack"];
			$paid_account  = $return["paid_account"];
			$unpaid_amount = $return["unpaid_amount"];

			$rst = $C_CS->setReturnConfirm($return_idx, $paid_site, $paid_pack, $paid_account, $unpaid_amount);
		}

		$response["result"] = true;
	}else{
		$response["result"] = false;
	}

}elseif($mode == "set_seller_cancel_confirm_one"){

	$order_idx = $_POST["order_idx"];
	$confirm_val = $_POST["confirm_val"];

	$rst = $C_CS->updateSellerCancelConfirm($order_idx, $confirm_val);

	$response["result"] = true;

}elseif($mode == "set_seller_cancel_confirm_selected"){

	$order_idx_list = $_POST["order_idx_list"];
	$confirm_val = $_POST["confirm_val"];

	foreach($order_idx_list as $order_idx) {
		$rst = $C_CS->updateSellerCancelConfirm($order_idx, $confirm_val);
	}

	$response["result"] = true;
}elseif($mode == "set_seller_cancel_off_confirm"){
	$order_idx_list = $_POST["order_idx_list"];
	$confirm_val = $_POST["confirm_val"];

	foreach($order_idx_list as $order_idx) {
		$rst = $C_CS->updateSellerCancelOffConfirm($order_idx, $confirm_val);
	}

	$response["result"] = true;
}



echo json_encode($response, true);
?>