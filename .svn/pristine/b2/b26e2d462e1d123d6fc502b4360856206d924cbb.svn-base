<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산, 계산서발행이력 파일생성 팝업 페이지
 */
//Page Info
$pageMenuIdx = 275;
//Init
include_once "../_init_.php";

$mode = $_GET["mode"];
$tax_type = $_GET["tax_type"];
$target_idx = $_GET["target_idx"];
$date_ym = $_GET["date_ym"];
$name = $_GET["name"];

$header_target_text = "";

if($tax_type == "SALE"){
	$header_target_text = "판매처";
	$target_param = "seller_idx=".$target_idx;
}else{
	$header_target_text = "공급처";
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
				<input type="hidden" id="tax_type" value="<?=$tax_type?>" />
				<input type="hidden" id="date_ym" value="" />
				<div class="tb_wrap">
					<table>
						<colgroup>
							<col width="120" />
							<col width="*" />
						</colgroup>
						<tbody>
					<tr>
						<th><?=$header_target_text?></th>
						<td class="text_left">
							<?php if($tax_type == "SALE"){ ?>
								<select name="product_seller_group_idx" class="product_seller_group_idx" data-selected="0">
									<option value="0">전체그룹</option>
								</select>
								<select name="seller_idx" class="seller_idx" id="target_idx" data-selected="<?=$target_idx?>" data-default-value="" data-default-text="판매처를 선택해주세요.">
								</select>
							<?php }else{ ?>
								<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="0">
									<option value="0">전체그룹</option>
								</select>
								<select name="supplier_idx" class="supplier_idx" id="target_idx" data-selected="<?=$target_idx?>" data-default-value="" data-default-text="공급처를 선택해주세요.">
									<?=($supplier_idx) ? '<option value="'.$supplier_idx.'"></option>' : ''?>
								</select>
							<?php } ?>

						</td>
					</tr>
					<tr>
						<th>날짜</th>
						<td class="text_left">
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
						</td>
					</tr>
					</tbody>
				</table>
				</div>
			</form>
			<div class="btn_set">
				<div class="center">
					<a href="javascript:;" class="large_btn blue_btn btn-create-tax-xls">&nbsp;&nbsp;&nbsp;파일생성&nbsp;&nbsp;&nbsp;</a>
					<a href="javascript:self.close();" class="large_btn red_btn btn-common-pop-close">닫기</a>
				</div>
			</div>
		</div>
	</div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/settle.tax.js"></script>
<script>
	SettleTax.TaxHistoryCreatePopInit('<?=$tax_type?>');
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

