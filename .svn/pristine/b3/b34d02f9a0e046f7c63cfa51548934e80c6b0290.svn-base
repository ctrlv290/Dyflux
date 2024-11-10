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
$C_Code = new Code();

$mode               = "selected_insert_cs";

//현재 날짜
$today = date('Y-m-d');

//CS 타입 리스트
$_cs_list = $C_Code -> getSubCodeList('CS_TYPE');
?>
<div class="container popup cs_order_hold_popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormCSInsert" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_idx" value="<?php echo $order_idx?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" name="order_matching_idx" value="<?php echo $order_matching_idx?>" />
				<input type="hidden" name="product_idx" value="<?php echo $product_idx?>" />
				<input type="hidden" name="product_option_idx" value="<?php echo $product_option_idx?>" />
				<input type="hidden" name="cs_task" value="NORMAL" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>타입</th>
							<td class="text_left">
								<select name="cs_type">
									<?php
									foreach($_cs_list as $row){
										echo '<option value="'.$row["code"].'">'.$row["code_name"].'</option>';
									}
									?>
								</select>
							</td>
						</tr>
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
						<a href="javascript:;" id="btn-save-order-write" class="large_btn blue_btn  ">C/S 남기기</a>
						<a href="javascript:;" class="large_btn red_btn btn-cs-write-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	CSList.CSPopupCSWritePopInit();
</script>

