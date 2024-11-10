<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주 합포 화면 페이지
 */
//Page Info
$pageMenuIdx = 73;
//Init
include_once "../_init_.php";


//오늘
$now_date = date('Y-m-d');
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="step_wrap">
			<a href="order_matching.php" class="large_btn">이전 <i class="fas fa-caret-left"></i></a>
			<a href="javascript:;" class="large_btn btn-next-complete">다음 <i class="fas fa-caret-right"></i></a>
			<div class="arrow-steps clearfix">
				<div class="step"><span>발주</span></div>
				<div class="step"><span>매칭</span></div>
				<div class="step current"><span>합포</span></div>
				<div class="step"><span>발주완료</span></div>
			</div>
		</div>
		<form name="searchForm" id="searchForm" method="get">
			<div class="find_wrap">

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


		<div id="modal_order_matching_pop" title="상품매칭" class="red_theme" style="display: none;"></div>
		<div id="modal_auto_pack_pop" title="자동합포" class="red_theme" style="display: none;"></div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/page/order.order.js?v=190820"></script>
<script>
	window.name = 'order_package';
	Order.OrderPackageInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

