<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 분석 차트 팝업 페이지
 */

//Page Info
$pageMenuIdx = 284;
//Init
include_once "../_init_.php";

$product_option_idx = $_GET["product_option_idx"];
$stock_unit_price = $_GET["stock_unit_price"];

$C_Product = new Product();
$C_Stock = new Stock();

$mode = "control_stock_amount";

//상품 옵션 정보
$_view = $C_Product->getProductOptionDataDetail($product_option_idx);

if(!$_view){
	put_msg_and_close("존재하지 않는 상품입니다.");
	exit;
}else{
	extract($_view);
}

//정상재고
$stock_amount_NORMAL = $C_Stock->getCurrentStockAmountByPrice($product_option_idx, "NORMAL", $stock_unit_price);

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<div class="tb_wrap">
				<p class="sub_tit2">상품정보</p>
				<table class="no_border">
					<tr>
						<td>
							<table>
								<colgroup>
									<col width="150">
									<col width="*">
								</colgroup>
								<tbody>
								<tr>
									<th>공급처</th>
									<td class="text_left">
										<?=$supplier_name?>
									</td>
								</tr>
								<tr>
									<th>상품옵션코드</th>
									<td class="text_left">
										<?=$product_option_idx?>
									</td>
								</tr>
								<tr>
									<th>상품명</th>
									<td class="text_left">
										<?=$product_name?>
									</td>
								</tr>
								<tr>
									<th>옵션</th>
									<td class="text_left">
										<?=$product_option_name?>
									</td>
								</tr>
								<?php if($stock_unit_price){?>
								<tr>
									<th>원가</th>
									<td class="text_left">
										<?=number_format($stock_unit_price)?> 원
									</td>
								</tr>
								<?php } ?>
								<tr>
									<th>정상재고</th>
									<td class="text_left">
										<?=number_format($stock_amount_NORMAL)?>
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</table>
			</div>

			<form name="searchForm" id="searchForm" method="get">
				<input type="hidden" id="product_option_idx" name="product_option_idx" value="<?=$product_option_idx?>" />
				<input type="hidden" id="stock_unit_price" name="stock_unit_price" value="<?=$stock_unit_price?>" />
				<div class="find_wrap">
					<div class="finder">
						<div class="finder_set">
							<div class="finder_col">
								<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " readonly="readonly" />
								~
								<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " readonly="readonly" />
								<select class="sel_period_preset" id="period_preset_select">

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

			<div class="tb_wrap ">
				<style>
					#chartdiv {width: 100%; height: 450px;overflow: hidden;}
				</style>
				<div id="chartdiv">

				</div>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>

<script src="/js/amcharts/core.js"></script>
<script src="/js/amcharts/charts.js"></script>
<script src="/js/amcharts/lang/ko_KR.js"></script>
<script src="/js/amcharts/themes/animated.js"></script>

<script src="/js/page/stock.chart.js"></script>
<script src="/js/fileupload.js"></script>
<script>
	window.name = 'stock_chart_pop';
	StockChart.StockChartInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
