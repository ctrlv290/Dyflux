<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 신규 발주 시 상품 추가 팝업 페이지
 * TODO : 판매처 접속 시 벤더사 노출 상품만 노출 되도록 변경 필요!
 */

//Page Info
$pageMenuIdx = 214;
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
				<input type="hidden" name="supplier_idx" value="<?=$supplier_idx?>" />
				<div class="find_wrap">
					<div class="finder">
						<div class="finder">
							<div class="finder_set">
								<div class="finder_col">
									<span class="text">공급처</span>
									<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="0">
										<option value="0">전체그룹</option>
									</select>
									<select name="supplier_idx" class="supplier_idx" data-selected="0" data-default-value="" data-default-text="전체 공급처">
									</select>
								</div>
								<div class="finder_col">
									<span class="text">상품명</span>
									<input type="text" name="product_name" class="w120px enterDoSearchPop" />
									<select name="search_column">
										<option value="product_option_name">옵션</option>
									</select>
									<input type="text" name="search_keyword" class="w150px enterDoSearchPop" placeholder="검색어" />
									<a href="javascript:;" id="btn_searchBar_pop" class="btn blue_btn btn_default">검색</a>
								</div>
							</div>
						</div>
					</div>
					<div class="find_btn empty">
						<div class="table">
							<div class="table_cell">
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
				<table id="grid_list_pop" style="width: 100%;">
				</table>
				<div id="grid_pager_pop"></div>
			</div>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" class="large_btn blue_btn btn-stock-order-add-product-exec">추가</a>
					<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/product.product.commission.js"></script>
<script>
	window.name = "product_commission_product_search_pop";
	ProductCommission.ProductCommissionAddOptionInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
