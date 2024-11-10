<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 충전금관리 - 충전금 입금내역 팝업 페이지
 */

//Page Info
$pageMenuIdx = 253;
//Init
include_once "../_init_.php";

$member_idx = $_GET["member_idx"];
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="searchForm" id="searchForm" method="get">
				<input type="hidden" name="member_idx" value="<?=$member_idx?>" />
			</form>
			<br>
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
<script src="/js/page/common.function.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/settle.charge.js"></script>
<script>
	window.name = "vendor_charge_history_pop";
	SettleCharge.VendorChargeHistoryPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
