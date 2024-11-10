<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처별통계 페이지
 */
//Page Info
$pageMenuIdx = 127;
//Init
include_once "../_init_.php";

$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$search_column              = $_GET["search_column"];
$search_keyword             = $_GET["search_keyword"];
$order_by                   = $_GET["order_by"];
$period_type                = "order_accept";

$C_Settle = new Settle();
if($date_start && $date_end){
	$_list = $C_Settle -> getSellerSaleStatistics($period_type, $date_start, $date_end, $product_seller_group_idx, $seller_idx, $search_column, $search_keyword, $order_by);
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
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
						<div class="finder_col">
							<span class="text">정렬방법</span>
							<select name="order_by" class="order_by">
								<option value="S.seller_name asc" <?=($order_by == "S.seller_name asc") ? "selected" : "" ?>>판매처 ▲</option>
								<option value="S.seller_name desc" <?=($order_by == "S.seller_name desc") ? "selected" : "" ?>>판매처 ▼</option>
								<option value="sum_settle_sale_supply asc" <?=($order_by == "sum_settle_sale_supply asc") ? "selected" : "" ?>>판매금액 ▲</option>
								<option value="sum_settle_sale_supply desc" <?=($order_by == "sum_settle_sale_supply desc") ? "selected" : "" ?>>판매금액 ▼</option>
								<option value="sum_settle_sale_supply_cancel asc" <?=($order_by == "sum_settle_sale_supply_cancel asc") ? "selected" : "" ?>>취소금액 ▲</option>
								<option value="sum_settle_sale_supply_cancel desc" <?=($order_by == "sum_settle_sale_supply_cancel desc") ? "selected" : "" ?>>취소금액 ▼</option>
								<option value="(sum_settle_sale_supply - sum_settle_sale_supply_cancel) asc" <?=($order_by == "(sum_settle_sale_supply - sum_settle_sale_supply_cancel) asc") ? "selected" : "" ?>>실매출금액 ▲</option>
								<option value="(sum_settle_sale_supply - sum_settle_sale_supply_cancel) desc" <?=($order_by == "(sum_settle_sale_supply - sum_settle_sale_supply_cancel) desc") ? "selected" : "" ?>>실매출금액 ▼</option>
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
						<div class="finder_col">
							<select name="search_column">
								<option value="product_name" <?=($search_keyword == "product_name") ? "selected" : ""?>>상품명</option>
								<option value="product_option_name" <?=($search_keyword == "product_option_name") ? "selected" : ""?>>옵션명</option>
							</select>
							<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" value="<?=$search_keyword?>" />
						</div>
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
		<p class="sub_desc">
			판매금액 : <span class="strong total_settle_sale_supply"></span>원, 취소금액 : <span class="strong total_settle_sale_supply_cancel"></span>원, 실매출금액 : <span class="strong total_settle_profit"></span>원
		</p>
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
					<col width="230">
				</colgroup>
				<thead>
				<tr>
					<th rowspan="2">판매처</th>
					<th colspan="7">수량</th>
					<th colspan="3">판매가 기준</th>
				</tr>
				<tr>
					<th>주문<br>수량</th>
					<th>상품<br>수량</th>
					<th>취소<br>주문</th>
					<th>취소상품<br>수량</th>
					<th>교환상품<br>수량</th>
					<th>주문 - 취소주문</th>
					<th>상품 - 취소수량</th>
					<th>판매<br>금액</th>
					<th>취소<br>금액</th>
					<th>실매출<br>금액</th>
				</tr>
				</thead>
				<tbody>
				<?php
				$total_settle_sale_supply = 0;
				$total_settle_sale_supply_cancel = 0;
				foreach($_list as $row)
				{
					$seller_idx = $row["seller_idx"];
					$seller_name = $row["seller_name"];
					$order_count = $row["order_count"];
					$sum_product_option_cnt = $row["sum_product_option_cnt"];
					$order_cancel_count = $row["order_cancel_count"];
					$sum_cancel_product_cnt = $row["sum_cancel_product_cnt"];
					$sum_cancel_change_cnt = $row["sum_cancel_change_cnt"];
					$sum_settle_sale_supply = $row["sum_settle_sale_supply"];
					$sum_settle_sale_supply_cancel = $row["sum_settle_sale_supply_cancel"];

					$total_settle_sale_supply += $sum_settle_sale_supply;
					$total_settle_sale_supply_cancel += $sum_settle_sale_supply_cancel;
				?>
				<tr>
					<td><a href="/settle/transaction_list.php?seller_idx=<?=$seller_idx?>&date_start=<?=$date_start?>&date_end=<?=$date_end?>" class="link" target="_blank"><?=$seller_name?></a></td>
					<td class="text_right"><?=number_format($order_count)?></td>
					<td class="text_right"><?=number_format($sum_product_option_cnt)?></td>
					<td class="text_right"><?=number_format($order_cancel_count)?></td>
					<td class="text_right"><?=number_format($sum_cancel_product_cnt)?></td>
					<td class="text_right"><?=number_format($sum_cancel_change_cnt)?></td>
					<td class="text_right"><?=number_format($order_count - $order_cancel_count)?></td>
					<td class="text_right"><?=number_format($sum_product_option_cnt - $sum_cancel_product_cnt)?></td>
					<td class="text_right"><?=number_format($sum_settle_sale_supply)?></td>
					<td class="text_right"><?=number_format($sum_settle_sale_supply_cancel)?></td>
					<td class="text_right"><?=number_format($sum_settle_sale_supply - $sum_settle_sale_supply_cancel)?></td>
				</tr>
				<?php
				}
				?>
				<tr>
					<th colspan="8">합계</th>
					<th class="text_right"><?=number_format($total_settle_sale_supply)?></th>
					<th class="text_right"><?=number_format($total_settle_sale_supply_cancel)?></th>
					<th class="text_right"><?=number_format($total_settle_sale_supply - $total_settle_sale_supply_cancel)?></th>
				</tr>
				</tbody>
			</table>
			<script>
				$(".total_settle_sale_supply").text("<?=number_format($total_settle_sale_supply)?>");
				$(".total_settle_sale_supply_cancel").text("<?=number_format($total_settle_sale_supply_cancel)?>");
				$(".total_settle_profit").text("<?=number_format($total_settle_sale_supply - $total_settle_sale_supply_cancel)?>");
			</script>
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
<script src="/js/page/settle.manage.js"></script>
<script>
	window.name = 'settle_today_summary';
	SettleManage.SellerSaleInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

