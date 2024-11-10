<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사용자관리 목록 페이지
 */
//Page Info
$pageMenuIdx = 56;
//Init
include_once "../_init_.php";

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
						<input type="text" name="nameid" class="w200px enterDoSearch" placeholder="이름 또는 아이디" />
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
			<a href="user_write.php" class="btn">신규등록</a>
			<div class="right">
				<a href="javascript:;" class="btn btn-change-log-viewer-pop">변경이력</a>
				<a href="javascript:;" class="btn green_btn btn-user-xls-down">다운로드</a>
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/info.user.js"></script>
<script>
	User.UserListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

