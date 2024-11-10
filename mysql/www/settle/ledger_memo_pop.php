<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래처별원장 메모 입력 팝업 페이지
 */
//Page Info
$pageMenuIdx = 135;
//Init
include_once "../_init_.php";

$mode = "ledger_memo";
$ledger_memo_idx = $_POST["ledger_memo_idx"];
$target_idx      = $_POST["target_idx"];
$ledger_type     = $_POST["ledger_type"];
$ledger_date     = $_POST["ledger_date"];

if($ledger_memo_idx)
{
	$C_Settle = new Settle();
	$ledger_memo = $C_Settle->getLedgerMemo($ledger_memo_idx);
}elseif($_POST["ledger_idx"]){
	$ledger_type = $_POST["ledger_type"];
	$ledger_idx = $_POST["ledger_idx"];
	$C_Settle = new Settle();
	$ledger_view = $C_Settle->getLedgerContents($ledger_idx);

	if($ledger_view){

		$ledger_type = $ledger_view["ledger_type"];
		$ledger_add_type = $ledger_view["ledger_add_type"];
		$ledger_title = $ledger_view["ledger_title"];
		$ledger_memo = $ledger_view["ledger_memo"];
		$ledger_adjust_amount = $ledger_view["ledger_adjust_amount"];
		$ledger_tran_amount = $ledger_view["ledger_tran_amount"];
		$ledger_refund_amount = $ledger_view["ledger_refund_amount"];

		$mode = "ledger_memo2";
	}
}
?>
<div class="container popup cs_order_hold_popup">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormLedgerPop" id="dyFormLedgerPop" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="ledger_idx" value="<?=$ledger_idx?>" />
				<input type="hidden" name="ledger_memo_idx" value="<?=$ledger_memo_idx?>" />
				<input type="hidden" name="target_idx" value="<?=$target_idx?>" />
				<input type="hidden" name="ledger_type" value="<?=$ledger_type?>" />
				<input type="hidden" name="ledger_date" value="<?=$ledger_date?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<?php
						if($mode == "ledger_memo2"){
							$amount_name = "";
							$amount_title = "";
							switch ($ledger_add_type)
							{
								case "ADJUST" :
									$amount_name = "ledger_adjust_amount";
									$amount_title = ($ledger_type == "SALE") ? "매출" : "매입";
									break;
								case "TRAN" :
									$amount_name = "ledger_tran_amount";
									$amount_title = "실입금액";
									break;
								case "REFUND" :
									$amount_name = "ledger_refund_amount";
									$amount_title = "공제/환급액";
									break;
							}
						?>
							<tr>
								<th>내용</th>
								<td class="text_left">
									<input type="text" name="ledger_title" value="<?=$ledger_title?>" class="w100per" />
								</td>
							</tr>
							<tr>
								<th><?=$amount_title?></th>
								<td class="text_left">
									<input type="text" name="amount" value="<?=$$amount_name?>" class="onlyNumberDynamic" />
									<input type="hidden" name="amount_name" value="<?=$amount_name?>" />
								</td>
							</tr>
						<?php }?>
						<tr>
							<th>비고</th>
							<td class="text_left">
								<textarea name="ledger_memo" class="w100per h100px commonCsContent"><?=$ledger_memo?></textarea>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save-pop" class="large_btn blue_btn  ">저장</a>
						<a href="javascript:;" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script>
	SettleLedge.LedgerMemoPopInit('<?=$ledger_type?>');
</script>

