<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 주문 보류 설정/해제 팝업 페이지
 */
//Page Info
$pageMenuIdx = 206;
//Init
include_once "../_init_.php";

$C_Order = new Order();
$C_CS = new CS();

$mode                   = "set_order_return_due";
$order_idx              = $_POST["order_idx"];
$order_pack_idx         = $_POST["order_pack_idx"];
$_order                 = $C_CS -> getOrderDetail($order_idx);

$current_is_return_due  = $_order["order_is_return_due"];

$order_is_return_due    = "";
$popup_title            = "";
$popup_save_btn_name    = "";

if($current_is_return_due == "Y"){
	//보류 해제
	$popup_title         = "반품예정 해제";
	$popup_save_btn_name = "반품예정 해제";
	$js_confirm_text     = "반품예정 해제";
	$order_is_return_due = "N";
}elseif($current_is_return_due == "N"){
	//보류 설정
	$popup_title         = "반품예정 설정";
	$popup_save_btn_name = "반품예정 설정";
	$js_confirm_text     = "반품예정 설정";
	$order_is_return_due = "Y";
}else{
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}

//현재 날짜
$today = date('Y-m-d');
?>
<div class="container popup cs_order_return_due_pop">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormHold" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_idx" value="<?php echo $order_idx?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" name="order_is_return_due" value="<?php echo $order_is_return_due?>" />
				<input type="hidden" name="save-text" value="<?php echo $popup_save_btn_name?>" />
				<input type="hidden" id="js_confirm_text" value="<?php echo $js_confirm_text?>" />
				<div class="tb_wrap">
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
						<a href="javascript:;" id="btn-save-order-return-due" class="large_btn blue_btn  "><?=$popup_save_btn_name?></a>
						<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$("#modal_common").dialog("option", "title", "<?=$popup_title?>");
	CSPopup.CSPopupOrderReturnDueInit();
</script>

