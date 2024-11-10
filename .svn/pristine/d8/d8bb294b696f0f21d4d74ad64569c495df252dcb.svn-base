<?php

//Page Info
$pageMenuIdx = 296;

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
                            <span class="text">판매처</span>
                            <select name="seller_group_idx" class="seller_group_idx" data-selected="<?=$sellerGroupIdx?>">
                                <option value="0">전체그룹</option>
                            </select>
                            <select name="seller_idx" class="seller_idx" data-selected="<?=$sellerIdx?>" data-default-value="" data-default-text="전체 판매처">
                            </select>
                        </div>
                        <div class="finder_col">
                            <select name="search_column">
                                <option value="kind_name">상품명</option>
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
            <a href="javascript:;" class="btn btn_kind_write_pop">신규등록</a>
            <div class="right">
                <a href="javascript:;" class="btn green_btn btn-seller-xls-down">다운로드</a>
            </div>
        </div>
        <div class="tb_wrap grid_tb">
            <table id="grid_list">
            </table>
            <div id="grid_pager"></div>
        </div>
        <div id="modal_write_kind" title="광고 종류 추가" class="red_theme" style="display: none;"></div>
        <div id="modal_kind_format" title="광고 포맷 변경" class="red_theme" style="display: none;"></div>
    </div>
</div>

