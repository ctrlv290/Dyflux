<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산 - 매출 페이지
 */
//Page Info
$pageMenuIdx = 264;
//Init
include_once "../_init_.php";

$tax_type                   = $_GET["tax_type"];
$date_start_year            = $_GET["date_start_year"];
$date_start_month           = $_GET["date_start_month"];
$date_end_year              = $_GET["date_end_year"];
$date_end_month             = $_GET["date_end_month"];
$product_seller_group_idx   = (isset($_GET["product_seller_group_idx"])) ? $_GET["product_seller_group_idx"] : "0";
$seller_idx                 = $_GET["seller_idx"];
$product_supplier_group_idx = (isset($_GET["product_supplier_group_idx"])) ? $_GET["product_supplier_group_idx"] : "0";
$supplier_idx               = $_GET["supplier_idx"];

$C_Settle = new Settle();

$tax_type = strtoupper($tax_type);
if($tax_type == "SALE"){
	$pageMenuIdx = 264;
	$target_idx = $seller_idx;

}elseif($tax_type == "PURCHASE"){
	$pageMenuIdx = 265;
	$target_idx = $supplier_idx;
}else{
	exit;
}

if($date_start_year && $date_start_month && $date_end_year && $date_end_month){

	$date_start_ym = $date_start_year . "-" . make2digit($date_start_month);
	$date_start_time = strtotime($date_start_ym."-01");

	$date_end_ym = $date_end_year . "-" . make2digit($date_end_month);
	$date_end_time = strtotime($date_end_ym."-01");

	$s_y = date('Y', $date_start_time);
	$s_m = date('n', $date_start_time);
	$e_y = date('Y', $date_end_time);
	$e_m = date('n', $date_end_time);

	$diff_y = $e_y - $s_y;
	$diff_m = $e_m - $s_m;

	$diff_m += $diff_y * 12;

	if($diff_m < 0 || $diff_m > 2){
		put_msg_and_back("기간은 3개월을 초과할 수 없습니다.");
	}

	$_list_set = array();
	$_month_ary = array();
	for($i = 0;$i <= $diff_m;$i++){

		$_cur_date_time = strtotime("-" . $i . " month", $date_end_time);
		$_cur_date_ym = date('Y-m', $_cur_date_time);

		$_list = $C_Settle -> getTransactionSumByMonth($_cur_date_ym, $tax_type, $target_idx);
		$_list_set[$_cur_date_ym] = $_list;
		$_month_ary[] = $_cur_date_ym;

	}


	$_build_set = array();


	$tmp = $_list_set[key($_list_set)];
	foreach($tmp as $main_key => $row){

		$new = array();

		$new["target_name"] = $row["target_name"];
		$new["target_idx"] = $row["target_idx"];
		$new["list"] = array();
		foreach ($_list_set as $key => $in_list){
			$new["list"][$key] = array(
				"taxation_amt" => $in_list[$main_key]["taxation_amt"],
				"free_amt" => $in_list[$main_key]["free_amt"],
				"small_amt" => $in_list[$main_key]["small_amt"],
				"sum_amt" => $in_list[$main_key]["sum_amt"],
				"taxation_amount" => $in_list[$main_key]["taxation_amount"],
				"free_amount" => $in_list[$main_key]["free_amount"],
				"small_amount" => $in_list[$main_key]["small_amount"],
				"sum_amount" => $in_list[$main_key]["sum_amount"],
				"taxation_memo" => $in_list[$main_key]["taxation_memo"],
				"free_memo" => $in_list[$main_key]["free_memo"],
				"small_memo" => $in_list[$main_key]["small_memo"],
				"sum_memo" => $in_list[$main_key]["sum_memo"],
			);

		}

		$_build_set[] = $new;
	}

	//print_r2($_build_set);
}

if(!$date_start_year) $date_start_year = date('Y');
if(!$date_start_month) $date_start_month = date('m');

