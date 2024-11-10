<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매입매출현황 [판매일보] 페이지
 */
//Page Info
$pageMenuIdx = 308;
//Init
include_once "../_init_.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"];
$product_seller_group_idx   = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx                 = $_GET["seller_idx"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];
$product_tax_type           = $_GET["product_tax_type"];

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
                            <input type="hidden" name="search_mode" id="search_mode" value="search" />
							<select name="period_type">
                                <option value="settle_date">정산일</option>
								<option value="order_accept_regdate">접수일</option>
                                <option value="order_progress_step_accept_temp_date">발주일</option>
							</select>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
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
							<span class="text">상품세금종류</span>
							<select name="product_tax_type">
								<option value="">전체</option>
								<option value="TAXATION" <?=(strtoupper($product_tax_type) == "TAXATION") ? "selected" : "" ?>>과세</option>
								<option value="FREE" <?=(strtoupper($product_tax_type) == "FREE") ? "selected" : "" ?>>면세</option>
								<option value="SMALL" <?=(strtoupper($product_tax_type) == "SMALL") ? "selected" : "" ?>>영세</option>
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
								<?php if($seller_idx) {
									echo '<option value="'.$seller_idx.'" selected></option>';
								}?>
							</select>
						</div>
						<?php if(isDYLogin()) { ?>
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx[]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처" multiple>
								<?php if($supplier_idx) {
									echo '<option value="'.$supplier_idx.'" selected></option>';
								}?>
							</select>
						</div>
						<?php } ?>
						<div class="finder_col">
							<select name="search_column">
								<option value="T.product_name">상품명</option>
								<option value="T.product_option_name">옵션명</option>
                                <option value="O.receive_name">수령자</option>
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
<!--		</form>-->
		<p class="sub_desc">
			매출합계 : <span class="strong total_sale_sum"></span>원
			<?php if(isDYLogin()) { ?>
			, 매입합계 : <span class="strong total_purchase_sum"></span>원
			, 매출이익합계 : <span class="strong total_profit_sum"></span>원
			<?php } ?>
		</p>
		<?php if(isDYLogin()){?>
		<div class="btn_set">
<!--			<a href="javascript:;" class="btn btn-column-setting-pop">항목설정</a>-->
			<!--<a href="javascript:;" class="btn btn-order-batch-proc">주문일괄처리</a>-->
			<span></span>
            <span class="finder_col">
                <span class="text">검색 결과 내 - 판매처</span>
                <select name="filter_product_seller_group_idx" id="filter_product_seller_group_idx">
                    <option value="all">전체그룹</option>
                </select>
                <select name="filter_seller_idx" id="filter_seller_idx">
                    <option value=>전체 판매처</option>
                </select>
            </span>
            <?php if(isDYLogin()) { ?>
                <span class="finder_col">
                    <span class="text">공급처</span>
                    <select name="filter_product_supplier_group_idx" id="filter_product_supplier_group_idx">
                        <option value="all">전체그룹</option>
                    </select>
                    <select name="filter_supplier_idx" id="filter_supplier_idx">
                    <option value=>전체 공급처</option>
                </select>
                </span>
            <?php } ?>
            <span>
            <a href="javascript:;" id="btn_filterBar" class="btn btn_default" style="margin-left: 5px; margin-top: 1px">검색</a>
            </form>
			<div class="right">
				<a href="javascript:;" class="btn btn-closing">발주마감</a>
				<a href="javascript:;" class="btn btn-cs-open">추가주문등록</a>
				<a href="javascript:;" class="btn btn-some">광고비등록</a>
				<a href="javascript:;" class="btn btn-purchase-adjust" data-type="purchase">매입보정</a>
				<a href="javascript:;" class="btn btn-sale-adjust" data-type="sale">매출보정</a>
				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
                <a href="javascript:;" class="btn green_btn btn-xls-up">업로드</a>
			</div>
		</div>
		<?php }else {?>
		<div class="btn_set">
			<!--			<a href="javascript:;" class="btn btn-column-setting-pop">항목설정</a>-->
			<!--<a href="javascript:;" class="btn btn-order-batch-proc">주문일괄처리</a>-->
<!--			<span>&nbsp;</span>-->
<!--			<div class="right">-->
<!--				<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>-->
<!--			</div>-->
		</div>
		<?php } ?>
		<div class="tb_wrap grid_tb transaction_grid_tb">
			<table id="grid_list_transaction">
			</table>
			<div id="grid_pager"></div>
		</div>


		<div id="modal_order_write_xls_pop" title="판매처 수동발주 업로드 팝업" class="red_theme" style="display: none;"></div>
		<div id="modal_order_format_seller_pop" title="발주서 포맷 사용자 정의" class="red_theme" style="display: none;"></div>
	</div>
</div>

<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js?v=200424"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/info.category.js"></script>
<script src="/js/addr.isolated.js?v=200323"></script>
<script src="/js/page/settle.transaction.js?v=200422"></script>
<script>
	window.name = 'transaction_list';
	SettleTransaction.TransactionListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

