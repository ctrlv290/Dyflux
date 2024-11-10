<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 로그 조회 팝업 페이지
 */

//Page Info
$pageMenuIdx = 200;
//Init
include_once "../_init_.php";

$product_option_idx         = $_GET["product_option_idx"];
$stock_unit_price           = $_GET["stock_unit_price"];
$date_start                 = $_GET["date_start"];
$date_end                   = $_GET["date_end"];

$C_Stock = new Stock();

//상품 옵션 정보
$_view = $C_Stock->getProductOptionDetailWithStockAmount($product_option_idx, $stock_unit_price);
//원가 정보
$_list_price = $C_Stock -> getStockUnitPriceListByProductOption($product_option_idx);

if(!$_view){
	put_msg_and_close("존재하지 않는 상품입니다.");
	exit;
}else{

	extract($_view);
}

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
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
								<tr>
									<th>정상재고 수량</th>
									<td class="text_left">
										<?=number_format($stock_amount_NORMAL)?>
									</td>
								</tr>
                                <tr class="current_stock_amount_tr" style="display:none">
                                    <th>원가별 정상재고 수량</th>
                                    <td class="text_left current_stock_amount">
                                        <?=number_format($stock_amount_unit_price_NORMAL)?>
                                    </td>
                                </tr>
                                </tbody>
							</table>
						</td>
					</tr>
				</table>
			</div>
			<form name="searchForm" id="searchForm" method="get">
				<input type="hidden" name="product_option_idx" id="product_option_idx" value="<?php echo $product_option_idx?>" />
				<div class="find_wrap">
					<div class="finder">
						<div class="finder_set">
							<div class="finder_col">
								<span class="text">검색기간</span>
								<input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
								~
								<input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
								<select class="sel_period_preset" id="period_preset_select"></select>
                                <span class="text">&nbsp원가(매입가)</span>
                                <select name="stock_unit_price" class="stock_unit_price" data-selected="">
                                    <option value="all">전체</option>
                                    <?php
                                        foreach($_list_price as $p) {
                                            $selected = "";
                                            if($p["stock_unit_price"] == $stock_unit_price){
                                                $selected = 'selected="selected"';
                                            }
                                            echo '<option value="'.$p["stock_unit_price"].'" '.$selected.'>'.number_format($p["stock_unit_price"]).'원</option>';
                                        }
                                    ?>
                                </select>
							</div>
						</div>
					</div>
					<div class="find_btn">
						<div class="table">
							<div class="table_cell">
								<a href="javascript:;" id="btn_searchBar" class="wide_btn btn_default">검색</a>
							</div>
						</div>
					</div>
					<a href="javascript:;" class="find_hide_btn">
						<i class="fas fa-angle-up up_btn"></i>
						<i class="fas fa-angle-down dw_btn"></i>
					</a>
				</div>
			</form>
			<div class="tb_wrap grid_tb">
				<table id="grid_list">
				</table>
				<div id="grid_pager"></div>
			</div>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/stock.product.js?v=191224"></script>
<script src="/js/fileupload.js"></script>
<script>
	window.name = 'stock_log_viewer_pop';
	StockProduct.StockLogViewerPopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
