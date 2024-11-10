/*
 * 상품 휴지통 관리 js
 */
var ProductTrash = (function() {
	var root = this;

	var xlsDownIng = false;
	var xlsDownInterval;

	var init = function() {
	};

	//상품 휴지통 목록 초기화
	var ProductTrashListInit = function(){

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


		//선택 복구 버튼 바인딩
		$(".btn-restore-product").on("click", function(){
			var rowData = $("#grid_list").getRowData();
			var idx_list = [];
			$.each(rowData, function(i, o){
				if($("#jqg_grid_list_" + (i + 1)).is(":checked")){
					idx_list.push(o.product_idx);
				}
			});

			if(confirm('선택하신 상품들을 복구 하시겠습니까?')){
				var p_url = "/product/product_proc.php";
				var dataObj = new Object();
				dataObj.mode = "product_restore_trash";
				dataObj.product_idx_list = idx_list.join(',');
				showLoader();
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: dataObj
				}).done(function (response) {
					if (response.result) {
						alert('복구 되었습니다.');
						ProductTrashListSearch();
					} else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
				}).fail(function (jqXHR, textStatus) {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
				});
			}
		});

		//엑셀 다운로드
		$(".btn-trash-product-xls-down").on("click", function(){
			ProductTrashXlsDown();
		});

		//상품 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './product_trash_list_grid.php',
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

				{label: '삭제일', name: 'product_is_trash_date', index: 'product_is_trash_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}
				},
				{label: '상품코드', name: 'product_idx', index: 'product_idx', width: 100, is_use : true},
				{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){

						return '<a href="product_write.php?product_idx='+rowobject.product_idx+'">'+cellvalue+'</a>';
					}},
				{label: '이미지', name: 'product_img', index: 'product_img', width: 150, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){

						var tmp = "";
						if(rowobject.product_img_1) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_1 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_1+'" data-filename="'+rowobject.product_img_filename_1+'"></a>';
						if(rowobject.product_img_2) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_2 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_2+'" data-filename="'+rowobject.product_img_filename_2+'"></a>';
						if(rowobject.product_img_3) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_3 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_3+'" data-filename="'+rowobject.product_img_filename_3+'"></a>';
						if(rowobject.product_img_4) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_4 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_4+'" data-filename="'+rowobject.product_img_filename_4+'"></a>';
						if(rowobject.product_img_5) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_5 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_5+'" data-filename="'+rowobject.product_img_filename_5+'"></a>';
						if(rowobject.product_img_6) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_6 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_6+'" data-filename="'+rowobject.product_img_filename_6+'"></a>';
						return tmp;

					}},
				{label: '판매타입', name: 'product_sale_type', index: 'product_sale_type', width: 150, sortable: false, is_use : true},
				{label: '카테고리', name: 'product_category', index: 'product_category', width: 150, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){

						return rowobject.category_l_name + '>' + rowobject.category_m_name;
					}},
				{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, is_use : true},
				{label: '공급처상품명', name: 'product_supplier_name', index: 'product_supplier_name', width: 150, sortable: false, is_use : true},
				{label: '등록일', name: 'product_regdate', index: 'product_regdate', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}
				},
				{label: '벤더사노출', name: 'product_vendor_show', index: 'product_vendor_show', width: 150, sortable: false, is_use : true},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'product_is_trash_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			multiselect: true,
			loadComplete: function(){
				$(".check_all").on("click", function(){
					if(!$(".check_all").is(":checked")){
						$(".check_all").prop("checked", true);
					}else{
						$(".check_all").prop("checked", false);
					}
					$(".is_use").prop("checked", $(this).is(":checked"));
				});

				productImgThumb();
				lightbox.option({
					'resizeDuration': 100,
					'fadeDuration': 200,
					'imageFadeDuration': 200,
					'albumLabel': "상품이미지 %1/%2",
				})
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
				ProductListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ProductTrashListSearch();
		});
	};

	//상품 휴지통 목록/검색
	var ProductTrashListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	//상품 휴지통  엑셀 다운로드
	var ProductTrashListXlsDown = function(){
		var param = $("#searchForm").serialize();
		location.href="seller_xls_down.php?"+param;
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
	 * 휴지통 엑셀 다운로드
	 * @constructor
	 */
	var ProductTrashXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "product_trash_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_PRODUCT_TRASH_LIST", function(){
				ProductTrash.ProductTrashXlsDownComplete();
			});
		}, 500);
	};

	var ProductTrashXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	return {
		ProductTrashListInit : ProductTrashListInit,
		ProductTrashXlsDownComplete : ProductTrashXlsDownComplete,
	}
})();