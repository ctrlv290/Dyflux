<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주관리 - 이메일 발송 페이지
 */
//Page Info
$pageMenuIdx = 189;
//Init
include_once "../_init_.php";


$mode                           = "send_order_download_email";
$supplier_idx                   = $_POST["supplier_idx"];
$order_download_file_idx        = $_POST["order_download_file_idx"];

$C_Order = new Order();
$C_Supplier = new Supplier();

$_supplier_view = $C_Supplier -> getSupplierData($supplier_idx);


if(!$order_download_file_idx){
	$_file_view = $C_Order->getLastOrderDownloadFile($supplier_idx);
}else{
	$_file_view = $C_Order->getLastOrderDownloadFile($supplier_idx, $order_download_file_idx);
}

$receiver_email_ary = array();
if(!$_supplier_view || !$_file_view){
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}else{

	extract($_file_view);

	$_supplier_view = $C_Supplier -> getSupplierData($supplier_idx);
}

?>
<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormEmail" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_download_file_idx" value="<?php echo $order_download_file_idx?>" />
				<input type="hidden" name="supplier_idx" id="email_pop_supplier_idx" value="<?php echo $supplier_idx?>" />
				<!--				<input type="hidden" name="stock_order_document_short_url" id="stock_order_document_short_url" value="" />-->
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>공급처</th>
							<td class="text_left"><?=$_supplier_view["supplier_name"]?></td>
						</tr>
						<tr>
							<th>수신이메일</th>
							<td class="text_left">
								<input type="text" name="supplier_email" class="w100per" value="<?=$_supplier_view["supplier_email_order"]?>" />
							</td>
						</tr>
						<tr>
							<th>제목</th>
							<td class="text_left"><input type="text" name="email_title" class="w300px" value="거래명세서 (주)덕윤" /></td>
						</tr>
						<tr>
							<th>내용</th>
							<td class="text_left"><textarea name="email_content" class="w100per">거래상세내역</textarea></td>
						</tr>

						<tr>
							<th>첨부파일</th>
							<td class="text_left">
								<a href="javascript:;" class="btn btn-order-download-xls-down" data-idx="<?=$order_download_file_idx?>">다운받기</a>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-send-email" class="large_btn blue_btn ">보내기</a>
						<a href="javascript:;" class="large_btn red_btn btn-stock-order-email-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	OrderShipped.OrderDownloadEmailSendPopInit();
</script>

