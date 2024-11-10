<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 매칭 Process
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
$C_Product = new Product();

$mode                  = $_POST["mode"];


if($mode == "order_matching_save") {

	$order_idx             = $_POST["order_idx"];
	$seller_idx            = $_POST["seller_idx"];
	$market_product_no     = $_POST["market_product_no"];       //판매처 상품코드 자동생성 (Y) 일 경우 빈 값으로 넘어옴!!!
	$market_product_name   = $_POST["market_product_name"];
	$market_product_option = $_POST["market_product_option"];
	$order_cnt             = $_POST["order_cnt"];
	$matching_save         = $_POST["matching_save"];
	$product_list          = $_POST["product_list"];

	$product_list_ary = json_decode(stripslashes($product_list), true);

	//매칭 저장
	$insertedArray = $C_Order->saveOrderMatching($order_idx, $order_cnt, $seller_idx, $product_list_ary, $matching_save);

	if(count($product_list_ary) != count($insertedArray)){
		$response["result"] = false;
		$response["msg"] = "매칭에 실패 하였습니다.";
	}else{
		$response["result"] = true;
	}

	//매칭 정보 저장
	if($matching_save  == "Y"){
		$m_save_result = $C_Product -> insertProductMatchingInfo($seller_idx, $market_product_no, $market_product_name, $market_product_option, $product_list_ary);

		$test = $C_Order->updateMatchingIdxForManual($insertedArray, $m_save_result);
	}

}elseif($mode == "exec_auto_matching"){

	//매칭 대상 가져오기
	$order_list = $C_Order->getOrderListForAutoMatching();

	//전체 매칭 대기 수
	$total = count($order_list);

	//자동 매칭 된 수
	$auto_cnt = 0;

	foreach ($order_list as $ord){

		//매칭 정보가 있는지 확인
		$order_idx = $ord["order_idx"];
		$seller_idx = $ord["seller_idx"];
		$market_product_no = $ord["market_product_no"];
		$market_product_name = $ord["market_product_name"];
		$market_product_option = $ord["market_product_option"];

		$tmp = $C_Order -> execOrderMatching($order_idx);

		if($tmp){
			$auto_cnt++;
		}
	}

	$response["result"] = true;
	$response["total"] = $total;
	$response["matching"] = $auto_cnt;
}elseif($mode == "cancel_matching"){

	$order_idx = $_POST["order_idx"];

	$rst = $C_Order -> cancelOrderMatching($order_idx);

	$response["result"] = $rst;
}

echo json_encode($response, true);
?>
