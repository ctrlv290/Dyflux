<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매칭 정보 관리 관련 Process
 */

//Page Info
$pageMenuIdx = 66;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["msg"] = "";

$C_Product = new Product();

$mode                            = $_POST["mode"];
$matching_info_idx               = $_POST["matching_info_idx"];

if($mode == "product_matching_info_delete_one") {

	$rst = $C_Product -> deleteProductMatchingInfo($matching_info_idx);
	$response["result"] = true;

} else if($mode == "product_matching_info_delete_multi") {
    $matching_info_idx_arr = empty($matching_info_idx)?'NULL':"'".join("','", $matching_info_idx)."'";
    $rst = $C_Product -> multiDeleteProductMatchingInfo($matching_info_idx_arr);
    $response["result"] = true;
}

echo json_encode($response, true);
?>