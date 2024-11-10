<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 관련 Process
 */

//Page Info
$pageMenuIdx = 199;
//Init
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = array();
$response["msg"] = "";

$mode = $_POST["mode"];

if($mode == "get_stock_amount_by_status") {
	//전체 입고 처리

	$product_option_idx = $_POST["product_option_idx"];
	$stock_status       = $_POST["stock_status"];
	$stock_unit_price   = $_POST["stock_unit_price"];

	$C_Product = new Product();
	$C_Stock = new Stock();

	//상품 확인
	$_view = $C_Product -> getProductOptionData($product_option_idx);
	if ($_view) {

		$current_stock_amount = $C_Stock->getCurrentStockAmountByPrice($product_option_idx, $stock_status, $stock_unit_price);

		$response["result"] = true;
		$response["data"] = array("current_stock_amount" => $current_stock_amount);

	} else {
		$response["msg"] = "잘못된 재고 데이터 입니다.";
	}

	echo json_encode($response);
	exit;
}elseif($mode == "control_stock_amount"){


	//print_r2($_POST);

	$product_option_idx   = $_POST["product_option_idx"];
	$stock_unit_price     = $_POST["stock_unit_price"];     //단가
	$stock_control_status = $_POST["stock_control_status"]; //변경 전 상태
	$stock_status         = $_POST["stock_status"];         //변경 후 상태
	$stock_status2        = $_POST["stock_status2"];        //변경 후 상태2 [출고지회송, 불량 의 sub상태]
	$stock_amount         = $_POST["stock_amount"];         //변경 수량
	$stock_msg            = $_POST["stock_msg"];            //메모

	$C_Product = new Product();
	$C_Stock = new Stock();

	//불량 또는 출고지회송 일 경우 sub 상태 값 설정
	if($stock_status == "BAD" || $stock_status == "FAC_RETURN"){
		$stock_status = $stock_status2;
	}

	$stock_amount = str_replace(",", "", $stock_amount);

	//상품 확인
	$_view = $C_Product -> getProductOptionData($product_option_idx);
	if ($_view && is_numeric($stock_amount)) {

		//현 재고 수량 가져오기
		$current_stock_amount = $C_Stock->getCurrentStockAmountByPrice($product_option_idx, $stock_control_status, $stock_unit_price);

		//변경 수량과 비교
		if($current_stock_amount < $stock_amount){
			put_msg_and_back("작업 가능한 재고 수량이 모자릅니다. (수량 부족)");
		}else{

			//변경 가능 수량
			//변경 실행
			$rst = $C_Stock -> controlStockAmount($product_option_idx, $stock_unit_price, $stock_control_status, $stock_status, $stock_amount, $stock_msg);

			if($rst["result"]){
				$script = "
					try{
						opener.StockProduct.StockProductListReLoad();
					}catch(e){}
					try{
						opener.StockProduct.StockListReLoad();
					}catch(e){}
					try{
						opener.StockProduct.StockPeriodListReLoad();
					}catch(e){}
				";
				put_msg_and_exec_script_and_close("재고 작업이 완료되었습니다.", $script);
			}else{
				put_msg_and_back($rst["msg"]);
			}

		}

	} else {
		put_msg_and_back("잘못된 재고 데이터 입니다.");
	}
}
