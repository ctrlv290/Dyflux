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


$mode = $_GET["mode"];
$tran_date = $_GET["tran_date"];
$tran_type = $_GET["tran_type"];
$tran_inout = $_GET["tran_inout"];
$target_idx = $_GET["target_idx"];
$tran_idx = $_GET["tran_idx"] ? $_GET["tran_idx"] : 0;

//창 타이틀 설정
switch ($tran_type){
	case "CASH_IN" :
		$pageMenuIdx = 243;
		break;
	case "CASH_OUT" :
		$pageMenuIdx = 244;
		break;
	case "BANK_CUSTOMER_IN" :
		$pageMenuIdx = 245;
		break;
	case "BANK_CUSTOMER_OUT" :
		$pageMenuIdx = 246;
		break;
	case "BANK_ETC_IN" :
		$pageMenuIdx = 247;
		break;
	case "BANK_ETC_OUT" :
		$pageMenuIdx = 248;
		break;
	case "TRANSFER_IN" :
		$pageMenuIdx = 249;
		break;
	case "TRANSFER_OUT" :
		$pageMenuIdx = 250;
		break;
}

$use_target = false;
$is_card = false;

//통장 입출금 내역 - 수입(거래처별) : 판매처,벤더사
//통장 입출금 내역 - 지출(거래처별) : 공급처
//위 두가지 항목은 각각 거래처를 불러온다.
if($tran_type == "BANK_CUSTOMER_IN"){
	$use_target = true;
	$C_Seller = new Seller();
	$_target_list = $C_Seller->getSellerList();
}elseif($tran_type == "BANK_CUSTOMER_OUT"){
	$use_target = true;
	$C_Supplier = new Supplier();
	$_target_list = $C_Supplier->getUseSupplierList();
}elseif($tran_type == "CARD_OUT"){
	$is_card = true;
}

$C_Report = new Report();

$_account_list = $C_Report->getAccountCodeList($tran_inout);

