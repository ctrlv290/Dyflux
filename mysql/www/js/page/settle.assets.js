/*
 * 정산통계 - 원장 관련 js
 */
var SettleAssets = (function() {
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
	 * 자산형황 페이지 초기화
	 * @constructor
	 */
	var AssetsStateInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "9");

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

		//카테고리 바인딩
		Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");

		AssetsStateGridInit();

		//다운로드 버튼
		$(".btn-xls-down").on("click", function(){
			AssetsStateXlsDown();
		});

	};

	/**
	 * 자산현황 Grid 초기화
	 * @constructor
	 */
	var AssetsStateGridInit = function(){

		//Grid 초기화
		$("#grid_list").jqGrid({
			url: '/settle/assets_state_grid.php',
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
				{ label: 'product_idx', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
				{ label: '공급처코드', name: 'supplier_idx', index: 'supplier_idx', width: 80, hidden: false},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 80, hidden: false},
				{ label: '옵션코드', name: 'product_option_idx', index: 'STOCK.product_option_idx', width: 80, hidden: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, hidden: false, align: 'left'},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, hidden: false, align: 'left'},
				{ label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 80, hidden: false, align: 'right', formatter: 'integer'},
				{ label: '재고수량', name: 'stock_assets_amount', index: 'stock_assets_amount', width: 80, sortable: false, hidden: false, align: 'right', formatter: 'integer'},
				{ label: '정상', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, hidden: false, align: 'right', formatter: 'integer'},
				{ label: '보류', name: 'stock_amount_HOLD', index: 'stock_amount_HOLD', width: 80, hidden: false, align: 'right', formatter: 'integer'},
				{ label: '양품', name: 'stock_amount_ABNORMAL', index: 'stock_amount_ABNORMAL', width: 80, hidden: false, align: 'right', formatter: 'integer'},
				{ label: '불량', name: 'stock_amount_BAD', index: 'stock_amount_BAD', width: 80, hidden: false, align: 'right', formatter: 'integer'},
				{ label: '일반폐기', name: 'stock_amount_DISPOSAL', index: 'stock_amount_DISPOSAL', width: 80, hidden: false, align: 'right', formatter: 'integer'},
				{ label: '자산금액', name: 'stock_assets_price', index: 'stock_assets_price', width: 80, sortable: false, hidden: false, align: 'right', formatter: 'integer'},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'STOCK.product_option_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				if(typeof userData != "undefined") {
					var stock_amount_NORMAL = userData.stock_amount_NORMAL;
					var stock_amount_ABNORMAL = userData.stock_amount_ABNORMAL;
					var stock_amount_BAD = userData.stock_amount_BAD;
					var stock_amount_HOLD = userData.stock_amount_HOLD;
					var stock_amount_DISPOSAL = userData.stock_amount_DISPOSAL;
					var stock_assets_amount = userData.stock_assets_amount;
					var stock_assets_price = userData.stock_assets_price;

					$(".stock_amount_NORMAL").text(Common.addCommas(stock_amount_NORMAL));
					$(".stock_amount_ABNORMAL").text(Common.addCommas(stock_amount_ABNORMAL));
					$(".stock_amount_BAD").text(Common.addCommas(stock_amount_BAD));
					$(".stock_amount_HOLD").text(Common.addCommas(stock_amount_HOLD));
					$(".stock_amount_DISPOSAL").text(Common.addCommas(stock_amount_DISPOSAL));
					$(".stock_assets_amount").text(Common.addCommas(stock_assets_amount));
					$(".stock_assets_price").text(Common.addCommas(stock_assets_price));
				}
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
				AssetsStateGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			AssetsStateGridSearch();
		});

	};

	/**
	 * 자산현황 목록/검색
	 * @constructor
	 */
	var AssetsStateGridSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};


	/**
	 * 자산현황 엑셀 다운로드
	 * @constructor
	 */
	var AssetsStateXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "assets_state_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_ASSETS_STATE", function(){
				SettleAssets.AssetsStateXlsDownComplete();
			});
		}, 500);
	};

	var AssetsStateXlsDownComplete = function(){
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
		AssetsStateInit : AssetsStateInit,
		AssetsStateXlsDownComplete: AssetsStateXlsDownComplete
	}
})();
$(function(){
	SettleAssets.init();
});