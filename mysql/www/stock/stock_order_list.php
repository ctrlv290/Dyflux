<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주관리 리스트 페이지
 * TODO : 판매처 접속 시 벤더사 노출 상품만 노출 되도록 변경 필요!
 */
//Page Info
$pageMenuIdx = 116;
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
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처" multiple>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">기간</span>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">작업</span>
							<select name="member_idx" class="member_idx" data-selected="<?=$member_idx?>">
								<option value="">전체</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">상태</span>
							<select name="member_idx" class="member_idx" data-selected="<?=$member_idx?>">
								<option value="">전체</option>
							</select>
						</div>
						<div class="finder_col">
							<select name="search_column">
								<option value="stock_order_idx" <?=($search_column == "stock_order_idx") ? "selected" : ""?>>발주코드</option>
								<option value="stock_order_officer_name" <?=($search_column == "stock_order_officer_name") ? "selected" : ""?>>발주사 담당자</option>
								<option value="stock_order_officer_tel" <?=($search_column == "stock_order_officer_tel") ? "selected" : ""?>>발주사 연락처</option>
								<option value="stock_order_supplier_name" <?=($search_column == "stock_order_supplier_name") ? "selected" : ""?>>공급처 담당자</option>
								<option value="stock_order_supplier_tel" <?=($search_column == "stock_order_supplier_tel") ? "selected" : ""?>>공급처 연락처</option>
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
			<a href="javascript:;" class="btn btn-stock-order-write-pop">신규발주</a>
			<div class="right">
				<a href="javascript:;" class="btn btn-log-for-file-create-pop">파일생성 이력</a>
				<a href="javascript:;" class="btn btn-log-for-download-pop">다운로드 이력</a>
				<a href="javascript:;" class="btn btn-log-for-email-send-pop">이메일발송 이력</a>
				<a href="javascript:;" class="btn green_btn btn-stock_order-xls-down">다운로드</a>
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
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/common.function.js"></script>
<script src="/js/page/stock.order.js"></script>
<script>
	window.name = 'stock_order';
	StockOrder.StockOrderListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