if($mode == "update"){
	$_list = $C_Report->getReportDataByDate($tran_date, $tran_type, $tran_inout, "day", "N");

	if (count($_list) && $tran_idx != 0) {
		$list_temp = array();

		foreach ($_list as $row) {
			if ($row["tran_idx"] == $tran_idx) {
				$list_temp[] = $row;
				break;
			}
		}

		$_list = $list_temp;
	}
}
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<style>
				.search-txt {ime-mode: active !important;}
				label {cursor: pointer;}
			</style>
			<form name="dyFormPop" id="dyFormPop" method="post" class="<?=$mode?>" action="report_proc.php">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<!--<input type="hidden" name="tran_date" value="<?=$tran_date?>" />-->
				<input type="hidden" name="tran_type" value="<?=$tran_type?>" />
				<input type="hidden" name="tran_inout" value="<?=$tran_inout?>" />
				<div class="tb_wrap" style="height: 100%;overflow-x: visible !important;overflow-y: visible;">
					<?php if($tran_type == "TRANSFER_IN" || $tran_type == "TRANSFER_OUT") {?>
					<p>
						<label><input type="checkbox" name="tran_is_sync" value="Y"/>계좌간이체 <?=($tran_type == "TRANSFER_IN") ? "지출" : "수입"?>에도 등록</label>
					</p>
					<?php } ?>
					<table>
						<colgroup>
							<?php if($is_card == true){ ?>
							<col width="180">
							<col width="180">
							<col width="180">
							<?php } ?>

							<col width="220">
							<?php if($use_target){ ?>
							<col width="220">
							<?php } ?>
							<col width="100">
							<col width="*">
							<col width="150">
						</colgroup>
						<thead>
						<tr>
							<?php if($is_card == true){ ?>
							<th>사용자</th>
							<th>카드번호</th>
							<th>지출처</th>
							<?php } ?>

							<th>계정과목 <a href="javascript:;" class="xsmall_btn btn-account-sync">일괄선택</a></th>
							<?php if($use_target){ ?>
							<th>거래처</th>
							<?php } ?>
							<th>일자</th>
							<th>적요</th>
							<th>금액</th>
						</tr>
						</thead>
						<tbody>
						<?php
						$limit = 10;
						if($mode == "update"){
							$limit = count($_list);
						}
						for($i=0;$i<$limit;$i++){
							$row = "";
							if($mode == "update") {
								$row = $_list[$i];
							}

							if($row["tran_date"]){
								$tran_date = $row["tran_date"];
							}

							//계좌간이체 수입/지출 등록 일 경우
							if($mode == "add"){
								if($tran_type == "TRANSFER_IN"){
									$row = array();
									$row["account_idx"] = "75"; //수입-계좌간이체
								}elseif($tran_type == "TRANSFER_OUT"){
									$row = array();
									$row["account_idx"] = "7";  //지출-계좌간이체
								}
							}
						?>
						<tr>
							<?php if($is_card == true){ ?>
								<td><input type="text" name="tran_user[]" class="w100per" value="<?=$row["tran_user"]?>" /></td>
								<td><input type="text" name="tran_card_no[]" class="w100per" value="<?=$row["tran_card_no"]?>" /></td>
								<td><input type="text" name="tran_purpose[]" class="w100per" value="<?=$row["tran_purpose"]?>" /></td>
							<?php } ?>
							<td class="text_left">
								<?php
								if($mode == "update") {
									echo '<input type="hidden" name="tran_idx[]" value="'.$row["tran_idx"].'" />';
								}
								?>

								<select name="account_idx[]" class="sel-account-idx">
									<?php
									if($mode == "add") {
										echo '<option value="">계정과목을 선택해주세요.</option>';
									}
									?>
									<?php
									foreach($_account_list as $ac){
										$selected = ($row["account_idx"] == $ac["account_idx"]) ? "selected" : "";
										echo '<option value="'.$ac["account_idx"].'" '.$selected.'>'.$ac["account_name"].'</option>';
									}
									?>
								</select>
							</td>
							<?php if($use_target){ ?>
							<td class="text_left">
								<select name="target_idx[]" class="sel-target-idx">
									<?php
									if($mode == "add") {
										echo '<option value="">거래처를 선택해주세요.</option>';
									}
									?>
									<?php
									foreach($_target_list as $t){

										$_target_idx = ($t["seller_idx"]) ? $t["seller_idx"] : $t["member_idx"];
										$_target_name = ($t["seller_name"]) ? $t["seller_name"] : $t["supplier_name"];

										if($row["target_idx"]) {
											$selected = ($row["target_idx"] == $_target_idx) ? "selected" : "";
										}else{
											if($target_idx){
												$selected = ($target_idx == $_target_idx) ? "selected" : "";
											}
										}

										echo '<option value="'.$_target_idx.'" '.$selected.'>'.$_target_name.'</option>';
									}
									?>
								</select>
							</td>
							<?php } ?>
							<td><input type="text" name="tran_date[]" class="w100per jqDate" value="<?=$tran_date?>" readonly="readonly" /></td>
							<td><input type="text" name="tran_memo[]" class="w100per" value="<?=$row["tran_memo"]?>" /></td>
							<td><input type="text" name="tran_amount[]" class="w100per money" value="<?=$row["tran_amount"]?>" /></td>
						</tr>
						<?php
						}
						?>
						<tr>
							<?php if($is_card == true){ ?>
							<th></th>
							<th></th>
							<th></th>
							<?php } ?>

							<th></th>
							<?php if($use_target){ ?>
							<th></th>
							<?php } ?>
							<th></th>
							<th class="w100per">합계</th>
							<th class="w100per text_right"><span class="money_total">0</span></th>
						</tr>
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
<script src="/js/page/settle.report.js?v=190719"></script>
<script>
	SettleReport.ReportPopInit();

	$('.money').keyup(function(){
		calculateTotal();
	});

	function calculateTotal() {
		var total = 0;
		
		$(".money").each(function(){
			var val = $(this).val().replace(/,/gi, '');
			if (val == "") {
				val = 0;
			}

			total += Number(val);
		});

		$('.money_total').text(Common.addCommas(total));
	}

	window.onload = function () {
		calculateTotal();
	}
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

