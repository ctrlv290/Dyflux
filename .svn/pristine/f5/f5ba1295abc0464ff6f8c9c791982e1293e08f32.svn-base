<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처상품별통계 페이지
 */
//Page Info
$pageMenuIdx = 131;
//Init
include_once "../_init_.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"];
$product_seller_group_idx = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx               = $_GET["seller_idx"];

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
							<span class="text">판매처 상품코드</span>
							<input type="text" name="market_product_no" class="w100px enterDoSearch" />
						</div>
						<div class="finder_col">
							<span class="text">판매처 상품명</span>
							<input type="text" name="market_product_name" class="w100px enterDoSearch" />
						</div>
						<div class="finder_col">
							<span class="text">판매처 옵션</span>
							<input type="text" name="market_product_option" class="w100px enterDoSearch" />
						</div>
						<div class="finder_col">
							<span class="text">판매처 상품명+옵션</span>
							<input type="text" name="market_product_all" class="w100px enterDoSearch" />
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<select name="period_type">
								<option value="accept_date">발주일</option>
								<option value="invoice_date">송장일</option>
								<option value="shipping_date">배송일</option>
								<option value="cancel_date">취소일</option>
							</select>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
						<div class="finder_col">
							<span class="text">상태</span>
							<select name="order_progress_step">
								<option value="">전체</option>
								<option value="ORDER_ACCEPT">접수</option>
								<option value="ORDER_INVOICE">송장</option>
								<option value="ORDER_SHIPPED">배송</option>
								<option value="ORDER_ACCEPT,ORDER_INOVICE">접수+송장</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">C/S</span>
							<select name="order_matching_cs">
								<option value="">전체</option>
								<option value="NORMAL">정상</option>
								<option value="ORDER_CANCEL_N">배송전취소</option>
								<option value="ORDER_CANCEL_Y">배송후취소</option>
								<option value="PRODUCT_CHANGE_N">배송전교환</option>
								<option value="PRODUCT_CHANGE_Y">배송후교환</option>
								<option value="ORDER_CANCEL">취소(배송전+배송후)</option>
								<option value="PRODUCT_CHANGE">교환(배송전+배송후)</option>
								<option value="NORMAL_PRODUCT_CHANGE">정상+교환(배송전+배송후)</option>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx[]" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처" multiple>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처" multiple>
							</select>
						</div>
						<div class="finder_col">
							<label>
								<input type="checkbox" name="ex_order_cancel_after_shipped" />배송후취소 제외
							</label>
<!--							<label>-->
<!--								<input type="checkbox" name="ex_delivery_is_free_y" />선불택배비 제외-->
<!--							</label>-->
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

		<div class="btn_set">
			<span>&nbsp;</span>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap grid_tb transaction_grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>


		<div id="modal_order_write_xls_pop" title="판매처 수동발주 업로드 팝업" class="red_theme" style="display: none;"></div>
		<div id="modal_order_format_seller_pop" title="발주서 포맷 사용자 정의" class="red_theme" style="display: none;"></div>
	</div>
</div>

<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.product.js"></script>
<script>
	window.name = 'settle_market_product';
	SettleProduct.MarketProductInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

