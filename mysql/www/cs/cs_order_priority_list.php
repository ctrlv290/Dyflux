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

$order_idx          = $_POST["order_idx"];
$order_pack_idx     = $_POST["order_pack_idx"];
$product_option_idx = $_POST["product_option_idx"];

$_list = $C_CS -> getOrderPriorityList($order_idx, $product_option_idx);

echo json_encode($_list, true);
?>