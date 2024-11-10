<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 기간별 매출이익
 */
//Page Info
$pageMenuIdx = 262;
//Init
include_once "../_init_.php";

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
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="get">
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<select name="date_year" id="date_year">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == $date_year) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_month" id="date_month">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == $date_month) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처">
							</select>
						</div>
					</div>
				</div>
				<div class="find_btn">
					<div class="table">
						<div class="table_cell">
							<a href="javascript:;" id="btn_searchBar" class="wide_btn btn_default">검색</a>
						</div>
					</div>
				</div>
				<a href="javascript:;" class="find_hide_btn">
					<i class="fas fa-angle-up up_btn"></i>
					<i class="fas fa-angle-down dw_btn"></i>
				</a>
			</div>
		</form>
		<div class="btn_set">
			<p class="sub_tit2">&nbsp;</p>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap">
			<table class="floatThead">
				<thead>
				<tr>
					<th>일자</th>
					<th>판매처</th>
					<th>판매가</th>
					<th>판매가 공급가액</th>
					<th>판매가 부가세</th>
					<th>매입가-단가</th>
					<th>매입가 공급가액</th>
					<th>매입가-단가 부가세</th>
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
					<td class="text_right"><?=number_format($settle_sale_supply_var)?></td>
					<td class="text_right"><?=number_format($settle_purchase_supply)?></td>
					<td class="text_right"><?=number_format($settle_purchase_supply_ex_vat)?></td>
					<td class="text_right"><?=number_format($settle_purchase_supply_vat)?></td>
					<td class="text_right"><?=number_format($settle_sale_profit)?></td>
				</tr>
				<?php if($seller_name == "합계") { ?>

				<tr class="<?=$tr_class?>">
					<td colspan="8">누계</td>
					<td class="text_right"><?=number_format($profit_sum)?></td>
				</tr>

				<?php } ?>
				<?php
				}
				?>
				</tbody>
			</table>
			<br><br>
			<table>
				<thead>
				<tr>
					<th></th>
					<th>판매가</th>
					<th>판매가 공급가액</th>
					<th>판매가 부가세</th>
					<th>매입가-단가</th>
					<th>매입가 공급가액</th>
					<th>매입가-단가 부가세</th>
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
					<td class="text_right"><?=number_format($settle_sale_supply_var)?></td>
					<td class="text_right"><?=number_format($settle_purchase_supply)?></td>
					<td class="text_right"><?=number_format($settle_purchase_supply_ex_vat)?></td>
					<td class="text_right"><?=number_format($settle_purchase_supply_vat)?></td>
					<td class="text_right"><?=number_format($settle_sale_profit)?></td>
				</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>


		<div id="modal_order_write_xls_pop" title="판매처 수동발주 업로드 팝업" class="red_theme" style="display: none;"></div>
		<div id="modal_order_format_seller_pop" title="발주서 포맷 사용자 정의" class="red_theme" style="display: none;"></div>
	</div>
</div>

<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/jquery.floatThead.min.js"></script>
<script src="/js/page/settle.profit.js?v=200410"></script>
<script>
	window.name = 'sales_profit_period';
	SettleProfit.SalesProfitPeriodInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

