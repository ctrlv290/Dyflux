<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 회수 팝업 페이지
 */
//Page Info
$pageMenuIdx = 217;
//Init
include_once "../_init_.php";

$C_Order = new Order();
$C_CS = new CS();

$mode                   = "restore_all";
$order_idx              = $_GET["order_idx"];
//$order_pack_idx         = $_POST["order_pack_idx"];
$_order                 = $C_CS -> getOrderDetail($order_idx);

if(!$_order){
	//header('HTTP/1.1 500 Internal Server Error');
	//header('Content-Type: text/html; charset=UTF-8');
	//die("Error");
}else{
	extract($_order);
}

$send_name = $_order["receive_name"];
$send_tel_num = $_order["receive_tp_num"];
$send_hp_num = $_order["receive_hp_num"];
$send_zipcode = $_order["receive_zipcode"];
$send_address = $_order["receive_addr1"];
if($_order["receive_addr2"]){
	$send_address .= " " . $_order["receive_addr2"];
}
$send_tel_num1 = "";
$send_tel_num2 = "";
$send_tel_num3 = "";
$send_hp_num1 = "";
$send_hp_num2 = "";
$send_hp_num3 = "";

$send_tel_num_ary = explode("-", add_hyphen(str_replace("-", "", $send_tel_num)));
$send_tel_num1 = $send_tel_num_ary[0];
$send_tel_num2 = $send_tel_num_ary[1];
$send_tel_num3 = $send_tel_num_ary[2];

$send_hp_num_ary = explode("-", add_hyphen(str_replace("-", "", $send_hp_num)));
$send_hp_num1 = $send_hp_num_ary[0];
$send_hp_num2 = $send_hp_num_ary[1];
$send_hp_num3 = $send_hp_num_ary[2];
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup cs_order_return_popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormReturn" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_idx" id="return_order_idx" value="<?php echo $order_idx?>" />
				<input type="hidden" name="order_pack_idx" id="return_order_pack_idx" value="<?php echo $order_pack_idx?>" />
			</form>
			<table class="tb_wrap">
				<colgroup>
					<col width="100">
					<col width="*">
					<col width="100">
				</colgroup>
				<tbody>
				<tr>
					<th>관리번호</th>
					<td class="text_left"><input type="text" name="order_idx" class="enterDoSearchReturn" value="<?=$order_idx?>" /> </td>
					<td>
						<a href="javascript:;" id="btn_searchBar" class="wide_btn btn_default btn-return-search">검색</a>
					</td>
				</tr>
				</tbody>
			</table>
			<div class="tb_wrap">
				<table>
					<tr>
						<td class="text_left">
							<label>
							<input type="checkbox" name="is_auto_stock_order" id="is_auto_stock_order" value="Y" checked="checked" />
							선택 상품 자동 입고예정 등록
							</label>
						</td>
					</tr>
				</table>
			</div>
			<div class="tb_wrap grid_tb">
				<table id="grid_return_order_list">
				</table>
				<div id="grid_return_order_pager"></div>
			</div>
			<div class="tb_wrap grid_tb">
				<table id="grid_return_call_list">
				</table>
				<div id="grid_return_call_pager"></div>
			</div>
			<div class="tb_wrap">
				<table class="no_padding_margin">
					<colgroup>
						<col width="50%" />
						<col width="50%" />
					</colgroup>
					<tbody>
					<tr>
						<td>
							<table class="font_small">
								<colgroup>
									<col width="90" />
									<col width="90" />
									<col width="*" />
									<col width="90" />
									<col width="*" />
								</colgroup>
								<tbody>
								<tr>
									<th rowspan="5">보내는 분</th>
									<th>이름</th>
									<td colspan="3" class="text_left">
										<input type="text" name="send_name" class="w80px" value="<?=$send_name?>" />
									</td>
								</tr>
								<tr>
									<th>전화번호</th>
									<td class="text_left">
										<input type="text" name="send_tel_num1" maxlength="4" class="w30px onlyNumberDynamic" value="<?=$send_tel_num1?>" />
										-
										<input type="text" name="send_tel_num2" maxlength="4" class="w30px onlyNumberDynamic" value="<?=$send_tel_num2?>" />
										-
										<input type="text" name="send_tel_num3" maxlength="4" class="w30px onlyNumberDynamic" value="<?=$send_tel_num3?>" />
									</td>
									<th>휴대폰</th>
									<td class="text_left">
										<input type="text" name="send_hp_num1" maxlength="4" class="w30px onlyNumberDynamic" value="<?=$send_hp_num1?>" />
										-
										<input type="text" name="send_hp_num2" maxlength="4" class="w30px onlyNumberDynamic" value="<?=$send_hp_num2?>" />
										-
										<input type="text" name="send_hp_num3" maxlength="4" class="w30px onlyNumberDynamic" value="<?=$send_hp_num3?>" />
									</td>
								</tr>
								<tr>
									<th rowspan="2">주소</th>
									<td colspan="3" class="text_left">
										<input type="text" name="send_zipcode" id="send_zipcode" class="w50px" maxlength="6" value="<?=$send_zipcode?>" />
										<a href="javascript:;" class="small_btn btn btn_default btn-address-zipcode" data-zipcode-id="send_zipcode" data-addr1-id="send_address" >우편번호 찾기</a>
									</td>
								</tr>
								<tr>
									<td colspan="3" class="text_left">
										<input type="text" name="send_address" id="send_address" maxlength="200" class="w100per" value="<?=$send_address?>" />
									</td>
								</tr>
								<tr>
									<th>배송메모</th>
									<td colspan="3" class="text_left">
										<input type="text" name="send_memo" maxlength="200" class="w100per" value="" />
									</td>
								</tr>
								</tbody>
							</table>
						</td>
						<td>
							<table class="font_small">
								<colgroup>
									<col width="90" />
									<col width="90" />
									<col width="*" />
									<col width="90" />
									<col width="*" />
								</colgroup>
								<tbody>
								<tr>
									<th rowspan="5">받는 분</th>
									<th rowspan="3">이름</th>
									<td colspan="3" class="text_left">
										<select name="address_book">
											<option>TEST</option>
										</select>

										<a href="javascript:;" class="xsmall_btn green_btn btn-addressbook-add">추가</a>
										<a href="javascript:;" class="xsmall_btn blue_btn btn-addressbook-update">수정</a>
										<a href="javascript:;" class="xsmall_btn red_btn btn-addressbook-delete">삭제</a>
										<a href="javascript:;" class="xsmall_btn btn-addressbook-reset">초기화</a>
