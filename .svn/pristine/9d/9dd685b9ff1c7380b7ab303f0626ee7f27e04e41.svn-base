<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 수수료관리 관련 Process
 */

//Page Info
$pageMenuIdx = 206;
//Init
include_once "../_init_.php";

$mode = $_POST["mode"];

$C_Product = new Product();

if($mode == "add") {

	$seller_idx          = $_POST["seller_idx"];
	$market_product_no   = $_POST["market_product_no"];
	$market_commission   = $_POST["market_commission"];
	$delivery_commission = $_POST["delivery_commission"];
	$product_idx_list    = $_POST["product_idx"];
	$product_option_idx  = $_POST["product_option_idx"];

	//중복확인
	$dup = $C_Product->dupCheckProductCommissionMarketProductNo($seller_idx, $market_product_no);

	if(!$dup){
		put_msg_and_back("이미 등록된 판매처상품코드입니다.");
		exit;
	}

	$rst = $C_Product->insertProductCommission($seller_idx, $market_product_no, $market_commission, $delivery_commission, $product_idx_list, $product_option_idx);

	if($rst){
		$exec_script = "
				alert('등록되었습니다.');		
				try{
					opener.ProductCommission.ProductCommissionListReload();
				}catch(e){
				}
			";
		exec_script_and_close($exec_script);
	}else{
		put_msg_and_back("오류가 발생하였습니다.");
		exit;
	}

}elseif($mode == "update"){

	$comm_idx            = $_POST["comm_idx"];
	$delivery_commission = $_POST["delivery_commission"];
	$product_idx_list    = $_POST["product_idx"];
	$product_option_idx  = $_POST["product_option_idx"];

	$rst = $C_Product->updateProductCommission($comm_idx, $market_commission, $delivery_commission, $product_idx_list, $product_option_idx);

	if($rst){
		$exec_script = "
				alert('수정되었습니다.');		
				try{
					opener.ProductCommission.ProductCommissionListReload();
				}catch(e){
				}
			";
		exec_script_and_close($exec_script);
	}else{
		put_msg_and_back("오류가 발생하였습니다.");
		exit;
	}

}

?>