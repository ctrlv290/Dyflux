<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 거래현황
 */

//Page Index Setting
$pageMenuNo_L = 4;
$pageMenuNo_M = 0;

//Init
include_once "../../_init_.php";

$date = $_GET["date"];

if(!validateDate($date, "Y-m-d")){
	$date = date('Y-m-d');
}

?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_top.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="wrap_main">
	<div class="wrap_page bd_non">
		<div class="wrap_page_in">
			<div class="page_tabs pdlr20">
				<a href="javascript:;" class="btn-period on" data-period="day">일별</a>
				<a href="javascript:;" class="btn-period" data-period="week">주별</a>
				<a href="javascript:;" class="btn-period" data-period="month">월별</a>
			</div>
		</div>
		<div class="wrap_page_in">
			<form name="dyForm" id="dyForm">
				<input type="hidden" id="period" value="day" />
				<div class="form_sale_set">
					<div class="page_line">
						<span class="title">날짜</span>
						<span class="select_set day">
							<span class="txt prev_date dis_none">2019-04-01 ~ </span>
							<input type="text" name="date" id="transaction_state_date" class="jqDate w90px" value="<?=$date?>" readonly="readonly" />
						</span>
						<span class="select_set month dis_none">
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
						</span>
					</div>
				</div>
				<a href="javascript:;" id="btn-search" class="search_btn">검색</a>
			</form>
		</div>
	</div>
	<div class="wrap_inner mt20">
		<div class="wrap_sale-credit">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 매출현황(외상매출금)</p>
			<div class="wrap_scroll con_sale-credit mt10">
				<table class="table_style05 sale-credit">
					<colgroup>
						<col style="width: 200px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 200px;" />
					</colgroup>
					<thead>
					<tr>
						<th>거래처명</th>
						<th>전일 미수금액</th>
						<th>매출합계</th>
						<th>입금액</th>
						<th>현재잔액</th>
						<th>비고</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>

		<div class="wrap_purchase-credit mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 매입현황(외상매입금)</p>
			<div class="wrap_scroll con_purchase-credit mt10">
				<table class="table_style05 purchase-credit">
					<colgroup>
						<col style="width: 200px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 200px;" />
					</colgroup>
					<thead>
					<tr>
						<th>거래처명</th>
						<th>전일 미지급금액</th>
						<th>매입합계</th>
						<th>송금액</th>
						<th>현재잔액</th>
						<th>비고</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>

		<div class="wrap_sale-prepay mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 매출현황(선입금)</p>
			<div class="wrap_scroll con_sale-prepay mt10">
				<table class="table_style05 sale-prepay">
					<colgroup>
						<col style="width: 200px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 200px;" />
					</colgroup>
					<thead>
					<tr>
						<th>거래처명</th>
						<th>전일 미수금액</th>
						<th>매출합계</th>
						<th>입금액</th>
						<th>현재잔액</th>
						<th>비고</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>

		<div class="wrap_purchase-prepay mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 매입현황(선입금)</p>
			<div class="wrap_scroll con_purchase-prepay mt10">
				<table class="table_style05 purchase-prepay">
					<colgroup>
						<col style="width: 200px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 200px;" />
					</colgroup>
					<thead>
					<tr>
						<th>거래처명</th>
						<th>전일 미지급금액</th>
						<th>매입합계</th>
						<th>송금액</th>
						<th>현재잔액</th>
						<th>비고</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>

		<div class="wrap_sale-etc mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 매출현황(기타)</p>
			<div class="wrap_scroll con_sale-etc mt10">
				<table class="table_style05 sale-etc">
					<colgroup>
						<col style="width: 200px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 200px;" />
					</colgroup>
					<thead>
					<tr>
						<th>거래처명</th>
						<th>전일 미수금액</th>
						<th>판매금액</th>
						<th>입금액</th>
						<th>현재잔액</th>
						<th>비고</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>

		<div class="wrap_purchase-etc mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 매입현황(기타)</p>
			<div class="wrap_scroll con_purchase-etc mt10">
				<table class="table_style05 purchase-etc">
					<colgroup>
						<col style="width: 200px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 200px;" />
					</colgroup>
					<thead>
					<tr>
						<th>거래처명</th>
						<th>전일 미지급금액</th>
						<th>발생금액</th>
						<th>송금액</th>
						<th>현재잔액</th>
						<th>비고</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>

		<br><br>
	</div>
</div>
<script src="../js/page/transaction.state.js"></script>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_footer.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_bottom.php"; ?>
