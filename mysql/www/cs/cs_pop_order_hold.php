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

$mode                   = "set_order_hold";
$order_pack_idx         = $_POST["order_pack_idx"];
$current_hold_status    = $C_CS -> getOrderHoldStatus($order_pack_idx);
$order_is_hold          = "";
$popup_title            = "";
$popup_save_btn_name    = "";

if($current_hold_status == "Y"){
	//보류 해제
	$popup_title         = "보류 해제";
	$popup_save_btn_name = "보류 해제";
	$order_is_hold       = "N";
}elseif($current_hold_status == "N"){
	//보류 설정
	$popup_title         = "보류 설정";
	$popup_save_btn_name = "보류 설정";
	$order_is_hold       = "Y";
}else{
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}

//현재 날짜
$today = date('Y-m-d');
?>
<div class="container popup cs_order_hold_popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormHold" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" name="order_is_hold" value="<?php echo $order_is_hold?>" />
				<input type="hidden" name="save-text" value="<?php echo $popup_save_btn_name?>" />
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
						<?php
						if($order_is_hold == "Y") {
						?>
						<tr>
							<th>알람</th>
							<td class="text_left td_product_option_name">
								<label><input type="checkbox" name="set_alert" value="Y"/> 표시</label>
								<br>
								<input type="text" name="set_alert_date" class="w80px jqDateDynamic" value="<?=$today?>" readonly="readonly"/>
								<select name="set_alert_time_h">
									<?php
									for ($i = 0; $i < 24; $i++) {
										$selected = (date('H') == make2digit($i)) ? 'selected="selected"' : '';
										echo '<option ' . $selected . '>' . make2digit($i) . '</option>';
									}
									?>
								</select> 시
								<select name="set_alert_time_m">
									<?php
									for ($i = 0; $i < 60; $i+=10) {
										echo '<option>' . make2digit($i) . '</option>';
									}
									?>
								</select> 분
							</td>
						</tr>
						<?php
						}
						?>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save-order-hold" class="large_btn blue_btn  "><?=$popup_save_btn_name?></a>
						<a href="javascript:;" class="large_btn red_btn btn-order-hold-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$("#modal_order_hold").dialog("option", "title", "<?=$popup_title?>");
	CSPopup.CSpopupOrderHoldInit();
</script>

