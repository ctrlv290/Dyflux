<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 주문 배송정보 변경 팝업 페이지
 */
//Page Info
$pageMenuIdx = 206;
//Init
include_once "../_init_.php";

$C_Order = new Order();
$C_CS = new CS();

$mode                   = "set_address_change";
$order_pack_idx         = $_POST["order_pack_idx"];
$_order                 = $C_CS -> getOrderDetail($order_pack_idx);

if(!$_order){
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}else{
	extract($_order);
}

//현재 날짜
$today = date('Y-m-d');
?>
<div class="container popup cs_order_common_popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormHold" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>수령자</th>
							<td class="text_left">
								<input type="text" name="receive_name" value="<?=$receive_name?>" maxlength="50" />
							</td>
						</tr>
						<tr>
							<th>수령자 전화</th>
							<td class="text_left">
								<input type="text" name="receive_tp_num" class="onlyNumber" value="<?=$receive_tp_num?>" maxlength="20" />
							</td>
						</tr>
						<tr>
							<th>수령자 핸드폰</th>
							<td class="text_left">
								<input type="text" name="receive_hp_num" class="onlyNumber" value="<?=$receive_hp_num?>" maxlength="20" />
							</td>
						</tr>
						<tr>
							<th rowspan="2">주소</th>
							<td class="text_left">
								<input type="text" name="receive_zipcode" id="receive_zipcode" class="w50px" value="<?=$receive_zipcode?>" maxlength="5" readonly="readonly" />
								<a href="javascript:;" class="btn blue_btn btn-address-zipcode" data-zipcode-id="receive_zipcode" data-addr1-id="receive_addr1" data-addr2-id="">우편번호</a>
							</td>
						</tr>
						<tr>
							<td class="text_left">
								<input type="text" name="receive_addr1" id="receive_addr1" class="w100per" value="<?=$receive_addr1?>" />
							</td>
						</tr>
						<tr>
							<th>배송메모</th>
							<td class="text_left">
								<textarea name="receive_memo" class="w100per h100px"><?=$receive_memo?></textarea>
							</td>
						</tr>
						<tr>
							<th>CS</th>
							<td class="text_left">
								<textarea name="cs_msg" class="w100per h100px commonCsContent"></textarea>
							</td>
						</tr>
						<tr>
							<th>붙여넣기</th>
							<td class="text_left">
								<a href="javascript:;" class="link_blue btn-cs-paste" data-paste-from="product_name">상품명</a>
								<a href="javascript:;" class="link_blue btn-cs-paste" data-paste-from="product_option_name">옵션</a>
								<a href="javascript:;" class="link_blue btn-cs-paste" data-paste-from="product_full_name">선택한 상품명 + 옵션</a>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save-address" class="large_btn blue_btn  ">변경</a>
						<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	CSPopup.CSPopupAddressChangeInit();
</script>

