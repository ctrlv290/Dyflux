<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 입고내역 페이지
 */
//Page Info
$pageMenuIdx = 196;
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


		}elseif($stock_kind == "RETURN" || $stock_kind == "EXCHANGE" || $stock_kind == "BACK"){
			//교환,반품 등 주문과 관련된 재고라면
			$view_code = $order_idx;
			$is_order = true;

			$C_CS = new CS();
			$_order_view = $C_CS->getOrderDetail($view_code);
		}

		$is_proc_n_count = $C_Stock -> getStockNonProcCountByStockIdx($stock_ref_idx);

		//파일 목록
		$C_Files = new Files();
		$_file_list = $C_Files -> getFileListByRef('DY_STOCK', $stock_ref_idx);
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
		"name" => "불량",
		"code" => "bad",
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
		"name" => "출고지배송",
		"code" => "factory_shipping",
		"date_text" => "입고예정일",
		"date_name" => "stock_due_date",
	),
	array(
		"name" => "보류",
		"code" => "hold",
		"date_text" => "입고일",
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
				<input type="hidden" name="stock_idx" id="stock_idx" value="<?php echo $stock_idx?>" />
				<input type="hidden" name="stock_ref_idx" id="stock_ref_idx" value="<?php echo $stock_ref_idx?>" />
				<input type="hidden" name="order_idx" id="order_idx" value="<?php echo $order_idx?>" />
				<input type="hidden" name="stock_order_idx" id="stock_order_idx" value="<?php echo $stock_order_idx?>" />
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
												<!--<?=$view_code?>-->
												<?=$_order_view["order_name"]?>
												/
												<?=$_order_view["order_tp_num"]?>
												/
												<?=$_order_view["order_hp_num"]?>
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
					<p class="sub_tit2">입고처리완료 내역</p>
					<div class="tb_wrap grid_tb">
						<table id="grid_list">
						</table>
					</div>
				</div>
				<?php
				if($is_proc_n_count > 0){
				?>
				<div class="tb_wrap">
					<p class="sub_tit2">입고미처리 내역</p>
					<div class="tb_wrap grid_tb">
						<table id="grid_list2">
						</table>
					</div>
				</div>
				<?php
				}
				?>

				<?php
				if(count($_file_list) > 0){
				?>
				<div class="tb_wrap">
					<p class="sub_tit2">첨부파일</p>
					<table class="no_border">
						<tr>
							<td>
								<table>
									<colgroup>
										<col width="150">
										<col width="*">
									</colgroup>
									<tbody>
									<?php
									$i = 0;
									foreach($_file_list as $file) {
									?>
									<tr>
										<?php
										if($i == 0){
										?>
										<th rowspan="<?=count($_file_list)?>">첨부파일</th>
										<?php
										}
										?>
										<td class="text_left">
											<a href="javascript:;" class="btn-stock-proc-file-down link" data-stock_file_idx="<?=$file["file_idx"] ?>" data-stock_file_name="<?=$file["save_filename"] ?>"><?=$file["user_filename"] ?></a>
										</td>
									</tr>
									<?php
										$i++;
									}
									?>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</div>
				<?php
				}
				?>
				<div class="btn_set">
					<div class="center">
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
	window.name = 'stock_confirm_detail_pop';
	StockDue.StockConfirmDetailInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
