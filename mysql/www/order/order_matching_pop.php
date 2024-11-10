<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 매칭 팝업
 * TODO : 판매처 접속 시 매칭 화면에서 벤더사 노출 상품만 검색 되도록 변경 필요!
 */
//Page Info
$pageMenuIdx = 73;
//Init
include_once "../_init_.php";


$mode                   = "matching";
$order_idx              = $_POST["order_idx"];

//로그인한 사용자가 벤더 판매처인지 여부 확인
$is_vendor_seller = false;

$C_Order = new Order();
$_order_info = $C_Order -> getOrderDataForMatching($order_idx);
if(!$_order_info) {
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}
?>
<form name="orderForm">
	<input type="hidden" id="order_idx" name="order_idx" value="<?=$order_idx?>" />
	<input type="hidden" id="seller_idx" name="seller_idx" value="<?=$_order_info["seller_idx"]?>" />
</form>
<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
			<div class="tb_wrap">
				<table>
					<colgroup>
						<col width="150">
						<col width="*">
						<col width="150">
						<col width="*">
					</colgroup>
					<tbody>
					<tr>
						<th>판매처</th>
						<td class="text_left"><?=$_order_info["seller_name"]?></td>
						<th>상품코드</th>
						<td class="text_left">
							<?php
							if($_order_info["market_product_no_is_auto"] == "Y") {
								echo $_order_info["market_product_name"];
								echo '<input type="hidden" id="ord_market_product_no" name="market_product_no" value="" />';
							}else{
								echo $_order_info["market_product_no"];
								echo '<input type="hidden" id="ord_market_product_no" name="market_product_no" value="' . $_order_info["market_product_no"] . '" />';
							}
							?>

						</td>
					</tr>
					<tr>
						<th>상품명</th>
						<td colspan="3" class="text_left"><input type="text" class="w100per" name="market_product_name" id="ord_market_product_name" value="<?=$_order_info["market_product_name"]?>" readonly="readonly"/></td>
					</tr>
					<tr>
						<th>옵션</th>
						<td colspan="3" class="text_left"><input type="text" class="w100per" name="market_product_option" id="ord_market_product_option" value="<?=$_order_info["market_product_option"]?>" readonly="readonly"/></td>
					</tr>
					<tr>
						<th>주문수량</th>
						<td colspan="3" class="text_left"><input type="text" class="w100px onlyNumberDynamic" name="order_cnt" id="ord_order_cnt" value="<?=$_order_info["order_cnt"]?>" readonly="readonly"/></td>
					</tr>
					</tbody>
				</table>

				<form name="searchFormPop" id="searchFormPop" method="get">
					<div class="find_wrap middle_pos" style="padding-right: 50px !important;">
						<div class="finder">
							<div class="finder_set">
								<div class="finder_col">
									<span class="text">상품명</span>
									<input type="text" name="product_name" class="w80px enterDoSearchPop" autofocus />
									<span class="text">옵션</span>
									<input type="text" name="product_option_name" class="w80px enterDoSearchPop" />
									<a href="javascript:;" id="btn_searchBar_pop" class="btn blue_btn btn_default">검색</a>
								</div>
								<div class="finder_col">
									<label>
										<input type="checkbox" name="matching_save" id="ord_matching_save" value="Y" checked="checked">매칭정보 저장
									</label>
									<label>
										<input type="checkbox" name="cnt_auto_cal" id="ord_cnt_auto_cal" value="Y" checked="checked">수량 자동계산
									</label>
									&nbsp;&nbsp;<span class="info_txt col_red"> '수량 자동계산' 체크 해제 시 '매칭정보 저장'을 할 수 없습니다.</span>
								</div>
							</div>
						</div>
						<div class="find_btn empty" style="width: 50px !important;">
							<div class="table">
								<div class="table_cell">

								</div>
							</div>
						</div>
						<a href="javascript:;" class="find_hide_btn">
							<i class="fas fa-angle-up up_btn"></i>
							<i class="fas fa-angle-down dw_btn"></i>
						</a>
					</div>
				</form>
				<div class="tb_wrap grid_tb matching_pop_grid_wrap">
					<table id="grid_list_pop" style="width: 100%;">
					</table>
					<div id="grid_pager_pop"></div>
				</div>
				<div class="tb_wrap grid_tb mt20">
					<table id="grid_list_pop_target" style="width: 100%;">
					</table>
				</div>
			</div>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" id="btn-save-format" class="large_btn blue_btn btn-order-matching-save ">매칭</a>
					<a href="javascript:;" class="large_btn red_btn btn-order-matching-pop-close">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var is_vendor_seller = false;
	Order.OrderMatchingPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

