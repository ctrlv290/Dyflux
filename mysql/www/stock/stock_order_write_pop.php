<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 발주 신규/수정 페이지
 */
//Page Info
$pageMenuIdx = 184;     //신규 발주
//$pageMenuIdx = 185;   //발주 정보
//Init
include_once "../_init_.php";

session_cache_limiter('private-no-expire');

$mode                      = "add";
$stock_order_idx           = $_GET["stock_order_idx"];
$stock_order_date          = date('Y-m-d');
$stock_order_in_date       = date('Y-m-d');
$stock_order_officer_name  = "";
$stock_order_officer_tel   = "";
$supplier_idx              = "";
$stock_order_supplier_name = "";
$stock_order_supplier_tel  = "";
$stock_order_receiver_name = "";
$stock_order_receiver_tel  = "";
$stock_order_receiver_addr = "";
$stock_order_is_order      = "";
$stock_order_is_del        = "";
$member_idx                = "";
$stock_order_regdate       = "";
$stock_order_regip         = "";
$stock_order_moddate       = "";
$stock_order_modip         = "";
$last_member_idx           = "";
$is_use                    = "Y";

$C_SiteInfo = new SiteInfo();
$C_Supplier = new Supplier();

//사이트 정보 얻기
$_site_info = $C_SiteInfo->getSiteInfo();

//사이트 담당자 리스트 얻기
$_site_officer_list = $C_SiteInfo->getOfficerList();

//$stock_order_receiver_name = $_site_officer_list[0]["name"];
//$stock_order_receiver_tel = $_site_officer_list[0]["tel"];
//$stock_order_receiver_addr = $_site_info["addr1"] . " " . $_site_info["addr2"];

//공급처 목록 얻기
$_supplier_list = $C_Supplier->getUseSupplierList();


//발주정보 확인 일 경우
$C_Stock = new Stock();
if($stock_order_idx){
	$_view = $C_Stock -> getStockOrderData($stock_order_idx);

	if(!$_view){
		put_msg_and_close("잘못된 접근입니다.");
		exit;
	}else{
		$mode = "mod";
		$pageMenuIdx = 185;   //발주 정보
		extract($_view);
	}
}

//발주서 복사 파라미터
$iscopy = $_GET["iscopy"];

