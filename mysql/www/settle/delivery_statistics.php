<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 미출고요약표 리스트 JSON
 */
//Page Info
$pageMenuIdx = 144;
//Init
include_once "../_init_.php";

$C_Delivery = new Delivery();
$_delivery_list = $C_Delivery->getDeliveryCodeList();

$period_type                = $_GET["period_type"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$product_seller_group_idx   = (isset($_GET["product_seller_group_idx"])) ? $_GET["product_seller_group_idx"] : "0";
$seller_idx                 = $_GET["seller_idx"];
$product_supplier_group_idx = (isset($_GET["product_supplier_group_idx"])) ? $_GET["product_supplier_group_idx"] : "0";
$supplier_idx               = $_GET["supplier_idx"];
$delivery_code              = (isset($_GET["product_supplier_group_idx"])) ? $_GET["delivery_code"] : "CJGLS";

//벤더사 로그인일 경우 공급처 검색 불가
if(!isDYLogin()){
	$supplier_idx = "";
}

$C_Settle = new Settle();

if($date_start && $date_end){
	$_list = $C_Settle->getDeliveryStatistics($period_type, $date_start, $date_end, $delivery_code, $seller_idx, $supplier_idx);
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
							<select name="period_type">
								<option value="order_accept_regdate" <?=($period_type == "order_accept_regdate") ? "selected" : ""?>>접수일</option>
								<option value="invoice_date" <?=($period_type == "invoice_date") ? "selected" : ""?>>송장일</option>
								<option value="shipping_date" <?=($period_type == "shipping_date") ? "selected" : ""?>>배송일</option>
								<!--								<option value="cancel_date">취소일</option>-->
							</select>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
						<div class="finder_col">
							<span class="text">택배사</span>
							<select name="delivery_code">
								<?php
								foreach ($_delivery_list as $d){
									$selected = ($delivery_code == $d["delivery_code"]) ? "selected" : "";
									echo '<option value="'.$d["delivery_code"].'" '.$selected.'>'.$d["delivery_name"].'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처">
							</select>
						</div>
						<?php
						//벤더사 로그인일 경우 공급처 검색 불가
						if(isDYLogin()){
						?>
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처">
							</select>
						</div>
						<?php }?>
					</div>
				</div>
				<div class="find_btn">
					<div class="table">
						<div class="table_cell">
							<a href="javascript:;" id="btn_searchBar" class="big_btn btn_default">검색</a>
						</div>
					</div>
				</div>
				<a href="javascript:;" class="find_hide_btn">
					<i class="fas fa-angle-up up_btn"></i>
					<i class="fas fa-angle-down dw_btn"></i>
				</a>
			</div>
		</form>
		<?php if(isDYLogin()){?>
		<div class="btn_set">
			<p class="sub_tit2">&nbsp;</p>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			</div>
		</div>
		<?php } ?>
		<div class="tb_wrap">
			<table>
				<thead>
				<tr>
					<th>일자</th>
					<th>선불</th>
					<th>착불</th>
					<th>합포</th>
					<th>개별</th>
					<th>합계</th>
				</tr>
				</thead>
				<tbody>
				<?php

				$prepay_sum = 0;
				$afterpay_sum = 0;
				$pack_sum = 0;
				$single_sum = 0;

				foreach ($_list as $row) {
					$date = $row["date"];
					$prepay_cnt = $row["prepay_cnt"];
					$afterpay_cnt = $row["afterpay_cnt"];
					$pack_cnt = $row["pack_cnt"];
					$single_cnt = $row["single_cnt"];

					$row_sum = $pack_cnt + $single_cnt;

					$prepay_sum   += $prepay_cnt;
					$afterpay_sum += $afterpay_cnt;
					$pack_sum     += $pack_cnt;
					$single_sum   += $single_cnt;
				?>
				<tr>
					<td><?=$date?></td>
					<td><?=number_format($prepay_cnt)?></td>
					<td><?=number_format($afterpay_cnt)?></td>
					<td><?=number_format($pack_cnt)?></td>
					<td><?=number_format($single_cnt)?></td>
					<td><?=number_format($row_sum)?></td>
				</tr>
				<?php
				}
				?>
				<tr>
					<th>합계</th>
					<th><?=number_format($prepay_sum)?></th>
					<th><?=number_format($afterpay_sum)?></th>
					<th><?=number_format($pack_sum)?></th>
					<th><?=number_format($single_sum)?></th>
					<th><?=number_format($pack_sum+$single_sum)?></th>
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
<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.delivery.js"></script>
<script>
	window.name = 'delivery_statistics';
	SettleDelivery.DeliveryStatisticsInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

