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

if($mode == "row_edit" || "transaction_sale_adjust") {

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

	$settle_purchase_unit_supply        = (!isset($_POST["settle_purchase_unit_supply"]) || !$_POST["settle_purchase_unit_supply"]) ? 0 : $_POST["settle_purchase_unit_supply"];
	$settle_purchase_unit_supply_ex_vat = (!isset($_POST["settle_purchase_unit_supply_ex_vat"]) || !$_POST["settle_purchase_unit_supply_ex_vat"]) ? 0 : $_POST["settle_purchase_unit_supply_ex_vat"];
	$settle_purchase_supply             = (!isset($_POST["settle_purchase_supply"]) || !$_POST["settle_purchase_supply"]) ? 0 : $_POST["settle_purchase_supply"];
	$settle_purchase_supply_ex_vat      = (!isset($_POST["settle_purchase_supply_ex_vat"]) || !$_POST["settle_purchase_supply_ex_vat"]) ? 0 : $_POST["settle_purchase_supply_ex_vat"];
	$settle_purchase_delivery_in_vat    = (!isset($_POST["settle_purchase_delivery_in_vat"]) || !$_POST["settle_purchase_delivery_in_vat"]) ? 0 : $_POST["settle_purchase_delivery_in_vat"];
	$settle_purchase_delivery_ex_vat    = (!isset($_POST["settle_purchase_delivery_ex_vat"]) || !$_POST["settle_purchase_delivery_ex_vat"]) ? 0 : $_POST["settle_purchase_delivery_ex_vat"];
	$settle_sale_amount                 = (!isset($_POST["settle_sale_amount"]) || !$_POST["settle_sale_amount"]) ? 0 : $_POST["settle_sale_amount"];
	$settle_sale_cost                   = (!isset($_POST["settle_sale_cost"]) || !$_POST["settle_sale_cost"]) ? 0 : $_POST["settle_sale_cost"];
	$settle_memo                        = $_POST["settle_memo"];
	$settle_settle_amt                  = (!isset($_POST["settle_settle_amt"]) || !$_POST["settle_settle_amt"]) ? 0 : $_POST["settle_settle_amt"];
	$settle_ad_amt                      = (!isset($_POST["settle_ad_amt"]) || !$_POST["settle_ad_amt"]) ? 0 : $_POST["settle_ad_amt"];

	//매출합계 (판매가 - 판매수수료 + 매출배송비 - 매출배송비 수수료)
	$settle_sale_sum = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
	//매입합계 (매입가 + 매입배송비)
	$settle_purchase_sum = $settle_purchase_supply + $settle_purchase_delivery_in_vat;
    //매출이익 (매출합계 - 매입합계)
    $settle_sale_profit = $settle_sale_sum - $settle_purchase_sum;
    $settle_sale_profit_ex_vat = ($settle_sale_supply_ex_vat - $settle_sale_commission_ex_vat + $settle_delivery_ex_vat - $settle_delivery_commission_ex_vat) - ($settle_purchase_supply_ex_vat + $settle_purchase_delivery_ex_vat);

	$args                                       = array();
	$args["order_unit_price"]                   = $order_unit_price;
	$args["product_option_cnt"]					= $product_option_cnt;
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
	$args["settle_sale_profit_ex_vat"]          = $settle_sale_profit_ex_vat;
	$args["settle_memo"]                        = $settle_memo;
	$args["settle_purchase_unit_supply"]        = $settle_purchase_unit_supply;
	$args["settle_purchase_unit_supply_ex_vat"] = $settle_purchase_unit_supply_ex_vat;
	$args["settle_settle_amt"]                  = $settle_settle_amt;
	$args["settle_ad_amt"]                      = $settle_ad_amt;
	$args["settle_sale_sum"]                    = $settle_sale_sum;
	$args["settle_purchase_sum"]                = $settle_purchase_sum;

	$valid = true;

	foreach($args as $key => $arg) {
		if($key == "settle_memo") continue;
		if(!is_numeric($arg)) $valid = false;
	}

	if($valid) {
		if($mode == "row_edit") {
			$args["settle_idx"] = $_POST["settle_idx"];

			$response["data"] = $args;
			$response["result"] = $C_SETTLE->insertFromArray($args, "DY_SETTLE", "settle_idx");
		} else {
			$args["settle_date"] = $_POST["settle_date"];
			$args["settle_type"] = $_POST["settle_type"];
			$args["seller_idx"] = $_POST["seller_idx"];
			$args["supplier_idx"] = $_POST["supplier_idx"];
			$args["product_idx"] = $_POST["product_idx"];
			$args["product_option_idx"] = $_POST["product_option_idx"];

			$args["order_amt"] = (!isset($_POST["order_amt"]) || !$_POST["order_amt"]) ? 0 : $_POST["order_amt"];

			$response["result"] = $C_SETTLE->insertTransaction($args);
		}
	} else {
		$response["result"] = false;
		$response["msg"] = "입력 값이 옳바르지 않습니다";
	}

}elseif($mode == "transaction_closing"){
	//마감처리
    $rst = $C_SETTLE->transactionClose();
	$response["result"] = true;
}

echo json_encode($response);
?>