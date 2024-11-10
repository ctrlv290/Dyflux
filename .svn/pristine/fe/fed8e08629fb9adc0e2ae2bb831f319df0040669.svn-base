/*
 * 재고조회 관리 js
 */
var chartData = [];
var StockChart = (function() {
	var root = this;

	var init = function () {
	};

	var StockChartInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

		$("#btn_searchBar").on("click", function(){
			StockChartGetData();
		});
	};

	var StockChartGetData = function(){

		var p_url = "./stock_product_chart_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_chart";
		dataObj.product_option_idx = $("#product_option_idx").val();
		dataObj.stock_unit_price = $("#stock_unit_price").val();
		dataObj.date_start = $("#period_preset_start_input").val();
		dataObj.date_end = $("#period_preset_end_input").val();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				chartData = [];
				console.log(response.data);
				$.each(response.data, function(i, o){

					var sum_amount = (o.sum_amount == null) ? 0 : o.sum_amount;
					var stock_amount_IN = (o.stock_amount_IN == null) ? 0 : o.stock_amount_IN;
					var stock_amount_OUT = (o.stock_amount_OUT == null) ? 0 : o.stock_amount_OUT;
					var stock_amount_INVOICE = (o.stock_amount_INVOICE == null) ? 0 : o.stock_amount_INVOICE;
					var stock_amount_SHIPPED = (o.stock_amount_SHIPPED == null) ? 0 : o.stock_amount_SHIPPED;
					chartData.push({"date": o.dt, "sum_amount": sum_amount, "stock_amount_IN": stock_amount_IN, "stock_amount_OUT": stock_amount_OUT, "stock_amount_INVOICE": stock_amount_INVOICE, "stock_amount_SHIPPED": stock_amount_SHIPPED});
				});

				console.log(chartData);
				DrawChart();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(wrap_sel);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(wrap_sel);
		});

	};

	var DrawChart = function(){

		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		var chart = am4core.create("chartdiv", am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";
		chart.dateFormatter.inputDateFormat = "yyyy-MM-dd";

		// Add chart title
		var title = chart.titles.create();
		title.text = "통계";
		title.fontSize = 14;
		title.marginBottom = 10;


		chart.data = chartData;

		// var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		// categoryAxis.renderer.grid.template.location = 0;
		// categoryAxis.dataFields.category = "date";
		// categoryAxis.renderer.grid.template.disabled = true;
		// categoryAxis.renderer.minGridDistance = 10;
		// categoryAxis.dashLength = 5;
		// //categoryAxis.renderer.labels.template.rotation = 45;

		var categoryAxis = chart.xAxes.push(new am4charts.DateAxis());

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.min = 0;
		valueAxis.strictMinMax = false;
		valueAxis.renderer.minGridDistance = 30;
		valueAxis.title.text = "Qty";
		valueAxis.dashLength = 5;

		var series = chart.series.push(new am4charts.LineSeries());
		series.dataFields.dateX = "date";
		series.dataFields.valueY = "sum_amount";
		series.name = "재고";
		series.tooltipText = "{value}";
		series.strokeWidth = 2;
		var s1Bullet = series.bullets.push(new am4charts.CircleBullet());
		s1Bullet.circle.radius = 4;
		series.tooltipText = "{name} - {dateX}: {valueY}";
		series.legendSettings.valueText = "{valueY}";
		series.visible  = false;
		//series.tooltip.background.fill = am4core.color("#FFF");
		// series.tooltip.getFillFromObject = false;
		// series.tooltip.getStrokeFromObject = true;
		// series.tooltip.background.strokeWidth = 3;
		series.sequencedInterpolation = true;
		series.fillOpacity = 0.6;
		series.defaultState.transitionDuration = 1000;
		series.stacked = true;
		series.strokeWidth = 2;


		var series2 = chart.series.push(new am4charts.ColumnSeries());
		series2.dataFields.dateX = "date";
		series2.dataFields.valueY = "stock_amount_IN";
		series2.name = "입고";
		series2.tooltipText = "{value}";
		series2.strokeWidth = 2;
		var s2Bullet = series2.bullets.push(new am4charts.CircleBullet());
		s2Bullet.circle.radius = 4;
		series2.tooltipText = "{name} - {dateX}: {valueY}";
		series2.legendSettings.valueText = "{valueY}";
		series2.visible  = false;


		var series3 = chart.series.push(new am4charts.LineSeries());
		series3.dataFields.dateX = "date";
		series3.dataFields.valueY = "stock_amount_OUT";
		series3.name = "출고";
		series3.tooltipText = "{value}";
		series3.strokeWidth = 2;
		var s3Bullet = series3.bullets.push(new am4charts.CircleBullet());
		s3Bullet.circle.radius = 4;
		series3.tooltipText = "{name} - {dateX}: {valueY}";
		series3.legendSettings.valueText = "{valueY}";
		series3.visible  = false;

		var series4 = chart.series.push(new am4charts.LineSeries());
		series4.dataFields.dateX = "date";
		series4.dataFields.valueY = "stock_amount_INVOICE";
		series4.name = "송장";
		series4.tooltipText = "{value}";
		series4.strokeWidth = 2;
		var s4Bullet = series4.bullets.push(new am4charts.CircleBullet());
		s4Bullet.circle.radius = 4;
		series4.tooltipText = "{name} - {dateX}: {valueY}";
		series4.legendSettings.valueText = "{valueY}";
		series4.visible  = false;

		var series5 = chart.series.push(new am4charts.LineSeries());
		series5.dataFields.dateX = "date";
		series5.dataFields.valueY = "stock_amount_SHIPPED";
		series5.name = "배송";
		series5.tooltipText = "{value}";
		series5.strokeWidth = 2;
		var s5Bullet = series5.bullets.push(new am4charts.CircleBullet());
		s5Bullet.circle.radius = 4;
		series5.tooltipText = "{name} - {dateX}: {valueY}";
		series5.legendSettings.valueText = "{valueY}";
		series5.visible  = false;


		// Add chart cursor
		chart.cursor = new am4charts.XYCursor();
		chart.cursor.behavior = "zoomY";

		// Add legend
		chart.legend = new am4charts.Legend();

		chart.exporting.menu = new am4core.ExportMenu();

		// Add scrollbar
		chart.scrollbarX = new am4charts.XYChartScrollbar();
		chart.scrollbarX.series.push(series);
		chart.scrollbarX.parent = chart.bottomAxesContainer;

		chart.events.on("ready", function () {
			//categoryAxis.zoom({start:0.79, end:1});
		});

	};

	return {
		StockChartInit : StockChartInit,
	}
})();