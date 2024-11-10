<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 주문 생성 팝업 페이지
 */
//Page Info
$pageMenuIdx = 206;
//Init
include_once "../_init_.php";

$product_seller_group_idx = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx               = $_GET["seller_idx"] || 0;
?>
<div class="container popup cs_order_write_popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm2" id="newOrderForm" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="new_order_write" />
				<input type="hidden" name="dupcheck" id="dupcheck" value="N" />
				<input type="hidden" name="product_idx" value="<?php echo $product_idx?>" />
				<input type="hidden" name="product_option_idx" value="<?php echo $product_option_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="300">
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>판매처 <span class="lb_red">필수</span></th>
							<td class="text_left" colspan="3">
								<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$product_seller_group_idx?>">
									<option value="0">전체그룹</option>
								</select>
								<select name="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="판매처를 선택해주세요.">
								</select>
							</td>
						</tr>
						<tr>
							<th>상품명 <span class="lb_red">필수</span></th>
							<td class="text_left td_product_name" colspan="2">
								<input type="text" name="product_name" class="w100per" value="" readonly="readonly" />
							</td>
							<td>
								<a href="javascript:;" class="btn btn_default btn-product-search-pop" >상품검색</a>
							</td>
						</tr>
						<tr>
							<th>옵션명 <span class="lb_red">필수</span></th>
							<td class="text_left td_product_option_name" colspan="3">
								<input type="text" name="product_option_name" class="w100per" value="" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<th>수량 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="product_option_cnt" class="w100px onlyNumberDynamic" maxlength="4" />
							</td>
							<th>배송비</th>
							<td class="text_left">
								<select name="delivery_is_free">
									<option value="Y">선불</option>
									<option value="N">착불</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>판매금액 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<input type="text" name="order_amt" class="w100px onlyNumberDynamic" maxlength="10" />
							</td>
							<th>판매단가</th>
							<td class="text_left">
								<input type="text" name="product_sale_price" class="w100px onlyNumberDynamic" maxlength="10" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<th>주문번호</th>
							<td class="text_left" colspan="3">
								<input type="text" name="market_order_no" class="w200px not-kor" maxlength="30" />
								미입력 시 주문번호가 자동생성 됩니다.
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="*">
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>구매자 <span class="lb_red">필수</span></th>
							<td class="text_left" colspan="3">
								<input type="text" name="order_name" class="w100px" maxlength="30"/>
							</td>
						</tr>
						<tr>
							<th>전화번호</th>
							<td class="text_left">
								<input type="text" name="order_tp_num" class="w150px" maxlength="15"/>
							</td>
							<th>휴대폰</th>
							<td class="text_left">
								<input type="text" name="order_hp_num" class="w150px" maxlength="15"/>
							</td>
						</tr>
						<tr>
							<th rowspan="3">주소</th>
							<td class="text_left" colspan="3">
								<input type="text" name="order_zipcode" id="order_zipcode" class="w50px" maxlength="5" readonly="readonly" />
								<a href="javascript:;" class="btn blue_btn btn-address-zipcode" data-zipcode-id="order_zipcode" data-addr1-id="order_addr1" data-addr2-id="order_addr2">우편번호찾기</a>
							</td>
						</tr>
						<tr>
							<td class="text_left" colspan="3">
								<input type="text" name="order_addr1" id="order_addr1" class="w100per" maxlength="100" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<td class="text_left" colspan="3">
								<input type="text" name="order_addr2" id="order_addr2" class="w100per" maxlength="100" />
							</td>
						</tr>
						</tbody>
					</table>
				</div>
<!--				<div class="tb_wrap">-->
<!--					<table>-->
<!--						<tbody>-->
<!--						<tr>-->
<!--							<td class="text_left">-->
<!--								<label>-->
<!--									<input type="checkbox" name="is_auto_stock_order" id="is_auto_stock_order" value="Y" checked="checked">-->
<!--									선택 상품 자동 입고예정 등록-->
<!--								</label>-->
<!--							</td>-->
<!--						</tr>-->
<!--						</tbody>-->
<!--					</table>-->
<!--				</div>-->
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="*">
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<td colspan="4">
								최근정보사용 : <span class="set_latest_shipping_info"></span>
								&nbsp;&nbsp;&nbsp;&nbsp;
								<span>
								<a href="javascript:;" class="btn red_btn btn_copy_from_buyer">구매자정보와 동일하게 설정</a>
								</span>
							</td>
						</tr>
						<tr>
							<th>수령자 <span class="lb_red">필수</span></th>
							<td class="text_left" colspan="3">
								<input type="text" name="receive_name" class="w100px" maxlength="30"/>
							</td>
						</tr>
						<tr>
							<th>전화번호</th>
							<td class="text_left">
								<input type="text" name="receive_tp_num" class="w150px" maxlength="15"/>
							</td>
							<th>휴대폰</th>
							<td class="text_left">
								<input type="text" name="receive_hp_num" class="w150px" maxlength="15"/>
							</td>
						</tr>
						<tr>
							<th rowspan="3">주소 <span class="lb_red">필수</span></th>
							<td class="text_left" colspan="3">
								<input type="text" name="receive_zipcode" id="receive_zipcode" class="w50px" maxlength="5" readonly="readonly" />
								<a href="javascript:;" class="btn blue_btn btn-address-zipcode" data-zipcode-id="receive_zipcode" data-addr1-id="receive_addr1" data-addr2-id="receive_addr2">우편번호찾기</a>
							</td>
						</tr>
						<tr>
							<td class="text_left" colspan="3">
								<input type="text" name="receive_addr1" id="receive_addr1" class="w100per" maxlength="100" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<td class="text_left" colspan="3">
								<input type="text" name="receive_addr2" id="receive_addr2" class="w100per" maxlength="100" />
							</td>
						</tr>
						</tbody>
					</table>
				</div>
                <div class="tb_wrap">
                    <table>
                        <colgroup>
                            <col width="120">
                            <col width="*">
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>배송 메세지</th>
                            <td class="text_left">
                                <textarea name="receive_memo" class="w100per h100px"></textarea>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
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
								<textarea name="cs_msg" class="w100per h100px"></textarea>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save-order" class="large_btn blue_btn ">주문 저장</a>
						<a href="javascript:;" class="large_btn red_btn btn-cs-order-write-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	CSPopup.CSPopupOrderWriteInit();
</script>

