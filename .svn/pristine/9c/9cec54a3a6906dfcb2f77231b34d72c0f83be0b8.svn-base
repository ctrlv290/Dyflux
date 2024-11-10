<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 광고비관리 - 광고비충전/광고비사용 팝업 페이지
 */
//Page Info
$pageMenuIdx = 254; //$pageMenuIdx = 254;
//Init
include_once "../_init_.php";

$mode = $_GET["mode"];

if($mode == "add_charge"){
	$pageMenuIdx = 254;
}elseif($mode == "add_use"){
	$pageMenuIdx = 255;
}

$C_Seller = new Seller();
$_seller_list = $C_Seller->getSellerList();
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormChargePop" id="dyFormChargePop" method="post" class="<?=$mode?>" action="ad_cost_proc.php">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="*">
							<col width="100">
							<col width="120">
							<col width="150">
							<col width="*">
						</colgroup>
						<thead>
						<tr>
							<th>판매처</th>
							<th>일자</th>
							<th>충전금</th>
							<th>광고상품</th>
							<th>비고</th>
						</tr>
						</thead>
						<tbody>
						<?php
						for($i=1;$i<11;$i++) {
							?>
							<tr>
								<td class="text_left">
									<select name="seller_idx[]" class="seller_idx">
										<option value="">판매처를 선택하세요.</option>
										<?php
										foreach($_seller_list as $s){
											echo '<option value="'.$s["seller_idx"].'">'.$s["seller_name"].'</option>';
										}
										?>
									</select>
								</td>
								<td>
									<input type="text" name="ad_date[]" class="w100per jqDate" value="" readonly="readonly" />
								</td>
								<td>
									<input type="text" name="ad_amount[]" class="w100per money" value="" />
								</td>
								<td>
									<input type="text" name="ad_product_name[]" class="w100per" value="" />
								</td>
								<td>
									<input type="text" name="ad_memo[]" class="w100per" value="" />
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
						<a href="javascript:;" id="btn-save-pop" class="large_btn blue_btn  ">저장</a>
						<a href="javascript:self.close();" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/settle.charge.js"></script>
<script>
	SettleCharge.AdCostPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

