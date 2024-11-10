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


$mode                   = "send_stock_order_email";
$stock_order_idx        = $_POST["stock_order_idx"];

$C_Stock = new Stock();
$C_Supplier = new Supplier();

$_stock_order_view = $C_Stock -> getStockOrderData($stock_order_idx);

$receiver_email_ary = array();
if(!$_stock_order_view){
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}else{

	extract($_stock_order_view);

	$_supplier_view = $C_Supplier -> getSupplierData($supplier_idx);
}

?>
<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormEmail" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="stock_order_idx" value="<?php echo $stock_order_idx?>" />
				<input type="hidden" name="stock_order_file_idx" value="<?php echo $stock_order_file_idx?>" />
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
								<select name="supplier_email">

								</select>
							</td>
						</tr>
						<tr>
							<th>제목</th>
							<td class="text_left"><input type="text" name="email_title" class="w300px" value="" /></td>
						</tr>
						<tr>
							<th>내용</th>
							<td class="text_left"><textarea name="email_content" class="w100per"></textarea></td>
						</tr>

						<tr>
							<th>첨부파일</th>
							<td class="text_left">
								<a href="javascript:;" class="btn btn-stock-order-xls-down">다운받기</a>
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
	StockOrder.StockOrderEmailSendPopInit(<?=$stock_order_idx?>);
</script>

