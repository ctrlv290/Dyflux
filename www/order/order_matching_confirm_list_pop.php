<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매칭 내역 확인 팝업
 */
//Page Info
$pageMenuIdx = 73;
//Init
include_once "../_init_.php";

$seller_idx = $_POST["seller_idx"];
?>
<form name="orderForm">
	<input type="hidden" id="order_idx" name="order_idx" value="<?=$order_idx?>" />
	<input type="hidden" id="seller_idx" name="seller_idx" value="<?=$_order_info["seller_idx"]?>" />
</form>
<div class="container popup">
	<div class="content write_page">
		<div class="content_wrap">
			<div class="tb_wrap">
				<form name="searchFormPop" id="searchFormPop" method="get">
					<input type="hidden" name="seller_idx" id="pop_seller_idx" value="<?=$seller_idx?>" />
				</form>
				<div class="tb_wrap grid_tb matching_pop_grid_wrap">
					<table id="grid_list_confirm" style="width: 100%;">
					</table>
				</div>
			</div>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" class="large_btn red_btn btn-order-matching-pop-close">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var is_vendor_seller = false;
	Order.OrderMatchingConfirmPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