<!--										<a href="javascript:;" class="xsmall_btn">기본주소</a>-->
									</td>
								</tr>
								<tr>
									<td rowspan="2" class="text_left">
										<input type="text" name="receive_name" maxlength="50" class="w120px" value="" />
									</td>
									<th>전화번호</th>
									<td class="text_left">
										<input type="text" name="receive_tel_num1" maxlength="4" class="w30px onlyNumberDynamic" value="" />
										-
										<input type="text" name="receive_tel_num2" maxlength="4" class="w30px onlyNumberDynamic" value="" />
										-
										<input type="text" name="receive_tel_num3" maxlength="4" class="w30px onlyNumberDynamic" value="" />
									</td>
								</tr>
								<tr>
									<th>휴대폰</th>
									<td class="text_left">
										<input type="text" name="receive_hp_num1" maxlength="4" class="w30px onlyNumberDynamic" value="" />
										-
										<input type="text" name="receive_hp_num2" maxlength="4" class="w30px onlyNumberDynamic" value="" />
										-
										<input type="text" name="receive_hp_num3" maxlength="4" class="w30px onlyNumberDynamic" value="" />
									</td>
								</tr>
								<tr>
									<th rowspan="2">주소</th>
									<td colspan="3" class="text_left">
										<input type="text" name="receive_zipcode" id="receive_zipcode" maxlength="6" class="w50px" value="" />
										<a href="javascript:;" class="small_btn btn btn_default btn-address-zipcode" data-zipcode-id="receive_zipcode" data-addr1-id="receive_address" >우편번호 찾기</a>
									</td>
								</tr>
								<tr>
									<td colspan="3" class="text_left">
										<input type="text" name="receive_address" id="receive_address" maxlength="200" class="w100per" value="" />
									</td>
								</tr>
								</tbody>
							</table>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
			<div class="tb_wrap">
				<table class="no_padding_margin ">
					<colgroup>
						<col width="65%" />
						<col width="35%" />
					</colgroup>
					<tbody>
					<tr>
						<td>
							<table class="font_small">
								<colgroup>
									<col width="90">
									<col width="90">
									<col width="*">
									<col width="90">
									<col width="90">
									<col width="90">
									<col width="*">
								</colgroup>
								<tbody>
								<tr>
									<th rowspan="9">접수정보</th>
									<th>정산구분</th>
									<td colspan="5" class="text_left">
										<!--
										*** CJ 계약 문제로 신용만 사용 할 수 있음 !!! *** - ssawoona
										<label>
											<input type="radio" name="delivery_pay_type" value="선불" />선불
										</label>
										<label>
											<input type="radio" name="delivery_pay_type" value="착불" />착불
										</label>
										-->
										<label>
											<input type="radio" name="delivery_pay_type" value="신용" checked="checked" />신용
										</label>
