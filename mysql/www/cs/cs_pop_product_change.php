<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 교환 팝업 페이지
 */
//Page Info
$pageMenuIdx = 206;
//Init
include_once "../_init_.php";

$C_Order = new Order();
$C_CS = new CS();

$mode                   = "product_change";
$order_pack_idx         = $_POST["order_pack_idx"];
$order_idx              = $_POST["order_idx"];
$order_matching_idx     = $_POST["order_matching_idx"];
$_order                 = $C_CS -> getOrderProductDetail($order_idx, $order_matching_idx);


if(!$_order){
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}else{
	extract($_order);
}


//교환타입 가져오기  - CS 사유 (교환타입)
$C_Code = new Code();
$_changeList = $C_Code -> getSubCodeList("CS_REASON_CHANGE");

?>
<div class="container popup cs_product_change_popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormChange" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_idx" value="<?php echo $order_idx?>" />
				<input type="hidden" name="order_matching_idx" value="<?php echo $order_matching_idx?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" name="product_idx" value="<?php echo $product_idx?>" />
				<input type="hidden" name="product_option_idx" value="<?php echo $product_option_idx?>" />
				<input type="hidden" name="cs_reason_code1" value="CS_REASON_CHANGE" />
				<input type="hidden" name="c_product_idx" id="c_product_idx" value="<?php echo $product_idx?>" />
				<input type="hidden" name="cs_msg" id="c_cs_msg" value="" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th rowspan="3" class="text_left">
								교환전
							</th>
							<td class="text_left">
								상품코드
								<input type="text" name="" value="<?=$product_option_idx?>" class="w100px" readonly="readonly" />
								&nbsp;&nbsp;&nbsp;
								수량
								<input type="text" name="" value="<?=$product_option_cnt?>" class="w100px" readonly="readonly" />
								&nbsp;&nbsp;&nbsp;
								판매가
								<input type="text" name="" value="<?=$product_option_sale_price?>" class="w100px" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								상품명
								<input type="text" name="" value="<?=$product_name?>" class="w300px" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								옵션
								<input type="text" name="" value="<?=$product_option_name?>" class="w300px" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<th rowspan="4" class="text_left">
								교환후
							</th>
							<td class="text_left">
								상품코드
								<input type="text" name="c_product_option_idx" value="<?=$product_option_idx?>" class="w100px" readonly="readonly" />
								&nbsp;&nbsp;&nbsp;
								판매가
								<input type="text" name="c_product_sale_price" value="" class="w100px onlyNumberDynamic" <?=($seller_type == "VENDOR_SELLER") ? 'readonly="readonly" onclick="alert(\'벤더사 판매처는 판매가를 수정할 수 없습니다.\');"' : ''?> />
								<input type="hidden" class="c_product_sale_price_unit" value="" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								상품명
								<input type="text" name="c_product_name" value="<?=$product_name?>" class="w300px" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								옵션
								<input type="text" name="c_product_option_name" value="<?=$product_option_name?>" class="w300px" readonly="readonly" />
							</td>
						</tr>
						<tr>
							<td class="text_left">
								수량
								<input type="text" name="c_product_option_cnt" value="<?=$product_option_cnt?>" class="w80px onlyNumberDynamic" />
								&nbsp;&nbsp;&nbsp;
								추가금액
								<input type="text" name="c_add_price" value="" class="w80px onlyNumberDynamic" />
								&nbsp;&nbsp;&nbsp;
								판매단가
								<input type="text" name="c_product_sale_price_unit_cal" value="" class="w80px onlyNumberDynamic c_product_sale_price_unit_cal" readonly="readonly" />
								&nbsp;&nbsp;&nbsp;
								교환타입
								<select name="cs_reason_code2">
									<?php foreach($_changeList as $cc){
										echo '<option value="'.$cc["code"].'">'.$cc["code_name"].'</option>';
									}
									?>
								</select>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</form>
			<form name="searchFormPop_ProductChangeSearch" id="searchFormPop_ProductChangeSearch" method="get">
				<input type="hidden" name="seller_idx" value="<?=$_order["seller_idx"]?>" />
				<input type="hidden" name="seller_type" value="<?=$_order["seller_type"]?>" />
				<div class="find_wrap" style="margin-bottom: 5px;">
					<div class="finder">
						<div class="finder">
							<div class="finder_set">
								<div class="finder_col">
									<span class="text">상품명</span>
									<input type="text" name="product_name" value="<?=$product_name?>" class="w200px enterDoSearchPop_ProductChangeSearch" />
									<select name="search_column">
										<option value="product_option_name">옵션</option>
									</select>
									<input type="text" name="search_keyword" class="w200px enterDoSearchPop_ProductChangeSearch" placeholder="검색어" />
									<a href="javascript:;" id="btn_searchBar_ProductChangeSearch" class="btn blue_btn btn_default">검색</a>
								</div>
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
				<div class="tb_wrap grid_tb cs_pop_product_change_wrap">
					<table id="grid_product_change_list" style="width: 100%;">
					</table>
					<div id="grid_product_change_pager"></div>
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
						</tbody>
					</table>
				</div>
			</form>


			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" id="btn-product-change" class="large_btn <?=$btn_cssClass?>  ">교환</a>
					<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	CSPopup.CSPopupProductChangeInit();
</script>

