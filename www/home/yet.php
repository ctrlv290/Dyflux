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
$_yet = $C_Home->getYet();
?>
<table class="grid">
	<colgroup>
		<col width="50%" />
		<col width="50%" />
	</colgroup>
	<thead>
	<tr>
		<th>송장출력예정</th>
		<th>송장전송대기</th>
	</tr>
	</thead>
	<tbody>
	<?php if(isDYLogin()){?>
	<tr>
		<td>
			일반 : <?=number_format($_yet["print"])?><br>
			합포 : <?=number_format($_yet["print_pack"])?>
		</td>
		<td><a href="/order/invoice_reg_list.php?date_start=<?=date("Y-m-d")?>&date_end=<?=date("Y-m-d")?>&market_invoice_state=N" class="link"><?=number_format($_yet["send"])?></a></td>
	</tr>
	<?php } else { ?>
	<tr>
		<td>
			일반 : <?=number_format($_yet["print"])?><br>
			합포 : <?=number_format($_yet["print_pack"])?>
		</td>
		<td><?=number_format($_yet["send"])?></td>
	</tr>
	<?php } ?>
	</tbody>
</table>
