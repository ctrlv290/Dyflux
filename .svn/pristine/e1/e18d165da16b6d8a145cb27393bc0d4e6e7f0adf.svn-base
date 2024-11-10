<?php

//Page Info
$pageMenuIdx = 299;

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
                            <span class="text">기간 종류</span>
                            <select id="select_period_type" name="period_type">
                                <option value="d">일별</option>
                                <option value="m">월별</option>
                                <option value="y">년별</option>
                            </select>
                            <span class="info_txt col_red">일별 최대 7일, 월별 최대 12개월까지 선택 가능합니다.</span>
                        </div>
                    </div>
                    <div class="finder_set">
                        <div class="finder_col">
                            <span class="text">광고 실행일</span>
                            <input type="text" name="date_start" id="period_preset_start_input" class="w80px jqDate " value="<?=date("Y-m-d")?>" readonly="readonly" />
                            ~
                            <input type="text" name="date_end" id="period_preset_end_input" class="w80px jqDate " value="<?=date("Y-m-d")?>" readonly="readonly" />
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
                                <option value="A.product_group">상품명</option>
                                <option value="A.product_keyword">키워드</option>
                            </select>
                            <input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" />
                        </div>
                        <div class="finder_col">
                            <label>
                                <input type="radio" name="display_ad_value" value="cost" data-unit="금액(비용)" data-title="비용" checked="checked">비용
                            </label>
                        </div>
                        <div class="finder_col">
                            <label>
                                <input type="radio" name="display_ad_value" value="operation_count" data-unit="갯수(실행)" data-title="실행수">실행수
                            </label>
                        </div>
                        <div class="finder_col">
                            <label>
                                <input type="radio" name="display_ad_value" value="conversion_count" data-unit="갯수(전환)" data-title="전환수">전환수
                            </label>
                        </div>
                        <div class="finder_col">
                            <label>
                                <input type="radio" name="display_ad_value" value="efficiency" data-unit="백분율(수익)" data-title="수익률">수익률
                            </label>
                        </div>
                        <div class="finder_col">
                            <label>
                                <input type="checkbox" name="display_ad_value" value="total_sales" data-unit="금액(총판매)" data-title="판매액" checked="checked">판매금액
                            </label>
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
        <div class="tb_wrap ">
            <style>
                #chartdiv {width: 100%; height: 400px;overflow: hidden;}
            </style>
            <div id="chartdiv">
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

