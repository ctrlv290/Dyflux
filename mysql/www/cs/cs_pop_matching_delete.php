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

$mode                   = "matching_delete";
$btn_name               = "매칭삭제";
$btn_cssClass           = "blue_btn";
$js_confirm_text        = "매칭삭제";
$order_idx              = $_POST["order_idx"];
$order_pack_idx         = $_POST["order_pack_idx"];
$matching_info_idx      = $_POST["matching_info_idx"];
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
<div class="container popup cs_order_hold_popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormMatchingDelete" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" name="matching_info_idx" value="<?php echo $matching_info_idx?>" />
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
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-matching-delete" class="large_btn blue_btn  "><?=$btn_name?></a>
						<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	CSPopup.CSPopupMatchingDeleteInit();
</script>

