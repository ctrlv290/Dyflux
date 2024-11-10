<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 입고지연 리스트 페이지
 * TODO : 판매처 접속 시 벤더사 노출 상품만 노출 되도록 변경 필요!
 */
//Page Info
$pageMenuIdx = 119;
//Init
include_once "../_init_.php";

$product_supplier_group_idx = "0";
$supplier_idx = "0";
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
							<span class="text">생성일</span>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
						<div class="finder_col">
							<span class="text">작업</span>
							<select name="stock_is_proc" class="stock_is_proc" data-selected="<?=$stock_is_proc?>">
								<option value="">전체</option>
								<option value="NA">미처리(추가입고)</option>
								<option value="N">미처리</option>
								<option value="Y">처리완료</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">상태</span>
							<select name="stock_status" class="stock_status" data-selected="<?=$stock_status?>">
								<option value="">전체</option>
								<option value="STOCK_ORDER_READY">발주</option>
								<option value="NORMAL">정상</option>
								<option value="ABNORMAL">양품</option>
								<option value="BAD">불량</option>
								<option value="SHORTAGE">부족</option>
								<option value="EXCHANGE">교환</option>
								<option value="FACTORY_SHIPPING">출고지배송</option>
							</select>
						</div>
					</div>
					<div class="finder_set">

						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처" multiple>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">카테고리</span>
							<select name="product_category_l_idx" class="product_category_l_idx" data-selected="<?=$product_category_l_idx?>">
								<option value="">전체</option>
							</select>
							<select name="product_category_m_idx" class="product_category_m_idx" data-selected="<?=$product_category_m_idx?>">
								<option value="">카테고리 전체</option>
							</select>
						</div>
						<div class="finder_col">
							<select name="search_column">
								<option value="product_name" <?=($search_column == "product_name") ? "selected" : ""?>>상품명</option>
								<option value="product_option_name" <?=($search_column == "product_option_name") ? "selected" : ""?>>상품옵션명</option>
								<option value="A.stock_order_idx" <?=($search_column == "A.stock_order_idx") ? "selected" : ""?>>발주코드</option>
							</select>
							<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" value="<?=$search_keyword?>" />
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
		<div class="grid_btn_set_top">
			<span>&nbsp;</span>
			<div class="right">
				<a href="javascript:;" class="btn btn-log-for-delay">입고지연이력</a>
				<a href="javascript:;" class="btn green_btn btn-stock-delay-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>


		<div id="modal_stock_order_email" title="이메일 발송" class="blue_theme" style="display: none;"></div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/common.function.js"></script>
<script src="/js/page/stock.due.js"></script>
<script>
	window.name = 'stock_delay_list';
	Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");
	StockDue.StockDelayListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

