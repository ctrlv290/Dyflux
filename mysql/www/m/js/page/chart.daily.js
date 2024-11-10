/*
 * 모바일 - 일별매출차트 관련 js
 */
var ChartDaily = (function() {
	var root = this;

	var init = function(){

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");

		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");

		//폼 전송 방지
		$("#dyForm").on("submit", function(e){

		});

		$("#btn-search").on("click", function(){
			$("#dyForm").submit();
		});

		ChartDailyDrawSale();
		ChartDailyDrawProductCnt();
		ChartDailyDrawOrderCnt();
		ChartDailyDrawInvoiceCnt();

	};
	/**
	 * 일별매출차트 매출 그래프 초기화
	 * @constructor
	 */
	var ChartDailyDrawSale = function(){
		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		var chart = am4core.create("chartdiv", am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";

		// Add chart title
		var title = chart.titles.create();
		title.text = "일일 매출그래프(금액)";
		title.fontSize = 12;
		title.marginBottom = 10;


		chart.data = chartData;

		var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.renderer.grid.template.location = 0;
		categoryAxis.dataFields.category = "date";
		categoryAxis.renderer.grid.template.disabled = true;
		categoryAxis.renderer.minGridDistance = 10;
		categoryAxis.dashLength = 5;
		categoryAxis.fontSize = 12;
		//categoryAxis.renderer.labels.template.rotation = 45;

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.min = 0;
		valueAxis.strictMinMax = false;
		valueAxis.renderer.minGridDistance = 30;
		valueAxis.title.text = "Money(만원)";
		valueAxis.title.fontSize = 12;
		valueAxis.fontSize = 12;
		valueAxis.dashLength = 5;
		valueAxis.extraMax = 0.15;

		var series = chart.series.push(new am4charts.ColumnSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "val";
		series.name = "일일 매출그래프";
		series.columns.template.tooltipText = "[font-size: 12px;]{categoryX}: [/][font-size: 12px; bold]{valueY}[/]";
		series.columns.template.strokeOpacity = 0.8;
		series.columns.template.fontSize = 12;

		// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
		series.columns.template.adapter.add("fill", function(fill, target) {
			return chart.colors.getIndex(target.dataItem.index);
		});

		var valueLabel = series.bullets.push(new am4charts.LabelBullet());
		valueLabel.label.text = "{valueY}";
		valueLabel.label.dy = -7;
		valueLabel.label.fontSize = 12;
		valueLabel.label.hideOversized = false;
		valueLabel.label.truncate = false;


		chart.exporting.menu = new am4core.ExportMenu();
	};

	/**
	 * 일별매출차트 수량 그래프
	 * @constructor
	 */
	var ChartDailyDrawProductCnt = function(){
		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		var chart = am4core.create("chartdiv2", am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";

		// Add chart title
		var title = chart.titles.create();
		title.text = "일일 매출그래프(수량)";
		title.fontSize = 12;
		title.marginBottom = 10;


		chart.data = chartData2;

		var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.renderer.grid.template.location = 0;
		categoryAxis.dataFields.category = "date";
		categoryAxis.renderer.grid.template.disabled = true;
		categoryAxis.renderer.minGridDistance = 10;
		categoryAxis.dashLength = 5;
		categoryAxis.fontSize = 12;
		//categoryAxis.renderer.labels.template.rotation = 45;

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.min = 0;
		valueAxis.strictMinMax = false;
		valueAxis.renderer.minGridDistance = 30;
		valueAxis.title.text = "Qty";
		valueAxis.title.fontSize = 12;
		valueAxis.fontSize = 12;
		valueAxis.dashLength = 5;
		valueAxis.extraMax = 0.15;

		var series = chart.series.push(new am4charts.ColumnSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "val";
		series.name = "일일 매출그래프";
		series.columns.template.tooltipText = "[font-size: 12px;]{categoryX}: [/][font-size: 12px; bold]{valueY}[/]";
		series.columns.template.strokeOpacity = 0.8;
		series.columns.template.fontSize = 12;

		// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
		series.columns.template.adapter.add("fill", function(fill, target) {
			return chart.colors.getIndex(target.dataItem.index);
		});

		var valueLabel = series.bullets.push(new am4charts.LabelBullet());
		valueLabel.label.text = "{valueY}";
		valueLabel.label.dy = -7;
		valueLabel.label.fontSize = 12;
		valueLabel.label.hideOversized = true;
		valueLabel.label.truncate = false;


		chart.exporting.menu = new am4core.ExportMenu();
	};

	/**
	 * 일별매출차트 주문 그래프
	 * @constructor
	 */
	var ChartDailyDrawOrderCnt = function(){
		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		var chart = am4core.create("chartdiv3", am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";

		// Add chart title
		var title = chart.titles.create();
		title.text = "일일 매출그래프(주문)";
		title.fontSize = 12;
		title.marginBottom = 10;


		chart.data = chartData3;

		var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.renderer.grid.template.location = 0;
		categoryAxis.dataFields.category = "date";
		categoryAxis.renderer.grid.template.disabled = true;
		categoryAxis.renderer.minGridDistance = 10;
		categoryAxis.dashLength = 5;
		categoryAxis.fontSize = 12;
		//categoryAxis.renderer.labels.template.rotation = 45;

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.min = 0;
		valueAxis.strictMinMax = false;
		valueAxis.renderer.minGridDistance = 30;
		valueAxis.title.text = "Qty";
		valueAxis.title.fontSize = 12;
		valueAxis.fontSize = 12;
		valueAxis.dashLength = 5;
		valueAxis.extraMax = 0.15;

		var series = chart.series.push(new am4charts.ColumnSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "val";
		series.name = "일일 매출그래프";
		series.columns.template.tooltipText = "[font-size: 12px;]{categoryX}: [/][font-size: 12px; bold]{valueY}[/]";
		series.columns.template.strokeOpacity = 0.8;
		series.columns.template.fontSize = 12;

		// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
		series.columns.template.adapter.add("fill", function(fill, target) {
			return chart.colors.getIndex(target.dataItem.index);
		});

		var valueLabel = series.bullets.push(new am4charts.LabelBullet());
		valueLabel.label.text = "{valueY}";
		valueLabel.label.dy = -7;
		valueLabel.label.fontSize = 12;
		valueLabel.label.hideOversized = false;
		valueLabel.label.truncate = false;


		chart.exporting.menu = new am4core.ExportMenu();
	};

	/**
	 * 일별매출차트 송장 그래프
	 * @constructor
	 */
	var ChartDailyDrawInvoiceCnt = function(){
		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		var chart = am4core.create("chartdiv4", am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";

		// Add chart title
		var title = chart.titles.create();
		title.text = "일일 매출그래프(송장)";
		title.fontSize = 12;
		title.marginBottom = 10;


		chart.data = chartData4;

		var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.renderer.grid.template.location = 0;
		categoryAxis.dataFields.category = "date";
		categoryAxis.renderer.grid.template.disabled = true;
		categoryAxis.renderer.minGridDistance = 10;
		categoryAxis.dashLength = 5;
		categoryAxis.fontSize = 12;
		//categoryAxis.renderer.labels.template.rotation = 45;

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.min = 0;
		valueAxis.strictMinMax = false;
		valueAxis.renderer.minGridDistance = 30;
		valueAxis.title.text = "Qty";
		valueAxis.title.fontSize = 12;
		valueAxis.fontSize = 12;
		valueAxis.dashLength = 5;
		valueAxis.extraMax = 0.15;

		var series = chart.series.push(new am4charts.ColumnSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "val";
		series.name = "일일 매출그래프";
		series.columns.template.tooltipText = "[font-size: 12px;]{categoryX}: [/][font-size: 12px; bold]{valueY}[/]";
		series.columns.template.strokeOpacity = 0.8;
		series.columns.template.fontSize = 12;

		// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
		series.columns.template.adapter.add("fill", function(fill, target) {
			return chart.colors.getIndex(target.dataItem.index);
		});

		var valueLabel = series.bullets.push(new am4charts.LabelBullet());
		valueLabel.label.text = "{valueY}";
		valueLabel.label.dy = -7;
		valueLabel.label.fontSize = 12;
		valueLabel.label.hideOversized = false;
		valueLabel.label.truncate = false;


		chart.exporting.menu = new am4core.ExportMenu();
	};

	return {
		init: init,
	}
})();

$(function(){
	ChartDaily.init();
});
