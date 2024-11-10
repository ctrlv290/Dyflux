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

$mode                   = "order_copy_one";
$btn_name               = "주문복사";
$btn_cssClass           = "blue_btn";
$js_confirm_text        = "주문복사";
$order_idx              = $_POST["order_idx"];
$product_idx            = $_POST["product_idx"];
$product_option_idx     = $_POST["product_option_idx"];
$_order                 = $C_CS -> getOrderDetailView2($order_idx, $product_option_idx);

if(!$_order){
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}else{
	extract($_order);
}

$readOnly = "";
if($_order["seller_type"] == "VENDOR_SELLER"){
	$readOnly = 'readonly="readonly"';
}

//현재 날짜
$today = date('Y-m-d');
?>
<div class="container popup cs_order_copy_one">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormOrderCopyOne" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_idx" value="<?php echo $order_idx?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" id="js_confirm_text" value="<?php echo $js_confirm_text?>" />
				<input type="hidden" name="copy_product_idx" value="<?php echo $product_idx?>" />
				<input type="hidden" name="cs_msg" id="c_cs_msg" value="" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th rowspan="5">복사전</th>
							<td class="text_left">판매처 : <?=$_order["seller_name"]?></td>
						</tr>
						<tr>
							<td class="text_left">상품코드 : <?=$product_option_idx?></td>
						</tr>
						<tr>
							<td class="text_left">상품명 : <?=$_order["product_name"]?></td>
						</tr>
						<tr>
							<td class="text_left">옵션 : <?=$_order["product_option_name"]?></td>
						</tr>
						<tr>
							<td class="text_left">수량 : <?=$_order["product_option_cnt"]?></td>
						</tr>
						<tr>
							<th rowspan="6">복사후</th>
							<td class="text_left">판매처 :
								<select name="copy_seller_idx" class="copy_seller_idx" data-selected="<?=$_order["seller_idx"]?>"></select>
							</td>
						</tr>
						<tr>
							<td class="text_left">상품코드 : <input type="text" name="copy_product_option_idx" value="<?=$product_option_idx?>" class="w100px" readonly="readonly" /></td>
						</tr>
						<tr>
							<td class="text_left">상품명 : <input type="text" name="copy_product_name" value="<?=$_order["product_name"]?>" class="w200px" readonly="readonly" /></td>
						</tr>
						<tr>
							<td class="text_left">옵션 : <input type="text" name="copy_product_option_name" value="<?=$_order["product_option_name"]?>" class="w200px" readonly="readonly" /></td>
						</tr>
						<tr>
							<td class="text_left">수량 : <input type="text" name="copy_product_option_cnt" value="<?=$_order["product_option_cnt"]?>" class="w80px copy_product_option_cnt" /></td>
						</tr>
						<tr>
							<td class="text_left">판매금액 : <input type="text" name="copy_product_option_sale_price" value="<?=$_order["product_option_sale_price"]?>" class="w80px copy_product_option_sale_price" <?=$readOnly?> data-price="<?=$_order["product_option_sale_price"]?>" /></td>
						</tr>
						</tbody>
					</table>
				</div>
			</form>
			<form name="searchFormPop_ProductAddSearch" id="searchFormPop_ProductAddSearch" method="get">
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
									<a href="javascript:;" id="btn_searchBar_ProductAddSearch" class="btn blue_btn btn_default">검색</a>
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
				<div class="tb_wrap grid_tb">
					<table id="grid_product_change_list" style="width: 100%;">
					</table>
					<div id="grid_product_change_pager"></div>
				</div>
			</form>

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
					</tbody>
				</table>
			</div>

			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" id="btn-product-add" class="large_btn blue_btn  "><?=$btn_name?></a>
					<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	CSPopup.CSPopupOrderCopyOneInit();
</script>

