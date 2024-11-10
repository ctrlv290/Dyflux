<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 주문 합포 추가 팝업 페이지
 */
//Page Info
$pageMenuIdx = 206;
//Init
include_once "../_init_.php";

$C_Order = new Order();
$C_CS = new CS();

$mode                   = "package_add";
$btn_name               = "추가";
$btn_cssClass           = "blue_btn";
$js_confirm_text        = "추가";
$order_pack_idx         = $_POST["order_pack_idx"];
$_order                 = $C_CS -> getOrderDetail($order_pack_idx);

if(!$_order || ($_order["order_progress_step"] != "ORDER_ACCEPT" && $_order["order_progress_step"] != "ORDER_ACCEPT_TEMP")){
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}else{
	extract($_order);
}

//택배사 리스트
$_delivery_list = $C_CS -> getDeliveryDistinctList();

//현재 날짜
$today = date('Y-m-d');
?>
<div class="container popup cs_order_common_popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormHold" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" id="js_confirm_text" value="<?php echo $js_confirm_text?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>추가 관리번호</th>
							<td class="text_left">
								<input type="text" class="w100per order_idx_selectize selectize-multi" name="order_idx_list" value="" />
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
						<a href="javascript:;" id="btn-save-address" class="large_btn <?=$btn_cssClass?>  "><?=$btn_name?></a>
						<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	CSPopup.CSPopupPackageAddInit();
</script>

