<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주서 파일 생성 로그 팝업 페이지
 */

//Page Info
$pageMenuIdx = 190;
//Init
include_once "../_init_.php";
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="searchForm" id="searchForm" method="get">
				<input type="hidden" name="manage_group_type" value="<?=$manage_group_type?>" />
				<div class="find_wrap">
					<div class="finder">
						<div class="finder_set">
							<div class="finder_col">
								<span class="text">파일생성일</span>
								<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
								~
								<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
								<select class="sel_period_preset" id="period_preset_select"></select>
							</div>
						</div>
						<div class="finder_set">
							<div class="finder_col">
								<span class="text">공급처</span>
								<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="0">
									<option value="0">전체그룹</option>
								</select>
								<select name="supplier_idx[]" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처" multiple>
								</select>
							</div>
							<div class="finder_col">
								<select name="search_column">
									<option value="member_id" <?=($search_column == "member_id") ? "selected" : ""?>>작업자</option>
								</select>
								<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" value="<?=$search_keyword?>" />
							</div>
						</div>
					</div>
					<div class="find_btn">
						<div class="table">
							<div class="table_cell">
								<a href="javascript:;" id="btn_searchBar" class="big_btn  btn_default">검색</a>
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
				<table id="grid_list">
				</table>
				<div id="grid_pager"></div>
			</div>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
				</div>
			</div>
		</div>


		<div id="modal_stock_order_email" title="이메일 발송" class="blue_theme" style="display: none;"></div>
	</div>
</div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/stock.order.js"></script>
<script>
	window.name = "stock_order_log_file";
	StockOrder.StockOrderLogFileInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
