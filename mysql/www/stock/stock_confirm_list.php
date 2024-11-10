<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 입고확정 리스트 페이지
 * TODO : 판매처 접속 시 벤더사 노출 상품만 노출 되도록 변경 필요!
 */
//Page Info
$pageMenuIdx = 118;
//Init
include_once "../_init_.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"];

$period_type                = $_GET["period_type"];

$stock_is_confirm = "N";
if($_GET["stock_is_confirm"]) $stock_is_confirm = $_GET["stock_is_confirm"];

$date_start = $_GET["date_start"];
$date_end = $_GET["date_end"];

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
                                <option value="order_accept_date" <?=($period_type == "order_accept_date") ? "selected" : ""?>>접수일</option>
                                <option value="stock_confirm_date" <?=($period_type == "stock_confirm_date") ? "selected" : ""?>>확정일</option>
                            </select>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
						<div class="finder_col">
							<span class="text">상태</span>
							<select name="stock_status" class="stock_status" data-selected="<?=$stock_status?>">
								<option value="">전체</option>
								<option value="NORMAL">정상</option>
								<option value="ABNORMAL">양품</option>
								<option value="BAD">불량</option>
							</select>
						</div>
                        <div class="finder_col">
                            <span class="text">구분</span>
                            <select name="stock_kind" class="stock_kind">
                                <option value="">전체</option>
                                <option value="STOCK_ORDER">발주</option>
                                <option value="BACK">회수</option>
                            </select>
                        </div>
                        <span><input type="checkbox" id="stock_is_confirm" name="stock_is_confirm" value="N" <?php if($stock_is_confirm == "N"){ echo "checked=\"checked\""; }?>/> 미확정만 내역만 보기</span>
					</div>
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
						<div class="finder_col">
							<select name="search_column">
                                <option value="all">전체</option>
								<option value="product_name" <?=($search_column == "product_name") ? "selected" : ""?>>상품명</option>
								<option value="product_option_name" <?=($search_column == "product_option_name") ? "selected" : ""?>>상품옵션명</option>
								<option value="A.stock_order_idx" <?=($search_column == "A.stock_order_idx") ? "selected" : ""?>>발주코드</option>
                                <option value="O.receive_name" <?=($search_column == "receive_name") ? "selected" : ""?>>고객명</option>
							</select>
							<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" value="<?=$search_keyword?>" />
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
        <p class="sub_desc">
            발주 금액 합계 : <span class="strong total_stock_sum"></span>원
        </p>
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="btn btn-column-setting-pop">항목설정</a>
			<div class="right">
                <a href="javascript:;" class="btn blue_btn btn-stock-multi-confirm">입고확정</a>
				<a href="javascript:;" class="btn green_btn btn-stock-confirm-list-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>


		<div id="modal_stock_order_email" title="이메일 발송" class="blue_theme" style="display: none;"></div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/common.function.js"></script>
<script>
	//사용자 항목설정을 불러오기전에 초기화
	var _gridColModel = [];
	var user_column_list = [];
</script>
<script src="/js/column_const.js?v=200316"></script>
<script src="/common/column_load_js.php?target=STOCK_CONFIRM_LIST"></script>
<script src="/js/page/stock.due.js?v=200316"></script>
<script>
	window.name = 'stock_confirm_list';
	Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");
	StockDue.StockConfirmListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>
