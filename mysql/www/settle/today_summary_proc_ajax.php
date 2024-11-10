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

if($mode == "get_seller") {

	$period_type             = $_POST["period_type"];
	$date_start              = $_POST["date_start"];
	$date_end                = $_POST["date_end"];
	$seller_idx              = $_POST["seller_idx"];
	$search_column           = $_POST["search_column"];
	$search_keyword          = $_POST["search_keyword"];
	$chk_except_cancel_order = $_POST["chk_except_cancel_order"];


	$_list = $C_SETTLE->getTodaySummarySeller($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $chk_except_cancel_order);
	$response["result"] = true;
	$response["data"] = $_list;
}elseif($mode == "get_category") {
	$period_type             = $_POST["period_type"];
	$date_start              = $_POST["date_start"];
	$date_end                = $_POST["date_end"];
	$seller_idx              = $_POST["seller_idx"];
	$search_column           = $_POST["search_column"];
	$search_keyword          = $_POST["search_keyword"];
	$chk_except_cancel_order = $_POST["chk_except_cancel_order"];


	$_list = $C_SETTLE->getTodaySummaryCategory($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $chk_except_cancel_order);
	$response["result"] = true;
	$response["data"] = $_list;
}elseif($mode == "get_order_count"){

	$period_type             = $_POST["period_type"];
	$date_start              = $_POST["date_start"];
	$date_end                = $_POST["date_end"];
	$seller_idx              = $_POST["seller_idx"];
	$search_column           = $_POST["search_column"];
	$search_keyword          = $_POST["search_keyword"];
	$chk_except_cancel_order = $_POST["chk_except_cancel_order"];


	$_list = $C_SETTLE->getTodaySummaryOrder($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $chk_except_cancel_order);
	$response["result"] = true;
	$response["data"] = $_list;
}elseif($mode == "get_order_invoice"){

	$period_type             = $_POST["period_type"];
	$date_start              = $_POST["date_start"];
	$date_end                = $_POST["date_end"];
	$seller_idx              = $_POST["seller_idx"];
	$search_column           = $_POST["search_column"];
	$search_keyword          = $_POST["search_keyword"];
	$chk_except_cancel_order = $_POST["chk_except_cancel_order"];


	$_list = $C_SETTLE->getTodaySummaryInvoice($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $chk_except_cancel_order);
	$response["result"] = true;
	$response["data"] = $_list;
}elseif($mode == "get_order_shipped"){

	$period_type             = $_POST["period_type"];
	$date_start              = $_POST["date_start"];
	$date_end                = $_POST["date_end"];
	$seller_idx              = $_POST["seller_idx"];
	$search_column           = $_POST["search_column"];
	$search_keyword          = $_POST["search_keyword"];
	$chk_except_cancel_order = $_POST["chk_except_cancel_order"];


	$_list = $C_SETTLE->getTodaySummaryShipped($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $chk_except_cancel_order);
	$response["result"] = true;
	$response["data"] = $_list;
}elseif($mode == "get_order_return"){

	$period_type             = $_POST["period_type"];
	$date_start              = $_POST["date_start"];
	$date_end                = $_POST["date_end"];
	$seller_idx              = $_POST["seller_idx"];
	$search_column           = $_POST["search_column"];
	$search_keyword          = $_POST["search_keyword"];
	$chk_except_cancel_order = $_POST["chk_except_cancel_order"];


	$_list = $C_SETTLE->getTodaySummaryReturn($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $chk_except_cancel_order);
	$response["result"] = true;
	$response["data"] = $_list;
}elseif($mode == "get_order_cs"){

	$period_type             = $_POST["period_type"];
	$date_start              = $_POST["date_start"];
	$date_end                = $_POST["date_end"];
	$seller_idx              = $_POST["seller_idx"];
	$search_column           = $_POST["search_column"];
	$search_keyword          = $_POST["search_keyword"];
	$chk_except_cancel_order = $_POST["chk_except_cancel_order"];


	$_list = $C_SETTLE->getTodaySummaryCS($period_type, $date_start, $date_end, $seller_idx, $search_column, $search_keyword, $chk_except_cancel_order);
	$response["result"] = true;
	$response["data"] = $_list;


}

echo json_encode($response);
?>