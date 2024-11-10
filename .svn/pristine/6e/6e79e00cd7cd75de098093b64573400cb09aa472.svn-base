<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통으로 사용되는 정보 변경이력 팝업
 */
//Page Info
$pageMenuIdx = 195;
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
				<input type="hidden" name="view" value="<?=$_GET["view"]?>" />
				<div class="find_wrap">
					<div class="finder">
						<div class="finder_set">
							<div class="finder_col">
								<span class="text">수정일</span>
								<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " readonly="readonly" />
								~
								<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " readonly="readonly" />
								<select class="sel_period_preset" id="period_preset_select">

								</select>
							</div>
						</div>
						<div class="finder_set">
							<div class="finder_col">
								<span class="text">입고예정일</span>
								<input type="text" name="date_start2" id="period_preset_start_input2" class="w80px jqDate " readonly="readonly" />
								~
								<input type="text" name="date_end2" id="period_preset_end_input2" class="w80px jqDate " readonly="readonly" />
								<select class="sel_period_preset2" id="period_preset_select2">

								</select>
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
			<div class="tb_wrap grid_tb">
				<table id="grid_list">
				</table>
				<div id="grid_pager"></div>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/stock.due.js"></script>
<script>
	StockDue.StockDueDelayListInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
