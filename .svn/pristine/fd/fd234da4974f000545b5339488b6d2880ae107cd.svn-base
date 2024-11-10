<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통으로 사용되는 정보 변경이력 팝업
 */
//Page Info
$pageMenuIdx = 165;
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
								<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " readonly="readonly" />
								~
								<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " readonly="readonly" />
								<select class="sel_period_preset" id="period_preset_select">

								</select>
							</div>
							<div class="finder_col">
								<select name="search_column">
									<option>검색조건</option>
									<option value="member_idx">대상코드</option>
									<option value="memo">항목</option>
									<option value="member_id">작업자 ID</option>
								</select>
								<input type="text" name="keyword" class="w150px enterDoSearch" placeholder="검색어" />
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
<!--			<div class="grid_btn_set_top">-->
<!--				<input type="text" name="manage_group_name" id="input_manage_group_name" value="" placeholder="그룹 이름" />-->
<!--				<a href="javascript:;" class="btn btn-manage-group-add">추가</a>-->
<!--				<div class="right">-->
<!--				</div>-->
<!--			</div>-->
			<div class="grid_btn_set_top">
				<span></span>
				<div class="right">
					<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
				</div>
			</div>
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
<script src="/js/page/info.change_log_viewer.js"></script>
<script>
	ChangeLogViewer.ChangeListInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
