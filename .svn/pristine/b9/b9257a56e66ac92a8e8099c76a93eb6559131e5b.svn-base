<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산 - 매출 페이지
 */
//Page Info
$pageMenuIdx = 266;
//Init
include_once "../_init_.php";

$tax_type                   = $_GET["tax_type"];
$date_year                  = $_GET["date_year"];
$date_month                 = $_GET["date_month"];
$product_seller_group_idx   = (isset($_GET["product_seller_group_idx"])) ? $_GET["product_seller_group_idx"] : "0";
$seller_idx                 = $_GET["seller_idx"];
$product_supplier_group_idx = (isset($_GET["product_supplier_group_idx"])) ? $_GET["product_supplier_group_idx"] : "0";
$supplier_idx               = $_GET["supplier_idx"];

$C_Settle = new Settle();

$tax_type = strtoupper($tax_type);
if($tax_type == "SALE"){
	$pageMenuIdx = 266;
	$target_idx = $seller_idx;

}elseif($tax_type == "PURCHASE"){
	$pageMenuIdx = 267;
	$target_idx = $supplier_idx;
}else{
	exit;
}

if($date_year && $date_month){

	$date_ym = $date_year . "-" . make2digit($date_month);
	$date_time = strtotime($date_ym."-01");

	$_list = $C_Settle -> getTransactionSumByMonth($date_ym, $tax_type, $target_idx);
	$_list = $_list[0];

	$date_prev_time = strtotime("-1 month", $date_time);
	$date_prev_ym = date('Y-m', $date_prev_time);

	$_list_prev = $C_Settle -> getTransactionSumByMonth($date_prev_ym, $tax_type, $target_idx);
	$_list_prev = $_list_prev[0];
}

