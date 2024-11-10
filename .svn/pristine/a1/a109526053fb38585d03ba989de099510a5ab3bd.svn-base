<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 수수료관리 수수료 등록 페이지
 */
//Page Info
$pageMenuIdx = 213;     //신규 발주
//$pageMenuIdx = 185;   //발주 정보
//Init
include_once "../_init_.php";

$mode = "add";

$market_product_no = "";
$market_commission = "";
$delivery_commission = "";

$C_Product = new Product();
$comm_idx = $_GET["comm_idx"];
$is_copy = $_GET["is_copy"];

if(isset($_GET["comm_idx"]) && $comm_idx){

	$_view = $C_Product->getProductCommissionInfo($comm_idx);

	if(!$_view){
		put_msg_and_close("잘못된 접근입니다.");
	}else{
		$mode = "update";
		extract($_view);

		$_product_list = $C_Product->getProductCommissionProductInfo($comm_idx);

		if($is_copy == "Y"){
			$mode = "add";
		}
	}
}

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" id="dyForm" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="comm_idx" value="<?php echo $comm_idx?>" />
				<input type="hidden" id="isdup" value="" />
				<input type="hidden" id="dup_seller_idx" value="" />
				<input type="hidden" id="dup_market_product_no" value="" />
				<div class="tb_wrap" style="overflow-x:visible !important;">
					<table>
						<colgroup>
							<col width="200" />
							<col width="*" />
						</colgroup>
						<tbody>
						<tr>
							<th>판매처 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<?php if ($mode == "add") {?>
								<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="0">
									<option value="0">전체 그룹</option>
								</select>
								<select name="seller_idx" class="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="판매처를 선택하세요.">
									<option value="">판매처를 선택하세요.</option>
								</select>
								<?php }else{?>
									<?=$seller_name?>
								<?php }?>
							</td>
						</tr>
						<tr>
							<th>판매처 상품코드 <span class="lb_red">필수</span></th>
							<td class="text_left">
								<?php if ($mode == "add") {?>
								<input type="text" name="market_product_no" class="market_product_no" value="" />
								<a href="javascript:;" class="btn btn-market-product-no-check">중복확인</a>
								<?php }else{?>
									<?=$market_product_no?>
								<?php }?>
							</td>
						</tr>
						<tr>
							<th>판매수수료(%) <span class="lb_red">필수</span></th>
							<td class="text_left"><input type="text" name="market_commission" class="onlyNumberComma" value="<?=$market_commission?>" /></td>
						</tr>
						<tr>
							<th>배송비 수수료(%) <span class="lb_red">필수</span></th>
							<td class="text_left"><input type="text" name="delivery_commission" class="onlyNumberComma" value="<?=$delivery_commission?>" /></td>
						</tr>
						</tbody>
					</table>
				</div>
				<p class="sub_tit2 mt20">
					<a href="javascript:;" class="btn btn-product-commission-product-add">상품검색</a>
				</p>
				<div class="tb_wrap grid_tb">
					<table id="grid_list_pop_target" style="width: 100%;">
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
						<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/product.product.commission.js"></script>
<script>
	window.name = 'product_commission_write';

	var product_list_txt = [];
	<?php
	if($mode =="update" || $is_copy == "Y"){

		echo 'product_list_txt = '.json_encode($_product_list).';';

	}
	?>

	ProductCommission.ProductCommissionWritePopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
