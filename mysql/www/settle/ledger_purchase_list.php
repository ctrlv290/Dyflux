<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매입거래처별원장 페이지
 */
//Page Info
$pageMenuIdx = 135;
//Init
include_once "../_init_.php";

$ledge_type = "LEDGER_PURCHASE";

$date_start_year  = $_POST["date_start_year"];
$date_start_month = $_POST["date_start_month"];
$date_end_year    = $_POST["date_end_year"];
$date_end_month   = $_POST["date_end_month"];
$supplier_idx     = $_POST["supplier_idx"];

$date_start = $date_start_year . "-" . $date_start_month . "-01";
$date_end = $date_end_year . "-" . $date_end_month . "-01";

$C_Settle = new Settle();

$date_start_time = strtotime($date_start);
$date_end_time = strtotime($date_end);

$i = 0;
do{
	$new_date_time = strtotime('+'.$i++.' month', $date_start_time);
	$_search_date_ary[] =  "" . date('Y-m-d', $new_date_time) . "";
//	$month_ary[] = array(
//		"date" => date('Y-m', $new_date_time),
//	);
	$month_ary[] = date('Y-m', $new_date_time);
}while ($new_date_time < $date_end_time);


//공급처 선급금 사용여부 확인
$C_Supplier = new Supplier();
$_supplier_view = $C_Supplier->getSupplierData($supplier_idx);

$vendor_use_prepay = $_supplier_view["supplier_use_prepay"];

?>

