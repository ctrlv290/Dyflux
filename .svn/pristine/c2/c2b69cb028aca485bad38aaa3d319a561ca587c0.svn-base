<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 사은품 등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 276;
//Init
include_once "../_init_.php";

$C_Order = new Order();

$mode = "add";
$gift_idx                 = $_GET["gift_idx"];
$gift_name = "";
$gift_date_start = "";
$gift_date_end = "";
$supplier_idx = 0;
$product_option_idx_list = "";
$seller_idx = 0;
$market_product_no_list = "";
$gift_match_pay = "";
$gift_match_product = "";
$gift_match_product_cnt_s = "";
$gift_match_product_cnt_e = "";
$gift_match_order_amount = "";
$gift_match_order_amount_s = "";
$gift_match_order_amount_e = "";
$gift_delivery_free = "";
$gift_memo = "";
$gift_product_full_name = "";
$gift_product_idx = "";
$gift_product_option_idx = "";
$gift_cnt = "";
$gift_is_only = "";
$gift_cnt_type = "O";
$gift_cnt_type_cnt = "";
$gift_status = "N";

if($gift_idx){
	$_view = $C_Order->getGift($gift_idx);

	if($_view){
		$mode = "update";

		extract($_view);
		$start = strtotime($gift_date_start);
		$end = strtotime($gift_date_end);

		$gift_date_start_1 = date("Y-m-d", $start);
		$gift_date_start_2 = date("H:i:s", $start);

		$gift_date_end_1 = date("Y-m-d", $end);
		$gift_date_end_2 = date("H:i:s", $end);

		$gift_product_full_name = $product_name . " " . $product_option_name;
	}
}

?>

