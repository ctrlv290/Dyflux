<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: HOME - 배송지연현황
 */

//Page Info
$pageMenuIdx = 0;

//Init
include_once "../_init_.php";


//이번달 첫째날
$date = strtotime(date("Y-m-01"));

//3개월전 첫째날
$date_3month_ago =date("Y-m-d", strtotime("-3 month", $date));
$date_3month_ago_md =date("m/d", strtotime("-3 month", $date));

$C_Home = new Home();
$_delay = $C_Home->getShippingDelay();
?>
<table class="grid">
	<colgroup>
		<col width="90" />
	</colgroup>
	<thead>
	<tr>
		<th>장기지연<br><?=$date_3month_ago_md?><br>이전/이후</th>
		<th>5일차</th>
		<th>4일차</th>
		<th>3일차</th>
		<th>2일차</th>
		<th>1일차</th>
		<th>금일</th>
		<th>합계</th>
	</tr>
	</thead>
	<tbody>
	<?php if(isDYLogin()){?>
	<tr>
		<td><?=$_delay["3month_before"]?>/<a href="/order/order_search_list.php?period_type=order_accept_regdate&date_start=<?=$_delay["3month_after_start"]?>&date_end=<?=$_delay["3month_after_end"]?>&order_progress_step=ORDER_ACCEPT,ORDER_INVOICE&order_cs_status=EXCEPT_PART_CANCEL" class="link"><?=$_delay["3month_after"]?></a></td>
		<td><a href="/order/order_search_list.php?period_type=order_accept_regdate&date_start=<?=$_delay["5day_start"]?>&date_end=<?=$_delay["5day_end"]?>&order_progress_step=ORDER_ACCEPT,ORDER_INVOICE&order_cs_status=EXCEPT_PART_CANCEL" class="link"><?=$_delay["5day"]?></a></td>
		<td><a href="/order/order_search_list.php?period_type=order_accept_regdate&date_start=<?=$_delay["4day_start"]?>&date_end=<?=$_delay["4day_end"]?>&order_progress_step=ORDER_ACCEPT,ORDER_INVOICE&order_cs_status=EXCEPT_PART_CANCEL" class="link"><?=$_delay["4day"]?></a></td>
		<td><a href="/order/order_search_list.php?period_type=order_accept_regdate&date_start=<?=$_delay["3day_start"]?>&date_end=<?=$_delay["3day_end"]?>&order_progress_step=ORDER_ACCEPT,ORDER_INVOICE&order_cs_status=EXCEPT_PART_CANCEL" class="link"><?=$_delay["3day"]?></a></td>
		<td><a href="/order/order_search_list.php?period_type=order_accept_regdate&date_start=<?=$_delay["2day_start"]?>&date_end=<?=$_delay["2day_end"]?>&order_progress_step=ORDER_ACCEPT,ORDER_INVOICE&order_cs_status=EXCEPT_PART_CANCEL" class="link"><?=$_delay["2day"]?></a></td>
		<td><a href="/order/order_search_list.php?period_type=order_accept_regdate&date_start=<?=$_delay["1day_start"]?>&date_end=<?=$_delay["1day_end"]?>&order_progress_step=ORDER_ACCEPT,ORDER_INVOICE&order_cs_status=EXCEPT_PART_CANCEL" class="link"><?=$_delay["1day"]?></a></td>
		<td><a href="/order/order_search_list.php?period_type=order_accept_regdate&date_start=<?=$_delay["0day_start"]?>&date_end=<?=$_delay["0day_end"]?>&order_progress_step=ORDER_ACCEPT,ORDER_INVOICE&order_cs_status=EXCEPT_PART_CANCEL" class="link"><?=$_delay["0day"]?></a></td>
		<td><a href="/order/order_search_list.php?period_type=order_accept_regdate&date_start=<?=$_delay["0day_start"]?>&date_end=<?=$_delay["0day_end"]?>&order_progress_step=ORDER_ACCEPT,ORDER_INVOICE&order_cs_status=EXCEPT_PART_CANCEL" class="link"><?=$_delay["sum"]?></a></td>
	</tr>
	<?php } else { ?>
		<tr>
			<td><?=$_delay["3month_before"]?>/<?=$_delay["3month_after"]?></td>
			<td><?=$_delay["5day"]?></td>
			<td><?=$_delay["4day"]?></td>
			<td><?=$_delay["3day"]?></td>
			<td><?=$_delay["2day"]?></td>
			<td><?=$_delay["1day"]?></td>
			<td><?=$_delay["0day"]?></td>
			<td><?=$_delay["sum"]?></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
