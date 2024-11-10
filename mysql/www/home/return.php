<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: HOME - 미처리현황
 */

//Page Info
$pageMenuIdx = 0;

//Init
include_once "../_init_.php";

$C_Home = new Home();
$_return  = $C_Home->getReturn();
?>
<table class="grid">
	<colgroup>
		<col width="50%" />
		<col width="50%" />
	</colgroup>
	<thead>
	<tr>
		<th>배송후취소</th>
		<th>반품장기지연</th>
	</tr>
	</thead>
	<tbody>
	<?php if(isDYLogin()){?>
	<tr>
		<td><a href="javascript:Common.newWinPopup2('/cs/cs.php?date_start=<?=$_return["cancel_date_ymd"]?>&date_end=<?=date("Y-m-d")?>&order_progress_step=SHIPPED&order_cs_status=CANCEL', 'menu_205', 0, 0, 0, 1);" class="link"><?=number_format($_return["cancel_cnt"])?></a><br>(<?=$_return["cancel_date"]?> ~)</td>
		<td><a href="/cs/cs_return_list.php?date_start=<?=$_return["cancel_date_ymd"]?>&date_end=<?=$_return["delay_date_end"]?>&return_is_confirm=N" class="link"><?=number_format($_return["delay_cnt"])?></a><br>(<?=$_return["delay_date_forsearch_start"]?> ~ <?=$_return["delay_date_forsearch_end"]?>)</td>
	</tr>
	<?php } else { ?>
		<tr>
			<td><?=number_format($_return["cancel_cnt"])?><br>(<?=$_return["cancel_date"]?> ~)</td>
			<td><?=number_format($_return["delay_cnt"])?><br>(<?=$_return["delay_date_forsearch"]?> ~)</td>
		</tr>
	<?php } ?>
	</tbody>
</table>