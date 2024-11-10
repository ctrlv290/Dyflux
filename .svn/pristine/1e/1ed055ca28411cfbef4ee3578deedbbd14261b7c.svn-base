<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 매출관리
 */

//Page Index Setting
$pageMenuNo_L = 8;
$pageMenuNo_M = 0;

//Init
include_once "../../_init_.php";

$date = $_GET["date"];

if(!validateDate($date, "Y-m-d")){
	$date = date('Y-m-d');
}

$seller_idx = $_GET["seller_idx"];
if(!$seller_idx) $seller_idx = 0;

$C_Settle = new Settle();

$_list = $C_Settle->getLast30DaysSettleData("settle_sale_supply", $date, $seller_idx, 6, true);
$_cnt_list = $C_Settle->getLast30DaysSettleData("settle_product_cnt", $date, $seller_idx, 6, true);
$_order_list = $C_Settle->getLast30DaysOrder("order", $date, $seller_idx, 6, true);
$_invoice_list = $C_Settle->getLast30DaysOrder("invoice", $date, $seller_idx, 6, true);
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
								<input type="text" name="date" id="date" class="jqDate w90px" value="<?=$date?>" readonly="readonly" />
							</span>
						</div>
						<div class="page_line">
							※ 선택한 날짜를 기준으로 지난 7일간 통계
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
			<style>
				#chartdiv {width: 100%; height: 200px;overflow: hidden;margin-top: 20px;}
			</style>
			<div id="chartdiv">

			</div>
			<style>
				#chartdiv2 {width: 100%; height: 200px;overflow: hidden;margin-top: 20px;}
			</style>
			<div id="chartdiv2">

			</div>
			<style>
				#chartdiv3 {width: 100%; height: 200px;overflow: hidden;margin-top: 20px;}
			</style>
			<div id="chartdiv3">

			</div>
			<style>
				#chartdiv4 {width: 100%; height: 200px;overflow: hidden;margin-top: 20px;}
			</style>
			<div id="chartdiv4">

			</div>
		</div>
	</div>

	<script src="/js/amcharts/core.js"></script>
	<script src="/js/amcharts/charts.js"></script>
	<script src="/js/amcharts/lang/ko_KR.js"></script>
	<script src="/js/amcharts/themes/animated.js"></script>
	<script>
		window.name = 'settle_chart';

		var chartData = [];
		<?php
		foreach($_list as $row) {
			$dt = strtotime($row["dt"]);
			$dt = date('m.d', $dt);

			$val = round($row["sum_settle_sale_supply"] / 10000);

			echo 'chartData.push({"date": "'.$dt.'", "val": '.$val.'});' . PHP_EOL;
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
	</script>
	<script src="../js/page/chart.daily.js"></script>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_footer.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_bottom.php"; ?>
