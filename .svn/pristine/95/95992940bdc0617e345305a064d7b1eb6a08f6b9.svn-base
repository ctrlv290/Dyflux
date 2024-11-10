<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매입추가등록 수정 팝업 페이지
 */
//Page Info
$pageMenuIdx = 289;
//Init
include_once "../_init_.php";

$mode = "add";

$loan_idx = $_GET["loan_idx"];
$loan_is_use = "Y";

$loan_name = "";
$loan_amount = "";
$loan_detail = "";
$loan_sort = "0";

$C_Loan = new Loan();

$loan_sort = $C_Loan -> getNextSortNum();

if($loan_idx) {
	$_view  = $C_Loan->getLoanInfo($loan_idx);

	$loan_name = "";
	$loan_amount = "";
	$loan_detail = "";

	if($_view){
		extract($_view);
		$mode = "update";
	}
}

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormPop" id="dyFormPop" method="post" class="<?=$mode?>" action="loan_proc.php">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<input type="hidden" name="loan_idx" value="<?=$loan_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>계좌명</th>
							<td class="text_left"><input type="text" name="loan_name" class="w100per" value="<?=$loan_name?>" /></td>
						</tr>
						<tr>
							<th>대출액</th>
							<td class="text_left"><input type="text" name="loan_amount" class="w120px money" value="<?=$loan_amount?>" /></td>
						</tr>
						<tr>
							<th>만기일 / 상환일정</th>
							<td class="text_left"><input type="text" name="loan_detail" class="w100per" maxlength="100" value="<?=$loan_detail?>" /></td>
						</tr>
						<?php if($mode == "add"){?>
							<tr>
								<th>순서</th>
								<td class="text_left">
									<input type="text" name="loan_sort" class="w50px onlyNumber" value="<?=$loan_sort?>" />
								</td>
							</tr>
						<?php } ?>
						<tr>
							<th>사용여부</th>
							<td class="text_left">
								<label><input type="radio" name="loan_is_use" value="Y"  <?=($loan_is_use == "Y") ? "checked" : ""?>/> Y</label>
								<label><input type="radio" name="loan_is_use" value="N"  <?=($loan_is_use == "N") ? "checked" : ""?> /> N</label>
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
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/info.loan.js"></script>
<script>
	Loan.LoanWritePopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

