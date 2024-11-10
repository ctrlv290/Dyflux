<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 기본정보관리[판매처, 벤더사, 공급처] 그룹 팝업
 *       각 그룹 관리 팝업 페이지에서 Include 함
  */
//Page Info
//Include 하는 파일에서 선 정의함.
//Init
include_once "../_init_.php";

//$manage_group_type
//Include 하는 파일에서 선 정의함.
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
								<input type="text" name="manage_group_name" class="w200px enterDoSearch" placeholder="그룹 이름" />
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
				<input type="text" name="manage_group_name" id="input_manage_group_name" value="" placeholder="그룹 이름" />
				<a href="javascript:;" class="btn btn-manage-group-add">추가</a>
				<div class="right">
				</div>
			</div>
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
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/info.group.js"></script>
<script>
	ManageGroup.ManageGroupInit('<?=$manage_group_type?>');
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
