/*
 * 정산통계 - 원장 관련 js
 */
var SettleCharge = (function() {
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
	 * 충전금관리 페이지 초기화
	 * @constructor
	 */
	var VendorChargeInit = function(){
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

		//충전금등록 버튼
		$(".btn-vendor-charge-add-pop").on("click", function(){
			Common.newWinPopup("vendor_charge_pop.php?mode=add", 'vendor_charge_pop', 800, 500, 'yes');
		});

		//다운로드 버튼
		$(".btn-xls-down").on("click", function(){
			VendorChargeXlsDown();
		});

		VendorChargeGridInit();
	};

	/**
	 * 충전금관리 Grid 초기화
	 * @constructor
	 */
	var VendorChargeGridInit = function(){

		//Grid 초기화
		$("#grid_list").jqGrid({
			url: '/settle/vendor_charge_grid.php',
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
				{ label: 'charge_idx', name: 'charge_idx', index: 'charge_idx', width: 0, hidden: true},
				{ label: '벤더사', name: 'vendor_name', index: 'vendor_name', width: 150, hidden: false, formatter: function(cellvalue, options, rowobject){
						return '<a href="javascript:;" class="link btn-vendor-charge-history" data-idx="'+rowobject.member_idx+'">'+cellvalue+'</a>';
					}},
				{ label: '벤더사 코드', name: 'member_idx', index: 'member_idx', width: 100, hidden: false},
				{ label: '벤더사 등급', name: 'vendor_grade', index: 'vendor_grade', width: 100, hidden: false},
				{ label: '마지막 입금일', name: 'last_charge_date', index: 'last_charge_date', width: 120, sortable: false},
				{ label: '충전금 잔액', name: 'remain_amount2', index: 'remain_amount2', width: 100, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return '<a href="/settle/ledger_sale.php?seller_idx='+rowobject.member_idx+'" class="link" data-idx="'+rowobject.member_idx+'">'+Common.addCommas(cellvalue)+'</a>';
					}},
				{ label: '마지막 입금 비고', name: 'last_memo', index: 'last_memo', width: 150, sortable: false},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'last_charge_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//상품 선택 버튼 바인딩
				$(".btn-vendor-charge-history").on("click", function(){
					var idx = $(this).data("idx");

					Common.newWinPopup("vendor_charge_history_pop.php?member_idx="+idx, 'vendor_charge_history_pop', 800, 750, 'yes');
				});
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
				VendorChargeGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			VendorChargeGridSearch();
		});

	};

	/**
	 * 충전금관리 목록/검색
	 * @constructor
	 */
	var VendorChargeGridSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 충전금관리 목록 Reload
	 * @constructor
	 */
	var VendorChargeGridReload = function(){
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
	var VendorChargeXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "vendor_charge_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_VENDOR_CHARGE", function(){
				SettleCharge.VendorChargeXlsDownComplete();
			});
		}, 500);
	};

	var VendorChargeXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 충전금 등록 팝업 페이지 초기화
	 * @constructor
	 */
	var VendorChargePopInit = function(){
		$(".money").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 0, autoGroup: true, rightAlign: true});

		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		$("#btn-save-pop").on("click", function(){
			$("#dyFormChargePop").submit();
		});

		$("#dyFormChargePop").on("submit", function(e){
			if(!confirm('저장 하시겠습니까?')){
				return false;
			}
		});

		// 일자 일괄 선택
		$("#dp").datepicker({
			onSelect: function(dateText, inst) {
					var cnt = $("input[name='charge_date[]']").length;
					for (var i = 0; i < cnt; i++) {
						$("input[name='charge_date[]']").eq(i).val(dateText);
					}
			},
			beforeShow: function () {
				setTimeout(function(){
					$('.ui-datepicker').css('z-index', 99999999999999);
				}, 0);
			}
		});
		$(".allDate").click(function() {
			$("#dp").datepicker("show");
		});
	};

	/**
	 * 충전금 내역 팝업 페이지 초기화
	 * @constructor
	 */
	var VendorChargeHistoryPopInit = function(){
		$("#grid_list").jqGrid({
			url: './vendor_charge_history_pop_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize(),
			},
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel:  [
				{ label: 'charge_idx', name: 'charge_idx', index: 'charge_idx', width: 0, sortable: false, hidden: true},
				{ label: '입금일', name: 'charge_date', index: 'charge_date', width: 120, sortable: true},
				{ label: '입금액', name: 'charge_amount', index: 'charge_amount', width: 120, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 100, sortable: true},
				{ label: '등록일시', name: 'charge_regdate', index: 'charge_regdate', width: 120, sortable: true, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'charge_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//브라우저 리사이즈 trigger
				$(window).trigger("resize");

			}
		});
		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};


	/**
	 * 광고비관리 페이지 초기화
	 * @constructor
	 */
	var AdCostInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "9");

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

		//광고비충전/사용 버튼
		$(".btn-ad-pop").on("click", function(){
			Common.newWinPopup("ad_cost_pop.php?mode="+$(this).data("mode"), 'ad_cost_pop', 960, 500, 'yes');
		});

		//다운로드 버튼
		$(".btn-xls-down").on("click", function(){
			AdCostXlsDown();
		});

		AdCostGridInit();
	};

	/**
	 * 광고비관리 Grid 초기화
	 * @constructor
	 */
	var AdCostGridInit = function(){

		//Grid 초기화
		$("#grid_list_add_cost").jqGrid({
			url: '/settle/ad_cost_grid.php',
			mtype: "GET",
			datatype: "local",
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
			colModel: [
				{ label: 'charge_idx', name: 'charge_idx', index: 'charge_idx', width: 0, hidden: true},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 150, hidden: false},
				{ label: '판매처 코드', name: 'seller_idx', index: 'seller_idx', width: 100, hidden: false},
				{ label: '날짜', name: 'ad_date', index: 'ad_date', width: 100, hidden: false},
				{ label: '광고비 충전', name: 'ad_amount_charge', index: 'ad_amount_charge', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
					return (cellvalue == '') ? '' : Common.addCommas(cellvalue);
					}},
				{ label: '광고비 사용', name: 'ad_amount_use', index: 'ad_amount_use', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
					return (cellvalue == '') ? '' : Common.addCommas(cellvalue);
				}},
				{ label: '광고상품명', name: 'ad_product_name', index: 'ad_product_name', width: 120, sortable: false},
				{ label: '비고', name: 'ad_memo', index: 'ad_memo', width: 200, sortable: false, align: 'left', formatter: function(cellvalue, options, rowobject){
						return cellvalue + '<a href="javascript:;" class="xsmall_btn btn-memo f-right">메모</a>';
					}},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 80, sortable: false},
				{ label: '등록일', name: 'ad_regdate', index: 'ad_regdate', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTimeOnlyDate(cellvalue);
					}},

			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'ad_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list_add_cost");

				//상품 선택 버튼 바인딩
				$(".btn-vendor-charge-history").on("click", function(){
					var idx = $(this).data("idx");

					Common.newWinPopup("vendor_charge_history_pop.php?member_idx="+idx, 'vendor_charge_history_pop', 800, 750, 'yes');
				});

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("adcost_list", $("#grid_list_add_cost"));

				var userData = $("#grid_list_add_cost").jqGrid("getGridParam", "userData");
				if(typeof userData != "undefined") {
					var total_charge = userData.total_charge;
					var total_use = userData.total_use;

					$(".total_charge").text(Common.addCommas(total_charge));
					$(".total_use").text(Common.addCommas(total_use));
				}
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list_add_cost").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "adcost_list");
			}
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list_add_cost");
		}).trigger("resize");

		setTimeout(function(){
			AdCostGridSearch();
		}, 500);

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				AdCostGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			AdCostGridSearch();
		});
	};

	/**
	 * 광고비관리 목록/검색
	 * @constructor
	 */
	var AdCostGridSearch = function(){
		$.jgrid.loadState("grid_list_add_cost", {restoreData: false, clearAfterLoad : true});
		$("#grid_list_add_cost").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 광고비관리 목록/검색
	 * @constructor
	 */
	var AdCostGridReload = function(){
		$("#grid_list_add_cost").setGridParam({
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 광고비관리 엑셀 다운로드
	 * @constructor
	 */
	var AdCostXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "ad_cost_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_AD_COST", function(){
				SettleCharge.AdCostXlsDownComplete();
			});
		}, 500);
	};

	var AdCostXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};


	/**
	 * 광고비충전/광고비사용 팝업 페이지 초기화
	 * @constructor
	 */
	var AdCostPopInit = function(){

		// $(".seller_idx").SumoSelect({
		// 	placeholder: '판매처를 선택해주세요.',
		// 	captionFormat : '{0}개 선택됨',
		// 	captionFormatAllSelected : '{0}개 모두 선택됨',
		// 	search: true,
		// 	searchText: '판매처 검색',
		// 	noMatch : '검색결과가 없습니다.'
		// });

		$(".money").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 0, autoGroup: true, rightAlign: true});

		$("#btn-save-pop").on("click", function(){
			$("#dyFormChargePop").submit();
		});

		$("#dyFormChargePop").on("submit", function(e){
			if(!confirm('저장 하시겠습니까?')){
				return false;
			}
		});
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
		VendorChargeInit: VendorChargeInit,
		VendorChargePopInit: VendorChargePopInit,
		VendorChargeGridReload: VendorChargeGridReload,
		VendorChargeHistoryPopInit: VendorChargeHistoryPopInit,
		AdCostInit: AdCostInit,
		AdCostGridReload: AdCostGridReload,
		AdCostPopInit: AdCostPopInit,
		VendorChargeXlsDownComplete: VendorChargeXlsDownComplete,
		AdCostXlsDownComplete: AdCostXlsDownComplete,
	}
})();
$(function(){
	SettleCharge.init();
});