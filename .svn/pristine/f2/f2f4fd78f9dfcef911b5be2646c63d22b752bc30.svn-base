<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile HOME
 */

//Page Index Setting
$pageMenuNo_L = 1;
$pageMenuNo_M = 0;

//Init
include_once "../_init_.php";

$today = date('Y-m-d');
$today_han = date('Y년 m월 d일');

$week_han = date('Y년 m월 d일', strtotime("-6 day")) . " ~ " . date('Y년 m월 d일');
$month_han = date('Y년 m월');

$C_Home = new Home();

//금일
$date = date('Y-m-d');
$_today = $C_Home->getTodaySalesSummaryMobile("today");
$_week = $C_Home->getTodaySalesSummaryMobile("week");
$_month = $C_Home->getTodaySalesSummaryMobile("month");
?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_header.php"; ?>
	<div class="wrap_main main_inner">
		<div class="wrap_inner">
			<div class="table_set">
				<dl>
					<dt>일 판매현황 :</dt>
					<dd><?=$today_han?></dd>
				</dl>
				<table class="table_style01">
					<colgroup>
						<col width="28%" />
						<col width="72%" />
					</colgroup>
					<tr>
						<th class="text_left">매출 (원)</th>
						<td class="text_left"><?=number_format($_today["sales_amt"])?></td>
					</tr>
					<tr>
						<th class="text_left">매입 (원)</th>
						<td class="text_left"><?=number_format($_today["purchase_amt"])?></td>
					</tr>
					<tr>
						<th class="text_left">주문 (건)</th>
						<td class="text_left"><?=number_format($_today["order_cnt"])?></td>
					</tr>
					<tr>
						<th class="text_left">배송 (건)</th>
						<td class="text_left"><?=number_format($_today["shipped_cnt"])?></td>
					</tr>
				</table>
			</div>
			<div class="table_set">
				<dl>
					<dt>최근 7일 :</dt>
					<dd><?=$week_han?></dd>
				</dl>
				<table class="table_style01">
					<colgroup>
						<col width="28%" />
						<col width="72%" />
					</colgroup>
					<tr>
						<th class="text_left">매출 (원)</th>
						<td class="text_left"><?=number_format($_week["sales_amt"])?></td>
					</tr>
					<tr>
						<th class="text_left">매입 (원)</th>
						<td class="text_left"><?=number_format($_week["purchase_amt"])?></td>
					</tr>
					<tr>
						<th class="text_left">주문 (건)</th>
						<td class="text_left"><?=number_format($_week["order_cnt"])?></td>
					</tr>
					<tr>
						<th class="text_left">배송 (건)</th>
						<td class="text_left"><?=number_format($_week["shipped_cnt"])?></td>
					</tr>
				</table>
			</div>
			<div class="table_set">
				<dl>
					<dt>이번달 판매현황 :</dt>
					<dd><?=$month_han?></dd>
				</dl>
				<table class="table_style01">
					<colgroup>
						<col width="28%" />
						<col width="72%" />
					</colgroup>
					<tr>
						<th class="text_left">매출 (원)</th>
						<td class="text_left"><?=number_format($_month["sales_amt"])?></td>
					</tr>
					<tr>
						<th class="text_left">매입 (원)</th>
						<td class="text_left"><?=number_format($_month["purchase_amt"])?></td>
					</tr>
					<tr>
						<th class="text_left">주문 (건)</th>
						<td class="text_left"><?=number_format($_month["order_cnt"])?></td>
					</tr>
					<tr>
						<th class="text_left">배송 (건)</th>
						<td class="text_left"><?=number_format($_month["shipped_cnt"])?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
<?php include_once DY_INCLUDE_PATH . "/_include_footer.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>