//발주서 복사일 경우
if($iscopy == "Y"){
	$pageMenuIdx = 184;     //신규 발주
	$mode = "add";
	$stock_order_is_order = "";
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
				<input type="hidden" name="stock_order_idx" value="<?php echo $stock_order_idx?>" />
				<input type="hidden" name="stock_order_is_order" value="<?php echo $stock_order_is_order?>" />
				<input type="hidden" name="iscopy" value="<?php echo $iscopy?>" />
				<div class="tb_wrap">
					<table class="no_border">
						<colgroup>
							<col width="49%">
							<col width="2%">
							<col width="49%">
						</colgroup>
						<tbody>
						<tr>
							<td>
								<table>
									<colgroup>
										<col width="150">
										<col width="*">
									</colgroup>
									<tbody>
									<tr>
										<th>발주일 <span class="lb_red">필수</span></th>
										<td class="text_left">
											<input type="text" name="stock_order_date" class="w100px jqDate" readonly="readonly" value="<?=$stock_order_date?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</td>
							<td></td>
							<td>
								<table>
									<colgroup>
										<col width="150">
										<col width="*">
									</colgroup>
									<tbody>
									<tr>
										<th>입고예정일 <span class="lb_red">필수</span></th>
										<td class="text_left">
											<input type="text" name="stock_order_in_date" class="w100px jqDate" readonly="readonly" value="<?=$stock_order_in_date?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
						</tbody>
					</table>
					<table class="no_border">
						<colgroup>
							<col width="49%">
							<col width="2%">
							<col width="49%">
						</colgroup>
						<tbody>
						<tr>
							<td class="text_left vtop">
								<p class="sub_tit2">발주사</p>
								<table>
									<colgroup>
										<col width="150">
										<col width="*">
									</colgroup>
									<tbody>
									<tr>
										<th>상호명</th>
										<td class="text_left"><?=$_site_info["site_name"]?></td>
									</tr>
									<tr>
										<th>대표자명</th>
										<td class="text_left"><?=$_site_info["ceo_name"]?></td>
									</tr>
									<tr>
										<th>주소</th>
										<td class="text_left"><?=$_site_info["addr1"]?> <?=$_site_info["addr2"]?></td>
									</tr>
									<tr>
										<th>사업자번호</th>
										<td class="text_left"><?=$_site_info["license_no"]?></td>
									</tr>
									<tr>
										<th>담당자</th>
										<td class="text_left">
											<input type="text" class="w100px" name="stock_order_officer_name" value="<?=$stock_order_officer_name?>" />
											<select name="stock_order_officer_name_sel">
												<option value="">담당자 선택</option>
												<?php
												foreach($_site_officer_list as $of){
													echo '<option value="'.$of["no"].'">'.$of["name"].'</option>';
												}
												?>
											</select>
										</td>
									</tr>
									<tr>
										<th>연락처</th>
										<td class="text_left">
											<input type="text" name="stock_order_officer_tel" class="w100px" value="<?=$stock_order_officer_tel?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</td>
							<td></td>
							<td class="text_left vtop">
								<p class="sub_tit2">공급처</p>
								<table>
									<colgroup>
										<col width="150">
										<col width="*">
									</colgroup>
									<tbody>
									<tr>
										<th>상호명</th>
										<td class="text_left">
											<?php
											if($mode == "add") {
											?>
												<select name="supplier_idx">
													<option value="">공급처를 선택해주세요.</option>
													<?php
													foreach ($_supplier_list as $sup) {
														$selected = "";
														if ($sup["member_idx"] == $supplier_idx) {
															$selected = 'selected="selected"';
														}

														echo '<option value="' . $sup["member_idx"] . '" ' . $selected . '>' . $sup["supplier_name"] . '</option>';
													}
													?>
												</select>
											<?php
											}else{
											?>
												<input type="hidden" name="supplier_idx" value="<?=$supplier_idx?>" />
												<?=$supplier_info_name?>
											<?php
											}
											?>
										</td>
									</tr>
									<tr>
										<th>대표자명</th>
										<td class="text_left supplier_info_ceo_name"><?=$supplier_info_ceo_name?></td>
									</tr>
									<tr>
										<th>주소</th>
										<td class="text_left supplier_info_addr"><?=$supplier_info_addr?></td>
									</tr>
									<tr>
										<th>사업자번호</th>
										<td class="text_left supplier_info_license_no"><?=$supplier_info_license_no?></td>
									</tr>
									<tr>
										<th>담당자</th>
										<td class="text_left">
											<input type="text" class="w100px" name="stock_order_supplier_name" value="<?=$stock_order_supplier_name?>" />
											<select name="stock_order_supplier_name_sel">
												<option value="">담당자 선택</option>
											</select>
										</td>
									</tr>
									<tr>
										<th>연락처</th>
										<td class="text_left">
											<input type="text" name="stock_order_supplier_tel" class="w100px" value="<?=$stock_order_supplier_tel?>" />
										</td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
						</tbody>
					</table>
					<table class="no_border">
						<tbody>
						<tr>
							<td colspan="3" class="text_left">
								<p class="sub_tit2">배송지</p>
								<table>
									<colgroup>
										<col width="150">
										<col width="*">
										<col width="150">
										<col width="*">
									</colgroup>
									<tbody>
									<tr>
										<th>고객명</th>
										<td class="text_left"><input type="text" name="stock_order_receiver_name" class="w100px" value="<?=$stock_order_receiver_name?>" /></td>
										<th>연락처</th>
										<td class="text_left"><input type="text" name="stock_order_receiver_tel" class="w100px" value="<?=$stock_order_receiver_tel?>" /></td>
									</tr>
									<tr>
										<th>주소</th>
										<td colspan="3" class="text_left"><input type="text" name="stock_order_receiver_addr" class="w100per" value="<?=$stock_order_receiver_addr?>" /></td>
									</tr>
									</tbody>
								</table>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
				<p class="sub_tit2 mt20">
					발주내역 <a href="javascript:;" class="btn btn-stock-order-add-option">상품추가</a>
				</p>
				<div class="tb_wrap grid_tb">
					<table id="grid_list_pop_target" style="width: 100%;">
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<?php if($mode == "add" || $stock_order_is_order == "N"){?>
						<a href="javascript:;" id="btn-save" class="large_btn blue_btn ">저장</a>
						<?php } ?>
						<a href="javascript:self.close();" class="large_btn red_btn">닫기</a>

						<?php if($mode == "mod"){ ?>
						<a href="javascript:;" class="large_btn green_btn btn-stock-order-copy">복사</a>
						<?php } ?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/stock.order.js"></script>
<script>
	window.name = 'stock_order_write_pop';
	StockOrder.StockOrderWritePopInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>
