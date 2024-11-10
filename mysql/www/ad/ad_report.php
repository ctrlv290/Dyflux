<?php

//Page Info
$pageMenuIdx = 298;

//Init
include_once "../_init_.php";

$sellerIdx = $_GET["seller_idx"] || 0;
$sellerGroupIdx = ($_GET["seller_group_idx"]) ? $_GET["seller_group_idx"] : 0;

$sumTextFormat = "비용 합계 : <strong>%s</strong>원, 매출 합계 : <strong>%s</strong>원";

?>
<?php include_once DY_INCLUDE_PATH."/_include_top.php"; ?>
<?php include_once DY_INCLUDE_PATH."/_include_header.php"; ?>
<div class="container">
    <?php include_once DY_INCLUDE_PATH."/_include_main_nav.php"; ?>
    <div class="content">
        <form name="searchForm" id="searchForm" method="get">
            <input type="hidden" name="report_type" value="ad_report">
            <div class="find_wrap">
                <div class="finder">
                    <div class="finder_set">
                        <div class="finder_col">
                            <span class="text">광고 실행일</span>
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
                                <option value="ad_name">광고명</option>
                                <option value="keyword">키워드</option>
                                <option value="product_name">상품명</option>
                                <option value="product_option_name">옵션명</option>
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
            <span id="report_sum_text"></span>
            <div class="right">
                <a href="javascript:;" class="btn green_btn btn-seller-xls-down">다운로드</a>
            </div>
        </div>
        <div class="tb_wrap grid_tb">
            <table id="grid_list">
            </table>
            <div id="grid_pager"></div>
        </div>
    </div>
</div>
<script src="/js/main.js"></script>
<script src="/js/page/info.group.js"></script>
<script src="/js/page/info.category.js"></script>
<script src="/js/page/common.function.js"></script>
<script src="/js/jquery.sumoselect.min.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">

<script>
    function adReportInit() {
        window.name = 'ad_report';
        ManageGroup.getManageGroupList('SELLER_GROUP');

        //날짜 검색 초기화 및 프리셋
        Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "4");

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
            url: '/ad/ad_report_grid.php',
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
                { label: '광고 업체', name: 'seller_name', index: 'seller_name', width: 100 },
                { label: '광고 유형', name: 'kind_name', index: 'kind_name', width: 100 },
                { label: '광고 이름', name: 'ad_name', index: 'ad_name', width: 200 },
                { label: '광고 그룹', name: 'seller_name', index: 'seller_name', width: 100 },
                { label: '대표 상품', name: 'rep_product_name', index: 'rep_product_name', width: 100 },
                { label: '키워드', name: 'keyword', index: 'keyword', width: 100 },
                { label: '광고일', name: 'operation_date', index: 'operation_date', width: 100 },
                { label: '노출수', name: 'display_count', index: 'display_count', width: 100, formatter:function(cv, opt, ro){
                	return Common.addCommas(cv);
                }},
                { label: '실행수', name: 'operation_count', index: 'operation_count', width: 100, formatter:function(cv, opt, ro){
                	return Common.addCommas(cv);
                }},
                { label: '총 비용', name: 'cost', index: 'cost', width: 100, formatter:function(cv, opt, ro){
                	return Common.addCommas(cv);
                }},
                { label: '매출액', name: 'total_sale_amount', index: 'total_sale_amount', width: 100, formatter:function(cv, opt, ro){
                	return Common.addCommas(cv);
                }},
                { label: '주문수', name: 'total_product_count', index: 'total_product_count', width: 100, formatter:function(cv, opt, ro){
                	return Common.addCommas(cv);
                }},
				{ label: '기여도', name: 'contribute_rate', index: 'contribute_rate', width: 100, hidden: false, formatter:function(cv, opt, ro){
					if (Number(ro.total_operation_count) > 0) {
						ro.contribute_rate = Number(ro.operation_count) / Number(ro.total_operation_count);
                    } else {
						ro.contribute_rate = 1 / Number(ro.keyword_count);
                    }
					ro.contribute_rate = ro.contribute_rate.toFixed(2);
					return ro.contribute_rate;
				}},
                { label: '추정 전환수', name: 'cc_conversion_count', index: 'cc_conversion_count', width: 100, formatter:function(cv, opt, ro){
					ro.cc_conversion_count	= Number(ro.contribute_rate) * Number(ro.total_order_count);
					ro.cc_conversion_count  = ro.cc_conversion_count.toFixed(2);
                	return Common.addCommas(ro.cc_conversion_count);
                }},
                { label: '전환률(%)', name: 'cc_conversion_rate', index: 'cc_conversion_rate', width: 100, formatter:function(cv, opt, ro){
                	if (Number(ro.total_operation_count) > 0) {
						ro.cc_conversion_rate = (Number(ro.total_order_count) / Number(ro.total_operation_count)) * Number(ro.contribute_rate) * 100;
                    } else {
						ro.cc_conversion_rate = 0;
                    }

                	ro.cc_conversion_rate = ro.cc_conversion_rate.toFixed(2);
                	return ro.cc_conversion_rate;
                }},
                { label: '수익률(%)', name: 'benefit_rate', index: 'benefit_rate', width: 100, formatter: function(cv, opt, ro){
                	if (ro.total_sale_amount) {
                		if (Number(ro.cost) > 0) {
							ro.benefit_rate = (Number(ro.total_sale_amount) / Number(ro.cost)) * ro.contribute_rate * 100;
                        } else {
							ro.benefit_rate = 0;
                        }
                    } else {
						ro.benefit_rate = 0;
                    }

                	ro.benefit_rate = ro.benefit_rate.toFixed(2);
                	return Common.addCommas(ro.benefit_rate);
                }}
            ],
            rowNum: Common.jsSiteConfig.jqGridRowList[1],
            rowList: Common.jsSiteConfig.jqGridRowList,
            pager: '#grid_pager',
            sortname: 'name',
            sortorder: "desc",
            viewrecords: true,
            autowidth: true,
            rownumbers: true,
            shrinkToFit: false,
            height: 550,
			loadComplete: function() {
            	refreshSumText();
			}
        });

        $("#btn_searchBar").on("click", function(){
            Common.jqGridRefresh('#grid_list', 1, $("#searchForm").serialize());
        });
    }

    function refreshSumText() {
        let list = $("#grid_list").jqGrid("getDataIDs");
        for (let i = 0; i < list.length; i++) {
        	let rowData = $("#grid_list").jqGrid("getRowData", list[i]);
        	let cost = Number(String(rowData.cost).replace(",", ""));
        	console.log(cost);
        }
    }

    adReportInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>