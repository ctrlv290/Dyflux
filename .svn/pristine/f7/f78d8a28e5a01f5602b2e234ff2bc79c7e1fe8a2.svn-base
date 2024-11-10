<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 매출관리
 */

//Page Index Setting
$pageMenuNo_L = 10;
$pageMenuNo_M = 0;

//Init
include_once "../../_init_.php";

$C_Delivery = new Delivery();
$_delivery_list = $C_Delivery->getDeliveryCodeList();

$date_year                = $_GET["date_year"];
$date_month               = $_GET["date_month"];
$product_seller_group_idx = (isset($_GET["product_seller_group_idx"])) ? $_GET["product_seller_group_idx"] : "0";
$seller_idx               = $_GET["seller_idx"];

$C_Settle = new Settle();

if($date_year && $date_month){
	$_list = $C_Settle -> getSalesProfitByPeriod($date_year, $date_month, $seller_idx, $product_seller_group_idx);
}

if(!$date_year) $date_year = date('Y');
if(!$date_month) $date_month = date('m');
?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_header.php"; ?>
	<div class="wrap_main">
		<div class="wrap_page bd_non">
			<div class="wrap_page_in">
				<form name="dyForm" id="dyForm">
					<div class="form_sale_set">
						<div class="page_line">
							<span class="title">발주일</span>
							<span class="select_set">
								<select name="date_year" id="period_start_year_input">
									<?php
									for($i = 2018;$i<=date('Y');$i++){
										$selected = ($i == date('Y')) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
								<select name="date_month" id="period_start_month_input">
									<?php
									for($i = 1;$i<=12;$i++){
										$selected = ($i == date('m')) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
							</span>
						</div>
						<div class="page_line sellers">
							<span class="title">판매처</span>
							<span class="select_set">
								<select name="product_seller_group_idx" class="product_seller_group_idx w100px" data-selected="0">
									<option value="0">전체그룹</option>
								</select>
								<select name="seller_idx" id="seller_idx" class="seller_idx w100px" data-selected="0" data-default-value="" data-default-text="전체 판매처">
								</select>
							</span>
						</div>
					</div>
					<a href="javascript:;" id="btn-search" class="search_btn">검색</a>
				</form>
			</div>
		</div>
		<div class="wrap_inner">
			<div class="wrap_scroll mt20 table_result">
				<table class="table_style05">
					<colgroup>
						<col style="width: 100px;">
						<col style="width: 150px;">
						<col style="width: 100px;">
						<col style="width: 100px;">
						<col style="width: 100px;">
						<col style="width: 100px;">
						<col style="width: 100px;">
					</colgroup>
					<thead>
					<tr>
						<th>일자</th>
						<th>판매처</th>
						<th>매출공급가액</th>
						<th>매출공급가액<br>(부가세 제외)</th>
						<th>매출원가공급가액</th>
						<th>매출원가공급가액<br>(부가세 제외)</th>
						<th>매출이익</th>
					</tr>
					</thead>
					<tbody>
					<?php

					$sale_sum            = 0;
					$sale_ex_vat_sum     = 0;
					$sale_vat_sum        = 0;
					$purchase_sum        = 0;
					$purchase_ex_vat_sum = 0;
					$purchase_vat_sum    = 0;
					$profit_sum          = 0;

					$_list_cnt = count($_list);

					for($i=0;$i<($_list_cnt-1);$i++){

						$row = $_list[$i];

						$date                          = $row["settle_date"];
						$seller_name                   = $row["seller_name"];
						$settle_sale_supply            = $row["settle_sale_supply"];
						$settle_sale_supply_ex_vat     = $row["settle_sale_supply_ex_vat"];
						$settle_sale_supply_var        = $settle_sale_supply - $settle_sale_supply_ex_vat;
						$settle_purchase_supply        = $row["settle_purchase_supply"];
						$settle_purchase_supply_ex_vat = $row["settle_purchase_supply_ex_vat"];
						$settle_purchase_supply_vat    = $settle_purchase_supply - $settle_purchase_supply_ex_vat;
						$settle_sale_profit            = $row["settle_sale_profit"];

						$td_colspan = 1;
						$tr_class="";
						if($seller_name == null) {
							$tr_class="sum";
							$td_colspan = 2;
							$seller_name = "합계";
						}else{
							$profit_sum      += $settle_sale_profit;
						}
						?>
						<tr class="<?=$tr_class?>">
							<?php if($seller_name != "합계") { ?>
								<td><?=$date?></td>
							<?php } ?>
							<td colspan="<?=$td_colspan?>"><?=$seller_name?></td>
							<td class="text_right"><?=number_format($settle_sale_supply)?></td>
							<td class="text_right"><?=number_format($settle_sale_supply_ex_vat)?></td>
							<td class="text_right"><?=number_format($settle_purchase_supply)?></td>
							<td class="text_right"><?=number_format($settle_purchase_supply_ex_vat)?></td>
							<td class="text_right"><?=number_format($settle_sale_profit)?></td>
						</tr>
						<?php if($seller_name == "합계") { ?>

							<tr class="<?=$tr_class?>">
								<td colspan="6">누계</td>
								<td class="text_right"><?=number_format($profit_sum)?></td>
							</tr>

						<?php } ?>
						<?php
					}
					?>
					</tbody>
				</table>
			</div>
			<div class="wrap_scroll mt20 table_result">
				<table class="table_style05">
					<colgroup>
						<col style="width: 100px;">
						<col style="width: 100px;">
						<col style="width: 100px;">
						<col style="width: 100px;">
						<col style="width: 100px;">
						<col style="width: 100px;">
					</colgroup>
					<thead>
					<tr>
						<th></th>
						<th>매출공급가액</th>
						<th>매출공급가액<br>(부가세 제외)</th>
						<th>매출원가공급가액</th>
						<th>매출원가공급가액<br>(부가세 제외)</th>
						<th>매출이익</th>
					</tr>
					</thead>
					<tbody>
					<?php
					if($_list_cnt > 0){
						$row = $_list[$_list_cnt-1];
						$settle_sale_supply            = $row["settle_sale_supply"];
						$settle_sale_supply_ex_vat     = $row["settle_sale_supply_ex_vat"];
						$settle_sale_supply_var        = $settle_sale_supply - $settle_sale_supply_ex_vat;
						$settle_purchase_supply        = $row["settle_purchase_supply"];
						$settle_purchase_supply_ex_vat = $row["settle_purchase_supply_ex_vat"];
						$settle_purchase_supply_vat    = $settle_purchase_supply - $settle_purchase_supply_ex_vat;
						$settle_sale_profit            = $row["settle_sale_profit"];

						?>
						<tr>
							<td>월 합 계</td>
							<td class="text_right"><?=number_format($settle_sale_supply)?></td>
							<td class="text_right"><?=number_format($settle_sale_supply_ex_vat)?></td>
							<td class="text_right"><?=number_format($settle_purchase_supply)?></td>
							<td class="text_right"><?=number_format($settle_purchase_supply_ex_vat)?></td>
							<td class="text_right"><?=number_format($settle_sale_profit)?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<script src="../js/page/sale.profit.period.js"></script>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_footer.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_bottom.php"; ?>