if(!$date_year) $date_year = date('Y');
if(!$date_month) $date_month = date('m');

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="get">
			<input type="hidden" id="tax_type" name="tax_type" value="<?=$tax_type?>" />
			<input type="hidden" id="date_ym" value="<?=$date_ym?>" />
			<input type="hidden" id="target_idx" value="<?=$target_idx?>" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text" style="margin-right: 8px;">기 간</span>
							<select name="date_year" id="date_year">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == $date_year) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_month" id="date_month">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == $date_month) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
						</div>
						<?php if($tax_type == "SALE"){ ?>
						<div class="finder_col">
							<span class="text">판매처</span>
							<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="0">
								<option value="0">전체그룹</option>
							</select>
							<select name="seller_idx" class="seller_idx" id="seller_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="판매처를 선택해주세요">
							</select>
						</div>
						<?php }else{ ?>
						<div class="finder_col">
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="0">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx" class="supplier_idx" id="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="공급처를 선택해주세요.">
								<?=($supplier_idx) ? '<option value="'.$supplier_idx.'"></option>' : ''?>
							</select>
						</div>
						<?php } ?>
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
		<?php if($_list){?>
		<div class="btn_set">
			<a href="javascript:;" id="btn-save" class="btn large_btn red_btn">&nbsp;&nbsp;&nbsp;저장&nbsp;&nbsp;&nbsp;</a>
			<span>&nbsp;</span>
			<div class="right">

				<a href="javascript:;" class="btn btn-email-log-pop">이메일발송이력</a>
				<a href="javascript:;" class="btn btn-down-log-pop">다운로드이력</a>
				<a href="javascript:;" class="btn btn-file-log-pop">파일생성이력</a>
			</div>
		</div>
		<div class="tb_wrap">


			<table>
				<colgroup>
					<col width="150" />
					<col width="60" />

					<col width="150" />
					<col width="120" />
					<col width="60" />
					<col width="80" />
					<col width="150" />
					<col width="*" />

					<col width="150" />
					<col width="120" />
					<col width="*" />
				</colgroup>
				<thead>
				<tr>
					<th rowspan="2">판매처</th>
					<th rowspan="2">구분</th>
					<th colspan="6"><?=$date_ym?></th>
					<th colspan="3"><?=$date_prev_ym?></th>
				</tr>
				<tr>
					<th>판매일보금액</th>
					<th>계산서발행금액</th>
					<th>Check</th>
					<th>확인</th>
					<th>확인일시</th>
					<th>메모</th>
					<th>판매일보금액</th>
					<th>계산서발행금액</th>
					<th>메모</th>
				</tr>
				</thead>
				<tbody>
				<tr>
					<td rowspan="4">
						<?=$_list["target_name"]?>
						<br>
						<a href="javascript:;" class="btn btn-create-tax-xls" data-name="<?=$_list["target_name"]?>">파일생성</a>
					</td>
					<td>과세</td>


					<td class="text_right">
						<span class="tax_taxation"><?=number_format($_list["taxation_amt"])?></span>
						<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="taxation" data-date="<?=$date_ym?>" data-name="<?=$_list["target_name"]?>">일별</a>
					</td>
					<td class="text_right">
						<input type="text" class="w100per money" name="tax_taxation" id="taxation_amount" value="<?=number_format($_list["taxation_amount"])?>" tabindex="1" />
					</td>
					<td><span class="chk_tax_taxation"></span></td>
					<td>
						<?php
						if($_list["taxation_confirm"] == "Y"){
							echo "확인 V";
						}else{
							echo '<a href="javascript:;" class="btn xsmall_btn btn-confirm" data-what="taxation">확인</a>';
						}
						?>
					</td>
					<td>
						<?php
						if($_list["taxation_confirm"] == "Y") {
							echo date("Y-m-d H:i:s", strtotime($_list["taxation_date"]));
						}
						?>
					</td>
					<td>
						<input type="text" class="w100per" name="tax_taxation_memo" id="taxation_memo" value="<?=$_list["taxation_memo"]?>" />
					</td>

					<td class="text_right">
						<?=number_format($_list_prev["taxation_amt"])?>
						<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="taxation" data-date="<?=$date_prev_ym?>" data-name="<?=$_list["target_name"]?>">일별</a>
					</td>
					<td class="text_right"><?=number_format($_list_prev["taxation_amount"])?></td>
					<td><?=$_list_prev["taxation_memo"]?></td>

				</tr>
				<tr>
					<td>면세</td>

					<td class="text_right">
						<span class="tax_free"><?=number_format($_list["free_amt"])?></span>
						<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="free" data-date="<?=$date_ym?>" data-name="<?=$_list["target_name"]?>">일별</a>
					</td>
					<td class="text_right">
						<input type="text" class="w100per money" name="tax_free" id="free_amount" value="<?=number_format($_list["free_amount"])?>" tabindex="2" />
					</td>
					<td><span class="chk_tax_free"></span></td>
					<td>
						<?php
						if($_list["free_confirm"] == "Y"){
							echo "확인 V";
						}else{
							echo '<a href="javascript:;" class="btn xsmall_btn btn-confirm" data-what="free">확인</a>';
						}
						?>
					</td>
					<td>
						<?php
						if($_list["free_confirm"] == "Y") {
							echo date("Y-m-d H:i:s", strtotime($_list["free_date"]));
						}
						?>
					</td>
					<td>
						<input type="text" class="w100per" name="tax_free_memo" id="free_memo" value="<?=$_list["free_memo"]?>" />
					</td>

					<td class="text_right">
						<?=number_format($_list_prev["free_amt"])?>
						<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="free" data-date="<?=$date_prev_ym?>" data-name="<?=$_list["target_name"]?>">일별</a>
					</td>
					<td class="text_right"><?=number_format($_list_prev["free_amount"])?></td>
					<td><?=$_list_prev["free_memo"]?></td>
				</tr>
				<tr>
					<td>영세</td>

					<td class="text_right">
						<span class="tax_small"><?=number_format($_list["small_amt"])?></span>
						<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="small" data-date="<?=$date_ym?>" data-name="<?=$_list["target_name"]?>">일별</a>
					</td>
					<td class="text_right">
						<input type="text" class="w100per money" name="tax_small" id="small_amount" value="<?=number_format($_list["small_amount"])?>" tabindex="3" />
					</td>
					<td><span class="chk_tax_small"></span></td>
					<td>
						<?php
						if($_list["small_confirm"] == "Y"){
							echo "확인 V";
						}else{
							echo '<a href="javascript:;" class="btn xsmall_btn btn-confirm" data-what="small">확인</a>';
						}
						?>
					</td>
					<td>
						<?php
						if($_list["small_confirm"] == "Y") {
							echo date("Y-m-d H:i:s", strtotime($_list["small_date"]));
						}
						?>
					</td>
					<td>
						<input type="text" class="w100per" name="tax_small_memo" id="small_memo" value="<?=$_list["small_memo"]?>" />
					</td>

					<td class="text_right">
						<?=number_format($_list_prev["small_amt"])?>
						<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="small" data-date="<?=$date_prev_ym?>" data-name="<?=$_list["target_name"]?>">일별</a>
					</td>
					<td class="text_right"><?=number_format($_list_prev["small_amount"])?></td>
					<td><?=$_list_prev["small_memo"]?></td>
				</tr>
				<tr class="sum">
					<td>합계</td>

					<td class="text_right">
						<span class="tax_sum"><?=number_format($_list["taxation_amt"] + $_list["small_amt"] + $_list["free_amt"])?></span>
						<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="sum" data-date="<?=$date_ym?>" data-name="<?=$_list["target_name"]?>">일별</a>
					</td>
					<td class="text_right">
						<span class="tax_input_sum"><?=number_format($_list["taxation_amount"] + $_list["small_amount"] + $_list["free_amount"])?></span>
					</td>
					<td><span class="chk_tax_sum"></span></td>
					<td></td>
					<td></td>
					<td></td>

					<td class="text_right">
						<?=number_format($_list_prev["taxation_amt"] + $_list_prev["small_amt"] + $_list_prev["free_amt"])?>
						<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="sum" data-date="<?=$date_prev_ym?>" data-name="<?=$_list["target_name"]?>">일별</a>
					</td>
					<td class="text_right"><?=number_format($_list_prev["taxation_amount"] + $_list_prev["small_amount"] + $_list_prev["free_amount"])?></td>
					<td></td>
				</tr>
				</tbody>
			</table>
		</div>
		<?php } ?>
	</div>
</div>

<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.tax.js?v=200417"></script>
<script>
	window.name = 'tax_sale_write';
	SettleTax.TaxSaleWriteInit('<?=$tax_type?>');
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

