<?php

//Page Info
$pageMenuIdx = 303;

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
            <a href="javascript:;" class="btn btn_kind_write_pop">충전금 등록</a>
        </div>
        <div class="tb_wrap grid_tb">
            <table id="grid_list">
            </table>
            <div id="grid_pager"></div>
        </div>
        <div id="modal_charge" title="충전금 등록" class="red_theme" style="display: none;"></div>
    </div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script>
    function initManageCharge() {
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

        $("#grid_list").jqGrid({
            url: '/ad/manage_ad_charge_grid.php',
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
                { label: '광고 업체', name: 'seller_name', index: 'seller_name', width: 200, sortable: false},
                { label: '충전 금액', name: 'ad_amount', index: 'ad_amount', width: 200},
                { label: '사용 금액', name: 'cost', index: 'cost', width: 200},
				{ label: '잔액', name: 'total', index: 'total', width: 200},
                { label: '비고', name: 'use_amount', index: 'use_amount', width: 200}
            ],
            rowNum: Common.jsSiteConfig.jqGridRowList[1],
            rowList: Common.jsSiteConfig.jqGridRowList,
            pager: '#grid_pager',
            sortname: 'reg_date',
            sortorder: "desc",
            viewrecords: true,
            autowidth: true,
            rownumbers: true,
            shrinkToFit: true,
            height: Common.jsSiteConfig.jqGridDefaultHeight,
            loadComplete: function(){
                //컬럼 사이즈 복구
                Common.getGridColumnSizeFromStorage("adKinds", $("#grid_list"));
            }
        });

		$("#btn_searchBar").on("click", function(){
			Common.jqGridRefresh('#grid_list', 1, $("#searchForm").serialize());
		});

		$(".btn_kind_write_pop").on("click", function(){
			openChargeAdPop();
		});

		//수동 등록 모달팝업 세팅
		$("#modal_charge").dialog({
			width: 800,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});
    }

    function openChargeAdPop() {
		let p_url = "/ad/popup_add_ad_charge.php";

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html"
		}).done(function (response) {
			if(response) {
				$("#modal_charge").html(response);
				$("#modal_charge").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
		}).fail(function(jqXHR, textStatus){
			alert('요청이 실패하였습니다. 잠시 후 다시 시도하여 주세요.');
		}).always(function(){
			hideLoader();
		});
    }

	initManageCharge();
</script>