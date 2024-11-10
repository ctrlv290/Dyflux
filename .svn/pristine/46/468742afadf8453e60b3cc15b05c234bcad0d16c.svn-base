<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주 첫 화면 페이지
 */
//Page Info
$pageMenuIdx = 73;
//Permission IDX
$pagePermissionIdx = 73;
//Init
include_once "../_init_.php";

$C_Order = new Order();
//판매처별 현재 발주 수량 가져오기 (for 그래프)
$_order_cnt_list = $C_Order->getOrderAcceptListBySeller();

//오늘
$now_date = date('Y-m-d');
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<div class="step_wrap">
			<?php if(isDYLogin()) {?>
			<div class="sp_btn_wrap"><a href="order_confirm.php" class="large_btn red_btn btn-order-accept-all">일괄접수처리</a></div>
			<?php } ?>
			<a href="order_matching.php" class="large_btn">다음 <i class="fas fa-caret-right"></i></a>
			<div class="arrow-steps clearfix">
				<div class="step current"><span>발주</span></div>
				<div class="step"><span>매칭</span></div>
				<div class="step"><span>합포</span></div>
				<div class="step"><span>발주완료</span></div>
			</div>
		</div>
		<form name="searchForm" id="searchForm" method="get">
			<div class="find_wrap">
			<div class="finder">
				<div class="finder_set">
					<div class="finder_col">
						<span class="text">발주일 설정(수동설정)</span>
						<input type="text" name="order_date" class="w80px jqDate" value="<?=$now_date?>" readonly="readonly" />
						<select name="search_column">
							<option value="seller_name">판매처</option>
						</select>
						<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" />
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
			<a href="javascript:;" class="btn btn-order-list-grid-reload">새로고침</a>
			<a href="javascript:;" class="btn btn-order-list-reload">전체보기</a>
			최근 발주수량 합 : <span class="txt_sum_last_order_count strong">0</span>,
			최근 신규 발주수량 합 : <span class="txt_sum_last_new_order_count strong">0</span>,
			발주수량 합 : <span class="txt_sum_available_order_count strong">0</span>
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-xls-down">발주서양식 다운</a>
				<?php if(isDYLogin()) {?><a href="javascript:;" class="btn red_btn btn-all-order-delete">전체발주삭제</a><?php } ?>
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list">
			</table>
			<div id="grid_pager"></div>
		</div>
		<style>
			#chartdiv {width: 100%; height: 250px;overflow: hidden;}
		</style>
		<div id="chartdiv">

		</div>
		<div id="modal_order_write_xls_pop" title="판매처 수동발주 업로드 팝업" class="red_theme" style="display: none;"></div>
		<div id="modal_order_format_seller_pop" title="발주서 포맷 사용자 정의" class="red_theme" style="display: none;"></div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/main.js"></script>

<script src="/js/amcharts/core.js"></script>
<script src="/js/amcharts/charts.js"></script>
<script src="/js/amcharts/lang/ko_KR.js"></script>
<script src="/js/amcharts/themes/animated.js"></script>

<script src="/js/page/order.order.js?v=190703"></script>
<script>
	window.name = 'order_list';


	var chartData = [];
	<?php
	foreach($_order_cnt_list as $row) {
		echo 'chartData.push({"name": "'.$row["seller_name"].'", "val": '.$row["cnt"].'});' . PHP_EOL;
	}
	?>

	Order.OrderListInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

