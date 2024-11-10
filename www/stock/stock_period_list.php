<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 기간별 재고조회 페이지
 * TODO : 판매처 접속 시 벤더사 노출 상품만 노출 되도록 변경 필요!
 */
//Page Info
$pageMenuIdx = 112;
//Init
include_once "../_init_.php";

//상품 수정에서 이전 페이지로 넘어 왔을 경우 파라미터 세팅
$period_search_type         = $_GET["period_search_type"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$stock_status               = $_GET["stock_status"];
$sale_status                = $_GET["sale_status"];
$product_category_l_idx     = $_GET["product_category_l_idx"];
$product_category_m_idx     = $_GET["product_category_m_idx"];
$product_sale_type          = $_GET["product_sale_type"];
$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$search_column              = $_GET["search_column"];
$search_keyword             = $_GET["search_keyword"];

$supplier_idx               = implode(",", $_GET["supplier_idx"]);
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
							<span class="text">카테고리</span>
							<select name="product_category_l_idx" class="product_category_l_idx" data-selected="<?=$product_category_l_idx?>">
								<option value="">전체</option>
							</select>
							<select name="product_category_m_idx" class="product_category_m_idx" data-selected="<?=$product_category_m_idx?>">
								<option value="">카테고리 전체</option>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">기간</span>
							<input type="text" name="date_start2" id="period_preset_start_input2" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end2" id="period_preset_end_input2" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset2" id="period_preset_select2"></select>
						</div>
						<div class="finder_col">
							<span class="text">상태</span>
							<select name="stock_status">
								<option value="">전체</option>
								<?php
								foreach($GL_StockStatusList as $key => $val){
									echo '<option value="'.$key.'">'.$val.'</option>';
								}
								?>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">작업</span>
							<select name="stock_kind">
								<option value="">전체</option>
								<option value="IN" <?=($stock_kind == "IN") ? "selected" : "" ?>>입고</option>
								<option value="OUT" <?=($stock_kind == "OUT") ? "selected" : "" ?>>출고</option>
								<option value="SHIPPED" <?=($stock_kind == "SHIPPED") ? "selected" : "" ?>>배송</option>
								<option value="RETURN" <?=($stock_kind == "RETURN") ? "selected" : "" ?>>반품</option>
								<option value="BAD_last" <?=($stock_kind == "BAD_last") ? "selected" : "" ?>>불량</option>
							</select>
							<input type="text" name="stock_kind_amount_start" class="w40px onlyNumber enterDoSearch" value="<?=($stock_kind != "") ? "1" : ""?>" maxlength="4" />
							~
							<input type="text" name="stock_kind_amount_end" class="w40px onlyNumber enterDoSearch" value="" maxlength="4" />
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<select name="stock_status_for_amount">
								<?php
								foreach($GL_StockStatusList as $key => $val){
									echo '<option value="'.$key.'">'.$val.'</option>';
								}
								?>
							</select>
							<input type="text" name="stock_amount_start" class="w40px onlyNumber enterDoSearch" value="" maxlength="4" />
							~
							<input type="text" name="stock_amount_end" class="w40px onlyNumber enterDoSearch" value="" maxlength="4" />
						</div>
						<div class="finder_col">
							<span class="text">재고상태</span>
							<select name="stock_alert">
								<option value="">전체</option>
								<option value="stock_warning" <?=($stock_status == "stock_warning") ? "selected" : ""?>>재고경고</option>
								<option value="stock_danger" <?=($stock_status == "stock_danger") ? "selected" : ""?>>재고위험</option>
								<option value="stock_warning_danger" <?=($stock_status == "stock_warning_danger") ? "selected" : ""?>>재고경고+위험</option>
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
			<a href="javascript:;" class="btn btn-column-setting-pop">항목설정</a>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-stock-period-list-xls-down">다운로드</a>
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
	//사용자 항목설정을 불러오기전에 초기화
	var _gridColModel = [];
	var user_column_list = [];
</script>
<script src="/js/column_const.js"></script>
<script src="/common/column_load_js.php?target=STOCK_PERIOD_LIST"></script>
<script>
	window.name = 'stock_period_list';
	StockProduct.StockPeriodListInit();
	//ManageGroup.getManageGroupList('SUPPIER_GROUP');
	Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

