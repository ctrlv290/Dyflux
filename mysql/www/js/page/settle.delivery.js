/*
 * 정산통계 - 원장 관련 js
 */
var SettleDelivery = (function() {
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
	 * 미출고요약표 페이지 초기화
	 * @constructor
	 */
	var UnDeliveryListInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "9");

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("VENDOR_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

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

		//다운로드 버튼
		$(".btn-xls-down").on("click", function(){
			UnDeliveryListXlsDown();
		});

		UnDeliveryListGridInit();
	};

	/**
	 * 미출고요약표 Grid 초기화
	 * @constructor
	 */
	var UnDeliveryListGridInit = function(){

		var _colModel = [
			{ label: 'product_idx', name: 'product_idx', index: 'Z.product_idx', width: 0, hidden: true},
			{ label: '옵션코드', name: 'product_option_idx', index: 'Z.product_option_idx', width: 80, hidden: false},
			{ label: '상품명', name: 'product_name', index: 'PP.product_name', width: 150, hidden: false, align: 'left'},
			{ label: '공급처 상품명', name: 'product_supplier_name', index: 'PP.product_supplier_name', width: 150, hidden: false, align: 'left'},
			{ label: '옵션', name: 'product_option_name', index: 'OO.product_option_name', width: 150, hidden: false, align: 'left'},
			{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 120, hidden: false, align: 'left'},
			{ label: '수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, hidden: false, align: 'right'},
			{ label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 80, hidden: false, align: 'right', formatter: 'integer'},
			{ label: '정상재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, hidden: false, align: 'right', formatter: 'integer'},
			{ label: '불량재고', name: 'stock_amount_BAD', index: 'stock_amount_BAD', width: 80, hidden: false, align: 'right', formatter: 'integer'},
		];

		if(!isDYLogin) {
			shrinkToFit = true;
			var _colModelVendor = new Array();
			$.each(_colModel, function (i, o) {
				if(o.name != "product_supplier_name" && o.name != "supplier_name" && o.name != "stock_unit_price")
				{
					_colModelVendor.push(o);
				}
			});

			_colModel = _colModelVendor;
		}

		//Grid 초기화
		$("#grid_list").jqGrid({
			url: '/settle/undelivery_list_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchFormPop").serialize()
			},
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: _colModel,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'Z.product_option_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

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
				UnDeliveryListGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			UnDeliveryListGridSearch();
		});

	};

	/**
	 * 미출고요약표 목록/검색
	 * @constructor
	 */
	var UnDeliveryListGridSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 미출고요약표 엑셀 다운로드
	 * @constructor
	 */
	var UnDeliveryListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "undelivery_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_UNDELIVERY_LIST", function(){
				SettleDelivery.UnDeliveryListXlsDownComplete();
			});
		}, 500);
	};

	var UnDeliveryListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};


	/**
	 * 배송통계 페이지 초기화
	 * @constructor
	 */
	var DeliveryStatisticsInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "9");

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("VENDOR_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

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

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});

		//검색 버튼 클릭 이벤트
		$(".btn-xls-down").on("click", function(){
			DeliveryStatisticsXlsDown();
		});

	};

	/**
	 * 배송통계 목록 엑셀 다운로드
	 * @constructor
	 */
	var DeliveryStatisticsXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "delivery_statistics_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("DELIVERY_STATISTICS", function(){
				SettleDelivery.DeliveryStatisticsXlsDownComplete();
			});
		}, 500);
	};

	var DeliveryStatisticsXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};


	/**
	 * 송장번호이력조회 페이지 초기화
	 * @constructor
	 */
	var InvoiceHistoryInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "9");

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("VENDOR_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

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

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});

		//검색 버튼 클릭 이벤트
		$(".btn-xls-down").on("click", function(){
			InvoiceHistoryXlsDown();
		});

	};

	/**
	 * 송장번호이력조회 목록 엑셀 다운로드
	 * @constructor
	 */
	var InvoiceHistoryXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "invoice_history_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_INVOICE_HISTORY", function(){
				SettleDelivery.InvoiceHistoryXlsDownComplete();
			});
		}, 500);
	};

	var InvoiceHistoryXlsDownComplete = function(){
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
		UnDeliveryListInit : UnDeliveryListInit,
		DeliveryStatisticsInit: DeliveryStatisticsInit,
		InvoiceHistoryInit: InvoiceHistoryInit,
		UnDeliveryListXlsDownComplete: UnDeliveryListXlsDownComplete,
		DeliveryStatisticsXlsDownComplete: DeliveryStatisticsXlsDownComplete,
		InvoiceHistoryXlsDownComplete: InvoiceHistoryXlsDownComplete,
	}
})();
$(function(){
	SettleDelivery.init();
});