<script src="/js/main.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/FormCheck.js"></script>
<script src="/js/page/info.seller.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script>
    window.name = 'manage_kinds';
    ManageGroup.getManageGroupList('SELLER_GROUP');

    let isWriting = false;

    function initManageKinds() {
        //판매처 그룹 및 판매처 선택창 초기화
        CommonFunction.bindManageGroupList("SELLER_GROUP", ".seller_group_idx", ".seller_idx");
        $(".seller_idx").SumoSelect({
            placeholder: '판매처를 선택해주세요.',
            captionFormat : '{0}개 선택됨',
            captionFormatAllSelected : '{0}개 모두 선택됨',
            search: true,
            searchText: '판매처 검색',
            noMatch : '검색결과가 없습니다.'
        });

        $(".btn_kind_write_pop").on("click", function(){
            openWriteKindPop(null);
        });

        $("#grid_list").jqGrid({
            url: '/ad/manage_kinds_grid.php',
            mtype: "GET",
            datatype: "json",
            jsonReader : {
                page: "page",
                total: "total",
                root: "rows",
                records: "records",
                repeatitems: true,
                id: "idx"
            },
            colModel: [
                { label:'수정', name: '수정', width: 120,formatter: function(cellValue, options, rowObject){
                        return '<a href="javascript:;" class="xsmall_btn btn_kind_modify_pop" data-idx="'+rowObject.idx+'">수정</a>';
                    }, sortable: false
                },
                { label: '광고 업체', name: 'seller_name', index: 'seller_name', width: 200, sortable: false},
                { label: '광고 상품 이름', name: 'kind_name', index: 'ad_kind_name', width: 300},
                { label: '과금 유형', name: 'code_name', index: 'code_name', width: 150},
                { label: '등록일', name: 'reg_date', index: 'reg_date', width: 200,formatter: function(cellValue, options, rowObject){ return Common.toDateTime(cellValue); }},
                { label: '사용여부', name: 'is_del', index: 'is_del', width: 100, sortable: false, formatter: function(cellValue, options, rowObject){
                        return cellValue == "Y"? "N" : "Y";
                    }
                },
                { label: '메모', name: 'memo', index: 'memo', width: 400, sortable: false},
                { label:'포맷', name: '포맷', width: 120,formatter: function(cellValue, options, rowObject){
                        return '<a href="javascript:;" class="xsmall_btn green_btn btn_kind_format_pop" data-idx="'+rowObject.idx+'">포맷</a>';
                    }, sortable: false
                }
            ],
            rowNum: Common.jsSiteConfig.jqGridRowList[1],
            rowList: Common.jsSiteConfig.jqGridRowList,
            pager: '#grid_pager',
            sortname: 'reg_date',
            sortorder: "desc",
            viewrecords: true,
            autowidth: false,
            rownumbers: true,
            shrinkToFit: false,
            height: Common.jsSiteConfig.jqGridDefaultHeight,
            loadComplete: function(){
                //수정 팝업
                $(".btn_kind_modify_pop").on("click", function(){
                    openWriteKindPop($(this).data("idx"));
                });

                //포맷 팝업
                $(".btn_kind_format_pop").on("click", function(){
                    openKindFormatPop($(this).data("idx"));
                });

                //컬럼 사이즈 복구
                Common.getGridColumnSizeFromStorage("adKinds", $("#grid_list"));
            },
            resizeStop: function(newWidth, index){
                //컬럼 사이즈 저장
                var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
                Common.setGridColumnSizeToStorage(col_ary, "adKinds");
            }
        });

        $("#btn_searchBar").on("click", function(){
            adKindsRefresh();
        });

        //옵션 추가 모달팝업 세팅
        $("#modal_write_kind").dialog({
            width: 600,
            autoOpen: false,
            modal: true,
            classes: {
                "ui-dialog-titlebar": "blue-theme"
            },
            open : function(event, ui) { windowScrollHide() },
            close : function(event, ui) { windowScrollShow() },
        });

        //포맷 변경 모달팝업 세팅
        $("#modal_kind_format").dialog({
            width: 500,
            autoOpen: false,
            modal: true,
            classes: {
                "ui-dialog-titlebar": "blue-theme"
            },
            open : function(event, ui) { windowScrollHide() },
            close : function(event, ui) { windowScrollShow() },
        });
    }

    function adKindsRefresh() {
        $("#grid_list").setGridParam({
            datatype: "json",
            page: 1,
            postData:{
                param: $("#searchForm").serialize()
            }
        }).trigger("reloadGrid");
    }

    function openWriteKindPop(idx) {
        let p_url = "/ad/popup_write_kind.php";
        let dataObj = {};
        if (idx) dataObj.kind_idx = idx;

        showLoader();
        $.ajax({
            type: 'POST',
            url: p_url,
            dataType: "html",
            data: dataObj
        }).done(function (response) {
            if(response) {
                $("#modal_write_kind").html(response);
                $("#modal_write_kind").dialog( "open" );

                initWriteKindPop();
            }else{
                alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
            }
        }).fail(function(jqXHR, textStatus){
            alert('요청이 실패하였습니다. 잠시 후 다시 시도하여 주세요.');
        }).always(function(){
            hideLoader();
        });
    }

    function closeModalPop(modalSelector) {
        $(modalSelector).html("");
        $(modalSelector).dialog("close");
    }

    function initWriteKindPop() {
        //판매처 그룹 및 판매처 선택창 초기화
        CommonFunction.bindManageGroupList("SELLER_GROUP", ".popup .seller_group_idx", ".popup .seller_idx");
        $(".popup .seller_idx").SumoSelect({
            placeholder: '판매처를 선택해주세요.',
            captionFormat : '{0}개 선택됨',
            captionFormatAllSelected : '{0}개 모두 선택됨',
            search: true,
            searchText: '판매처 검색',
            noMatch : '검색결과가 없습니다.'
        });

        //저장 버튼
        $("#btn_save_ad_kind").on("click", function(e){
            e.preventDefault ? e.preventDefault() : (e.returnValue = false);

            if (!isWriting)
                $("form[name='form_write_kind']").submit();
        });

        $(".btn_close_pop").on("click", function(){
            closeModalPop("#modal_write_kind");
        });

        //폼 Submit 이벤트
        $("form[name='form_write_kind']").submit(function(e){
            e.preventDefault();
            let returnType = false;        // "" or false;
            let valForm = new FormValidation();
            let objForm = this;

            try{
                if (!valForm.chkValue(objForm.seller_idx, "판매처를 선택해주세요.", 1, 10, null)) return returnType;
                if (!valForm.chkValue(objForm.kind_name, "이름을 정확히 입력해주세요.", 1, 20, null)) return returnType;

                isWriting = true;

                showLoader();

                $.ajax({
                    type: 'POST',
                    url: '/ad/ad_proc.php',
                    dataType: "json",
                    data: $("form[name='form_write_kind']").serialize()
                }).done(function (response) {
                    if(response.rst) {
                        alert('저장되었습니다.');
                        closeModalPop("#modal_write_kind");
                        adKindsRefresh();
                    }else{
                        alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
                    }
                }).fail(function(jqXHR, textStatus){
                    alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
                }).always(function(){
                    hideLoader();
                    isWriting = false;
                });

                return false;

            }catch(e){
                alert(e);
                isWriting = false;
                return false;
            }
        });
    }

    function openKindFormatPop(idx) {
        let p_url = "/ad/popup_kind_format.php";
        let dataObj = {};
        if (idx) dataObj.kind_idx = idx;

        showLoader();
        $.ajax({
            type: 'POST',
            url: p_url,
            dataType: "html",
            data: dataObj
        }).done(function (response) {
            if(response) {
                $("#modal_kind_format").html(response);
                $("#modal_kind_format").dialog( "open" );

                initKindFormatPop();
            }else{
                alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
            }
        }).fail(function(jqXHR, textStatus){
            alert('요청이 실패하였습니다. 잠시 후 다시 시도하여 주세요.');
        }).always(function(){
            hideLoader();
        });
    }

    function closeKindFormatPop() {
        closeModalPop("#modal_write_kind");
    }

    function initKindFormatPop() {
        //저장 버튼
        $("#btn_save_ad_kind_format").on("click", function(e){
            e.preventDefault ? e.preventDefault() : (e.returnValue = false);

            if (!isWriting)
                $("form[name='form_kind_format']").submit();
        });

        $(".btn_close_pop").on("click", function(){
            closeModalPop("#modal_kind_format");
        });

        //폼 Submit 이벤트
        $("form[name='form_kind_format']").submit(function(e){
            e.preventDefault();

            isWriting = true;

            showLoader();

            $.ajax({
                type: 'POST',
                url: '/ad/ad_proc.php',
                dataType: "json",
                data: $("form[name='form_kind_format']").serialize()
            }).done(function (response) {
                if(response.rst) {
                    alert('저장되었습니다.');
                    closeModalPop("#modal_kind_format");
                    adKindsRefresh();
                }else{
                    alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
                }
            }).fail(function(jqXHR, textStatus){
                alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
            }).always(function(){
                hideLoader();
                isWriting = false;
            });

            return false;
        });
    }

    initManageKinds();
</script>

<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>
