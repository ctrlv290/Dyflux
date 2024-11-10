<?php

//Page Info
$pageMenuIdx = 297;

//Init
include_once "../_init_.php";

$sellerIdx = $_GET["seller_idx"] || 0;
$sellerGroupIdx = ($_GET["seller_group_idx"]) ? $_GET["seller_group_idx"] : 0;

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
                            <span class="text">실행일</span>
                            <input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=$date_start?>" readonly="readonly" />
                            ~
                            <input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=$date_end?>" readonly="readonly" />
                            <select class="sel_period_preset" id="period_preset_select"></select>
                        </div>
                        <div class="finder_col">
                            <span class="text">판매처</span>
                            <select name="seller_group_idx" class="seller_group_idx" data-selected="<?=$sellerGroupIdx?>">
                                <option value="0">전체그룹</option>
                            </select>
                            <select name="seller_idx" class="seller_idx" data-selected="<?=$sellerIdx?>" data-default-value="" data-default-text="전체 판매처">
                            </select>
                        </div>
                    </div>
                    <div class="finder_set">
                        <div class="finder_col">
                            <select name="search_column">
                                <option value="kind_name">그룹명</option>
                                <option value="kind_name">상품명</option>
                                <option value="kind_name">키워드</option>
                            </select>
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
            </div>
        </form>
        <div class="grid_btn_set_top">
            <a href="javascript:;" class="btn btn_add_ad_manual_pop">수동 등록</a>
            <a href="javascript:;" class="btn btn_add_ad_excel_pop">엑셀 등록</a>
            <div class="right">
                <a href="javascript:;" class="btn green_btn btn-seller-xls-down">다운로드</a>
            </div>
        </div>
        <div class="tb_wrap grid_tb">
            <table id="grid_list">
            </table>
            <div id="grid_pager"></div>
        </div>
        <div id="modal_add_ad_manual" title="수동 등록" class="red_theme" style="display: none;"></div>
        <div id="modal_add_ad_excel" title="엑셀 등록" class="red_theme" style="display: none;"></div>
    </div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script src="/js/selectize.min.js"></script>
