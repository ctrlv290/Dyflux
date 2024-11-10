<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 매출관리
 */

//Page Index Setting
$pageMenuNo_L = 9;
$pageMenuNo_M = 0;

//Init
include_once "../../_init_.php";

$date_year = $_GET["date_year"];
$date_month = $_GET["date_month"];
$date = $date_year . "-" . make2digit($date_month) . "-01";

if(!validateDate($date . " 00:00:00")){
	$date = date('Y-m-d');
	$date_year = date('Y');
	$date_month = date('m');
}

$select_date_time = strtotime($date);
$prev_date_year = date('Y', strtotime("-1 month", $select_date_time));
$prev_date_month = date('m', strtotime("-1 month", $select_date_time));
$next_date_year = date('Y', strtotime("+1 month", $select_date_time));
$next_date_month = date('m', strtotime("+1 month", $select_date_time));

$seller_idx = $_GET["seller_idx"];
$supplier_idx = $_GET["$supplier_idx"];
if(!$seller_idx) $seller_idx = 0;
if(!$supplier_idx) $supplier_idx = 0;

$C_Settle = new Settle();

$_list = $C_Settle->getThisMonthsSettleData("settle_sale_supply", $date, $seller_idx, $supplier_idx, true);
$_amount_min = "";
$_amount_max = "";
$_amount_total = 0;
$_count_total = 0;
$_listAry = array();

$week_han = array('일','월','화','수','목','금','토');

?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="wrap_main">
	<div class="wrap_page bd_non">
		<div class="wrap_page_in">
			<form name="dyForm" id="dyForm">
				<input type="hidden" name="date_year" value="<?=$date_year?>" />
				<input type="hidden" name="date_month" value="<?=$date_month?>" />
				<div class="form_sale_set">
					<div class="page_line" style="text-align: center;">
						<a href="chart_calendar.php?date_year=<?=$prev_date_year?>&date_month=<?=$prev_date_month?>&seller_idx=<?=$seller_idx?>"><img src="../images/arrow_l.png" alt="" /></a>
						<span class="year"><?=$date_year?></span>
						<span class="month"><?=$date_month?></span>
						<a href="chart_calendar.php?date_year=<?=$next_date_year?>&date_month=<?=$next_date_month?>&seller_idx=<?=$seller_idx?>"><img src="../images/arrow_r.png" alt="" /></a>
					</div>
					<div class="page_line sellers">
						<span class="title">판매처</span>
						<span class="select_set">
							<select name="product_seller_group_idx" class="product_seller_group_idx w100px" data-selected="0">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx" id="seller_idx" class="seller_idx w100px" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처">
							</select>
						</span>
					</div>
				</div>
				<a href="javascript:;" id="btn-search" class="search_btn">검색</a>
			</form>
		</div>
	</div>
	<div class="wrap_inner mt20">
		<table class="table_style03 lowHeight">
			<colgroup>
				<col width="100">
				<col width="*">
			</colgroup>
			<tbody>
			<?php
			foreach($_list as $row){
				$dt = $row["dt"];
				$dt_time = strtotime($dt);
				$dt_short = date("m-d", $dt_time);
				$dt_yoil = $week_han[date("w", $dt_time)];
			?>
			<tr>
				<th class=""><?=$dt_short?> (<?=$dt_yoil?>)</th>
				<td class="text_right"><?=number_format($row["sum_settle_sale_supply"])?> (<?=number_format($row["sum_product_option_cnt"])?>)</td>
			</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>

<script src="../js/page/chart.calendar.js"></script>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_footer.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_bottom.php"; ?>
