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

$mode                   = "set_invoice_priority";
$order_idx              = $_POST["order_idx"];
$order_pack_idx         = $_POST["order_pack_idx"];
$product_option_idx     = $_POST["product_option_idx"];
$_order                 = $C_CS -> getOrderDetail($order_idx);

$current_invoice_priority  = $_order["invoice_priority"];

$order_is_return_due    = "";
$popup_title            = "";
$popup_save_btn_name    = "";

if($_order){
	if($current_invoice_priority == 0){
		//우선순위 설정
		$popup_title         = "우선순위 설정";
		$popup_save_btn_name = "우선순위 설정";
		$js_confirm_text     = "우선순위 설정";

		$_list = $C_CS -> getOrderPriorityList($order_idx, $product_option_idx);
	}else{
		//우선순위 설정
		$popup_title = "우선순위 해제";
		$popup_save_btn_name = "우선순위 해제";
		$js_confirm_text     = "우선순위 해제";
	}
}else{
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}

//현재 날짜
$today = date('Y-m-d');
?>
<div class="container popup cs_invoice_priority_pop">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormHold" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_idx" value="<?php echo $order_idx?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" name="product_option_idx" value="<?php echo $product_option_idx?>" />
				<input type="hidden" name="save-text" value="<?php echo $popup_save_btn_name?>" />
				<input type="hidden" id="js_confirm_text" value="<?php echo $js_confirm_text?>" />
				<?php if($current_invoice_priority == 0){?>
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="80">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th class="">
								기존<br>
								우선순위<br>
								목록
							</th>
							<td class="text_left">
								<table class="font_small priority_list_table">
									<colgroup>
										<col width="60" />
										<col width="80" />
										<col width="100" />
										<col width="*" />
										<col width="*" />
										<col width="100" />
										<col width="100" />
									</colgroup>
									<thead>
									<tr>
										<th>우선순위</th>
										<th>관리번호</th>
										<th>발주일</th>
										<th>판매처</th>
										<th>수령자</th>
										<th>설정일</th>
										<th>설정자</th>
									</tr>
									</thead>
									<tbody>
									<?php foreach($_list as $row){?>
										<tr>
											<td><?=$row["invoice_priority"]?></td>
											<td><?=$row["order_idx"]?></td>
											<td><?=$row["order_progress_step_accept_date"]?></td>
											<td><?=$row["seller_name"]?></td>
											<td><?=$row["receive_name"]?></td>
											<td><?=$row["invoice_priority_date"]?></td>
											<td><?=$row["member_id"]?></td>
										</tr>
									<?php }?>
									</tbody>
								</table>
								<br>
								<p class="info_txt">동일 상품의 주문에 대해서만 우선순위를 보여줍니다.</p>
								<p class="info_txt">우선순위 숫자가 클 수록 먼저 송장출력됩니다.</p>
							</td>
						</tr>
						<tr>
							<th class="">
								설정할<br>
								우선순위<br>
								위치
							</th>
							<td class="text_left">
								<label>
									<input type="radio" name="priority_type" value="top" checked="checked" />우선순위 최상<br>
								</label>
								<label>
									<input type="radio" name="priority_type" value="bottom" />우선순위 최하(우선순위 없는 주문 보다는 먼저 송장 출력됩니다.)<br>
								</label>
								<label>
									<input type="radio" name="priority_type" value="position" />위치지정 : 우선순위
										<input type="text" name="position_number" class="w40px onlyNumberDynamic" value="" /> 번 주문 다음으로 배송.
								</label>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<?php }?>
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
						<a href="javascript:;" id="btn-save-priority" class="large_btn blue_btn  "><?=$popup_save_btn_name?></a>
						<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	$("#modal_common").dialog("option", "title", "<?=$popup_title?>");
	CSPopup.CSPopupInvoicePriorityInit();
</script>