<script src="/js/amcharts/core.js"></script>
<script src="/js/amcharts/charts.js"></script>
<script src="/js/amcharts/lang/ko_KR.js"></script>
<script src="/js/amcharts/themes/animated.js"></script>
<link rel="stylesheet" href="/css/sumoselect.min.css">
<script>
    let reportChart = null;
    let reportChartData = null;
    let chartConfig = {
    	data: new Array(),
        periodType: $("#select_period_type option:selected").val(),
        startDate: $("#period_preset_start_input").val(),
        endDate: $("#period_preset_end_input").val()
    };

    function adReportInit() {
        window.name = 'ad_report_by_keyword';
        ManageGroup.getManageGroupList('SELLER_GROUP');

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

        $("#select_period_type").on("change", function(){
            changePeriodType($(this).val());
        });

        $('input[name="display_ad_value"]').on("click", function(){
        	onClickDisplayValue();
        });

        $("#grid_list").jqGrid({
            url: '/ad/ad_report_by_keyword_grid.php',
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
                { label: '선택', name: 'select', width: 50, formatter:function(cv, opt, ro){
                	chartConfig.data[ro.rowNum] = ro;
                    return '<input type="radio" name="select_ad_group" class="select_ad_group" data-idx="' + ro.rowNum + '"/>';
                }},
                { name: 'idx', index: 'idx', width: 100, hidden: true },
				{ name: 'period_type', index: 'period_type', width: 100, hidden: true },
				{ name: 'start_date', index: 'start_date', width: 100, hidden: true },
				{ name: 'end_date', index: 'end_date', width: 100, hidden: true },
                { label: '광고 업체', name: 'seller_name', index: 'seller_name', width: 100 },
                { label: '광고 유형', name: 'kind_name', index: 'kind_name', width: 100 },
                { label: '광고 이름', name: 'ad_name', index: 'ad_name', width: 300 },
                { label: '대표 상품', name: 'rep_product_name', index: 'rep_product_name', width: 100 },
                { label: '포함 키워드', name: 'keywords', index: 'keywords', width: 800 }
            ],
            rowNum: 10,
            rowList: [10],
            pager: '#grid_pager',
            sortname: 'name',
            sortorder: "desc",
            viewrecords: true,
            autowidth: true,
            rownumbers: true,
            multiselect: false,
            shrinkToFit: true,
            height: 200,
            loadComplete: function() {
                $(".select_ad_group").on("change", function(){
                	selectAdGroup($(this).data("idx"));
                });
            }
        });

        $("#btn_searchBar").on("click", function(){
			chartConfig.data = new Array();
			chartConfig.periodType = $("#select_period_type option:selected").val();
			chartConfig.startDate = $("#period_preset_start_input").val();
			chartConfig.endDate = $("#period_preset_end_input").val();

			if (checkReportDate()) {
				Common.jqGridRefresh('#grid_list', 1, $("#searchForm").serialize());
            }
        });

        am4core.useTheme(am4themes_animated);

		reportChart = am4core.create("chartdiv", am4charts.XYChart);
		reportChart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		reportChart.language.locale = am4lang_ko_KR;
		reportChart.fontFamilly = "dotum";

        // Add chart title
        let title = reportChart.titles.create();
        title.text = "통계";
        title.fontSize = 14;
        title.marginBottom = 10;

        let categoryAxis = reportChart.xAxes.push(new am4charts.CategoryAxis());
        categoryAxis.renderer.grid.template.location = 0;
        categoryAxis.dataFields.category = "date";
        categoryAxis.renderer.grid.template.disabled = true;
        categoryAxis.renderer.minGridDistance = 10;
        categoryAxis.dashLength = 5;

        // Add chart cursor
		reportChart.cursor = new am4charts.XYCursor();
		reportChart.cursor.behavior = "zoomY";

        // Add legend
		reportChart.legend = new am4charts.Legend();

		reportChart.exporting.menu = new am4core.ExportMenu();
    }

    function changePeriodType(type) {
		$(".jqDate").datepicker("destroy");

    	if (type == "d") {
			$(".jqDate").datepicker({});

			$("#period_preset_start_input").val('<?=date("Y-m-d")?>');
			$("#period_preset_end_input").val('<?=date("Y-m-d")?>');
        } else if (type == "m") {
			$(".jqDate").datepicker({
				changeMonth: true,
				changeYear: true,
				showButtonPanul: true,
				yearRange:"-1:+1",
				dateFormat: "yy-mm"
			});

			$("#period_preset_start_input").val('<?=date("Y-m")?>');
			$("#period_preset_end_input").val('<?=date("Y-m")?>');
        } else if (type == "y") {
			$(".jqDate").datepicker({
				changeYear: true,
				showButtonPanul: true,
				yearRange:"-1:+1",
				dateFormat: "yy"
			});

			$("#period_preset_start_input").val('<?=date("Y")?>');
			$("#period_preset_end_input").val('<?=date("Y")?>');
        }
    }

    function checkReportDate() {
    	let startDateVal = $("#period_preset_start_input").val();
		let endDateVal = $("#period_preset_end_input").val();

    	let startDate = new Date(startDateVal);
		let endDate = new Date(endDateVal);

		if (startDate > endDate) {
			alert("시작일은 종료일보다 클 수 없습니다.");
			return false;
        }

		let periodType = $("#select_period_type option:selected").val();
		if (periodType == "d") {
			let diffTime = Math.abs(endDate - startDate);
			let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
			if (diffDays > 7) {
				alert("일별 최대 7일까지 선택 가능합니다.");
				return false;
            }
        } else if (periodType == "m") {
			if (((endDate.getFullYear() - startDate.getFullYear()) * 12) + (endDate.getMonth() - startDate.getMonth()) > 12) {
				alert("월별 최대 12일까지 선택 가능합니다.");
				return false;
			}
        }

		return true;
    }

    function selectAdGroup(rowNum) {
    	let selectedData = chartConfig.data[rowNum];

    	showLoader();

        //get chart data
        $.ajax({
			type: 'POST',
			url: '/ad/ad_proc.php',
			dataType: "json",
			data: {
				mode : 'get_chart_data_by_keyword',
				group_data : selectedData,
                period_type : chartConfig.periodType,
                start_date : chartConfig.startDate,
                end_date : chartConfig.endDate
            }
        }).done(function (response) {
        	if (response.rst) {
				setChartData(response.rst);
            }
        }).fail(function(jqXHR, textStatus){
            alert('오류가 발생하였습니다. error[' + jqXHR + ']\n' + textStatus);
        }).always(function(){
        	hideLoader();
        });
    }

    function setChartData(report) {
    	if (Object.keys(report.keywords).length == 0) {
    		alert("조회한 내역이 존재하지 않습니다.");
    		return;
        }

		reportChart.data = [];
		reportChartData = report;

    	refreshChartData();
    }

    function refreshChartData() {
		refreshYAxis();

		let chartData = [];
		$.each(reportChartData.period, function(key, date){
			let chartDatum = {};
			chartDatum.date = date;
			$.each(reportChartData.keywords, function(key2, val2){
				chartDatum["cost_" + val2] = 0;
				chartDatum["operation_count_" + val2] = 0;
				chartDatum["conversion_count_" + val2] = 0;
				chartDatum["efficiency_" + val2] = 0;
				chartDatum["total_sales"] = 0;
            });
			chartData.push(chartDatum);
		});

		$.each(reportChartData.list, function(key, val) {
			$.each(chartData, function(chartDatumKey, chartDatumVal){
				if (val["keyword"]) {
					console.log(chartDatumVal.date + " ---- " + val["operation_date"]);
					if (chartDatumVal.date === val["operation_date"]) {
						chartDatumVal["cost_" + val["keywordIdx"]] = val["cost"];
						chartDatumVal["operation_count_" + val["keywordIdx"]] = val["operation_count"];

						let contributionRate = 0;
						if (Number(val["total_operation_count"]) > 0) {
							contributionRate = Number(val["operation_count"]) / Number(val["total_operation_count"]);
                        } else {
							contributionRate = 1 / reportChartData.keywords.length;
                        }

						if (val["settle_date"]) {
							chartDatumVal["conversion_count_" + val["keywordIdx"]] = Number(val["total_order_count"]) * contributionRate;
							chartDatumVal["conversion_count_" + val["keywordIdx"]] = chartDatumVal["conversion_count_" + val["keywordIdx"]].toFixed(2);

							if (Number(val["cost"]) > 0) {
								chartDatumVal["efficiency_" + val["keywordIdx"]] = (Number(val["sum_sale_amount"]) / Number(val["cost"])) * contributionRate * 100;
								chartDatumVal["efficiency_" + val["keywordIdx"]] = chartDatumVal["efficiency_" + val["keywordIdx"]].toFixed(2);
                            } else {
								chartDatumVal["efficiency_" + val["keywordIdx"]] = 0;
                            }
                        } else {
							chartDatumVal["conversion_count_" + val["keywordIdx"]] = 0;
							chartDatumVal["efficiency_" + val["keywordIdx"]] = 0;
                        }
                    }
                }
				if (chartDatumVal.date === val["settle_date"]) {
					chartDatumVal["total_sales"] = val["sum_sale_amount"];
				}
			});
		});

		reportChart.data = chartData;
    }

    function refreshYAxis() {
		while(reportChart.yAxes.length) {
			reportChart.yAxes.removeIndex(0);
		}

		while(reportChart.series.length) {
			reportChart.series.removeIndex(0);
		}

    	let chartTitle = "통계 - ";

    	$('input[name="display_ad_value"]:checked').each(function(idx, item){
			let type = $(item).val();
    		let title = $(item).data("unit");
    		let name = $(item).data("title");
    		if (idx) {
    			chartTitle += ", " + name;
            } else {
    			chartTitle += name;
            }

    		let valueAxis = reportChart.yAxes.push(new am4charts.ValueAxis());
			valueAxis.min = 0;
			valueAxis.strictMinMax = false;
			valueAxis.renderer.minGridDistance = 30;
			valueAxis.title.text = title;
			valueAxis.dashLength = 5;

			if (type != "total_sales") {
				$.each(reportChartData.keywords, function(key, val) {
					addSeries(type + "_" + val, key, valueAxis);
				});
            } else {
				addSeries('total_sales', '총 판매금액', valueAxis);
			}
        });

		reportChart.titles.values[0].text = chartTitle;
    }

    function addSeries(field, name, axis, baseColor) {
		let series = reportChart.series.push(new am4charts.LineSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = field;
		series.name = name;
		series.strokeWidth = 2;
		series.yAxis = axis;
		series.bullets.push(new am4charts.CircleBullet());
		series.tooltipText = "{name}: [bold]{valueY}[/]";
		series.legendSettings.valueText = "{valueY}";
		series.tensionX = 0.9;
    }

    function onClickDisplayValue() {
    	if (reportChartData) {
			refreshChartData(reportChartData);
        }
    }

    adReportInit();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>