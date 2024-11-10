<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: HOME - 충전금 부족 업체 리스트
 */

//Page Info
$pageMenuIdx = 0;

//Init
include_once "../_init_.php";

$C_Home = new Home();
$_vendor_list = $C_Home->getNotEnoughChargeVendorList();
?>
<table class="grid">
	<colgroup>
		<col width="50%" />
		<col width="50%" />
	</colgroup>
	<thead>
	<tr>
		<th>벤더사명</th>
		<th>충전금 잔액</th>
	</tr>
	</thead>
	<tbody>
	<?php
	if($_vendor_list)
	{
		foreach($_vendor_list as $_v) {
			?>
			<tr>
				<td class="text_left"><?=$_v["vendor_name"]?></td>
				<td class="text_right"><?=number_format($_v["remain_amount"])?></td>
			</tr>
	<?php
		}
	}else{
	?>
		<tr>
			<td colspan="2" class="text_center">충전금 부족 업체가 없습니다.</td>
		</tr>
	<?php
	}
	?>
	</tbody>
</table>
