<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 당일판매요약표 페이지
 */
//Page Info
$pageMenuIdx = 126;
//Init
include_once "../_init_.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"] || 0;

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
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
						<div class="finder_col">
							<label><input type="checkbox" name="chk_except_cancel_order" value="Y" /> 취소제외</label>
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
								<option value="product_name">상품명</option>
								<option value="product_option_name">옵션명</option>
							</select>
							<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" />
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
		<div class="tb_wrap ">
			<table class="no_border max1200">
				<colgroup>
					<col width="39%" />
					<col width="20" />
					<col width="*" />
				</colgroup>
				<tbody>
				<tr>
					<td class="text_left vtop">
						<div class="tb_wrap">
							<p class="sub_tit2">판매처</p>
							<table class="summary-seller">
								<colgroup>
									<col width="25%">
									<col width="25%">
									<col width="25%">
									<col width="25%">
								</colgroup>
								<thead>
								<tr>
									<th>판매처</th>
									<th>매출합계</th>
									<th>정산예정</th>
									<th>주문수량</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="tb_wrap">
							<p class="sub_tit2">카테고리</p>
							<table class="summary-category">
								<colgroup>
									<col width="30%">
									<col width="20%">
									<col width="25%">
									<col width="25%">
								</colgroup>
								<thead>
								<tr>
									<th>카테고리</th>
									<th>수량</th>
									<th>매출합계</th>
									<th>매입합계</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="tb_wrap">
							<p class="sub_tit2">주문</p>
							<table class="summary-order-count">
								<colgroup>
									<col width="70%">
									<col width="30%">
								</colgroup>
								<thead>
								<tr>
									<th>항목</th>
									<th>개수</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="tb_wrap">
							<p class="sub_tit2">송장</p>
							<table class="summary-order-invoice-count">
								<colgroup>
									<col width="70%">
									<col width="30%">
								</colgroup>
								<thead>
								<tr>
									<th>항목</th>
									<th>개수</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="tb_wrap">
							<p class="sub_tit2">배송</p>
							<table class="summary-order-shipped-count">
								<colgroup>
									<col width="70%">
									<col width="30%">
								</colgroup>
								<thead>
								<tr>
									<th>항목</th>
									<th>개수</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="tb_wrap">
							<p class="sub_tit2">반품</p>
							<table class="summary-order-return-count">
								<colgroup>
									<col width="70%">
									<col width="30%">
								</colgroup>
								<thead>
								<tr>
									<th>항목</th>
									<th>개수</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
						<div class="tb_wrap">
							<p class="sub_tit2">CS이력</p>
							<table class="summary-order-cs-count">
								<colgroup>
									<col width="33%">
									<col width="33%">
									<col width="34%">
								</colgroup>
								<thead>
								<tr>
									<th colspan="2">항목</th>
									<th>개수</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
					<td></td>
					<td class="text_left vtop">
						<div class="tb_wrap">
							<p class="sub_tit2">상품별</p>
							<div class="tb_wrap grid_tb">
								<table id="grid_list"></table>
								<div id="grid_pager"></div>
							</div>
						</div>
					</td>
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
<script src="/js/page/settle.manage.js?v=200410"></script>
<script>
	window.name = 'settle_today_summary';
	SettleManage.TodaySummaryInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

