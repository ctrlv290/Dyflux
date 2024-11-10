<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처별송장등록 페이지
 */
//Page Info
$pageMenuIdx = 80;
//Init
include_once "../_init_.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$product_seller_group_idx = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx               = $_GET["seller_idx"] || 0;

$C_Code = new Code();
$_delivery_list = $C_Code->getDeliveryList();

//벤더사 로그인 일 때 판매처 기본 선택
if(!isDYLogin()) {
	$seller_idx = $GL_Member["member_idx"];
}

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
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="0">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="판매처를 선택하세요.">
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<select name="period_type">
								<option value="order_accept_regdate">접수일</option>
								<option value="shipping_date" selected="selected">배송일</option>
							</select>
							<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
							<input type="text" name="time_start" id="period_preset_start_time_input" class="w60px time_start " value="00:00:00" maxlength="8" />
							~
							<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
							<input type="text" name="time_end" id="period_preset_end_time_input" class="w60px time_end " value="23:59:59" maxlength="8" />
							<select class="sel_period_preset" id="period_preset_select"></select>
						</div>
					</div>
					<div class="finder_set">
						<?php if(isDYLogin()){?>
						<div class="finder_col">
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처">
							</select>
						</div>
						<?php } ?>
						<div class="finder_col">
							<span class="text">상태</span>
							<select name="order_progress_step">
								<option value="">전체</option>
								<option value="ORDER_INVOICE">송장</option>
								<option value="ORDER_SHIPPED">배송</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">C/S</span>
							<select name="order_progress_step">
								<option value="">전체</option>
								<option value="">정상</option>
							</select>
						</div>
						<div class="finder_col">
							<span class="text">택배사</span>
							<select name="delivery_code">
								<option value="">전체</option>
								<?php
								foreach($_delivery_list as $d)
								{
									echo '<option value="'.$d["delivery_code"].'">'.$d["delivery_name"].'</option>';
								}
								?>
							</select>
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">관리번호</span>
							<input type="text" name="A.order_idx" class="w100px enterDoSearch" placeholder="관리번호" />
						</div>
						<div class="finder_col">
							<span class="text">주문번호</span>
							<input type="text" name="A.market_order_no" class="w100px enterDoSearch" placeholder="주문번호" />
						</div>
						<div class="finder_col">
							<span class="text">송장번호</span>
							<input type="text" name="A.invoice_no" class="w100px enterDoSearch" placeholder="송장번호" />
						</div>
						<div class="finder_col">
							<span class="text">수령자</span>
							<input type="text" name="A.receive_name" class="w100px enterDoSearch" placeholder="수령자" />
						</div>
					</div>
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">수령자핸드폰</span>
							<input type="text" name="A.receive_hp_num" class="w100px enterDoSearch" placeholder="수령자핸드폰" />
						</div>
						<div class="finder_col">
							<span class="text">수령자주소</span>
							<input type="text" name="A.receive_addr1" class="w100px enterDoSearch" placeholder="수령자주소" />
						</div>
						<div class="finder_col">
							<span class="text">판매처상품명</span>
							<input type="text" name="A.market_product_name" class="w100px enterDoSearch" placeholder="판매처상품명" />
						</div>
						<div class="finder_col">
							<span class="text">판매처옵션</span>
							<input type="text" name="A.market_product_option" class="w100px enterDoSearch" placeholder="판매처옵션" />
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
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="btn btn-format-pop">포맷설정</a>
			<!--<a href="javascript:;" class="btn btn-order-batch-proc">주문일괄처리</a>-->
			<div class="right">
				<a href="javascript:;" class="btn green_btn btn-seller-invoice-down">다운로드</a>
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
<script src="/js/page/common.function.js?v=190704"></script>
<script src="/js/main.js"></script>
<script src="/js/page/order.order.js?v=190704"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script>
	window.name = 'order_seller_invoice';
	Order.SellerInvoiceInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

