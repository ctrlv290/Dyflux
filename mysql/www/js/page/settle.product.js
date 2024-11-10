/*
 * 정산통계 - 상품매출통계 관리 js
 */
var SettleProduct = (function() {
	var root = this;

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
	 * 상품별매출통계 페이지 초기화
	 * @constructor
	 */
	var ProductSaleInit = function(){

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
			selectAll:true,
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//selectAll 박스 위치 조정
		$('.seller_idx').on('sumo:opening', function () {
			$('.select-all').css('height', '35px');
			$('.select-all').children('label').text('전체 판매처');
		});

		//다운로드 버튼 클릭 이벤트
		$(".btn-xls-down").on("click", function(){
			ProductSaleXlsDown();
		});


		//jqGrid 초기화
		ProductSaleGridInit();
	};

	/**
	 * 상품별매출통계 Grid 초기화
	 * @constructor
	 */
	var ProductSaleGridInit = function(){

		$("#grid_list").jqGrid({
			url: './product_sale_grid.php',
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
				{ label: 'product_name', name: 'product_name', index: 'product_name', width: 100, sortable: false, hidden: true},
				{ label: 'product_option_name', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: false, hidden: true},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: false},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false},
				{ label: '상품코드', name: 'product_idx', index: 'product_idx', width: 100, sortable: false, hidden: true},
				{ label: '옵션코드', name: 'product_option_idx', index: 'PO.product_option_idx', width: 100, sortable: true},
				{ label: '상품명', name: 'product_name2', index: 'product_name2', width: 100, sortable: false, align: 'left', formatter: function(cellvalue, options, rowobject){
						var rst = '<a href="javascript:;" class="link btn-pop-detail-product" data-rowid="'+options.rowId+'">'+rowobject.product_name+'</a>';
						return rst;

					}},
				{ label: '공급처 상품명', name: 'product_supplier_name', index: 'product_supplier_name', width: 100, sortable: false, align: 'left'},
				{ label: '옵션', name: 'product_option_name2', index: 'product_option_name2', width: 100, sortable: false, align: 'left', formatter: function(cellvalue, options, rowobject){

					var rst = '<a href="javascript:;" class="link btn-pop-detail-product-option" data-rowid="'+options.rowId+'">'+rowobject.product_option_name+'</a>';
					return rst;

					}},
				{ label: '현재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 100, sortable: true, formatter: 'integer'},
				{ label: '불량재고', name: 'stock_amount_BAD', index: 'stock_amount_BAD', width: 100, sortable: true, formatter: 'integer'},
				{ label: '수량', name: 'product_count', index: 'product_count', width: 100, sortable: true, formatter: 'integer'},
				{ label: '매출합계', name: 'sum_settle_sale_supply', index: 'sum_settle_sale_supply', width: 100, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '품절', name: 'product_option_soldout', index: 'product_option_soldout', width: 100, sortable: false},
				{ label: '등록일', name: 'product_regdate', index: 'product_regdate', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTimeOnlyDate(cellvalue);
					}},
			],
			rowNum: 1000,
			rowList: [],
			pager: '#grid_pager',
			sortname: 'seller_name',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				$(".btn-pop-detail-product").on("click", function(){
					var rowid = $(this).data("rowid");
					ProductSalePopSellerOpen('product', rowid);
				});
				$(".btn-pop-detail-product-option").on("click", function(){
					var rowid = $(this).data("rowid");
					ProductSalePopSellerOpen('product_option', rowid);
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
				ProductSaleSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ProductSaleSearch();
		});
	};

	/**
	 * 상품별매출통계 페이지 Grid 목록/검색
	 * @constructor
	 */
	var ProductSaleSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var xlsDownIng = false;
	var xlsDownInterval;

	/**
	 * 상품별매출통계목록 엑셀 다운로드
	 * @constructor
	 */
	var ProductSaleXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "product_sale_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_PRODUCT_SALE", function(){
				SettleProduct.ProductSaleXlsDownComplete();
			});
		}, 500);
	};

	var ProductSaleXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 판매처별통계 팝업 오픈
	 * @constructor
	 */
	var ProductSalePopSellerOpen = function(tp, rowid){
		var rowData = $("#grid_list").getRowData(rowid);
		var product_idx = rowData.product_idx;
		var product_option_idx = rowData.product_option_idx;
		var product_name = rowData.product_name;
		var product_option_name = rowData.product_option_name;

		var userdata = $("#grid_list").jqGrid("getGridParam", "userData");
		var date_start = userdata.date_start;
		var date_end = userdata.date_end;


		var p_url = "product_sale_pop_seller.php";
		var dataObj = new Object();
		dataObj.mode = tp;
		dataObj.product_idx = product_idx;
		dataObj.product_option_idx = product_option_idx;
		dataObj.product_name = product_name;
		dataObj.product_option_name = product_option_name;
		dataObj.date_start = date_start;
		dataObj.date_end = date_end;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				console.log(response);
				PopupCommonPopOpen(650, 0, "판매처별통계", response);
				$("#modal_common").css("maxHeight", 500);
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 판매처별통계 팝업 페이지 초기화
	 * @constructor
	 */
	var ProductSalePopSellerInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			PopupCommonPopClose();
		});

	};


	/**
	 * 판매처상품별통계 페이지 초기화
	 * @constructor
	 */
	var MarketProductInit = function(){

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

		//다운로드 버튼 클릭 이벤트
		$(".btn-xls-down").on("click", function(){
			MarketProductXlsDown();
		});

		//jqGrid 초기화
		MarketProductGridInit();
	};

	/**
	 * 판매처상품별통계 Grid 초기화
	 * @constructor
	 */
	var MarketProductGridInit = function(){

		$("#grid_list").jqGrid({
			url: './market_product_grid.php',
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
				{ label: '발주일', name: 'accept_date', index: 'accept_date', width: 80, sortable: true},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: true},
				{ label: '상품코드', name: 'market_product_no', index: 'market_product_no', width: 100, sortable: false},
				{ label: '상품명', name: 'market_product_name', index: 'market_product_name', width: 100, sortable: false, align: 'left'},
				{ label: '옵션', name: 'market_product_option', index: 'market_product_option', width: 100, sortable: false, align: 'left'},
				{ label: '주문수', name: 'order_cnt', index: 'order_cnt', width: 60, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '상품수', name: 'order_product_cnt', index: 'order_product_cnt', width: 60, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '판매금액합', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '매입가합', name: 'product_option_purchase_price', index: 'product_option_purchase_price', width: 80, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '정산금액', name: 'order_calculation_amt', index: 'order_calculation_amt', width: 100, sortable: true, align: 'right', formatter: 'integer'},
				// { label: '선불택배비', name: 'delivery_is_free', index: 'delivery_is_free', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
				// 		if(cellvalue == "Y"){
				// 			return Common.addCommas(rowobject.delivery_fee);
				// 		}else{
				// 			return 0;
				// 		}
				// 	}},
			],
			rowNum: 1000,
			rowList: [],
			pager: '#grid_pager',
			sortname: '',
			sortorder: "",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				$(".btn-pop-detail-product").on("click", function(){
					var rowid = $(this).data("rowid");
					ProductSalePopSellerOpen('product', rowid);
				});
				$(".btn-pop-detail-product-option").on("click", function(){
					var rowid = $(this).data("rowid");
					ProductSalePopSellerOpen('product_option', rowid);
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
				MarketProductSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			MarketProductSearch();
		});
	};

	/**
	 * 판매처상품별통계 페이지 Grid 목록/검색
	 * @constructor
	 */
	var MarketProductSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 상품별매출통계목록 엑셀 다운로드
	 * @constructor
	 */
	var MarketProductXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "market_product_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_MARKET_PRODUCT", function(){
				SettleProduct.MarketProductXlsDownComplete();
			});
		}, 500);
	};

	var MarketProductXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 일별상품별 페이지 초기화
	 * @constructor
	 */
	var ProductDailyListInit = function(){
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


		//시간 inputMask
		$(".time_start, .time_end").inputmask("datetime", {
				placeholder: 'hh:mm:ss',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);

		//항목설정 팝업
		$(".btn-column-setting-pop").on("click", function(){
			Common.newWinPopup("/common/column_setting_pop.php?target=STOCK_PERIOD_LIST&mode=list", 'column_setting_pop', 700, 720, 'no');
		});


		//다운로드 버튼 바인딩
		$(".btn-stock-list-xls-down").on("click", function(){
			ProductDailyXlsDown();
		});

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				ProductDailyListGridHeaderInit(true);
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ProductDailyListGridHeaderInit(true);
		});

		//Grid 초기화
		ProductDailyListGridHeaderInit(false);
	};

	var ProductDailyListGridHeaderInit = function(isSearch){

		//기존 그리드가 생성되었을 경우 UnLoad
		$.jgrid.gridUnload("#grid_list");

		var colModel = [
			{label: '상품옵션코드', name: 'product_option_idx2', index: 'STOCK_DAILY.product_option_idx', width: 80, is_use : true},
			{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left', is_use : true},
			{label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left', is_use : true},
			{ label: '원가', name: 'product_option_purchase_price', index: 'STOCK_DAILY.product_option_purchase_price', width: 80, align: 'right', sortable: true, is_use : true, formatter: 'integer'},
			{ label: '원가x수량', name: 'sum_product_option_purchase_price', index: 'sum_product_option_purchase_price', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return Common.addCommas(rowobject.product_option_purchase_price * rowobject.sum_product_option_cnt);
				}},
			{ label: '판매가합', name: 'sum_settle_sale_supply', index: 'STOCK_DAILY.sum_settle_sale_supply', width: 80, align: 'right', sortable: true, is_use : true, formatter: 'integer'},
			// { label: '판매가x수량', name: 'sum_settle_sale_supply2', index: 'sum_settle_sale_supply2', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
			// 		return Common.addCommas(rowobject.sum_settle_sale_supply * rowobject.sum_product_option_cnt);
			// 	}},
			{label: '현재<br>정상재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, sortable: false, is_use : true, formatter: 'integer'},
			{label: '접수', name: 'current_accept_count', index: 'current_accept_count', width: 80, sortable: true, is_use : true, formatter: 'integer'},
			{label: '송장', name: 'current_invoice_count', index: 'current_invoice_count', width: 80, sortable: true, is_use : true, formatter: 'integer'},
			{label: '기간내<br>발주수량', name: 'accept_count', index: 'accept_count', width: 80, sortable: true, is_use : true, formatter: 'integer'},
			{label: '기간내<br>송장수량', name: 'invoice_count', index: 'invoice_count', width: 80, sortable: true, is_use : true, formatter: 'integer'},
			{label: '기간내<br>배송수량', name: 'shipping_count', index: 'shipping_count', width: 80, sortable: true, is_use : true, formatter: 'integer'},
			{label: '차트', name: 'btn_chart', index: 'btn_chart', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="btn btn-chart-pop" data-product_option_idx="'+rowobject.product_option_idx2+'" data-product_option_purchase_price="'+rowobject.product_option_purchase_price+'">챠트</a>'
				}},
		];

		var start_date = $("#period_preset_start_input").val();
		var end_date = $("#period_preset_end_input").val();

		start_date = moment(start_date, 'YYYY-MM-DD');
		end_date = moment(end_date, 'YYYY-MM-DD');

		var value_view = $("select[name='value_view']").val();

		// If you want an inclusive end date (fully-closed interval)
		for (var m = start_date; m.diff(end_date, 'days') <= 0; m.add(1, 'days')) {
			var colLabel = m.format('MM/DD');
			var colName = m.format('YYYYMMDD');

			colModel.push({
				label: colLabel
				, name: 's'+colName+'_'+value_view
				, index: 's'+colName+'_'+value_view
				, width: 80
				, sortable: false
				, formatter: 'integer'
			});
		}

		ProductDailyListGridInit(colModel);

		// if(isSearch){
		// 	$("#grid_list").clearGridData().setGridParam({
		// 		datatype: "json",
		// 		page: 1,
		// 		postData:{
		// 			param: $("#searchForm").serialize()
		// 		}
		// 	}).trigger("reloadGrid");
		// }
	};

	/**
	 * 일별상품별 목록 바인딩 jqGrid
	 * @constructor
	 */
	var ProductDailyListGridInit = function(colModel){

		var grid_cookie_name = "settle_product_daily";

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './product_daily_grid.php',
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
			colModel: colModel,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'PO.product_option_idx',
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

				//마지막 날짜 변경
				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				$(".th_period").text(userData.period);

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_daily_list", $("#grid_list"));

				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				var sum_total = userData.sum_total;

				$(".product_total").text(Common.addCommas(sum_total));


				//차트
				$(".btn-chart-pop").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					var product_option_purchase_price = $(this).data("product_option_purchase_price");
					var date_start = $("#period_preset_start_input").val();
					var date_end = $("#period_preset_end_input").val();
					var seller_idx = $("#seller_idx").val();
					Common.newWinPopup("product_chart_pop.php?product_option_idx="+product_option_idx+"&product_option_purchase_price="+product_option_purchase_price+"&date_start="+date_start+"&date_end="+date_end+"&seller_idx="+seller_idx, 'stock_chart_pop', 1200, 610, 'yes');
				});
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_daily_list");
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	/**
	 * 일별상품별 목록/검색
	 * @constructor
	 */
	var ProductDailyListSearch = function(){
		ProductDailyListGridHeaderInit(true);
		/*
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
		*/
	};

	/**
	 * 일별상품별 목록 엑셀 다운로드
	 * @constructor
	 */
	var ProductDailyXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "product_daily_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_PRODUCT_DAILY", function(){
				SettleProduct.ProductDailyXlsDownComplete();
			});
		}, 500);
	};

	var ProductDailyXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};
	/**
	 * 월별상품별 페이지 초기화
	 * @constructor
	 */
	var ProductMonthlyListInit = function(){
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

		//Grid 초기화
		ProductMonthlyListGridHeaderInit(false);

		//다운로드 버튼 바인딩
		$(".btn-stock-list-xls-down").on("click", function(){
			ProductMonthlyXlsDown();
		});

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				ProductMonthlyListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ProductMonthlyListSearch();
		});
	};

	/**
	 * 월별상품별통계 - Grid Header 구성 (날짜별 컬럼 추가)
	 * @param isSearch
	 * @constructor
	 */
	var ProductMonthlyListGridHeaderInit = function(isSearch) {

		//기존 그리드가 생성되었을 경우 UnLoad
		$.jgrid.gridUnload("#grid_list");

		var colModel = [
			{label: '상품옵션코드', name: 'product_option_idx2', index: 'STOCK_DAILY.product_option_idx', width: 80, is_use : true},
			{label: '상품명', name: 'product_name', index: 'P.product_name', width: 150, sortable: false, align: 'left', is_use : true},
			{label: '옵션명', name: 'product_option_name', index: 'PO.product_option_name', width: 150, sortable: false, align: 'left', is_use : true},
			{label: '원가', name: 'product_option_purchase_price', index: 'STOCK_DAILY.product_option_purchase_price', width: 80, align: 'right', sortable: true, is_use : true, formatter: 'integer'},
			{label: '원가x수량', name: 'sum_product_option_purchase_price', index: 'sum_product_option_purchase_price', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return Common.addCommas(rowobject.product_option_purchase_price * rowobject.sum_product_option_cnt);
				}},
			{label: '판매가합', name: 'sum_settle_sale_supply', index: 'STOCK_DAILY.sum_settle_sale_supply', width: 80, align: 'right', sortable: true, is_use : true, formatter: 'integer'},
			// {label: '판매가x수량', name: 'sum_settle_sale_supply2', index: 'sum_settle_sale_supply2', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
			// 		return Common.addCommas(rowobject.sum_settle_sale_supply * rowobject.sum_product_option_cnt);
			// 	}},
			{label: '현재<br>정상재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, sortable: false, is_use : true, formatter: 'integer'},
			{label: '접수', name: 'current_accept_count', index: 'current_accept_count', width: 80, sortable: false, is_use : true, formatter: 'integer'},
			{label: '송장', name: 'current_invoice_count', index: 'current_invoice_count', width: 80, sortable: false, is_use : true, formatter: 'integer'},
			{label: '기간내<br>발주수량', name: 'accept_count', index: 'accept_count', width: 80, sortable: false, is_use : true, formatter: 'integer'},
			{label: '기간내<br>송장수량', name: 'invoice_count', index: 'invoice_count', width: 80, sortable: false, is_use : true, formatter: 'integer'},
			{label: '기간내<br>배송수량', name: 'shipping_count', index: 'shipping_count', width: 80, sortable: false, is_use : true, formatter: 'integer'},
			{label: '차트', name: 'btn_chart', index: 'btn_chart', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="btn btn-chart-pop" data-product_option_idx="'+rowobject.product_option_idx2+'" data-product_option_purchase_price="'+rowobject.product_option_purchase_price+'">챠트</a>'
				}},
		];

		var start_date = $("#period_start_year_input").val() + '-' + $("#period_start_month_input").val() + '-1';
		var end_date = $("#period_end_year_input").val() + '-' + $("#period_end_month_input").val() + '-1';

		start_date = moment(start_date, 'YYYY-MM-DD');
		end_date = moment(end_date, 'YYYY-MM-DD');

		var value_view = $("select[name='value_view']").val();

		// If you want an inclusive end date (fully-closed interval)
		for (var m = start_date; m.diff(end_date, 'days') <= 0; m.add(1, 'month')) {
			var colLabel = m.format('YYYY/MM');
			var colName = m.format('YYYYMM');

			colModel.push({
				label: colLabel
				, name: 's'+colName+'_'+value_view
				, index: 's'+colName+'_'+value_view
				, width: 80
				, sortable: false
				, formatter: 'integer'
			});
		}

		ProductMonthlyListGridInit(colModel);

		if(isSearch){
			$("#grid_list").clearGridData().setGridParam({
				datatype: "json",
				page: 1,
				postData:{
					param: $("#searchForm").serialize()
				}
			}).trigger("reloadGrid");
		}
	};

	/**
	 * 월별상품별 목록 바인딩 jqGrid
	 * @constructor
	 */
	var ProductMonthlyListGridInit = function(colModel){


		var grid_cookie_name = "settle_product_monthly";

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './product_monthly_grid.php',
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
			colModel: colModel,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'PO.product_option_idx',
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

				//마지막 날짜 변경
				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				$(".th_period").text(userData.period);

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_monthly_list", $("#grid_list"));

				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				var sum_total = userData.sum_total;

				$(".product_total").text(Common.addCommas(sum_total));

				//차트
				$(".btn-chart-pop").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					var product_option_purchase_price = $(this).data("product_option_purchase_price");
					var date_start_year = $("#period_start_year_input").val();
					var date_start_month = $("#period_start_month_input").val();
					var date_end_year = $("#period_end_year_input").val();
					var date_end_month = $("#period_end_month_input").val();
					var seller_idx = $("#seller_idx").val();
					Common.newWinPopup("product_chart_pop.php?product_option_idx="+product_option_idx+"&product_option_purchase_price="+product_option_purchase_price+"&date_start_year="+date_start_year+"&date_start_month="+date_start_month+"&date_end_year="+date_end_year+"&date_end_month="+date_end_month+"&seller_idx="+seller_idx+"&format=month", 'stock_chart_pop', 1200, 610, 'yes');
				});
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_monthly_list");
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");


	};

	/**
	 * 월별상품별 목록/검색
	 * grid 헤더 재구성 후 grid reload
	 * @constructor
	 */
	var ProductMonthlyListSearch = function(){
		ProductMonthlyListGridHeaderInit(true);
		/*
		$("#grid_list").clearGridData().setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
		*/
	};

	/**
	 * 월별상품별 목록 엑셀 다운로드
	 * @constructor
	 */
	var ProductMonthlyXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "product_monthly_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_PRODUCT_MONTHLY", function(){
				SettleProduct.ProductMonthlyXlsDownComplete();
			});
		}, 500);
	};

	var ProductMonthlyXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};


	/**
	 * 카테고리별 페이지 초기화
	 * @constructor
	 */
	var CategorySaleListInit = function(){
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

		//다운로드 버튼 바인딩
		$(".btn-stock-list-xls-down").on("click", function(){
			CategorySaleXlsDown();
		});

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				CategorySaleListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			CategorySaleListSearch();
		});
	};

	var CategorySaleListGridInit = function(){
		var p_url = "category_sale_grid.php";

		$(".table_category_sale tbody").empty();

		showLoader();
		$.ajax({
			type: 'GET',
			url: p_url,
			dataType: "json",
			data: $("#searchForm").serialize()
		}).done(function (response) {
			if(response)
			{
				if(response.result)
				{
					var data = response.data;
					if(data.length > 0){
						$.each(data, function(i, o){

							var html;
							var rowNum = o.rowNum;
							var rowCnt = o.rowCnt;
							var tr_class= "";

							var cate1_name = o.name;
							var cate2_name = o.c2_name;
							if (rowNum == rowCnt){
								cate2_name = '합계';
								tr_class = "light_gray";
							}

							if(cate1_name == null) cate1_name = "카테고리 없음";
							if(cate2_name == null) cate2_name = "카테고리 없음";


							html = '';
							html += '<tr class="'+tr_class+'">';
							if(rowNum == 1) {
								html += '<td rowspan="'+rowCnt+'">' + cate1_name + '</td>';
							}
							html += '<td>' + cate2_name + '</td>';
							html += '<td class="text_right">' + Common.addCommas(o.sum_product_option_cnt) + '</td>';
							html += '<td class="text_right">' + Common.addCommas(o.sum_product_option_purchase_price) + '</td>';
							html += '<td class="text_right">' + Common.addCommas(o.sum_settle_sale_supply) + '</td>';
							html += '</tr>';

							$(".table_category_sale tbody").append(html);
						});
					}
				}

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	var CategorySaleListSearch = function(){
		CategorySaleListGridInit();
	};

	/**
	 * 월별상품별 목록 엑셀 다운로드
	 * @constructor
	 */
	var CategorySaleXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "category_sale_xls_down.php?"+param;
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_CATEGORY_SALE", function(){
				SettleProduct.CategorySaleXlsDownComplete();
			});
		}, 500);
	};

	var CategorySaleXlsDownComplete = function(){
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
		ProductSaleInit: ProductSaleInit,
		ProductSalePopSellerInit: ProductSalePopSellerInit,
		MarketProductInit: MarketProductInit,
		ProductDailyListInit: ProductDailyListInit,
		ProductMonthlyListInit: ProductMonthlyListInit,
		CategorySaleListInit: CategorySaleListInit,
		ProductSaleXlsDownComplete: ProductSaleXlsDownComplete,
		MarketProductXlsDownComplete: MarketProductXlsDownComplete,
		ProductDailyXlsDownComplete: ProductDailyXlsDownComplete,
		ProductMonthlyXlsDownComplete: ProductMonthlyXlsDownComplete,
		CategorySaleXlsDownComplete: CategorySaleXlsDownComplete,
	}
})();
$(function(){
	SettleProduct.init();
});