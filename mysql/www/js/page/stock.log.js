/*
 * 재고 로그 조회 js
 */
var StockLog = (function() {
	var root = this;

	var xlsDownIng = false;
	var xlsDownInterval;

	var init = function () {
	};

	/**
	 * 재고 로그 조회 페이지 초기화
	 * @constructor
	 */
	var StockLogListInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

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

		//Grid 초기화
		StockLogListGridInit();

		//다운로드 버튼 바인딩
		$(".btn-stock-log-xls-down").on("click", function(){
			StockLogListXlsDown();
		});

	};

	/**
	 * 재고 로그 조회 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockLogListGridInit = function(){
		//재고 로그 조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './stock_log_list_grid.php',
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
			colModel:  [
				{ label: '로그IDX', name: 'stock_move_idx', index: 'A.stock_move_idx', width: 0, sortable: false, hidden: true},
				{ label: '작업일', name: 'stock_move_regdate', index: 'stock_move_regdate', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 80, sortable: false},
				{ label: '상품코드', name: 'product_idx', index: 'product_idx', width: 80, sortable: false, hidden: true},
				{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: false},
				{ label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: false},
				{ label: '처리 전 상태', name: 'stock_status_prev_han', index: 'stock_status_prev_han', width: 80, sortable: false},
				{ label: '처리 후 상태', name: 'stock_status_next_han', index: 'stock_status_next_han', width: 80, sortable: false},
				{ label: '수량', name: 'stock_move_amount', index: 'stock_move_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '원가(매입가)', name: 'stock_unit_price', index: 'stock_unit_price', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 80, sortable: false},
				{ label: '재고메모', name: 'stock_move_msg', index: 'stock_move_msg', width: 200, sortable: false, align: 'left'}
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'stock_move_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
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
				StockLogListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockLogListSearch();
		});
	};

	/**
	 * 재고 로그 조회 목록/검색
	 * @constructor
	 */
	var StockLogListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 재고 로그 조회 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockLogListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};
		var url = "stock_log_xls_down.php?"+$.param(dataObj);

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("STOCK_LOG_LIST", function(){
				StockLog.StockLogListXlsDownComplete();
			});
		}, 500);
	};

	var StockLogListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	return {
		StockLogListInit: StockLogListInit,
		StockLogListXlsDownComplete: StockLogListXlsDownComplete,
	}
})();