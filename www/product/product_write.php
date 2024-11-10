<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 신상품등록 등록/수정 페이지
 */
//Page Info
$pageMenuIdx = 36;
//Permission IDX
$permissionMenuIdx = 54;
//Init
include_once "../_init_.php";

$mode                     = "add";
$product_idx              = $_GET["product_idx"];
$product_sale_type        = "SELF";
$product_tax_type         = "TAXATION";
$product_category_l_idx   = "";
$product_category_m_idx   = "";
$product_delivery_type    = "COURIER_DELIVERY";
$product_vendor_show      = "HIDE";
$product_vendor_show_type = "ALL";
$supplier_group_idx       = 0;
$supplier_idx             = 0;
$seller_group_idx         = 0;
$seller_idx               = 0;
$product_img_main         = 0;

$product_vendor_show_list_name = "";
$product_vendor_show_list = "";

$C_Product = new Product();

if($product_idx)
{
	//상품 기본 정보
	$_view = $C_Product->getProductData($product_idx);
	if($_view)
	{
		$mode = "mod";
		$pageMenuIdx = 177;

		//이전페이지 세팅
		$param = urldecode($_GET["param"]);

		extract($_view);

		//상품 쇼핑몰 상세페이지 가져오기
		$_detail_list = $C_Product -> getProductDetailList($product_idx);

		//벤더사 노출 : 특정업체 노출 일 경우 업체 리스트 가져오기
		if($product_vendor_show == "SELECTED") {
			$_vendor_selected_list = $C_Product->getProductVendorSelectedList($product_idx);
			if($_vendor_selected_list) {
				$product_vendor_show_list      = implode(",", array_map(function ($e) {
					return $e['vendor_idx'];
				}, $_vendor_selected_list));
				$product_vendor_show_list_name = implode(",",array_map(function ($e) {
					return $e['vendor_name'];
				}, $_vendor_selected_list));
			}
		}

		if($supplier_group_idx == ""){
			$supplier_group_idx = 0;
		}

		if($seller_group_idx == ""){
			$seller_group_idx = 0;
		}

		if($product_vendor_show == "ALL" || $product_vendor_show == "SELECTED"){
			$product_vendor_show_type = $product_vendor_show;
			$product_vendor_show = "SHOW";
		}else{
			$product_vendor_show = "HIDE";
		}

	}else{
		put_msg_and_back("존재하지 않는 벤더사입니다.");
	}
}

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
?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
	<div class="container">
		<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
		<div class="content write_page">
			<div class="content_wrap">
				<form name="dyForm" method="post" class="<?php echo $mode?>">
					<input type="hidden" name="mode" value="<?php echo $mode?>" />
					<input type="hidden" name="dupcheck" id="dupcheck" value="N" />
					<input type="hidden" name="product_idx" value="<?php echo $product_idx?>" />
					<input type="hidden" name="goto_option" id="goto_option" value="N" />
					<div class="tb_wrap">
						<table>
							<colgroup>
								<col width="150">
								<col width="*">
							</colgroup>
							<tbody>
								<?if($mode == "mod"){?>
								<tr>
									<th>상품코드</th>
									<td class="text_left"><?=$product_idx?></td>
								</tr>
								<?}?>
								<?php if(isDYLogin()){?>
								<tr>
									<th>판매타입 <span class="lb_red">필수</span></th>
									<td class="text_left">
										<?php
										if($mode == "add"){
										?>
										<label><input type="radio" id="sales_type_1" name="product_sale_type" value="SELF" <?=($product_sale_type == "SELF") ? "checked" : ""?>/> 사입/자체</label>
										<label><input type="radio" id="sales_type_2" name="product_sale_type" value="CONSIGNMENT" <?=($product_sale_type == "CONSIGNMENT") ? "checked" : ""?> /> 위탁</label>
										<?php
										}else{
											if($product_sale_type == "SELF"){
												echo "사입/자체";
											}elseif($product_sale_type == "CONSIGNMENT"){
												echo "위탁";
											}
										?>
											<input type="hidden" name="product_sale_type" value="<?=$product_sale_type?>" />
										<?php
										} ?>
									</td>
								</tr>
								<tr>
									<th>공급처 <span class="lb_red">필수</span></th>
									<td class="text_left">
										<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$supplier_group_idx?>">
											<option value="0">전체 그룹</option>
										</select>
										<select name="supplier_idx" class="supplier_idx" data-selected="<?=$supplier_idx?>">
											<option>공급처를 선택하세요.</option>
										</select>
										<a href="javascript:;" class="btn btn-supplier-search-pop">검색</a>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<th>상품명 <span class="lb_red">필수</span></th>
									<td class="text_left">
										<input type="text" name="product_name" maxlength="150" class="w400px" value="<?=$product_name?>" />
									</td>
								</tr>
								<?php if(isDYLogin()){?>
								<tr>
									<th>공급처 상품명</th>
									<td class="text_left">
										<input type="text" name="product_supplier_name" maxlength="200" class="w400px" value="<?=$product_supplier_name?>" />
										<span class="info_txt col_red">(한글 20자 이내)</span>
									</td>
								</tr>
								<tr>
									<th>공급처 옵션</th>
									<td class="text_left">
										<input type="text" name="product_supplier_option" maxlength="200" class="w400px" value="<?=$product_supplier_option?>" />
									</td>
								</tr>
								<tr>
									<th>판매처</th>
									<td class="text_left">
										<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="<?=$seller_group_idx?>">
											<option value="0">전체 그룹</option>
										</select>
										<select name="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>">
											<option>판매처를 선택하세요.</option>
										</select>
										<a href="javascript:;" class="btn btn-seller-search-pop">검색</a>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<th>원산지</th>
									<td class="text_left">
										<input type="text" name="product_origin" maxlength="50" class="w400px" value="<?=$product_origin?>" />
									</td>
								</tr>
								<tr>
									<th>제조사</th>
									<td class="text_left">
										<input type="text" name="product_manufacturer" maxlength="50" class="w400px" value="<?=$product_manufacturer?>" />
									</td>
								</tr>
								<tr>
									<th>담당MD</th>
									<td class="text_left">
										<input type="text" name="product_md" maxlength="50" class="w400px" value="<?=$product_md?>" />
									</td>
								</tr>
								<tr>
									<th>배송비</th>
									<td class="text_left">
										매출배송비
										<input type="text" name="product_delivery_fee_sale" maxlength="6" class="onlyNumber" value="<?=$product_delivery_fee_sale?>" />
										<?php if(isDYLogin()){?>

										매입배송비
										<input type="text" name="product_delivery_fee_buy" maxlength="6" class="onlyNumber" value="<?=$product_delivery_fee_buy?>" />
										<?php } ?>
									</td>
								</tr>
								<tr>
									<th>배송타입</th>
									<td class="text_left">
										<select name="product_delivery_type" class="">
											<option value="COURIER_DELIVERY" <?=($product_delivery_type == "COURIER_DELIVERY") ? "selected" : ""?>>택배</option>
											<option value="DIRECT_DELIVERY" <?=($product_delivery_type == "DIRECT_DELIVERY") ? "selected" : ""?>>직배</option>
										</select>
									</td>
								</tr>
								<tr>
									<th>카테고리</th>
									<td class="text_left">
										<select name="product_category_l_idx" class="product_category_l_idx" data-selected="<?=$product_category_l_idx?>">
											<option value="0">카테고리 선택</option>
										</select>
										<select name="product_category_m_idx" class="product_category_m_idx" data-selected="<?=$product_category_m_idx?>">
											<option value="">카테고리 선택</option>
										</select>
                                        <a href="javascript:;" class="btn btn-category-list-pop">카테고리관리</a>
									</td>
								</tr>
								<tr>
									<th>판매시작일</th>
									<td class="text_left">
										<input type="text" name="product_sales_date" class="jqDate w80px" readonly="readonly" maxlength="10" value="<?=$product_sales_date?>" />
									</td>
								</tr>
								<tr>
									<th>대상세금종류 <span class="lb_red">필수</span></th>
									<td class="text_left">
										<label><input type="radio" id="tax_type_1" name="product_tax_type" value="TAXATION" <?=($product_tax_type == "TAXATION") ? "checked" : ""?> /> 과세</label>
										<label><input type="radio" id="tax_type_2" name="product_tax_type" value="FREE" <?=($product_tax_type == "FREE") ? "checked" : ""?> /> 면세</label>
										<label><input type="radio" id="tax_type_3" name="product_tax_type" value="SMALL" <?=($product_tax_type == "SMALL") ? "checked" : ""?> /> 영세</label>
									</td>
								</tr>
								<tr>
									<th>상품정보고시</th>
									<td class="text_left">
										<div>
											<select name="product_notice_idx" class="product_notice_idx" data-selected="<?=$product_notice_idx?>">
												<option value="0">------</option>
											</select>
											<a href="javascript:;" class="btn btn-product-notice-write-pop">신규등록</a>
										</div><br>
										<div class="op_tb_wrap dis_none product_notice_wrap">
											<table class="opt_table auto_width product_notice_table">
												<colgroup>
													<col width="100">
													<col width="400">
												</colgroup>
												<tbody>
												<?php
												for($i=1;$i<21;$i++){
													$product_notice_column = "product_notice_".$i."_content";
													echo '<tr class="dis_none"><th>정보 '.$i.'</th><td><input type="text" name="product_notice_'.$i.'_content" id="product_notice_'.$i.'_content" maxlength="150" class="w100per" value="'.$$product_notice_column.'" /></td></tr>';
												}
												?>
												</tbody>
											</table>
										</div>
									</td>
								</tr>
								<tr>
									<th>이미지</th>
									<td class="text_left">
										<ul class="ul_list">
											<li>
												<div>
													<label class="input_label"><input type="checkbox" name="product_img_1_default" class="product_img_main" value="Y" <?=($product_img_main == 1) ? "checked" : ""?> disabled="disabled">대표이미지 설정</label>
												</div>
												<div class="product_upload_img img_product_img_1"></div>
												<div style="padding-top: 5px;text-align: center;">
													<a href="javascript:;" class="btn btn_product_img_1" id="btn_product_img_1">등록</a>
													<a href="javascript:;" class="btn red_btn btn_product_img_1_delete">삭제</a>
													<input type="hidden" name="product_img_1" id="product_img_1" value="<?=$product_img_1?>" />
												</div>
											</li>
											<li>
												<div>
													<label class="input_label"><input type="checkbox" name="product_img_2_default" class="product_img_main" value="Y" <?=($product_img_main == 2) ? "checked" : ""?> disabled="disabled">대표이미지 설정</label>
												</div>
												<div class="product_upload_img img_product_img_2"></div>
												<div style="padding-top: 5px;text-align: center;">
													<a href="javascript:;" class="btn btn_product_img_2" id="btn_product_img_2">등록</a>
													<a href="javascript:;" class="btn red_btn btn_product_img_2_delete">삭제</a>
													<input type="hidden" name="product_img_2" id="product_img_2" value="<?=$product_img_2?>" />
												</div>
											</li>
											<li>
												<div>
													<label class="input_label"><input type="checkbox" name="product_img_3_default" class="product_img_main" value="Y" <?=($product_img_main == 3) ? "checked" : ""?> disabled="disabled">대표이미지 설정</label>
												</div>
												<div class="product_upload_img img_product_img_3"></div>
												<div style="padding-top: 5px;text-align: center;">
													<a href="javascript:;" class="btn btn_product_img_3" id="btn_product_img_3">등록</a>
													<a href="javascript:;" class="btn red_btn btn_product_img_3_delete">삭제</a>
													<input type="hidden" name="product_img_3" id="product_img_3" value="<?=$product_img_3?>" />
												</div>
											</li>
											<li>
												<div>
													<label class="input_label"><input type="checkbox" name="product_img_4_default" class="product_img_main" value="Y" <?=($product_img_main == 4) ? "checked" : ""?> disabled="disabled">대표이미지 설정</label>
												</div>
												<div class="product_upload_img img_product_img_4"></div>
												<div style="padding-top: 5px;text-align: center;">
													<a href="javascript:;" class="btn btn_product_img_4" id="btn_product_img_4">등록</a>
													<a href="javascript:;" class="btn red_btn btn_product_img_4_delete">삭제</a>
													<input type="hidden" name="product_img_4" id="product_img_4" value="<?=$product_img_4?>" />
												</div>
											</li>
											<li>
												<div>
													<label class="input_label"><input type="checkbox" name="product_img_5_default" class="product_img_main" value="Y" <?=($product_img_main == 5) ? "checked" : ""?> disabled="disabled">대표이미지 설정</label>
												</div>
												<div class="product_upload_img img_product_img_5"></div>
												<div style="padding-top: 5px;text-align: center;">
													<a href="javascript:;" class="btn btn_product_img_5" id="btn_product_img_5">등록</a>
													<a href="javascript:;" class="btn red_btn btn_product_img_5_delete">삭제</a>
													<input type="hidden" name="product_img_5" id="product_img_5" value="<?=$product_img_5?>" />
												</div>
											</li>
											<li>
												<div>
													<label class="input_label"><input type="checkbox" name="product_img_6_default" class="product_img_main" value="Y" <?=($product_img_main == 6) ? "checked" : ""?> disabled="disabled">대표이미지 설정</label>
												</div>
												<div class="product_upload_img img_product_img_6"></div>
												<div style="padding-top: 5px;text-align: center;">
													<a href="javascript:;" class="btn btn_product_img_6" id="btn_product_img_6">등록</a>
													<a href="javascript:;" class="btn red_btn btn_product_img_6_delete">삭제</a>
													<input type="hidden" name="product_img_6" id="product_img_6" value="<?=$product_img_6?>" />
												</div>
											</li>
										</ul>
									</td>
									</td>
								</tr>
								<?php if(isDYLogin()) {?>
								<tr>
									<th>쇼핑몰 상세페이지</th>
									<td class="text_left">
										<div class="op_tb_wrap">
											<table class="opt_table auto_width table_product_detail">
												<colgroup>
													<col width="200">
													<col width="500">
													<col width="100">
												</colgroup>
												<tbody>
												<tr>
													<th class="text_center">쇼핑몰 이름</th>
													<th class="text_center">URL</th>
													<th></th>
												</tr>

												<tr>
													<td><input type="text" class="w100per dmy_product_detail_mall_name" maxlength="50" /></td>
													<td><input type="text" class="w100per dmy_product_detail_url" maxlength="300" /></td>
													<td><a href="javascript:;" class="btn green_btn btn-product-detail-add-row ">저장</a> </td>
												</tr>
												<?php
												foreach($_detail_list as $_lt){
												?>
												<tr>
													<td>
														<span class="spn_product_detail_mall_name"><?=$_lt["product_detail_mall_name"]?></span>
														<input type="hidden" name="product_detail_idx[]" class="product_detail_idx" value="<?=$_lt["product_detail_idx"]?>"/>
														<input type="hidden" name="product_detail_mall_name[]" class="product_detail_mall_name" value="<?=$_lt["product_detail_mall_name"]?>"/>
													</td>
													<td>
														<span class="spn_product_detail_url"><?=$_lt["product_detail_url"]?></span>
														<input type="hidden" name="product_detail_url[]" class="product_detail_url" value="<?=$_lt["product_detail_url"]?>"/>
													</td>
													<td>
														<a href="javascript:;" class="btn red_btn btn-product-detail-delete-row">X</a>
													</td>
												</tr>
												<?php
												}
												?>
												<tr class="dis_none">
													<td>
														<span class="spn_product_detail_mall_name"></span>
														<input type="hidden" name="product_detail_mall_name[]" class="product_detail_mall_name" value=""/>
													</td>
													<td>
														<span class="spn_product_detail_url"></span>
														<input type="hidden" name="product_detail_url[]" class="product_detail_url" value=""/>
													</td>
													<td>
														<a href="javascript:;" class="btn red_btn btn-product-detail-delete-row">X</a>
													</td>
												</tr>
												</tbody>
											</table>
										</div>
									</td>
								</tr>
								<?php } ?>
								<tr>
									<th>상품설명</th>
									<td class="text_left">
										<textarea name="product_desc" maxlength="8000" class="w100per"><?=$product_desc?></textarea>
									</td>
								</tr>
								<?php if(isDYLogin()){?>
								<tr>
									<th>벤더사 노출 <span class="lb_red">필수</span></th>
									<td class="text_left">
										<p>
											<label><input type="radio" id="product_vendor_show_Y" name="product_vendor_show" class="product_vendor_show" value="SHOW" <?=($product_vendor_show == "SHOW") ? "checked" : "" ?> /> Y</label>
											<label><input type="radio" id="product_vendor_show_N" name="product_vendor_show" class="product_vendor_show" value="HIDE" <?=($product_vendor_show == "HIDE") ? "checked" : "" ?> /> N</label>
										</p>
										<p class="set_vender_show_y dis_none">
											<label><input type="radio" id="product_vendor_show_A" name="product_vendor_show_type" class="product_vendor_show_type" value="ALL" <?=($product_vendor_show_type == "ALL") ? "checked" : "" ?> /> 전체노출</label>
											<label><input type="radio" id="product_vendor_show_S" name="product_vendor_show_type" class="product_vendor_show_type" value="SELECTED" <?=($product_vendor_show_type == "SELECTED") ? "checked" : "" ?> /> 특정업체노출</label>
											<a href="javascript:;" class="small_btn btn-product-vendor-show-selected dis_none">선택</a>
										</p>
										<div class="sub_desc_border div_product_vendor_show_list dis_none">
											<?=$product_vendor_show_list_name?>
										</div>
										<input type="hidden" name="product_vendor_show_list" class="product_vendor_show_list" value="<?=$product_vendor_show_list?>" />
									</td>
								</tr>
								<?php } ?>
							</tbody>
						</table>
					</div>
				</form>
			</div>
			<div class="content_wrap_full">
				<?php if($mode == "mod"){?>
				<div class="con_tit">
					<p class="title">옵션정보</p>
				</div>
				<form name="searchForm" id="searchForm" method="get">
					<input type="hidden" name="product_idx" value="<?=$product_idx?>" />
					<div class="find_wrap">
						<div class="finder">
							<div class="finder_set">
								<div class="finder_col">
									<span class="text">판매상태</span>
									<select name="product_option_soldout_type">
										<option value="">전체</option>
										<option value="both">품절+일시품절</option>
										<option value="product_option_soldout">품절</option>
										<option value="product_option_soldout_temp">일시품절</option>
										<option value="available">판매가능</option>
									</select>
								</div>
								<div class="finder_col">
									<span class="text">재고상태</span>
									<select name="product_option_stock_type" class="product_option_stock_type">
										<option value="">전체</option>
										<option value="available">판매가능</option>
										<option value="not_enough">재고부족</option>
										<option value="warning">재고경고</option>
										<option value="soldout">재고없음</option>
									</select>
								</div>
								<div class="finder_col">
									<select name="search_column">
										<option value="product_option_name">옵션명</option>
										<option value="product_option_idx">옵션코드</option>
									</select>
									<input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" />
								</div>

							</div>
						</div>
						<div class="find_btn">
							<div class="table">
								<div class="table_cell">
									<a href="javascript:;" id="btn_searchBar" class="wide_btn btn_default">검색</a>
								</div>
							</div>
						</div>
					</div>
				</form>
				<?php if(isDYLogin()) {?>
				<div class="grid_btn_set_top">
					<a href="javascript:;" class="btn btn-product-option-write" data-idx="<?=$product_idx?>">옵션추가</a>
					<a href="javascript:;" class="btn btn-manage-group-pop btn-product-option-xls-write" data-idx="<?=$product_idx?>">옵션일괄추가(엑셀)</a>
					<a href="javascript:;" class="btn btn-manage-group-pop btn-product-option-xls-modify" data-idx="<?=$product_idx?>">옵션정보일괄수정</a>
					<a href="javascript:;" class="btn btn-manage-group-pop">다운로드</a>
					<a href="javascript:;" class="btn btn-manage-group-pop">발주/입고추가</a>
					<div class="right">
						<a href="javascript:;" class="btn btn-product-option-soldout-all-y" data-idx="<?=$product_idx?>">전체품절처리</a>
						<a href="javascript:;" class="btn green_btn btn-product-option-soldout-all-n" data-idx="<?=$product_idx?>">전체판매가능처리</a>
					</div>
				</div>
				<?php } ?>
				<div class="tb_wrap grid_tb">
					<table id="grid_list">
					</table>
					<div id="grid_pager"></div>
				</div>
				<?php }?>
				<div class="btn_set">
					<?php if($mode == "mod"){?>
					<div class="left">
						<a href="product_list.php?<?=$param?>" class="large_btn "><i class="fas fa-angle-left"></i>이전 페이지</a>
					</div>
					<?php }?>
					<?php if(isDYLogin()) {?>
					<div class="center">
						<?php if($mode == "add"){?>
						<a href="javascript:;" id="btn-save-and-go" class="large_btn green_btn ">저장 후 옵션등록</a>
						<?php } ?>
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
						<a href="javascript:history.back();" class="large_btn red_btn">취소</a>
					</div>
					<?php } ?>
					<?php if($mode == "mod" && isDYLogin()){?>
					<div class="right">
						<a href="javascript:;" class="large_btn red_btn btn-product-delete" data-idx="<?=$product_idx?>">삭제</a>
					</div>
					<?php }?>
				</div>
			</div>

			<div id="modal_product_option_write" title="상품 옵션 추가" class="red_theme" style="display: none;"></div>
			<div id="modal_product_option_delete" title="상품 옵션 삭제" class="red_theme" style="display: none;"></div>
			<div id="modal_product_delete" title="상품 삭제" class="red_theme" style="display: none;"></div>
			<div id="modal_product_option_write_xls" title="상품 옵션 일괄 등록" class="red_theme" style="display: none;"></div>
			<div id="modal_product_option_sold_out_memo" title="품절 메모 변경" class="red_theme" style="display: none;"></div>
			<div id="common_input_modal_pop" title="" class="red_theme" style="display: none;"></div>
		</div>
	</div>
	<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
	<script src="/js/main.js"></script>
	<script src="/js/String.js"></script>
	<script src="/js/FormCheck.js"></script>
	<script src="/js/page/product.product.js?v=191211"></script>
	<script src="/js/page/info.product.notice.js"></script>
	<script src="/js/page/info.category.js?v=191211"></script>
	<script src="/js/fileupload.js"></script>
	<script>
		window.name = "product_write";
		//벤더사 등급명 출력
		var vendor_grade_name_list = {
			"A" : "<?=$product_option_sale_price_A_name?>",
			"B" : "<?=$product_option_sale_price_B_name?>",
			"C" : "<?=$product_option_sale_price_C_name?>",
			"D" : "<?=$product_option_sale_price_D_name?>",
			"E" : "<?=$product_option_sale_price_E_name?>"
		};
		$(function() {
			Product.ProductWriteInit();
			ProductNotice.bindProductNoticeList(".product_notice_idx");
			Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");

			<?php if($mode == "mod"){ ?>
			Product.ProductOptionInit();
			<?php } ?>

			<?php if(!isDYLogin()) {?>
			$(".content_wrap .tb_wrap input").prop("disabled", true).css({"backgroundColor": "#fff"});
			$(".content_wrap .tb_wrap select").prop("disabled", true).css({"backgroundColor": "#fff"});
			$(".content_wrap .tb_wrap a").remove();
			<?php } ?>
		});
	</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>