<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처 검색 팝업 창
 *       공통으로 사용되는 팝업
 *       판매처 선택 시 사용
 */
//Page Info
$pageMenuIdx = 173;
//Permission IDX
$pagePermissionIdx = 38;    //신상품등록
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
								<select name="manage_group_idx" id="manage_group_idx">
									<option value="">전체 그룹</option>
								</select>
							</div>
							<div class="finder_col">
								<input type="text" name="seller_nameidx" class="w200px enterDoSearch" placeholder="판매처명 또는 판매처 코드" />
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
<script src="/js/page/info.seller.js"></script>
<script src="/js/page/info.group.js"></script>
<script>
	Seller.SellerSearchPopInit();
	ManageGroup.getManageGroupList('SELLER_ALL_GROUP');
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
