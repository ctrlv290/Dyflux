<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: HOME - 당일매출현황
 */

//Page Info
$pageMenuIdx = 0;

//Init
include_once "../_init_.php";

//매출(건수, 금액)
//취소(건수, 금액)
//송장(건수)
//배송(건수)

//금일 & 전일

$C_Home = new Home();

//금일
$date = date('Y-m-d');
$_today = $C_Home->getTodaySalesSummary($date);

//전일
$date2 = date('Y-m-d', strtotime("-1 days"));
$_yesterday = $C_Home->getTodaySalesSummary($date2);
?>
<table class="grid">
	<thead>
	<tr>
		<th rowspan="2"></th>
		<th colspan="2">매출</th>
		<th colspan="2">취소</th>
		<th>송장</th>
		<th>배송</th>
	</tr>
	<tr>
		<th>건수</th>
		<th>금액</th>
		<th>건수</th>
		<th>금액</th>
		<th>건수</th>
		<th>건수</th>
	</tr>
	</thead>
	<tbody>
	<?php if(isDYLogin()){?>
	<tr>
		<td>금일</td>
		<td class="text_right"><?=number_format($_today["sales_cnt"])?></td>
		<td class="text_right"><?=number_format($_today["sales_amt"])?></td>
		<td class="text_right"><?=number_format($_today["cancel_cnt"])?></td>
		<td class="text_right"><?=number_format($_today["cancel_amt"])?></td>
		<td class="text_right"><a href="/order/order_search_list.php?period_type=invoice_date&date_start=<?=$date?>&date_end=<?=$date?>&order_progress_step=" class="link"><?=number_format($_today["invoice_cnt"])?></a></td>
		<td class="text_right"><a href="/order/order_search_list.php?period_type=shipping_date&date_start=<?=$date?>&date_end=<?=$date?>&order_progress_step=" class="link"><?=number_format($_today["shipped_cnt"])?></a></td>
	</tr>
	<tr>
		<td>전일</td>
		<td class="text_right"><?=number_format($_yesterday["sales_cnt"])?></td>
		<td class="text_right"><?=number_format($_yesterday["sales_amt"])?></td>
		<td class="text_right"><?=number_format($_yesterday["cancel_cnt"])?></td>
		<td class="text_right"><?=number_format($_yesterday["cancel_amt"])?></td>
		<td class="text_right"><a href="/order/order_search_list.php?period_type=invoice_date&date_start=<?=$date2?>&date_end=<?=$date2?>&order_progress_step=" class="link"><?=number_format($_yesterday["invoice_cnt"])?></a></td>
		<td class="text_right"><a href="/order/order_search_list.php?period_type=shipping_date&date_start=<?=$date2?>&date_end=<?=$date2?>&order_progress_step=" class="link"><?=number_format($_yesterday["shipped_cnt"])?></a></td>
	</tr>
	<?php } else { ?>
		<tr>
			<td>금일</td>
			<td class="text_right"><?=number_format($_today["sales_cnt"])?></td>
			<td class="text_right"><?=number_format($_today["sales_amt"])?></td>
			<td class="text_right"><?=number_format($_today["cancel_cnt"])?></td>
			<td class="text_right"><?=number_format($_today["cancel_amt"])?></td>
			<td class="text_right"><?=number_format($_today["invoice_cnt"])?></td>
			<td class="text_right"><?=number_format($_today["shipped_cnt"])?></td>
		</tr>
		<tr>
			<td>전일</td>
			<td class="text_right"><?=number_format($_yesterday["sales_cnt"])?></td>
			<td class="text_right"><?=number_format($_yesterday["sales_amt"])?></td>
			<td class="text_right"><?=number_format($_yesterday["cancel_cnt"])?></td>
			<td class="text_right"><?=number_format($_yesterday["cancel_amt"])?></td>
			<td class="text_right"><?=number_format($_yesterday["invoice_cnt"])?></td>
			<td class="text_right"><?=number_format($_yesterday["shipped_cnt"])?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
