<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 - CS 내역 리스트 JSON
 */
//Page Info
$pageMenuIdx = 205;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$C_CS = new CS();

$order_pack_idx = $_POST["order_pack_idx"];
$cs_task        = $_POST["cs_task"];
$search_column  = $_POST["search_column"];
$search_keyword = $_POST["search_keyword"];
$_list = $C_CS -> getCSList($order_pack_idx, $cs_task, $search_column, $search_keyword);

$response = array();
$response["result"] = false;
$response["data"] = "";

if($_list){
	foreach($_list as $k => $v){
		foreach($v as $_k => $_v){
			$v[$_k] = str_replace(array("<", ">"), array("&lt;", "&gt;"), $_v);
		}
		$_list[$k] = $v;
	}
	$response["result"] = true;
	$response["data"] = $_list;
}

echo json_encode($response, true);
?>