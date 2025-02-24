<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 확장주문검색 페이지
 */
//Page Info
$pageMenuIdx = 77;
//Permission IDX
$pagePermissionIdx = 77;
//Init
include_once "../_init_.php";

/**
 * 권한 체크
 * 페이지 상단 _init_php 로그 하기전
 * $pagePermissionIdx 변수가 선언되어 있으면
 * 해당 메뉴 IDX 로 권한을 체크한다.
 */
include_once DY_INCLUDE_PATH . "/_include_check_permission.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"] || 0;
$order_progress_step        = $_GET["order_progress_step"];
$period_type                = $_GET["period_type"];
$order_cs_status            = $_GET["order_cs_status"];
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="get">
			<input type="hidden" name="include_sum" id="include_sum" value="" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<select name="period_type">
								<option value="order_regdate" <?=($period_type == "order_regdate") ? "selected" : ""?> >발주일</option>
								<option value="order_accept_regdate" <?=($period_type == "order_accept_regdate") ? "selected" : ""?>>접수일</option>
								<option value="invoice_date" <?=($period_type == "invoice_date") ? "selected" : ""?>>송장입력일시</option>
								<option value="shipping_date" <?=($period_type == "shipping_date") ? "selected" : ""?>>배송처리일시</option>
							</select>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							<input type="text" name="time_start" id="period_preset_start_time_input" class="w60px time_start " value="00:00:00" maxlength="8" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<input type="text" name="time_end" id="period_preset_end_time_input" class="w60px time_end " value="23:59:59" maxlength="8" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처" multiple>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">상태</span>
							<select name="order_progress_step">
								<option value="">전체</option>
								<option value="ORDER_COLLECT,ORDER_PRODUCT_MATCHING,ORDER_PACKING">발주</option>
								<option value="ORDER_ACCEPT_TEMP">가접수</option>
								<option value="ORDER_ACCEPT">접수</option>
								<option value="ORDER_INVOICE" <?=($order_progress_step == "ORDER_INVOICE") ? "selected" : ""?>>송장</option>
								<option value="ORDER_SHIPPED" <?=($order_progress_step == "ORDER_SHIPPED") ? "selected" : ""?>>배송</option>
								<option value="ORDER_ACCEPT,ORDER_INVOICE" <?=($order_progress_step == "ORDER_ACCEPT,ORDER_INVOICE") ? "selected" : ""?>>접수+송장</option>
								<option value="ORDER_ACCEPT,ORDER_SHIPPED">접수+배송</option>
								<option value="ORDER_INVOICE,ORDER_SHIPPED">송장+배송</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">C/S</span>
							<select name="order_cs_status">
								<option value="">전체</option>
								<option value="NORMAL" <?=($order_cs_status == "NORMAL") ? "selected" : ""?>>정상</option>
								<option value="EXCEPT_PART_CANCEL" <?=($order_cs_status == "EXCEPT_PART_CANCEL") ? "selected" : ""?>>부분취소제외</option>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx[]" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처" multiple>
							</select>
						</div>
						<div class="finder_col">
							<select name="search_column">
								<option value="A.order_idx">관리번호</option>
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
		<!--
		<p class="sub_tit">신규가입회원 <span class="red_strong">5</span>건 목록</p>
		<p class="sub_desc">총회원수 <span class="red_strong">1,255</span>명 중 차단 <span class="strong">0</span>명, 탈퇴 : <span class="strong">18</span>명</p>
		-->
		<p class="sub_desc">
			배송개수 : <span class="strong summary_shipped_cnt">-</span>건 / 주문건수 : <span class="strong summary_order_cnt">-</span>건 / 상품개수 : <span class="strong summary_product_cnt">-</span>건
<!--			&nbsp;&nbsp;&nbsp;주문수량 : <span class="strong summary_shipped_cnt">-</span>개 / 상품수량 : <span class="strong sum_product_option_cnt">-</span>개-->
			&nbsp;&nbsp;&nbsp;판매금액 : <span class="strong summary_order_amt_sum">-</span>원, 정산예정금액 : <span class="strong summary_order_calculation_amt_sum">-</span>원
		</p>
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="btn btn-column-setting-pop">항목설정</a>
			<!--<a href="javascript:;" class="btn btn-order-batch-proc">주문일괄처리</a>-->
			<div class="right">
				<label><input type="checkbox" name="include_sum" class="chk-include-sum" value="Y"/>합계포함 </label>
<!--				<select name="xls_down_type">-->
<!--					<option value="product">상품단위</option>-->
<!--					<option value="order">주문단위</option>-->
<!--					<option value="shipped">배송단위</option>-->
<!--					<option value="product_group">상품그룹</option>-->
<!--				</select>-->
				<a href="javascript:;" class="btn green_btn btn-order-search-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>


		<div id="modal_order_write_xls_pop" title="판매처 수동발주 업로드 팝업" class="red_theme" style="display: none;"></div>
		<div id="modal_order_format_seller_pop" title="발주서 포맷 사용자 정의" class="red_theme" style="display: none;"></div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script>
	//사용자 항목설정을 불러오기전에 초기화
	var _gridColModel = [];
	var user_column_list = [];
</script>
<script src="/js/column_const.js"></script>
<script src="/common/column_load_js.php?target=ORDER_SEARCH_LIST"></script>
<script src="/js/page/order.order.js?v=190619"></script>
<script>
	window.name = 'order_search_list';
	Order.OrderSearchListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

