/*
 * 재고조회 관리 js
 */
var StockProduct = (function() {
	var root = this;

	var init = function () {
	};

	/**
	 * 상품재고조회 페이지 초기화
	 * @constructor
	 */
	var StockProductListInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "9");

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

		//항목설정 팝업
		$(".btn-column-setting-pop").on("click", function(){
			Common.newWinPopup("/common/column_setting_pop.php?target=STOCK_PRODUCT_LIST&mode=list", 'column_setting_pop', 700, 720, 'no');
		});

		//Grid 초기화
		StockProductListGridInit();

		//다운로드 버튼 바인딩
		$(".btn-stock-product-xls-down").on("click", function(){
			StockProductListXlsDown();
		});
	};

	/**
	 * 상품재고조회 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockProductListGridInit = function(){
		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './stock_product_list_grid.php',
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
			colModel: _gridColModel,
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

				productImgThumb();
				lightbox.option({
					'resizeDuration': 100,
					'fadeDuration': 200,
					'imageFadeDuration': 200,
					'albumLabel': "상품이미지 %1/%2",
				});
				$("td[name='warning']").addClass("bg_danger");

				//재고조정
				$(".btn-stock-control").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					var stock_unit_price = $(this).data("stock_unit_price");
					StockControlPopOpen(product_option_idx, 'NORMAL', stock_unit_price);
				});

				//각 재고 수량 클릭 시 재고조정
				$(".btn-stock-control-status").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					var stock_status = $(this).data("stock_status");
					var stock_unit_price = $(this).data("stock_unit_price");
					StockControlPopOpen(product_option_idx, stock_status, stock_unit_price);
				});

				//상품 별 로그
				$(".btn-stock-product-log").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					var stock_unit_price = $(this).data("stock_unit_price");
                    var date_start = $(this).data("date_start");
                    var date_end = $(this).data("date_end");
                    console.log(date_start)
					StockUnitPriceLogViewerPopOpen(product_option_idx, stock_unit_price, date_start, date_end);
				});

				//재고 차트
				$(".btn-stock-chart").on("click", function(){
					var product_option_idx = $(this).data("product_option_idx");
					var stock_unit_price = $(this).data("stock_unit_price");
					Common.newWinPopup("stock_product_chart.php?product_option_idx="+product_option_idx+"&stock_unit_price="+stock_unit_price, 'stock_chart_pop', 1024, 820, 'yes');
				});

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_product_list", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_product_list");
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
				StockProductListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockProductListSearch();
		});
	};

	/**
	 * 상품재고조회 목록/검색
	 * @constructor
	 */
	var StockProductListSearch = function(){
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
	 * 상품재고조회 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockProductListXlsDown = function(){

		if(xlsDownIng) return;

		xlsDownIng = true;


		var param = $("#searchForm").serialize();

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "stock_product_list_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("STOCK_PRODUCT_LIST", function(){
				StockProduct.StockProductListXlsDownComplete();
			});
		}, 500);
	};

	var StockProductListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 현재고조회 페이지 초기화
	 * @constructor
	 */
	var StockListInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "9");

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select2", 'period_preset_start_input2', 'period_preset_end_input2', "9");


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
			Common.newWinPopup("/common/column_setting_pop.php?target=STOCK_LIST&mode=list", 'column_setting_pop', 700, 720, 'no');
		});

		//Grid 초기화
		StockListGridInit();

		//다운로드 버튼 바인딩
		$(".btn-stock-list-xls-down").on("click", function(){
			StockListXlsDown();
		});
	};

	/**
	 * 현재고조회 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockListGridInit = function(){

		var grid_cookie_name = "stock_list";

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './stock_list_grid.php',
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
			colModel: _gridColModel,
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

				productImgThumb();
				lightbox.option({
					'resizeDuration': 100,
					'fadeDuration': 200,
					'imageFadeDuration': 200,
					'albumLabel': "상품이미지 %1/%2",
				});

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

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_list", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_list");
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
				StockListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockListSearch();
		});
	};

	/**
	 * 현재고조회 목록/검색
	 * @constructor
	 */
	var StockListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 현재고조회 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;


		var param = $("#searchForm").serialize();

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "stock_list_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("STOCK_LIST", function(){
				StockProduct.StockListXlsDownComplete();
			});
		}, 500);
	};

	var StockListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 기간별 재고조회 페이지 초기화
	 * @constructor
	 */
	var StockPeriodListInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "9");

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select2", 'period_preset_start_input2', 'period_preset_end_input2', "9");

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

		//Grid 초기화
		StockPeriodListGridInit();

		//다운로드 버튼 바인딩
		$(".btn-stock-period-list-xls-down").on("click", function(){
			StockPeriodListXlsDown();
		});
	};

	/**
	 * 기간별 재고조회 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockPeriodListGridInit = function(){

		var grid_cookie_name = "stock_list";

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './stock_period_list_grid.php',
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
			colModel: _gridColModel,
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

				productImgThumb();
				lightbox.option({
					'resizeDuration': 100,
					'fadeDuration': 200,
					'imageFadeDuration': 200,
					'albumLabel': "상품이미지 %1/%2",
				});

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
				$(".th_last_date").text(userData.last_date);

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_period_list", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_period_list");
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
				StockPeriodListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockPeriodListSearch();
		});
	};

	/**
	 * 기간별 재고조회 목록/검색
	 * @constructor
	 */
	var StockPeriodListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 기간별 재고조회 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockPeriodListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();

		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "stock_period_list_xls_down.php?"+$.param(dataObj);

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("STOCK_PERIOD_LIST", function(){
				StockProduct.StockPeriodListXlsDownComplete();
			});
		}, 500);
	};

	var StockPeriodListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 일자별 재고조회[입고량] 페이지 초기화
	 * @constructor
	 */
	var StockDailyInListInit = function(){
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
			Common.newWinPopup("/common/column_setting_pop.php?target=STOCK_DAILY_LIST&mode=list", 'column_setting_pop', 700, 720, 'no');
		});

		//Grid 초기화
		StockDailyInListGridInit();

		//다운로드 버튼 바인딩
		$(".btn-stock-list-xls-down").on("click", function(){
			StockDailyInListXlsDown();
		});
	};

	/**
	 * 일자별 재고조회[입고량] 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockDailyInListGridInit = function(){

		var colModel = _gridColModel;

		var start_date = $("#period_preset_start_input").val();
		var end_date = $("#period_preset_end_input").val();

		start_date = moment(start_date, 'YYYY-MM-DD');
		end_date = moment(end_date, 'YYYY-MM-DD');

		for (var m = start_date; m.diff(end_date, 'days') <= 0; m.add(1, 'days')) {
			var colLabel = m.format('MM-DD');
			var colName = m.format('YYYYMMDD');
			colModel.push({
				label: colLabel
				, name: 's' + colName
				, index: colName
				, width: 60
				, sortable: false
				, formatter: function (cellvalue, options, rowobject) {
					if(cellvalue != 0) {
						return '<a href="javascript:;" class="link btn-stock-detail" ' +
							'data-product_option_idx="' + rowobject.product_option_idx + '" ' +
							'data-confirm_date="' + options.colModel.index + '" ' +
							'data-stock_kind="' + $("select[name='stock_kind']").val() + '" ' +
							'data-stock_unit_price="' + rowobject.stock_unit_price + '">' +
							Common.addCommas(cellvalue) + '</a>';
					} else {
						return '-';
					}
				}
			});
		}

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './stock_daily_list_grid.php',
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
			loadComplete: function() {
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");
				productImgThumb();
				lightbox.option({
					'resizeDuration': 100,
					'fadeDuration': 200,
					'imageFadeDuration': 200,
					'albumLabel': "상품이미지 %1/%2",
				});
				//재고조정
				$(".btn-stock-control").on("click", function () {
					var product_option_idx = $(this).data("product_option_idx");
					StockControlPopOpen(product_option_idx, 'NORMAL', '');
				});

				//각 재고 수량 클릭 시 재고조정
				$(".btn-stock-control-status").on("click", function () {
					var product_option_idx = $(this).data("product_option_idx");
					var stock_status = $(this).data("stock_status");
					StockControlPopOpen(product_option_idx, stock_status, '');
				});

				//상품 별 로그
				$(".btn-stock-product-log").on("click", function () {
					var product_option_idx = $(this).data("product_option_idx");
					StockLogViewerPopOpen(product_option_idx);
				});

				//마지막 날짜 변경
				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				$(".th_period").text(userData.period);

				//재고 차트
				$(".btn-stock-chart").on("click", function () {
					var product_option_idx = $(this).data("product_option_idx");
					var stock_unit_price = $(this).data("stock_unit_price");
					Common.newWinPopup("stock_product_chart.php?product_option_idx=" + product_option_idx + "&stock_unit_price=" + stock_unit_price, 'stock_chart_pop', 1024, 820, 'yes');
				});
				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_daily_in", $("#grid_list"));

				//일자별 재고 0인 컬럼 Hide
				var hideDate = $("#grid_list").jqGrid("getGridParam", "userData");
				for(var i=0; i < hideDate.hide_date.length; i++){
					$("#grid_list").jqGrid("hideCol", hideDate.hide_date[i]);
				}

				//일자별 재고 상세내역 확인
				$(".btn-stock-detail").on("click", function(){
					StockDailyDetailPopup($(this).data("product_option_idx"),$(this).data("confirm_date"),$(this).data("stock_unit_price"),$(this).data("stock_kind"));
				});
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_daily_in");
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			//e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				StockDailyInListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockDailyInListSearch();
		});
	};

	/**
	 * 일자별 재고조회[입고량] 목록/검색
	 * @constructor
	 */
	var StockDailyInListSearch = function(){
		/*
		$("#grid_list").setGridParam({
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
		*/
		$("#searchForm").submit();
	};

	/**
	 * 일자별 재고조회[입고량] 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockDailyInListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};
		var url = "stock_daily_list_xls_down.php?"+$.param(dataObj);

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("STOCK_DAILY_LIST", function(){
				StockProduct.StockDailyInListXlsDownComplete();
			});
		}, 500);
	};

	var StockDailyInListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

    /**
     * 일자별 재고조회[입고량] 상세내역 페이지 팝업 Open
     * @param stock_idx
     * @constructor
     */
    var StockDailyDetailPopup = function(product_option_idx, confirm_date, stock_unit_price, stock_kind){
        Common.newWinPopup(
            "stock_daily_detail_pop.php?stock_kind="+stock_kind+
            "&product_option_idx="+product_option_idx+
            "&confirm_date="+confirm_date+
            "&stock_unit_price="+stock_unit_price,
            'stock_daily_detail_pop', 800, 600, 'yes');
    };

    /**
     * 일자별 재고조회[입고량] 상세내역 페이지 초기화
     * @constructor
     */
    var StockDailyDetailInit = function(){

        if($("#stock_kind").val()=="IN"){
            //입고 데이터 컬럼 정의
            var colModel = [
                { label: '발주번호', name: 'stock_order_idx', index: 'stock_order_idx', width: 60, sortable: false},
                { label: '상태', name: 'stock_status_name', index: 'stock_status_name', width: 60, sortable: false},
                { label: '생성일', name: 'stock_request_date', index: 'stock_request_date', width: 80, sortable: true, formatter: function(cellvalue, options, rowobject){
                        return Common.toDateTimeOnlyDate(cellvalue);}},
                { label: '입고예정일', name: 'stock_due_date', index: 'stock_due_date', width: 80, sortable: true},
                { label: '입고일', name: 'stock_in_date', index: 'stock_in_date', width: 80, sortable: true},
                // { label: '예정수량', name: 'stock_due_amount', index: 'stock_due_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
                //         return Common.addCommas(cellvalue);}},
                { label: '입고수량', name: 'stock_amount', index: 'stock_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
                        // var confirm_date = Common.toDateTimeOnlyDate(rowobject.stock_is_confirm_date).replace(/-/gi,"")
                        // if(rowobject.stock_status_name == '정상' && $("#confirm_date").val() == confirm_date){
                        //     return "<strong>"+Common.addCommas(cellvalue)+"</strong>";
                        // }else{
                            return Common.addCommas(cellvalue);
                        // }
                }},
                { label: '메모', name: 'stock_msg', index: 'stock_msg', width: 150, align: 'left', sortable: false, formatter: function(cellvalue, options, rowobject){
                        return (rowobject.stock_msg != null) ? rowobject.stock_msg : '' + ' ';}}
            ]
        }else if($("#stock_kind").val()=="OUT"){
            // 출고데이터 컬럼 정의
            var colModel = [
                { label: '주문번호', name: 'order_idx', index: 'A.order_idx', width: 60, sortable: false},
				{ label: '송장번호', name: 'invoice_no', index: 'O.invoice_no', width: 120, sortable: false},
                { label: '상태', name: 'stock_status_name', index: 'stock_status_name', width: 60, sortable: false},
                { label: '생성일', name: 'stock_request_date', index: 'stock_request_date', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
                        return Common.toDateTimeOnlyDate(cellvalue);}},
                { label: '송장일자', name: 'stock_invoice_date', index: 'stock_invoice_date', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
                        return Common.toDateTimeOnlyDate(cellvalue);}},
                { label: '배송일자', name: 'stock_shipped_date', index: 'stock_shipped_date', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
                        return Common.toDateTimeOnlyDate(cellvalue);}},
                { label: '출고수량', name: 'stock_amount', index: 'stock_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
                        return Common.addCommas(cellvalue);}},
				{ label: '수취인', name: 'receive_name', index: 'O.receive_name', width: 80, sortable: false},
				{ label: '전화번호', name: 'receive_tp_num', index: 'O.receive_tp_num', width: 120, sortable: false},
				{ label: '핸드폰번호', name: 'receive_hp_num', index: 'O.receive_hp_num', width: 120, sortable: false},
				{ label: '주소', name: 'receive_addr1', index: 'O.receive_addr1', width: 240, sortable: false, formatter: function(cellvalue, options, rowobject){
					if(!Common.isEmpty(rowobject.receive_zipcode)){
						if(!Common.isEmpty(rowobject.receive_addr2)){
							return '[' + rowobject.receive_zipcode + '] ' + cellvalue + ' ' + rowobject.receive_addr2;
						} else {
							return '[' + rowobject.receive_zipcode + '] ' + cellvalue;
						}
					} else {
						return cellvalue;
					}
				}},
                { label: '메모', name: 'stock_msg', index: 'stock_msg', width: 150, align: 'left', sortable: false, formatter: function(cellvalue, options, rowobject){
                        return (rowobject.stock_msg != null) ? rowobject.stock_msg : '' + ' ';}}
            ]
        }
        //처리완료 목록 바인딩 jqGrid
        $("#grid_list").jqGrid({
            url: './stock_daily_detail_grid.php',
            mtype: "GET",
            datatype: "json",
            postData:{
                product_option_idx: $("#product_option_idx").val(),
                confirm_date: $("#confirm_date").val(),
                stock_unit_price: $("#stock_unit_price").val(),
                stock_kind: $("#stock_kind").val(),
            },
            jsonReader : {
                page: "page",
                total: "total",
                root: "rows",
                records: "records",
                repeatitems: true,
                id: "idx"
            },
            colModel:  colModel,
            rowNum: 0,
            pgbuttons : false,
            pgtext: null,
            sortname: 'stock_request_date',
            sortorder: "asc",
            viewrecords: true,
            autowidth: true,
            rownumbers: true,
            shrinkToFit: false,
            height: 150,
            loadComplete: function(){

                //Grid 사이즈 reSize
                Common.jqGridResizeWidth("#grid_list");
            }
        });
        //브라우저 리사이즈 시 jqgrid 리사이징
        $(window).on("resize", function(){
            Common.jqGridResizeWidth("#grid_list");
        }).trigger("resize");
    };

	/**
	 * 일자별 재고조회[누적재고] 페이지 초기화
	 * @constructor
	 */
	var StockDailySUMListInit = function(){
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

		//Grid 초기화
		StockDailySUMListGridInit();

		//다운로드 버튼 바인딩
		$(".btn-xls-down").on("click", function(){
			StockSUMInListXlsDown();
		});
	};

	/**
	 * 일자별 재고조회[누적재고] 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockDailySUMListGridInit = function(){

		var grid_cookie_name = "stock_list";

		var colModel = [
			{label: '공급처코드', name: 'supplier_idx', index: 'supplier_idx', width: 150, sortable: false, is_use : true},
			{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, is_use : true},
			{label: '상품옵션코드', name: 'product_option_idx', index: 'STOCK.product_option_idx', width: 100, is_use : true},
			{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left', is_use : true},
			{label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left', is_use : true},
			{label: '품절', name: 'product_option_soldout', index: 'product_option_soldout', width: 60, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return (cellvalue == 'Y') ? '품절' : '';
				}},
			{label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return Common.addCommas(cellvalue);
				}},
			{label: '현재<br>정상재고', name: 'stock_amount_NORMAL_NOW', index: 'stock_amount_NORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="link btn-stock-control-status" ' +
						'data-product_option_idx="' + rowobject.product_option_idx + '"' +
						'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
						'data-stock_status="NORMAL"' +
						' >' + Common.addCommas(cellvalue) + '</a>';
				}},
			{label: '전체 재고량', name: 'stock_amount_IN_TOTAL', index: 'stock_amount_IN_TOTAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					try{
						return Common.addCommas(rowobject.stock_amount_NORMAL + rowobject.stock_amount_BAD + rowobject.stock_amount_ABNORMAL + rowobject.stock_amount_HOLD + rowobject.stock_amount_DISPOSAL);
					}catch(e){
						return '';
					}

				}},

			{label: '정상재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="link btn-stock-control-status" ' +
						'data-product_option_idx="' + rowobject.product_option_idx + '"' +
						'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
						'data-stock_status="NORMAL"' +
						' >' + Common.addCommas(cellvalue) + '</a>';
				}, cellattr: function(rowid, val, rowObject, cm, rdata){
					if(rowObject.stock_amount_normal > 0 && rowObject.product_option_warning_count > val ){
						return ' name="warning" ';
					}
				}},
			{label: '불량재고', name: 'stock_amount_BAD', index: 'stock_amount_BAD', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="link btn-stock-control-status" ' +
						'data-product_option_idx="' + rowobject.product_option_idx + '"' +
						'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
						'data-stock_status="BAD"' +
						' >' + Common.addCommas(cellvalue) + '</a>';
				}},
			{label: '양품재고', name: 'stock_amount_ABNORMAL', index: 'stock_amount_ABNORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="link btn-stock-control-status" ' +
						'data-product_option_idx="' + rowobject.product_option_idx + '"' +
						'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
						'data-stock_status="ABNORMAL"' +
						' >' + Common.addCommas(cellvalue) + '</a>';
				}},
			{label: '보류', name: 'stock_amount_HOLD', index: 'stock_amount_HOLD', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="link btn-stock-control-status" ' +
						'data-product_option_idx="' + rowobject.product_option_idx + '"' +
						'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
						'data-stock_status="HOLD"' +
						' >' + Common.addCommas(cellvalue) + '</a>';
				}},
			{label: '일반폐기', name: 'stock_amount_DISPOSAL', index: 'stock_amount_DISPOSAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="link btn-stock-control-status" ' +
						'data-product_option_idx="' + rowobject.product_option_idx + '"' +
						'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
						'data-stock_status="DISPOSAL"' +
						' >' + Common.addCommas(cellvalue) + '</a>';
				}},
		];

		if(typeof addColModel == "object"){
			colModel = colModel.concat(addColModel);
		}

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './stock_sum_list_grid.php',
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
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				productImgThumb();
				lightbox.option({
					'resizeDuration': 100,
					'fadeDuration': 200,
					'imageFadeDuration': 200,
					'albumLabel': "상품이미지 %1/%2",
				});

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
				Common.getGridColumnSizeFromStorage("stock_daily_sum", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_daily_sum");
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			//e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				StockDailySUMListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockDailySUMListSearch();
		});
	};

	/**
	 * 일자별 재고조회[누적재고] 목록/검색
	 * @constructor
	 */
	var StockDailySUMListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 일자별 재고조회[누적재고] 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockSUMInListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};
		var url = "stock_sum_list_xls_down.php?"+$.param(dataObj);

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("STOCK_SUM_LIST", function(){
				StockProduct.StockSUMInListXlsDownComplete();
			});
		}, 500);
	};

	var StockSUMInListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	//상품 이미지 리스트에서 썸네일 보기
	var productImgThumb = function(){
		$(".product_img_thumb").each(function(i, o) {
			var p_url = "/proc/_thumbnail.php";
			var dataObj = new Object();
			dataObj.file_idx = $(o).data("file_idx");
			dataObj.save_filename = $(o).data("filename");
			dataObj.width = 18;
			dataObj.height = 18;
			dataObj.is_crop = "Y";
			dataObj.force_create = "N";

			$.ajax({
				type: 'GET',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					//console.log(response);
					$(o).html('<img src="' + response.thumb.src + '" />');
				} else {
					//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});
	};

	/**
	 * 재고 작업 팝업 Open
	 * @param product_option_idx
	 * @param stock_control_type
	 * @constructor
	 */
	var StockControlPopOpen = function(product_option_idx, stock_control_type, stock_unit_price){
		Common.newWinPopup("stock_control_pop.php?product_option_idx="+product_option_idx+'&stock_control_type='+stock_control_type+'&stock_unit_price='+stock_unit_price, 'stock_control_pop', 720, 680, 'yes');
	};

	/**
	 * 재고작업 폼 진행 여부
	 * @type {boolean}
	 */
	var stockControlFormInt = false;

	/**
	 * 재고작업 변수 : 재고 선택 리스트
	 * @type {{LOSS: string, DISPOSAL: string, BAD: string, ABNORMAL: string, NORMAL: string, HOLD: string}}
	 */
	var stockControlSelectList = {
		NORMAL: '정상',
		ABNORMAL: '양품',
		HOLD: '보류',
		BAD: '불량재고',
		LOSS: '분실재고',
		DISPOSAL: '일반폐기'
	};

	/**
	 * 재고작업 변수 : 재고 처리 작업 가능 리스트
	 * @type {{LOSS: string, FAC_RETURN: string, DISPOSAL: string, BAD: string, ABNORMAL: string, DISPOSAL_PERMANENT: string, NORMAL: string, HOLD: string}}
	 */
	var stockControlTargetList = {
		NORMAL: '정상',
		ABNORMAL: '양품',
		HOLD: '보류',
		FAC_RETURN: '출고지회송',
		BAD: '불량',
		LOSS: '분실',
		DISPOSAL: '일반폐기',
		DISPOSAL_PERMANENT : '영구폐기'
	};

	/**
	 * 재고작업 변수 : 재고 처리 작업 가능 리스트 (sub)
	 * @type {{FAC_RETURN: {FAC_RETURN_BACK: string, FAC_RETURN_EXCHNAGE: string}, BAD: {BAD: string, BAD_OUT_EXCHANGE: string, BAD_OUT_RETURN: string}}}
	 */
	var stockControlTargetListSub = {
		BAD : {
			BAD : '불량재고',
			BAD_OUT_EXCHANGE : '교환출고',
			BAD_OUT_RETURN : '반품출고'
		},
		FAC_RETURN : {
			FAC_RETURN_EXCHNAGE : '교환회송',
			FAC_RETURN_BACK : '반품회송'
		}

	};

	/**
	 * 재고작업 팝업 페이지 초기화
	 * @constructor
	 */
	var StockControlPopInit = function(){



		//재고선택 - 원가(매입가) Selectbox 바인딩
		$(".stock_unit_price").on("change", function(){
			changeStockControlContent();
		});
		//재고선택 - 재고타입 SelectBox 바인딩
		$(".stock_control_status").on("change", function(){
			changeStockControlContent();
		});

		//재고처 작업 선택 바인딩
		$(".stock_status").on("change", function(){
			bindChangeStockStatus($(this));
		});

		changeStockControlContent();

		//Input Mask 바인딩
		$(".stock_amount").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 6, autoGroup: true, rightAlign: false});

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			if(stockControlFormInt) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.stock_amount, "수량을 정확히 입력해주세요.", 1, 10, null)) return returnType;

				if(parseInt(objForm.stock_amount.value) == 0){
					alert('수량은 0보다 커야합니다.');
					objForm.stock_amount.focus();
					return false;
				}

				if(parseInt(objForm.stock_amount.value) > parseInt($("#current_stock_amount").val())){
					alert("처리하려는 수량이 현재 재고수량 보다 큽니다.");
					return false;
				}

				if(!confirm('재고 변경작업을 실행하시겠습니까?')){
					return false;
				}

				this.action = "stock_control_proc.php";
				$("#btn-save").attr("disabled", true);
				stockControlFormInt = true;


			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 재고 수량(실시간)을 구해오는 함수
	 * (재고 선택 - 원가(매입가), 재고타입 변경 시)
	 */
	var changeStockControlContent = function(){

		var product_option_idx = $("#product_option_idx").val();
		var stock_unit_price = $(".stock_unit_price").val();
		var stock_control_status = $(".stock_control_status").val();

		var p_url = "/stock/stock_control_proc.php";
		var dataObj = new Object();
		dataObj.mode = "get_stock_amount_by_status";
		dataObj.product_option_idx = product_option_idx;
		dataObj.stock_status = stock_control_status;
		dataObj.stock_unit_price = stock_unit_price;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {

				//현재 재고 수량
				$("#current_stock_amount").val(response.data.current_stock_amount);
				$(".txt_stock_amount").html(Common.addCommas(response.data.current_stock_amount));

				//재고처리 작업 SelectBox reLoad
				$(".stock_status option").remove();

				//단순 'var 변수=변수' 할 경우 deep copy 가 되어버림
				//shallow copy 를 위해 jQuery 이용
				var tmp_target_list = $.extend({}, stockControlTargetList);

				//재고선택에서 선택된 타입 삭제
				delete tmp_target_list[stock_control_status];

				$.each(tmp_target_list, function(i, o){
					$(".stock_status").append('<option value="'+i+'">'+o+'</option>');
				});

				//재고처리 수량 0으로 변경
				$(".stock_amount").val(0);

			} else {
				alert(response.msg);
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});

	};

	/**
	 * 재고처리의 처리타입 선택 시 서브 Selectbox 구현 하는 함수
	 * 불량, 출고지회송 에 필요
	 * @param $obj
	 */
	var bindChangeStockStatus = function($obj){

		$target = $(".stock_status2");
		$("OPTION", $target).remove();

		if($obj.val() == "BAD" || $obj.val() == "FAC_RETURN"){
			$target.removeClass("dis_none");
			var tmp_list = stockControlTargetListSub[$obj.val()];

			$.each(tmp_list, function(i, o){
				$target.append('<option value="'+i+'">'+o+'</option>');
			});

		}else{
			$target.addClass("dis_none");
		}

		//재고처리 수량 0으로 변경
		$(".stock_amount").val(0);
	};

	/**
	 * 재고 로그 조회 팝업 Open
	 * @param product_option_idx
	 */
	var StockLogViewerPopOpen = function(product_option_idx,date_start,date_end){
		Common.newWinPopup("stock_log_viewer_pop.php?product_option_idx="+product_option_idx, 'stock_log_viewer_pop', 1024, 680, 'yes');
	};

    /**
     * 재고 로그 원가별 조회 팝업 Open
     * @param product_option_idx
     */
    var StockUnitPriceLogViewerPopOpen = function(product_option_idx,stock_unit_price,date_start,date_end){
        Common.newWinPopup("stock_log_viewer_pop.php?product_option_idx="+product_option_idx+"&stock_unit_price="+stock_unit_price+"&date_start="+date_start+"&date_end="+date_end, 'stock_log_viewer_pop', 1024, 680, 'yes');
    };

	/**
	 * 재고 로그 조회 팝업 페이지 초기화
	 * @constructor
	 */
	var StockLogViewerPopInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

		//Grid 초기화
		StockLogViewerPopGridInit();
	};

	/**
	 * 재고 로그 조회 팝업 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockLogViewerPopGridInit = function(){
		//재고 로그 조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './stock_log_viewer_grid.php',
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
				// { label: '로그IDX', name: 'stock_move_idx', index: 'A.stock_move_idx', width: 0, sortable: false, hidden: true},
				{ label: '일자', name: 'is_date', index: 'is_date', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '처리 전 상태', name: 'stock_status_prev_han', index: 'stock_status_prev_han', width: 80, sortable: false},
				{ label: '처리 후 상태', name: 'stock_status_next_han', index: 'stock_status_next_han', width: 80, sortable: false , formatter: function(cellvalue, options, rowobject){
						if(rowobject.stock_status_next_han == "기타") {
							if (rowobject.stock_type == "1") {
								return "수량변경 (+)";
							} else if (rowobject.stock_type == "-1") {
								return "수량변경 (-)";
							}
						} else {
							return cellvalue
						}
					}
				},
				{ label: '수량', name: 'stock_move_amount', index: 'stock_move_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '원가(매입가)', name: 'stock_unit_price', index: 'stock_unit_price', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 80, sortable: false},
				{ label: '재고메모', name: 'is_msg', index: 'is_msg', width: 200, sortable: false, align: 'left'}
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'is_date',
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
				StockDueDelayListSearch();
			}
		});

		//원가별 재고 수량 show
		if ($(".stock_unit_price").val() != "all"){
			$(".current_stock_amount_tr").css("display","");
		}

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockLogViewerPopSearch();
			showLoader();
			//원가별 재고 수량 -> 추후 타입별 추가
			var product_option_idx = $("#product_option_idx").val();
			var stock_unit_price = $(".stock_unit_price").val();
			var stock_control_status = "NORMAL";

			var p_url = "/stock/stock_control_proc.php";
			var dataObj = new Object();
			dataObj.mode = "get_stock_amount_by_status";
			dataObj.product_option_idx = product_option_idx;
			dataObj.stock_unit_price = stock_unit_price;
			dataObj.stock_status = stock_control_status;

			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					//현재 재고 수량
					$(".current_stock_amount").html(Common.addCommas(response.data.current_stock_amount));
					//원가별 재고 수량 hide
					if (stock_unit_price == "all"){
						$(".current_stock_amount_tr").css("display","none");
					} else {
						$(".current_stock_amount_tr").css("display","");
					}
				} else {
					alert(response.msg);
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			});

		});
	};

	/**
	 * 입고지연 변경이력 목록/검색
	 * @constructor
	 */
	var StockLogViewerPopSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var xlsValidRow = 0;                //업로드된 엑셀 Row 중 정상인 Row 수
	var xlsUploadedFileName = "";       //업로드 된 엑셀 파일명
	var xlsWritePageMode = "";          //일괄등록 / 일괄수정 Flag
	var xlsWriteReturnStyle = "";       //리스트 반환 또는 적용
	var xlsWriteInsertExcludeList = []; //반영 제외 리스트 xls_idx array
	/**
	 * 재고일괄조정 페이지 초기화
	 * @constructor
	 */
	var StockMoveXlsInit = function(){

		xlsWritePageMode = $("#xlswrite_mode").val();
		xlsWriteReturnStyle = $("#xlswrite_act").val();

		$(".btn-upload").on("click", function(){
			if($("input[name='xls_file']").val() == "")
			{
				alert("업로드 할 파일을 선택해주세요.");
				return false;
			}

			showLoader();
			$("#searchForm").submit();
		});

		$(".btn-xls-insert").on("click", function(){
			if(xlsValidRow < 1)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = xlsValidRow + "건의 데이터를 적용 하시겠습니까?";
				if(confirm(msg)) {
					StockMoveXlsInsert("stock_move_proc_xls.php");
				}
			}
		});

		StockMoveXlsGridInit();
	};

	/**
	 * 재고일괄조정 jqGrid 초기화
	 * @constructor
	 */
	var StockMoveXlsGridInit = function(){
		var validErr = [];

		$("#grid_list").jqGrid({
			url: './stock_move_proc_xls.php',
			mtype: "POST",
			datatype: "local",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: [
				{ label: '엑셀일련번호', name: 'xls_idx', index: 'xls_idx', width: 50, sortable: false, hidden: true},
				{ label: '상품코드', name: 'product_idx', index: 'product_idx', width: 80, sortable: false, hidden: true},
				{ label: '상품옵션코드', name: 'A', index: 'supplier_name', width: 100, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left'},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left'},
				{ label: '원가', name: 'B', index: 'stock_unit_price', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '현재고', name: 'current_stock_amount', index: 'current_stock_amount', width: 60, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '처리 전 상태', name: 'C', index: 'stock_move_status_prev', width: 120, sortable: false},
				{ label: '처리 후 상태', name: 'D', index: 'stock_move_status_next', width: 120, sortable: false},
				{ label: '작업수량', name: 'E', index: 'stock_move_amount', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '재고메모', name: 'F', index: 'stock_move_msg', width: 150, sortable: false},
				{ label: '비고', name: 'valid', index: 'valid', width: 150, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;
						if(cellvalue)
						{
							rst = "정상";
							xlsValidRow++;
						}else{
							rst = "오류";
							validErr.push(options.rowId);
						}
						return rst;
					}
				},
			],
			rowNum:1000,
			pager: '#grid_pager',
			sortname: 'regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				$.each(validErr, function(k, v){
					$("#grid_list #"+v).addClass("upload_err");
					validErr = [];
				});
			},
			beforeRequest: function(){
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	/**
	 * 업로드 된 엑셀 파일 로딩
	 * @param xls_file_path_name
	 * @constructor
	 */
	var StockMoveXlsRead = function(xls_file_path_name){
		//console.log(xls_file_path_name);
		xlsUploadedFileName = xls_file_path_name;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				mode: xlsWritePageMode,
				act: xlsWriteReturnStyle,
				xls_filename: xls_file_path_name,
				operation_date: $("#operation_date").val(),
				operation_time: $("#operation_time").val()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 업로드 된 엑셀 파일 적용
	 * @constructor
	 */
	let StockMoveXlsInsert = function(url){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		let p_url = url;
		let dataObj = {};
		dataObj.mode = xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = xlsUploadedFileName;
		dataObj.xls_validrow = xlsValidRow;
		dataObj.exclude_list = xlsWriteInsertExcludeList.join(",");

		if ($("#operation_date").length) dataObj.operation_date = $("#operation_date").val();
		if ($("#operation_time").length) dataObj.operation_time = $("#operation_time").val();

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj,
			traditional: true
		}).done(function (response) {
			hideLoader();

			if(response.result) {
				alert(response.msg+"건이 정상 적용 되었습니다.");
				location.reload();
			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
		}).fail(function(jqXHR, textStatus){
			hideLoader();

			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
		});
	};

	let changeAmountInit = function() {
		xlsWritePageMode = $("#xls_write_mode").val();
		xlsWriteReturnStyle = $("#xls_write_act").val();

		$(".btn-upload").on("click", function(){
			if($("input[name='xls_file']").val() == "") {
				alert("업로드 할 파일을 선택해주세요.");
				return false;
			}

			showLoader();
			$("#searchForm").submit();
		});

		$(".btn-xls-insert").on("click", function(){
			if(xlsValidRow < 1) {
				alert("적용할 데이터가 없습니다.");
				return;
			}

			let msg = xlsValidRow + "건의 데이터를 적용 하시겠습니까?";
			if(confirm(msg)) {
				StockMoveXlsInsert("stock_change_amount_proc.php");
			}
		});

		//grid
		let validErr = [];

		$("#grid_list").jqGrid({
			url: '/stock/stock_change_amount_proc.php',
			mtype: "POST",
			datatype: "local",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: [
				{ label: '엑셀일련번호', name: 'xls_idx', index: 'xls_idx', width: 50, sortable: false, hidden: true},
				{ label: '상품코드', name: 'product_idx', index: 'product_idx', width: 80, sortable: false, hidden: true},
				{ label: '상품옵션코드', name: 'A', index: 'product_option_idx', width: 100, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left'},
				{ label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left'},
				{ label: '원가', name: 'B', index: 'stock_unit_price', width: 80, sortable: false, align: 'right', formatter: function(cellValue, options, rowObject){
						return Common.addCommas(cellValue);
					}},
				{ label: '상태', name: 'C', index: 'stock_status', width: 80, sortable: false, align: 'left'},
				{ label: '현재고', name: 'current_stock_amount', index: 'current_stock_amount', width: 50, sortable: false, align: 'center', formatter: function(cellValue, options, rowObject){
						return Common.addCommas(cellValue);
					}},
				{ label: '작업수량', name: 'D', index: 'changed_stock_amount', width: 50, sortable: false, align: 'center', formatter: function(cellValue, options, rowObject){
						return Common.addCommas(cellValue);
					}},
				{ label: '메모', name: 'E', index: 'stock_msg', width: 150, sortable: false},
				{ label: '비고', name: 'valid', index: 'valid', width: 100, sortable: false
					, formatter: function(cellValue, options, rowObject){
						let rst;
						if(cellValue) {
							rst = "정상";
							xlsValidRow++;
						} else {
							rst = "오류";
							validErr.push(options.rowId);
						}
						return rst;
					}
				},
			],
			rowNum:10000,
			pager: '#grid_pager',
			sortname: 'regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				$.each(validErr, function(k, v){
					$("#grid_list #"+v).addClass("upload_err");
					validErr = [];
				});
			},
			beforeRequest: function(){
			}
		});

		//브라우저 리사이즈 시 jqGrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	return {
		StockProductListInit: StockProductListInit,
		StockProductListReLoad: StockProductListSearch,
		StockListInit: StockListInit,
		StockListReLoad: StockListSearch,
		StockPeriodListInit: StockPeriodListInit,
		StockPeriodListReLoad: StockPeriodListSearch,
		StockDailyInListInit: StockDailyInListInit,
		StockDailyInListReLoad: StockDailyInListSearch,
		StockDailyDetailInit: StockDailyDetailInit,
		StockDailySUMListInit: StockDailySUMListInit,
		StockDailySUMListReLoad: StockDailySUMListSearch,
		StockControlPopInit: StockControlPopInit,
		StockLogViewerPopInit: StockLogViewerPopInit,
		StockMoveXlsInit: StockMoveXlsInit,
		StockMoveXlsRead: StockMoveXlsRead,
		StockProductListXlsDownComplete: StockProductListXlsDownComplete,
		StockListXlsDownComplete: StockListXlsDownComplete,
		StockPeriodListXlsDownComplete: StockPeriodListXlsDownComplete,
		StockDailyInListXlsDownComplete: StockDailyInListXlsDownComplete,
		StockSUMInListXlsDownComplete: StockSUMInListXlsDownComplete,
		changeAmountInit: changeAmountInit,
	}
})();