<script>
    function initManageAd() {
        window.name = 'manage_ads';
        ManageGroup.getManageGroupList('SELLER_GROUP');

        //날짜 검색 초기화 및 프리셋
        Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', '', '', "4");

        //판매처 그룹 및 판매처 선택창 초기화
        CommonFunction.bindManageGroupList("SELLER_GROUP", ".seller_group_idx", ".seller_idx");
        $(".seller_idx").SumoSelect({
            placeholder: '전체 판매처',
            captionFormat : '{0}개 선택됨',
            captionFormatAllSelected : '{0}개 모두 선택됨',
            search: true,
            searchText: '판매처 검색',
            noMatch : '검색결과가 없습니다.'
        });

        initGridAd();

        $(".btn_add_ad_manual_pop").on("click", function(){
            openAddAdManualPop(null);
        });

        $(".btn_add_ad_excel_pop").on("click", function(){
            openAddAdExcelPop();
        });

        //수동 등록 모달팝업 세팅
        $("#modal_add_ad_manual").dialog({
            width: 1000,
            autoOpen: false,
            modal: true,
            classes: {
                "ui-dialog-titlebar": "blue-theme"
            },
            open : function(event, ui) { windowScrollHide() },
            close : function(event, ui) { windowScrollShow() },
        });

        //엑셀 등록 모달팝업 세팅
        $("#modal_add_ad_excel").dialog({
            width: 1300,
            autoOpen: false,
            modal: true,
            minHeight: 700,
            classes: {
                "ui-dialog-titlebar": "blue-theme"
            },
            open : function(event, ui) { windowScrollHide() },
            close : function(event, ui) { windowScrollShow() },
        });

		$("#btn_searchBar").on("click", function(){
			Common.jqGridRefresh('#grid_list', 1, $("#searchForm").serialize());
		});
    }

    function initGridAd() {
        $("#grid_list").jqGrid({
            url: '/ad/manage_ad_grid.php',
            mtype: "GET",
            datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			},
            jsonReader : {
                page: "page",
                total: "total",
                root: "rows",
                records: "records",
                repeatitems: true,
                id: "idx"
            },
            colModel: [
                { label: 'group_idx', name: 'group_idx', width: 100, hidden: true },
                { label:'수정', name: '수정', width: 80, formatter: function(cellValue, options, rowObject){
                        return '<a href="javascript:;" class="xsmall_btn btn_ad_modify" data-idx="'+rowObject.idx+'">수정</a>';
                    }},
                { label:'삭제', name: '삭제', width: 80, formatter: function(cellValue, options, rowObject){
                        return '<a href="javascript:;" class="xsmall_btn red_btn btn_ad_delete" data-idx="'+rowObject.idx+'">삭제</a>';
                    }},
                { label: '광고 업체', name: 'seller_name', index: 'seller_name', width: 150, sortable: false},
                { label: '광고 유형', name: 'kind_name', index: 'kind_name', width: 100, sortable: false},
                { label: '광고 이름', name: 'ad_name', index: 'ad_name', width: 150, sortable: false},
                { label: '대표 광고 상품', name: 'rep_product_full_name', index: 'rep_product_full_name', width: 220, sortable: false},
                { label: '키워드', name: 'keyword', index: 'keyword', width: 200, sortable: false},
                { label: '총 비용', name: 'cost', index: 'cost', width: 100, sortable: false, formatter: function(cv, opt, ro){
                        return Common.addCommas(cv);
                    }},
                { label: '실행 수', name: 'operation_count', index: 'operation_count', width: 100, sortable: false, formatter: function(cv, opt, ro){
                        return Common.addCommas(cv);
                    }},
                { label: '노출 수', name: 'display_count', index: 'display_count', width: 100, sortable: false, formatter: function(cv, opt, ro){
                        return Common.addCommas(cv);
                    }},
                { label: '광고 실행일', name: 'operation_date', index: 'operation_date', width: 100, sortable: false, formatter: function(cellValue, options, rowObject){ return Common.toDateTime(cellValue); }},
                { label: '메모', name: 'memo', index: 'memo', width: 220, sortable: false}
            ],
            rowNum: Common.jsSiteConfig.jqGridRowList[1],
            rowList: Common.jsSiteConfig.jqGridRowList,
            pager: '#grid_pager',
            sortname: 'operation_date',
            sortorder: "desc",
            viewrecords: true,
            autowidth: false,
            rownumbers: true,
            shrinkToFit: false,
            height: Common.jsSiteConfig.jqGridDefaultHeight,
            loadComplete: function(){
                $(".btn_ad_modify").on("click", function(){
                    openAddAdManualPop($(this).data("idx"));
                });

                $(".btn_ad_delete").on("click", function(){
                    deleteAdGroup($(this).data("idx"));
                });
            }
        });
    }

    function adListRefresh() {
        $("#grid_list").setGridParam({
            datatype: "json",
            page: 1,
            postData:{
                param: $("#searchForm").serialize()
            }
        }).trigger("reloadGrid");
    }

    function closeModalPop(modalSelector) {
        $(modalSelector).dialog("close");
        $(modalSelector).html("");
    }

    function openAddAdManualPop(idx) {
        let p_url = "/ad/popup_add_ad_manual.php";
        let dataObj = {};
        dataObj.mode = "add_ad_manual";
        if (idx) {
            dataObj.mode = "mod_ad_manual";
            dataObj.idx = idx;
        }

        showLoader();
        $.ajax({
            type: 'POST',
            url: p_url,
            dataType: "html",
            data: dataObj
        }).done(function (response) {
            if(response) {
                $("#modal_add_ad_manual").html(response);
                $("#modal_add_ad_manual").dialog( "open" );
            }else{
                alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
            }
        }).fail(function(jqXHR, textStatus){
            alert('요청이 실패하였습니다. 잠시 후 다시 시도하여 주세요.');
        }).always(function(){
            hideLoader();
        });
    }

    function deleteAdGroup(groupIdx) {
        if(!confirm("정말 삭제하시겠습니까?")) {
            return;
        }

        showLoader();

        let dataObj = {};
        dataObj.group_idx = groupIdx;
        dataObj.mode = "delete_ad_group";

        $.ajax({
            type: 'POST',
            url: '/ad/ad_proc.php',
            dataType: "json",
            data: dataObj
        }).done(function (response) {
            if(response.rst) {
                adListRefresh();
            }else{
                alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
            }
        }).fail(function(jqXHR, textStatus){
            alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
        }).always(function(){
            hideLoader();
        });
    }

    function openAddAdExcelPop() {
        let p_url = "/ad/popup_add_ad_excel.php";

        showLoader();
        $.ajax({
            type: 'POST',
            url: p_url,
            dataType: "html"
        }).done(function (response) {
            if(response) {
                $("#modal_add_ad_excel").html(response);
                $("#modal_add_ad_excel").dialog( "open" );

                initAddAdExcelPop();

                $(".btn_close_pop").on("click", function() {
                    closeModalPop("#modal_add_ad_excel");
                });
            }else{
                alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
            }
        }).fail(function(jqXHR, textStatus){
            alert('요청이 실패하였습니다. 잠시 후 다시 시도하여 주세요.');
        }).always(function(){
            hideLoader();
        });
    }

    initManageAd();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>