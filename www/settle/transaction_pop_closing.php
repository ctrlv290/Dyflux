<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 교환 팝업 페이지
 */
//Page Info
$pageMenuIdx = 122;
//Init
include_once "../_init_.php";

$mode = "transaction_closing";

$C_SETTLE = new Settle();

$closingInfo = $C_SETTLE->getClosingInfo();

?>
<div class="container popup transaction_pop">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm2" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<div class="tb_wrap">
					<table autofocus="autofocus">
						<colgroup>
							<col width="150">
							<col width="*">
						</colgroup>
						<tbody>
						<tr>
							<th>마감시간</th>
							<td class="text_left">
								<?=date('Y-m-d H:i:s');?>
							</td>
						</tr>
						<tr>
							<th>총 매출액</th>
							<td class="text_left">
								<?=number_format($closingInfo["order_unit_price"])?>
							</td>
						</tr>
						<tr>
							<th>총 주문수</th>
							<td class="text_left">
								<?=$closingInfo["order_cnt"];?>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</form>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" id="btn-save" class="large_btn red_btn ">마감처리</a>
					<a href="javascript:;" class="large_btn btn-common-pop-close">취소</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	SettleTransaction.TransactionClosingPopInit();
</script>