<!--										<label>-->
<!--											<input type="radio" name="delivery_pay_type" value="" />착지신용-->
<!--										</label>-->
									</td>
								</tr>
								<tr>
									<th>반품송장번호</th>
									<td colspan="2" class="text_left return_invoice_no"></td>
									<th>원송장번호</th>
									<td colspan="2" class="text_left return_invoice_no_original"></td>
								</tr>
								<tr>
									<th>접수일</th>
									<td colspan="2" class="text_left return_accept_date"></td>
									<th>송장일</th>
									<td colspan="2" class="text_left return_invoice_date"></td>
								</tr>
								<tr>
									<th>배송일</th>
									<td colspan="2" class="text_left return_delivery_date"></td>
									<th>수령일</th>
									<td colspan="2" class="text_left return_receive_date"></td>
								</tr>
								<tr>
									<th>고객사용번호</th>
									<td colspan="5" class="text_left return_customer_use_no"></td>
								</tr>
								<tr>
									<th>접수구분</th>
									<td colspan="5" class="text_left">
										<label>
											<input type="radio" name="delivery_return_type" value="BACK" checked="checked" />회수
										</label>
										<label>
											<input type="radio" name="delivery_return_type" value="RETURN" />반품
										</label>
									</td>
								</tr>
								<tr>
									<th>박스수량</th>
									<td colspan="5" class="text_left">
										<input type="text" name="box_num" class="w100px return_box_num" value="1" />
									</td>
								</tr>
								<tr>
									<th>물품가격</th>
									<td colspan="2" class="text_left">
										<input type="text" name="product_price" class="w100px return_product_price" value="0" />
									</td>
									<th>배송운임</th>
									<td colspan="2" class="text_left">
										<input type="text" name="delivery_price" class="w100px return_delivery_price" value="0" />
									</td>
								</tr>
								<tr>
									<th>사이트결제</th>
									<td class="text_left">
										<input type="text" name="pay_site" class="w100px return_pay_site" value="0" />
									</td>
									<th>동봉</th>
									<td class="text_left">
										<input type="text" name="pay_pack" class="w60px return_pay_pack" value="0" />
									</td>
									<th>계좌</th>
									<td class="text_left">
										<input type="text" name="pay_account" class="w100px return_pay_account" value="0" />
									</td>
								</tr>
								</tbody>
							</table>
						</td>
						<td>
							<table>
								<colgroup>
									<col width="120">
									<col width="*">
								</colgroup>
								<tbody>
								<tr>
									<th>CS</th>
									<td class="text_left">
										<textarea name="cs_msg" class="w100per h100px commonCsContent"></textarea>
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<a href="javascript:;" id="btn-return-update" class="large_btn green_btn">반품수정</a>
										<a href="javascript:;" id="btn-return-delete" class="large_btn red_btn  ">반품삭제</a>
										<a href="javascript:;" id="btn-return-add" class="large_btn blue_btn  ">반품접수</a>
										<a href="javascript:self.close();" class="large_btn red_btn">취소</a>
									</td>
								</tr>
								</tbody>
							</table>

						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
	jqgridDefaultSetting = false;
</script>
<script src="/js/main.js"></script>
<script src="/js/page/cs.cs.js?v=200220"></script>
<script>
	CSPopup.CSPopupOrderReturnInit(<?=$order_idx?>);
</script>

<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

