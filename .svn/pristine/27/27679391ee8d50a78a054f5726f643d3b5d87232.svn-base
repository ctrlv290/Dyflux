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

$ledge_type = "LEDGER_SALE";

$date_start_year  = $_POST["date_start_year"];
$date_start_month = $_POST["date_start_month"];
$date_end_year    = $_POST["date_end_year"];
$date_end_month   = $_POST["date_end_month"];
$seller_idx       = $_POST["seller_idx"];

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


//벤더사 판매처 인지 확인
$C_Seller = new Seller();
$_seller_view = $C_Seller -> getUseSellerAllData($seller_idx);

$seller_type = $_seller_view["seller_type"];

?>

<?php
foreach($month_ary as $month) {
	$_list = $C_Settle->getSaleLedgerList($seller_idx, $month);
	?>

	<div class="btn_set">
		<p class="sub_tit2"><?=date('Y년 m월', strtotime($month."-01"))?></p>
		<div class="right">
			<?php if(isDYLogin()){?>
			<a href="javascript:;" class="btn btn-ledger-create-xls" data-month_start="<?=$month?>"  data-month_end="<?=$month?>" data-month="<?=$month?>" data-target_idx="<?=$seller_idx?>" data-type="<?=$ledge_type?>">파일생성</a>
			<label>
				<input type="checkbox" name="minimize" class="chk-ledge-minimize" data-date="<?=$month?>"> 상세내역 제외
			</label>
			<?php }?>
		</div>
	</div>

	<div class="tb_wrap">
		<table class="ledge <?=(!isDYLogin()) ? "shrink" : ""?>" data-date="<?=$month?>">
			<colgroup>
				<col width="80" />
				<col width="180" />
				<col width="130" class="minimize" />
				<?php if(isDYLogin()){?>
				<col width="130" class="expand" />
				<col width="130" class="expand" />
				<col width="130" class="expand" />
				<col width="130" />
				<col width="130" class="expand" />
				<col width="130" />
				<col width="*" class="expand" />
				<col width="60" class="expand" />
				<?php } else { ?>
					<col width="130" class="" />
					<col width="130" class="" />
					<col width="130" class="" />
					<col width="130" class="" />
				<?php } ?>
			</colgroup>
			<thead>
			<tr>
				<th rowspan="2">일자</th>
				<th rowspan="2">내용</th>
				<th rowspan="2" class="minimize">매출합계</th>
				<?php if(isDYLogin()){?>
				<th rowspan="1" class="expand" colspan="3">매출합계</th>
				<th rowspan="2" class="">실입금액</th>
				<th rowspan="2" class="">공제/환급액 등</th>
				<?php } else { ?>
				<th rowspan="1" class="">실입금액</th>
				<?php } ?>
				<th rowspan="2" class="">잔액</th>
				<th rowspan="2" class="expand">비고</th>
				<th rowspan="2" class="expand"></th>
			</tr>
			<?php if(isDYLogin()){?>
			<tr>
				<th class="expand">마감</th>
				<th class="expand">보정</th>
				<th class="expand">합계</th>
			</tr>
			<?php } ?>
			</thead>
			<tbody>
			<?php
			//전월이월
			$_prev_total = $C_Settle->getSaleLedgerPrevSum($seller_idx, $month);

			//마감금액
			$_prev_sum_settle_amount          = $_prev_total["sum_settle_amount"];
			$_prev_sum_ledge_adjust_amount    = $_prev_total["sum_ledger_adjust_amount"];
			$_prev_sum_ledge_tran_amount      = $_prev_total["sum_ledger_tran_amount"];
			$_prev_sum_ledge_refund_amount    = $_prev_total["sum_ledger_refund_amount"];

			//잔액
			$_prev_remain_total = $_prev_sum_settle_amount + $_prev_sum_ledge_adjust_amount - $_prev_sum_ledge_tran_amount + $_prev_sum_ledge_refund_amount;

			?>
			<tr>
				<td></td>
				<td>전월이월</td>
				<td class="text_right minimize"></td>
				<?php if(isDYLogin()){?>
				<td class="text_right expand"></td>
				<td class="text_right expand"></td>
				<td class="text_right expand"></td>
				<td class="text_right"></td>
				<td class="text_right"></td>
				<?php } else { ?>
					<td class="text_right"></td>
				<?php } ?>
				<td class="text_right">
					<?php
					if($seller_type == "VENDOR_SELLER") {
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
					<?php if(isDYLogin()){?>
					<td class="text_right expand"><?=number_format($closing_settle_amount)?></td>
					<td class="text_right expand"><?=number_format($adjust_total)?></td>
					<td class="text_right expand"><?=number_format($purchase_total)?></td>
					<td class="text_right"><?=number_format($sum_ledge_tran_amount)?></td>
					<td class="text_right"><?=number_format($sum_ledge_refund_amount)?></td>
					<?php } else { ?>
					<td class="text_right"><?=number_format($sum_ledge_tran_amount)?></td>
					<?php } ?>
					<td class="text_right">
						<?php
						if($seller_type == "VENDOR_SELLER") {
							echo number_format($_total_remain * -1);
						}else{
							echo number_format($_total_remain);
						}
						?>
					</td>
					<td class="text_left expand ledger_memo_wrap" data-idx="<?=$row["ledger_memo_idx"]?>" data-date="<?=$row["dt"]?>"><?=$memo?></td>
					<td class="expand"><a href="javascript:;" class="xsmall_btn btn-ledger-memo-modify" data-idx="<?=$row["ledger_memo_idx"]?>" data-date="<?=$row["dt"]?>" data-target_idx="<?=$seller_idx?>" data-type="<?=$ledge_type?>">메모</a></td>
				</tr>
				<?php

				//세부항목 가져오기
				$_detail_list = "";
				$_detail_list = $C_Settle->getLedgerDetail($seller_idx, $row["dt"], $ledge_type);

				if($_detail_list){
					foreach ($_detail_list as $detail) {
						?>
						<tr class="detail" data-ledger_idx="<?=$detail["ledger_idx"]?>">
							<td></td>
							<td class="text_left"><?= $detail["ledger_title"] ?></td>
							<td class="text_right minimize"></td>
							<?php if(isDYLogin()){?>
							<td class="text_right expand"></td>
							<td class="text_right expand"><?= number_format($detail["ledger_adjust_amount"]) ?></td>
							<td class="text_right expand"></td>
							<td class="text_right"><?= number_format($detail["ledger_tran_amount"]) ?></td>
							<td class="text_right"><?= number_format($detail["ledger_refund_amount"]) ?></td>
							<?php } else { ?>
								<td class="text_right"><?= number_format($detail["ledger_tran_amount"]) ?></td>
							<?php } ?>
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
			}
			?>
			<tr>
				<th colspan="2">합계</th>
				<th class="text_right minimize"><?=number_format($_total_closing + $_total_adjust)?></th>
				<?php if(isDYLogin()){?>
				<th class="text_right expand"><?=number_format($_total_closing)?></th>
				<th class="text_right expand"><?=number_format($_total_adjust)?></th>
				<th class="text_right expand"><?=number_format($_total_closing + $_total_adjust)?></th>
				<th class="text_right"><?=number_format($_total_tran)?></th>
				<th class="text_right"><?=number_format($_total_refund)?></th>
				<?php } else { ?>
				<th class="text_right"><?=number_format($_total_tran)?></th>
				<?php } ?>
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