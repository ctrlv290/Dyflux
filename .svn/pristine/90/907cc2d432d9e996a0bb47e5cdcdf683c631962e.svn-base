<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품목록 페이지
 * TODO : 판매처 접속 시 벤더사 노출 상품만 노출 되도록 변경 필요!
 */
//Page Info
$pageMenuIdx = 35;
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
$period_yn		            = $_GET["period_yn"] ? $_GET["period_yn"] : "N";

$supplier_idx               = implode(",", $_GET["supplier_idx"]);

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
			<input type="hidden" name="include_option" id="include_option" value="" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<select name="period_search_type" id="period_search_type_select" >
								<option value="regdate" <?=($period_search_type == "regdate") ? "selected" : ""?>>등록일</option>
								<option value="soldoutdate" <?=($period_search_type == "soldoutdate") ? "selected" : ""?>>품절일</option>
							</select>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select" ></select>
							<span><input type="checkbox" id="period_yn" name="period_yn" value="N"  onclick="onClickPeriodYn(this);"/> 전체 기간</span>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">재고상태</span>
							<select name="stock_status">
								<option value="">전체</option>
								<option value="in_stock" <?=($stock_status == "in_stock") ? "selected" : ""?>>판매가능</option>
								<option value="not_enough" <?=($stock_status == "not_enough") ? "selected" : ""?>>재고부족</option>
								<option value="warning" <?=($stock_status == "warning") ? "selected" : ""?>>재고경고</option>
								<option value="danger" <?=($stock_status == "danger") ? "selected" : ""?>>재고위협</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">판매상태</span>
							<select name="sale_status">
								<option value="">전체</option>
								<option value="soldout" <?=($sale_status == "soldout") ? "selected" : ""?>>품절(전체품절+부분품절)</option>
								<option value="soldout_part" <?=($sale_status == "soldout_part") ? "selected" : ""?>>부분품절</option>
								<option value="soldout_whole" <?=($sale_status == "soldout_whole") ? "selected" : ""?>>전체품절</option>
								<option value="in_stock" <?=($sale_status == "in_stock") ? "selected" : ""?>>판매가능</option>
								<option value="soldout_temp" <?=($sale_status == "soldout_temp") ? "selected" : ""?>>일시품절</option>
								<option value="x_sold_out_temp" <?=($sale_status == "x_sold_out_temp") ? "selected" : ""?>>일시품절제외</option>
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
						<?php if(isDYLogin()){?>
						<div class="finder_col">
							<span class="text">판매타입</span>
							<select name="product_sale_type" class="product_sale_type">
								<option value="">전체</option>
								<option value="SELF" <?=($product_sale_type == "SELF") ? "selected" : ""?>>사입/자체</option>
								<option value="CONSIGNMENT" <?=($product_sale_type == "CONSIGNMENT") ? "selected" : ""?>>위탁</option>
							</select>
						</div>
						<?php } ?>
					</div>
					<div class="finder_set">
						<?php if(isDYLogin()){?>
						<div class="finder_col">
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처" multiple>
							</select>
						</div>
						<?php } ?>
						<div class="finder_col">
							<select name="search_column">
								<option value="product_name" <?=($search_column == "product_name") ? "selected" : ""?>>상품명</option>
								<option value="product_option_name" <?=($search_column == "product_option_name") ? "selected" : ""?>>옵션명</option>
								<option value="product_option_idx" <?=($search_column == "product_option_idx") ? "selected" : ""?>>옵션코드</option>
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
		<?php if(isDYLogin()){?>
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="btn btn-column-setting-pop">항목설정</a>
			<div class="right">
				<a href="javascript:;" class="btn btn-change-log-viewer-pop">변경이력</a>
<!--				<a href="javascript:;" class="btn btn-change-log-viewer-pop">출력하기</a>-->
				<a href="javascript:;" class="btn green_btn btn-product-xls-down">다운로드</a>
				<div class="label_set">
					<label><input type="checkbox" class="chk-include-product-option" />상세옵션포함</label>
				</div>
			</div>
		</div>
		<?php }?>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/product.product.js?v=191216"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script>
	//사용자 항목설정을 불러오기전에 초기화
	var _gridColModel = [];
	var user_column_list = [];
</script>
<script src="/js/column_const.js"></script>
<script src="/common/column_load_js.php?target=PRODUCT_LIST"></script>
<script>
	window.name = 'product_list';

	function onClickPeriodYn(e) {
		togglePeriodUsed(! e.checked);
	}

	function togglePeriodUsed(yn) {
		$("#period_yn").prop("checked", !yn);

		$("#period_search_type_select").prop("disabled", !yn);
		$("#period_preset_start_input").prop("disabled", !yn);
		$("#period_preset_end_input").prop("disabled", !yn);
		$("#period_preset_select").prop("disabled", !yn);
	}

	var periodYn = false;
	<?php if ($period_yn == "Y") { ?>
	periodYn = true;
	<?php } ?>

	togglePeriodUsed(periodYn);

	<?php if(!isDYLogin()) {?>
	_gridColModel = columnModel.PRODUCT_LIST_VENDOR;
	<?php } ?>

	Product.ProductListInit();
	//ManageGroup.getManageGroupList('SUPPIER_GROUP');
	Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

