<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 옵션 추가/수정 페이지
 */
//Page Info
$pageMenuIdx = 100;
//Init
include_once "../_init_.php";

$send_date = isset($_GET['send_date']) ? $_GET['send_date'] : date('Y-m-d');
$si =  isset($_GET['month']) ? $_GET['month'] : date('H');
$bun =  isset($_GET['month']) ? $_GET['month'] : date('i');

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
    <?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
    <div class="content">
        <form name="searchForm" id="searchForm" method="get">
            <div class="find_wrap">
                <div class="finder">
                    <div class="finder_set">
                        <div class="finder_col">
                            <span class="text">발송일</span>
                            <input type="text" name="send_date" id="send_date" class="w80px jqDate " value="<?=$send_date?>" readonly="readonly" />
                        </div>
                        <div class="finder_col">
                            <select name="si">
                                <option value="">선택</option>
                                <?
                                for($i=0; $i<24; $i++) {
                                    $val = $i;

                                    if(strlen($val) == 1) {
                                        $val = "0" . $val;
                                    }
                                ?>
                                    <option value="<?=$val?>" <?=($val == $si) ? "selected" : "" ?>><?=$val?></option>
                                <? } ?>

                            </select>시

                            <select name="bun">
                                <option value="">선택</option>
                                <?
                                    for($i=0; $i<60; $i++) {
                                        $val = $i;

                                        if(strlen($val) == 1) {
                                            $val = "0" . $val;
                                        }
                                ?>
                                <option value="<?=$val?>" <?=($val == $bun) ? "selected" : "" ?>><?=$val?></option>
                                <? } ?>
                            </select>분
                        </div>
                        <div class="finder_col">
                            <span class="text">검색어</span>
                            <select name="search_column" id="search_column">
                                <option value="">전체</option>
                                <option value="TEMPLATE_CODE">템플릿코드</option>
                            </select>
                        </div>
                        <div class="finder_col">
                            <input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" />
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

        <div class="tb_wrap grid_tb">
            <table id="grid_list">
            </table>
            <div id="grid_pager"></div>
        </div>
    </div>
</div>
<script src="/js/main.js"></script>
<link href="/js/yearpicker/yearpicker.css" rel="stylesheet" type="text/css" />
<script src="/js/yearpicker/yearpicker.js"></script>
<script>
    var AlimTalkUse = (function() {
        var root = this;

        var init = function () {
        };

        //템플릿 리스트 초기화
        var AlimTalkUseListInit = function() {

            //템플릿 목록 바인딩 jqGrid
            $("#grid_list").jqGrid({
                url: './sms_alimtalk_send_history_grid.php',
                mtype: "GET",
                datatype: "json",
                jsonReader: {
                    page: "page",
                    total: "total",
                    root: "rows",
                    records: "records",
                    repeatitems: true,
                    id: "idx"
                },
                colModel: [
                    {label: '제목', name: 'al_title', index: 'al_title', width: 150},
                    {label: '발송일', name: 'al_date', index: 'al_date', width: 250},
                    {label: '템플릿코드', name: 'tp_code', index: 'tp_code', width: 250, sortable: false},
                    {label: '보낸번호', name: 'send_phone', index: 'send_phone', width: 250, sortable: false},
                    {label: '요청수', name: 'receive_cnt', index: 'receive_cnt', width: 250, sortable: false},
                    {label: '성공수', name: 'send_succ_cnt', index: 'send_succ_cnt', width: 250, sortable: false},
                    {label: '실패수', name: 'send_fail_cnt', index: 'send_fail_cnt', width: 250, sortable: false}
                ],
                rowNum: Common.jsSiteConfig.jqGridRowList[1],
                rowList: Common.jsSiteConfig.jqGridRowList,
                pager: '#grid_pager',
                sortname: 'REQUEST_TIME',
                sortorder: "desc",
                viewrecords: true,
                autowidth: true,
                rownumbers: true,
                shrinkToFit: true,
                height: Common.jsSiteConfig.jqGridDefaultHeight,
                loadComplete: function () {


                }
            });

            //브라우저 리사이즈 시 jqgrid 리사이징
            $(window).on("resize", function(){
                Common.jqGridResize("#grid_list");
            }).trigger("resize");

            //검색 폼 Submit 방지
            $("#searchForm").on("submit", function(e){
                e.preventDefault();
            });

            //Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
            $("input.enterDoSearch").on("keyup", function(e){
                var keyCode = (event.keyCode ? event.keyCode : event.which);
                if (keyCode == 13) {
                    event.preventDefault();
                    AlimTalkUseListSearch();
                }
            });

            //검색 버튼 클릭 이벤트
            $("#btn_searchBar").on("click", function(){
                AlimTalkUseListSearch();
            });
        };

        var AlimTalkUseListSearch = function(){
            $("#grid_list").setGridParam({
                datatype: "json",
                postData:{
                    param: $("#searchForm").serialize()
                }
            }).trigger("reloadGrid");
        };

        return {
            AlimTalkUseListInit: AlimTalkUseListInit,
        }
    })();

    window.name = 'sms_alimtalk_use_list';
    AlimTalkUse.AlimTalkUseListInit();
</script>

