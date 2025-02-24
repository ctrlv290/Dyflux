<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 월별상품별 페이지
 */
//Page Info
$pageMenuIdx = 133;
//Init
include_once "../_init_.php";

//상품 수정에서 이전 페이지로 넘어 왔을 경우 파라미터 세팅
$product_seller_group_idx = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = (isset($_GET["supplier_idx"]) && $_GET["supplier_idx"] !== "") ? $_GET["supplier_idx"] : "";
$seller_idx                 = (isset($_GET["seller_idx"]) && $_GET["seller_idx"] !== "") ? $_GET["seller_idx"] : "";

$product_category_l_idx     = $_GET["product_category_l_idx"];
$product_category_m_idx     = $_GET["product_category_m_idx"];

$period_search_type         = $_GET["period_search_type"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];

$search_column              = $_GET["search_column"];
$search_keyword             = $_GET["search_keyword"];

$soldout_status             = $_GET["soldout_status"];
$value_view                 = $_GET["value_view"];

if($value_view != "c" && $value_view != "p" && $value_view != "s"){
	$value_view = "c";
}

if(!$date_start || !validateDate($date_start, 'Y-m-d')){
	$date_start = date('Y-m-d');
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
		<form name="searchForm" id="searchForm" method="get">
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text" style="margin-right: 8px;">기 간</span>

							<select name="date_start_year" id="period_start_year_input">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == date('Y')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_start_month" id="period_start_month_input">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == date('m')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							~
							<select name="date_end_year" id="period_end_year_input">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == date('Y')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_end_month" id="period_end_month_input">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == date('m')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
						</div>
						<div class="finder_col">
							<select name="search_column">
								<option value="P.product_name" <?=($search_column == "product_name") ? "selected" : ""?>>상품명</option>
								<option value="PO.product_option_name" <?=($search_column == "product_option_name") ? "selected" : ""?>>옵션</option>
								<option value="product_name_option_name" <?=($search_column == "product_option_name") ? "selected" : ""?>>상품명+옵션</option>
								<option value="P.product_idx" <?=($search_column == "product_idx") ? "selected" : ""?>>상품코드</option>
								<option value="PO.product_option_idx" <?=($search_column == "product_option_idx") ? "selected" : ""?>>상품옵션코드</option>
								<option value="product_supplier_name" <?=($search_column == "product_supplier_name") ? "selected" : ""?>>공급처 상품명</option>
								<option value="product_supplier_option" <?=($search_column == "product_supplier_option") ? "selected" : ""?>>공급처 옵션</option>
							</select>
							<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" value="<?=$search_keyword?>" />
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx" id="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처">
								<?=($seller_idx) ? '<option value="'.$seller_idx.'"></option>' : ''?>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처">
								<?=($supplier_idx) ? '<option value="'.$supplier_idx.'"></option>' : ''?>
							</select>
						</div>
					</div>

					<div class="finder_set">
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
							<span class="text">품절</span>
							<select name="soldout_status">
								<option value="" <?=($soldout_status == "") ? "selected" : ""?>>전체</option>
								<option value="soldout" <?=($soldout_status == "soldout") ? "selected" : ""?>>품절</option>
								<option value="except_soldout" <?=($soldout_status == "except_soldout") ? "selected" : ""?>>품절제외</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">조회</span>
							<select name="value_view">
								<option value="c" <?=($value_view == "c") ? "selected" : ""?>>수량조회</option>
								<option value="p" <?=($value_view == "p") ? "selected" : ""?>>원가조회</option>
								<option value="s" <?=($value_view == "s") ? "selected" : ""?>>판매가조회</option>
							</select>
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
			판매총액 : <span class="strong product_total"></span>원
		</p>
		<div class="grid_btn_set_top">
			<span>&nbsp;</span>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-stock-list-xls-down">다운로드</a>
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
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.product.js?v=200410"></script>
<script>
	window.name = 'settle_product_daily';
	Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");
	SettleProduct.ProductMonthlyListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

