<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매입거래처별원장 페이지
 */
//Page Info
$pageMenuIdx = 135;
//Init
include_once "../_init_.php";

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$product_seller_group_idx = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx               = $_GET["seller_idx"] || 0;

$period = $_GET["period"];
$periodAry = array("day", "week", "month");
if(!in_array($period, $periodAry)){
	$period = $periodAry[0];
}

$date = date('Y-m-d');

if($period == "week"){
	$prev_date = date('Y-m-d', strtotime("-6 days", strtotime($date)));
}

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
	<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
	<div class="content">
		<form name="searchForm" id="searchForm" method="get">
			<input type="hidden" id="period" value="<?=$period?>" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text" style="margin-right: 8px;">기 간</span>
							<select name="date_start_year" id="date_start_year">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == date('Y')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_start_month" id="date_start_month">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == date('m')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							~
							<select name="date_end_year" id="date_end_year">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == date('Y')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_end_month" id="date_end_month">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == date('m')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
						</div>
						<div class="finder_col">
							<?php if(isDYLogin()) {?>
							<span class="text">공급처</span>
							<select name="product_supplier_group_idx" class="product_supplier_group_idx" data-selected="<?=$product_supplier_group_idx?>">
								<option value="0">전체그룹</option>
							</select>
							<select name="supplier_idx" class="supplier_idx" id="supplier_idx" data-selected="<?=$supplier_idx?>" data-default-value="" data-default-text="공급처를 선택해주세요.">
								<?=($supplier_idx) ? '<option value="'.$supplier_idx.'"></option>' : ''?>
							</select>
							<?php }else{ ?>
								<input type="hidden" name="seller_idx" id="seller_idx" value="<?=$GL_Member["member_idx"]?>" />
							<?php } ?>
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
		<?php if(isDYLogin()){?>
		<div class="btn_set top_btn_set dis_none">
			<!--			<a href="javascript:;" class="btn btn-column-setting-pop">항목설정</a>-->
			<!--<a href="javascript:;" class="btn btn-order-batch-proc">주문일괄처리</a>-->
			<span>&nbsp;</span>
			<div class="right">
				<a href="javascript:;" class="btn btn-email-log-pop">이메일발송 이력</a>
				<a href="javascript:;" class="btn btn-down-log-pop">다운로드 이력</a>
				<a href="javascript:;" class="btn btn-file-log-pop">파일생성 이력</a>
<!--				<a href="javascript:;" class="btn btn-ledger-detail-add" data-type="TRAN">송금액 등록</a>-->
				<a href="javascript:;" class="btn btn-ledger-detail-add" data-type="REFUND">공제/환급액 등록</a>
				<a href="javascript:;" class="btn btn-ledger-detail-add" data-type="ADJUST">매입 추가 등록</a>
				<a href="javascript:;" class="btn green_btn btn-ledger-all-down btn-ledger-create-xls" data-month_start="" data-month_end="" data-target_idx="" data-type="LEDGER_PURCHASE">다운로드</a>
				<label><input type="checkbox" id="chk-ledger-all-down-shrink" /> 상세내역 제외</label>
			</div>
		</div>
		<?php }?>
		<table class="no_border">
			<tr>
				<td class="wrap_ledge text_left">

				</td>
			</tr>
		</table>

	</div>
</div>

<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.ledger.js"></script>
<script>
	window.name = 'settle_purchase_ledge';
	SettleLedge.PurchaseLedgeInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

