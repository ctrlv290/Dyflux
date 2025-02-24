<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 월별판매처통계 페이지
 */
//Page Info
$pageMenuIdx = 128;
//Init
include_once "../_init_.php";

$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"];
$date_year                  = $_GET["date_year"];
$monthly_type              = $_GET["monthly_type"];

$period_type                = "order_accept";

$C_Settle = new Settle();
if($date_year){
	$date_year_prev = (int) $date_year - 1;
	if($monthly_type == "settle_sale_supply"){
		$_list = $C_Settle->getSellerMonthlyStatistics($date_year, $seller_idx);
	}else {

		$_list = $C_Settle->getSellerMonthlyOrderCntStatistics($date_year, $seller_idx);
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
							<select name="date_year">
								<?php
								for($i = 2017;$i<=date('Y');$i++){
									if($date_year){
										$sel_year = $date_year;
									}else{
										$sel_year = date('Y');
									}
									$seleted = ($sel_year == $i) ? "selected" : "";

									echo '<option value="'.$i.'" '.$seleted.'>'.$i.'년</option>';
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
						<div class="finder_col">
							<span class="text">기준</span>
							<select name="monthly_type">
								<option value="settle_sale_supply" <?=($monthly_type == "settle_sale_supply") ? "selected" : ""?>>매출합계</option>
								<option value="order_count" <?=($monthly_type == "order_count") ? "selected" : ""?>>주문수량</option>
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
		<?php if($_list){?>
		<div class="btn_set">
			<span>&nbsp;</span>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			</div>
		</div>
		<?php }?>
		<div class="tb_wrap ">
			<table class="">
				<colgroup>
					<col width="160">
					<col width="80">
				</colgroup>
				<thead>
				<tr>
					<th>판매처</th>
					<th>년도</th>
					<th>합계</th>
					<?php
					for($i=1;$i<13;$i++){
						echo '<th>'.$i.'월</th>';
					}
					?>
				</tr>
				</thead>
				<tbody>
				<?php
				if($_list) {
					for ($j = 0; $j < 13; $j++) {
						$this_month_col = "sum_" . $date_year . "_" . $j; //$sum_2019_1 = 0;
						$prev_month_col = "sum_" . $date_year_prev . "_" . $j;

						$$this_month_col = 0;
						$$prev_month_col = 0;
					}
					foreach ($_list as $row) {
						$sum_this_year = 0;
						$sum_prev_year = 0;

						for ($j = 0; $j < 13; $j++) {

							$a = (int)$row[$date_year . "-" . $j];
							$b = (int)$row[$date_year_prev . "-" . $j];

							$this_month_col = "sum_" . $date_year . "_" . $j;
							$prev_month_col = "sum_" . $date_year_prev . "_" . $j;

							$$this_month_col += $a;
							$$prev_month_col += $b;
						}
					}
				?>
				<tr>
					<td rowspan="2">합계</td>
					<td><?= $date_year ?></td>
					<?php
					for ($j = 0; $j < 13; $j++) {
						$col = "sum_" . $date_year . "_" . $j;
						?>
						<td class="text_right"><?= number_format($$col) ?></td>
						<?php
					}
					?>
				</tr>
				<tr>
					<td><?=$date_year_prev?></td>
					<?php
					for($j=0;$j<13;$j++){
						$col = "sum_".$date_year_prev."_".$j;
						?>
						<td class="text_right"><?=number_format($$col)?></td>
						<?php
					}
					?>
				</tr>
				<?php
				}
				?>
				<?php
				foreach($_list as $row)
				{
					$seller_idx = $row["seller_idx"];
					$seller_name = $row["seller_name"];
					$sum_settle_sale_supply = $row["sum_settle_sale_supply"];
				?>
					<tr>
						<td rowspan="2"><?=$seller_name?></td>
						<td><?=$date_year?></td>
						<?php
						for($j=0;$j<13;$j++){
							$date_start = date('Y-m-d', strtotime($date_year . "-" . make2digit($j) . "-01"));
							$date_end = date('Y-m-d', strtotime($date_year . "-" . make2digit($j+1) . "-00"));
							if($j == 0) {
								$val = number_format($row[$date_year . "-" . $j]);
							}else{
								$val = '<a href="/settle/product_daily.php?seller_idx=' . $seller_idx . '&date_start=' . $date_start . '&date_end=' . $date_end . '" class="link" target="_blank">' . number_format($row[$date_year . "-" . $j]) . '</a>';
							}
						?>
							<td class="text_right"><?=$val?></td>
						<?php
						}
						?>
					</tr>
					<tr>
						<td><?=$date_year_prev?></td>
						<?php
						for($j=0;$j<13;$j++){
							$date_start = date('Y-m-d', strtotime($date_year_prev . "-" . make2digit($j) . "-01"));
							$date_end = date('Y-m-d', strtotime($date_year_prev . "-" . make2digit($j+1) . "-00"));
							if($j == 0) {
								$val = number_format($row[$date_year_prev . "-" . $j]);
							}else{
								$val = '<a href="/settle/product_daily.php?seller_idx=' . $seller_idx . '&date_start=' . $date_start . '&date_end=' . $date_end . '" class="link" target="_blank">' . number_format($row[$date_year_prev . "-" . $j]) . '</a>';
							}
						?>
							<td class="text_right"><?=$val?></td>
						<?php
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
<script src="/js/page/settle.manage.js?v=200410"></script>
<script>
	window.name = 'settle_today_summary';
	SettleManage.SellerMonthlyInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

