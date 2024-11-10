<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 일괄 추가 (엑셀) 팝업 (Iframe)
 */
//Page Info
$pageMenuIdx = 176;
//Init
include_once "../_init_.php";

$mode = "add";
$_sample_filename = "상품옵션_일괄_등록_엑셀.xlsx";
//일괄수정
if($_POST["mode"] == "mod")
{
	//Page Info
	$pageMenuIdx = 176;

	$mode = "mod";
	$_sample_filename = "상품옵션_일괄_수정_엑셀.xlsx";
}

$product_idx = $_POST["product_idx"];

$C_Product = new Product();

$rst = $C_Product -> getProductData($product_idx);

if(!$rst) {
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}
?>
<div class="container popup">
	<div class="content write_page xls_pop_content">
		<div class="tb_wrap">
			<table>
				<colgroup>
					<col width="200" />
					<col width="*" />
				</colgroup>
				<tr>
					<th>대표상품</th>
					<td class="text_left"><?=$rst["product_name"]?></td>
				</tr>
				<tr>
					<th>대표상품 코드</th>
					<td class="text_left"><?=$rst["product_idx"]?></td>
				</tr>
			</table>
		</div>
		<form name="searchForm_xls" id="searchForm_xls" method="post" enctype="multipart/form-data" action="/proc/_xls_upload.php" target="xls_hidden_frame">
			<input type="hidden" name="mode" id="xlswrite_mode" value="<?=$mode?>" />
			<input type="hidden" name="act" id="xlswrite_act" value="grid" />
			<input type="hidden" name="product_idx" id="xlswrite_product_idx" value="<?=$product_idx?>" />
			<input type="hidden" name="xls_type" value="product_option_regist" />
			<input type="hidden" name="product_idx" value="<?=$rst["product_idx"]?>" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<input type="file" name="xls_file" />
						</div>
						<div class="finder_col">
							<a href="javascript:;" class="btn green_btn btn-upload">업로드</a>
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
		</form>

		<p class="sub_desc">
			샘플 파일 다운로드 <a href="/_xls_sample/<?=$_sample_filename?>" class="btn blue_btn">다운로드</a>
			* 샘플파일을 다운로드하여 포맷을 확인하시고 등록해 주세요. 양식에 맞지 않으면 정상적으로 등록되지 않습니다.
		</p>
		<?php if($mode == "mod"){?>
			<p class="info_txt col_red">수정하려는 판매처코드가 맞지 않는 경우 수정되지 않습니다.</p>
		<?php }?>
		<div class="grid_btn_set_top">
			<a href="javascript:;" class="large_btn red_btn btn-xls-insert">&nbsp;&nbsp;&nbsp;적용&nbsp;&nbsp;&nbsp;</a>
			<div class="right">
			</div>
		</div>
		<div class="tb_wrap grid_tb">
			<table id="grid_list_xls" style="width: 100%;">
			</table>
			<div id="grid_pager_xls"></div>
		</div>
	</div>
</div>
<script>
	Product.ProductOptionXlsWriteInit();
</script>