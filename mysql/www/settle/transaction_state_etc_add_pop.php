<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: CS 팝업 창에서 주문 생성 시 상품 검색 팝업
 */
//Page Info
$pageMenuIdx = 226;

$prev_text = "전일미수금액";
$today_text = "판매금액";
$tran_text = "입금액";

if($_GET["tran_type"] == "PURCHASE_ETC"){
	$pageMenuIdx = 227;

	$prev_text = "전일선급금액";
	$today_text = "발생금액";
	$tran_text = "송금액";
}

//Init
include_once "../_init_.php";
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="searchFormPop" id="searchFormPop" method="post" action="transaction_state_proc.php">
				<input type="hidden" name="mode" value="etc_add" />
				<input type="hidden" name="tran_type" value="<?=$tran_type?>" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="200" />
							<col width="120" />
							<col width="120" />
							<col width="120" />
							<col width="120" />
							<col width="120" />
							<col width="*" />
						</colgroup>
						<thead>
						<tr>
							<th>거래처</th>
							<th>일자</th>
							<th><?=$prev_text?></th>
							<th><?=$today_text?></th>
							<th><?=$tran_text?></th>
							<th>현재잔액</th>
							<th>비고</th>
						</tr>
						</thead>
						<tbody>
						<?php
						for($i=1;$i<11;$i++) {
						?>
						<tr>
							<td>
								<input type="text" name="target_name[]" class="w100per" value="" />
							</td>
							<td>
								<input type="text" name="tran_date[]" class="w100per jqDate" value="" readonly="readonly" />
							</td>
							<td>
								<input type="text" name="prev_amount[]" class="w100per onlyNumber" value="" />
							</td>
							<td>
								<input type="text" name="today_amount[]" class="w100per onlyNumber" value="" />
							</td>
							<td>
								<input type="text" name="tran_amount[]" class="w100per onlyNumber" value="" />
							</td>
							<td>
								<input type="text" name="remain_amount[]" class="w100per onlyNumber" value="" />
							</td>
							<td>
								<input type="text" name="tran_memo[]" class="w100per" value="" />
							</td>
						</tr>
						<?php
						}
						?>
						</tbody>
					</table>
				</div>
			</form>

			<div class="btn_set">
				<div class="center">
					<a href="javascript:" id="btn-pop-save" class="large_btn">저장</a>
					<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/settle.purchase.js"></script>
<script>
	SettlePurchase.TransactionStateEtcPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
