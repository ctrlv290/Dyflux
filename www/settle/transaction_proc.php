<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매입매출현황 [판매일보] 관련 Process
 */

//Page Info
$pageMenuIdx = 122;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["data"] = "";
$response["msg"] = "";

$mode = $_POST["mode"];

$C_SETTLE = new Settle();

function getParameterAndSetDefault($method, $parameter_name, $default_value)
{
	global $_POST, $_GET;
	$returnValue = "";
	$_param = "";
	if($method == "get"){
		$_param = "_GET";
	}elseif($method == "post"){
		$_param = "_POST";
	}

	if(!isset($$_param[$parameter_name]) || !$$_param[$parameter_name])
	{
		if($default_value !== ""){
			$returnValue = $default_value;
		}else{
			$returnValue = "";
		}
	}else{
		$returnValue = $$_param[$parameter_name];
	}

	return $returnValue;
}

if($mode == "row_edit") {

	//정산 테이블 업데이트

	$settle_idx                         = $_POST["settle_idx"];
	//$order_amt                          = (!isset($_POST["order_amt"]) || !$_POST["order_amt"]) ? 0 : $_POST["order_amt"];
	$order_unit_price                   = (!isset($_POST["order_unit_price"]) || !$_POST["order_unit_price"]) ? 0 : $_POST["order_unit_price"];
	$product_option_purchase_price      = (!isset($_POST["product_option_purchase_price"]) || !$_POST["product_option_purchase_price"]) ? 0 : $_POST["product_option_purchase_price"];
	$product_option_cnt                 = (!isset($_POST["product_option_cnt"]) || !$_POST["product_option_cnt"]) ? 0 : $_POST["product_option_cnt"];
	$settle_sale_supply                 = (!isset($_POST["settle_sale_supply"]) || !$_POST["settle_sale_supply"]) ? 0 : $_POST["settle_sale_supply"];
	$settle_sale_supply_ex_vat          = (!isset($_POST["settle_sale_supply_ex_vat"]) || !$_POST["settle_sale_supply_ex_vat"]) ? 0 : $_POST["settle_sale_supply_ex_vat"];
	$settle_sale_commission_ex_vat      = (!isset($_POST["settle_sale_commission_ex_vat"]) || !$_POST["settle_sale_commission_ex_vat"]) ? 0 : $_POST["settle_sale_commission_ex_vat"];
	$settle_sale_commission_in_vat      = (!isset($_POST["settle_sale_commission_in_vat"]) || !$_POST["settle_sale_commission_in_vat"]) ? 0 : $_POST["settle_sale_commission_in_vat"];
	$settle_delivery_in_vat             = (!isset($_POST["settle_delivery_in_vat"]) || !$_POST["settle_delivery_in_vat"]) ? 0 : $_POST["settle_delivery_in_vat"];
	$settle_delivery_ex_vat             = (!isset($_POST["settle_delivery_ex_vat"]) || !$_POST["settle_delivery_ex_vat"]) ? 0 : $_POST["settle_delivery_ex_vat"];
	$settle_delivery_commission_ex_vat  = (!isset($_POST["settle_delivery_commission_ex_vat"]) || !$_POST["settle_delivery_commission_ex_vat"]) ? 0 : $_POST["settle_delivery_commission_ex_vat"];
	$settle_delivery_commission_in_vat  = (!isset($_POST["settle_delivery_commission_in_vat"]) || !$_POST["settle_delivery_commission_in_vat"]) ? 0 : $_POST["settle_delivery_commission_in_vat"];
	$settle_purchase_supply             = (!isset($_POST["settle_purchase_supply"]) || !$_POST["settle_purchase_supply"]) ? 0 : $_POST["settle_purchase_supply"];
	$settle_purchase_supply_ex_vat      = (!isset($_POST["settle_purchase_supply_ex_vat"]) || !$_POST["settle_purchase_supply_ex_vat"]) ? 0 : $_POST["settle_purchase_supply_ex_vat"];
	$settle_purchase_delivery_in_vat    = (!isset($_POST["settle_purchase_delivery_in_vat"]) || !$_POST["settle_purchase_delivery_in_vat"]) ? 0 : $_POST["settle_purchase_delivery_in_vat"];
	$settle_purchase_delivery_ex_vat    = (!isset($_POST["settle_purchase_delivery_ex_vat"]) || !$_POST["settle_purchase_delivery_ex_vat"]) ? 0 : $_POST["settle_purchase_delivery_ex_vat"];
//	$settle_sale_profit                 = (!isset($_POST["settle_sale_profit"]) || !$_POST["settle_sale_profit"]) ? 0 : $_POST["settle_sale_profit"];
	$settle_sale_amount                 = (!isset($_POST["settle_sale_amount"]) || !$_POST["settle_sale_amount"]) ? 0 : $_POST["settle_sale_amount"];
	$settle_sale_cost                   = (!isset($_POST["settle_sale_cost"]) || !$_POST["settle_sale_cost"]) ? 0 : $_POST["settle_sale_cost"];
	$settle_memo                        = $_POST["settle_memo"];
	$settle_purchase_unit_supply        = (!isset($_POST["settle_purchase_unit_supply"]) || !$_POST["settle_purchase_unit_supply"]) ? 0 : $_POST["settle_purchase_unit_supply"];
	$settle_purchase_unit_supply_ex_vat = (!isset($_POST["settle_purchase_unit_supply_ex_vat"]) || !$_POST["settle_purchase_unit_supply_ex_vat"]) ? 0 : $_POST["settle_purchase_unit_supply_ex_vat"];
	$settle_settle_amt                  = (!isset($_POST["settle_settle_amt"]) || !$_POST["settle_settle_amt"]) ? 0 : $_POST["settle_settle_amt"];
	$settle_ad_amt                      = (!isset($_POST["settle_ad_amt"]) || !$_POST["settle_ad_amt"]) ? 0 : $_POST["settle_ad_amt"];
	//$settle_ad_amt                      = getParameterAndSetDefault("post", "settle_ad_amt", 0);


	$settle_sale_supply                = str_replace(",", "", $settle_sale_supply);
	$settle_sale_commission_in_vat     = str_replace(",", "", $settle_sale_commission_in_vat);
	$settle_delivery_in_vat            = str_replace(",", "", $settle_delivery_in_vat);
	$settle_delivery_commission_in_vat = str_replace(",", "", $settle_delivery_commission_in_vat);
	$settle_purchase_supply            = str_replace(",", "", $settle_purchase_supply);
	$settle_purchase_delivery_in_vat   = str_replace(",", "", $settle_purchase_delivery_in_vat);

	if(!is_numeric($settle_sale_supply)) $settle_sale_supply = 0;
	if(!is_numeric($settle_sale_commission_in_vat)) $settle_sale_commission_in_vat = 0;
	if(!is_numeric($settle_delivery_in_vat)) $settle_delivery_in_vat = 0;
	if(!is_numeric($settle_delivery_commission_in_vat)) $settle_delivery_commission_in_vat = 0;
	if(!is_numeric($settle_purchase_supply)) $settle_purchase_supply = 0;
	if(!is_numeric($settle_purchase_delivery_in_vat)) $settle_purchase_delivery_in_vat = 0;

	$settle_sale_sum                   = 0;     //매출합계
	$settle_purchase_sum               = 0;     //매입합계
    $settle_sale_profit                = 0;     //매출이익

	//매출합계 (판매가 - 판매수수료 + 매출배송비 - 매출배송비 수수료)
	$settle_sale_sum = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
	//매입합계 (매입가 + 매입배송비)
	$settle_purchase_sum = $settle_purchase_supply + $settle_purchase_delivery_in_vat;
    //매출이익 (판매가 공급가액 - 판매수수료 공급가액 + 매출배송비 공급가액 - 매출배송비 수수료 공급가액) - (매입가 공급가액 + 매입배송비 공급가액)
    $settle_sale_profit = ($settle_sale_supply_ex_vat - $settle_sale_commission_ex_vat + $settle_delivery_ex_vat - $settle_delivery_commission_ex_vat) - ($settle_purchase_supply_ex_vat + $settle_purchase_delivery_ex_vat);

	$args                                       = array();
	//$args["order_amt"]                          = $order_amt;
	$args["order_unit_price"]                   = $order_unit_price;
	$args["settle_sale_supply"]                 = $settle_sale_supply;
	$args["settle_sale_supply_ex_vat"]          = $settle_sale_supply_ex_vat;
	$args["settle_sale_commission_ex_vat"]      = $settle_sale_commission_ex_vat;
	$args["settle_sale_commission_in_vat"]      = $settle_sale_commission_in_vat;
	$args["settle_delivery_in_vat"]             = $settle_delivery_in_vat;
	$args["settle_delivery_ex_vat"]             = $settle_delivery_ex_vat;
	$args["settle_delivery_commission_ex_vat"]  = $settle_delivery_commission_ex_vat;
	$args["settle_delivery_commission_in_vat"]  = $settle_delivery_commission_in_vat;
	$args["settle_purchase_supply"]             = $settle_purchase_supply;
	$args["settle_purchase_supply_ex_vat"]      = $settle_purchase_supply_ex_vat;
	$args["settle_purchase_delivery_in_vat"]    = $settle_purchase_delivery_in_vat;
	$args["settle_purchase_delivery_ex_vat"]    = $settle_purchase_delivery_ex_vat;
	$args["settle_sale_profit"]                 = $settle_sale_profit;
	$args["settle_memo"]                        = $settle_memo;
	$args["settle_purchase_unit_supply"]        = $settle_purchase_unit_supply;
	$args["settle_purchase_unit_supply_ex_vat"] = $settle_purchase_unit_supply_ex_vat;
	$args["settle_settle_amt"]                  = $settle_settle_amt;
	$args["settle_ad_amt"]                      = $settle_ad_amt;
	$args["settle_sale_sum"]                    = $settle_sale_sum;
	$args["settle_purchase_sum"]                = $settle_purchase_sum;
	//$args["product_option_purchase_price"]     = $product_option_purchase_price;
	//$args["settle_sale_amount"]                = $settle_sale_amount;
	//$args["settle_sale_cost"]                  = $settle_sale_cost;

	$response["data"] = $args;

	$rst = $C_SETTLE->updateTransactionRow($settle_idx, $args);


	$response["result"] = true;
}elseif($mode == "transaction_sale_adjust") {

	//$response["data"] = $_POST;
	//매출보정
	$settle_date                        = $_POST["settle_date"];
	$settle_type                        = $_POST["settle_type"];
	$seller_idx                         = $_POST["seller_idx"];
	$product_idx                        = $_POST["product_idx"];
	$product_option_idx                 = $_POST["product_option_idx"];
	$supplier_idx                       = $_POST["supplier_idx"];

	$product_option_cnt                 = (!isset($_POST["product_option_cnt"]) || !$_POST["product_option_cnt"]) ? 0 : $_POST["product_option_cnt"];
	$purchase_amt                       = (!isset($_POST["purchase_amt"]) || !$_POST["purchase_amt"]) ? 0 : $_POST["purchase_amt"];
	$order_amt                          = (!isset($_POST["order_amt"]) || !$_POST["order_amt"]) ? 0 : $_POST["order_amt"];
	$order_unit_price                   = (!isset($_POST["order_unit_price"]) || !$_POST["order_unit_price"]) ? 0 : $_POST["order_unit_price"];
	$product_option_purchase_price      = (!isset($_POST["product_option_purchase_price"]) || !$_POST["product_option_purchase_price"]) ? 0 : $_POST["product_option_purchase_price"];
	$product_option_cnt                 = (!isset($_POST["product_option_cnt"]) || !$_POST["product_option_cnt"]) ? 0 : $_POST["product_option_cnt"];
	$settle_sale_supply                 = (!isset($_POST["settle_sale_supply"]) || !$_POST["settle_sale_supply"]) ? 0 : $_POST["settle_sale_supply"];
	$settle_sale_supply_ex_vat          = (!isset($_POST["settle_sale_supply_ex_vat"]) || !$_POST["settle_sale_supply_ex_vat"]) ? 0 : $_POST["settle_sale_supply_ex_vat"];
	$settle_sale_commission_ex_vat      = (!isset($_POST["settle_sale_commission_ex_vat"]) || !$_POST["settle_sale_commission_ex_vat"]) ? 0 : $_POST["settle_sale_commission_ex_vat"];
	$settle_sale_commission_in_vat      = (!isset($_POST["settle_sale_commission_in_vat"]) || !$_POST["settle_sale_commission_in_vat"]) ? 0 : $_POST["settle_sale_commission_in_vat"];
	$settle_delivery_in_vat             = (!isset($_POST["settle_delivery_in_vat"]) || !$_POST["settle_delivery_in_vat"]) ? 0 : $_POST["settle_delivery_in_vat"];
	$settle_delivery_ex_vat             = (!isset($_POST["settle_delivery_ex_vat"]) || !$_POST["settle_delivery_ex_vat"]) ? 0 : $_POST["settle_delivery_ex_vat"];
	$settle_delivery_commission_ex_vat  = (!isset($_POST["settle_delivery_commission_ex_vat"]) || !$_POST["settle_delivery_commission_ex_vat"]) ? 0 : $_POST["settle_delivery_commission_ex_vat"];
	$settle_delivery_commission_in_vat  = (!isset($_POST["settle_delivery_commission_in_vat"]) || !$_POST["settle_delivery_commission_in_vat"]) ? 0 : $_POST["settle_delivery_commission_in_vat"];
	$settle_purchase_supply             = (!isset($_POST["settle_purchase_supply"]) || !$_POST["settle_purchase_supply"]) ? 0 : $_POST["settle_purchase_supply"];
	$settle_purchase_supply_ex_vat      = (!isset($_POST["settle_purchase_supply_ex_vat"]) || !$_POST["settle_purchase_supply_ex_vat"]) ? 0 : $_POST["settle_purchase_supply_ex_vat"];
	$settle_purchase_delivery_in_vat    = (!isset($_POST["settle_purchase_delivery_in_vat"]) || !$_POST["settle_purchase_delivery_in_vat"]) ? 0 : $_POST["settle_purchase_delivery_in_vat"];
	$settle_purchase_delivery_ex_vat    = (!isset($_POST["settle_purchase_delivery_ex_vat"]) || !$_POST["settle_purchase_delivery_ex_vat"]) ? 0 : $_POST["settle_purchase_delivery_ex_vat"];
	$settle_sale_profit                 = (!isset($_POST["settle_sale_profit"]) || !$_POST["settle_sale_profit"]) ? 0 : $_POST["settle_sale_profit"];
	$settle_sale_amount                 = (!isset($_POST["settle_sale_amount"]) || !$_POST["settle_sale_amount"]) ? 0 : $_POST["settle_sale_amount"];
	$settle_sale_cost                   = (!isset($_POST["settle_sale_cost"]) || !$_POST["settle_sale_cost"]) ? 0 : $_POST["settle_sale_cost"];
	$settle_memo                        = $_POST["settle_memo"];
	$settle_purchase_unit_supply        = (!isset($_POST["settle_purchase_unit_supply"]) || !$_POST["settle_purchase_unit_supply"]) ? 0 : $_POST["settle_purchase_unit_supply"];
	$settle_purchase_unit_supply_ex_vat = (!isset($_POST["settle_purchase_unit_supply_ex_vat"]) || !$_POST["settle_purchase_unit_supply_ex_vat"]) ? 0 : $_POST["settle_purchase_unit_supply_ex_vat"];
	$settle_settle_amt                  = (!isset($_POST["settle_settle_amt"]) || !$_POST["settle_settle_amt"]) ? 0 : $_POST["settle_settle_amt"];
	$settle_ad_amt                      = (!isset($_POST["settle_ad_amt"]) || !$_POST["settle_ad_amt"]) ? 0 : $_POST["settle_ad_amt"];

	$settle_sale_supply                = str_replace(",", "", $settle_sale_supply);
	$settle_sale_commission_in_vat     = str_replace(",", "", $settle_sale_commission_in_vat);
	$settle_delivery_in_vat            = str_replace(",", "", $settle_delivery_in_vat);
	$settle_delivery_commission_in_vat = str_replace(",", "", $settle_delivery_commission_in_vat);
	$settle_purchase_supply            = str_replace(",", "", $settle_purchase_supply);
	$settle_purchase_delivery_in_vat   = str_replace(",", "", $settle_purchase_delivery_in_vat);

	if(!is_numeric($settle_sale_supply)) $settle_sale_supply = 0;
	if(!is_numeric($settle_sale_commission_in_vat)) $settle_sale_commission_in_vat = 0;
	if(!is_numeric($settle_delivery_in_vat)) $settle_delivery_in_vat = 0;
	if(!is_numeric($settle_delivery_commission_in_vat)) $settle_delivery_commission_in_vat = 0;
	if(!is_numeric($settle_purchase_supply)) $settle_purchase_supply = 0;
	if(!is_numeric($settle_purchase_delivery_in_vat)) $settle_purchase_delivery_in_vat = 0;

	$settle_sale_sum                   = 0;     //매출합계
	$settle_purchase_sum               = 0;     //매입합계

	//매출합계 (판매가 - 판매수수료 + 매출배송비 - 매출배송비 수수료)
	$settle_sale_sum = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
	//매입합계 (매입가 + 매입배송비)
	$settle_purchase_sum = $settle_purchase_supply + $settle_purchase_delivery_in_vat;

	$args                                       = array();
	$args["settle_date"]                        = $settle_date;
	$args["settle_type"]                        = $settle_type;
	$args["seller_idx"]                         = $seller_idx;
	$args["product_idx"]                        = $product_idx;
	$args["product_option_idx"]                 = $product_option_idx;
	$args["supplier_idx"]                       = $supplier_idx;
	$args["product_option_cnt"]                 = $product_option_cnt;
	$args["purchase_amt"]                       = $purchase_amt;
	$args["order_amt"]                          = $order_amt;
	$args["order_unit_price"]                   = $order_unit_price;
	$args["product_option_purchase_price"]      = $product_option_purchase_price;
	$args["settle_sale_supply"]                 = $settle_sale_supply;
	$args["settle_sale_supply_ex_vat"]          = $settle_sale_supply_ex_vat;
	$args["settle_sale_commission_ex_vat"]      = $settle_sale_commission_ex_vat;
	$args["settle_sale_commission_in_vat"]      = $settle_sale_commission_in_vat;
	$args["settle_delivery_in_vat"]             = $settle_delivery_in_vat;
	$args["settle_delivery_ex_vat"]             = $settle_delivery_ex_vat;
	$args["settle_delivery_commission_ex_vat"]  = $settle_delivery_commission_ex_vat;
	$args["settle_delivery_commission_in_vat"]  = $settle_delivery_commission_in_vat;
	$args["settle_purchase_supply"]             = $settle_purchase_supply;
	$args["settle_purchase_supply_ex_vat"]      = $settle_purchase_supply_ex_vat;
	$args["settle_purchase_delivery_in_vat"]    = $settle_purchase_delivery_in_vat;
	$args["settle_purchase_delivery_ex_vat"]    = $settle_purchase_delivery_ex_vat;
	$args["settle_sale_profit"]                 = $settle_sale_profit;
	$args["settle_sale_amount"]                 = $settle_sale_amount;
	$args["settle_sale_cost"]                   = $settle_sale_cost;
	$args["settle_memo"]                        = $settle_memo;
	$args["settle_purchase_unit_supply"]        = $settle_purchase_unit_supply;
	$args["settle_purchase_unit_supply_ex_vat"] = $settle_purchase_unit_supply_ex_vat;
	$args["settle_settle_amt"]                  = $settle_settle_amt;
	$args["settle_ad_amt"]                      = $settle_ad_amt;
	$args["settle_sale_sum"]                    = $settle_sale_sum;
	$args["settle_purchase_sum"]                = $settle_purchase_sum;

	$rst = $C_SETTLE->insertTransaction($args);

	$response["result"] = true;
}elseif($mode == "transaction_closing"){

	//마감처리
    $rst = $C_SETTLE->transactionClose();
	$response["result"] = true;
}

echo json_encode($response);
?>