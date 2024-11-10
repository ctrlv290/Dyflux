<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 하부주문관리 페이지
 */
//Page Info
$pageMenuIdx = 81;
//Init
include_once "../_init_.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$product_seller_group_idx = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx               = $_GET["seller_idx"] || 0;

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
							<select name="period_type">
								<option value="order_regdate">발주일</option>
								<option value="order_accept_regdate">접수일</option>
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
							<span class="text">상태</span>
							<select name="order_progress_step">
								<option value="">전체</option>
								<option value="ORDER_COLLECT,ORDER_PRODUCT_MATCHING,ORDER_PACKING">발주</option>
								<option value="ORDER_ACCEPT">접수</option>
								<option value="ORDER_INVOICE">송장</option>
								<option value="ORDER_SHIPPED">배송</option>
								<option value="ORDER_ACCEPT,ORDER_INVOICE">접수+송장</option>
								<option value="ORDER_ACCEPT,ORDER_SHIPPED">접수+배송</option>
								<option value="ORDER_INVOICE,ORDER_SHIPPED">송장+배송</option>
							</select>
							<select name="order_matching_cs">
								<option value="">전체</option>
								<option value="NORMAL">정상</option>
								<option value="ORDER_CANCEL">취소(배송전+배송후)</option>
								<option value="PRODUCT_CHANGE">교환(배송전+배송후)</option>
								<option value="ORDER_CANCEL_N">배송전취소</option>
								<option value="ORDER_CANCEL_Y">배송후취소</option>
								<option value="PRODUCT_CHANGE_N">배송전교환</option>
								<option value="PRODUCT_CHANGE_Y">배송후교환</option>
								<option value="HOLD">보류</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">관리번호</span>
							<input type="text" name="A.order_idx" class="w100px enterDoSearch" placeholder="검색어" />
						</div>
						<div class="finder_col">
							<span class="text">주문번호</span>
							<input type="text" name="market_order_no" class="w100px enterDoSearch" placeholder="검색어" />
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">상품코드</span>
							<input type="text" name="M.product_idx" class="w100px enterDoSearch" placeholder="검색어" />
						</div>
						<div class="finder_col">
							<span class="text">옵션코드</span>
							<input type="text" name="M.product_option_idx" class="w100px enterDoSearch" placeholder="검색어" />
						</div>
						<div class="finder_col">
							<span class="text">상품명</span>
							<input type="text" name="product_name" class="w100px enterDoSearch" placeholder="검색어" />
						</div>
						<div class="finder_col">
							<span class="text">옵션</span>
							<input type="text" name="product_option_name" class="w100px enterDoSearch" placeholder="검색어" />
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">수령자</span>
							<input type="text" name="receive_name" class="w100px enterDoSearch" placeholder="검색어" />
						</div>
						<div class="finder_col">
							<span class="text">수령자핸드폰</span>
							<input type="text" name="receive_hp_num" class="w100px enterDoSearch" placeholder="검색어" />
						</div>
						<div class="finder_col">
							<span class="text">송장</span>
							<input type="text" name="invoice_no" class="w100px enterDoSearch" placeholder="검색어" />
						</div>
						<div class="finder_col">
							<span class="text">수령자주소</span>
							<input type="text" name="receive_addr" class="w100px enterDoSearch" placeholder="검색어" />
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
<!--		<p class="sub_desc">-->
<!--			배송개수 : <span class="strong summary_shipped_cnt">-</span>건 / 주문건수 : <span class="strong summary_order_cnt">-</span>건 / 상품개수 : <span class="strong summary_product_cnt">-</span>건-->
<!--			&nbsp;&nbsp;&nbsp;주문수량 : <span class="strong summary_shipped_cnt">-</span>개 / 상품수량 : <span class="strong summary_product_cnt">-</span>개-->
<!--			&nbsp;&nbsp;&nbsp;판매금액 : <span class="strong summary_order_amt_sum">-</span>원, 정산예정금액 : <span class="strong summary_order_calculation_amt_sum">-</span>원-->
<!--		</p>-->
		<div class="grid_btn_set_top">
			<span><br></span>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
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
<script src="/js/page/order.sub.js?v=190826"></script>
<script>
	window.name = 'sub_list';
	OrderSub.OrderSubInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

