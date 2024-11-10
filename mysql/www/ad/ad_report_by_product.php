<?php

//Page Info
$pageMenuIdx = 300;

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
                        <div class="finder_col">
                            <div class="finder_col">
                                <select name="search_column">
                                    <option value="A.product_group">상품명</option>
                                    <option value="A.product_option_group">옵션명</option>
                                </select>
                                <input type="text" name="search_keyword" class="w200px enterDoSearch" placeholder="검색어" />
                            </div>
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
        <div class="tb_wrap grid_tb">
            <table id="grid_list">
            </table>
            <div id="grid_pager"></div>
        </div>
        <div class="tb_wrap" id="chart_div_wrap">
            <style>
                .chartdiv {width: 100%; height: 300px;overflow: hidden;}
            </style>
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
	let chartConfig = {
		chartList : [],
		periodType: $("#select_period_type option:selected").val(),
		startDate: $("#period_preset_start_input").val(),
		endDate: $("#period_preset_end_input").val()
	};

	let chartAsyncList = [];

    function initAdReport() {
        window.name = 'ad_report_by_product';
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

        $("#btn_searchBar").on("click", function(){
			chartConfig.periodType = $("#select_period_type option:selected").val();
			chartConfig.startDate = $("#period_preset_start_input").val();
			chartConfig.endDate = $("#period_preset_end_input").val();

			clearReportChart();

			if (checkReportDate()) {
				Common.jqGridRefresh('#grid_list', 1, $("#searchForm").serialize());
            }
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
				{ name: 'idx', index: 'idx', width: 100, hidden: true },
				{ name: 'kind_idx', index: 'kind_idx', width: 100, hidden: true },
				{ name: 'seller_idx', index: 'seller_idx', width: 100, hidden: true },
				{ name: 'period_type', index: 'period_type', width: 100, hidden: true },
				{ name: 'start_date', index: 'start_date', width: 100, hidden: true },
				{ name: 'end_date', index: 'end_date', width: 100, hidden: true },
				{ name: 'product_type', index: 'product_type', width: 100, hidden: true },
				{ name: 'product_group', index: 'product_group', width: 100, hidden: true },
				{ name: 'product_option_group', index: 'product_option_group', width: 100, hidden: true },
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
			multiselect: true,
			shrinkToFit: true,
			height: 144,
            onSelectAll: function(aRowids, status) {
				if (status) {
					getReportCharts(aRowids);
				} else {
					clearReportChart();
				}
            },
            onSelectRow: function(rowId, status, e) {
				if (status) {
					getReportChartData(rowId, null);
				} else {
					removeReportChart(rowId);
				}
            }
		});

		am4core.useTheme(am4themes_animated);
    }

    function getReportChartData(idx, callback) {
		if (chartConfig.chartList.hasOwnProperty(Number(idx))) {
			return;
		}

    	showLoader();

		//get chart data
		$.ajax({
			type: 'POST',
			url: '/ad/ad_proc.php',
			dataType: "json",
			data: {
				mode : 'get_chart_data_by_product',
				group_data : $("#grid_list").jqGrid("getRowData", idx),
				period_type : chartConfig.periodType,
				start_date : chartConfig.startDate,
				end_date : chartConfig.endDate
			}
		}).done(function (response) {
			if (response.rst) {
				addReportChart(idx, response.rst);
			}
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. error[' + jqXHR + ']\n' + textStatus);
		}).always(function(){
			hideLoader();
		});
    }

    function addReportChart(idx, data) {
    	let div = '<div class="chartdiv" id="chart_div_' + idx + '"></div>';
        $("#chart_div_wrap").append(div);

		let chart = am4core.create("chart_div_" + idx, am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";

		// Add chart title
		let title = chart.titles.create();
		title.text = data.ad_name;
		title.fontSize = 14;
		title.marginBottom = 10;

		let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.renderer.grid.template.location = 0;
		categoryAxis.dataFields.category = "date";
		categoryAxis.renderer.grid.template.disabled = true;
		categoryAxis.renderer.minGridDistance = 10;
		categoryAxis.dashLength = 5;

		let yAxesList = {
            "금액" : {"cost" : "비용", "sum_sale_amount" : "총 판매금액"},
            "갯수" : {"display_count" : "노출수", "operation_count" : "실행수", "conversion_count" : "전환수"},
            "백분율" : {"efficiency" : "광고 수익률"}
		};

		$.each(yAxesList, function(key, val){
			let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
			valueAxis.min = 0;
			valueAxis.strictMinMax = false;
			valueAxis.renderer.minGridDistance = 30;
			valueAxis.title.text = key;
			valueAxis.dashLength = 5;

			$.each(val, function(key2, title){
				let series = chart.series.push(new am4charts.LineSeries());
				series.dataFields.categoryX = "date";
				series.dataFields.valueY = key2;
				series.name = title;
				series.strokeWidth = 2;
				series.yAxis = valueAxis;
				series.bullets.push(new am4charts.CircleBullet());
				series.tooltipText = "{name}: [bold]{valueY}[/]";
				series.legendSettings.valueText = "{valueY}";
			});
		});

		// Add chart cursor
		chart.cursor = new am4charts.XYCursor();
		chart.cursor.behavior = "zoomY";

		// Add legend
		chart.legend = new am4charts.Legend();

		chart.exporting.menu = new am4core.ExportMenu();

		let chartData = [];
		$.each(data.period, function(key, date){
			let chartDatum = {};
			chartDatum.date = date;
			chartDatum.cost = 0;
			chartDatum.sum_sale_amount = 0;
			chartDatum.display_count = 0;
			chartDatum.operation_count = 0;
			chartDatum.conversion_count = 0;
			chartDatum.efficiency = 0;

			$.each(data.list, function(key, val) {
				if (date == val["operation_date"]) {
					chartDatum.cost = Number(val["cost"]);
					chartDatum.display_count = Number(val["display_count"]);
					chartDatum.operation_count = Number(val["operation_count"]);
				}

				if (date == val["settle_date"]) {
					chartDatum.sum_sale_amount = Number(val["sum_sale_amount"]);
					chartDatum.total_order_count = Number(val["total_order_count"]);
				}

				if (val["operation_date"] && val["settle_date"]) {
					chartDatum.conversion_count = chartDatum.total_order_count;
					if (chartDatum.cost > 0) {
						chartDatum.efficiency = (chartDatum.sum_sale_amount / chartDatum.cost) * 100;
                    }
                }
			});

			chartData.push(chartDatum);
		});

		chart.data = chartData;
		chartConfig.chartList[Number(idx)] = chart;
    }

    function removeReportChart(idx) {
		$('#chart_div_wrap > div[id="chart_div_' + idx + '"]').remove();
    	chartConfig.chartList[Number(idx)].dispose();
    	delete chartConfig.chartList[Number(idx)];
    }

    function getReportCharts(ids) {
		chartAsyncList = ids;
		if (chartAsyncList.length == 0)
			return;

		for (let i = 0; i < ids.length; i++) {
			getReportChartData(ids[i], null);
        }
    }

    function clearReportChart() {
        $(".chartdiv").remove();
        $.each(chartConfig.chartList, function(key, val){
        	val.dispose();
        });
		chartConfig.chartList = [];
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

    initAdReport();
</script>
<?php include_once DY_INCLUDE_PATH."/_include_bottom.php"; ?>