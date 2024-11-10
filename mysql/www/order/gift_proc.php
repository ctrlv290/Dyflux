<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사은품 관련 Process
 */

//Page Info
$pageMenuIdx = 276;
//Init
include "../_init_.php";

$mode                      = $_POST["mode"];
$gift_idx                  = $_POST["gift_idx"];
$gift_name                 = $_POST["gift_name"];
$gift_date_start_1         = $_POST["gift_date_start_1"];
$gift_date_start_2         = $_POST["gift_date_start_2"];
$gift_date_end_1           = $_POST["gift_date_end_1"];
$gift_date_end_2           = $_POST["gift_date_end_2"];
$supplier_idx              = $_POST["supplier_idx"];
$product_option_idx_list   = $_POST["product_option_idx_list"];
$seller_idx                = $_POST["seller_idx"];
$market_product_no_list    = $_POST["market_product_no_list"];
$gift_match_pay            = ($_POST["gift_match_pay"] == "Y") ? "Y" : "N";
$gift_match_pay_text       = $_POST["gift_match_pay_text"];
$gift_match_product        = ($_POST["gift_match_product"] == "Y") ? "Y" : "N";
$gift_match_product_cnt_s  = $_POST["gift_match_product_cnt_s"];
$gift_match_product_cnt_e  = $_POST["gift_match_product_cnt_e"];
$gift_match_order_amount   = ($_POST["gift_match_order_amount"] == "Y") ? "Y" : "N";
$gift_match_order_amount_s = $_POST["gift_match_order_amount_s"];
$gift_match_order_amount_e = $_POST["gift_match_order_amount_e"];
$gift_delivery_free        = ($_POST["gift_delivery_free"] == "Y") ? "Y" : "N";
$gift_memo                 = $_POST["gift_memo"];
$gift_product_full_name    = $_POST["gift_product_full_name"];
$gift_product_idx          = $_POST["gift_product_idx"];
$gift_product_option_idx   = $_POST["gift_product_option_idx"];
$gift_cnt                  = $_POST["gift_cnt"];
$gift_is_only              = $_POST["gift_is_only"];
$gift_cnt_type             = $_POST["gift_cnt_type"];
$gift_cnt_type_cnt         = $_POST["gift_cnt_type_cnt"];
$gift_status               = $_POST["gift_status"];

$gift_date_start = $gift_date_start_1 . " " . $gift_date_start_2;
$gift_date_end = $gift_date_end_1 . " " . $gift_date_end_2;

if($supplier_idx) $product_option_idx_list = "";

$C_Order = new Order();
if($mode == "add"){

	$args                              = array();
	$args["gift_name"]                 = $gift_name;
	$args["gift_date_start"]           = $gift_date_start;
	$args["gift_date_end"]             = $gift_date_end;
	$args["supplier_idx"]              = $supplier_idx;
	$args["product_option_idx_list"]   = $product_option_idx_list;
	$args["seller_idx"]                = $seller_idx;
	$args["market_product_no_list"]    = $market_product_no_list;
	$args["gift_match_pay"]            = $gift_match_pay;
	$args["gift_match_pay_text"]       = $gift_match_pay_text;
	$args["gift_match_product"]        = $gift_match_product;
	$args["gift_match_product_cnt_s"]  = $gift_match_product_cnt_s;
	$args["gift_match_product_cnt_e"]  = $gift_match_product_cnt_e;
	$args["gift_match_order_amount"]   = $gift_match_order_amount;
	$args["gift_match_order_amount_s"] = $gift_match_order_amount_s;
	$args["gift_match_order_amount_e"] = $gift_match_order_amount_e;
	$args["gift_delivery_free"]        = $gift_delivery_free;
	$args["gift_memo"]                 = $gift_memo;
	$args["gift_product_full_name"]    = $gift_product_full_name;
	$args["gift_product_idx"]          = $gift_product_idx;
	$args["gift_product_option_idx"]   = $gift_product_option_idx;
	$args["gift_cnt"]                  = $gift_cnt;
	$args["gift_is_only"]              = $gift_is_only;
	$args["gift_cnt_type"]             = $gift_cnt_type;
	$args["gift_cnt_type_cnt"]         = $gift_cnt_type_cnt;
	$args["gift_status"]               = $gift_status;

	//print_r2($args);

	$rst = $C_Order -> insertGift($args);

	$exec_script = "
			try{
				opener.OrderGift.GiftListGridReload();
			}catch(e){
			}
			alert('등록 되었습니다.');
		";

	exec_script_and_close($exec_script);
}elseif($mode == "update"){

	$args                              = array();
	$args["gift_idx"]                  = $gift_idx;
	$args["gift_name"]                 = $gift_name;
	$args["gift_date_start"]           = $gift_date_start;
	$args["gift_date_end"]             = $gift_date_end;
	$args["supplier_idx"]              = $supplier_idx;
	$args["product_option_idx_list"]   = $product_option_idx_list;
	$args["seller_idx"]                = $seller_idx;
	$args["market_product_no_list"]    = $market_product_no_list;
	$args["gift_match_pay"]            = $gift_match_pay;
	$args["gift_match_pay_text"]       = $gift_match_pay_text;
	$args["gift_match_product"]        = $gift_match_product;
	$args["gift_match_product_cnt_s"]  = $gift_match_product_cnt_s;
	$args["gift_match_product_cnt_e"]  = $gift_match_product_cnt_e;
	$args["gift_match_order_amount"]   = $gift_match_order_amount;
	$args["gift_match_order_amount_s"] = $gift_match_order_amount_s;
	$args["gift_match_order_amount_e"] = $gift_match_order_amount_e;
	$args["gift_delivery_free"]        = $gift_delivery_free;
	$args["gift_memo"]                 = $gift_memo;
	$args["gift_product_full_name"]    = $gift_product_full_name;
	$args["gift_product_idx"]          = $gift_product_idx;
	$args["gift_product_option_idx"]   = $gift_product_option_idx;
	$args["gift_cnt"]                  = $gift_cnt;
	$args["gift_is_only"]              = $gift_is_only;
	$args["gift_cnt_type"]             = $gift_cnt_type;
	$args["gift_cnt_type_cnt"]         = $gift_cnt_type_cnt;
	$args["gift_status"]               = $gift_status;

	$rst = $C_Order -> updateGift($args);

	$exec_script = "
			try{
				opener.OrderGift.GiftListGridReload();
			}catch(e){
			}
			alert('수정 되었습니다.');
		";

	exec_script_and_close($exec_script);
}

?>