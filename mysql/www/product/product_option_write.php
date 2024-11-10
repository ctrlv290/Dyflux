<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 추가/수정 페이지
 */
//Page Info
$pageMenuIdx = 176;
//Init
include_once "../_init_.php";


$mode                   = "add";
$product_idx            = $_POST["product_idx"];
$product_option_idx     = $_POST["product_option_idx"];

$product_option_sale_price_A_percent = 0;
$product_option_sale_price_B_percent = 0;
$product_option_sale_price_C_percent = 0;
$product_option_sale_price_D_percent = 0;
$product_option_sale_price_E_percent = 0;

$C_Product = new Product();

$_product_view = $C_Product -> getProductData($product_idx);

if(!$_product_view){
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}else{

	extract($_product_view);

	//벤더사 등급명, 할인율 가져오기
	$C_VendorGrade = new VendorGrade();
	$_vendor_grade_list = $C_VendorGrade->getVendorGradeList();

	foreach($_vendor_grade_list as $vg)
	{
		$tmp = "product_option_sale_price_".$vg["vendor_grade"]."_percent";
		$tmp_grade_name = "product_option_sale_price_".$vg["vendor_grade"]."_name";
		$$tmp = $vg["vendor_grade_discount"];
		$$tmp_grade_name = $vg["vendor_grade_name"];
	}

	//상품 옵션 수정 여부
	if($product_option_idx){
		$_product_option_view = $C_Product -> getProductOptionData($product_option_idx);

		if(!$_product_option_view){
			header('HTTP/1.1 500 Internal Server Error');
			header('Content-Type: text/html; charset=UTF-8');
			die("Error");
		}else{

			$mode = "mod";
			extract($_product_option_view);

		}
	}
}

