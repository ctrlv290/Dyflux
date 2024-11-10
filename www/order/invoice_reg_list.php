<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 송장등록조회 페이지
 */
//Page Info
$pageMenuIdx = 223;
//Init
include_once "../_init_.php";

$date_start           = $_GET["date_start"];
$date_end             = $_GET["date_end"];
$market_invoice_state = $_GET["market_invoice_state"];

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
							<select name="seller_idx" class="seller_idx" data-selected="" data-default-value="" data-default-text="판매처 선택">
								<option value="">전체판매처</option>
								<?php
								foreach($seller_idx_list as $item){
									echo '<option value="'.$item["idx"].'">'.$item["name"].'</option>';
								}
								?>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">등록일</span>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
						<div class="finder_col">
							<span class="text">등록결과</span>
							<select name="market_invoice_state">
								<option value="">전체</option>
								<option value="N" <?=($market_invoice_state == "N") ? "selected" : ""?>>대기</option>
								<option value="S" <?=($market_invoice_state == "S") ? "selected" : ""?>>정상</option>
								<option value="E" <?=($market_invoice_state == "E") ? "selected" : ""?>>오류</option>
								<option value="U" <?=($market_invoice_state == "U") ? "selected" : ""?>>알수없음</option>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">주문번호</span>
							<input type="text" name="O.market_order_no" class="w80px enterDoSearch"  />
						</div>
						<div class="finder_col">
							<span class="text">송장번호</span>
							<input type="text" name="I.invoice_no" class="w80px enterDoSearch"  />
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
		<p class="sub_tit">※ 최근 10일 이내 자료만 조회 가능합니다.</p>
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


		<div id="modal_order_write_xls_pop" title="판매처 수동발주 업로드 팝업" class="red_theme" style="display: none;"></div>
		<div id="modal_order_format_seller_pop" title="발주서 포맷 사용자 정의" class="red_theme" style="display: none;"></div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/order.order.js"></script>
<script>
	window.name = 'order_collect';
	Order.InoviceRegListPageInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

