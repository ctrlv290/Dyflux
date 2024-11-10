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

$mode                   = "product_add";
$order_pack_idx         = $_POST["order_pack_idx"];
$order_idx              = $_POST["order_idx"];
$order_matching_idx     = $_POST["order_matching_idx"];
$_order                 = $C_CS -> getOrderDetail($order_pack_idx);

if(!$_order || $_order["order_progress_step"] == "ORDER_INVOICE" || $_order["order_progress_step"] == "ORDER_SHIPPED"){
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}else{
	extract($_order);
}

?>
<div class="container popup cs_product_add_popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormProductAdd" id="dyFormProductAdd" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="order_idx" value="<?php echo $order_idx?>" />
				<input type="hidden" name="order_pack_idx" value="<?php echo $order_pack_idx?>" />
				<input type="hidden" name="cs_msg" id="c_cs_msg" value="" />
				<input type="hidden" name="seller_idx" value="<?=$_order["seller_idx"]?>" />
				<input type="hidden" name="seller_type" value="<?=$_order["seller_type"]?>" />
				<div class="tb_wrap grid_tb">
					<table id="grid_list_pop_target" style="width: 100%;">
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
				<div class="tb_wrap grid_tb cs_pop_product_add_wrap">
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
					<a href="javascript:;" id="btn-product-add" class="large_btn <?=$btn_cssClass?>  ">상품추가</a>
					<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	CSPopup.CSPopupProductAddInit();
</script>

