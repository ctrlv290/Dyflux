<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산, 계산서발행이력 일별 팝업 페이지
 */
//Page Info
$pageMenuIdx = 271;
//Init
include_once "../_init_.php";

$mode = $_GET["mode"];
$tax_type = $_GET["tax_type"];
$target_idx = $_GET["target_idx"];
$date_ym = $_GET["date_ym"];
$name = $_GET["name"];

$header_target_text = "";

if($tax_type == "SALE"){
	$header_target_text = "매출처";
	$target_param = "seller_idx=".$target_idx;
}else{
	$header_target_text = "매입처";
	$target_param = "supplier_idx=".$target_idx;
}

if($mode == "sum"){
	$popup_title = "합계";
}else{
	if($mode == "taxation") {
		$popup_title = "과세";
	}elseif($mode == "free") {
		$popup_title = "면세";
	}elseif($mode == "small") {
		$popup_title = "영세";
	}
}

$C_Settle = new Settle();

$_list = $C_Settle -> getSettleDailySum($date_ym, $tax_type, $target_idx);


$time = strtotime($date_ym . "-01");

$date_start = date('Y-m-d', $time);
$date_end = date('Y-m-t', $time);
?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
	<?php include_once DY_INCLUDE_PATH . "/_include_main_nav.php"; ?>
	<div class="content write_page">
		<div class="content_wrap">
			<form name="dyFormChargePop" id="dyFormChargePop" method="post" class="<?=$mode?>" action="vendor_charge_proc.php">
				<input type="hidden" name="mode" value="<?=$mode?>" />
				<div class="tb_wrap">
					<p class="sub_tit2"><?=$header_target_text?> : <?=$name?></p>
					<p class="sub_tit2">날짜 : <?=$date_ym?></p>
					<table class="floatThead">
						<colgroup>
							<col width="150">
							<col width="80">
							<col width="*">
						</colgroup>
						<thead>
						<tr>
							<th>날짜</th>
							<th colspan="2">판매일보금액</th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($_list as $_row){

							$rowspan = "1";
							if($mode == "sum"){
								$rowspan = "4";
								$row1_tit = "과세";
								$row1 = $_row["taxation_amt"];
							}elseif($mode == "taxation"){
								$row1_tit = "과세";
								$row1 = $_row["taxation_amt"];
							}elseif($mode == "free"){
								$row1_tit = "면세";
								$row1 = $_row["free_amt"];
							}elseif($mode == "small"){
								$row1_tit = "영세";
								$row1 = $_row["small_amt"];
							}

							if($mode != "sum"){
								if($row1 == null && empty($row1))
								{
									continue;
								}
							}
						?>
						<tr>
							<td rowspan="<?=$rowspan?>"><a href="transaction_list.php?date_start=<?=$_row["settle_date"]?>&date_end=<?=$_row["settle_date"]?>&product_tax_type=<?=$mode?>&<?=$target_param?>" class="link" target="_blank"><?=$_row["settle_date"]?></a></td>
							<td><?=$row1_tit?></td>
							<td class="text_right"><?=number_format($row1)?></td>
						</tr>
						<?php if($mode == "sum"){ ?>
								<tr>
									<td>면세</td>
									<td class="text_right"><?=number_format($_row["free_amt"])?></td>
								</tr>
								<tr>
									<td>영세</td>
									<td class="text_right"><?=number_format($_row["small_amt"])?></td>
								</tr>
								<tr class="sum">
									<td>합계</td>
									<td class="text_right"><?=number_format($_row["sum_amt"])?></td>
								</tr>
						<?php } ?>
						<?php
						}
						?>

						</tbody>
					</table>
				</div>
				<div class="btn_set">
					<div class="center">
						<a href="javascript:self.close();" class="large_btn red_btn btn-common-pop-close">닫기</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<script src="/js/main.js"></script>
<script src="/js/String.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/jquery.floatThead.min.js"></script>
<script src="/js/page/settle.tax.js"></script>
<script>
	SettleTax.TaxDailyPopInit('<?=$popup_title?>');
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

