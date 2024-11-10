<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 관리 관련 Process
 */

//Page Info
$pageMenuIdx = 73;
//Init
$GL_JsonHeader = true;
include_once "../_init_.php";

$response = array();
$response["result"] = false;
$response["msg"] = "";

$C_Order = new Order();

$mode                           		= $_POST["mode"];
$supplier_idx                    		= $_POST["supplier_idx"];
$order_download_format_default_idx      = $_POST["order_download_format_default_idx"];
$order_download_format_header_name 		= $_POST["order_download_format_header_name"];
$sort = 1;
$not_del_sort = "";

if($mode == "save") {
	if($order_download_format_default_idx) {
		foreach ($order_download_format_default_idx as $key => $val) {
			$rst = $C_Order->saveOrderDownloadFormat($supplier_idx, $order_download_format_default_idx[$key], $order_download_format_header_name[$key], $sort);
			$not_del_sort .= $sort . ", ";
			$sort++;
		}
		$not_del_sort = trim($not_del_sort, ", ");
		$del_rst = $C_Order->delOrderDownloadFormat($supplier_idx, false, $not_del_sort);
	} else {
		$del_rst = $C_Order->delOrderDownloadFormat($supplier_idx, true, $not_del_sort);
	}
	$response["result"] = true;
}
echo json_encode($response, true);
?>

