<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 부분입고 확인 페이지
 */
//Page Info
$pageMenuIdx = 193;
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

		$mode = "stock_receiving_partial";

		extract($_view);

		$view_code = "";
		$is_stock_order = false;
		$is_order = false;

		if($stock_kind == "STOCK_ORDER"){
			//발주한 재고라면
			$view_code = $stock_order_idx;
			$is_stock_order = true;
		}elseif($stock_kind == "RETURN" || $stock_kind == "EXCHANGE"){
			//교환,반품 등 주문과 관련된 재고라면
			$view_code = $order_idx;
			$is_order = true;
		}

	}
}else{
	put_msg_and_close("잘못된 접근입니다..");
}


$stock_type_ary = array(

	array(
		"name" => "정상",
		"code" => "normal",
		"date_text" => "입고일",
		"date_name" => "stock_in_date",
	),
	array(
		"name" => "양품",
		"code" => "abnormal",
		"date_text" => "입고예정일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "불량",
		"code" => "bad",
		"date_text" => "입고일",
		"date_name" => "stock_in_date",
	),
	array(
		"name" => "부족",
		"code" => "shortage",
		"date_text" => "입고예정일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "교환",
		"code" => "exchange",
		"date_text" => "입고예정일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "분실",
		"code" => "loss",
		"date_text" => "입고예정일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "출고지회송 - 교환회송",
		"code" => "fac_return_exchange",
		"date_text" => "입고예정일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "출고지회송 - 반품회송",
		"code" => "fac_return_back",
		"date_text" => "입고예정일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "구매자회송 - 교환불가회송",
		"code" => "buyer_out_no_exchange",
		"date_text" => "입고예정일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "구매자회송 - 반품불가회송",
		"code" => "buyer_out_no_back",
		"date_text" => "입고예정일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "보류",
		"code" => "hold",
		"date_text" => "입고일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "기타처리",
		"code" => "etc",
		"date_text" => "처리일",
		"date_name" => "stock_due_date",
	),

);

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
				<input type="hidden" name="stock_ref_idx" value="<?php echo $stock_ref_idx?>" />
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
					<p class="sub_tit2">입고처리</p>
					<table class="no_border">
						<tr>
							<td>
								<table>
									<colgroup>
										<col width="200">
										<col width="*">
									</colgroup>
									<tbody>
									<tr>
										<th>예정수량</th>
										<td class="text_left txt_stock_due_amount">
											<?=number_format($stock_due_amount)?>
										</td>
									</tr>
									<?php
									foreach($stock_type_ary as $sta){
									?>
									<tr>
										<th><?=$sta["name"]?></th>
										<td class="text_left">
											<?=$sta["date_text"]?> <input type="text" name="stock_in_date_<?=$sta["code"]?>" class="w80px jqDate stock_due_date" value="<?=date('Y-m-d')?>" />
											&nbsp;<input type="text" name="stock_amount_<?=$sta["code"]?>" class="w50px onlyNumber stock_amount" value="0" />개
											&nbsp;&nbsp;
											메모 <input type="text" name="stock_msg_<?=$sta["code"]?>" class="w200px" />
										</td>
									</tr>
									<?php
									}
									?>
									<tr>
										<th>첨부파일</th>
										<td class="text_left">

											<a href="javascript:;" class="btn green_btn btn_relative btn-stock-file-idx" id="btn-stock-file-idx">
												파일업로드
											</a>
											<span class="uploaded-file span_stock_file_idx"></span>
											<input type="hidden" name="stock_file_idx" id="stock_file_idx" value="<?=$stock_file_idx?>" />
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
	window.name = 'stock_due_write_pop';
	StockDue.StockReceivingPartialInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
