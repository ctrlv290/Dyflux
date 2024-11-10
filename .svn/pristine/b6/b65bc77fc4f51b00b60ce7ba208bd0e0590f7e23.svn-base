<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 관리 관련 Process
 */

//Page Info
$pageMenuIdx = 73;
//Init
include_once "../_init_.php";

$seller_idx = $_POST["seller_idx"];
$print_header = $_POST["print_header"];
$margin_top = $_POST["margin_top"];
$column_setting_array = array();
foreach(excelColumnRange('A', 'AZ') as $char) {
	$column_setting_array[$char] = $_POST["header_".$char] . '|' . $_POST["value_".$char];
}

$C_Seller = new Seller();
$rst = $C_Seller->saveSellerInvoiceFormat($seller_idx, $print_header, $margin_top, $column_setting_array);


put_msg_and_close("저장되었습니다.");


?>