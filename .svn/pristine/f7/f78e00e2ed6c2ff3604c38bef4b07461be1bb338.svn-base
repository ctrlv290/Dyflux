<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래형황 페이지
 */
//Page Info
$pageMenuIdx = 134;
//Init
include_once "../_init_.php";

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
		<div class="wrap_tab_menu">
			<ul class="tab_menu">
				<li><a href="transaction_state.php?period=day" class="<?=($period == "day") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;일별&nbsp;&nbsp;&nbsp;</a></li>
				<li><a href="transaction_state.php?period=week" class="<?=($period == "week") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;주별&nbsp;&nbsp;&nbsp;</a></li>
				<li><a href="transaction_state.php?period=month" class="<?=($period == "month") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;월별&nbsp;&nbsp;&nbsp;</a></li>
			</ul>
		</div>
		<form name="searchForm" id="searchForm" method="get">
			<input type="hidden" id="period" name="period" value="<?=$period?>" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text">날짜</span>

							<?php if($period == "day" || $period == "week") {?>

							<?php if($period == "week"){?>
									<span class="prev_date"><?=$prev_date?></span> ~
							<?php }?>
							<input type="text" name="date" id="transaction_state_date" class="w80px jqDate " value="<?=$date?>" readonly="readonly" />

							<?php }elseif($period == "month") { ?>
							<select name="date_year" id="date_year">
								<?php
								for($i = 2018;$i<=date('Y');$i++){
									$selected = ($i == date('Y')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
							<select name="date_month" id="date_month">
								<?php
								for($i = 1;$i<=12;$i++){
									$selected = ($i == date('m')) ? 'selected="selected"' : '';
									echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
								}
								?>
							</select>
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
		<div class="btn_set">
			<a href="javascript:;" class="btn green_btn btn-xls-down">다운로드</a>
			<div class="right">

			</div>
		</div>
		<div class="tb_wrap min1400">
			<table class="no_border">
				<colgroup>
					<col width="49%" />
					<col width="20" />
					<col width="*" />
				</colgroup>
				<tbody>
				<tr>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">매출현황(외상매출금)</p>
							<div class="right">
								<?php if($period == "day") { ?>
								<!--<a href="javascript:;" id="btn-save-sale-credit" class="btn wide_btn">저장</a>-->
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="sale-credit">
								<colgroup>
									<col width="*">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="*">
									<col width="60">
								</colgroup>
								<thead>
								<tr>
									<th>거래처명</th>
									<th><?=($period == "day") ? "전일" : "이월"?> 미수금액</th>
									<th>매출합계</th>
									<th>입금액</th>
									<th>현재잔액</th>
									<th>비고</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
					<td></td>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">매입현황(외상매입금)</p>
							<div class="right">
								<?php if($period == "day") { ?>
								<!--<a href="javascript:;" id="btn-save-purchase-credit" class="btn wide_btn">저장</a>-->
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="purchase-credit">
								<colgroup>
									<col width="*">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="*">
									<col width="60">
								</colgroup>
								<thead>
								<tr>
									<th>거래처명</th>
									<th><?=($period == "day") ? "전일" : "이월"?> 미지급금액</th>
									<th>매입합계</th>
									<th>송금액</th>
									<th>현재잔액</th>
									<th>비고</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">매출현황(선입금)</p>
						</div>
						<div class="tb_wrap">
							<table class="sale-prepay">
								<colgroup>
									<col width="*">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="*">
									<col width="60">
								</colgroup>
								<thead>
								<tr>
									<th>거래처명</th>
									<th><?=($period == "day") ? "전일" : "이월"?> 잔액</th>
									<th>매출합계</th>
									<th>입금액</th>
									<th>현재잔액</th>
									<th>비고</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>

					</td>
					<td></td>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">매입현황(선급금)</p>
							<div class="right">
								<?php if($period == "day") { ?>
								<!--<a href="javascript:;" id="btn-save-purchase-prepay" class="btn wide_btn">저장</a>-->
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="purchase-prepay">
								<colgroup>
									<col width="*">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="*">
									<col width="60">
								</colgroup>
								<thead>
								<tr>
									<th>거래처명</th>
									<th><?=($period == "day") ? "전일" : "이월"?> 잔액</th>
									<th>매입합계</th>
									<th>송금액</th>
									<th>현재잔액</th>
									<th>비고</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<?php if($period == "day") { ?>
				<tr>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">매출현황(기타)</p>
							<div class="right">
								<a href="javascript:;" id="btn-add-sale-etc" data-tran_type="SALE_ETC" class="btn wide_btn">추가</a>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="sale-etc">
								<colgroup>
									<col width="*">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="*">
									<col width="60">
								</colgroup>
								<thead>
								<tr>
									<th>거래처명</th>
									<th>전일 미수금액</th>
									<th>판매금액</th>
									<th>입금액</th>
									<th>현재잔액</th>
									<th>비고</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
					<td></td>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">매입현황(기타)</p>
							<div class="right">
								<a href="javascript:;" id="btn-add-purchase-etc" data-tran_type="PURCHASE_ETC" class="btn wide_btn">추가</a>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="purchase-etc">
								<colgroup>
									<col width="*">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="110">
									<col width="*">
									<col width="60">
								</colgroup>
								<thead>
								<tr>
									<th>거래처명</th>
									<th>전일 미지급금액</th>
									<th>발생금액</th>
									<th>송금액</th>
									<th>현재잔액</th>
									<th>비고</th>
									<th></th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<div id="modal_common" title="" class="red_theme" style="display: none;"></div>

<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/info.category.js"></script>
<script src="/js/page/settle.purchase.js?v=200212"></script>
<script>
	window.name = 'settle_transaction_state';
	$(function(){
		SettlePurchase.TransactionStateInit();
	});
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

