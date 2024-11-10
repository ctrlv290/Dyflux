<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 자산현황 페이지
 */
//Page Info
$pageMenuIdx = 257;
//Init
include_once "../_init_.php";

$date = date('Y-m-d');
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
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="0">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="" data-default-value="" data-default-text="전체 공급처" multiple>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">입고확정일</span>
							<input type="text" name="date" class="w80px jqDate " value="<?=$date?>" readonly="readonly" />
						</div>

						<div class="finder_col">
							<span class="text">카테고리</span>
							<select name="P.product_category_l_idx" class="product_category_l_idx" data-selected="<?=$product_category_l_idx?>">
								<option value="">전체</option>
							</select>
							<select name="P.product_category_m_idx" class="product_category_m_idx" data-selected="<?=$product_category_m_idx?>">
								<option value="">카테고리 전체</option>
							</select>
						</div>

						<div class="finder_col">
							<select name="search_column">
								<option value="P.product_name">상품명</option>
								<option value="PO.product_option_name">옵션명</option>
							</select>
							<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" />
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
		<p class="sub_desc">
			자산금액 : <span class="strong stock_assets_price">0</span>원,
			재고수량 : <span class="strong stock_assets_amount">0</span>,
			정상 : <span class="strong stock_amount_NORMAL">0</span>,
			보류 : <span class="strong stock_amount_HOLD">0</span>,
			양품 : <span class="strong stock_amount_ABNORMAL">0</span>,
			불량 : <span class="strong stock_amount_BAD">0</span>,
			일반폐기 : <span class="strong stock_amount_DISPOSAL">0</span>
		</p>
		<div class="grid_btn_set_top">
			<span>&nbsp;</span>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/page/common.function.js?v=190807"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/settle.assets.js"></script>
<script>
	window.name = 'assets_state';
	SettleAssets.AssetsStateInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

