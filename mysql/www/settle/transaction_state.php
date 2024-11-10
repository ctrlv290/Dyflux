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

$show = $_GET["show"];
$showAry = array("all", "sale", "purchase");
if(!in_array($show, $showAry)){
    $show = $showAry[0];
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
        <span>
		<div class="wrap_tab_menu">
			<ul class="tab_menu">
				<li><a id="p_day" href="transaction_state.php?period=day<?= "&show=".$show ?>" class="<?=($period == "day") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;일별&nbsp;&nbsp;&nbsp;</a></li>
				<li><a id="p_week" href="transaction_state.php?period=week<?= "&show=".$show ?>" class="<?=($period == "week") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;주별&nbsp;&nbsp;&nbsp;</a></li>
				<li><a id="p_month" href="transaction_state.php?period=month<?= "&show=".$show ?>" class="<?=($period == "month") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;월별&nbsp;&nbsp;&nbsp;</a></li>
			</ul>
		</div>
        </span>
        &nbsp;&nbsp;
        <span>
        <div class="wrap_tab_menu">
            <ul class="tab_menu">
                <li><a href="javascript:setDisplay('all');" id="show_all" class="<?=($show == "all") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;전체&nbsp;&nbsp;&nbsp;</a></li>
                <li><a href="javascript:setDisplay('purchase');" id="show_purchase" class="<?=($show == "purchase") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;매입&nbsp;&nbsp;&nbsp;</a></li>
                <li><a href="javascript:setDisplay('sale');" id="show_sale" class="<?=($show == "sale") ? "on" : ""?>">&nbsp;&nbsp;&nbsp;매출&nbsp;&nbsp;&nbsp;</a></li>
            </ul>
        </div>
        </span>
		<form name="searchForm" id="searchForm" method="get">
			<input type="hidden" id="period" name="period" value="<?=$period?>" />
            <input type="hidden" id="show" name="show" value="show_all" />
			<div class="find_wrap">
				<div class="finder">
					<div class="finder_set">
						<div class="finder_col">
							<span class="text" style="margin-right: 8px;">기 간</span>

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
        <?php if($show == "purchase"){?>
        <div class="sale-div" style="display:none">
        <?php }else{ ?>
        <div class="sale-div">
        <?php } ?>
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
                            <p class="sub_tit2">매출현황(판매처)</p>
                            <div class="right">
                                <?php if($period == "day") { ?>
                                    <!--<a href="javascript:;" id="btn-save-sale-credit" class="btn wide_btn">저장</a>-->
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tb_wrap">
                            <table class="sale-credit">
                                <colgroup>
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="*">
                                    <col width="150">
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
                        <br>
                        <div class="btn_set">
                            <p class="sub_tit2">매출현황(일반거래처)</p>
                        </div>
                        <div class="tb_wrap">
                            <table class="sale-prepay-n">
                                <colgroup>
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="*">
                                    <col width="150">
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
                        <br>
                        <div class="btn_set">
                            <p class="sub_tit2">매출현황(선입금)</p>
                        </div>
                        <div class="tb_wrap">
                            <table class="sale-prepay">
                                <colgroup>
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="*">
                                    <col width="150">
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
                        <br>
                        <?php if($period == "day") { ?>
                            <div class="btn_set">
                                <p class="sub_tit2">매출현황(기타)</p>
                                <div class="right">
                                    <a href="javascript:;" id="btn-add-sale-etc" data-tran_type="SALE_ETC" class="btn wide_btn">추가</a>
                                </div>
                            </div>
                            <div class="tb_wrap">
                                <table class="sale-etc">
                                    <colgroup>
                                        <col width="180">
                                        <col width="180">
                                        <col width="180">
                                        <col width="180">
                                        <col width="180">
                                        <col width="*">
                                        <col width="150">
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
                        <?php } ?>
                    </td>
                </tr>
                </tbody>
            </table>
            <br>
        </div>
        <?php if($show == "sale"){?>
        <div class="purchase-div" style="display:none">
        <?php }else{ ?>
        <div class="purchase-div">
        <?php } ?>
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
                            <p class="sub_tit2">매입현황(일 결제 업체) </p>
                            <div class="right">
                                <?php if($period == "day") { ?>
                                    <!--<a href="javascript:;" id="btn-save-purchase-credit" class="btn wide_btn">저장</a>-->
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tb_wrap">
                            <table class="purchase-credit-type-d">
                                <colgroup>
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="*">
                                    <col width="150">
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
                        <br>
                        <div class="btn_set">
                            <p class="sub_tit2">매입현황(월 결제 업체) </p>
                            <div class="right">
                                <?php if($period == "day") { ?>
                                    <!--<a href="javascript:;" id="btn-save-purchase-credit" class="btn wide_btn">저장</a>-->
                                <?php } ?>
                            </div>
                        </div>
                        <div class="tb_wrap">
                            <table class="purchase-credit-type-m">
                                <colgroup>
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="*">
                                    <col width="150">
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
                        <br>
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
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="180">
                                    <col width="*">
                                    <col width="150">
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
                        <br>
                        <?php if($period == "day") { ?>
                            <div class="btn_set">
                                <p class="sub_tit2">매입현황(기타)</p>
                                <div class="right">
                                    <a href="javascript:;" id="btn-add-purchase-etc" data-tran_type="PURCHASE_ETC" class="btn wide_btn">추가</a>
                                </div>
                            </div>
                            <div class="tb_wrap">
                                <table class="purchase-etc">
                                    <colgroup>
                                        <col width="180">
                                        <col width="180">
                                        <col width="180">
                                        <col width="180">
                                        <col width="180">
                                        <col width="*">
                                        <col width="150">
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
                        <?php } ?>
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
<script src="/js/page/settle.purchase.js?v=200413"></script>
<script>
	window.name = 'settle_transaction_state';
	$(function(){
		SettlePurchase.TransactionStateInit();
	});

    //유형 선택시 매출 매입 숨기기
    function setDisplay(type){
        if(type == 'all') {
            $("#show_all").attr('class', 'on');
            $("#show_sale").attr('class', '');
            $("#show_purchase").attr('class', '');
            $(".sale-div").css('display','');
            $(".purchase-div").css('display','');
            $("#show").val("show_all");
            $("#p_day").attr('href', 'transaction_state.php?period=day&show=all');
            $("#p_week").attr('href', 'transaction_state.php?period=week&show=all');
            $("#p_month").attr('href', 'transaction_state.php?period=month&show=all');
        } else if(type == 'sale'){
            $("#show_all").attr('class', '');
            $("#show_sale").attr('class', 'on');
            $("#show_purchase").attr('class', '');
            $(".sale-div").css('display','');
            $(".purchase-div").css('display','none');
            $("#show").val("show_sale");
            $("#p_day").attr('href', 'transaction_state.php?period=day&show=sale');
            $("#p_week").attr('href', 'transaction_state.php?period=week&show=sale');
            $("#p_month").attr('href', 'transaction_state.php?period=month&show=sale');
        } else if (type == 'purchase'){
            $("#show_all").attr('class', '');
            $("#show_sale").attr('class', '');
            $("#show_purchase").attr('class', 'on');
            $(".sale-div").css('display','none');
            $(".purchase-div").css('display','');
            $("#show").val("show_purchase");
            $("#p_day").attr('href', 'transaction_state.php?period=day&show=purchase');
            $("#p_week").attr('href','transaction_state.php?period=week&show=purchase');
            $("#p_month").attr('href', 'transaction_state.php?period=month&show=purchase');
        }
    }
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>

