<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주 매칭 화면 페이지
 * TODO : 판매처 접속 시 매칭 화면에서 벤더사 노출 상품만 검색 되도록 변경 필요!
 */
//Page Info
$pageMenuIdx = 73;
//Init
include_once "../_init_.php";


//오늘
$now_date = date('Y-m-d');
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<style>
	#grid_pager_left {text-align: left;}
	#grid_pager_left select {padding: 0;vertical-align: bottom;}
	.sumo_seller_idx {vertical-align: bottom;}
</style>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="step_wrap">
			<a href="order_list.php" class="large_btn">이전 <i class="fas fa-caret-left"></i></a>
			<a href="javascript:;" class="large_btn btn-next-package">다음 <i class="fas fa-caret-right"></i></a>
			<div class="arrow-steps clearfix">
				<div class="step"><span>발주</span></div>
				<div class="step current"><span>매칭</span></div>
				<div class="step"><span>합포</span></div>
				<div class="step"><span>발주완료</span></div>
			</div>
		</div>
		<form name="searchForm" id="searchForm" method="get">
			<div class="find_wrap">
			<div class="finder">
				<div class="finder_set">
					<div class="finder_col">
						<select name="search_column">
							<option value="market_product_name_no">상품명+옵션명</option>
							<option value="order_idx">관리번호</option>
							<option value="market_product_no">상품코드</option>
							<option value="market_product_name">상품명</option>
							<option value="market_product_option">옵션명</option>
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
		<!--
		<p class="sub_tit">신규가입회원 <span class="red_strong">5</span>건 목록</p>
		<p class="sub_desc">총회원수 <span class="red_strong">1,255</span>명 중 차단 <span class="strong">0</span>명, 탈퇴 : <span class="strong">18</span>명</p>
		-->
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="btn btn-matching-list-reload">전체보기</a>
			<div class="right">
				<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="0">
					<option value="0">전체그룹</option>
				</select>
				<select name="seller_idx" class="seller_idx" id="pop_seller_idx" data-selected="" data-default-value="" data-default-text="전체 판매처">
				</select>
				<a href="javascript:;" class="btn btn-matching-confirm-pop">매칭 내역 확인</a>
				<a href="javascript:;" class="btn green_btn btn-matching-confirm-xls-down">매칭 내역 다운로드</a>
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>


		<div id="modal_order_matching_pop" title="상품매칭" class="red_theme" style="display: none;"></div>
		<div id="modal_matching_list" title="매칭 내역 확인" class="red_theme" style="display: none;"></div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script>
	jqgridDefaultSetting = false;
</script>
<script src="/js/main.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/order.order.js?v=190722"></script>
<script>
	window.name = 'order_matching';
	$(function(){
		Order.OrderMatchingInit();
	});
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

