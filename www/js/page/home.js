/*
 * 홈화면 js
 */
var Home = (function() {
	var root = this;

	var init = function() {
	};

	var HomeInit = function(){
		$(window).on("resize", function() {
			var winH = $(window).height();
			var mainWrapTop = $(".main_wrap").eq(0).offset().top + 50;
			$(".main_wrap").height(winH - mainWrapTop);
		}).trigger("resize");

		//tableLoader.on("li.today");
		$(".btn-today").on("click", function(){
			HomeToday();
		});

		$(".btn-delay").on("click", function(){
			HomeDelay();
		});

		$(".btn-stock").on("click", function(){
			HomeStock();
		});

		$(".btn-yet").on("click", function(){
			HomeYet();
		});

		$(".btn-return").on("click", function(){
			HomeReturn();
		});

		$(".btn-vendor").on("click", function(){
			HomeVendor();
		});

		$(".btn-lastest").on("click", function(){
			HomeLastest();
		}).trigger("click");

		$(".tab_menu li a").on("click", function(){
			$(".tab_menu li a").removeClass("on");
			$(this).addClass("on");
			$(".btn-lastest").trigger("click");
		});

		//홈배너 초기화
		$('.home_slider').slick({
			dots: true,
			arrows: true,
			autoplay:true,
			infinite: true,
			speed: 800
		});

		//홈배너 X 버튼 바인딩
		$(".btn-home-banner-close").on("click", function(){
			$(".home_banner").removeClass("show");
		});

		//쿠키 체크 후 배너 Show
		if($.cookie("home_banner") != "Y" && banner_cnt > 0){
			$(".home_banner").addClass("show");
		}

		//오늘 하루 닫기 버튼 바인딩
		$(".btn-home-banner-hide-today").on("click", function(){
			$.cookie("home_banner", "Y", {expires: todayExpires});
			$(".btn-home-banner-close").trigger("click");
		});
	};

	var HomeToday = function(){
		var wrap_sel = "li.today";
		$wrap = $(wrap_sel);
		tableLoader.on(wrap_sel);

		var p_url = "/home/today.php";
		var dataObj = new Object();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: {}
		}).done(function (response) {
			if(response)
			{
				$wrap.find("table.grid").remove();
				$wrap.find(".main_box_header").after(response);
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(wrap_sel);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(wrap_sel);
		});
	};

	var HomeDelay = function(){
		var wrap_sel = "li.delay";
		$wrap = $(wrap_sel);
		tableLoader.on(wrap_sel);

		var p_url = "/home/delay.php";
		var dataObj = new Object();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: {}
		}).done(function (response) {
			if(response)
			{
				$wrap.find("table.grid").remove();
				$wrap.find(".main_box_header").after(response);
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(wrap_sel);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(wrap_sel);
		});
	};

	var HomeStock = function(){
		var wrap_sel = "li.stock";
		$wrap = $(wrap_sel);
		tableLoader.on(wrap_sel);

		var p_url = "/home/stock.php";
		var dataObj = new Object();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: {}
		}).done(function (response) {
			if(response)
			{
				$wrap.find("table.grid").remove();
				$wrap.find(".main_box_header").after(response);
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(wrap_sel);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(wrap_sel);
		});
	};

	var HomeYet = function(){
		var wrap_sel = "li.yet";
		$wrap = $(wrap_sel);
		tableLoader.on(wrap_sel);

		var p_url = "/home/yet.php";
		var dataObj = new Object();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: {}
		}).done(function (response) {
			if(response)
			{
				$wrap.find("table.grid").remove();
				$wrap.find(".main_box_header").after(response);
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(wrap_sel);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(wrap_sel);
		});
	};

	var HomeReturn = function(){
		var wrap_sel = "li.return";
		$wrap = $(wrap_sel);
		tableLoader.on(wrap_sel);

		var p_url = "/home/return.php";
		var dataObj = new Object();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: {}
		}).done(function (response) {
			if(response)
			{
				$wrap.find("table.grid").remove();
				$wrap.find(".main_box_header").after(response);
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(wrap_sel);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(wrap_sel);
		});
	};

	var HomeVendor = function(){
		var wrap_sel = "li.vendor";
		$wrap = $(wrap_sel);
		tableLoader.on(wrap_sel);

		var p_url = "/home/vendor_charge.php";
		var dataObj = new Object();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: {}
		}).done(function (response) {
			if(response)
			{
				$wrap.find("table.grid").remove();
				$wrap.find(".main_box_header").after(response);
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(wrap_sel);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(wrap_sel);
		});
	};



	var chartData = [];
	var HomeLastest = function(){

		var wrap_sel = "li.lastest";

		if($(wrap_sel).length == 0) return;

		$wrap = $(wrap_sel);
		tableLoader.on(wrap_sel);

		var p_url = "/home/lastest.php";
		var dataObj = new Object();
		dataObj.mode = $(".tab_menu li a.on").data("type");
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				chartData = [];
				//console.log(response.data);
				$.each(response.data, function(i, o){
					chartData.unshift({"date": o.dt, "val": o.val});
				});

				//console.log(chartData);
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

	/**
	 * 최근현황  그래프 초기화
	 * @constructor
	 */
	var DrawChart = function(){
		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		var chart = am4core.create("chartdiv", am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";


		chart.data = chartData;

		var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.renderer.grid.template.location = 0;
		categoryAxis.dataFields.category = "date";
		categoryAxis.renderer.grid.template.disabled = true;
		categoryAxis.renderer.minGridDistance = 10;
		categoryAxis.dashLength = 5;
		//categoryAxis.renderer.labels.template.rotation = 45;

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.min = 0;
		valueAxis.strictMinMax = false;
		valueAxis.renderer.minGridDistance = 30;
		valueAxis.dashLength = 5;

		var series = chart.series.push(new am4charts.LineSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "val";
		series.name = "정산";
		series.tooltipText = "{value}";
		series.strokeWidth = 2;
		var circleBullet = series.bullets.push(new am4charts.CircleBullet());
		circleBullet.circle.radius = 4;
		series.tooltipText = "{categoryX}: {valueY}";
		series.legendSettings.valueText = "{valueY}";
		series.visible  = false;

		// Add chart cursor
		chart.cursor = new am4charts.XYCursor();
		chart.cursor.behavior = "zoomY";
	};

	/***
	 * 테이블 로더
	 * @type {{off: off, on: on}}
	 */
	var tableLoader = {
		on : function(sel){
			//var loader = '<div class="table-loading-wrap"><div class="table-loading"><div></div><div></div><div></div></div></div>';
			var loader = '<div class="table-loading-wrap"><div class="sk-fading-circle"><div class="sk-circle1 sk-circle"></div><div class="sk-circle2 sk-circle"></div><div class="sk-circle3 sk-circle"></div><div class="sk-circle4 sk-circle"></div><div class="sk-circle5 sk-circle"></div><div class="sk-circle6 sk-circle"></div><div class="sk-circle7 sk-circle"></div><div class="sk-circle8 sk-circle"></div><div class="sk-circle9 sk-circle"></div><div class="sk-circle10 sk-circle"></div><div class="sk-circle11 sk-circle"></div><div class="sk-circle12 sk-circle"></div></div></div>';
			$(sel).append(loader);
		},
		off : function(sel){
			$(sel).find(".table-loading-wrap").remove();
		}
	};
	return {
		HomeInit: HomeInit
	}
})();