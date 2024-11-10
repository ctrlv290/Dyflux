<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처별송장등록 - 포맷설정 팝업 페이지
 */
//Page Info
$pageMenuIdx = 224;
//Init
include_once "../_init_.php";

$C_Seller = new Seller();
$_seller_list = $C_Seller->getSellerList();

$seller_idx  = $_GET["seller_idx"];

if($seller_idx) {
	$_format = $C_Seller->getSellerInvoiceFormat($seller_idx);

	if (!$_format) {
		$_format = $GL_SELLER_INVOICE_FORMAT;
	}
}

function getHeaderOptionList($val){

	$returnValue = array();
	$returnValue[] = '<option value="">값 없음</option>';

	if($val == "") $val = "A";

	foreach(excelColumnRange('A', 'AZ') as $char){
		$selected = ($val == $char) ? 'selected="selected"' : '';
		$returnValue[] = '<option value="'.$char.'" '.$selected.'>'.$char.'</option>';
	}

	return implode("\n", $returnValue);
}
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
	<div class="container popup">
		<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
		<div class="content write_page">
			<div class="content_wrap">
				<form name="dyFormFormat" id="dyFormFormat" method="post" class="<?php echo $mode?>" action="seller_invoice_pop_proc.php">
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150" />
							<col width="*" />
						</colgroup>
						<tbody>
						<tr>
							<th>판매처</th>
							<td class="text_left">
								<select name="seller_idx" class="seller_idx">
									<option value="">판매처를 선택해주세요.</option>
									<?php
									foreach($_seller_list as $seller){
										$selected = ($seller_idx == $seller["seller_idx"]) ? "selected" : "";
										echo '<option value="'.$seller["seller_idx"].'" '.$selected.'>'.$seller["seller_name"].'</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<th>헤더출력</th>
							<td class="text_left">
								<select name="print_header">
									<option value="Y" <?=($_format["print_header"] == "Y") ? "selected" : ""?>>출력</option>
									<option value="N" <?=($_format["print_header"] == "N") ? "selected" : ""?>>출력안함</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>상단공백</th>
							<td class="text_left">
								<select name="margin_top">
									<?php
									for($i=0;$i<11;$i++){
										$selected = ($_format["margin_top"] == $i) ? "selected" : "";
										echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
									}
									?>
								</select>
							</td>
						</tr>
						</tbody>
					</table>
					<br>
					<table>
						<colgroup>
							<col width="100" />
							<col width="*" />
							<col width="*" />
						</colgroup>
						<thead>
						<tr>
							<th>컬럼</th>
							<th>헤더</th>
							<th>값</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach(excelColumnRange('A', 'AZ') as $char) {
							$val = explode("|", $_format[$char]);
							$header = $val[0];
							$value = $val[1];
						?>
						<tr>
							<th><?=$char?></th>
							<td><input type="text" class="w100" name="header_<?=$char?>" value="<?=$header?>" /></td>
							<td><input type="text" class="w100" name="value_<?=$char?>" value="<?=$value?>" /></td>
						</tr>
						<?php
						}
						?>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save" class="large_btn ">저장</a>
						<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
				</form>
			</div>
		</div>
	</div>

	<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
	<script src="/js/main.js"></script>
	<script src="/js/String.js"></script>
	<script src="/js/FormCheck.js"></script>
	<script src="/js/page/common.function.js"></script>
	<script src="/js/page/info.group.js"></script>
	<script src="/js/jquery.sumoselect.min.js"></script>
	<link rel="stylesheet" href="/css/sumoselect.min.css">
	<script src="/js/page/order.order.js"></script>
	<script>
		window.name = "order_seller_invoice";
		Order.SellerInvoicePopInit();
	</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>