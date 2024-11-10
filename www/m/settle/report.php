<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: Mobile 자금일보
 */

//Page Index Setting
$pageMenuNo_L = 5;
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
							<input type="text" name="date" id="transaction_date" class="jqDate w90px" value="<?=$date?>" readonly="readonly" />
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
		<div class="wrap_bank_domestic">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 예금 및 현금 입출금 현황 - 국내계좌</p>
			<div class="wrap_scroll con_bank_domestic mt10">
				<table class="table_style05 bank_domestic">
					<colgroup>
						<col style="width: 250px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>예금기관명</th>
						<th>전월이월</th>
						<th>입금</th>
						<th>출금</th>
						<th>금일잔액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>

		<div class="wrap_bank_foreign mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 예금 및 현금 입출금 현황 - 외환계좌</p>
			<div class="wrap_scroll con_bank_foreign mt10">
				<table class="table_style05 bank_foreign">
					<colgroup>
						<col style="width: 250px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>예금기관명</th>
						<th>전월이월</th>
						<th>입금</th>
						<th>출금</th>
						<th>금일잔액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_CASH_IN mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 현금출납내역 - 수입</p>
			<div class="wrap_scroll con_CASH_IN mt10">
				<table class="table_style05 CASH_IN">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>적요</th>
						<th>출금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_CASH_OUT mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 현금출납내역 - 지출</p>
			<div class="wrap_scroll con_CASH_OUT mt10">
				<table class="table_style05 CASH_OUT">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>적요</th>
						<th>출금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_BANK_CUSTOMER_IN mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 통장 입출금 내역 - 수입(거래처별)</p>
			<div class="wrap_scroll con_BANK_CUSTOMER_IN mt10">
				<table class="table_style05 BANK_CUSTOMER_IN">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>거래처</th>
						<th>적요</th>
						<th>출금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_BANK_CUSTOMER_OUT mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 통장 입출금 내역 - 지출(거래처별)</p>
			<div class="wrap_scroll con_BANK_CUSTOMER_OUT mt10">
				<table class="table_style05 BANK_CUSTOMER_OUT">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>거래처</th>
						<th>적요</th>
						<th>출금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_BANK_ETC_IN mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 통장 입출금 내역 - 수입(기타)</p>
			<div class="wrap_scroll con_BANK_ETC_IN mt10">
				<table class="table_style05 BANK_ETC_IN">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>적요</th>
						<th>출금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_BANK_ETC_OUT mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 통장 입출금 내역 - 수입(기타)</p>
			<div class="wrap_scroll con_BANK_ETC_OUT mt10">
				<table class="table_style05 BANK_ETC_OUT">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>적요</th>
						<th>출금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_TRANSFER_IN mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 계좌간이체 - 수입</p>
			<div class="wrap_scroll con_TRANSFER_IN mt10">
				<table class="table_style05 TRANSFER_IN">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>적요</th>
						<th>출금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_TRANSFER_OUT mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 계좌간이체 - 지출</p>
			<div class="wrap_scroll con_TRANSFER_OUT mt10">
				<table class="table_style05 TRANSFER_OUT">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>적요</th>
						<th>출금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_CARD_OUT mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 카드 사용내역</p>
			<div class="wrap_scroll con_CARD_OUT mt10">
				<table class="table_style05 CARD_OUT">
					<colgroup>
						<col style="width: 100px;" />
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>사용자</th>
						<th>카드번호</th>
						<th>지출처</th>
						<th>계정과목</th>
						<th>적요</th>
						<th>지출금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>


		<div class="wrap_ACCOUNT_IN mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 계정과목별 집계 - 수입</p>
			<div class="wrap_scroll con_ACCOUNT_IN mt10">
				<table class="table_style05 ACCOUNT_IN">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>금액</th>
					</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
		</div>
		<div class="wrap_ACCOUNT_OUT mt20">
			<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 계정과목별 집계 - 지출</p>
			<div class="wrap_scroll con_ACCOUNT_OUT mt10">
				<table class="table_style05 ACCOUNT_OUT">
					<colgroup>
						<col style="width: 150px;" />
						<col style="width: 120px;" />
					</colgroup>
					<thead>
					<tr>
						<th>계정과목</th>
						<th>금액</th>
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
<script src="../js/page/report.js"></script>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_footer.php"; ?>
<?php include_once DY_Mobile_INCLUDE_PATH . "/_include_bottom.php"; ?>
