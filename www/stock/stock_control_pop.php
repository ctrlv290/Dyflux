<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 작업 팝업 페이지
 */

//Page Info
$pageMenuIdx = 199;
//Init
include_once "../_init_.php";

$product_option_idx = $_GET["product_option_idx"];
$stock_control_type = $_GET["stock_control_type"];
$stock_unit_price = $_GET["stock_unit_price"];

$C_Product = new Product();
$C_Stock = new Stock();

$mode = "control_stock_amount";

//상품 옵션 정보
$_view = $C_Product->getProductOptionDataDetail($product_option_idx);

if(!$_view){
	put_msg_and_close("존재하지 않는 상품입니다.");
	exit;
}else{

	extract($_view);

	//원가 리스트
	$_list_price = $C_Stock -> getStockUnitPriceListByProductOption($product_option_idx);
}

$_control_list = array(
	"NORMAL" => '정상',
	"ABNORMAL" => '양품',
	"HOLD" => '보류',
	"BAD" => '불량재고',
	"LOSS" => '분실재고',
	"DISPOSAL" => '일반폐기'
);

if(!array_key_exists($stock_control_type, $_control_list)){
	put_msg_and_close("잘못된 접근입니다.");
	exit;
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
				<input type="hidden" name="product_option_idx" id="product_option_idx" value="<?php echo $product_option_idx?>" />
				<input type="hidden" name="current_stock_amount" id="current_stock_amount" value="0" />
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
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</div>
				<div class="tb_wrap">
					<p class="sub_tit2">재고선택</p>
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
										<th>원가(매입가)</th>
										<td class="text_left">
											<select name="stock_unit_price" class="stock_unit_price">
												<?php
												foreach($_list_price as $p) {
													$selected = "";
													if($p["stock_unit_price"] == $stock_unit_price){
														$selected = 'selected="selected"';
													}
													echo '<option value="'.$p["stock_unit_price"].'" '.$selected.'>'.$p["stock_unit_price"].' 원</option>';
												}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<th>재고타입</th>
										<td class="text_left">
											<select name="stock_control_status" class="stock_control_status">
												<?php
												foreach($_control_list as $key => $val) {
													$selected = "";
													if($key == $stock_control_type){
														$selected = 'selected="selected"';
													}
													echo '<option value="'.$key.'" '.$selected.'>'.$val.'</option>';
												}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<th>재고수량</th>
										<td class="text_left txt_stock_amount">

										</td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</table>
				</div>

				<div class="tb_wrap">
					<p class="sub_tit2">재고처리</p>
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
										<th>처리타입</th>
										<td class="text_left">
											<select name="stock_status" class="stock_status">
												<option value="NORMAL">정상</option>
												<option value="BAD">불량</option>
												<option value="ABNORMAL">양품</option>
											</select>
											<select name="stock_status2" class="stock_status2 dis_none">
											</select>
										</td>
									</tr>
									<tr>
										<th>수량</th>
										<td class="text_left">
											<input type="text" name="stock_amount" class="w50px onlyNumber stock_amount" value="0" maxlength="6" />개
										</td>
									</tr>
									<tr>
										<th>재고메모</th>
										<td class="text_left">
											<input type="text" name="stock_msg" class="w300px" />
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
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">재고변경</a>
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
<script src="/js/page/stock.product.js"></script>
<script src="/js/fileupload.js"></script>
<script>
	window.name = 'stock_control_pop';
	StockProduct.StockControlPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