?>
<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm2" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="dupcheck" id="dupcheck" value="N" />
				<input type="hidden" name="product_idx" value="<?php echo $product_idx?>" />
				<input type="hidden" name="product_option_idx" value="<?php echo $product_option_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>대표상품코드</th>
							<td class="text_left"><?=$product_idx?></td>
						</tr>
						<tr>
							<th>대표상품명</th>
							<td class="text_left"><?=$product_name?></td>
						</tr>
						<?php
						if($mode == "mod") {
						?>
							<tr>
								<th>옵션코드</th>
								<td class="text_left"><?=$product_option_idx?></td>
							</tr>
							<tr>
								<th>옵션명</th>
								<td class="text_left"><input type="text" name="product_option_name" class="w300px" value="<?=$product_option_name?>" /></td>
							</tr>
						<?php
						}
						?>
						<?php
						if($mode == "add") {
						?>
						<tr>
							<th>옵션명</th>
							<td class="text_left">
								<div class="sub_desc">옵션을 등록 하실 때 ','를 이용해서 입력주세요.</div>
								<table>
									<colgroup>
										<col width="100px" />
										<col width="*" />
									</colgroup>
									<tbody>
									<tr>
										<th class="text_left">옵션1 <span class="lb_red">필수</span></th>
										<td class="text_left">
											<input type="text" class="w100per product_selectize product_option_mix_1" name="product_option_mix_1" value="" />
											<span class="info_txt col_blue">예: 빨강, 파랑, 노랑 (빈칸 입력 불가)</span>
										</td>
									</tr>
									<tr>
										<th class="text_left">옵션2</th>
										<td class="text_left">
											<input type="text" class="w100per product_selectize product_option_mix_2" name="product_option_mix_2" value="" />
											<span class="info_txt col_blue">예: XL, L (빈칸 입력 불가)</span>
										</td>
									</tr>
									<tr>
										<th class="text_left">옵션3</th>
										<td class="text_left">
											<input type="text" class="w100per product_selectize product_option_mix_3" name="product_option_mix_3" value="" />
											<span class="info_txt col_blue">예: 긴팔, 반팔 (빈칸 입력 불가)</span>
										</td>
									</tr>
									</tbody>
								</table>
								<div class="desc_box_red option_mix_result"></div>
							</td>
						</tr>
						<?php
						}
						?>
						<tr>
							<th>판매가<br>(벤더사 등급별)</th>
							<td class="text_left">
								<table>
									<colgroup>
										<col width="120px" />
										<col width="*" />
									</colgroup>
									<tbody>
									<tr>
										<th class="text_left">판매기준가 <span class="lb_red">필수</span></th>
										<td class="text_left">
											<input type="text" class="w100px product_option_sale_price_mask" name="product_option_sale_price_default" maxlength="10" value="<?=$product_option_sale_price?>" />
											<a href="javascript:;" class="btn btn-product-option-sale-price-calculate">할인율 적용</a>
										</td>
									</tr>
									<tr>
										<th class="text_left"><?=$product_option_sale_price_A_name?> 판매가</th>
										<td class="text_left">
											<input type="text" class="w100px product_option_sale_price_mask product_option_sale_price_cal" name="product_option_sale_price_A" value="<?=$product_option_sale_price_A?>"/>
											<i class="fas fa-caret-left"></i>
											<input type="text" class="w30px onlyNumberDynamic product_option_sale_price_cal_per" name="product_option_sale_price_A_per" maxlength="3" value="<?=$product_option_sale_price_A_percent?>" />% 할인
										</td>
									</tr>
									<tr>
										<th class="text_left"><?=$product_option_sale_price_B_name?> 판매가</th>
										<td class="text_left">
											<input type="text" class="w100px product_option_sale_price_mask product_option_sale_price_cal" name="product_option_sale_price_B" value="<?=$product_option_sale_price_B?>" />
											<i class="fas fa-caret-left"></i>
											<input type="text" class="w30px onlyNumberDynamic product_option_sale_price_cal_per" name="product_option_sale_price_B_per" maxlength="3" value="<?=$product_option_sale_price_B_percent?>" />% 할인
										</td>
									</tr>
									<tr>
										<th class="text_left"><?=$product_option_sale_price_C_name?> 판매가</th>
										<td class="text_left">
											<input type="text" class="w100px product_option_sale_price_mask product_option_sale_price_cal" name="product_option_sale_price_C" value="<?=$product_option_sale_price_C?>" />
											<i class="fas fa-caret-left"></i>
											<input type="text" class="w30px onlyNumberDynamic product_option_sale_price_cal_per" name="product_option_sale_price_C_per" maxlength="3" value="<?=$product_option_sale_price_C_percent?>" />% 할인
										</td>
									</tr>
									<tr>
										<th class="text_left"><?=$product_option_sale_price_D_name?> 판매가</th>
										<td class="text_left">
											<input type="text" class="w100px product_option_sale_price_mask product_option_sale_price_cal" name="product_option_sale_price_D" value="<?=$product_option_sale_price_D?>" />
											<i class="fas fa-caret-left"></i>
											<input type="text" class="w30px onlyNumberDynamic product_option_sale_price_cal_per" name="product_option_sale_price_D_per" maxlength="3" value="<?=$product_option_sale_price_D_percent?>" />% 할인
										</td>
									</tr>
									<tr>
										<th class="text_left"><?=$product_option_sale_price_E_name?> 판매가</th>
										<td class="text_left">
											<input type="text" class="w100px product_option_sale_price_mask product_option_sale_price_cal" name="product_option_sale_price_E" value="<?=$product_option_sale_price_E?>" />
											<i class="fas fa-caret-left"></i>
											<input type="text" class="w30px onlyNumberDynamic product_option_sale_price_cal_per" name="product_option_sale_price_E_per" maxlength="3" value="<?=$product_option_sale_price_E_percent?>" />% 할인
										</td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
						<?php
						if($product_sale_type == "SELF") {
							//사입/자체 제품일 경우
						?>
						<tr>
							<th>재고경고수량</th>
							<td class="text_left">
								<input type="text" class="w100px product_option_warning_count onlyNumberDynamic" name="product_option_warning_count" maxlength="8" value="<?=$product_option_warning_count?>"/>
							</td>
						<tr>
							<th>재고위협수량</th>
							<td class="text_left">
								<input type="text" class="w100px product_option_danger_count onlyNumberDynamic" name="product_option_danger_count" maxlength="8" value="<?=$product_option_danger_count?>"/>
							</td>
						</tr>
						<?php
						}else{
							//위탁 판매 일 경우
						?>
						<tr>
							<th>매입가</th>
							<td class="text_left">
								<input type="text" class="w100px product_option_purchase_price" name="product_option_purchase_price" maxlength="8" value="<?=$product_option_purchase_price?>" />
								<span class="info_txt col_blue">모든 옵션에 동일한 매입가가 입력됩니다.</span>
							</td>
						</tr>
						<?php
						}
						?>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save-option" class="large_btn blue_btn ">저장</a>
						<a href="javascript:;" class="large_btn red_btn btn-product-option-write-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	Product.ProductOptionWriteInit();
</script>

