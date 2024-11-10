<?php

//Page Info
$pageMenuIdx = 301;

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
            <input type="hidden" name="mode" value="get_chart_data_by_market">
			<div class="find_wrap" style="margin-bottom: 10px;">
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
        <p class="sub_desc">
            총 광고비용 : 광고비 합, 총 판매금액 : 매입매출현황 > 매출합계 합, 총 판매이익 : 매입매출현황 > 매출이익 합, 광고 수익 : 총 광고비용 - 총 판매금액
        </p>
		<div class="tb_wrap" id="chart_div_wrap">
            <table class="w100per" id="table_report">
                <colgroup>
                    <col width="100">
                    <col width="400">
                    <col width="*">
                </colgroup>
                <tbody>
                    <tr>
                        <th>광고업체</th>
                        <th>합산 통계</th>
                        <th>기간별 통계</th>
                    </tr>
                </tbody>
            </table>
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
    let chartList = [];

	function initAdReport() {
		window.name = 'ad_report_by_market';
		ManageGroup.getManageGroupList('SELLER_GROUP');

		am4core.useTheme(am4themes_animated);

		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', '', '', '1');

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
            searchReport();
		});

		searchReport();
	}

	function searchReport() {
		if (!checkReportDate()) {
			return;
        }

		removeAllChartRow();

		showLoader();

		//get chart data
		$.ajax({
			type: 'POST',
			url: '/ad/ad_proc.php',
			dataType: "json",
			data: $("#searchForm").serialize()
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
		let leftChartList = {};
		let rightChartList = {};

		let leftChartData = {};
		let rightChartData = {};

		if (report.list.length > 0) {
			$.each(report.list, function(key, val){
				if ($("#chart_tr_" + val["seller_idx"]).length == 0) {
					let trHtml = '<tr class="table_report_row" id="chart_tr_' + val["seller_idx"] + '" style="height: 400px;">' +
						'<td>' + val["seller_name"] + '</td>' +
						'<td><div class="w100per" id="chart_div_left_' + val["seller_idx"] + '" style="height: 400px;"></div></td>' +
						'<td><div class="w100per" id="chart_div_right_' + val["seller_idx"] + '" style="height: 400px;"></div></td>' +
						'</tr>';

					$('table[id="table_report"] > tbody:last').append(trHtml);

					let chart = addLeftChart(val["seller_idx"], val["seller_name"]);
					leftChartList[val["seller_idx"]] = chart;

					chart = addRightChart(val["seller_idx"], val["seller_name"]);
					rightChartList[val["seller_idx"]] = chart;

					let datum = [];
					datum.push({"name":"sum_cost", "category":"총 비용", "value":0});
					datum.push({"name":"sum_sale_amt", "category":"총 판매금", "value":0});
					datum.push({"name":"sum_profit_amt", "category":"총 이익금", "value":0});

					leftChartData[val["seller_idx"]] = datum;
					rightChartData[val["seller_idx"]] = [];
				}

				let datum = {};
				datum.sum_cost = Number(val["sum_cost"]);
				datum.sum_sale_amt = Number(val["sum_sale_amt"]);
				datum.sum_profit_amt = Number(val["sum_profit_amt"]);
				datum.date = val["operation_date"];
				datum.sum_efficiency_amt = datum.sum_profit_amt - datum.sum_cost;

				rightChartData[val["seller_idx"]].push(datum);

				$.each(leftChartData[val["seller_idx"]], function(key, val){
					val["value"] += datum[val["name"]];
                });
			});

			$.each(rightChartData, function(key, val){
				leftChartList[key].data = leftChartData[key];
				rightChartList[key].data = val;
			});
        } else {
			let trHtml = '<tr class="table_report_row">' +
				'<td colspan="3">내역이 없습니다</td>' +
				'</tr>';

			$('table[id="table_report"] > tbody:last').append(trHtml);
        }
    }

    function addLeftChart(idx, name) {
		let chart = am4core.create("chart_div_left_" + idx, am4charts.PieChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";

		chart.innerRadius = am4core.percent(50);

		let pieSeries = chart.series.push(new am4charts.PieSeries());
		pieSeries.dataFields.value = "value";
		pieSeries.dataFields.category = "category";
		pieSeries.slices.template.stroke = am4core.color("#fff");
		pieSeries.slices.template.strokeWidth = 2;
		pieSeries.slices.template.strokeOpacity = 1;
		pieSeries.ticks.template.disabled = true;
		pieSeries.labels.template.disabled = true;
		pieSeries.labels.template.text = "{category}: {value}원";
		pieSeries.slices.template.tooltipText = "{category}: {value}원";
		pieSeries.legendSettings.labelText = '{category}';
		pieSeries.legendSettings.valueText = '{value}원';

		// This creates initial animation
		pieSeries.hiddenState.properties.opacity = 1;
		pieSeries.hiddenState.properties.endAngle = -90;
		pieSeries.hiddenState.properties.startAngle = -90;

		pieSeries.colors.list = [
			am4core.color("#efc35d"),
			am4core.color("#6bb6e2"),
			am4core.color("#FF6F91")
		];

		// Create a base filter effect (as if it's not there) for the hover to return to
		let shadow = pieSeries.slices.template.filters.push(new am4core.DropShadowFilter);
		shadow.opacity = 0;

        // Create hover state
		let hoverState = pieSeries.slices.template.states.getKey("hover"); // normally we have to create the hover state, in this case it already exists

        // Slightly shift the shadow and make it more prominent on hover
		let hoverShadow = hoverState.filters.push(new am4core.DropShadowFilter);
		hoverShadow.opacity = 0.7;
		hoverShadow.blur = 5;

		chart.legend = new am4charts.Legend();

		chartList.push(chart);

		return chart;
    }

    function addRightChart(idx, name) {
		let chart = am4core.create("chart_div_right_" + idx, am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";

		chart.colors.list = [
			am4core.color("#efc35d"),
			am4core.color("#6bb6e2"),
			am4core.color("#FF6F91"),
			am4core.color("#FF0000")
		];

		let categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.renderer.grid.template.location = 0;
		categoryAxis.dataFields.category = "date";
		categoryAxis.renderer.grid.template.disabled = true;
		categoryAxis.renderer.minGridDistance = 10;
		categoryAxis.dashLength = 5;
		categoryAxis.renderer.cellStartLocation = 0.2;
		categoryAxis.renderer.cellEndLocation = 0.8;

		let yAxesList = {
			"금액" : {"sum_cost" : "총 광고비용", "sum_sale_amt" : "총 판매금액", "sum_profit_amt" : "총 판매이익", "sum_efficiency_amt" : "광고 수익"}
		};

		$.each(yAxesList, function(key, val){
			let valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
			valueAxis.strictMinMax = false;
			valueAxis.renderer.minGridDistance = 30;
			valueAxis.dashLength = 5;

			let i = 1;
			$.each(val, function(key2, title){
				let series = chart.series.push(new am4charts.ColumnSeries());
				series.dataFields.categoryX = "date";
				series.dataFields.valueY = key2;
				series.name = title;
				series.clustered = true;
				series.yAxis = valueAxis;
				series.tooltipText = "{name}: [bold]{valueY}원[/]";
				series.legendSettings.valueText = "{valueY}원";
				series.columns.template.strokeWidth = 2;
				i++;
			});
		});

		// Add chart cursor
		chart.cursor = new am4charts.XYCursor();
		chart.cursor.behavior = "zoomY";

		// Add legend
		chart.legend = new am4charts.Legend();

		chart.exporting.menu = new am4core.ExportMenu();

		chartList.push(chart);

		return chart;
    }

    function removeAllChartRow() {
		$('table[id="table_report"] > tbody:last .table_report_row').remove();
        $.each(chartList, function(key, val){
        	val.dispose();
        });
        chartList = [];
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
