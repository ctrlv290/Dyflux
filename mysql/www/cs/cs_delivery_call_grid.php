<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 - 회수요청 - 회수요청 내역 JSON
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

$_list = $C_CS -> getOrderReturnList($order_idx);


$grid_response             = array();
$grid_response["page"]     = $page;
$grid_response["records"]  = count($_list);
$grid_response["total"]    = count($_list);
$grid_response["rows"]     = $_list;
//foreach($WholeGetListResult['listRst'] as $row)
//{
//	$grid_response["rows"][] = array("id" => $row["idx"], "cell" => $row);
//}
echo json_encode($grid_response, true);
?>