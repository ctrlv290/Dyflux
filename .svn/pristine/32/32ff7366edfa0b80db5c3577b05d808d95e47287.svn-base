<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매출추가등록 수정 팝업 페이지
 */
//Page Info
$pageMenuIdx = 232;
//Init
include_once "../_init_.php";

$mode = "adjust_add";

$seller_idx = $_GET["seller_idx"];

$C_Seller = new Seller();
$_seller_list = $C_Seller -> getSellerList();

$ledger_add_type = $_GET["ledger_add_type"];

if($ledger_add_type == "ADJUST"){
	$amount_text = "매출액";
	$pageMenuIdx = 232;
}elseif($ledger_add_type == "TRAN"){
	$amount_text = "실입금액";
	$pageMenuIdx = 233;
}elseif($ledger_add_type == "REFUND"){
	$amount_text = "공제/환급액";
	$pageMenuIdx = 234;
}

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormLedgePop" id="dyFormLedgePop" method="post" class="<?=$mode?>" action="ledger_proc.php">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<input type="hidden" name="ledger_type" value="LEDGER_SALE" />
				<input type="hidden" name="ledger_add_type" value="<?=$ledger_add_type?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="260">
							<col width="120">
							<col width="*">
							<col width="120">
							<col width="*">
						</colgroup>
						<thead>
						<tr>
							<th>공급처</th>
							<th>일자</th>
							<th>내용</th>
							<th><?=$amount_text?></th>
							<th>비고</th>
						</tr>
						</thead>
						<tbody>
						<?php
						for($i=1;$i<11;$i++) {
							?>
							<tr>
								<td>
									<select name="target_idx[]">
										<option value="">판매처를 선택하세요.</option>
										<?php
										foreach($_seller_list as $s){
											$selected = "";
											$selected = ($seller_idx == $s["seller_idx"]) ? "selected" : "";
											echo '<option value="'.$s["seller_idx"].'" '.$selected.'>'.$s["seller_name"].'</option>';
										}
										?>
									</select>
								</td>
								<td>
									<input type="text" name="ledger_date[]" class="w100per jqDate" value="" readonly="readonly" />
								</td>
								<td>
									<input type="text" name="ledger_title[]" class="w100per" value="" />
								</td>
								<td>
									<input type="text" name="ledger_amount[]" class="w100per inp_ledger_amount" value="" />
								</td>
								<td>
									<input type="text" name="ledger_memo[]" class="w100per" value="" />
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
<script src="/js/page/settle.ledger.js"></script>
<script>
	SettleLedge.PurchaseLedgePopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

