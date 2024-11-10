/*
 * 정산통계 - 매출이익 관련 js
 */
var SettleProfit = (function() {
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

		//창 닫기 버튼 바인딩
		$("body").on("click", ".btn-common-pop-close", function(){
			PopupCommonPopClose();
		});
	};

	/**
	 * 기간별 매출이익 페이지 초기화
	 * @constructor
	 */
	var SalesProfitPeriodInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "9");

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

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});

		//다운로드 버튼 클릭 이벤트
		$(".btn-xls-down").on("click", function(){
			SalesProfitPeriodXlsDown();
		});

		$('table.floatThead').floatThead({
			position: 'fixed',
			top: 50,
			zIndex: 900
		});

	};

	/**
	 * 기간별 매출이익 엑셀 다운로드
	 * @constructor
	 */
	var SalesProfitPeriodXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "sales_profit_period_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_SALE_PROFIT_PERIOD", function(){
				SettleProfit.SalesProfitPeriodXlsDownComplete();
			});
		}, 500);
	};

	var SalesProfitPeriodXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};


	/**
	 * 상품별 매출이익 페이지 초기화
	 * @constructor
	 */
	var SalesProfitProductInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "3");

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

		//다운로드 버튼 클릭 이벤트
		$(".btn-xls-down").on("click", function(){
			SalesProfitProductXlsDown();
		});

		$('table.floatThead').floatThead({
			position: 'fixed',
			top: 50,
			zIndex: 900
		});

	};

	/**
	 * 상품별 매출이익 엑셀 다운로드
	 * @constructor
	 */
	var SalesProfitProductXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "sales_profit_product_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_SALE_PROFIT_PRODUCT", function(){
				SettleProfit.SalesProfitProductXlsDownComplete();
			});
		}, 500);
	};

	var SalesProfitProductXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};



	/**
	 * 팝업 페이지 - 공통 모달 팝업 Open
	 * @param width
	 * @param height
	 * @param title
	 * @param html
	 * @constructor
	 */
	var PopupCommonPopOpen = function(width, height, title, html){
		var _width = (width === 0) ? 600 : width;
		var _height = (height === 0) ? "" : height;

		$("#modal_common").dialog( "option", "width", width);
		if(_height != "") {
			$("#modal_common").dialog("option", "height", height);
		}
		$("#modal_common").dialog("option", "title", title);
		$("#modal_common").html(html);
		$("#modal_common").dialog( "open" );
	};

	/**
	 * 팝업 페이지 - 공통 모달 팝업 Close
	 * @constructor
	 */
	var PopupCommonPopClose = function() {
		$("#modal_common").html("");
		$("#modal_common").dialog( "close" );
	};

	return {
		init: init,
		SalesProfitPeriodInit : SalesProfitPeriodInit,
		SalesProfitProductInit: SalesProfitProductInit,
		SalesProfitPeriodXlsDownComplete: SalesProfitPeriodXlsDownComplete,
		SalesProfitProductXlsDownComplete: SalesProfitProductXlsDownComplete,
	}
})();
$(function(){
	SettleProfit.init();
});