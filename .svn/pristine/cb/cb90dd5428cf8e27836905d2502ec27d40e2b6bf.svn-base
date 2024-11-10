<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 일자별 재고조회 - 누적재고조회 페이지
 * TODO : 판매처 접속 시 벤더사 노출 상품만 노출 되도록 변경 필요!
 */
//Page Info
$pageMenuIdx = 113;
//Init
include_once "../_init_.php";

//상품 수정에서 이전 페이지로 넘어 왔을 경우 파라미터 세팅
$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;

$product_category_l_idx     = $_GET["product_category_l_idx"];
$product_category_m_idx     = $_GET["product_category_m_idx"];

$period_search_type         = $_GET["period_search_type"];
$search_date                 = $_GET["search_date"];

$stock_kind                 = $_GET["stock_kind"];
$stock_status_for_amount    = $_GET["stock_status_for_amount"];
$stock_amount_start         = $_GET["stock_amount_start"];
$stock_amount_end           = $_GET["stock_amount_end"];
$stock_status               = $_GET["stock_status"];
$stock_alert                = $_GET["stock_alert"];
$search_column              = $_GET["search_column"];
$search_keyword             = $_GET["search_keyword"];

if(!$search_date || !validateDate($search_date, 'Y-m-d')){
	$search_date = date('Y-m-d');
}
if(!$date_end || !validateDate($date_end, 'Y-m-d')){
	$date_end = date('Y-m-d');
}

//사용자 항목 설정 가져오기
$C_ColumnModel = new ColumnModel();
$userColumnList = $C_ColumnModel -> getUserColumn("PRODUCT_LIST", $GL_Member["member_idx"]);
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="wrap_tab_menu">
			<ul class="tab_menu">
				<li>
					<a href="stock_daily_list.php">입고량조회</a>
				</li>
				<li>
					<a href="stock_sum_list.php" class="on">누적재고조회</a>
				</li>
			</ul>
		</div>
		<form name="searchForm" id="searchForm" method="get">
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처">
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">날짜</span>
							<input type="text" name="search_date" id="search_date" class="w80px jqDate " value="<?=$search_date?>" readonly="readonly" />
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
								<option value="product_option_name" <?=($search_column == "product_option_name") ? "selected" : ""?>>옵션</option>
								<option value="product_name_option_name" <?=($search_column == "product_option_name") ? "selected" : ""?>>상품명+옵션</option>
								<option value="product_idx" <?=($search_column == "product_idx") ? "selected" : ""?>>상품코드</option>
								<option value="product_option_idx" <?=($search_column == "product_option_idx") ? "selected" : ""?>>상품옵션코드</option>
								<option value="product_supplier_name" <?=($search_column == "product_supplier_name") ? "selected" : ""?>>공급처 상품명</option>
								<option value="product_supplier_option" <?=($search_column == "product_supplier_option") ? "selected" : ""?>>공급처 옵션</option>
                                <option value="product_option_barcode_GTIN">바코드번호</option>
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
		<!--
		<p class="sub_tit">신규가입회원 <span class="red_strong">5</span>건 목록</p>
		<p class="sub_desc">총회원수 <span class="red_strong">1,255</span>명 중 차단 <span class="strong">0</span>명, 탈퇴 : <span class="strong">18</span>명</p>
		-->
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
<script src="/js/page/stock.product.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script>
	window.name = 'stock_sum_list';
	StockProduct.StockDailySUMListInit();
	//ManageGroup.getManageGroupList('SUPPIER_GROUP');
	Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

