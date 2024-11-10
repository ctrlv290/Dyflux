<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 입고내역 리스트 JSON
 */
//Page Info
$pageMenuIdx = 302;
//Init
include_once "../_init_.php";

$grid_response = array();
$grid_response["page"] = 0;
$grid_response["records"] = 0;
$grid_response["total"] = 0;
$grid_response["rows"] = array();

$C_Stock         = new Stock();

$product_option_idx       = $_GET["product_option_idx"];
$confirm_date             = $_GET["confirm_date"];
$stock_unit_price         = $_GET["stock_unit_price"];
$stock_kind            = $_GET["stock_kind"];
$stock_status            = $_GET["stock_status"];

$qry_where = "";
if($stock_kind == "IN"){
    $qry_where .= "
				And stock_type = 1
				And stock_status = N'$stock_status'
			";
}elseif($stock_kind == "OUT") {
    $qry_where .= "
				And stock_type = -1
				And stock_status = N'$stock_status'
			";
}

$_list = $C_Stock->getStockDailyRelationList($product_option_idx, $confirm_date, $stock_unit_price, $qry_where);

if($_list){
	$grid_response["page"] = 1;
	$grid_response["records"] = count($_list);
	$grid_response["total"] = count($_list);
	$grid_response["rows"] = $_list;
}


echo json_encode($grid_response, true);
?>