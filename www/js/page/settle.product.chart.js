/*
 * 재고조회 관리 js
 */
var chartData = [];
var SettleProductChart = (function() {
	var root = this;

	var init = function () {
	};

	var SettleProductChartInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		$("#btn_searchBar").on("click", function(){
			SettleProductGetData();
		});
	};

	var SettleProductGetData = function(){

		var p_url = "./product_chart_pop_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_chart";
		dataObj.date_start = $("#period_preset_start_input").val();
		dataObj.date_end = $("#period_preset_end_input").val();

		if($("#format").val() == "month"){
			dataObj.mode = "get_chart_month";
			dataObj.date_start_year = $("#period_start_year_input").val();
			dataObj.date_start_month = $("#period_start_month_input").val();
			dataObj.date_end_year = $("#period_end_year_input").val();
			dataObj.date_end_month = $("#period_end_month_input").val();
		}

		dataObj.product_option_idx = $("#product_option_idx").val();
		dataObj.product_option_purchase_price = $("#product_option_purchase_price").val();
		dataObj.seller_idx = $("#seller_idx").val();

		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				if(response.result) {

					chartData = response.data;
					//chartData = [];
					//console.log(response.data);

					// $.each(response.data, function (i, o) {
					//
					// 	var sum_amount = (o.sum_amount == null) ? 0 : o.sum_amount;
					// 	var stock_amount_IN = (o.stock_amount_IN == null) ? 0 : o.stock_amount_IN;
					// 	var stock_amount_OUT = (o.stock_amount_OUT == null) ? 0 : o.stock_amount_OUT;
					// 	var stock_amount_INVOICE = (o.stock_amount_INVOICE == null) ? 0 : o.stock_amount_INVOICE;
					// 	var stock_amount_SHIPPED = (o.stock_amount_SHIPPED == null) ? 0 : o.stock_amount_SHIPPED;
					// 	chartData.push({
					// 		"date": o.dt,
					// 		"sum_amount": sum_amount,
					// 		"stock_amount_IN": stock_amount_IN,
					// 		"stock_amount_OUT": stock_amount_OUT,
					// 		"stock_amount_INVOICE": stock_amount_INVOICE,
					// 		"stock_amount_SHIPPED": stock_amount_SHIPPED
					// 	});
					// });

					DrawChart();
				}else{
					alert("검색된 데이터가 없습니다.");
				}
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			//tableLoader.off(wrap_sel);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			//tableLoader.off(wrap_sel);
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
		if($("#format").val() == "month"){
			chart.dateFormatter.dateFormat = "yyyy-MM";
		}

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
		if($("#format").val() == "month") {
			categoryAxis.dateFormatter = new am4core.DateFormatter();
			categoryAxis.dateFormatter.dateFormat = "yyyy-MM";
			categoryAxis.dateFormats.setKey("day", "yyyy-MM");
			categoryAxis.periodChangeDateFormats.setKey("day", "yyyy-MM");
		}

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.min = 0;
		valueAxis.strictMinMax = false;
		valueAxis.renderer.minGridDistance = 30;
		valueAxis.title.text = "Money";
		valueAxis.dashLength = 5;


		// var series = chart.series.push(new am4charts.LineSeries());
		// series.dataFields.dateX = "date";
		// series.dataFields.valueY = "sum_amount";
		// series.name = "재고";
		// series.tooltipText = "{value}";
		// series.strokeWidth = 2;
		// var s1Bullet = series.bullets.push(new am4charts.CircleBullet());
		// s1Bullet.circle.radius = 4;
		// series.tooltipText = "{name} - {dateX}: {valueY}";
		// series.legendSettings.valueText = "{valueY}";
		// series.visible  = false;
		// //series.tooltip.background.fill = am4core.color("#FFF");
		// // series.tooltip.getFillFromObject = false;
		// // series.tooltip.getStrokeFromObject = true;
		// // series.tooltip.background.strokeWidth = 3;
		// series.sequencedInterpolation = true;
		// series.fillOpacity = 0.6;
		// series.defaultState.transitionDuration = 1000;
		// series.stacked = true;
		// series.strokeWidth = 2;


		var tmp = chartData[0];

		$.each(tmp, function(i, o){
			console.log(i, o);
			if(i != 'date'){
				var series2 = chart.series.push(new am4charts.ColumnSeries());
				series2.dataFields.dateX = "date";
				series2.dataFields.valueY = i;
				series2.name = i;
				series2.tooltipText = "{value}";
				series2.strokeWidth = 2;
				series2.clustered = false;
				var s2Bullet = series2.bullets.push(new am4charts.CircleBullet());
				s2Bullet.circle.radius = 1;
				series2.tooltipText = "{name} - {dateX}: {valueY}";
				series2.legendSettings.valueText = "{valueY}";
				series2.visible  = false;
				if($("#format").val() == "month") {
					series2.dateFormatter.dateFormat = "yyyy-MM";
				}
			}
		});

		// var series2 = chart.series.push(new am4charts.ColumnSeries());
		// series2.dataFields.dateX = "date";
		// series2.dataFields.valueY = "stock_amount_IN";
		// series2.name = "입고";
		// series2.tooltipText = "{value}";
		// series2.strokeWidth = 2;
		// var s2Bullet = series2.bullets.push(new am4charts.CircleBullet());
		// s2Bullet.circle.radius = 4;
		// series2.tooltipText = "{name} - {dateX}: {valueY}";
		// series2.legendSettings.valueText = "{valueY}";
		// series2.visible  = false;


		// Add chart cursor
		chart.cursor = new am4charts.XYCursor();
		chart.cursor.behavior = "zoomX";

		chart.exporting.menu = new am4core.ExportMenu();

		// Add scrollbar
		chart.scrollbarX = new am4charts.XYChartScrollbar();
		//chart.scrollbarX.series.push(series);
		chart.scrollbarX.parent = chart.bottomAxesContainer;

		/* Create legend */
		chart.legend = new am4charts.Legend();
		chart.legend.width = 180;
		/* Create a separate container to put legend in */
		var legendContainer = am4core.create("legenddiv", am4core.Container);
		legendContainer.width = am4core.percent(100);
		legendContainer.height = am4core.percent(100);
		chart.legend.parent = legendContainer;


		chart.events.on("ready", function () {
			//categoryAxis.zoom({start:0.79, end:1});
		});

	};

	return {
		SettleProductChartInit : SettleProductChartInit,
	}
})();