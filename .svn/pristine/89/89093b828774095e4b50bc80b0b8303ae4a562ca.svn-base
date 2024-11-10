<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 일별상품별통계 차트 팝업 페이지
 */

//Page Info
$pageMenuIdx = 285;
//Init
include_once "../_init_.php";

$format = $_GET["format"];
if($format != "day" && $format != "month"){
	$format = "day";
}

if($format == "day"){
	//Page Info
	$pageMenuIdx = 285;
}else{
	//Page Info
	$pageMenuIdx = 286;
}


$product_option_idx = $_GET["product_option_idx"];
$product_option_purchase_price = $_GET["product_option_purchase_price"];

$date_start = $_GET["date_start"];
$date_end = $_GET["date_end"];

$seller_idx = $_GET["seller_idx"];

if($format == "month"){
	$date_start_year  = $_GET["date_start_year"];
	$date_start_month = $_GET["date_start_month"];
	$date_end_year    = $_GET["date_end_year"];
	$date_end_month   = $_GET["date_end_month"];

	$date_start = date("Y-m-d", strtotime($date_start_year . "-" . make2digit($date_start_month) . "-01"));
	$date_end = date("Y-m-t", strtotime($date_end_year . "-" . make2digit($date_end_month) . "-01"));
}

$C_Product = new Product();
$C_Stock = new Stock();

$mode = "control_stock_amount";

//상품 옵션 정보
$_view = $C_Product->getProductOptionDataDetail($product_option_idx);

if(!$_view){
	put_msg_and_close("존재하지 않는 상품입니다.");
	exit;
}else{
	//extract($_view);
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
			<div class="tb_wrap dis_none">
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
				<input type="hidden" id="product_option_purchase_price" name="product_option_purchase_price" value="<?=$product_option_purchase_price?>" />
				<input type="hidden" id="format" name="format" value="<?=$format?>" />
				<div class="find_wrap">
					<div class="finder">
						<div class="finder_set">
							<?php if($format == "day"){?>
							<div class="finder_col">
								<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
								~
								<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
								<select class="sel_period_preset" id="period_preset_select">

								</select>
							</div>
							<?php } else { ?>

							<div class="finder_col">
								<select name="date_start_year" id="period_start_year_input">
									<?php
									for($i = 2018;$i<=date('Y');$i++){
										$selected = ($i == date('Y', strtotime($date_start))) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
								<select name="date_start_month" id="period_start_month_input">
									<?php
									for($i = 1;$i<=12;$i++){
										$selected = ($i == date('m', strtotime($date_start))) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
								~
								<select name="date_end_year" id="period_end_year_input">
									<?php
									for($i = 2018;$i<=date('Y');$i++){
										$selected = ($i == date('Y', strtotime($date_end))) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
								<select name="date_end_month" id="period_end_month_input">
									<?php
									for($i = 1;$i<=12;$i++){
										$selected = ($i == date('m', strtotime($date_end))) ? 'selected="selected"' : '';
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
							</div>

							<?php } ?>

							<div class="finder_col">
								<span class="text">판매처</span>
								<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="0">
									<option value="0">전체그룹</option>
								</select>
								<select name="seller_idx" id="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처">
									<?php if($seller_idx) {
										echo '<option value="'.$seller_idx.'" selected></option>';
									}?>
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
					#legenddiv {width: 20%; height: 450px;overflow-y: scroll;display: inline-block;}
					#chartdiv {width: 79%; height: 450px;overflow: hidden;display: inline-block;}
				</style>
				<div id="legenddiv"></div>
				<div id="chartdiv">

				</div>

			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">


<script src="/js/amcharts/core.js"></script>
<script src="/js/amcharts/charts.js"></script>
<script src="/js/amcharts/lang/ko_KR.js"></script>
<script src="/js/amcharts/themes/animated.js"></script>

<script src="/js/page/settle.product.chart.js"></script>
<script src="/js/fileupload.js"></script>
<script>
	window.name = 'settle_product_chart_pop';
	SettleProductChart.SettleProductChartInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
