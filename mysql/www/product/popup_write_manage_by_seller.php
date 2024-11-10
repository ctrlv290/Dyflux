<?php

include_once "../_init_.php";

$idx = $_POST["idx"] ? $_POST["idx"] : 0;

$sellerIdx = 0;
$sellerGroupIdx = 0;

$product_idx = "";
$product_option_idx = "";

if($idx) {
	$pdt_conn = new Product();
	$data = $pdt_conn->getSpecialSaleDataByIndex($idx);

	$sellerIdx = $data["seller_idx"];
}
?>

<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="form_write_pdt_mng_by_seller" method="post" class="">
				<input type="hidden" name="mode" value="write_manage_by_seller"/>
				<input type="hidden" name="idx" value="<?=$idx?>"/>
                <input type="hidden" id= "product_idx" name="product_idx" value="<?=$data["product_idx"]?>"/>
                <input type="hidden" id= "product_option_idx" name="product_option_idx" value="<?=$data["product_option_idx"]?>"/>
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
								<select name="seller_group_idx" class="seller_group_idx" data-selected="<?=$sellerGroupIdx?>">
									<option value="0">전체그룹</option>
								</select>
								<select name="seller_idx" class="seller_idx" data-selected="<?=$sellerIdx?>" data-default-value="" data-default-text="판매처를 선택해주세요.">
								</select>
							</td>
						</tr>
						<tr>
							<th>상품명</th>
							<td class="text_left">
								<label id="lb_product_name"><?=$data["product_name"]?></label>
							</td>
						</tr>
						<tr>
							<th>옵션명</th>
							<td class="text_left">
								<label id="lb_product_option_name"><?=$data["product_option_name"]?></label>
							</td>
						</tr>
						<tr>
							<th>판매 단가</th>
							<td class="text_left"><input name="sale_unit_price" type="text" class="w100per" maxlength="20" value="<?=$data["sale_unit_price"]?>"></td>
						</tr>
						<tr>
							<th>판매 배송비</th>
							<td class="text_left"><input name="sale_delivery_fee" type="text" class="w100per" maxlength="20" value="<?=$data["sale_delivery_fee"]?>"></td>
						</tr>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
                        <a href="javascript:;" id="btn_add_product_option" class="large_btn green_btn ">옵션 추가</a>
						<a href="javascript:;" id="btn_write" class="large_btn blue_btn ">저장</a>
						<a href="javascript:;" class="large_btn red_btn btn_close_pop">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>

</script>