<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주관리 - 이메일 발송 페이지
 */
//Page Info
$pageMenuIdx = 230;
//Init
include_once "../_init_.php";

$isMultiSend     = false;
$mode            = "tax_send_email";

$tax_type     = $_POST["tax_type"];
$target_idx_list = $_POST["target_idx_list"];
$file_idx_list   = $_POST["file_idx_list"];

if(count($target_idx_list) > 1){
	$isMultiSend = true;
	$mode        = "tax_send_email_multi";
}else{

	$target_idx = $target_idx_list[0];

	if($tax_type == "PURCHASE"){
		$C_Supplier = new Supplier();
		$_view = $C_Supplier -> getSupplierData($target_idx);
		$target_name = $_view["supplier_name"];
		$email = $_view["supplier_email_order"];
		$file_idx = $file_idx_list[0];
	}else{
		$C_Vendor = new Vendor();
		$_view = $C_Vendor->getVendorData($target_idx);
		$target_name = $_view["vendor_name"];
		$email = $_view["vendor_email_order"];
		$file_idx = $file_idx_list[0];
	}
}


?>
<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormEmail" method="post" class="<?=$mode?>">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<input type="hidden" name="tax_type" value="<?=$tax_type?>" />
				<?php if(!$isMultiSend) { ?>
					<input type="hidden" name="file_idx" value="<?=$file_idx?>" />
					<input type="hidden" name="target_idx" id="target_idx" value="<?=$target_idx?>" />
				<?php } else {
					foreach($target_idx_list as $key => $target_idx){
						echo '<input type="hidden" name="target_list[]" value="'.$target_idx.'|'.$file_idx_list[$key].'" />';
					}
				}
				?>
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<?php if(!$isMultiSend) { ?>
							<tr>
								<th>공급처</th>
								<td class="text_left"><?=$target_name?></td>
							</tr>
							<tr>
								<th>수신이메일</th>
								<td class="text_left">
									<input type="text" name="target_email" class="w100per" value="<?=$email?>" />
								</td>
							</tr>
						<?php } ?>
						<tr>
							<th>제목</th>
							<td class="text_left"><input type="text" name="email_title" class="w300px" value="" /></td>
						</tr>
						<tr>
							<th>내용</th>
							<td class="text_left"><textarea name="email_content" class="w100per"></textarea></td>
						</tr>
						<?php if(!$isMultiSend) { ?>
							<tr>
								<th>첨부파일</th>
								<td class="text_left">
									<a href="javascript:;" class="btn btn-xls-down" data-idx="<?=$file_idx?>">다운받기</a>
								</td>
							</tr>
						<?php } ?>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-send-email" class="large_btn blue_btn ">보내기</a>
						<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	SettleTax.TaxEmailSendPopInit();
</script>

