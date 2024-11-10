<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 교환 팝업 페이지
 */
//Page Info
$pageMenuIdx = 122;
//Init
include_once "../_init_.php";

$mode = "transaction_sale_adjust";
$settle_type = $_POST["settle_type"];


?>
<div class="container popup transaction_pop">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm2" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="product_idx" value="" />
				<input type="hidden" name="product_option_idx" value="" />
				<input type="hidden" name="settle_type" value="<?=$settle_type?>" />
				<input type="hidden" name="supplier_idx" value="" />
				<div class="tb_wrap">
					<table autofocus="autofocus">
						<colgroup>
							<col width="100">
							<col width="100">
							<col width="*">
							<col width="100">
							<col width="100">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th colspan="2">날짜 <span class="lb_red">필수</span></th>
							<td colspan="4" class="text_left"><input type="text" name="settle_date" class="adjust_settle_date" value="" readonly="readonly" /></td>
						</tr>
						<tr>
							<th colspan="2">판매처 <span class="lb_red">필수</span></th>
							<td colspan="4" class="text_left">
								<select name="product_seller_group_idx" class="adjust_product_seller_group_idx" data-selected="0">
									<option value="0">전체 그룹</option>
								</select>
								<select name="seller_idx" class="adjust_seller_idx" data-selected="0" data-default-value="" data-default-text="판매처를 선택하세요.">
									<option>판매처를 선택하세요.</option>
								</select>
							</td>
						</tr>
						<tr>
							<th colspan="2">상품명 <span class="lb_red">필수</span></th>
							<td colspan="4" class="text_left">
								<input type="text" name="product_name" class="w300px" value="" readonly="readonly" />
								<a href="javascript:;" class="btn btn_default btn-product-search-pop" >상품검색</a>
							</td>
						</tr>
						<tr>
							<th colspan="2">옵션명 <span class="lb_red">필수</span></th>
							<td colspan="4" class="text_left">
								<input type="text" name="product_option_name" class="w300px" value="" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<th colspan="2">공급처명 <span class="lb_red">필수</span></th>
							<td colspan="4" class="text_left">
								<input type="text" name="supplier_name" class="w300px" value="" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<th colspan="2">판매단가</th>
							<td class="text_left">
								<input type="text" name="order_unit_price" class="w150px onlyNumber" value="0" tabindex="5" />
							</td>
							<th colspan="2">판매수량</th>
							<td class="text_left">
								<input type="text" name="product_option_cnt" class="w50px onlyNumber" value="0" tabindex="6" />
							</td>
						</tr>
						<tr>
							<th rowspan="2">판매가</th>
							<th>판매가</th>
							<td class="text_left">
								<input type="text" name="settle_sale_supply" class="w150px onlyNumber" value="0" tabindex="7" />
							</td>
							<th rowspan="2">판매수수료</th>
							<th>수수료</th>
							<td class="text_left">
								<input type="text" name="settle_sale_commission_ex_vat" class="w150px onlyNumber" value="0" tabindex="9" />
							</td>
						</tr>
						<tr>
							<th>공급가액</th><!--매출공급가액-->
							<td class="text_left">
								<input type="text" name="settle_sale_supply_ex_vat" class="w150px onlyNumber" value="0" tabindex="8" />
							</td>
							<th>공급가액</th><!--판매수수료-->
							<td class="text_left">
								<input type="text" name="settle_sale_commission_in_vat" class="w150px onlyNumber" value="0" tabindex="10" />
							</td>
						</tr>
						<tr>
							<th rowspan="2">매출배송비</th>
							<th>배송비</th>
							<td class="text_left">
								<input type="text" name="settle_delivery_in_vat" class="w150px onlyNumber" value="0" tabindex="11" />
							</td>
							<th rowspan="2">배송비<br>수수료</th>
							<th>수수료</th>
							<td class="text_left">
								<input type="text" name="settle_delivery_commission_ex_vat" class="w150px onlyNumber" value="0" tabindex="13" />
							</td>
						</tr>
						<tr>
							<th>공급가액</th><!--배송비-->
							<td class="text_left">
								<input type="text" name="settle_delivery_ex_vat" class="w150px onlyNumber" value="0" tabindex="12" />
							</td>
							<th>공급가액</th><!--판매배송비<br>수수료-->
							<td class="text_left">
								<input type="text" name="settle_delivery_commission_in_vat" class="w150px onlyNumber" value="0" tabindex="14" />
							</td>
						</tr>
						<tr>
							<th rowspan="2">매입단가</th>
							<th>매입가</th>
							<td class="text_left">
								<input type="text" name="settle_purchase_unit_supply" class="w150px onlyNumber" value="0" tabindex="15" />
							</td>
							<th rowspan="2">매입가</th>
							<th>단가</th>
							<td class="text_left">
								<input type="text" name="settle_purchase_supply" class="w150px onlyNumber" value="0" tabindex="17" />
							</td>
						</tr>
						<tr>
							<th>공급가액</th><!--매입단가(매출원가)<br>공급가액-->
							<td class="text_left">
								<input type="text" name="settle_purchase_unit_supply_ex_vat" class="w150px onlyNumber" value="0" tabindex="16" />
							</td>
							<th>공급가액</th><!--매입가(매출원가)<br>공급가액-->
							<td class="text_left">
								<input type="text" name="settle_purchase_supply_ex_vat" class="w150px onlyNumber" value="0" tabindex="18" />
							</td>
						</tr>
						<tr>
							<th rowspan="2">매입 배송비</th>
							<th>배송비</th>
							<td class="text_left">
								<input type="text" name="settle_purchase_delivery_in_vat" class="w150px onlyNumber" value="0" tabindex="19" />
							</td>
							<th colspan="2">정산/배송비</th>
							<td class="text_left">
								<input type="text" name="settle_settle_amt" class="w150px onlyNumber" value="0" tabindex="21" />
							</td>
						</tr>
						<tr>
							<th>공급가액</th><!--매입 배송비-->
							<td class="text_left">
								<input type="text" name="settle_purchase_delivery_ex_vat" class="w150px onlyNumber" value="0" tabindex="20" />
							</td>
							<th colspan="2">광고비</th>
							<td class="text_left">
								<input type="text" name="settle_ad_amt" class="w150px onlyNumber" value="0" tabindex="22" />
							</td>
						</tr>
						<tr>
							<th colspan="2">매출이익</th>
							<td class="text_left">
								<input type="text" name="settle_sale_profit" class="w150px onlyNumber" value="0" tabindex="23" />
							</td>
							<td colspan="3"></td>
						</tr>
						<tr>
							<th colspan="2">메모</th>
							<td colspan="4" class="text_left">
								<textarea name="settle_memo" class="w100per h100px" tabindex="24"></textarea>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</form>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" id="btn-save" class="large_btn ">저장</a>
					<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	SettleTransaction.TransactionAdjustPopInit();
</script>

