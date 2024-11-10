/*
 * 주문관리 js
 */
var OrderSub = (function() {
	var root = this;

	var xlsDownIng = false;
	var xlsDownInterval;

	var init = function() {
	};

	/**
	 * 확장주문검색 페이지 초기화
	 * @constructor
	 */
	var OrderSubInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "1");

		//다운로드 버튼 바인딩
		$(".btn-xls-down").on("click", function(){
			OrderSubXlsDown();
		});

		OrderSubGridInit();
	};

	/**
	 * 확장주문검색 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderSubGridInit = function(){

		var grid_cookie_name = "stock_list";

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './sub_list_grid.php',
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
				{label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 100, is_use : true},
				{label: '관리번호', name: 'order_idx', index: 'order_idx', width: 100, is_use : true},
				{label: '수령자<br>이름', name: 'receive_name', index: 'receive_name', width: 100, sortable: false, is_use : true},
				{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left', is_use : true},
				{label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left', is_use : true},
				{label: '주문수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return Common.addCommas(cellvalue);
					}},
				{label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: false, is_use : true},
				{label: '상태', name: 'order_progress_step_han', index: 'order_progress_step_han', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return cellvalue + '/' + rowobject.order_cs_status_han;
					}


				},

				{label: '발주시간', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}
				},
				{label: '송장입력일', name: 'invoice_date', index: 'invoice_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTimeOnlyDate(cellvalue);
					}
				},
				{label: '택배사', name: 'delivery_name', index: 'delivery_name', width: 100, sortable: false, is_use : true},
				{label: '송장번호', name: 'invoice_no', index: 'invoice_no', width: 100, sortable: false, is_use : true},
				{label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 120, sortable: false, is_use : true},
				{label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTimeOnlyDate(cellvalue);
					}
				},
				{label: '선착불', name: 'delivery_is_free', index: 'delivery_is_free', width: 120, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return (cellvalue == "Y") ? "선불" : "착불";
					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowListBig[0],
			rowList: Common.jsSiteConfig.jqGridRowListBig,
			pager: '#grid_pager',
			sortname: 'A.order_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//재고조정
				$(".btn-stock-control").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					StockControlPopOpen(product_option_idx, 'NORMAL', '');
				});

				//각 재고 수량 클릭 시 재고조정
				$(".btn-stock-control-status").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					var stock_status = $(this).data("stock_status");
					StockControlPopOpen(product_option_idx, stock_status, '');
				});

				//상품 별 로그
				$(".btn-stock-product-log").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					StockLogViewerPopOpen(product_option_idx);
				});

				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				$(".summary_order_amt_sum").text(Common.addCommas(userData.order_amt_sum));
				$(".summary_order_calculation_amt_sum").text(Common.addCommas(userData.order_calculation_amt_sum));

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("order_sub_list", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "order_sub_list");
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

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
				OrderSubSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderSubSearch();
		});
	};

	/**
	 * 확장주문검색 목록/검색
	 * @constructor
	 */
	var OrderSubSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 확장주문검색 목록 엑셀 다운로드
	 * @constructor
	 */
	var OrderSubXlsDown = function(){
		var param = $("#searchForm").serialize();
		location.href="order_search_xls_down.php?"+param;
	};

	/**
	 * 하부주문관리 엑셀 다운로드
	 * @constructor
	 */
	var OrderSubXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "sub_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_ORDER_SUB_LIST", function(){
				OrderSub.OrderSubXlsDownComplete();
			});
		}, 500);
	};

	var OrderSubXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	return {
		OrderSubInit : OrderSubInit,
		OrderSubXlsDownComplete: OrderSubXlsDownComplete,
	}
})();