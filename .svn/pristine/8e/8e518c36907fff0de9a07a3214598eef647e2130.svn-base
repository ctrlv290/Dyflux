/*
 * 상품관리 js
 */
var Product = (function() {
	var root = this;

	var init = function() {
	};

	//상품 등록 수정 함수
	var ProductWritePage = function(product_idx) {
		var url = '/product/product_write.php';
		url += (product_idx != "" && typeof product_idx !== 'undefined') ? '?product_idx=' + product_idx : '';
		var param = $("#searchForm").serialize();
		url += "&param="+encodeURIComponent(param);
		location.href=url;
	};

	//상품목록 초기화
	var ProductListInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "9");

		//공급처 그룹 및 공급처 선택창 초기화
		bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
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
			Common.newWinPopup("/common/column_setting_pop.php?target=PRODUCT_LIST&mode=list", 'column_setting_pop', 700, 720, 'no');
		});

		//엑셀 다운로드
		$(".btn-product-xls-down").on("click", function(){
			ProductListXlsDown();
		});

		//변경 이력 팝업
		$(".btn-change-log-viewer-pop").on("click", function(){
			Common.changeLogViewerPopup("product");
		});

		//벤더사 체크
		var vendorColAry = new Array();
		var autoWidth = true;
		var shrinkToFit = false;
		if(!isDYLogin){

			autoWidth = false;
			shrinkToFit = true;

			$.each(_gridColModel, function(i, o){

				console.log(o.name);
				if(o.name != "product_vendor_show" && o.name != "supplier_name" && o.name != "product_supplier_name" && o.name != "product_sale_type") {
					vendorColAry.push(o);
				}
			});

			_gridColModel = vendorColAry;
		}

		//상품 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './product_list_grid.php',
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
			sortname: 'product_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: autoWidth,
			rownumbers: true,
			rownumWidth: 50,
			shrinkToFit: shrinkToFit,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//수정
				$(".btn-product-modify").on("click", function(){
					ProductWritePage($(this).data("idx"));
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
			ProductListSearch();
		});
	};

	//상품 목록/검색
	var ProductListSearch = function(){
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
	//상품 엑셀 다운로드
	var ProductListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		if($(".chk-include-product-option").eq(0).is(":checked")){
			$("#include_option").val("Y");
		}else{
			$("#include_option").val("N");
		}

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};
		var url = "product_list_xls_down.php?"+$.param(dataObj);

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);
		// $("#hidden_ifrm_common_filedownload").attr("src", "product_list_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("PRODUCT_LIST", function(){
				Product.ProductListXlsDownComplete();
			});
		}, 500);
	};

	var ProductListXlsDownComplete = function(){
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

	//상품 등록/수정 초기화
	var ProductWriteInit = function(){

		//공급처 그룹 및 공급처 선택창 초기화
		bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
		//판매처 그룹 및 판매처 선택창 초기화
		bindManageGroupList("SELLER_ALL_GROUP", ".product_seller_group_idx", ".seller_idx");

		//공급처 검색 버튼 바인딩
		$(".btn-supplier-search-pop").on("click", function(){
			Common.newWinPopup("/info/supplier_search_pop.php", 'supplier_search_pop', 700, 720, 'yes');
		});

		//판매처 검색 버튼 바인딩
		$(".btn-seller-search-pop").on("click", function(){
			Common.newWinPopup("/info/seller_search_pop.php", 'seller_search_pop', 700, 720, 'yes');
		});

		//상품정보고시 셀렉트 박스 change 바인딩
		$(".product_notice_idx").on("change", function(){
			//console.log("TEST");
			bindProductNoticeTitle($(this).val());

		});

		//상품정보고시 "신규등록" 버튼 바인딩
		$(".btn-product-notice-write-pop").on("click", function(){
			ProductNotice.ProductNoticeWritePopup();
		});

		//카테고리관리 버튼 팝업
		$(".btn-category-list-pop").on("click", function(){
			Category.CategoryListPopup();
		});

		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn_product_img_1', {
			_target_table : 'DY_PRODUCT',
			_target_table_column : 'product_img_1',
			_target_filename : '',
			_target_input_hidden : '#product_img_1',
			_upload_no: 1,
			_upload_type : "product",
			_upload_delete_btn : "btn_product_img_1_delete",
			_onComplete : function(path, filename, file_idx){
				if(file_idx != "" && file_idx != 0) {
					productImgLoad(filename, file_idx, '.img_product_img_1');
					$(".product_img_main").eq(0).prop("disabled", false);
				}
			},
			_onDeleted : function(file_idx) {
				$(".img_product_img_1").empty();
			}
		});

		var file2 = new FileUpload2('btn_product_img_2', {
			_target_table : 'DY_PRODUCT',
			_target_table_column : 'product_img_2',
			_target_filename : '',
			_target_input_hidden : '#product_img_2',
			_upload_no: 1,
			_upload_type : "product",
			_upload_delete_btn : "btn_product_img_2_delete",
			_onComplete : function(path, filename, file_idx){
				if(file_idx != "" && file_idx != 0) {
					productImgLoad(filename, file_idx, '.img_product_img_2');
					$(".product_img_main").eq(1).prop("disabled", false);
				}
			},
			_onDeleted : function(file_idx) {
				$(".img_product_img_2").empty();
			}
		});

		var file3 = new FileUpload2('btn_product_img_3', {
			_target_table : 'DY_PRODUCT',
			_target_table_column : 'product_img_3',
			_target_filename : '',
			_target_input_hidden : '#product_img_3',
			_upload_no: 1,
			_upload_type : "product",
			_upload_delete_btn : "btn_product_img_3_delete",
			_onComplete : function(path, filename, file_idx){
				if(file_idx != "" && file_idx != 0) {
					productImgLoad(filename, file_idx, '.img_product_img_3');
					$(".product_img_main").eq(2).prop("disabled", false);
				}
			},
			_onDeleted : function(file_idx) {
				$(".img_product_img_3").empty();
			}
		});

		var file4 = new FileUpload2('btn_product_img_4', {
			_target_table : 'DY_PRODUCT',
			_target_table_column : 'product_img_4',
			_target_filename : '',
			_target_input_hidden : '#product_img_4',
			_upload_no: 1,
			_upload_type : "product",
			_upload_delete_btn : "btn_product_img_4_delete",
			_onComplete : function(path, filename, file_idx){
				if(file_idx != "" && file_idx != 0) {
					productImgLoad(filename, file_idx, '.img_product_img_4');
					$(".product_img_main").eq(3).prop("disabled", false);
				}
			},
			_onDeleted : function(file_idx) {
				$(".img_product_img_4").empty();
			}
		});

		var file5 = new FileUpload2('btn_product_img_5', {
			_target_table : 'DY_PRODUCT',
			_target_table_column : 'product_img_5',
			_target_filename : '',
			_target_input_hidden : '#product_img_5',
			_upload_no: 1,
			_upload_type : "product",
			_upload_delete_btn : "btn_product_img_5_delete",
			_onComplete : function(path, filename, file_idx){
				if(file_idx != "" && file_idx != 0) {
					productImgLoad(filename, file_idx, '.img_product_img_5');
					$(".product_img_main").eq(4).prop("disabled", false);
				}
			},
			_onDeleted : function(file_idx) {
				$(".img_product_img_5").empty();
			}
		});

		var file6 = new FileUpload2('btn_product_img_6', {
			_target_table : 'DY_PRODUCT',
			_target_table_column : 'product_img_6',
			_target_filename : '',
			_target_input_hidden : '#product_img_6',
			_upload_no: 1,
			_upload_type : "product",
			_upload_delete_btn : "btn_product_img_6_delete",
			_onComplete : function(path, filename, file_idx){
				if(file_idx != "" && file_idx != 0) {
					productImgLoad(filename, file_idx, '.img_product_img_6');
					$(".product_img_main").eq(5).prop("disabled", false);
				}
			},
			_onDeleted : function(file_idx) {
				$(".img_product_img_6").empty();
			}
		});

		//대표 이미지 설정
		//다른 이미지를 대표로 설정 시 이미 설정 되어 있던 Checked : False
		//하나의 이미지만 대표로 설정 가능 하도록
		$(".product_img_main").on("click", function(){
			if($(".product_img_main").is(":checked")){
				$(".product_img_main").not($(this)).prop("checked", false);
			}
		});

		//쇼핑몰 상세페이지
		productDetailInit();

		//벤더사 노출 설정
		productVendorShowInit();

		//삭제 버튼 이벤트 바인딩
		$(".btn-product-delete").on("click", function(){
			var idx = $(this).data("idx");
			ProductDeletePop(idx);
		});

		//폼 바인딩
		bindWriteForm();
	};

	//상품 이미지 업로드 후 바인딩 (미리보기)
	var productImgLoad = function(filename, file_idx, target){
		var p_url = "/proc/_thumbnail.php";
		var dataObj = new Object();
		dataObj.file_idx = file_idx;
		dataObj.save_filename = filename;
		dataObj.width = 118;
		dataObj.height = 118;
		dataObj.is_crop = "N";
		dataObj.force_create = "N";

		$.ajax({
			type: 'GET',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				//console.log(response);
				$(target).html('<img src="'+response.thumb.src+'" />');
			}else{
				//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				//alert('이미지 썸네일 표시 오류 : thumbnail return false');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			//alert('이미지 썸네일 표시 오류 : thumbnail fail');
			hideLoader();
		});
	};

	//상품 상세페이지 초기화/바인딩
	var productDetailInit = function(){
		$(".btn-product-detail-add-row").on("click", function(){
			var name = $(".dmy_product_detail_mall_name").eq(0).val().trim();
			var url = $(".dmy_product_detail_url").eq(0).val().trim();

			if(name == "" || url == "") {
				alert("쇼핑몰 이름과 URL을 입력해 주세요.");
				return;
			}else if(url.indexOf("http://") !== 0 && url.indexOf("https://") !== 0){
				alert("URL 은 http:// 또는 https:// 로 시작해야 합니다.");
				return;
			}else{
				productDetailAdd(name, url);
			}
		});

		$(".table_product_detail").on("click", ".btn-product-detail-delete-row", function(){
			$(this).parent().parent().remove();
		});
	};

	//상품 상세페이지 등록/삭제
	var productDetailAdd = function(mall_name, mall_url){
		var $_row = $(".table_product_detail tr:last-child").clone().removeClass("dis_none");
		$_row.find(".spn_product_detail_mall_name").text(mall_name);
		$_row.find(".product_detail_mall_name").val(mall_name);
		$_row.find(".spn_product_detail_url").html('<a href="'+mall_url+'" target="_blank">'+mall_url+'</a>');
		$_row.find(".product_detail_url").val(mall_url);
		$(".table_product_detail tr:last-child").before($_row);

		$(".dmy_product_detail_mall_name").eq(0).val("")
		$(".dmy_product_detail_url").eq(0).val("")
	};

	//상품 등록/수정 폼 초기화
	var writeFormIng = false;
	var bindWriteForm = function () {
		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("#goto_option").val("N"); //저장 후 옵션추가 페이지로 이동 초기화
			$("form[name='dyForm']").submit();
		});
		$("#btn-save-and-go").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("#goto_option").val("Y"); //저장 후 옵션추가 페이지로 이동
			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			if(writeFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.supplier_idx, "공급처를 선택해주세요.", 1, 20, null)) return returnType;
				if (!valForm.chkValue(objForm.product_name, "상품명을 정확히 입력해주세요.", 1, 100, null)) return returnType;
				// if (!valForm.chkValue(objForm.market_login_pw, "로그인 비밀번호를 정확히 입력해주세요.", 1, 50, null)) return returnType;

				if($("input[name='product_vendor_show']:checked").val() == "SHOW"){
					if($("input[name='product_vendor_show_type']:checked").val() == "SELECTED"){
						if($("input[name='product_vendor_show_list']").val() == "")
						{
							alert("벤더사를 선택해주세요.");
							return false;
						}
					}
				}

				this.action = "product_proc.php";
				$("#btn-save").attr("disabled", true);
				writeFormIng = true;

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	//상품 삭제 Modal Popup
	var ProductDeletePop = function(product_idx){
		var p_url = "product_delete.php";
		var dataObj = new Object();
		dataObj.product_idx = product_idx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_product_delete").html(response);
				$("#modal_product_delete").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	//상품 삭제 Modal Popup Close
	var ProductDeletePopClose = function() {
		$("#modal_product_delete").dialog( "close" );
	};

	//상품 삭제 Modal Popup 초기화
	var ProductDeleteInit = function(){

		//상품 삭제 팝업 - 취소 버튼 이벤트 바인딩
		$("#btn-delete-product").on("click", function(){
			ProductDelete($(this).data("idx"));
		});
		//상품 삭제 팝업 - 취소 버튼 이벤트 바인딩
		$(".btn-product-delete-close").on("click", function(){
			ProductDeletePopClose();
		});
	};

	//상품 삭제
	var ProductDelete = function(product_idx){
		var p_url = "/product/product_proc.php";
		var dataObj = new Object();
		dataObj.mode = "product_goto_trash";
		dataObj.product_idx = product_idx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {
				alert('삭제되었습니다.');
				location.replace("product_list.php");
			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	//상품 옵션 목록 초기화
	var ProductOptionListInit = function(){

		//전체품절처리 버튼 바인딩
		$(".btn-product-option-soldout-all-y").on("click", function(){
			ProductOptionAllSoldOut($(this).data("idx"), "Y");
		});

		//전체판매가능처리 버튼 바인딩
		$(".btn-product-option-soldout-all-n").on("click", function(){
			ProductOptionAllSoldOut($(this).data("idx"), "N");
		});

		//옵션일괄추가(엑셀) 버튼 바인딩
		$(".btn-product-option-xls-write").on("click", function(){
			ProductOptionWriteXlsPopOpen($(this).data("idx"), "add");
		});

		//옵션일괄수정(엑셀) 버튼 바인딩
		$(".btn-product-option-xls-modify").on("click", function(){
			ProductOptionWriteXlsPopOpen($(this).data("idx"), "mod");
		});

		//상품 옵션 목록 jqGrid 초기화
		ProductOptionListGridInit();

		//옵션 추가 모달팝업 세팅
		$( "#modal_product_option_write" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		//옵션 일괄 추가 모달팝업 세팅
		$( "#modal_product_option_write_xls" ).dialog({
			width: '80%',
			height: 680,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) {
				windowScrollHide();
				$(window).trigger("resize");
			},
			close : function(event, ui) { windowScrollShow(); },
		});

		//옵션 삭제 모달팝업 세팅
		$( "#modal_product_option_delete" ).dialog({
			width: 500,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		//상품 삭제 모달팝업 세팅
		$( "#modal_product_delete" ).dialog({
			width: 500,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		//옵션 추가 버튼 바인딩
		$(".btn-product-option-write").on("click", function(){
			ProductOptionWritePopOpen($(this));
		});

		// 공통 폼 모달 팝업 세팅
		Common.registCommonInputModalPop();
	};

	//상품 옵션 추가 모달 팝업 Open
	var ProductOptionWritePopOpen = function($obj){
		var p_url = "product_option_write.php";
		var dataObj = new Object();
		dataObj.product_idx = $obj.data("idx");

		if(typeof $obj.data("option-idx") != "undefined"){
			dataObj.product_option_idx = $obj.data("option-idx");
		}

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_product_option_write").html(response);
				$("#modal_product_option_write").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	//상품 옵션 추가 Modal Popup Close
	var ProductOptionWritePopClose = function() {
		$("#modal_product_option_write").dialog( "close" );
	};

	//상품 옵션 일괄 등록 Modal Popup Open
	var ProductOptionWriteXlsPopOpen = function(product_idx, mode){
		var p_url = "product_option_write_xls.php";
		var dataObj = new Object();
		dataObj.product_idx = product_idx;
		dataObj.mode = mode;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_product_option_write_xls").html(response);
				$("#modal_product_option_write_xls").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});

	};

	//상품 옵션 일괄 등록 Modal Popup Close
	var ProductOptionWriteXlsPopClose = function(){
		$("#modal_product_option_write_xls").dialog( "close" );
	};

	//상품 옵션 등록/수정 창 초기화
	var ProductOptionWriteInit = function(){

		//옵션창 닫기 버튼 바인딩
		$(".btn-product-option-write-close").on("click", function(){
			ProductOptionWritePopClose();
		});

		//옵션 입력 창 Selectize 적용
		$('.product_selectize').selectize({
			plugins: ['remove_button'],
			delimiter: ',',
			persist: false,
			createOnBlur: true,
			create: function(input) {
				return {
					value: input,
					text: input
				}
			},
			createFilter: function(input){
				return (input.indexOf(' ') == -1) ? 1 : 0;
			},
			onDropdownOpen: function() {
				this.close();
			},
			onChange: function(o){
				ProductOptionMixEvent();
			}
		});

		//옵션 입력창 Space 입력 불가 설정
		$(".product_selectize").on("keypress", function(event){

			if(event.which && event.which == 32) {
				event.preventDefault();
			}
		});

		//판매가 할인율 적용 버튼 바인딩
		$(".btn-product-option-sale-price-calculate").on("click", function(){
			ProductOptionSalePriceCal();
		});

		//판매가 Input Mask 바인딩
		$(".product_option_sale_price_mask, .product_option_purchase_price").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 2, autoGroup: true, rightAlign: false});

		// $(".product_option_sale_price_mask, .product_option_sale_price_cal_per").on("keyup", function(){});

		//수정일 경우 판매기준가 - 판매가 할인율 역계산 세팅
		if($("form[name='dyForm2']").hasClass("mod")) {
			$(".product_option_sale_price_cal").each(function (i, o) {
				//판매기준가
				var d_price = $("input[name='product_option_sale_price_default']").val();
				if (typeof d_price != "undefined") {
					d_price = d_price.replace(/,/gi, '');
					if ($.isNumeric(d_price)) {

						//현재가격
						var cur_price = $(this).val();
						if (typeof cur_price != "undefined") {
							if ($.isNumeric(cur_price)) {
								var per = Math.ceil(((d_price-cur_price) / d_price) * 100);
								$(".product_option_sale_price_cal_per").eq(i).val(per);
							}
						}

					}
				}

			});
		}

		//상품 옵션 등록/수정 폼 바인딩
		ProductOptionWriteFormInit();
	};

	//상품 옵션 등록/수정 진행 여부 변수
	var _ProductOptionWriteIng = false;

	//상품 옵션 등록/수정 폼 바인딩
	var ProductOptionWriteFormInit = function(){

		//저장 버튼
		$("#btn-save-option").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if(!_ProductOptionWriteIng) {
				$("form[name='dyForm2']").submit();
			}
		});

		//폼 Submit 이벤트
		$("form[name='dyForm2']").submit(function(e){
			e.preventDefault();
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{

				if(typeof objForm.product_option_mix_1 !== 'undefined'){
					if (!valForm.chkValue(objForm.product_option_mix_1, "옵션1을 정확히 입력해주세요.", 1, 500, null)) return returnType;
				}

				if (!valForm.chkValue(objForm.product_option_sale_price_default, "판매기준가를 정확히 입력해주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_A, "판매기준가를 입력후 할인율 적용 버튼을 클릭해주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_A_per, "판매가1의 할인율을 정확히 입력해주세요.", 1, 3, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_B, "판매기준가를 입력후 할인율 적용 버튼을 클릭해주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_B_per, "판매가2의 할인율을 정확히 입력해주세요.", 1, 3, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_C, "판매기준가를 입력후 할인율 적용 버튼을 클릭해주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_C_per, "판매가3의 할인율을 정확히 입력해주세요.", 1, 3, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_D, "판매기준가를 입력후 할인율 적용 버튼을 클릭해주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_D_per, "판매가4의 할인율을 정확히 입력해주세요.", 1, 3, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_E, "판매기준가를 입력후 할인율 적용 버튼을 클릭해주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_sale_price_E_per, "판매가5의 할인율을 정확히 입력해주세요.", 1, 3, null)) return returnType;

				if(objForm.product_option_sale_price_A.value == "0")
				{
					alert("판매가1은 0원일 수 없습니다.");
					return false;
				}
				if(objForm.product_option_sale_price_B.value == "0")
				{
					alert("판매가2은 0원일 수 없습니다.");
					return false;
				}
				if(objForm.product_option_sale_price_C.value == "0")
				{
					alert("판매가3은 0원일 수 없습니다.");
					return false;
				}
				if(objForm.product_option_sale_price_D.value == "0")
				{
					alert("판매가4은 0원일 수 없습니다.");
					return false;
				}
				if(objForm.product_option_sale_price_E.value == "0")
				{
					alert("판매가5은 0원일 수 없습니다.");
					return false;
				}

				_ProductOptionWriteIng = true;
				showLoader();
				var p_url = "product_option_proc.php";
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: $("form[name='dyForm2']").serialize()
				}).done(function (response) {
					if(response.result)
					{
						alert('저장되었습니다.');
						ProductOptionWritePopClose();
						//Option List Grid reLoad
						ProductOptionListSearch();

					}else{
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
					_ProductOptionWriteIng = false;
				}).fail(function(jqXHR, textStatus){
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
					_ProductOptionWriteIng = false;
				});
				return false;

			}catch(e){
				alert(e);
				_ProductOptionWriteIng = false;
				return false;
			}
		});
	};

	//상품 옵션 목록 바인딩 jqGrid
	let ProductOptionListGridInit = function(){
		let grid = $("#grid_list");
		let searchForm = $("#searchForm");

		let _colModel = [
			{ label: '옵션<br>수정/삭제', name: 'btnz', index: 'btnz', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="xsmall_btn btn-product-option-modify" data-idx="'+rowobject.product_idx+'" data-option-idx="'+rowobject.product_option_idx+'">수정</a>'
						+ ' <a href="javascript:;" class="xsmall_btn btn-product-option-delete" data-idx="'+rowobject.product_option_idx+'">삭제</a>';
				}},
			{ label: '상품코드<br>(옵션)', name: 'product_option_idx', index: 'product_option_idx', width: 75, sortable: true},
			{ label: '품절', name: 'product_option_soldout', index: 'product_option_soldout', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
					return '<label class="dType red"><input type="checkbox" class="dType chk-product_option_soldout" name="" value="Y" data-idx="' + rowobject.product_option_idx + '" ' + ((cellvalue == "Y") ? 'checked' : '') + ' /><span></span></label>';
				}},
			{ label: '일시<br>품절', name: 'product_option_soldout_temp', index: 'product_option_soldout_temp', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
					return '<label class="dType blue"><input type="checkbox" class="dType chk-product_option_soldout_temp" name="" value="Y" data-idx="' + rowobject.product_option_idx + '" ' + ((cellvalue == "Y") ? 'checked' : '') + ' /><span></span></label>';
				}},
			{ label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 200, sortable: true},
			{ label: vendor_grade_name_list.A + '<br>판매가', name: 'product_option_sale_price_A', index: 'product_option_sale_price_A', price : "A", width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
					return Common.addCommas(cellvalue);
				}},
			{ label: vendor_grade_name_list.B + '<br>판매가', name: 'product_option_sale_price_B', index: 'product_option_sale_price_B', price : "B", width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
					return Common.addCommas(cellvalue);
				}},
			{ label: vendor_grade_name_list.C + '<br>판매가', name: 'product_option_sale_price_C', index: 'product_option_sale_price_C', price : "C", width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
					return Common.addCommas(cellvalue);
				}},
			{ label: vendor_grade_name_list.D + '<br>판매가', name: 'product_option_sale_price_D', index: 'product_option_sale_price_D', price : "D", width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
					return Common.addCommas(cellvalue);
				}},
			{ label: vendor_grade_name_list.E + '<br>판매가', name: 'product_option_sale_price_E', index: 'product_option_sale_price_E', price : "E", width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
					return Common.addCommas(cellvalue);
				}},
			{ label: '등록일', name: 'product_option_regdate', index: 'A.product_option_regdate', width: 150, sortable: true, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
			{ label: '경고<br>수량', name: 'product_option_warning_count', index: 'product_option_warning_count', width: 60, sortable: false},
			{ label: '위협<br>수량', name: 'product_option_danger_count', index: 'product_option_danger_count', width: 60, sortable: false},
			{ label: '정상<br>재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, sortable: false
				, formatter: function(cellvalue, options, rowobject){
					if(rowobject.product_sale_type === "SELF") {
						return Common.addCommas(cellvalue);
					}else{
						return '-';
					}
				}
			},
			{ label: '원가<br>(매입가)', name: 'product_option_purchase_price', index: 'product_option_purchase_price', width: 80, sortable: false
				, formatter: function(cellvalue){
					return Common.addCommas(cellvalue);
				}
			},
			{ label: '재고관리', name: 'product_option_stock_btn', index: 'product_option_stock_btn', width: 80, sortable: false
				, formatter: function(cellvalue, options, rowobject){
					if(rowobject.product_sale_type === "SELF") {
						return '<a href="javascript:;" class="xsmall_btn btn-product-option-stock-manage" data-idx="'+rowobject.idx+'">재고관리</a>';
					}else{
						return '-';
					}
				}
			},

			{ label: '품절 메모', name: 'product_option_soldout_memo', index: 'product_option_soldout_memo', width: 150, sortable: false
				, formatter: function (cellValue, options, rowObject) {
					if (rowObject.product_option_soldout === "Y") {
						return '<a href="javascript:;" class="xsmall_btn gray_btn btn_sold_out_memo" data-option-idx="' + rowObject.product_option_idx + '" data-option-memo="' + cellValue + '">변경</a>' + ' ' + cellValue;
					} else {
						return '';
					}
				}
			},

			{label: '바코드(GTIN)', name: 'product_option_barcode_GTIN', index: 'product_option_barcode_GTIN', width: 200, sortable: false
				, formatter: function (cellValue, options, rowObject) {
					return '<a href="javascript:;" class="xsmall_btn gray_btn btn_barcode_GTIN" data-option-idx="' + rowObject.product_option_idx + '" data-option-barcode="' + cellValue + '">변경</a>' + ' ' + cellValue;
				}
			},
		];

		let shrinkToFit = false;
		if(!isDYLogin) {
			shrinkToFit = true;
			_colModel.shift();
			let _colModelVendor = [];
			$.each(_colModel, function (i, o) {
				if(typeof o.price != "undefined") {
					if(gl_vendor_grade === o.price){
						o.label = "판매가";
						o.formatter = function(cv, opt, ro) {
							if (ro.special_sale_unit_price !== null
								&& ro.special_sale_unit_price !== undefined) {
									cv = ro.special_sale_unit_price;
							}
							return Common.addCommas(cv);
						};
						_colModelVendor.push(o);
					}
				}else{
					if(o.name !== "product_option_warning_count"
						&& o.name !== "product_option_danger_count"
						&& o.name !== "product_option_purchase_price"
						&& o.name !== "product_option_stock_btn"
						&& o.name !== "product_option_barcode_GTIN")
					_colModelVendor.push(o);
				}
			});

			_colModel = _colModelVendor;
		}

		grid.jqGrid({
			url: './product_option_list_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: searchForm.serialize()
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
			sortname: 'A.product_option_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: shrinkToFit,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//사입/자체 시 원가 컬럼 Hide
				if($("input[name='product_sale_type']").val() === "SELF") {
					grid.jqGrid("hideCol", "product_option_purchase_price");
				}else{
					grid.jqGrid("hideCol", "product_option_warning_count");
					grid.jqGrid("hideCol", "product_option_danger_count");
					grid.jqGrid("hideCol", "stock_amount_NORMAL");
					grid.jqGrid("hideCol", "product_option_stock_btn");
				}

				//수정
				$(".btn-product-option-modify").on("click", function(){
					ProductOptionWritePopOpen($(this));
				});

				//삭제
				$(".btn-product-option-delete").on("click", function(){
					ProductOptionDeletePop($(this).data("idx"));
				});

				//품절
				$(".chk-product_option_soldout").on("change", function(){
					ProductOptionSoldOutUpdate($(this).data("idx"), "product_option_soldout", ($(this).is(":checked")) ? "Y" : "N");
					ProductOptionListSearch();
				});

				//일시품절
				$(".chk-product_option_soldout_temp").on("change", function(){
					ProductOptionSoldOutUpdate($(this).data("idx"), "product_option_soldout_temp", ($(this).is(":checked")) ? "Y" : "N");
					ProductOptionListSearch();
				});

				$(".btn-product-option-stock-manage").on("click", function(){
					Common.newWinPopup('/stock/stock_order_write_pop.php', 'stock_order_write_pop', 950, 750, 'yes');
				});

				//품절 메모 변경
				$(".btn_sold_out_memo").on("click", function(){
					let optIdx = $(this).data("option-idx");
					let optMemo = $(this).data("option-memo");

					let dataList = [
						[['name', '상품 옵션 순번'], ['type', 'hidden'], ['column_name', 'product_option_idx'], ['value', optIdx]],
						[['name', '품절 메모'], ['type', 'text'], ['column_name', 'product_option_sold_out_memo'], ['value', optMemo]]
					];

					Common.openCommonInputModalPop('품절 메모 변경', 'update_option_sold_out_memo', '/product/product_option_proc.php', dataList, ProductOptionListSearch);
				});

				//바코드 변경
				$(".btn_barcode_GTIN").on("click", function(){
					let optIdx = $(this).data("option-idx");
					let optBarcode = $(this).data("option-barcode");

					let dataList = [
						[['name', '상품 옵션 순번'], ['type', 'hidden'], ['column_name', 'product_option_idx'], ['value', optIdx]],
						[['name', '바코드 번호(GTIN)'], ['type', 'text'], ['column_name', 'product_option_barcode_GTIN'], ['value', optBarcode]]
					];

					Common.openCommonInputModalPop('바코드(GTIN) 변경', 'update_option_barcode_GTIN', '/product/product_option_proc.php', dataList, ProductOptionListSearch);
				});

				if(!isDYLogin) {
					$(".chk-product_option_soldout").prop("disabled", true);
					$(".chk-product_option_soldout_temp").prop("disabled", true);
				}

			}
		});
		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeWidth("#grid_list");
		}).trigger("resize");

		//검색 폼 Submit 방지
		searchForm.on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode === 13) {
				event.preventDefault();
				ProductOptionListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ProductOptionListSearch();
		});

	};

	//상품 옵션 목록/검색
	let ProductOptionListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	//상품 옵션 삭제 Modal Popup
	let ProductOptionDeletePop = function(product_option_idx){
		let p_url = "product_option_delete.php";
		let dataObj = {};
		dataObj.product_option_idx = product_option_idx;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_product_option_delete").html(response);
				$("#modal_product_option_delete").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	//상품 옵션 삭제 Modal Popup Close
	var ProductOptionDeletePopClose = function() {
		$("#modal_product_option_delete").dialog( "close" );
	};

	//상품 옵션 삭제 창 초기화
	var ProductOptionDeleteInit = function(){

		//옵션창 닫기 버튼 바인딩
		$(".btn-product-option-delete-close").on("click", function(){
			ProductOptionDeletePopClose();
		});

		//삭제 버튼 바인딩
		$("#btn-delete-option").on("click", function(){
			if(confirm('상품 옵션을 삭제하시겠습니까?')){
				var p_url = "/product/product_option_proc.php";
				var dataObj = new Object();
				dataObj.mode = "product_option_delete";
				dataObj.product_option_idx = $(this).data("idx");

				showLoader();
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: dataObj
				}).done(function (response) {
					if (response.result) {
						alert('삭제 되었습니다.');

						//삭제 창 닫기
						ProductOptionDeletePopClose();

						//상품 옵션 목록 reLoad
						ProductOptionListSearch();
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
	};

	//상품 옵션 리스트에서 품절, 일시품절 Update
	var ProductOptionSoldOutUpdate = function(product_option_idx, soldout_type, val){
		var p_url = "/product/product_option_proc.php";
		var dataObj = new Object();
		dataObj.mode = "product_option_soldout_update";
		dataObj.product_option_idx = product_option_idx;
		dataObj.soldout_type = soldout_type;
		dataObj.change_value = (val == "Y") ? "Y" : "N";

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {

			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	//상품 옵션 전체 품절/판매가능 처리
	var ProductOptionAllSoldOut = function(product_idx, val){
		var p_url = "/product/product_option_proc.php";
		var dataObj = new Object();
		dataObj.mode = "product_option_soldout_all_update";
		dataObj.product_idx = product_idx;
		dataObj.change_value = (val == "Y") ? "Y" : "N";

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {
				ProductOptionListSearch();
			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	//상품 옵션 조합을 위한 이벤트
	var ProductOptionMixEvent = function(){
		var o_1 = $("input[name='product_option_mix_1']").val();
		var o_2 = $("input[name='product_option_mix_2']").val();
		var o_3 = $("input[name='product_option_mix_3']").val();

		var o_1_ary = ($.trim(o_1) != "") ? o_1.split(",") : [];
		var o_2_ary = ($.trim(o_2) != "") ? o_2.split(",") : [];
		var o_3_ary = ($.trim(o_3) != "") ? o_3.split(",") : [];
		console.log(o_1_ary);
		console.log(o_2_ary);
		console.log(o_3_ary);
		var total_ary = [];
		if(o_1_ary.length > 0)
		{
			$.each(o_1_ary, function(i, o){
				var result1 = [];
				o = $.trim(o);
				if(o != "") {
					if (o_2_ary.length > 0) {
						$.each(o_2_ary, function (ii, oo) {
							oo = $.trim(oo);
							if (oo != "") {
								if (o_3_ary.length > 0) {
									$.each(o_3_ary, function (iii, ooo) {
										ooo = $.trim(ooo);
										if ($.trim(ooo) != "") {
											total_ary.push(o + '-' + oo + '-' + ooo);
										}
									});
								}else{
									total_ary.push(o + '-' + oo);
								}
							}
						});
					}else{
						total_ary.push(o);
					}
				}
			});
		}

		//console.log(total_ary);
		//$(".option_mix_result").html(total_ary.join(','));

		var tmp = "등록결과 : ";
		$.each(total_ary, function(i, o){
			tmp += '<span class="lb_black">'+o+'</span> ';
		});
		$(".option_mix_result").html(tmp);
	};

	//상품 옵션 판매가 할인율 계산 및 반영
	var ProductOptionSalePriceCal = function(){
		var val = $(".product_option_sale_price_mask").val();
		val = val.replace(/,/gi, '');

		if($.trim(val) == "")
		{
			alert("판매기준가를 입력해주세요.");
			return;
		}

		$(".product_option_sale_price_cal_per").each(function(i, o){
			var percent = $(this).val();

			$(".product_option_sale_price_cal").eq(i).val(val - Math.floor(val * (percent/100)));
		});
	};

	//공급처/판매처/벤더사 검색 팝업에서 그룹 리스트 로딩 및 바인딩
	var bindManageGroupList = function(group_type, select_target, child_select_target){
		$(select_target).on("change", function(){
			bindManageGroupMemberList(group_type, child_select_target, $(this).val());
		});

		var p_url = "/info/manage_group_proc.php";
		var dataObj = new Object();
		dataObj.mode = "get_manage_group_list";
		dataObj.manage_group_type = group_type;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				var manage_group_idx_selected = $(select_target).data("selected");
				//console.log(manage_group_idx_selected);
				//console.log(response.list);
				var $list = response.list;
				//$(select_target+" option").remove();
				//$(select_target).append('<option value="0">전체 그룹</option>');
				$.each($list, function(i, v){
					if(manage_group_idx_selected == v.manage_group_idx)
					{
						$(select_target).append('<option value="' + v.manage_group_idx + '">' + v.manage_group_name + '</option>');
					}else {
						$(select_target).append('<option value="' + v.manage_group_idx + '">' + v.manage_group_name + '</option>');
					}
				});

				if(typeof manage_group_idx_selected != "undefined") {
					if(manage_group_idx_selected !== "") {
						$(select_target).val(manage_group_idx_selected).trigger("change").data("selected", "");
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

	//공급처/판매처/벤더사 검색 팝업에서 그룹 선택 시 하위 공급처/판매처 로딩 및 바인딩
	var bindManageGroupMemberList = function(group_type, select_target, manage_group_idx){
		var p_url = "/info/manage_group_proc.php";
		var dataObj = new Object();
		dataObj.mode = "get_manage_group_member_list";
		dataObj.manage_group_type = group_type;
		dataObj.manage_group_idx = manage_group_idx;

		var manage_group_name = "";
		if(group_type == "SELLER_GROUP" || group_type == "SELLER_ALL_GROUP"){
			manage_group_name = "판매처";
		}else if(group_type == "VENDOR_GROUP") {
			manage_group_name = "벤더사";
		}else if(group_type == "SUPPLIER_GROUP") {
			manage_group_name = "공급처";
		}

		if(manage_group_idx == "")
		{
			$(select_target + " option").remove();
			if($(select_target).data("default-text") != "" && typeof $(select_target).data("default-text") != 'undefined')
			{
				if(window.name != "product_list") {
					$(select_target).append('<option value="' + $(select_target).data("default-value") + '">' + $(select_target).data("default-text") + '</option>');
				}
			}else{
				$(select_target).append('<option value="0">'+manage_group_name+'를 선택하세요.</option>');
			}
		}else {
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					var manage_member_idx_selected = $(select_target).data("selected");
					var $list = response.list;
					$(select_target + " option").remove();
					if($(select_target).data("default-text") != "" && typeof $(select_target).data("default-text") != 'undefined')
					{
						if(window.name != "product_list") {
							$(select_target).append('<option value="' + $(select_target).data("default-value") + '">' + $(select_target).data("default-text") + '</option>');
						}
					}else{
						$(select_target).append('<option value="0">'+manage_group_name+'를 선택하세요.</option>');
					}
					$.each($list, function (i, v) {
						if (manage_member_idx_selected == v.idx) {
							$(select_target).append('<option value="' + v.idx + '" selected="selected">' + v.name + '</option>');
						} else {
							$(select_target).append('<option value="' + v.idx + '">' + v.name + '</option>');
						}
					});
					if(typeof manage_member_idx_selected == "string") {
						if(manage_member_idx_selected.indexOf(',') > -1)
						{
							var manage_member_idx_selected_ary = manage_member_idx_selected.split(',');

							if(manage_member_idx_selected_ary.length > 0)
							{
								$.each(manage_member_idx_selected_ary, function(i, o){
									//$(select_target).val(o).data("selected", "");
									$(select_target + " option[value='" + o + "']").prop("selected", true);
								});
								$(select_target).data("selected", "");
							}
						}else {
							$(select_target).val(manage_member_idx_selected).data("selected", "");
						}
					}

					//상품 목록 일 경우 공급사 리스트 다중 선택 스크립트 Reload()
					if(window.name == "product_list") {
						$(select_target)[0].sumo.reload();
					}

				} else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	//공급처/판매처/벤더사 검색 팝업에서 선택 버튼 클릭 시 호출 되는 함수
	var setGroupMemberSelect = function(group_type, manage_group_idx, manage_group_member_idx){
		if(group_type == "SUPPLIER_GROUP"){
			$(".product_supplier_group_idx").data("selected", manage_group_idx);
			$(".product_supplier_group_idx").val(manage_group_idx);
			$(".supplier_idx").data("selected", manage_group_member_idx);
			$(".product_supplier_group_idx").trigger("change");
		}else if(group_type == "SELLER_GROUP" || group_type == "SELLER_ALL_GROUP"){
			$(".product_seller_group_idx").data("selected", manage_group_idx);
			$(".product_seller_group_idx").val(manage_group_idx);
			$(".seller_idx").data("selected", manage_group_member_idx);
			$(".product_seller_group_idx").trigger("change");
		}
	};

	//상품정보고시 셀렉트 박스 onChange 시 항목 명 바인딩
	var bindProductNoticeTitle = function(product_notice_idx){

		$(".product_notice_wrap").addClass("dis_none");
		$(".product_notice_table tbody tr").addClass("dis_none");

		if(product_notice_idx != "0" && product_notice_idx != "") {

			var p_url = "/info/product_notice_proc.php";
			var dataObj = new Object();
			dataObj.mode = "get_product_notice_content";
			dataObj.product_notice_idx = product_notice_idx;
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					$(".product_notice_wrap").removeClass("dis_none");
					var $data = response.data;

					for (var i = 1; i < 21; i++) {
						var tmpCol = "product_notice_" + i + "_use";

						var useVal = eval("$data." + tmpCol);
						if (useVal == "Y") {
							var tmpColTitle = "product_notice_" + i + "_title";
							var titleVal = eval("$data." + tmpColTitle);

							$(".product_notice_table tbody tr").eq(i - 1).removeClass("dis_none");
							$(".product_notice_table tbody tr:eq(" + (i - 1) + ") th").html(titleVal);
						}
					}

				} else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				//console.log(jqXHR, textStatus);
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			});
		}
	}

	//벤더사 노출 초기화
	var productVendorShowInit = function(){

		//노출 값 변경 시 (특정 업체 노출 리스트 Show/hide)
		$(".product_vendor_show").on("change", function(){
			if($(".product_vendor_show:checked").val() == "SHOW")
			{
				$(".set_vender_show_y").removeClass("dis_none");
			}else{
				$(".set_vender_show_y").addClass("dis_none");
			}
		}).trigger("change");

		$(".product_vendor_show_type").on("change", function(){
			if($(".product_vendor_show_type:checked").val() == "SELECTED")
			{
				$(".btn-product-vendor-show-selected, .div_product_vendor_show_list").removeClass("dis_none");
			}else{
				$(".btn-product-vendor-show-selected, .div_product_vendor_show_list").addClass("dis_none");
			}
		}).trigger("change");

		//벤더사 선택 버튼 클릭 시
		$(".btn-product-vendor-show-selected").on("click", function(){
			Common.newWinPopup("/product/product_write_vendor_select_pop.php", 'product_write_vendor_select_pop', 700, 600, 'yes');
		});
	};

	//입력/수정 이후 본창 리스트를 reload 할때 실행되는 함수
	//window.name 이 'product_list' 일 때 만 실행
	//다른 페이지에서 입력/수정 팝업을 띄워 입력/수정 된 경우 실행하지 않도록 설정
	var ProductListReload = function(){
		if(window.name == 'product_list'){
			ProductListSearch();
		}
	};

	var xlsValidRow = 0;                //업로드된 엑셀 Row 중 정상인 Row 수
	var xlsUploadedFileName = "";       //업로드 된 엑셀 파일명
	var xlsWritePageMode = "";          //일괄등록 / 일괄수정 Flag
	var xlsWriteReturnStyle = "";       //리스트 반환 또는 적용

	//상품 옵션 일괄등록  페이지 초기화
	var ProductOptionXlsWriteInit = function(){

		xlsWritePageMode = $("#xlswrite_mode").val();
		xlsWriteReturnStyle = $("#xlswrite_act").val();

		$(".btn-upload").on("click", function(){
			if($("input[name='xls_file']").val() == "")
			{
				alert("업로드 할 파일을 선택해주세요.");
				return false;
			}

			showLoader();
			$("#searchForm_xls").submit();
		});

		$(".btn-xls-insert").on("click", function(){
			if(xlsValidRow == 0)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = xlsValidRow + "건의 데이터를 적용 하시겠습니까?";
				if(confirm(msg)) {
					ProductOptionXlsInsert();
				}
			}
		});

		ProductOptionXlsWriteGridInit();
	};

	//상품 옵션 일괄등록 페이지 jqGrid 초기화
	var ProductOptionXlsWriteGridInit = function(){
		var validErr = [];

		var colModel = [];
		if(xlsWritePageMode == "add")
		{
			colModel = [
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
				{ label: '상품코드', name: 'A', index: 'product_idx', width: 100, sortable: false},
				{ label: '옵션명', name: 'B', index: 'product_option_name', width: 150, sortable: false},
				{ label: '판매기준가', name: 'C', index: 'product_option_sale_price', width: 150, sortable: false},
				{ label: '판매가(A등급)', name: 'D', index: 'product_option_sale_price_A', width: 150, sortable: false},
				{ label: '판매가(B등급)', name: 'E', index: 'product_option_sale_price_B', width: 150, sortable: false},
				{ label: '판매가(C등급)', name: 'F', index: 'product_option_sale_price_C', width: 150, sortable: false},
				{ label: '판매가(D등급)', name: 'G', index: 'product_option_sale_price_D', width: 150, sortable: false},
				{ label: '판매가(E등급)', name: 'H', index: 'product_option_sale_price_E', width: 150, sortable: false},
				{ label: '매입가', name: 'I', index: 'product_option_purchase_price', width: 150, sortable: false},
				{ label: '재고경고수량', name: 'J', index: 'product_option_warning_count', width: 150, sortable: false},
				{ label: '재고위협수량', name: 'K', index: 'product_option_danger_count', width: 150, sortable: false},


			];
		}else{
			colModel = [
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
				{ label: '상품코드', name: 'A', index: 'product_idx', width: 100, sortable: false},
				{ label: '옵션코드', name: 'B', index: 'product_option_name', width: 150, sortable: false},
				{ label: '옵션명', name: 'C', index: 'product_option_name', width: 150, sortable: false},
				{ label: '판매기준가', name: 'D', index: 'product_option_sale_price', width: 150, sortable: false},
				{ label: '판매가(A등급)', name: 'E', index: 'product_option_sale_price_A', width: 150, sortable: false},
				{ label: '판매가(B등급)', name: 'F', index: 'product_option_sale_price_B', width: 150, sortable: false},
				{ label: '판매가(C등급)', name: 'G', index: 'product_option_sale_price_C', width: 150, sortable: false},
				{ label: '판매가(D등급)', name: 'H', index: 'product_option_sale_price_D', width: 150, sortable: false},
				{ label: '판매가(E등급)', name: 'I', index: 'product_option_sale_price_E', width: 150, sortable: false},
				{ label: '매입가', name: 'J', index: 'product_option_purchase_price', width: 150, sortable: false},
				{ label: '재고경고수량', name: 'K', index: 'product_option_warning_count', width: 150, sortable: false},
				{ label: '재고위협수량', name: 'L', index: 'product_option_danger_count', width: 150, sortable: false},


			];
		}

		$("#grid_list_xls").jqGrid({
			url: './product_option_proc_xls.php',
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
			colModel: colModel,
			rowNum:1000,
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager_xls',
			sortname: 'seller_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 300,
			loadComplete: function(){
				//console.log(validErr);
				$.each(validErr, function(k, v){
					$("#grid_list_xls #"+v).addClass("upload_err");
					validErr = [];
				});
			},
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeWidthByTarget("#grid_list_xls", $(".xls_pop_content"));
		}).trigger("resize");
	};

	//상품 옵션 업로드 된 엑셀 파일 로딩
	var ProductOptionXlsRead = function(xls_file_path_name){
		//console.log(xls_file_path_name);
		xlsUploadedFileName = xls_file_path_name;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list_xls").setGridParam({
			datatype: "json",
			postData:{
				mode: xlsWritePageMode,
				act: xlsWriteReturnStyle,
				xls_filename: xls_file_path_name,
				product_idx: $("form[name='searchForm_xls'] input[name='product_idx']").val()
			}
		}).trigger("reloadGrid");
	};

	//상품 옵션 업로드 된 엑셀 파일 적용
	var ProductOptionXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "product_option_proc_xls.php";
		var dataObj = new Object();
		dataObj.mode = xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = xlsUploadedFileName;
		dataObj.xls_validrow = xlsValidRow;
		dataObj.product_idx = $("form[name='searchForm_xls'] input[name='product_idx']").val();

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert(response.msg+"건이 정상 적용 되었습니다.");
				ProductOptionWriteXlsPopClose();
				ProductOptionListSearch();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	//상품 엑셀 업로드 페이지 초기화
	var ProductXlsWriteInit = function(){
		xlsWritePageMode = $("#xlswrite_mode").val();
		xlsWriteReturnStyle = $("#xlswrite_act").val();

		$(".btn-upload").on("click", function(){
			if($("input[name='xls_file']").val() == "")
			{
				alert("업로드 할 파일을 선택해주세요.");
				return false;
			}

			showLoader();
			$("#searchForm_xls").submit();
		});

		$(".btn-xls-insert").on("click", function(){
			if(xlsValidRow == 0)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = xlsValidRow + "건의 데이터를 적용 하시겠습니까?";
				if(confirm(msg)) {
					ProductXlsInsert();
				}
			}
		});

		ProductXlsWriteGridInit();
	};

	//상품 일괄 등록 페이지 jqGrid 초기화
	var ProductXlsWriteGridInit = function () {
		var validErr = [];

		var colModel = [];
		if(xlsWritePageMode == "add")
		{
			colModel = [
				{ label: '비고', name: 'valid', index: 'valid', width: 100, sortable: false
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
				{ label: '판매타입', name: 'A', index: 'product_sale_type', width: 100, sortable: false},
				{ label: '공급처코드', name: 'B', index: 'supplier_idx', width: 100, sortable: false},
				{ label: '상품명', name: 'C', index: 'product_name', width: 100, sortable: false},
				{ label: '브랜드명', name: 'D', index: 'product_brand_name', width: 100, sortable: false},
				{ label: '출고지정보', name: 'E', index: 'product_factory_info', width: 100, sortable: false},
				{ label: 'Location', name: 'F', index: 'product_location', width: 100, sortable: false},
				{ label: '공급처 상품명', name: 'G', index: 'product_supplier_name', width: 100, sortable: false},
				{ label: '공급처 옵션', name: 'H', index: 'product_supplier_option', width: 100, sortable: false},
				{ label: '판매처', name: 'I', index: 'seller_idx', width: 100, sortable: false},
				{ label: '원산지', name: 'J', index: 'product_origin', width: 100, sortable: false},
				{ label: '제조사', name: 'K', index: 'product_manufacturer', width: 100, sortable: false},
				{ label: '담당MD', name: 'L', index: 'product_md', width: 100, sortable: false},
				{ label: '매출배송비', name: 'M', index: 'product_delivery_fee_sale', width: 100, sortable: false},
				{ label: '매입배송비', name: 'N', index: 'product_delivery_fee_buy', width: 100, sortable: false},
				{ label: '배송타입', name: 'O', index: 'product_delivery_type', width: 100, sortable: false},
				{ label: '카테고리1', name: 'P', index: 'product_category_l_name', width: 100, sortable: false},
				{ label: '카테고리2', name: 'Q', index: 'product_category_m_name', width: 100, sortable: false},
				{ label: '판매시작일', name: 'R', index: 'product_sales_date', width: 100, sortable: false},
				{ label: '대상세금종류', name: 'S', index: 'product_tax_type', width: 100, sortable: false},
				{ label: 'A/S안내', name: 'T', index: 'product_as_info', width: 100, sortable: false},
				{ label: '상품설명', name: 'U', index: 'product_desc', width: 100, sortable: false},
				{ label: '상품옵션명', name: 'V', index: 'product_option_name', width: 100, sortable: false},
				{ label: '판매기준가', name: 'Y', index: 'product_option_sale_price', width: 100, sortable: false},
				{ label: '재고경고수량', name: 'Z', index: 'product_option_warning_count', width: 100, sortable: false},
				{ label: '재고위협수량', name: 'AA', index: 'product_option_danger_count', width: 100, sortable: false},
				{ label: '매입가', name: 'AB', index: 'product_option_purchase_price', width: 100, sortable: false},


			];
		}else{
			colModel = [
				{ label: '상품코드', name: 'A', index: 'product_idx', width: 100, sortable: false},
				{ label: '옵션코드', name: 'B', index: 'product_option_name', width: 150, sortable: false},
				{ label: '옵션명', name: 'C', index: 'product_option_name', width: 150, sortable: false},
				{ label: '판매기준가', name: 'D', index: 'product_option_sale_price', width: 150, sortable: false},
				{ label: '판매가(A등급)', name: 'E', index: 'product_option_sale_price_A', width: 150, sortable: false},
				{ label: '판매가(B등급)', name: 'F', index: 'product_option_sale_price_B', width: 150, sortable: false},
				{ label: '판매가(C등급)', name: 'G', index: 'product_option_sale_price_C', width: 150, sortable: false},
				{ label: '판매가(D등급)', name: 'H', index: 'product_option_sale_price_D', width: 150, sortable: false},
				{ label: '판매가(E등급)', name: 'I', index: 'product_option_sale_price_E', width: 150, sortable: false},
				{ label: '매입가', name: 'J', index: 'product_option_purchase_price', width: 150, sortable: false},
				{ label: '재고경고수량', name: 'K', index: 'product_option_warning_count', width: 150, sortable: false},
				{ label: '재고위협수량', name: 'L', index: 'product_option_danger_count', width: 150, sortable: false},
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

			];
		}

		$("#grid_list_xls").jqGrid({
			url: './product_proc_xls.php',
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
			colModel: colModel,
			rowNum:10000,
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager_xls',
			sortname: 'seller_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: false,
			height: 300,
			loadComplete: function(){
				//console.log(validErr);
				$.each(validErr, function(k, v){
					$("#grid_list_xls #"+v).addClass("upload_err");
					validErr = [];
				});
			},
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeWidthByTarget("#grid_list_xls", $(".xls_pop_content"));
		}).trigger("resize");
	};

	//상품 업로드 된 엑셀 파일 로딩
	var ProductXlsRead = function(xls_file_path_name){
		//console.log(xls_file_path_name);
		xlsUploadedFileName = xls_file_path_name;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list_xls").setGridParam({
			datatype: "json",
			postData:{
				mode: xlsWritePageMode,
				act: xlsWriteReturnStyle,
				xls_filename: xls_file_path_name
			}
		}).trigger("reloadGrid");
	};

	//상품 업로드 된 엑셀 파일 적용
	var ProductXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "product_proc_xls.php";
		var dataObj = new Object();
		dataObj.mode = xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = xlsUploadedFileName;
		dataObj.xls_validrow = xlsValidRow;
		dataObj.product_idx = $("form[name='searchForm_xls'] input[name='product_idx']").val();

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert(response.msg+"건이 정상 적용 되었습니다.");
				location.reload();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	//상품 일괄 선택 수정 페이지 초기화
	var ProductXlsWriteUpdateInit = function(){
		xlsWritePageMode = $("#xlswrite_mode").val();
		xlsWriteReturnStyle = $("#xlswrite_act").val();

		$(".btn-upload").on("click", function(){
			if($("input[name='xls_file']").val() == "")
			{
				alert("업로드 할 파일을 선택해주세요.");
				return false;
			}

			showLoader();
			$("#searchForm_xls").submit();
		});

		$(".btn-xls-insert").on("click", function(){
			if(xlsValidRow == 0)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = xlsValidRow + "건의 데이터를 적용 하시겠습니까?";
				if(confirm(msg)) {
					ProductXlsInsert();
				}
			}
		});

		ProductXlsWriteUpdateGridInit();
	};

	//상품 일괄 선택 수정 페이지 jqGrid 초기화
	var ProductXlsWriteUpdateGridInit = function () {
		var validErr = [];

		var colModel = [];
		colModel = [
			{ label: '상품코드 또는 옵션코드', name: 'A', index: 'product_idx', width: 200, sortable: false},
			{ label: '수정필드명', name: 'B', index: 'supplier_idx', width: 200, sortable: false},
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
		];

		$("#grid_list_xls").jqGrid({
			url: './product_proc_xls.php',
			mtype: "POST",
			datatype: "local",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx",

			},
			colModel: colModel,
			rowNum:10000,
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager_xls',
			sortname: 'xls_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//console.log(validErr);
				$.each(validErr, function(k, v){
					$("#grid_list_xls #"+v).addClass("upload_err");
					validErr = [];
				});

				var userdata = $("#grid_list_xls").jqGrid("getGridParam", "userData");
				jQuery("#grid_list_xls").jqGrid('setLabel', 2, userdata.field_name);
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrProductXlsUpdateReadid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list_xls");
		}).trigger("resize");
	};

	//상품 일괄 선택 수정 업로드 된 엑셀 파일 로딩
	var ProductXlsUpdateRead = function(xls_file_path_name){
		//console.log(xls_file_path_name);
		xlsUploadedFileName = xls_file_path_name;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list_xls").setGridParam({
			datatype: "json",
			postData:{
				mode: xlsWritePageMode,
				act: xlsWriteReturnStyle,
				xls_filename: xls_file_path_name
			}
		}).trigger("reloadGrid");
	};

	return {
		ProductListInit : ProductListInit,
		ProductWriteInit : ProductWriteInit,
		ProductListReload : ProductListReload,
		ProductXlsWriteInit : ProductXlsWriteInit,
		ProductXlsRead: ProductXlsRead,
		ProductOptionXlsWriteInit : ProductOptionXlsWriteInit,
		ProductOptionXlsRead : ProductOptionXlsRead,
		setGroupMemberSelect: setGroupMemberSelect,
		ProductOptionInit: ProductOptionListInit,
		ProductOptionWriteInit: ProductOptionWriteInit,
		ProductOptionDeleteInit: ProductOptionDeleteInit,
		ProductDeleteInit: ProductDeleteInit,
		ProductXlsWriteUpdateInit: ProductXlsWriteUpdateInit,
		ProductXlsUpdateRead: ProductXlsUpdateRead,
		ProductListXlsDownComplete: ProductListXlsDownComplete,
	}
})();