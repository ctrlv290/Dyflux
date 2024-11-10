<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래현황 수정 팝업 페이지
 */
//Page Info
$pageMenuIdx = 134;
//Init
include_once "../_init_.php";

$C_Settle = new Settle();

$mode            = "save_etc_transaction";
$tran_idx        = $_POST["target_idx"];
if($tran_type == "SALE_ETC") {
	//매출현황(외상매출금)
	$type = "매출현황(기타)";
	$will_amount_text = "전일 미수금액";
	$today_amount_text = "판매금액";
	$today_in_amount_text = "입금액";
}elseif($tran_type == "PURCHASE_ETC"){
	//매입현황(외상매입금)
	$type = "매입현황(기타)";
	$will_amount_text = "전일 미지급금액";
	$today_amount_text = "발생금액";
	$today_in_amount_text = "송금액";
}

$_view = $C_Settle->getTransactionStateDataByTranIdx($tran_idx);
if($_view){
	$prev_amount   = $_view["tran_prev_amount"];
	$today_amount  = $_view["tran_today_amount"];
	$remain_amount = $_view["tran_remain_amount"];
	$tran_amount   = $_view["tran_amount"];
	$tran_memo     = $_view["tran_memo"];
	$target_name   = $_view["target_name"];
}

?>
<div class="container popup ">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormStatePop" id="dyFormStatePop" method="post" class="<?=$mode?>">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<input type="hidden" name="tran_type" value="<?=$tran_type?>" />
				<input type="hidden" name="tran_idx" value="<?=$tran_idx?>" />
				<input type="hidden" name="date" value="<?=$date?>" />
				<input type="hidden" name="target_idx" value="<?=$target_idx?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>구분</th>
							<td class="text_left"><?=$type?></td>
						</tr>
						<tr>
							<th>날짜</th>
							<td class="text_left"><?=$date?></td>
						</tr>
						<tr>
							<th>거래처명</th>
							<td class="text_left"><?=$target_name?></td>
						</tr>
						<tr>
							<th><?=$will_amount_text?></th>
							<td class="text_left">
								<input type="text" name="prev_amount" class="w100px onlyNumberDynamic" value="<?=$prev_amount?>" />
							</td>
						</tr>
						<tr>
							<th><?=$today_amount_text?></th>
							<td class="text_left">
								<input type="text" name="today_amount" class="w100px onlyNumberDynamic" value="<?=$today_amount?>" />
							</td>
						</tr>
						<tr>
							<th><?=$today_in_amount_text?></th>
							<td class="text_left">
								<input type="text" name="tran_amount" class="w100px onlyNumberDynamic" value="<?=$tran_amount?>" />
							</td>
						</tr>
						<tr>
							<th>현재잔액</th>
							<td class="text_left tran_remain">
								<input type="text" name="remain_amount" class="w100px onlyNumberDynamic" value="<?=$remain_amount?>" />
							</td>
						</tr>
						<tr>
							<th>비고</th>
							<td class="text_left">
								<input type="text" name="tran_memo" class="w200px" value="<?=$tran_memo?>" />
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
	SettlePurchase.TransactionStateEditEtcPopInit();
</script>