if(!$date_end_year) $date_end_year = date('Y');
if(!$date_end_month) $date_end_month = date('m');

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
							<span class="text">날짜</span>
							<select name="date_start_year" id="date_start_year">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == $date_start_year) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_start_month" id="date_start_month">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == $date_start_month) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							~
							<select name="date_end_year" id="date_end_year">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == $date_end_year) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_end_month" id="date_end_month">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == $date_end_month) ? 'selected="selected"' : '';
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
								<select name="seller_idx" class="seller_idx" id="target_idx" data-selected="<?=$seller_idx?>" data-default-value="" data-default-text="전체 판매처">
								</select>
							</div>
						<?php }else{ ?>
							<div class="finder_col">
								<span class="text">공급처</span>
								<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="0">
									<option value="0">전체그룹</option>
								</select>
								<select name="supplier_idx" class="supplier_idx" id="target_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="전체 공급처">
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
		<?php if(count($_build_set) > 0){?>
			<div class="btn_set">
				<span>&nbsp;</span>
				<div class="right">

					<a href="javascript:;" class="btn btn-file-create-pop">파일생성</a>
					<a href="javascript:;" class="btn btn-email-log-pop">이메일발송이력</a>
					<a href="javascript:;" class="btn btn-down-log-pop">다운로드이력</a>
					<a href="javascript:;" class="btn btn-file-log-pop">파일생성이력</a>
				</div>
			</div>
			<div class="tb_wrap">
				<table class="floatThead">
					<colgroup>
						<col width="150" />
						<col width="60" />
						<?php
						foreach ($_month_ary as $item) {
						?>
						<col width="140" />
						<col width="140" />
						<col width="*" />
						<?php
						}
						?>

					</colgroup>
					<thead>
					<tr>
						<th rowspan="2">판매처</th>
						<th rowspan="2">구분</th>
						<?php
						foreach ($_month_ary as $val) {
						?>
						<th colspan="3"><?=$val?></th>
						<?php
						}
						?>
					</tr>
					<tr>
						<?php
						foreach ($_month_ary as $val) {
						?>
						<th>판매일보금액</th>
						<th>계산서발행이력금액</th>
						<th>메모</th>
						<?php
						}
						?>
					</tr>
					</thead>
					<tbody>
					<?php
					foreach ($_build_set as $key => $row) {
						$z = 0;
						?>
						<tr>
							<td rowspan="4"><?= $row["target_name"] ?></td>
							<td>과세</td>
							<?php foreach($row["list"] as $key2 => $sub_row){?>
							<td class="text_right">
								<?=number_format($sub_row["taxation_amt"])?>
								<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="taxation" data-idx="<?=$row["target_idx"]?>" data-name="<?=$row["target_name"]?>" data-ym="<?=$key2?>">일별</a>
							</td>
							<td class="text_right"><?=number_format($sub_row["taxation_amount"])?></td>
							<td class="text_left"><?=$sub_row["taxation_memo"] ?></td>
							<?php } ?>
						</tr>
						<tr>
							<td>면세</td>
							<?php foreach($row["list"] as $key2 => $sub_row){?>
								<td class="text_right">
									<?=number_format($sub_row["free_amt"])?>
									<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="free" data-idx="<?=$row["target_idx"]?>" data-name="<?=$row["target_name"]?>" data-ym="<?=$key2?>">일별</a>
								</td>
								<td class="text_right"><?=number_format($sub_row["free_amount"])?></td>
								<td class="text_left"><?=$sub_row["free_memo"] ?></td>
							<?php } ?>
						</tr>

						<tr>
							<td>영세</td>
							<?php foreach($row["list"] as $key2 => $sub_row){?>
								<td class="text_right">
									<?=number_format($sub_row["small_amt"])?>
									<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="small" data-idx="<?=$row["target_idx"]?>" data-name="<?=$row["target_name"]?>" data-ym="<?=$key2?>">일별</a>
								</td>
								<td class="text_right"><?=number_format($sub_row["small_amount"])?></td>
								<td class="text_left"><?=$sub_row["small_memo"] ?></td>
							<?php } ?>
						</tr>

						<tr class="sum">
							<td>합계</td>
							<?php foreach($row["list"] as $key2 => $sub_row){?>
								<td class="text_right">
									<?=number_format($sub_row["sum_amt"])?>
									<a href="javascript:;" class="xsmall_btn btn-daily-pop" data-mode="sum" data-idx="<?=$row["target_idx"]?>" data-name="<?=$row["target_name"]?>" data-ym="<?=$key2?>">일별</a>
								</td>
								<td class="text_right"><?=number_format($sub_row["sum_amount"])?></td>
								<td></td>
							<?php } ?>
						</tr>
						<?php
					}
					?>
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
<script src="/js/jquery.floatThead.min.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.tax.js"></script>
<script>
	window.name = 'tax_sale_write';
	SettleTax.TaxHistoryInit('<?=$tax_type?>');
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

