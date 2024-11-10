<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 삭제 페이지
 */
//Page Info
$pageMenuIdx = 178;
//Init
include_once "../_init_.php";


$mode                   = "add";
$product_option_idx     = $_POST["product_option_idx"];

//현재고
$current_stock_count =0 ;
//매칭된 수
$product_option_matching_count = 0;
//매칭정보에 저장된 수
$matching_info_count = 0;

$C_Product = new Product();

$_product_option_view = $C_Product -> getProductOptionDataByDelete($product_option_idx);

if(!$_product_option_view){
	header('HTTP/1.1 500 Internal Server Error');
	header('Content-Type: text/html; charset=UTF-8');
	die("Error");
}else{
	extract($_product_option_view);
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
				<div class="sub_desc">
					재고가 있거나 매칭정보가 있는 상품은 삭제 할 수 없습니다.
				</div>
				<div class="tb_wrap">
					<table class="w80per mt20 mb20" style="margin: 0 auto;">
						<colgroup>
							<col width="33%">
							<col width="34%">
							<col width="33%">
						</colgroup>
						<thead>
						<tr>
							<th>현재고</th>
							<th>매칭된 수</th>
							<th>등록된 매칭정보</th>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><?=$current_stock_count?></td>
							<td><?=$product_option_matching_count?></td>
							<td><?=$matching_info_count?></td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<?php if($current_stock_count == 0 && $product_option_matching_count == 0 || $matching_info_count == 0){?>
						<a href="javascript:;" id="btn-delete-option" data-idx="<?=$product_option_idx?>" class="large_btn red_btn ">삭제</a>
						<?php } ?>
						<a href="javascript:;" class="large_btn  btn-product-option-delete-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	Product.ProductOptionDeleteInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

