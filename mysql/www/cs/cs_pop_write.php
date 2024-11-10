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

$mode               = "insert_cs";
$order_idx          = $_POST["order_idx"];
$order_pack_idx     = $_POST["order_pack_idx"];
$order_matching_idx = $_POST["order_matching_idx"];
$product_idx        = $_POST["product_idx"];
$product_option_idx = $_POST["product_option_idx"];


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
						<tr>
							<th>붙여넣기</th>
							<td class="text_left">
								<a href="javascript:;" class="link_blue btn-cs-paste" data-paste-from="product_name">상품명</a>
								<a href="javascript:;" class="link_blue btn-cs-paste" data-paste-from="product_option_name">옵션</a>
								<a href="javascript:;" class="link_blue btn-cs-paste" data-paste-from="product_full_name">선택한 상품명 + 옵션</a>
							</td>
						</tr>
						<tr>
							<th rowspan="5">첨부파일</th>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn_cs_file1" id="btn_cs_file1">
									파일업로드
								</a>
								<span class="uploaded-file span_cs_file1"></span>
								<input type="hidden" name="cs_file1" id="cs_file1" value="" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn_cs_file2" id="btn_cs_file2">
									파일업로드
								</a>
								<span class="uploaded-file span_cs_file2"></span>
								<input type="hidden" name="cs_file2" id="cs_file2" value="" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn_cs_file3" id="btn_cs_file3">
									파일업로드
								</a>
								<span class="uploaded-file span_cs_file3"></span>
								<input type="hidden" name="cs_file3" id="cs_file3" value="" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn_cs_file4" id="btn_cs_file4">
									파일업로드
								</a>
								<span class="uploaded-file span_cs_file4"></span>
								<input type="hidden" name="cs_file4" id="cs_file4" value="" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								<a href="javascript:;" class="btn green_btn btn_relative btn_cs_file5" id="btn_cs_file5">
									파일업로드
								</a>
								<span class="uploaded-file span_cs_file5"></span>
								<input type="hidden" name="cs_file5" id="cs_file5" value="" />
							</td>
						</tr>
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
	CSPopup.CSPopupCSWritePopInit();
</script>

