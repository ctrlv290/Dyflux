<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 페이지 -주문 생성 관련 Process
 */

//Page Info
$pageMenuIdx = 206;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";
$response["order_pack_idx"] = "";

$mode = $_POST["mode"];

$C_Order = new Order();
$C_CS = new CS();

if($mode == "new_order_write") {
	$rst = $C_Order -> insertNewAcceptOrder($_POST);
	if($rst) $response["result"] = true;

}elseif($mode == "get_order_hold_status"){

	//현재 보류 상태 가져오기

	$rst = $C_Order -> getOrderHoldStatus($_POST["order_pack_idx"]);

	if($rst === false){
		$response["result"] = false;
	}else{
		$response["result"] = true;
		$response["data"] = $rst;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}
}elseif($mode == "set_order_hold"){

	//보류 설정

	$order_pack_idx   = $_POST["order_pack_idx"];
	$order_is_hold    = $_POST["order_is_hold"];
	$cs_msg           = $_POST["cs_msg"];
	$set_alert        = $_POST["set_alert"];
	$set_alert_date   = $_POST["set_alert_date"];
	$set_alert_time_h = $_POST["set_alert_time_h"];
	$set_alert_time_m = $_POST["set_alert_time_m"];

	$set_alert_datetime = $set_alert_date . " " . $set_alert_time_h . ":" . $set_alert_time_m . ":00";

	if($order_is_hold == "Y"){
		$rst = $C_CS -> setOrderHoldOn($order_pack_idx, $cs_msg, $set_alert, $set_alert_datetime);
	}elseif($order_is_hold == "N"){
		$rst = $C_CS -> setOrderHoldOff($order_pack_idx, $cs_msg);
	}

	$response["result"]         = $rst;
	$response["order_pack_idx"] = $_POST["order_pack_idx"];

}elseif($mode == "set_address_change"){

	//배송 정보 변경
	$order_pack_idx  = $_POST["order_pack_idx"];
	$receive_name    = $_POST["receive_name"];
	$receive_tp_num  = $_POST["receive_tp_num"];
	$receive_hp_num  = $_POST["receive_hp_num"];
	$receive_zipcode = $_POST["receive_zipcode"];
	$receive_addr1   = $_POST["receive_addr1"];
	$receive_memo    = $_POST["receive_memo"];
	$cs_msg          = $_POST["cs_msg"];

	$rst = $C_CS -> updateOrderAddressIncludeRel($order_pack_idx, $receive_name, $receive_tp_num, $receive_hp_num, $receive_zipcode, $receive_addr1, $receive_memo, $cs_msg);

	$response["result"]         = $rst;
	$response["order_pack_idx"] = $_POST["order_pack_idx"];

}elseif($mode == "insert_invoice"){

	//송장입력
	$order_pack_idx = $_POST["order_pack_idx"];
	$delivery_code  = $_POST["delivery_code"];
	$invoice_no     = $_POST["invoice_no"];
	$cs_msg         = $_POST["cs_msg"];

	//송장입력
	$rst = $C_Order -> updateOrderStepToInvoice($order_pack_idx, $invoice_no, $delivery_code, false, $cs_msg);

	if($rst["result"]){
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = $rst["msg"];
	}

}elseif($mode == "delete_invoice"){

	//송장삭제
	$order_pack_idx = $_POST["order_pack_idx"];
	$invoice_no     = $_POST["invoice_no"];
	$cs_msg         = $_POST["cs_msg"];

	//송장번호로 송장 삭제
	$rst = $C_Order -> deleteOrderInvoiceByInvoiceNo($invoice_no, false, $cs_msg);

	if($rst){
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "";
	}
}elseif($mode == "update_shipped"){

	//배송확인 - 송장->배송
	$order_pack_idx = $_POST["order_pack_idx"];
	$invoice_no     = $_POST["invoice_no"];
	$cs_msg         = $_POST["cs_msg"];

	$rst = $C_Order -> updateOrderStepToShippedByInvoiceNo($invoice_no, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다. 배송처리에 실패하였습니다.";
	}

}elseif($mode == "cancel_shipped"){

	//배송취소 - 배송->송장
	$order_pack_idx = $_POST["order_pack_idx"];
	$invoice_no     = $_POST["invoice_no"];
	$cs_msg         = $_POST["cs_msg"];

	$rst = $C_Order -> updateOrderShippedToCancelByInvoiceNo($invoice_no, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다. 배송처리에 실패하였습니다.";
	}

}elseif($mode == "package_add"){

	//합포 추가
	$order_pack_idx = $_POST["order_pack_idx"];
	$order_idx_list = $_POST["order_idx_list"];
	$cs_msg         = $_POST["cs_msg"];

	$order_idx_list_ary = explode(",", $order_idx_list);

	foreach($order_idx_list_ary as $order_idx){

		$chk = $C_Order -> isCanOrderPackageAdd($order_idx, $order_pack_idx);

		if($chk["result"]) {
			$C_Order->execOrderPackage($order_idx, $order_pack_idx, $cs_msg);
		}
	}

	$response["result"]      = $chk["result"];
	$response["msg"]         = $chk["msg"];
	$response["order_pack_idx"] = $_POST["order_pack_idx"];
}elseif($mode == "package_lock"){

	//합포금지 설정
	$order_pack_idx = $_POST["order_pack_idx"];
	$cs_msg         = $_POST["cs_msg"];
	$order_is_lock  = "Y";

	$rst = $C_CS -> updateOrderPackageLock($order_pack_idx, $order_is_lock, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다. 배송처리에 실패하였습니다.";
	}

}elseif($mode == "package_unlock"){

	//합포금지 해제
	$order_pack_idx = $_POST["order_pack_idx"];
	$cs_msg         = $_POST["cs_msg"];
	$order_is_lock  = "N";

	$rst = $C_CS -> updateOrderPackageLock($order_pack_idx, $order_is_lock, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다. 배송처리에 실패하였습니다.";
	}


}elseif($mode == "cancel_all"){

	//전체 취소
	$order_pack_idx  = $_POST["order_pack_idx"];
	$cs_reason_code1 = $_POST["cs_reason_code1"];
	$cs_reason_code2 = $_POST["cs_reason_code2"];
	$cs_msg          = $_POST["cs_msg"];


	$rst = $C_CS -> updateOrderCancelAll($order_pack_idx, $cs_reason_code1, $cs_reason_code2, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다. 전체취소에 실패하였습니다.";
	}

}elseif($mode == "restore_all"){

	//전체정상복귀
	//전체 취소
	$order_pack_idx  = $_POST["order_pack_idx"];
	$cs_msg          = $_POST["cs_msg"];

	$rst = $C_CS -> updateOrderRestoreAll($order_pack_idx, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다. 전체정상복귀에 실패하였습니다.";
	}

}elseif($mode == "cancel_one"){

	//개별취소
	$order_pack_idx  = $_POST["order_pack_idx"];
	$except_list = $_POST["except"];
	$cs_reason_code1 = $_POST["cs_reason_code1"];
	$cs_reason_code2 = $_POST["cs_reason_code2"];
	$cs_msg = $_POST["cs_msg"];


	foreach($except_list as $except)
	{
		$rst = $C_CS -> updateOrderCancelOne($order_pack_idx, $except, $cs_reason_code1, $cs_reason_code2, $cs_msg);
	}

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다. 개별취소에 실패하였습니다.";
	}
}elseif($mode == "restore_one"){

	//개별정상복귀
	$order_pack_idx  = $_POST["order_pack_idx"];
	$except_list = $_POST["except"];
	$cs_msg = $_POST["cs_msg"];

	foreach($except_list as $except)
	{
		$rst = $C_CS -> updateOrderRestoreOne($order_pack_idx, $except, $cs_msg);
	}

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다. 개별취소에 실패하였습니다.";
	}
}elseif($mode == "return_add"){

	//회수 추가 (반품 접수)

	$order_idx            = $_POST["order_idx"];
	$order_pack_idx       = $_POST["order_pack_idx"];
	$is_auto_stock_order  = $_POST["is_auto_stock_order"];

	$address_name         = $_POST["address_name"];
	$address_tel_num      = $_POST["address_tel_num"];
	$address_hp_num       = $_POST["address_hp_num"];
	$address_zipcode      = $_POST["address_zipcode"];
	$address_address      = $_POST["address_address"];

	$send_name            = $_POST["send_name"];
	$send_tel_num         = $_POST["send_tel_num"];
	$send_hp_num          = $_POST["send_hp_num"];
	$send_zipcode         = $_POST["send_zipcode"];
	$send_address         = $_POST["send_address"];
	$send_memo            = $_POST["send_memo"];

	$delivery_pay_type    = $_POST["delivery_pay_type"];
	$delivery_return_type = $_POST["delivery_return_type"];
	$box_num              = $_POST["box_num"];
	$product_price        = $_POST["product_price"];
	$delivery_price       = $_POST["delivery_price"];
	$pay_site             = $_POST["pay_site"];
	$pay_pack             = $_POST["pay_pack"];
	$pay_account          = $_POST["pay_account"];

	$product_list         = $_POST["product_list"];

	$cs_msg               = $_POST["cs_msg"];

	$send_info                 = array();
	$send_info["send_name"]    = $send_name;
	$send_info["send_tel_num"] = $send_tel_num;
	$send_info["send_hp_num"]  = $send_hp_num;
	$send_info["send_zipcode"] = $send_zipcode;
	$send_info["send_address"] = $send_address;
	$send_info["send_memo"]    = $send_memo;

	$receive_info                    = array();
	$receive_info["receive_name"]    = $address_name;
	$receive_info["receive_tel_num"] = $address_tel_num;
	$receive_info["receive_hp_num"]  = $address_hp_num;
	$receive_info["receive_zipcode"] = $address_zipcode;
	$receive_info["receive_address"] = $address_address;

	$request_info                         = array();
	$request_info["delivery_pay_type"]    = $delivery_pay_type;
	$request_info["delivery_return_type"] = $delivery_return_type;
	$request_info["box_num"]              = $box_num;
	$request_info["product_price"]        = $product_price;
	$request_info["delivery_price"]       = $delivery_price;
	$request_info["pay_site"]             = $pay_site;
	$request_info["pay_pack"]             = $pay_pack;
	$request_info["pay_account"]          = $pay_account;

	$rst = $C_CS -> insertOrderReturn($order_idx, $order_pack_idx, $send_info, $receive_info, $request_info, $is_auto_stock_order, $product_list, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}
}elseif($mode == "stock_return"){
    //재고회수
    $order_idx            = $_POST["order_idx"];
    $order_pack_idx       = $_POST["order_pack_idx"];
    $product_list         = $_POST["product_list"];
    $cs_msg               = $_POST["cs_msg"];
    $rst = $C_CS -> insertStockReturn($order_idx, $order_pack_idx, $product_list, $cs_msg);
    if($rst) {
        $response["result"]         = true;
        $response["order_pack_idx"] = $_POST["order_pack_idx"];
    }else{
        $response["result"]         = false;
        $response["order_pack_idx"] = $_POST["order_pack_idx"];
        $response["msg"]            = "오류가 발생하였습니다.";
    }
}elseif($mode == "return_update"){

	//회수 수정

	$return_idx           = $_POST["return_idx"];
	$order_idx            = $_POST["order_idx"];
	$order_pack_idx       = $_POST["order_pack_idx"];
	$is_auto_stock_order  = $_POST["is_auto_stock_order"];

	$address_name         = $_POST["address_name"];
	$address_tel_num      = $_POST["address_tel_num"];
	$address_hp_num       = $_POST["address_hp_num"];
	$address_zipcode      = $_POST["address_zipcode"];
	$address_address      = $_POST["address_address"];

	$send_name            = $_POST["send_name"];
	$send_tel_num         = $_POST["send_tel_num"];
	$send_hp_num          = $_POST["send_hp_num"];
	$send_zipcode         = $_POST["send_zipcode"];
	$send_address         = $_POST["send_address"];
	$send_memo            = $_POST["send_memo"];

	$delivery_pay_type    = $_POST["delivery_pay_type"];
	$delivery_return_type = $_POST["delivery_return_type"];
	$box_num              = $_POST["box_num"];
	$product_price        = $_POST["product_price"];
	$delivery_price       = $_POST["delivery_price"];
	$pay_site             = $_POST["pay_site"];
	$pay_pack             = $_POST["pay_pack"];
	$pay_account          = $_POST["pay_account"];

	$cs_msg               = $_POST["cs_msg"];

	$send_info                 = array();
	$send_info["send_name"]    = $send_name;
	$send_info["send_tel_num"] = $send_tel_num;
	$send_info["send_hp_num"]  = $send_hp_num;
	$send_info["send_zipcode"] = $send_zipcode;
	$send_info["send_address"] = $send_address;
	$send_info["send_memo"]    = $send_memo;

	$receive_info                    = array();
	$receive_info["receive_name"]    = $address_name;
	$receive_info["receive_tel_num"] = $address_tel_num;
	$receive_info["receive_hp_num"]  = $address_hp_num;
	$receive_info["receive_zipcode"] = $address_zipcode;
	$receive_info["receive_address"] = $address_address;

	$request_info                         = array();
	$request_info["delivery_pay_type"]    = $delivery_pay_type;
	$request_info["delivery_return_type"] = $delivery_return_type;
	$request_info["box_num"]              = $box_num;
	$request_info["product_price"]        = $product_price;
	$request_info["delivery_price"]       = $delivery_price;
	$request_info["pay_site"]             = $pay_site;
	$request_info["pay_pack"]             = $pay_pack;
	$request_info["pay_account"]          = $pay_account;

	$rst = $C_CS -> updateOrderReturn($return_idx, $order_idx, $order_pack_idx, $send_info, $receive_info, $request_info, $is_auto_stock_order, $product_list, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}
}elseif($mode == "return_delete"){

	//회수 취소

	$return_idx           = $_POST["return_idx"];
	$order_pack_idx       = $_POST["order_pack_idx"];
	$cs_msg               = $_POST["cs_msg"];

	$rst = $C_CS -> deleteOrderReturn($return_idx, $order_pack_idx, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}


}elseif($mode == "product_change"){

	//상품 교환

	$order_idx            = $_POST["order_idx"];
	$order_matching_idx   = $_POST["order_matching_idx"];
	$order_pack_idx       = $_POST["order_pack_idx"];
	$product_idx          = $_POST["product_idx"];
	$product_option_idx   = $_POST["product_option_idx"];
	$cs_reason_code1      = $_POST["cs_reason_code1"];
	$cs_reason_code2      = $_POST["cs_reason_code2"];
	$c_product_idx        = $_POST["c_product_idx"];
	$c_product_option_idx = $_POST["c_product_option_idx"];
	$c_product_option_cnt = $_POST["c_product_option_cnt"];
	$c_product_sale_price = $_POST["c_product_sale_price"];
	$c_add_price          = $_POST["c_add_price"];
	$cs_msg               = $_POST["cs_msg"];

	$rst = $C_CS -> changeOrderProduct($order_pack_idx, $order_idx, $order_matching_idx, $c_product_idx, $c_product_option_idx, $c_product_option_cnt, $c_product_sale_price, $c_add_price, $cs_reason_code1, $cs_reason_code2, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}
}elseif($mode == "product_add"){

	//상품 추가

	$order_idx                 = $_POST["order_idx"];
	$order_pack_idx            = $_POST["order_pack_idx"];
	$seller_idx                = $_POST["seller_idx"];
	$seller_type                = $_POST["seller_type"];
	$product_idx               = $_POST["product_idx"];
	$product_option_idx        = $_POST["product_option_idx"];
	$product_option_cnt        = $_POST["product_option_cnt"];
	$product_option_sale_price = $_POST["product_option_sale_price"];
	$cs_msg                    = $_POST["cs_msg"];

	$rst = $C_CS -> addOrderProduct($order_pack_idx, $order_idx, $seller_idx, $seller_type, $product_idx, $product_option_idx, $product_option_cnt, $product_option_sale_price, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}
}elseif($mode == "order_copy_whole") {
	//주문 전체 복사
	$order_idx       = $_POST["order_idx"];
	$order_pack_idx  = $_POST["order_pack_idx"];
	$copy_seller_idx = $_POST["copy_seller_idx"];
	$cs_msg          = $_POST["cs_msg"];

	$rst = $C_CS->copyOrderWhole($order_idx, $copy_seller_idx, $cs_msg);

	if ($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	} else {
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}
}elseif($mode == "order_copy_one"){
	//주문 복사
	$order_idx                      = $_POST["order_idx"];
	$order_pack_idx                 = $_POST["order_pack_idx"];
	$copy_seller_idx                = $_POST["copy_seller_idx"];
	$copy_product_idx               = $_POST["copy_product_idx"];
	$copy_product_option_idx        = $_POST["copy_product_option_idx"];
	$copy_product_option_cnt        = $_POST["copy_product_option_cnt"];
	$copy_product_option_sale_price = $_POST["copy_product_option_sale_price"];
	$cs_msg                         = $_POST["cs_msg"];

	$rst = $C_CS -> copyOrderOne($order_idx, $copy_seller_idx, $copy_product_idx, $copy_product_option_idx, $copy_product_option_cnt, $copy_product_option_sale_price, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}

}elseif($mode == "check_seller_type") {

	//판매자 타입 반환

	$seller_idx = $_POST["seller_idx"];

	$C_Seller = new Seller();
	$_view    = $C_Seller->getAllSellerData($seller_idx);
	$C_Seller = "";

	if ($_view) {
		$response["result"]      = true;
		$response["seller_type"] = $_view["seller_type"];
	} else {
		$response["result"] = false;
		$response["msg"]    = "오류가 발생하였습니다.";
	}

}elseif($mode == "order_cancel"){

	//주문 삭제
	$order_idx      = $_POST["order_idx"];
	$order_pack_idx = $_POST["order_pack_idx"];
	$cs_msg         = $_POST["cs_msg"];

	$rst = $C_CS->deleteOrderOne($order_idx, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}

}elseif($mode == "order_cancel_all"){

	//합포 삭제
	$order_idx      = $_POST["order_idx"];
	$order_pack_idx = $_POST["order_pack_idx"];
	$cs_msg         = $_POST["cs_msg"];

	$rst = $C_CS->deleteOrderAll($order_pack_idx, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}
}elseif($mode == "matching_delete"){

	//매칭삭제
	$order_idx         = $_POST["order_idx"];
	$order_pack_idx    = $_POST["order_pack_idx"];
	$matching_info_idx = $_POST["matching_info_idx"];
	$cs_msg            = $_POST["cs_msg"];

	$rst = $C_CS->deleteMatchingInfo($order_idx, $order_pack_idx, $matching_info_idx, $cs_msg);

	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}
}elseif($mode == "set_order_return_due"){

	//반품예정 설정/해제

	$order_idx           = $_POST["order_idx"];
	$order_pack_idx      = $_POST["order_pack_idx"];
	$order_is_return_due = $_POST["order_is_return_due"];
	$cs_msg              = $_POST["cs_msg"];

	if($order_is_return_due == "Y"){
		$rst = $C_CS -> setOrderReturnDueOn($order_idx, $order_pack_idx, $cs_msg);
	}elseif($order_is_return_due == "N"){
		$rst = $C_CS -> setOrderReturnDueOff($order_idx, $order_pack_idx, $cs_msg);
	}

	$response["result"]         = $rst;
	$response["order_pack_idx"] = $_POST["order_pack_idx"];

}elseif($mode == "set_invoice_priority"){

	$order_idx          = $_POST["order_idx"];
	$order_pack_idx     = $_POST["order_pack_idx"];
	$product_option_idx = $_POST["product_option_idx"];
	$priority_type      = $_POST["priority_type"];
	$position_number    = $_POST["position_number"];
	$cs_msg             = $_POST["cs_msg"];

	$rst = $C_CS->setInvoicePriority($order_idx, $order_pack_idx, $product_option_idx, $priority_type, $position_number, $cs_msg);
	if($rst) {
		$response["result"]         = true;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
	}else{
		$response["result"]         = false;
		$response["order_pack_idx"] = $_POST["order_pack_idx"];
		$response["msg"]            = "오류가 발생하였습니다.";
	}
}elseif($mode == "get_latest_shipping_info"){

	$rst = $C_CS -> getLatestShippingInfo();

	if($rst) {
		$response["result"]         = true;
		$response["data"]           = $rst;
	}else{
		$response["result"]         = false;
		$response["msg"]            = "오류가 발생하였습니다.";
	}

}


echo json_encode($response, true);
?>