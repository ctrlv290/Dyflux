/*
 * 정산통계 - 상품매출통계 관리 js
 */
var SettleChart = (function() {
	var root = this;

	var xlsDownIng = false;
	var xlsDownInterval;

	var init = function () {
		//공통 모달팝업 세팅
		$( "#modal_common" ).dialog({
			width: 600,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow(); $( "#modal_common" ).html(""); },
		});
	};

	/**
	 * 일별매출차트 페이지 초기화
	 * @constructor
	 */
	var ChartDailyInit = function(){

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_ALL_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
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
		title.fontSize = 14;
		title.marginBottom = 10;


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
		valueAxis.title.text = "Money(만원)";
		valueAxis.dashLength = 5;
		valueAxis.extraMax = 0.15;

		var series = chart.series.push(new am4charts.ColumnSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "val";
		series.dataFields.valueY2 = "val2";
		series.name = "일일 매출그래프";
		series.columns.template.tooltipText = "{categoryX}: [bold]{valueY2}[/]";
		series.columns.template.strokeOpacity = 0.8;

		// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
		series.columns.template.adapter.add("fill", function(fill, target) {
			return chart.colors.getIndex(target.dataItem.index);
		});

		var valueLabel = series.bullets.push(new am4charts.LabelBullet());
		valueLabel.label.text = "{valueY}";
		valueLabel.label.dy = -7;
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
		title.fontSize = 14;
		title.marginBottom = 10;


		chart.data = chartData2;

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
		valueAxis.title.text = "Qty";
		valueAxis.dashLength = 5;
		valueAxis.extraMax = 0.15;

		var series = chart.series.push(new am4charts.ColumnSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "val";
		series.name = "일일 매출그래프";
		series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
		series.columns.template.strokeOpacity = 0.8;

		// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
		series.columns.template.adapter.add("fill", function(fill, target) {
			return chart.colors.getIndex(target.dataItem.index);
		});

		var valueLabel = series.bullets.push(new am4charts.LabelBullet());
		valueLabel.label.text = "{valueY}";
		valueLabel.label.dy = -7;
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
		title.fontSize = 14;
		title.marginBottom = 10;


		chart.data = chartData3;

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
		valueAxis.title.text = "Qty";
		valueAxis.dashLength = 5;
		valueAxis.extraMax = 0.15;

		var series = chart.series.push(new am4charts.ColumnSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "val";
		series.name = "일일 매출그래프";
		series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
		series.columns.template.strokeOpacity = 0.8;

		// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
		series.columns.template.adapter.add("fill", function(fill, target) {
			return chart.colors.getIndex(target.dataItem.index);
		});

		var valueLabel = series.bullets.push(new am4charts.LabelBullet());
		valueLabel.label.text = "{valueY}";
		valueLabel.label.dy = -7;
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
		title.fontSize = 14;
		title.marginBottom = 10;


		chart.data = chartData4;

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
		valueAxis.title.text = "Qty";
		valueAxis.dashLength = 5;
		valueAxis.extraMax = 0.15;

		var series = chart.series.push(new am4charts.ColumnSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "val";
		series.name = "일일 매출그래프";
		series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/]";
		series.columns.template.strokeOpacity = 0.8;

		// as by default columns of the same series are of the same color, we add adapter which takes colors from chart.colors color set
		series.columns.template.adapter.add("fill", function(fill, target) {
			return chart.colors.getIndex(target.dataItem.index);
		});

		var valueLabel = series.bullets.push(new am4charts.LabelBullet());
		valueLabel.label.text = "{valueY}";
		valueLabel.label.dy = -7;
		valueLabel.label.hideOversized = false;
		valueLabel.label.truncate = false;


		chart.exporting.menu = new am4core.ExportMenu();
	};

	/**
	 * 매출캘린더 페이지 초기화
	 * @constructor
	 */
	var ChartCalendarInit = function(){

		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
		$(".supplier_idx").SumoSelect({
			placeholder: '전체 공급처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '공급처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_ALL_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});
	};


	/**
	 * 취소통계 페이지 초기화
	 * @constructor
	 */
	var CancelStatInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "8");

		//카테고리 바인딩
		Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");

		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
		$(".supplier_idx").SumoSelect({
			placeholder: '전체 공급처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '공급처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_ALL_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		$(".btn-xls-down").on("click", function(){
			CancelStatXlsDown();
		});

		//jqGrid 초기화
		CancelStatGridInit();
	};

	/**
	 * 취소통계 Grid 초기화
	 * @constructor
	 */
	var CancelStatGridInit = function(){

		$("#grid_list").jqGrid({
			url: './cancel_statistics_grid.php',
			mtype: "GET",
			postData:{
				param: $("#searchForm").serialize()
			},
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
				{ label: '상품코드', name: 'product_idx', index: 'A.product_idx', width: 100, sortable: false, hidden: true, cellattr:jsFormatterComparePrimaryKey},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: true, hidden: false, cellattr:jsFormatterCompareAndSetRowSpan},
				{ label: '옵션코드', name: 'product_option_idx', index: 'A.product_option_idx', width: 100, sortable: true},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: true, hidden: false, align: 'left'},
				{ label: '발주수량', name: 'sum_product_option_cnt', index: 'sum_product_option_cnt', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '취소수량', name: 'cancel_cnt', index: 'cancel_cnt', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '배송전취소', name: 'product_cancel_shipped_N', index: 'product_cancel_shipped_N', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '배송후취소', name: 'product_cancel_shipped_Y', index: 'product_cancel_shipped_Y', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '반품-취소(환불)', name: 'RETURN_REFUND', index: 'RETURN_REFUND', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '반품-불량', name: 'RETURN_POOR', index: 'RETURN_POOR', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '반품-오배송', name: 'RETURN_DELIVERY_ERR', index: 'RETURN_DELIVERY_ERR', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '반품-고객변심', name: 'RETURN_CUSTOMER_MIND', index: 'RETURN_CUSTOMER_MIND', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '취소-분실', name: 'CANCEL_LOSS', index: 'CANCEL_LOSS', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '취소-상품품절', name: 'CANCEL_SOLDOUT', index: 'CANCEL_SOLDOUT', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '취소-배송지연', name: 'CANCEL_DELIVERY_DELAY', index: 'CANCEL_DELIVERY_DELAY', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '기타', name: 'ETC', index: 'ETC', width: 80, sortable: true, align: 'right', formatter: 'integer'},
			],
			rowNum: 1000,
			rowList: [],
			pager: '#grid_pager',
			sortname: 'A.product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");
				var grid = this;
				$('td[name="cellRowspan"]', grid).each(function() {
					var spans = $('td[rowspanid="'+this.id+'"]',grid).length+1;
					if(spans>1){
						$(this).attr('rowspan',spans).addClass("bg-force-white");
					}
				});

				$("td[data-is-key='1']").parent().find("td").addClass("bold_top_line");
			},
			beforeRequest: function(){
				chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크
			}
		});


		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				CancelStatSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			CancelStatSearch();
		});
	};

	/**
	 * jqGrid 셀 병합을 위한 임시 저장변수
	 * @type {{chkval: undefined, rowNo: number, cellId: undefined}}
	 */
	var chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크

	/**
	 * 목록 셀병합 함수 1
	 * jqGrid 셀 병합을 위한 함수 (Key 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterComparePrimaryKey = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		var cellId = this.id + '_row_'+rowObject.product_idx+'-'+cm.name;
		if(chkcell.chkval != rowObject.product_idx && rowid != chkcell.rowNo){ //check 값이랑 비교값이 다른 경우
			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan" data-is-key="1"';
			// alert(result);
			chkcell = {cellId:cellId, chkval:rowObject.product_idx, rowNo: rowid};
		}else{
			result = 'style="display: none;"  rowspanid="'+cellId+'"'; //같을 경우 display none 처리
			// alert(result);
		}
		return result;
	};
	/**
	 *  목록 셀병합 함수 2
	 * jqGrid 셀병합을 위한 함수 (일반 Cell 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterCompareAndSetRowSpan = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		//console.log(cm);

		var cellId = this.id + '_row_'+rowObject.product_idx+'-'+cm.name;
		if(chkcell.chkval == rowObject.product_idx && rowid == chkcell.rowNo){ //check 값이랑 비교값이 다른 경우
			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan"';
		}else{
			result = 'style="display: none;" rowspanid="'+cellId+'"'; //같을 경우 display none 처리
		}
		return result;
	};

	/**
	 * 취소통계 페이지 Grid 목록/검색
	 * @constructor
	 */
	var CancelStatSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 충전금관리 엑셀 다운로드
	 * @constructor
	 */
	var CancelStatXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "cancel_statistics_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_CANCEL_STATISTICS", function(){
				SettleChart.CancelStatXlsDownComplete();
			});
		}, 500);
	};

	var CancelStatXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	return {
		init: init,
		ChartDailyInit: ChartDailyInit,
		ChartCalendarInit: ChartCalendarInit,
		CancelStatInit: CancelStatInit,
		CancelStatXlsDownComplete: CancelStatXlsDownComplete,
	}
})();
$(function(){
	SettleChart.init();
});