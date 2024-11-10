<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 자금일보 입금/출금 팝업 페이지
 */
//Page Info
$pageMenuIdx = 242;
//Init
include_once "../_init_.php";

$mode = "save_bank_transaction";
$tran_date = $_GET["tran_date"];
$C_Bank = new Bank();
$_bank_list = $C_Bank->getTodayBankTransaction($tran_date);

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormPop" id="dyFormPop" method="post" class="<?=$mode?>" action="report_proc.php">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<input type="hidden" name="tran_date" value="<?=$tran_date?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="80">
							<col width="350">
							<col width="120">
							<col width="120">
							<col width="*">
						</colgroup>
						<thead>
						<tr>
							<th>구분</th>
							<th>계좌명</th>
							<th>입금액</th>
							<th>출금액</th>
							<th>비고</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach($_bank_list as $bank) {

							$tran_in = $bank["tran_in"];
							$tran_out = $bank["tran_out"];

							$tran_in = str_replace(".0000", "", $tran_in);
							$tran_out = str_replace(".0000", "", $tran_out);


							if($bank["bank_type"] == "DOMESTIC"){
								$mask_class = "money";
							}else{
								$mask_class = "money";
							}

							?>
							<tr>
								<td><?=$bank["bank_type_han"]?></td>
								<td class="text_left">
									<?=$bank["bank_name"]?>
									<input type="hidden" name="bank_idx[]" value="<?=$bank["bank_idx"]?>" />
								</td>
								<td>
									<input type="text" name="tran_in[]" class="w100per <?=$mask_class?>" value="<?=$tran_in?>" />
								</td>
								<td>
									<input type="text" name="tran_out[]" class="w100per <?=$mask_class?>" value="<?=$tran_out?>" />
								</td>
								<td>
									<input type="text" name="tran_memo[]" class="w100per" value="<?=$bank["tran_memo"]?>" />
								</td>
							</tr>
							<?php
						}
						?>
                        <tr>
                            <th colspan="2"><strong>합계</strong></th>
                            <th class="text_right">
                                <strong name="sum_tran_in[]" class="w100per money">0</strong>
                            </th>
                            <th class="text_right">
                                <strong name="sum_tran_out[]" class="w100per money">0</strong>
                            </th>
                            <th></th>
                        </tr>
						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save-pop" class="large_btn blue_btn  ">저장</a>
						<a href="javascript:;" onclick="self.close();" class="large_btn red_btn btn-common-pop-close">취소</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/settle.report.js?v=191217"></script>
<script>
	SettleReport.ReportBankPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