<?php
foreach($month_ary as $month) {
	$_list = $C_Settle->getPurchaseLedgerList($supplier_idx, $month);
?>
<div class="btn_set">
	<p class="sub_tit2"><?=date('Y년 m월', strtotime($month."-01"))?></p>
	<div class="right">
		<?php if(isDYLogin()){?>
		<a href="javascript:;" class="btn btn-ledger-create-xls" data-month_start="<?=$month?>" data-month_end="<?=$month?>" data-target_idx="<?=$supplier_idx?>" data-type="<?=$ledge_type?>">파일생성</a>
		<label>
			<input type="checkbox" name="minimize" class="chk-ledge-minimize" data-date="<?=$month?>"> 상세내역 제외
		</label>
		<?php }?>
	</div>
</div>
<div class="tb_wrap">
	<table class="ledge" data-date="<?=$month?>">
		<colgroup>
			<col width="80" />
			<col width="180" />
			<col width="130" class="minimize" />
			<col width="130" class="expand" />
			<col width="130" class="expand" />
			<col width="130" class="expand" />
			<col width="130" />
			<col width="130" class="expand" />
			<col width="130" />
			<col width="*" class="expand" />
			<col width="60" class="expand" />
		</colgroup>
		<thead>
		<tr>
			<th rowspan="2">일자</th>
			<th rowspan="2">내용</th>
			<th rowspan="2" class="minimize">매입합계</th>
			<th rowspan="1" class="expand" colspan="3">매입합계</th>
			<th rowspan="2" class="">송금액</th>
			<th rowspan="2" class="">공제/환급액 등</th>
			<th rowspan="2" class="">잔액</th>
			<th rowspan="2" class="expand">비고</th>
			<th rowspan="2" class="expand"></th>
		</tr>
		<tr>
			<th class="expand">마감</th>
			<th class="expand">보정</th>
			<th class="expand">합계</th>
		</tr>
		</thead>
		<tbody>
		<?php
		//전월이월
		$_prev_total = $C_Settle->getPurchaseLedgerPrevSum($supplier_idx, $month);

		//마감금액
		$_prev_sum_settle_amount          = $_prev_total["sum_settle_amount"];
		$_prev_sum_ledge_adjust_amount    = $_prev_total["sum_ledger_adjust_amount"];
		$_prev_sum_ledge_tran_amount      = $_prev_total["sum_ledger_tran_amount"];
		$_prev_sum_ledge_refund_amount    = $_prev_total["sum_ledger_refund_amount"];
		$_prev_sum_stock_amount          = $_prev_total["sum_stock_amount"];

		//잔액
		$_prev_remain_total = $_prev_sum_settle_amount + $_prev_sum_ledge_adjust_amount - $_prev_sum_ledge_tran_amount + $_prev_sum_ledge_refund_amount + $_prev_sum_stock_amount;

		?>
		<tr>
			<td></td>
			<td>전월이월</td>
			<td class="text_right minimize"></td>
			<td class="text_right expand"></td>
			<td class="text_right expand"></td>
			<td class="text_right expand"></td>
			<td class="text_right"></td>
			<td class="text_right"></td>
			<td class="text_right">
				<?php
				//선급금 사용 공급처일 경우 잔액 x -1
				if($vendor_use_prepay == "Y") {
					echo number_format($_prev_remain_total * -1);
				}else{
					echo number_format($_prev_remain_total);
				}
				?>
			</td>
			<td class="expand"></td>
			<td class="expand"></td>
		</tr>
		<?php
		//합계 계산
		$_total_closing = 0;
		$_total_adjust = 0;
		$_total_tran = 0;
		$_total_refund = 0;
		$_total_remain = $_prev_remain_total;
		foreach ($_list as $row) {

			$closing_settle_amount = $row["closing_settle_amount"];
			$adjust_settle_amount = $row["adjust_settle_amount"];
			$sum_ledge_adjust_amount = $row["sum_ledger_adjust_amount"];
			$sum_ledge_tran_amount = $row["sum_ledger_tran_amount"];
			$sum_ledge_refund_amount = $row["sum_ledger_refund_amount"];

			//보정금액 합계
			$adjust_total = $adjust_settle_amount + $sum_ledge_adjust_amount;
			//매입합계
			$purchase_total = $closing_settle_amount + $adjust_total;

			//잔액
			$remain_total = $purchase_total - $sum_ledge_tran_amount + $sum_ledge_refund_amount;

			$_total_remain += $remain_total;

			$_total_closing += $closing_settle_amount;
			$_total_adjust += $adjust_settle_amount + $sum_ledge_adjust_amount;
			$_total_tran += $sum_ledge_tran_amount;
			$_total_refund += $sum_ledge_refund_amount;

			$memo = nl2br($row["ledger_memo"]);
		?>
		<tr data-memo_idx="<?=$row["ledger_memo_idx"]?>">
			<td><?=date('m/d', strtotime($row["dt"]))?></td>
			<td></td>
			<td class="text_right minimize"><?=number_format($purchase_total)?></td>
			<td class="text_right expand"><?=number_format($closing_settle_amount)?></td>
			<td class="text_right expand"><?=number_format($adjust_total)?></td>
			<td class="text_right expand"><?=number_format($purchase_total)?></td>
			<td class="text_right"><?=number_format($sum_ledge_tran_amount)?></td>
			<td class="text_right"><?=number_format($sum_ledge_refund_amount)?></td>
			<td class="text_right">
				<?php
				//선급금 사용 공급처일 경우 잔액 x -1
				if($vendor_use_prepay == "Y") {
					echo number_format($_total_remain * -1);
				}else{
					echo number_format($_total_remain);
				}
				?>
			</td>
			<td class="text_left expand ledger_memo_wrap" data-idx="<?=$row["ledger_memo_idx"]?>" data-date="<?=$row["dt"]?>"><?=$memo?></td>
			<td class="expand"><a href="javascript:;" class="xsmall_btn btn-ledger-memo-modify" data-idx="<?=$row["ledger_memo_idx"]?>" data-date="<?=$row["dt"]?>" data-target_idx="<?=$supplier_idx?>" data-type="<?=$ledge_type?>">메모</a></td>
		</tr>
		<?php

			//세부항목 가져오기
			$_detail_list = "";
			$_detail_list = $C_Settle->getLedgerDetail($supplier_idx, $row["dt"], $ledge_type);

			if($_detail_list){
				foreach ($_detail_list as $detail) {
					?>
					<tr class="detail" data-ledger_idx="<?=$detail["ledger_idx"]?>">
						<td></td>
						<td class="text_left"><?= $detail["ledger_title"] ?></td>
						<td class="text_right minimize"></td>
						<td class="text_right expand"></td>
						<td class="text_right expand"><?= number_format($detail["ledger_adjust_amount"]) ?></td>
						<td class="text_right expand"></td>
						<td class="text_right"><?= number_format($detail["ledger_tran_amount"]) ?></td>
						<td class="text_right"><?= number_format($detail["ledger_refund_amount"]) ?></td>
						<td class="text_right"></td>
						<td class="text_left expand">
							<span class="ledger_memo"><?= $detail["ledger_memo"] ?></span>
							<?php if($detail["charge_idx"] == "0" && $detail["tran_idx"] == "0") {?>
								<div style="float:right"><a href="javascript:;" class="xsmall_btn btn-ledger-memo-delete" data-idx="<?=$detail["ledger_idx"]?>">삭제</a></div>
							<?php } ?>
						</td>
						<td class="expand">
							<?php if($detail["charge_idx"] == "0" && $detail["tran_idx"] == "0") {?>
							<a href="javascript:;" class="xsmall_btn btn-ledger-memo-modify2" data-idx="<?=$detail["ledger_idx"]?>">수정</a>
							<?php }?>
						</td>
					</tr>
					<?php
				}
			}
			// 사입 제품 가격 확인 링크를 위해 - 엑셀에는 반영하지 않음
			if ($stock_order_total = $C_Settle->getStockOrderAmountForDate($supplier_idx, $row["dt"])) {
				?>
				<tr class="detail">
					<td></td>
					<td class="text_left"><a class="link" href="/stock/stock_confirm_list.php?period_type=stock_confirm_date&supplier_idx=<?=$supplier_idx?>&date_start=<?=$row["dt"]?>&date_end=<?=$row["dt"]?>&stock_is_confirm=Y" target="_blank">사입 발주 확정</a></td>
					<td class="text_right minimize"></td>
					<td class="text_right expand"><?= number_format($stock_order_total) ?></td>
					<td class="text_right expand"></td>
					<td class="text_right expand"></td>
					<td class="text_right"></td>
					<td class="text_right"></td>
					<td class="text_right"></td>
					<td class="text_left expand">
					</td>
					<td class="expand">
					</td>
				</tr>
				<?php
			}
		}
		?>
		<tr>
			<th colspan="2">합계</th>
			<th class="text_right minimize"><?=number_format($_total_closing + $_total_adjust)?></th>
			<th class="text_right expand"><?=number_format($_total_closing)?></th>
			<th class="text_right expand"><?=number_format($_total_adjust)?></th>
			<th class="text_right expand"><?=number_format($_total_closing + $_total_adjust)?></th>
			<th class="text_right"><?=number_format($_total_tran)?></th>
			<th class="text_right"><?=number_format($_total_refund)?></th>
			<th class="text_right"></th>
			<th class="expand"></th>
			<th class="expand"></th>
		</tr>
		</tbody>
	</table>
</div>
<?php
}
?>