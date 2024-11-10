<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매입추가등록 수정 팝업 페이지
 */
//Page Info
$pageMenuIdx = 228;
//Init
include_once "../_init_.php";

$mode = "adjust_add";

$supplier_idx = $_GET["supplier_idx"];

$C_Supplier = new Supplier();
$_supplier_list = $C_Supplier-> getUseSupplierList();

$ledger_add_type = $_GET["ledger_add_type"];

if($ledger_add_type == "ADJUST"){
	$amount_text = "매입액";
	$pageMenuIdx = 228;
}elseif($ledger_add_type == "TRAN"){
	$amount_text = "송금액";
	$pageMenuIdx = 238;
}elseif($ledger_add_type == "REFUND"){
	$amount_text = "공제/환급액";
	$pageMenuIdx = 239;
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
				<input type="hidden" name="ledger_type" value="LEDGER_PURCHASE" />
				<input type="hidden" name="ledger_add_type" value="<?=$ledger_add_type?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="200">
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
									<option value="">공급처를 선택하세요.</option>
									<?php
									foreach($_supplier_list as $s){
										$selected = "";
										$selected = ($supplier_idx == $s["member_idx"]) ? "selected" : "";
										echo '<option value="'.$s["member_idx"].'" '.$selected.'>'.$s["supplier_name"].'</option>';
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

