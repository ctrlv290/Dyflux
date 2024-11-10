<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품별매출통계 - 상품 판매처별 팝업 페이지
 */
//Page Info
$pageMenuIdx = 130;
//Init
include_once "../_init_.php";

$mode                = $_POST["mode"];
$product_idx         = $_POST["product_idx"];
$product_option_idx  = $_POST["product_option_idx"];
$product_name        = $_POST["product_name"];
$product_option_name = $_POST["product_option_name"];
$date_start          = $_POST["date_start"];
$date_end            = $_POST["date_end"];

$C_Settle = new Settle();

$_list = $C_Settle->getSettleProductEachSeller($product_option_idx, $date_start, $date_end);

?>
<div class="container popup transaction_pop">
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm2" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<p>
					상품코드 : <?=$product_idx?><br>
					옵션코드 : <?=$product_option_idx?><br>
					상품명 : <?=$product_name?><br>
					옵션명 : <?=$product_option_name?><br>
					검색기간 : <?=$date_start?> ~ <?=$date_end?><br>
				</p>
				<div class="tb_wrap">
					<table autofocus="autofocus">
						<colgroup>
							<col width="*">
							<col width="*">
							<col width="*">
							<col width="*">
						</colgroup>
						<thead>
						<tr>
							<th>판매처코드</th>
							<th>판매처명</th>
							<th>판매수량</th>
							<th>판매가금액</th>
						</tr>
						</thead>
						<tbody>
						<?php
						if($_list){
							foreach($_list as $settle) {
						?>
						<tr>
							<td><?= $settle["seller_idx"] ?></td>
							<td><?= $settle["seller_name"] ?></td>
							<td class="text_right"><?= number_format($settle["product_count"]) ?></td>
							<td class="text_right"><?= number_format($settle["settle_sale_supply"]) ?></td>
						</tr>
						<?php
							}
						}else{
						?>
						<tr>
							<td colspan="4">검색된 내역이 없습니다.</td>
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
					<a href="javascript:;" class="large_btn btn-common-pop-close">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	SettleProduct.ProductSalePopSellerInit();
</script>

