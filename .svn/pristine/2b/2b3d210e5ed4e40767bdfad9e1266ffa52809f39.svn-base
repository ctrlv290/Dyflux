<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: HOME - 재고현황
 */

//Page Info
$pageMenuIdx = 0;

//Init
include_once "../_init_.php";

$C_Home = new Home();
if(isDYLogin()) {
	$_stock = $C_Home->getStock();
}else{
	$_stock = $C_Home->getStockForVendor();
}
?>

<?php if(isDYLogin()){?>
<table class="grid">
	<colgroup>
		<col width="17%" />
		<col width="17%" />
		<col width="17%" />
		<col width="17%" />
		<col width="17%" />
		<col width="16%" />
	</colgroup>
	<thead>
	<tr>
		<th>현재고</th>
		<th>금일입고</th>
		<th>금일출고</th>
		<th>금일배송</th>
		<th>금일불량</th>
		<th>재고경고</th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td><a href="/stock/stock_list.php" class="link"><?=number_format($_stock["sum"])?></a></td>
		<td><a href="/stock/stock_period_list.php?date_start=<?=date("Y-m-d")?>&date_end=<?=date("Y-m-d")?>&stock_kind=IN" class="link"><?=number_format($_stock["in"])?></a></td>
		<td><a href="/stock/stock_period_list.php?date_start=<?=date("Y-m-d")?>&date_end=<?=date("Y-m-d")?>&stock_kind=OUT" class="link"><?=number_format($_stock["out"])?></a></td>
		<td><a href="/stock/stock_period_list.php?date_start=<?=date("Y-m-d")?>&date_end=<?=date("Y-m-d")?>&stock_kind=SHIPPED" class="link"><?=number_format($_stock["shipped"])?></a></td>
		<td><a href="/stock/stock_period_list.php?date_start=<?=date("Y-m-d")?>&date_end=<?=date("Y-m-d")?>&stock_kind=BAD_last" class="link"><?=number_format($_stock["bad"])?></a></td>
		<td><a href="/stock/stock_list.php?date_start=<?=date("Y-m-d")?>&date_end=<?=date("Y-m-d")?>&stock_alert=stock_warning" class="link"><?=number_format($_stock["warning"])?></a></td>
	</tr>
	</tbody>
</table>
<?php } else { ?>
	<table class="grid">
		<colgroup>
			<col width="100%" />
		</colgroup>
		<thead>
		<tr>
			<th>현재고</th>
		</tr>
		</thead>
		<tbody>
			<tr>
				<td><a href="/stock/stock_list_vendor.php" class="link"><?=number_format($_stock["sum"])?></a></td>
			</tr>
		</tbody>
	</table>
<?php } ?>
