<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매입추가등록 수정 팝업 페이지
 */
//Page Info
$pageMenuIdx = 241;
//Init
include_once "../_init_.php";

$mode = "add";

$bank_idx = $_GET["bank_idx"];
$bank_is_use = "Y";

$bank_sort = "0";

$C_Bank = new Bank();

$bank_sort = $C_Bank -> getNextSortNum();

if($bank_idx) {
	$_view  = $C_Bank->getBankInfo($bank_idx);

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
			<form name="dyFormPop" id="dyFormPop" method="post" class="<?=$mode?>" action="bank_proc.php">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<input type="hidden" name="bank_idx" value="<?=$bank_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>구분</th>
							<td class="text_left">
								<select name="bank_type">
									<option value="DOMESTIC" <?=($bank_type == "DOMESTIC") ? "selected" : ""?>>국내계좌</option>
									<option value="FOREIGN" <?=($bank_type == "FOREIGN") ? "selected" : ""?>>외환계좌</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>계좌명</th>
							<td class="text_left"><input type="text" name="bank_name" class="w100per" value="<?=$bank_name?>" /></td>
						</tr>

						<tr <?php if($mode == "update"){?>style="display: none;"<?php } ?>>
							<th>순서</th>
							<td class="text_left">
								<input type="text" name="bank_sort" class="w50px onlyNumber" value="<?=$bank_sort?>" />
							</td>
						</tr>
						<tr>
							<th>사용여부</th>
							<td class="text_left">
								<label><input type="radio" name="bank_is_use" value="Y"  <?=($bank_is_use == "Y") ? "checked" : ""?>/> Y</label>
								<label><input type="radio" name="bank_is_use" value="N"  <?=($bank_is_use == "N") ? "checked" : ""?> /> N</label>
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
<script src="/js/page/info.bank.js"></script>
<script>
	Bank.BankWritePopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

