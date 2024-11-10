<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매출캘린더 페이지
 */
//Page Info
$pageMenuIdx = 141;
//Init
include_once "../_init_.php";

$_is_DYLogin = isDYLogin();

$date_year = $_GET["date_year"];
$date_month = $_GET["date_month"];
$date = $date_year . "-" . make2digit($date_month) . "-01";

if(!validateDate($date . " 00:00:00")){
	$date = date('Y-m-d');
	$date_year = date('Y');
	$date_month = date('m');
}

$seller_idx = $_GET["seller_idx"];
$supplier_idx = $_GET["$supplier_idx"];
if(!$seller_idx) $seller_idx = 0;
if(!$supplier_idx) $supplier_idx = 0;

$C_Settle = new Settle();

$_list = $C_Settle->getThisMonthsSettleData("settle_sale_supply", $date, $seller_idx, $supplier_idx);
$_amount_min = "";
$_amount_max = "";
$_amount_total = 0;
$_count_total = 0;
$_listAry = array();

foreach ($_list as $row) {

	if($_amount_min === ""){
		$_amount_min = $row["sum_settle_sale_supply"];
	}else{

		$_amount_min = ($_amount_min > $row["sum_settle_sale_supply"]) ? $row["sum_settle_sale_supply"] : $_amount_min;
	}

	if($_amount_max === ""){
		$_amount_max = $row["sum_settle_sale_supply"];
	}else{
		$_amount_max = ($_amount_max < $row["sum_settle_sale_supply"]) ? $row["sum_settle_sale_supply"] : $_amount_max;
	}

	$_amount_total += $row["sum_settle_sale_supply"];
	$_count_total += $row["sum_product_option_cnt"];

	$_listAry[$row["dt"]] = array("amount" => $row["sum_settle_sale_supply"], "count" => $row["sum_product_option_cnt"]);
}

//---- 기준날짜
$thisyear = date('Y', strtotime($date)); // 4자리 연도
$thismonth = date('n', strtotime($date)); // 0을 포함하지 않는 월
$today = date('j', strtotime($date)); // 0을 포함하지 않는 일

//------ $year, $month 값이 없으면 현재 날짜
$year = isset($_GET['year']) ? $_GET['year'] : $thisyear;
$month = isset($_GET['month']) ? $_GET['month'] : $thismonth;
$day = isset($_GET['day']) ? $_GET['day'] : $today;

$prev_month = $month - 1;
$next_month = $month + 1;
$prev_year = $next_year = $year;
if ($month == 1) {
    $prev_month = 12;
    $prev_year = $year - 1;
} else if ($month == 12) {
    $next_month = 1;
    $next_year = $year + 1;
}
$preyear = $year - 1;
$nextyear = $year + 1;

$predate = date("Y-m-d", mktime(0, 0, 0, $month - 1, 1, $year));
$nextdate = date("Y-m-d", mktime(0, 0, 0, $month + 1, 1, $year));

// 1. 총일수 구하기
$max_day = date('t', mktime(0, 0, 0, $month, 1, $year)); // 해당월의 마지막 날짜
//echo '총요일수'.$max_day.'<br />';

// 2. 시작요일 구하기
$start_week = date("w", mktime(0, 0, 0, $month, 1, $year)); // 일요일 0, 토요일 6

// 3. 총 몇 주인지 구하기
$total_week = ceil(($max_day + $start_week) / 7);

// 4. 마지막 요일 구하기
$last_week = date('w', mktime(0, 0, 0, $month, $max_day, $year));


function avgAmount($amount)
{
	global $_amount_min, $_amount_max;

	$long = $_amount_max - $_amount_min;
	$cur = $amount - $_amount_min;
	$cal = round(($cur/$long) * 100);

	return $cal;
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
							<span class="text">발주일</span>
							<select name="date_year" id="period_start_year_input">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == $date_year) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_month" id="period_start_month_input">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == $date_month) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<?php if(isDYLogin()){?>
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="0">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_id]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처">
							</select>
						</div>
						<?php } ?>
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
		<p class="sub_tit">총 금액 : <span class="red_strong"><?=number_format($_amount_total)?></span>원 (수량 : <?=number_format($_count_total)?> 개)</p>
		<div class="tb_wrap">
			<?php
			$dt = strtotime("2019-03-23");
			$start_date = date('Y-m-01', $dt);
			$end_date = date('Y-m-t', strtotime($start_date));

			$i = 0;
			do{
				$new_date = strtotime('+'.$i++.' days', $start_date);
				$_search_date_ary[] =  "" . date('Y-m-d', $new_date) . "";
				$_qry_date_ary[] = array(
					"date" => date('Y-m-d', $new_date),
					"colName" => "s".date('Ymd', $new_date)
				);
			}while ($new_date < $end_date);
			?>
			<table class="max1200 calendar">
				<thead>
				<tr>
					<th>일</th>
					<th>월</th>
					<th>화</th>
					<th>수</th>
					<th>목</th>
					<th>금</th>
					<th>토</th>
				</tr>
				</thead>
				<tbody>
				<?php
				// 5. 화면에 표시할 화면의 초기값을 1로 설정
				$day=1;

				// 6. 총 주 수에 맞춰서 세로줄 만들기
				for($i=1; $i <= $total_week; $i++) {
				?>
				<tr>
					<?php
					// 7. 총 가로칸 만들기
					for ($j = 0; $j < 7; $j++) {
						// 8. 첫번째 주이고 시작요일보다 $j가 작거나 마지막주이고 $j가 마지막 요일보다 크면 표시하지 않음

						if (!(($i == 1 && $j < $start_week) || ($i == $total_week && $j > $last_week))) {

							if($_is_DYLogin) {
								echo '<td style="cursor: pointer;" onclick="location.href=\'/settle/transaction_list.php?date_start=' . $year . '-' . make2digit($month) . '-' . make2digit($day) . '&date_end=' . $year . '-' . make2digit($month) . '-' . make2digit($day) . '\'">';
							}else{
								echo '<td>';
							}

							if ($j == 0) {
								// 9. $j가 0이면 일요일이므로 빨간색
								$style = "holy";
							} else if ($j == 6) {
								// 10. $j가 0이면 토요일이므로 파란색
								$style = "blue";
							} else {
								// 11. 그외는 평일이므로 검정색
								$style = "black";
							}

							// 12. 오늘 날짜면 굵은 글씨
							if ($year == $thisyear && $month == $thismonth && $day == date("j")) {
								// 13. 날짜 출력
								echo '<div class="day ' . $style . '">';
								echo $day;
								echo '</div>';
							} else {
								echo '<div class="day ' . $style . '">';
								echo $day;
								echo '</div>';
							}

							$l_date = $year . "-" . make2digit($month) . "-" . make2digit($day);
							//매출 출력

							$amount = $_listAry[$l_date]["amount"];
							$count = $_listAry[$l_date]["count"];

							$per = round(avgAmount($amount) / 2);

							if($amount != 0 && $count != 0) {
								echo '<div class="graph"><span class="bar" style="height: ' . $per . 'px;"></span></div>';
								echo '<div class="amount">
									' . number_format($amount) . '
									<span class="cnt">(' . number_format($count) . ')</span>
								</div>';
							}
							// 14. 날짜 증가
							$day++;
							echo '</td>';
						}else {
							echo '<td>';
							echo '</td>';
						}
					}
					?>
				</tr>
				<?php
				}
				?>
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

<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.chart.js"></script>
<script>
	window.name = 'settle_chart';


	SettleChart.ChartCalendarInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

