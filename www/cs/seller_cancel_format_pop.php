<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 회수 팝업 페이지
 */
//Page Info
$pageMenuIdx = 217;
//Init
include_once "../_init_.php";

$C_Seller = new Seller();
$_seller_list = $C_Seller->getSellerList();

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
<div class="container popup seller_cancel_format_pop">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormFormat" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="update_seller_format" />

				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>판매처</th>
							<td class="text_left">
								<select name="seller_idx" class="seller_idx">
									<option value="">판매처를 선택해주세요.</option>
									<?php
									foreach($_seller_list as $seller){
										echo '<option value="'.$seller["seller_idx"].'">'.$seller["seller_name"].'</option>';
									}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<th>취소일</th>
							<td class="text_left">
								<select name="cancel_date" class="header_sel">
									<?=getHeaderOptionList("")?>
								</select>
							</td>
						</tr>
						<tr>
							<th>주문번호</th>
							<td class="text_left">
								<select name="market_order_no" class="header_sel">
									<?=getHeaderOptionList("")?>
								</select>
							</td>
						</tr>
						<tr>
							<th>주문자</th>
							<td class="text_left">
								<select name="order_name" class="header_sel">
									<?=getHeaderOptionList("")?>
								</select>
							</td>
						</tr>
						<tr>
							<th>상품번호</th>
							<td class="text_left">
								<select name="market_product_no" class="header_sel">
									<?=getHeaderOptionList("")?>
								</select>
							</td>
						</tr>
						<tr>
							<th>상품명</th>
							<td class="text_left">
								<select name="market_product_name" class="header_sel">
									<?=getHeaderOptionList("")?>
								</select>
							</td>
						</tr>
						<tr>
							<th>사유</th>
							<td class="text_left">
								<select name="reason" class="header_sel">
									<?=getHeaderOptionList("")?>
								</select>
							</td>
						</tr>
						<tr>
							<th>관리번호</th>
							<td class="text_left">
								<select name="order_idx" class="header_sel">
									<?=getHeaderOptionList("")?>
								</select>
							</td>
						</tr>
						<tr>
							<th>반품송장번호</th>
							<td class="text_left">
								<select name="return_invoice_no" class="header_sel">
									<?=getHeaderOptionList("")?>
								</select>
							</td>
						</tr>
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
<script src="/js/main.js"></script>
<script src="/js/page/cs.cancel.js"></script>
<script>
	CSCancel.CSCancelFormatPopInit();
</script>
