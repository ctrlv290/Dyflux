<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산예정금 통계 페이지
 */
//Page Info
$pageMenuIdx = 270;
//Init
include_once "../_init_.php";

$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];

$period_type                = "order_accept";

$C_Settle = new Settle();
if($date_start && $date_end && $seller_idx){
	$_list = $C_Settle -> getLossStatistics($seller_idx, $date_start, $date_end);
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
							<span class="text">입금예정일</span>
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
							<select name="seller_idx" class="seller_idx"  id="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="판매처를 선택해주세요.">
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
			<?php if($_list){ ?>
			<div id="chartdiv">

			</div>
			<?php } ?>
		</div>
		<?php if($_list){ ?>
		<div class="btn_set">
			<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			<div class="right">

			</div>
		</div>
		<?php } ?>
		<div class="tb_wrap">
			<table class="max1200">
				<colgroup>
					<col width="150">
					<col width="*">
					<col width="*">
					<col width="*">
					<col width="*">
				</colgroup>
				<thead>
				<tr>
					<th>입금예정일</th>
					<th>수집정보 합계<br>(판매가+배송비, 수수료 제외)</th>
					<th>사이트 합계<br>(판매가+배송비, 수수료 제외)</th>
					<th>공제/환급액 등</th>
					<th>실입금액 합계</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$settle_total     = 0;
				$site_total       = 0;
				$commission_total = 0;
				$tran_total       = 0;
				foreach($_list as $row)
				{
					$settle_date    = $row["settle_date"];

					if(!$settle_date) $settle_date = $row["loss_date"];
					if(!$settle_date) $settle_date = $row["tran_date"];

					$settle_sum     = $row["settle_sum"];
					$site_sum       = $row["site_sum"];
					$commission_etc = $row["commission_etc"];
					$tran_amount    = $row["tran_amount"];

					$settle_total     += $settle_sum;
					$site_total       += $site_sum;
					$commission_total += $commission_etc;
					$tran_total       += $tran_amount;
					?>
					<tr>
						<td><?=$settle_date?></td>
						<td class="text_right"><?=number_format($settle_sum)?></td>
						<td class="text_right"><?=number_format($site_sum)?></td>
						<td class="text_right"><?=number_format($commission_etc)?></td>
						<td class="text_right"><?=number_format($tran_amount)?></td>
					</tr>
					<?php
				}
				?>
				<tr class="sum">
					<td>합계</td>
					<td class="text_right"><?=number_format($settle_total)?></td>
					<td class="text_right"><?=number_format($site_total)?></td>
					<td class="text_right"><?=number_format($commission_total)?></td>
					<td class="text_right"><?=number_format($tran_total)?></td>
				</tr>
				</tbody>
			</table>
		</div>
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

<script src="/js/page/settle.loss.js?v=200410"></script>
<script>
	window.name = 'settle_loss_statistics';

	var chartData = [];
	<?php
	foreach($_list as $row) {


		$settle_date    = $row["settle_date"];

		if(!$settle_date) $settle_date = $row["loss_date"];
		if(!$settle_date) $settle_date = $row["tran_date"];

		$dt = strtotime($settle_date);
		$dt = date('m.d', $dt);

		$settle_sum     = $row["settle_sum"];
		$site_sum       = $row["site_sum"];
		$commission_etc = $row["commission_etc"];
		$tran_amount    = $row["tran_amount"];

		if(!$settle_sum) $settle_sum = 0;
		if(!$site_sum) $site_sum = 0;
		if(!$commission_etc) $commission_etc = 0;
		if(!$tran_amount) $tran_amount = 0;

		echo 'chartData.push({"date": "'.$dt.'", "settle": '.$settle_sum.', "site": '.$site_sum.', "etc": '.$commission_etc.', "tran": '.$tran_amount.'});' . PHP_EOL;
	}

	if($_list){
		echo 'SettleLoss.LossStatisticsChart();';
	}

	?>

	SettleLoss.LossStatisticsInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

