<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 일별매출차트 페이지
 */
//Page Info
$pageMenuIdx = 140;
//Init
include_once "../_init_.php";

$date = $_GET["date"];

if(!validateDate($date, "Y-m-d")){
	$date = date('Y-m-d');
}

$seller_idx = $_GET["seller_idx"];
if(!$seller_idx) $seller_idx = 0;

$C_Settle = new Settle();

$_list = $C_Settle->getLast30DaysSettleData("settle_sale_supply", $date, $seller_idx);
$_cnt_list = $C_Settle->getLast30DaysSettleData("settle_product_cnt", $date, $seller_idx);
$_order_list = $C_Settle->getLast30DaysOrder("order", $date, $seller_idx);
$_invoice_list = $C_Settle->getLast30DaysOrder("invoice", $date, $seller_idx);

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
							<span class="text">발주일</span>
							<input type="text" name="date" id="chart_date" class="w80px jqDate " value="<?=$date?>" readonly="readonly" />
							<span class="text">* 선택일 기준 최근 30일간 통계</span>
						</div>
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="0">
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
		<div class="tb_wrap ">
			<style>
				#chartdiv {width: 100%; height: 450px;overflow: hidden;}
			</style>
			<div id="chartdiv">

			</div>
		</div>

		<div class="tb_wrap ">
			<style>
				#chartdiv2 {width: 100%; height: 450px;overflow: hidden;}
			</style>
			<div id="chartdiv2">

			</div>
		</div>

		<div class="tb_wrap ">
			<style>
				#chartdiv3 {width: 100%; height: 450px;overflow: hidden;}
			</style>
			<div id="chartdiv3">

			</div>
		</div>

		<div class="tb_wrap ">
			<style>
				#chartdiv4 {width: 100%; height: 450px;overflow: hidden;}
			</style>
			<div id="chartdiv4">

			</div>
		</div>
<!---->
<!--		<div class="tb_wrap ">-->
<!--			<style>-->
<!--				#chartdiv5 {width: 100%; height: 450px;overflow: hidden;}-->
<!--			</style>-->
<!--			<div id="chartdiv5">-->
<!---->
<!--			</div>-->
<!--		</div>-->
	</div>
</div>

<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">

<script src="/js/amcharts/core.js"></script>
<script src="/js/amcharts/charts.js"></script>
<script src="/js/amcharts/lang/ko_KR.js"></script>
<script src="/js/amcharts/themes/animated.js"></script>

<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.chart.js"></script>
<script>
	window.name = 'settle_chart';

	var chartData = [];
	<?php
	foreach($_list as $row) {
		$dt = strtotime($row["dt"]);
		$dt = date('m.d', $dt);
		$val = $row["sum_settle_sale_supply"];
		$val2 = round($row["sum_settle_sale_supply"] / 10000);

		echo 'chartData.push({"date": "'.$dt.'", "val": '.$val2.', "val2": '.$val.'});' . PHP_EOL;
	}
	?>

	var chartData2 = [];
	<?php
	foreach($_cnt_list as $row) {
		$dt = strtotime($row["dt"]);
		$dt = date('m.d', $dt);

		echo 'chartData2.push({"date": "'.$dt.'", "val": '.$row["sum_product_option_cnt"].'});' . PHP_EOL;
	}
	?>

	var chartData3 = [];
	<?php
	foreach($_order_list as $row) {
		$dt = strtotime($row["dt"]);
		$dt = date('m.d', $dt);

		echo 'chartData3.push({"date": "'.$dt.'", "val": '.$row["order_cnt"].'});' . PHP_EOL;
	}
	?>

	var chartData4 = [];
	<?php
	foreach($_invoice_list as $row) {
		$dt = strtotime($row["dt"]);
		$dt = date('m.d', $dt);

		echo 'chartData4.push({"date": "'.$dt.'", "val": '.$row["order_cnt"].'});' . PHP_EOL;
	}
	?>
	//console.log(chartData);

	SettleChart.ChartDailyInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

