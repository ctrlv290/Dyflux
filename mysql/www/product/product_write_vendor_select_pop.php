<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 등록 시 벤더사 노출 여부 항목 중 특정업체노출 선택 시 사용되는 팝업
 */
//Page Info
$pageMenuIdx = 174;
//Init
include_once "../_init_.php";

$C_Vendor = new Vendor();

$vendorList = $C_Vendor -> getVendorUseAbleList();
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="searchForm" id="searchForm" method="get">
				<input type="hidden" name="manage_group_type" value="<?=$manage_group_type?>" />
				<div class="find_wrap">
					<div class="finder">
						<div class="finder_set">
							<div class="finder_col">
								<label><input type="checkbox" class="vendor_select_all" />전체 선택</label>
							</div>

						</div>
					</div>
					<div class="find_btn">
						<div class="table">
							<div class="table_cell">

							</div>
						</div>
					</div>
				</div>
			</form>
			<div class="div_h400_scroll">
				<div class="tb_wrap grid_tb">
					<ul class="list_33">
						<?php
						foreach($vendorList as $vd){
							echo '<li>
									<label class="input_label"><input type="checkbox" name="vendor_select" class="vendor_select" data-vendor-name="'.$vd["vendor_name"].'" value="'.$vd["idx"].'" />'.$vd["vendor_name"].'</label>
								</li>';
						}
						?>
					</ul>
				</div>
			</div>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
					<a href="javascript:self.close();" class="large_btn red_btn">취소</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/product.product.vendor.select.js"></script>
<script>
	ProductVendorSelect.ProductVendorSelectInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
