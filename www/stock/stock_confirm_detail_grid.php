<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 입고내역 리스트 JSON
 */
//Page Info
$pageMenuIdx = 196;
//Init
include_once "../_init_.php";

$grid_response = array();
$grid_response["page"] = 0;
$grid_response["records"] = 0;
$grid_response["total"] = 0;
$grid_response["rows"] = array();

$C_Stock         = new Stock();
$stock_idx       = $_GET["stock_idx"];
$stock_ref_idx   = $_GET["stock_ref_idx"];
$order_idx       = $_GET["order_idx"];
$stock_order_idx = $_GET["stock_order_idx"];
$stock_is_proc   = $_GET["stock_is_proc"];

$order_by = "stock_request_date ASC";
if($_GET["sidx"] && $_GET["sord"])
{
	$order_by = $_GET["sidx"] . " " . $_GET["sord"];
}

$_list = $C_Stock->getStockOrderRelationList($stock_ref_idx, $order_by, $stock_is_proc);

if($_list){
	$grid_response["page"] = 1;
	$grid_response["records"] = count($_list);
	$grid_response["total"] = count($_list);
	$grid_response["rows"] = $_list;
}


echo json_encode($grid_response, true);
?>