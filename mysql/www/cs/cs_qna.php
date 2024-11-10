<?php
/**
 * User: ysh
 * Date: 2020-03-05
 * Desc: CS 수집 팝업 페이지
 */
//Page Info
$pageMenuIdx = 308;
//Permission IDX
$pagePermissionIdx = 308;
//Init
include_once "../_init_.php";

//오늘
$now_date = date('Y-m-d');

$date_start          = $_GET["date_start"];
$date_end            = $_GET["date_end"];
$order_progress_step = $_GET["order_progress_step"];
$order_cs_status     = $_GET["order_cs_status"];

$product_supplier_group_idx = ($_GET["product_supplier_group_idx"]) ? $_GET["product_supplier_group_idx"] : 0;
$supplier_idx               = $_GET["supplier_idx"] || 0;
$product_seller_group_idx = ($_GET["product_seller_group_idx"]) ? $_GET["product_seller_group_idx"] : 0;
$seller_idx               = $_GET["seller_idx"] || 0;

?>
<?php include_once DY_INCLUDE_PATH . "/_include_top_popup.php"; ?>
<?php include_once DY_INCLUDE_PATH . "/_include_header.php"; ?>
<div class="container popup">
<?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
    <br>
    <div class="content">
        <div class="progress-bar-wrapper"></div>
        <form name="searchForm" id="searchForm" method="get">
            <div class="find_wrap">
                <div class="finder">
                    <div class="finder_set">
                        <div class="finder_col">
                            <span class="text">등록일자</span>
                            <input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
                            ~
                            <input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
                            <select class="sel_period_preset" id="period_preset_select"></select>
                        </div>
                    </div>
                    <div class="finder_set">
                        <div class="finder_col">
                            <span class="text">판매처</span>
                            <select name="seller_idx" class="seller_idx">
                                <option value="90034">네이버스토어팜</option>
                            </select>
                        </div>
                        <div class="finder_col">
                            <select name="search_column">
                                <option value="product_name">상품명</option>
                            </select>
                            <input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" />
                            <a href="javascript:;" id="btn_searchBar" class="btn btn_default">조회</a>
                        </div>
                    </div>
                </div>
                <div class="find_btn">
                    <div class="table">
                        <div class="table_cell">
                            <a href="javascript:;" id="btn_searchBar" class="big_btn btn_default">수집</a>
                        </div>
                    </div>
                </div>
                <a href="javascript:;" class="find_hide_btn">
                    <i class="fas fa-angle-up up_btn"></i>
                    <i class="fas fa-angle-down dw_btn"></i>
                </a>
            </div>
        </form>
        <div class="tb_wrap ">
            <table class="no_border max1200">
                <colgroup>
                    <col width="39%" />
                    <col width="20" />
                    <col width="*" />
                </colgroup>
                <tbody>
                <tr>
                    <td class="text_left vtop">
                        <div class="tb_wrap">
                            <p class="sub_tit2">CS목록</p>
                            <div class="tb_wrap grid_tb">
                                <table id="grid_list"></table>
                                <div id="grid_pager"></div>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td class="text_left vtop">
                        <div class="tb_wrap">
                            <p class="sub_tit2">CS 내역</p>
                            <div class="bot">
                                <div class="inner cs_list" id="cs_list">
                                </div>
                            </div>
                            <div class="top">
                                <div class="r_set">
                                    <a href="javascript:;" class="btn btn-cs-write">답변저장</a>
                                </div>
                            </div>
                        </div>
<!--                        <div class="tb_wrap">-->
<!--                            <p class="sub_tit2">판매처</p>-->
<!--                            <table class="summary-seller">-->
<!--                                <colgroup>-->
<!--                                    <col width="25%">-->
<!--                                    <col width="25%">-->
<!--                                    <col width="25%">-->
<!--                                    <col width="25%">-->
<!--                                </colgroup>-->
<!--                                <thead>-->
<!--                                <tr>-->
<!--                                    <th>판매처</th>-->
<!--                                    <th>매출합계</th>-->
<!--                                    <th>정산예정</th>-->
<!--                                    <th>주문수량</th>-->
<!--                                </tr>-->
<!--                                </thead>-->
<!--                                <tbody>-->
<!--                                </tbody>-->
<!--                            </table>-->
<!--                        </div>-->
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<iframe src="about:_blank" name="xls_hidden_frame" frameborder="0" class="dis_none"></iframe>
<script src="/js/page/common.function.js"></script>
<script src="/js/progress-bar.js"></script>
<script src="/js/main.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link href="/css/progress-bar.css" rel="stylesheet" />
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/page/cs.cs.js?v=200224"></script>
<script>
    window.name = "cs_qna_pop";
    CSPopup.CSQnaPopupInit();
</script>
<?php include_once DY_INCLUDE_PATH . "/_include_bottom.php"; ?>

