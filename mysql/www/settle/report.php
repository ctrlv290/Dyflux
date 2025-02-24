<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 거래형황 페이지
 */
//Page Info
$pageMenuIdx = 137;
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
				<li><a href="report.php?period=day" class="<?=($period == "day") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;일별&nbsp;&nbsp;&nbsp;</a></li>
				<li><a href="report.php?period=week" class="<?=($period == "week") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;주별&nbsp;&nbsp;&nbsp;</a></li>
				<li><a href="report.php?period=month" class="<?=($period == "month") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;월별&nbsp;&nbsp;&nbsp;</a></li>
			</ul>
		</div>
		<form name="searchForm" id="searchForm" method="get">
			<input type="hidden" id="period" name="period" value="<?=$period?>" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text" style="margin-right: 8px;">기 간</span>

							<?php if($period == "day" || $period == "week") {?>

								<?php if($period == "week"){?>
									<span class="text prev_date"><?=$prev_date?></span> ~
								<?php }?>
								<input type="text" name="date" id="transaction_date" class="w80px jqDate " value="<?=$date?>" readonly="readonly" />

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
		<style>
			.word-break table td {word-break: break-all !important;}
		</style>
		<div class="tb_wrap max1400 word-break ">
			<div class="btn_set">
				<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 예금 및 현금 입출금 현황 - 국내계좌</p>
				<div class="right">
					<?php if($period == "day") { ?>
						<a href="javascript:;" id="btn-bank-pop" class="btn wide_btn">입금/출금</a>
					<?php } ?>
				</div>
			</div>
			<div class="tb_wrap">
				<table class="bank_domestic">
					<colgroup>
						<col width="300">
						<col width="160">
						<col width="160">
						<col width="160">
						<col width="160">
						<col width="*">
					</colgroup>
					<thead>
					<tr>
						<th>예금기관명</th>
						<th>전일이월</th>
						<th>입금</th>
						<th>출금</th>
						<th>금일잔액</th>
						<th>비고</th>
					</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
			<br>
			<div class="btn_set">
				<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 예금 및 현금 입출금 현황 - 외환계좌</p>
			</div>
			<div class="tb_wrap">
				<table class="bank_foreign">
					<colgroup>
						<col width="300">
						<col width="160">
						<col width="160">
						<col width="160">
						<col width="160">
						<col width="*">
					</colgroup>
					<thead>
					<tr>
						<th>예금기관명</th>
						<th>전일이월</th>
						<th>입금</th>
						<th>출금</th>
						<th>금일잔액</th>
						<th>비고</th>
					</tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>

			<table class="no_border">
				<colgroup>
					<col width="49%" />
					<col width="20" />
					<col width="*" />
				</colgroup>
				<tbody>
				<tr>
					<td colspan="3" class="text_left">
						<p class="sub_tit"><i class="fas fa-caret-right f20px"></i> 현금출납내역</p>
					</td>
				</tr>
                <tr>
                    <td class="text_left vtop">
                        <div class="btn_set">
                            <p class="sub_tit2">- 수입(거래처별)</p>
                            <div class="right">
                                <?php if($period == "day") { ?>
                                    <a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="CASH_CUSTOMER_IN" data-inout="IN">수정</a>
                                    <a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="CASH_CUSTOMER_IN" data-inout="IN">추가등록</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tb_wrap">
                            <table class="CASH_CUSTOMER_IN">
                                <colgroup>
                                    <col width="120">
                                    <col width="200">
                                    <col width="120">
                                    <col width="*">
                                    <?=($period == "day") ? '<col width="70">' : '' ?>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>계정과목</th>
                                    <th>거래처</th>
                                    <th>입금액</th>
                                    <th>적요</th>
                                    <?=($period == "day") ? '<th></th>' : '' ?>
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
                            <p class="sub_tit2">- 지출(거래처별)</p>
                            <div class="right">
                                <?php if($period == "day") { ?>
                                    <a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="CASH_CUSTOMER_OUT" data-inout="OUT">수정</a>
                                    <a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="CASH_CUSTOMER_OUT" data-inout="OUT">추가등록</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tb_wrap">
                            <table class="CASH_CUSTOMER_OUT">
                                <colgroup>
                                    <col width="120">
                                    <col width="200">
                                    <col width="120">
                                    <col width="*">
                                    <?=($period == "day") ? '<col width="70">' : '' ?>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>계정과목</th>
                                    <th>거래처</th>
                                    <th>출금액</th>
                                    <th>적요</th>
                                    <?=($period == "day") ? '<th></th>' : '' ?>
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
                            <p class="sub_tit2">- 수입(기타)</p>
                            <div class="right">
                                <?php if($period == "day") { ?>
                                    <a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="CASH_IN" data-inout="IN">수정</a>
                                    <a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="CASH_IN" data-inout="IN">추가등록</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tb_wrap">
                            <table class="CASH_IN">
                                <colgroup>
                                    <col width="150">
                                    <col width="150">
                                    <col width="*">
                                    <?=($period == "day") ? '<col width="70">' : '' ?>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>계정과목</th>
                                    <th>입금액</th>
                                    <th>적요</th>
                                    <?=($period == "day") ? '<th></th>' : '' ?>
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
                            <p class="sub_tit2">- 지출(기타)</p>
                            <div class="right">
                                <?php if($period == "day") { ?>
                                    <a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="CASH_OUT" data-inout="OUT">수정</a>
                                    <a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="CASH_OUT" data-inout="OUT">추가등록</a>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tb_wrap">
                            <table class="CASH_OUT">
                                <colgroup>
                                    <col width="150">
                                    <col width="150">
                                    <col width="*">
                                    <?=($period == "day") ? '<col width="70">' : '' ?>
                                </colgroup>
                                <thead>
                                <tr>
                                    <th>계정과목</th>
                                    <th>출금액</th>
                                    <th>적요</th>
                                    <?=($period == "day") ? '<th></th>' : '' ?>
                                </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </td>
                </tr>
				<tr>
					<td colspan="3" class="text_left">
						<p class="sub_tit"><i class="fas fa-caret-right f20px"></i> 통장 입출금 내역</p>
					</td>
				</tr>
				<tr>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">- 수입(거래처별)</p>
							<div class="right">
								<?php if($period == "day") { ?>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="BANK_CUSTOMER_IN" data-inout="IN">수정</a>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="BANK_CUSTOMER_IN" data-inout="IN">추가등록</a>
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="BANK_CUSTOMER_IN">
								<colgroup>
									<col width="120">
									<col width="200">
									<col width="120">
                                    <col width="*">
									<?=($period == "day") ? '<col width="70">' : '' ?>
								</colgroup>
								<thead>
								<tr>
									<th>계정과목</th>
									<th>거래처</th>
									<th>입금액</th>
                                    <th>적요</th>
									<?=($period == "day") ? '<th></th>' : '' ?>
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
							<p class="sub_tit2">- 지출(거래처별)</p>
							<div class="right">
								<?php if($period == "day") { ?>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="BANK_CUSTOMER_OUT" data-inout="OUT">수정</a>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="BANK_CUSTOMER_OUT" data-inout="OUT">추가등록</a>
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="BANK_CUSTOMER_OUT">
								<colgroup>
									<col width="120">
									<col width="200">
									<col width="120">
                                    <col width="*">
									<?=($period == "day") ? '<col width="70">' : '' ?>
								</colgroup>
								<thead>
								<tr>
									<th>계정과목</th>
									<th>거래처</th>
									<th>출금액</th>
                                    <th>적요</th>
									<?=($period == "day") ? '<th></th>' : '' ?>
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
							<p class="sub_tit2">- 수입(기타)</p>
							<div class="right">
								<?php if($period == "day") { ?>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="BANK_ETC_IN" data-inout="IN">수정</a>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="BANK_ETC_IN" data-inout="IN">추가등록</a>
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="BANK_ETC_IN">
								<colgroup>
									<col width="150">
									<col width="150">
                                    <col width="*">
									<?=($period == "day") ? '<col width="70">' : '' ?>
								</colgroup>
								<thead>
								<tr>
									<th>계정과목</th>
									<th>입금액</th>
                                    <th>적요</th>
									<?=($period == "day") ? '<th></th>' : '' ?>
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
							<p class="sub_tit2">- 지출(기타)</p>
							<div class="right">
								<?php if($period == "day") { ?>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="BANK_ETC_OUT" data-inout="OUT">수정</a>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="BANK_ETC_OUT" data-inout="OUT">추가등록</a>
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="BANK_ETC_OUT">
								<colgroup>
									<col width="150">
									<col width="150">
                                    <col width="*">
									<?=($period == "day") ? '<col width="70">' : '' ?>
								</colgroup>
								<thead>
								<tr>
									<th>계정과목</th>
									<th>출금액</th>
                                    <th>적요</th>
									<?=($period == "day") ? '<th></th>' : '' ?>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="text_left">
						<p class="sub_tit"><i class="fas fa-caret-right f20px"></i> 계좌간이체</p>
					</td>
				</tr>
				<tr>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">- 수입</p>
							<div class="right">
								<?php if($period == "day") { ?>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="TRANSFER_IN" data-inout="IN">수정</a>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="TRANSFER_IN" data-inout="IN">추가등록</a>
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="TRANSFER_IN">
								<colgroup>
									<col width="150">
									<col width="150">
                                    <col width="*">
									<?=($period == "day") ? '<col width="70"><col width="70">' : '' ?>
								</colgroup>
								<thead>
								<tr>
									<th>계정과목</th>
									<th>입금액</th>
                                    <th>적요</th>
									<?=($period == "day") ? '<th></th><th></th>' : '' ?>
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
							<p class="sub_tit2">- 지출</p>
							<div class="right">
								<?php if($period == "day") { ?>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="TRANSFER_OUT" data-inout="OUT">수정</a>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="TRANSFER_OUT" data-inout="OUT">추가등록</a>
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="TRANSFER_OUT">
								<colgroup>
									<col width="150">
									<col width="150">
                                    <col width="*">
									<?=($period == "day") ? '<col width="70"><col width="70">' : '' ?>
								</colgroup>
								<thead>
								<tr>
									<th>계정과목</th>
									<th>출금액</th>
                                    <th>적요</th>
									<?=($period == "day") ? '<th></th><th></th>' : '' ?>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 카드 사용내역</p>
							<div class="right">
								<?php if($period == "day") { ?>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-mod" data-type="CARD_OUT" data-inout="OUT">수정</a>
									<a href="javascript:;" class="btn wide_btn btn-report-pop-add" data-type="CARD_OUT" data-inout="OUT">추가등록</a>
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="CARD_OUT">
								<colgroup>
									<col width="180">
									<col width="180">
									<col width="180">
									<col width="180">
									<col width="150">
                                    <col width="*">
									<?=($period == "day") ? '<col width="70">' : '' ?>
								</colgroup>
								<thead>
								<tr>
									<th>사용자</th>
									<th>카드번호</th>
									<th>지출처</th>
									<th>계정과목</th>
									<th>지출금액</th>
                                    <th>적요</th>
									<?=($period == "day") ? '<th></th>' : '' ?>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3">&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3" class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2"><i class="fas fa-caret-right f20px"></i> 차입금계좌</p>
							<div class="right">
								<?php if($period == "day") { ?>
									<a href="javascript:;" id="btn-loan-pop" class="btn wide_btn">입금/출금</a>
								<?php } ?>
							</div>
						</div>
						<div class="tb_wrap">
							<table class="bank_loan">
								<colgroup>
									<col width="300">
									<col width="160">
									<col width="160">
									<col width="160">
									<col width="160">
									<col width="*">
									<col width="160">
								</colgroup>
								<thead>
								<tr>
									<th>계좌명</th>
									<th>대출액</th>
									<th>전일잔액</th>
									<th>상환액</th>
									<th>금일잔액</th>
									<th>비고</th>
									<th>만기일/상환일정</th>
								</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="3" class="text_left">
						<p class="sub_tit"><i class="fas fa-caret-right f20px"></i> 계정과목별 집계</p>
					</td>
				</tr>
				<tr>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">- 수입</p>
						</div>
						<div class="tb_wrap">
							<table class="ACCOUNT_IN">
								<colgroup>
									<col width="*">
									<col width="200">
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
					</td>
					<td></td>
					<td class="text_left vtop">
						<div class="btn_set">
							<p class="sub_tit2">- 지출</p>
						</div>
						<div class="tb_wrap">
							<table class="ACCOUNT_OUT">
								<colgroup>
									<col width="*">
									<col width="200">
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
					</td>
				</tr>

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
<script src="/js/page/settle.report.js?v=200609"></script>
<script>
	window.name = 'settle_report';
	SettleReport.ReportInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

