<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 매출관리
 */

//Init
include_once "../../_init_.php";

$today = date('Y-m-d');

$C_Settle = new Settle();

$period_type = "day";
$date_start = $_POST["date_start"];
$date_end = $_POST["date_end"];
$search_column = "";
$seller_idx = $_POST["seller_idx"];
$search_keyword = "";

$_list = $C_Settle -> getSellerSaleStatisticsSum($period_type, $date_start, $date_end, 0, $search_column, $search_keyword);
$_list_seller_ary = $C_Settle -> getSellerSaleStatistics($period_type, $date_start, $date_end, null, $seller_idx, $search_column, $search_keyword);
$_list_seller = $_list_seller_ary[0];
?>
<div class="wrap_scroll mt20">
	<table class="table_style05">
	<colgroup>
		<col width="50">
		<col width="100">
		<col width="100">
		<col width="100">
	</colgroup>
	<thead>
	<tr>
		<th colspan="2">항목</th>
		<th>판매처명</th>
		<th>전체</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<th rowspan="7">수량</th>
		<th>주문 수량</th>
		<td class="text_right"><?=number_format($_list_seller["order_count"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["order_count"])?></span></td>
	</tr>
	<tr>
		<th>상품 수량</th>
		<td class="text_right"><?=number_format($_list_seller["sum_product_option_cnt"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["sum_product_option_cnt"])?></span></td>
	</tr>
	<tr>
		<th>취소주문 수량</th>
		<td class="text_right"><?=number_format($_list_seller["order_cancel_count"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["order_cancel_count"])?></span></td>
	</tr>
	<tr>
		<th>취소상품 수량</th>
		<td class="text_right"><?=number_format($_list_seller["sum_cancel_product_cnt"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["sum_cancel_product_cnt"])?></span></td>
	</tr>
	<tr>
		<th>교환상품 수량</th>
		<td class="text_right"><?=number_format($_list_seller["sum_cancel_change_cnt"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["sum_cancel_change_cnt"])?></span></td>
	</tr>
	<tr>
		<th>주문 - 취소주문</th>
		<td class="text_right"><?=number_format($_list_seller["order_count"] - $_list_seller["order_cancel_count"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["order_count"] - $_list["order_cancel_count"])?></span></td>
	</tr>
	<tr>
		<th>상품 - 취소수량</th>
		<td class="text_right"><?=number_format($_list_seller["sum_product_option_cnt"] - $_list_seller["sum_cancel_product_cnt"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["sum_product_option_cnt"] - $_list["sum_cancel_product_cnt"])?></span></td>
	</tr>
	<tr>
		<th rowspan="3">판매가<br>기준</th>
		<th>판매금액</th>
		<td class="text_right"><?=number_format($_list_seller["sum_settle_sale_supply"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["sum_settle_sale_supply"])?></span></td>
	</tr>
	<tr>
		<th>취소금액</th>
		<td class="text_right"><?=number_format($_list_seller["sum_settle_sale_supply_cancel"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["sum_settle_sale_supply_cancel"])?></span></td>
	</tr>
	<tr>
		<th>실매출금액</th>
		<td class="text_right"><?=number_format($_list_seller["sum_settle_sale_supply"] - $_list_seller["sum_settle_sale_supply_cancel"])?></td>
		<td class="text_right"><span class="gray"><?=number_format($_list["sum_settle_sale_supply"] - $_list["sum_settle_sale_supply_cancel"])?></span></td>
	</tr>
	</tbody>
</table>
</div>

