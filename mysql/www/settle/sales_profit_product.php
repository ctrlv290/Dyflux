<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품별 매출이익
 */
//Page Info
$pageMenuIdx = 263;
//Init
include_once "../_init_.php";

//$C_Delivery = new Delivery();
//$_delivery_list = $C_Delivery->getDeliveryCodeList();

$date_start               = $_GET["date_start"];
$date_end                 = $_GET["date_end"];
$product_seller_group_idx = (isset($_GET["product_seller_group_idx"])) ? $_GET["product_seller_group_idx"] : "0";
$seller_idx               = $_GET["seller_idx"];
$product_idx              = $_GET["product_idx"];

$C_Settle = new Settle();

if($date_start && $date_end){
	if(validateDate($date_start, 'Y-m-d') && validateDate($date_end, 'Y-m-d')) {
		$_list = $C_Settle->getSalesProfitByProduct($date_start, $date_end, $seller_idx, $product_idx);
	}
}
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
							<span class="text" style="margin-right: 8px;">기 간</span>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
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
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">상품코드</span>
							<input type="text" name="product_idx" class="w200px enterDoSearch onlyNumber" placeholder="검색어" value="<?=$product_idx?>" />
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
				<colgroup>
					<col width="120" />
					<col width="80" />
					<col width="*" />
					<col width="*" />
					<col width="80" />
					<col width="*" />
					<col width="*" />
					<col width="*" />
					<col width="*" />
					<col width="*" />
					<col width="*" />
					<col width="100" />

				</colgroup>
				<thead>
				<tr>
					<th>일자</th>
					<th>상품코드</th>
					<th>상품명</th>
					<th>옵션</th>
					<th>판매수량</th>
					<th>매출공급가액</th>
					<th>매출공급가액<br>(부가세 제외)</th>
					<th>매출공급가액<br>부가세</th>
					<th>매출원가공급가액</th>
					<th>매출원가공급가액<br>(부가세 제외)</th>
					<th>매출원가공급가액<br>부가세</th>
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
					$product_idx                   = $row["product_idx"];
					$product_name                  = $row["product_name"];
					$product_option_name           = $row["product_option_name"];
					$settle_sale_supply            = $row["settle_sale_supply"];
					$settle_sale_supply_ex_vat     = $row["settle_sale_supply_ex_vat"];
					$settle_sale_supply_var        = $settle_sale_supply - $settle_sale_supply_ex_vat;
					$settle_purchase_supply        = $row["settle_purchase_supply"];
					$settle_purchase_supply_ex_vat = $row["settle_purchase_supply_ex_vat"];
					$settle_purchase_supply_vat    = $settle_purchase_supply - $settle_purchase_supply_ex_vat;
					$settle_sale_profit            = $row["settle_sale_profit"];
					$product_option_cnt            = $row["product_option_cnt"];

					$td_colspan = 1;
					$tr_class="";
					if($product_name == null) {
						$tr_class="sum";
						$td_colspan = 4;
						$product_name = "합계";
					}else{
						$profit_sum      += $settle_sale_profit;
					}
					?>
					<tr class="<?=$tr_class?>">
						<?php if($product_name == "합계") { ?>
							<td colspan="<?=$td_colspan?>"><?=$product_name?></td>
						<?php }else{ ?>
							<td><?=$date?></td>
							<td><?=$product_idx?></td>
							<td class="text_left"><?=$product_name?></td>
							<td class="text_left"><?=$product_option_name?></td>
						<?php } ?>
						<td class="text_right"><?=number_format($product_option_cnt)?></td>
						<td class="text_right"><?=number_format($settle_sale_supply)?></td>
						<td class="text_right"><?=number_format($settle_sale_supply_ex_vat)?></td>
						<td class="text_right"><?=number_format($settle_sale_supply_var)?></td>
						<td class="text_right"><?=number_format($settle_purchase_supply)?></td>
						<td class="text_right"><?=number_format($settle_purchase_supply_ex_vat)?></td>
						<td class="text_right"><?=number_format($settle_purchase_supply_vat)?></td>
						<td class="text_right"><?=number_format($settle_sale_profit)?></td>
					</tr>
					<?php if($product_name == "합계") { ?>

						<tr class="<?=$tr_class?>">
							<td colspan="11">누계</td>
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
					<th>수량</th>
					<th>매출공급가액</th>
					<th>매출공급가액<br>(부가세 제외)</th>
					<th>매출공급가액<br>부가세</th>
					<th>매출원가공급가액</th>
					<th>매출원가공급가액<br>(부가세 제외)</th>
					<th>매출원가공급가액<br>부가세</th>
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
					$product_option_cnt            = $row["product_option_cnt"];

					?>
					<tr>
						<td>월 합 계</td>
						<td class="text_right"><?=number_format($product_option_cnt)?></td>
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
	SettleProfit.SalesProfitProductInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

