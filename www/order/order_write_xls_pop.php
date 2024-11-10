<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처 수동 발주 - 업로드 팝업
 */
//Page Info
$pageMenuIdx = 70;
//Page Info
$pagePermissionIdx = 73; //주문관리->발주 페이지
//Init
include_once "../_init_.php";

$mode = "add";

$seller_idx = $_POST["seller_idx"];

$C_Seller = new Seller();
$_seller_info = $C_Seller -> getUseSellerAllData($seller_idx);

if(!$_seller_info) {
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}
?>
<div class="container popup">
	<div class="content write_page xls_pop_content">
		<div class="tb_wrap">
			<table>
				<colgroup>
					<col width="200" />
					<col width="*" />
				</colgroup>
				<tr>
					<th>판매처코드</th>
					<td class="text_left"><?=$seller_idx?></td>
				</tr>
				<tr>
					<th>판매처명</th>
					<td class="text_left"><?=$_seller_info["seller_name"]?></td>
				</tr>
			</table>
		</div>
		<form name="searchForm_xls" id="searchForm_xls" method="post" enctype="multipart/form-data" action="/proc/_order_xls_upload.php" target="xls_hidden_frame">
			<input type="hidden" name="mode" id="xlswrite_mode" value="<?=$mode?>" />
			<input type="hidden" name="act" id="xlswrite_act" value="save" />
			<input type="hidden" name="xls_type" value="order_seller_upload" />
			<input type="hidden" name="seller_idx" value="<?=$seller_idx?>" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<input type="file" name="xls_file" />
						</div>
						<div class="finder_col">
							<a href="javascript:;" class="btn green_btn btn-upload">업로드</a>
						</div>
					</div>
				</div>
				<div class="find_btn empty">
					<div class="table">
						<div class="table_cell">
						</div>
					</div>
				</div>
				<a href="javascript:;" class="find_hide_btn">
					<i class="fas fa-angle-up up_btn"></i>
					<i class="fas fa-angle-down dw_btn"></i>
				</a>
			</div>
		</form>
	</div>
</div>
<script>
	Order.OrderWriteXlsInit();
</script>