<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 송장일괄삭제(조회) 페이지
 */
//Page Info
$pageMenuIdx = 83;
//Init
include_once "../_init_.php";

//오늘
$now_date = date('Y-m-d');

//판매처 리스트
$C_ManageGroup = new ManageGroup();

$seller_idx_list = $C_ManageGroup -> getManageGroupMemberList('SELLER_ALL_GROUP', '');
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
							<span class="text">송장입력일</span>
							<input type="text" name="date" class="w80px jqDate " value="<?=$now_date?>" readonly="readonly" />
						</div>
						<div class="finder_col">
							<span class="text">송장입력시간</span>
							<input type="text" name="time_start" id="period_preset_start_time_input" class="w60px time_start " value="00:00:00" maxlength="8" />
							~
							<input type="text" name="time_end" id="period_preset_end_time_input" class="w60px time_end " value="23:59:59" maxlength="8" />
						</div>
						<div class="finder_col">
							<label><input type="checkbox" name="include_shipped" value="Y" />배송주문 포함</label>
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
		<p class="info_txt col_red">주의 : 해당 송장 정보를 삭제하시면 복구가 불가능하므로 주의하시기 바랍니다. </p>
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="large_btn red_btn btn-order-invoice-delete-all">&nbsp;&nbsp;&nbsp;송장삭제&nbsp;&nbsp;&nbsp;</a>
			<div class="right">
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/order.shipped.js"></script>
<script>
	window.name = 'order_invoice_delete_view';
	OrderShipped.OrderInvoiceDeleteViewInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

