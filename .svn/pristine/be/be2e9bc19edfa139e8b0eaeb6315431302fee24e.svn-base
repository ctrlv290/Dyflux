<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매칭 내역 확인 그리드 리스트
 */
//Page Info
$pageMenuIdx = 73;
//Init
include_once "../_init_.php";

$C_Order = new Order();


$seller_idx = $_GET["seller_idx"];

$_list = $C_Order -> getOrderDataForMatchingConfirm($seller_idx);


$grid_response             = array();
$grid_response["page"]     = 1;
$grid_response["records"]  = count($_list);
$grid_response["total"]    = count($_list);
$grid_response["rows"]     = $_list;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>