<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처 목록 페이지
 */
//Page Info
$pageMenuIdx = 43;
//Permission IDX
$pagePermissionIdx = 43;
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
							<select name="seller_is_use">
								<option value="">전체</option>
								<option value="Y">사용함(Y)</option>
								<option value="N">사용안함(N)</option>
							</select>
						</div>
						<div class="finder_col">
							<select name="manage_group_idx" id="manage_group_idx">
								<option value="">전체</option>
							</select>
						</div>
						<div class="finder_col">
							<input type="text" name="seller_name" class="w200px enterDoSearch" placeholder="판매처 이름" />
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
			<a href="javascript:;" class="btn btn-seller-write-pop">신규등록</a>
			<a href="javascript:;" class="btn btn-manage-group-pop">그룹관리</a>
			<div class="right">
				<a href="javascript:;" class="btn btn-change-log-viewer-pop">변경이력</a>
				<a href="javascript:;" class="btn green_btn btn-seller-xls-down">다운로드</a>
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
<script src="/js/page/info.seller.js"></script>
<script src="/js/page/info.group.js"></script>
<script>
	window.name = 'seller_list';
	Seller.SellerListInit();
	ManageGroup.getManageGroupList('SELLER_GROUP');
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

