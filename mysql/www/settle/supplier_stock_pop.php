<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공급처별정산(재고) - 공급처상품상세 팝업 페이지
 */
//Page Info
$pageMenuIdx = 222;
//Init
include_once "../_init_.php";

$supplier_idx  = $_GET["supplier_idx"];
$supplier_name = $_GET["supplier_name"];
$date_start    = $_GET["date_start"];
$date_end      = $_GET["date_end"];

$C_Settle = new Settle();
$_list = $C_Settle->getSupplierStockList($supplier_idx, $date_start, $date_end)
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<div class="tb_wrap">
				<p>
					공급처코드 : <?=$supplier_idx?>
					/
					공급처명 : <?=$supplier_name?>
					/
					기간 : <?=$date_start?> ~ <?=$date_end?>
				</p>
				<table>
					<colgroup>
						<col width="100" />
						<col width="250" />
						<col width="250" />
					</colgroup>
					<thead>
					<tr>
						<th>옵션코드</th>
						<th>상품명</th>
						<th>옵션</th>
						<th>공급처상품명</th>
						<th>공급처옵션</th>
						<th>입고수량</th>
						<th>원가</th>
						<th>총원가</th>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach($_list as $row) {
					?>
					<tr>
						<?php if($row["product_option_idx"] == null){?>
						<td colspan="5">합계</td>
						<?php }else{?>
						<td><?=$row["product_option_idx"]?></td>
						<td class="text_left"><?=$row["product_name"]?></td>
						<td class="text_left"><?=$row["product_option_name"]?></td>
						<td class="text_left"><?=$row["product_supplier_name"]?></td>
						<td class="text_left"><?=$row["product_supplier_option"]?></td>
						<?php }?>
						<td class="text_right"><?=number_format($row["stock_in_amount"])?></td>
						<td class="text_right"><?=number_format($row["stock_unit_price"])?></td>
						<td class="text_right"><?=number_format($row["stock_in_sum"])?></td>
					</tr>
					<?php
					}
					?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/settle_manage.js"></script>
<script>
	window.name = "ssupplier_stock_pop";
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>