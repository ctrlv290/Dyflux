<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 부분입고 확인 페이지
 */
//Page Info
$pageMenuIdx = 194;
//Init
include_once "../_init_.php";

session_cache_limiter('private-no-expire');

$C_Stock = new Stock();

$stock_idx = $_GET["stock_idx"];

if(is_numeric($stock_idx)) {
	$_view = $C_Stock->getStockDataDetail($stock_idx);
	if(!$_view){
		put_msg_and_close("잘못된 접근입니다.");
		exit;
	}else{
		$mode = "regist_stock_due_delay";

		extract($_view);

		if($stock_is_proc != "N") {
			put_msg_and_close("이미 입고 처리된 상태입니다.");
		}else{
			$view_code      = "";
			$is_stock_order = false;
			$is_order       = false;

			if ($stock_kind == "STOCK_ORDER") {
				//발주한 재고라면
				$view_code      = $stock_order_idx;
				$is_stock_order = true;
			} elseif ($stock_kind == "RETURN" || $stock_kind == "EXCHANGE") {
				//교환,반품 등 주문과 관련된 재고라면
				$view_code = $order_idx;
				$is_order  = true;
			}
		}

	}
}else{
	put_msg_and_close("잘못된 접근입니다..");
}
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyForm" id="dyForm" method="post" class="<?php echo $mode?>">
				<input type="hidden" name="mode" value="<?php echo $mode?>" />
				<input type="hidden" name="stock_idx" value="<?php echo $stock_idx?>" />
				<div class="tb_wrap">
					<p class="sub_tit2">상품정보</p>
					<table class="no_border">
						<tr>
							<td>
								<table>
									<colgroup>
										<col width="150">
										<col width="*">
									</colgroup>
									<tbody>
									<tr>
										<th>코드</th>
										<td class="text_left">
											<?=$view_code?>
										</td>
									</tr>
									<?php if($is_stock_order){?>
										<tr>
											<th>발주일</th>
											<td class="text_left">
												<?=$stock_order_date?>
											</td>
										</tr>
									<?php }?>
									<tr>
										<th>입고예정일</th>
										<td class="text_left">
											<?=$stock_due_date?>
										</td>
									</tr>
									<tr>
										<th>공급처</th>
										<td class="text_left">
											<?=$supplier_name?>
										</td>
									</tr>
									<tr>
										<th>상품옵션코드</th>
										<td class="text_left">
											<?=$product_option_idx?>
										</td>
									</tr>
									<tr>
										<th>상품명</th>
										<td class="text_left">
											<?=$product_name?>
										</td>
									</tr>
									<tr>
										<th>옵션</th>
										<td class="text_left">
											<?=$product_option_name?>
										</td>
									</tr>
									<tr>
										<th>원가(매입가)</th>
										<td class="text_left">
											<?=number_format($stock_unit_price);?> 원
										</td>
									</tr>
									<?php if($is_order){?>
										<tr>
											<th>구매자 정보</th>
											<td class="text_left">
												<?=$view_code?>
											</td>
										</tr>
									<?php }?>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</div>
				<div class="tb_wrap">
					<p class="sub_tit2">지연이력</p>
					<table class="no_border">
						<tr>
							<td>
								<table>
									<colgroup>
										<col width="150">
										<col width="*">
									</colgroup>
									<tbody>
									<tr>
										<th>예정수량</th>
										<td class="text_left">
											<?=number_format($stock_due_amount)?>
										</td>
									</tr>
									<tr>
										<th>지연 입고 예정일</th>
										<td class="text_left">
											<input type="text" name="stock_due_delay_date" class="w80px jqDate stock_due_delay_date" value="<?=date('Y-m-d')?>" />
										</td>
									</tr>
									<tr>
										<th>메모</th>
										<td class="text_left">
											<input type="text" name="stock_due_delay_msg" class="w300px" />
										</td>
									</tr>
									<tr>
										<th>첨부파일</th>
										<td class="text_left">
											<a href="javascript:;" class="btn green_btn btn_relative btn-stock-file-idx" id="btn-stock-file-idx">
												파일업로드
											</a>
											<span class="uploaded-file span_stock_due_delay_file_idx"></span>
											<input type="hidden" name="stock_due_delay_file_idx" id="stock_due_delay_file_idx" value="<?=$stock_due_delay_file_idx?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
						<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/stock.due.js"></script>
<script src="/js/fileupload.js"></script>
<script>
	window.name = 'stock_due_delay_write_pop';
	StockDue.StockDueDelayWriteInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
