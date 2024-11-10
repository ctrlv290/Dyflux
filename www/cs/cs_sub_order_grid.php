<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 주문 리스트 JSON
 */
//Page Info
$pageMenuIdx = 205;
//Init
include_once "../_init_.php";

$C_CS = new CS();

//******************************* 리스트 기본 설정 ******************************//
// get info set
$page = (!$page)? '1' : $page ;
$args['page'] 		    = (!$page)? '1' : $page ;
$args['searchVar'] 	    = $searchVar;
$args['searchWord'] 	= $searchWord;
$args['sortBy'] 		= $sortBy;
$args['sortType'] 		= $sortType;
$args['pagename']	    = $GL_page_nm;

$order_idx = $_GET["order_idx"];
$order_cs_status = $_GET["order_cs_status"];

$_list = $C_CS->getOrderDetailRelateOrderIdx($order_idx, $order_cs_status);

$grid_response             = array();
$grid_response["page"]     = $page;
$grid_response["records"]  = $WholeGetListResult["pageInfo"]["total"];
$grid_response["total"]    = $WholeGetListResult["pageInfo"]["totalpages"];
$grid_response["rows"]     = $_list;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>