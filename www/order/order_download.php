<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 주문다운로드 페이지
 */
//Page Info
$pageMenuIdx = 78;
//Init
include_once "../_init_.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$product_seller_group_idx = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx               = $_GET["seller_idx"] || 0;

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="get">
			<input type="hidden" name="detail_list" id="detail_list" value="" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">배송비</span>
							<select name="delivery_type">
								<option value="">전체</option>
								<option value="선불">선불</option>
								<option value="착불">착불</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">발주상태</span>
							<select name="order_progress_step">
								<option value="">전체</option>
								<option value="ORDER_ACCEPT">접수</option>
								<option value="ORDER_INVOICE">송장</option>
								<option value="ORDER_SHIPPED">배송</option>
							</select>
						</div>
						<div class="finder_col">
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처">
							</select>
						</div>
						<div class="finder_col">
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처">
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">발주일</span>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							<input type="text" name="time_start" id="period_preset_start_time_input" class="w60px time_start " value="00:00:00" maxlength="8" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<input type="text" name="time_end" id="period_preset_end_time_input" class="w60px time_end " value="23:59:59" maxlength="8" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>

						<div class="finder_col">
							<span class="text">수령자명</span>
							<input type="text" name="receive_name" class="w200px enterDoSearch" placeholder="검색어" />
						</div>
					</div>
				</div>
				<div class="find_btn">
					<div class="table">
						<div class="table_cell">
							<a href="javascript:;" id="btn_searchBar" class="big_btn btn_default">검색</a>
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
		<div class="tb_wrap div_right">
			<a href="javascript:;" class="btn btn-log-for-file-create-pop">파일 생성이력</a>
			<a href="javascript:;" class="btn btn-log-for-email-send-pop">이메일 발송이력</a>
			<a href="javascript:;" class="btn btn-log-for-download-pop">다운로드 이력</a>
		</div>
		<div class="grid_set">
			<div class="grid_box grid_box_40">
				<div class="grid_btn_set_top">
					<h3>공급처별 주문현황</h3>
<!--					<a href="javascript:;" class="btn">전체내역 파일생성</a>-->
					<div class="right">
					</div>
				</div>
				<div class="tb_wrap grid_tb">
					<table id="grid_list">
					</table>
				</div>
			</div>
			<div class="grid_box grid_box_gap">
				<i class="fas fa-caret-right"></i>
			</div>
			<div class="grid_box grid_box_50">
				<div class="grid_btn_set_top">
					<h3>상세내역</h3>
					<span></span>
					<div class="right">
						<a href="javascript:;" class="btn green_btn btn_file_download">파일다운로드</a>
						<a href="javascript:;" class="btn btn_email_send">이메일발송</a>
					</div>
				</div>
				<div class="tb_wrap grid_tb">
					<table id="grid_list2">
					</table>
					<div id="grid_pager2"></div>
				</div>
			</div>
		</div>
	</div>
	<div id="modal_order_download_email" title="이메일 발송" class="blue_theme" style="display: none;"></div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/order.order.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/order.shipped.js"></script>
<script>
	window.name = 'order_download';
	OrderShipped.OrderDownloadInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