<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" method="post" class="<?=$mode?>">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<input type="hidden" name="gift_idx" value="<?=$gift_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>사은품 이름 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="gift_name" class="w400px" maxlength="20" value="<?=$gift_name?>" />
							</td>
						</tr>
						<tr>
							<th>기간 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="gift_date_start_1" class="w80px jqDate" readonly="readonly" value="<?=$gift_date_start_1?>" />
								<input type="text" name="gift_date_start_2" class="w80px time" value="<?=$gift_date_start_2?>" />
								~
								<input type="text" name="gift_date_end_1" class="w80px jqDate" readonly="readonly" value="<?=$gift_date_end_1?>" />
								<input type="text" name="gift_date_end_2" class="w80px time" value="<?=$gift_date_end_2?>" />

								<span class="info_txt col_red">주의: 시간설정은 주문시간이 아닙니다. 발주시간입니다.</span>
							</td>
						</tr>
						<tr>
							<th>공급처</th>
							<td class="text_left">
								<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="0">
									<option value="0">전체 그룹</option>
								</select>
								<select name="supplier_idx" class="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="공급처를 선택하세요.">
								</select>
							</td>
						</tr>
						<tr>
							<th>관리자 옵션코드</th>
							<td class="text_left">
								<input type="text" name="product_option_idx_list" id="product_option_idx_list" class="w400px" maxlength="300" value="<?=$product_option_idx_list?>" />
								<a href="javascript:;" class="btn btn-product-select">검색</a>
								<span class="info_txt col_red">공급처 선택 시 관리자 상품 코드는 비활성화 됩니다.</span>
							</td>
						</tr>
						<tr>
							<th>판매처</th>
							<td class="text_left">
								<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="0">
									<option value="0">전체 그룹</option>
								</select>
								<select name="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="판매처를 선택하세요.">
								</select>
							</td>
						</tr>
						<tr>
							<th>판매처 상품코드</th>
							<td class="text_left">
								<input type="text" name="market_product_no_list" class="w400px" maxlength="20" value="<?=$market_product_no_list?>" />
							</td>
						</tr>
						</tbody>
					</table>
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th rowspan="3">사은품<br>맵핑조건 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<label><input type="checkbox" name="gift_match_pay" value="Y" <?=($gift_match_pay == "Y") ? "checked" : ""?> />결제수단</label>
								<input type="text" name="gift_match_pay_text" class="w100px" maxlength="50" value="<?=$gift_match_pay_text?>" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								<label><input type="checkbox" name="gift_match_product" value="Y" <?=($gift_match_product == "Y") ? "checked" : ""?> />상품수량</label>
								<input type="text" name="gift_match_product_cnt_s" class="w50px onlyNumber" maxlength="5" value="<?=$gift_match_product_cnt_s?>" />
								개 이상
								<input type="text" name="gift_match_product_cnt_e" class="w50px onlyNumber" maxlength="5" value="<?=$gift_match_product_cnt_e?>" />
								개 이하
							</td>
						</tr>
						<tr>
							<td class="text_left">
								<label><input type="checkbox" name="gift_match_order_amount" value="Y" <?=($gift_match_order_amount == "Y") ? "checked" : ""?> />주문금액</label>
								<input type="text" name="gift_match_order_amount_s" class="w50px onlyNumber" maxlength="7" value="<?=$gift_match_order_amount_s?>" />
								원 이상
								<input type="text" name="gift_match_order_amount_e" class="w50px onlyNumber" maxlength="7" value="<?=$gift_match_order_amount_e?>" />
								원 이하
							</td>
						</tr>
						</tbody>
					</table>
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr class="dis_none">
							<th>배송비</th>
							<td class="text_left">
								<label><input type="checkbox" name="gift_delivery_free" value="Y" <?=($gift_delivery_free == "Y") ? "checked" : ""?> />무료배송</label>
							</td>
						</tr>
						<tr>
							<th>사은품 내용</th>
							<td class="text_left">
								<input type="text" name="gift_memo" class="w400px" maxlength="20" value="<?=$gift_memo?>" />
							</td>
						</tr>
						<tr>
							<th>사은품 상품</th>
							<td class="text_left">
								<input type="text" name="gift_product_full_name" class="w400px" maxlength="100" readonly="readonly" value="<?=$gift_product_full_name?>" />
								<a href="javascript:;" class="btn btn-gift-select">검색</a>
								<input type="hidden" name="gift_product_idx" value="<?=$gift_product_idx?>" />
								<input type="hidden" name="gift_product_option_idx" value="<?=$gift_product_option_idx?>" />
							</td>
						</tr>
						<tr>
							<th>사은품 수량</th>
							<td class="text_left">
								<input type="text" name="gift_cnt" class="w80px onlyNumber" maxlength="3" value="<?=$gift_cnt?>" />개
							</td>
						</tr>
						</tbody>
					</table>
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>적용조건</th>
							<td class="text_left">
								<label><input type="checkbox" name="gift_is_only" value="Y" <?=($gift_is_only == "Y") ? "checked" : ""?> />중복불가</label>
							</td>
						</tr>
						<tr>
							<th>적용수량</th>
							<td class="text_left">
								<label><input type="radio" name="gift_cnt_type" value="O" <?=($gift_cnt_type == "O") ? "checked" : ""?> />주문번호당</label>
								<label><input type="radio" name="gift_cnt_type" value="C" <?=($gift_cnt_type == "C") ? "checked" : ""?> />주문수량만큼</label>
								<label><input type="radio" name="gift_cnt_type" value="N" <?=($gift_cnt_type == "N") ? "checked" : ""?> />주문수량</label>
								<input type="text" name="gift_cnt_type_cnt" class="w40px onlyNumber" value="<?=$gift_cnt_type_cnt?>" />개당 사은품 1개
							</td>
						</tr>
						</tbody>
					</table>
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>상태 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<label><input type="radio" name="gift_status" value="N" <?=($gift_status == "N") ? "checked" : ""?> />준비중</label>
								<label><input type="radio" name="gift_status" value="Y" <?=($gift_status == "Y") ? "checked" : ""?> />진행중</label>
								<label><input type="radio" name="gift_status" value="X" <?=($gift_status == "X") ? "checked" : ""?> />종료</label>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
						<a href="javascript:self.close();" class="large_btn red_btn">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>


<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/order.gift.js"></script>
<script>
	window.name="gift_pop";
	OrderGift.GiftWriteInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
