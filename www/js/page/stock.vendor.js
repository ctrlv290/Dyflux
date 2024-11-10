/*
 * 벤더사용 재고 관리 js
 */
var StockVendor = (function() {
	var root = this;

	var init = function () {
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
			url: './stock_list_vendor_grid.php',
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
			{label: '상품옵션코드', name: 'product_option_idx', index: 'STOCK.product_option_idx', width: 100, is_use : true},
			{label: '이미지', name: 'product_img', index: 'product_img', width: 60, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){

					var tmp = "";

					if(rowobject.product_img_main > 0)
					{
						var main_img = eval('rowobject.product_img_'+rowobject.product_img_main);
						var main_img_file = eval('rowobject.product_img_filename_'+rowobject.product_img_main);

						if(main_img)
						{
							//console.log(rowobject.product_idx, main_img_file);
							tmp = '<a href="/_data/product/'+ main_img_file +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_option_idx+'" data-file_idx="'+main_img+'" data-filename="'+main_img_file+'"></a>';
						}
					}

					// if(rowobject.product_img_1) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_1 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_1+'" data-filename="'+rowobject.product_img_filename_1+'"></a>';
					// if(rowobject.product_img_2) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_2 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_2+'" data-filename="'+rowobject.product_img_filename_2+'"></a>';
					// if(rowobject.product_img_3) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_3 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_3+'" data-filename="'+rowobject.product_img_filename_3+'"></a>';
					// if(rowobject.product_img_4) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_4 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_4+'" data-filename="'+rowobject.product_img_filename_4+'"></a>';
					// if(rowobject.product_img_5) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_5 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_5+'" data-filename="'+rowobject.product_img_filename_5+'"></a>';
					// if(rowobject.product_img_6) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_6 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_6+'" data-filename="'+rowobject.product_img_filename_6+'"></a>';
					return tmp;

				}},
			{label: '상품명+옵션명', name: 'product_full_name', index: 'product_full_name', width: 400, sortable: false, align: 'left', is_use : true, formatter: function(cellvalue, options, rowobject){

					return rowobject.product_name + ' / ' + rowobject.product_option_name
				}},
			{label: '정상재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 120, sortable: false, is_use : true, align: 'right', formatter: 'integer', cellattr: function(rowid, val, rowObject, cm, rdata){
					if(rowObject.stock_amount_normal > 0 && rowObject.product_option_warning_count > val ){
						return ' name="warning" ';
					}
				}},
		],
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
				Common.getGridColumnSizeFromStorage("stock_list_vendor", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_list_vendor");
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

	return {
		StockListInit: StockListInit,
	}
})();