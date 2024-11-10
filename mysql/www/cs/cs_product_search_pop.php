<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 창에서 주문 생성 시 상품 검색 팝업
 */
//Page Info
$pageMenuIdx = 207;
//Init
include_once "../_init_.php";
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="searchFormPop" id="searchFormPop" method="get">
				<div class="find_wrap">
					<div class="finder">
						<div class="finder_set">
							<div class="finder_col">
								<span class="text">상품명</span>
								<input type="text" name="product_name" class="w100px enterDoSearchPop" placeholder="상품명" />
							</div>
							<div class="finder_col">
								<span class="text">옵션명</span>
								<input type="text" name="product_option_name" class="w100px enterDoSearchPop" placeholder="옵션명" />
							</div>
							<div class="finder_col">
								<span class="text">옵션코드</span>
								<input type="text" name="product_option_idx" class="w100px enterDoSearchPop" placeholder="옵션코드" />
							</div>
						</div>
					</div>
					<div class="find_btn">
						<div class="table">
							<div class="table_cell">
								<a href="javascript:;" id="btn_searchBar_pop" class="wide_btn btn_default">검색</a>
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
			<div class="tb_wrap grid_tb">
				<table id="grid_list_pop">
				</table>
				<div id="grid_pager_pop"></div>
			</div>

			<div class="btn_set">
				<div class="center">
					<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/cs.cs.js"></script>
<script>
	window.name = "cs_product_search_pop";
	CSPopup.CSPopupOrderWriteProductAddPopupInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
