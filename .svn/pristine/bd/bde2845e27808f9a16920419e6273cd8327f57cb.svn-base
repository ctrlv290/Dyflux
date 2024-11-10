/*
 * 사은품 관리 js
 */
var OrderGift = (function() {
	var root = this;
	var orderTabs = null;

	var xlsDownIng = false;
	var xlsDownInterval;

	var init = function() {
	};


	/**
	 * 사은품목록 페이지 초기화
	 * @constructor
	 */
	var GiftListInit = function(){
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

		//신규등록
		$(".btn-write").on("click", function(){
			GiftPopup('');
		});

		//신규등록
		$(".btn-xls-down").on("click", function(){
			GiftListXlsDown();
		});

		GiftListGridInit();
	};

	/**
	 * 사은품목록 Grid 초기화
	 * @constructor
	 */
	var GiftListGridInit = function(){

		//Grid 초기화
		$("#grid_list").jqGrid({
			url: '/order/gift_list_grid.php',
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
				{ label: '사은품코드', name: 'gift_idx', index: 'gift_idx', width: 0, sortable: false, hidden: true},
				{ label: '사은품 이름', name: 'gift_name', index: 'gift_name', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" class="btn-edit link" data-idx="'+rowobject.gift_idx+'">'+cellvalue+'</a>';
					}},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 150, sortable: false},
				{ label: '생성자', name: 'member_id', index: 'member_id', width: 100, sortable: false},
				{ label: '시작일', name: 'gift_date_start', index: 'gift_date_start', width: 150, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '종료일', name: 'gift_date_end', index: 'gift_date_end', width: 150, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '상태', name: 'gift_status', index: 'gift_status', width: 80, sortable: false, formatter: function (cellvalue, options, rowobject) {
						var val = "";
						if(cellvalue == "N") {
							val = "준비중";
						}else if(cellvalue == "Y") {
							val = "진행중";
						}else if(cellvalue == "X") {
							val = "종료";
						}
						return val;
					}},
				{ label: '매칭수량<br>(소진/설정)', name: 'matching_status', index: 'matching_status', width: 60, sortable: true, formatter: function(cellvalue, options, rowobject){
						return rowobject.use_cnt + '/' + rowobject.gift_cnt;
					}},
				{ label: '등록일', name: 'gift_regdate', index: 'gift_regdate', width: 180, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '최종 수정일', name: 'gift_moddate', index: 'gift_moddate', width: 180, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'gift_regdate',
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
				$(".btn-edit").on("click", function(){
					GiftPopup($(this).data("idx"));

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
				GiftListGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			GiftListGridSearch();
		});

	};

	/**
	 * 사은품목록 목록/검색
	 * @constructor
	 */
	var GiftListGridSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 사은품목록 reload
	 * @constructor
	 */
	var GiftListGridReload = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 하부주문관리 엑셀 다운로드
	 * @constructor
	 */
	var GiftListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "gift_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_GIFT_LIST", function(){
				OrderGift.GiftListXlsDownComplete();
			});
		}, 500);
	};

	var GiftListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 사은품 팝업
	 * @param gift_idx
	 * @constructor
	 */
	var GiftPopup = function(gift_idx) {
		var url = '/order/gift_write_pop.php';
		url += (gift_idx != '') ? '?gift_idx=' + gift_idx : '';
		Common.newWinPopup(url, 'gift_write_pop', 700, 800, 'yes');
	};

	/**
	 * 사은품 팝업 페이지 초기화
	 * @constructor
	 */
	var GiftWriteInit = function(){
		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
		$(".supplier_idx").SumoSelect({
			placeholder: '공급처를 선택하세요',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '공급처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '판매처를 선택하세요',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//시간 inputMask
		$(".time").inputmask("datetime", {
				placeholder: 'hh:mm:ss',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);

		$(".supplier_idx").on("change", function(){
			//console.log($(this).val() );
			if($(this).val() == "" || $(this).val() == null || $(this).val() == "null"){
				$("input[name='product_option_idx_list']").prop("disabled", false);
				$(".btn-product-select").show();
			}else{
				$("input[name='product_option_idx_list']").prop("disabled", true);
				$(".btn-product-select").hide();
			}
		}).trigger("change");

		//관리자 상품 추가 버튼 바인딩
		$(".btn-product-select").on("click", function(){
			var url = '/order/gift_add_option_pop.php';
			Common.newWinPopup(url, 'gift_add_option_pop', 650, 600, 'yes');
		});

		//관리자 상품 추가 버튼 바인딩
		$(".btn-gift-select").on("click", function(){
			GiftSelectAddPopup();
		});

		bindWriteForm();
	};

	var writeFormIng = false;

	/**
	 * 사은품 팝업 페이지 폼 바인딩
	 */
	var bindWriteForm = function () {

		setTimeout(function() {
			$(".supplier_idx").trigger("change");
		}, 600);

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			if(writeFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.gift_name, "사은품 이름을 입력해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.gift_date_start_1, "기간을 정확히 입력해주세요.", 10, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.gift_date_start_2, "기간을 정확히 입력해주세요.", 8, 8, null)) return returnType;
				if (!valForm.chkValue(objForm.gift_date_end_1, "기간을 정확히 입력해주세요.", 10, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.gift_date_end_2, "기간을 정확히 입력해주세요.", 8, 8, null)) return returnType;

				var timeTest = new RegExp("[^0-9\:]");
				if(timeTest.test(objForm.gift_date_start_2.value)){
					alert("시간을 정확히 입력해주세요. \n시:분:초 형식 00:00:00");
					return returnType;
				}
				if(timeTest.test(objForm.gift_date_end_2.value)){
					alert("시간을 정확히 입력해주세요. \n시:분:초 형식 00:00:00");
					return returnType;
				}


				this.action = "gift_proc.php";
				$("#btn-save").attr("disabled", true);
				writeFormIng = true;

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 관리자 상품 추가 팝업 페이지 초기화
	 * @constructor
	 */
	var GfitAddOptionInit = function(){

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

		//추가 버튼 바인딩
		$(".btn-stock-order-add-product-exec").on("click", function(){
			GiftAddOptionExec();
		});

		//상품 Grid 초기화
		var colModel =  [
			{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
			{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, is_use : true},
			{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: true},
			{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: true},
		];
		$("#grid_list_pop").jqGrid({
			url: '/order/gift_add_option_pop_grid.php',
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
			colModel: colModel,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager_pop',
			sortname: 'A.product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: true,
			height: 300,
			multiselect: true,
			loadComplete: function(){

			}
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeWidthByTarget("#grid_list_pop", $(".container.popup .tb_wrap"));
		}).trigger("resize");

		//검색 폼 Submit 방지
		$("#searchFormPop").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearchPop").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				GiftAddOptionSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_pop").on("click", function(){
			GiftAddOptionSearch();
		});
	};

	/**
	 * 관리자 상품 추가 목록/검색
	 * @constructor
	 */
	var GiftAddOptionSearch = function(){
		$("#grid_list_pop").setGridParam({
			datatype: "json",
			url: '/order/gift_add_option_pop_grid.php',
			postData:{
				param: $("#searchFormPop").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 관리자 상품 추가 실행 함수
	 * @constructor
	 */
	var GiftAddOptionExec = function(){

		//선택된 rowid 가져오기
		var rowIds = $("#grid_list_pop").jqGrid('getGridParam', "selarrrow" );

		if(rowIds.length > 0) {
			try {
				$.each(rowIds, function (i, o) {

					var rowData = $("#grid_list_pop").getRowData(o);
					var product_option_idx = rowData.product_option_idx;
					//console.log(rowData);

					opener.OrderGift.GiftAddOption(product_option_idx);
				});
			}catch (e) {
				console.log(e);
				alert("신규등록 팝업페이지가 없습니다.");
				//self.close();
			}
		}

	};

	/**
	 * 관리자 상품 선택 추가 시 입력 함수
	 * @param option_idx
	 * @constructor
	 */
	var GiftAddOption = function(option_idx){
		var isExists = false;

		var list = $("#product_option_idx_list").val();
		var list_ary = list.split(',');
		list_ary = $.grep(list_ary,function(n){ return n == " " || n; });
		$.each(list_ary, function(i, o){
			if($.trim(o) == option_idx){
				isExists = true;
			}
		});
		if(!isExists){

			//console.log(list_ary.length);

			list_ary.push(option_idx);
		}

		$("#product_option_idx_list").val(list_ary.join(','));

	};

	/**
	 * 사은품 선택 팝업 Open
	 * @constructor
	 */
	var GiftSelectAddPopup = function(){
		Common.newWinPopup("gift_select_pop.php", 'gift_select_pop', 750, 450, 'yes');
	};

	/**
	 * 사은품 선택 - 상품 검색 팝업 페이지 초기화
	 * @constructor
	 */
	var GiftSelectAddPopupInit = function(){
		//Grid 초기화
		$("#grid_list_pop").jqGrid({
			url: '/order/gift_select_pop_grid.php',
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
				{
					label: '선택', name: '선택', width: 60, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return '<a href="javascript:;" class="xsmall_btn btn-product-select" data-idx="' + rowobject.product_idx + '" data-num="' + options.rowId+ '">선택</a>';
					}
				},
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
				{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, is_use : true},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: true},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: true},
				{ label: '공급업체', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false, align: 'left'},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager_pop',
			sortname: 'A.product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list_pop");

				//상품 선택 버튼 바인딩
				$(".btn-product-select").on("click", function(){
					var rowNum = $(this).data("num");
					var rowData = $("#grid_list_pop").getRowData(rowNum);
					var product_idx = rowData.product_idx;
					var product_option_idx = rowData.product_option_idx;
					var product_name = rowData.product_name;
					var product_option_name = rowData.product_option_name;
					GiftSelectAddPopupSelect(product_idx, product_option_idx, product_name, product_option_name);
				});
			}
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeWidthByTarget("#grid_list_pop", $(".container.popup .tb_wrap"));
		}).trigger("resize");

		//검색 폼 Submit 방지
		$("#searchFormPop").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearchPop").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				GiftSelectAddPopupSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_pop").on("click", function(){
			GiftSelectAddPopupSearch();
		});
	};

	/**
	 * 사은품 선택 - 상품 검색 팝업 목록/검색
	 * @constructor
	 */
	var GiftSelectAddPopupSearch = function(){

		var txt1 = $("form[name='searchFormPop'] input[name='product_name']").val();
		var txt2 = $("form[name='searchFormPop'] input[name='product_option_name']").val();
		var txt3 = $("form[name='searchFormPop'] input[name='product_option_idx']").val();

		if($.trim(txt1) == "" && $.trim(txt2) == "" && $.trim(txt3) == ""){
			alert('검색어를 입력해주세요.');
			return;
		}

		$("#grid_list_pop").setGridParam({
			datatype: "json",
			page: 1,
			url: '/order/gift_select_pop_grid.php',
			postData:{
				param: $("#searchFormPop").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 사은품 선택 - 상품 검색 팝업 - 상품 선택
	 * opener 를 검사 후 opener 의 상품 입력 함수 실행
	 * @param product_idx
	 * @param product_option_idx
	 * @param product_name
	 * @param product_option_name
	 * @constructor
	 */
	var GiftSelectAddPopupSelect = function(product_idx, product_option_idx, product_name, product_option_name){
		var openerName = window.opener &&
			window.opener.document &&
			window.opener.name;
		if (openerName == "gift_pop") {

			window.opener.OrderGift.GiftSelectSelect(product_idx, product_option_idx, product_name, product_option_name)
			self.close();
		}else{

		}
	};

	/**
	 * 사은품 선택 - 상품 정보 입력 함수
	 * @param product_idx
	 * @param product_option_idx
	 * @param product_name
	 * @param product_option_name
	 * @constructor
	 */
	var GiftSelectSelect = function(product_idx, product_option_idx, product_name, product_option_name){
		$("form[name='dyForm'] input[name='gift_product_idx']").val(product_idx);
		$("form[name='dyForm'] input[name='gift_product_option_idx']").val(product_option_idx);
		$("input[name='gift_product_full_name']").val(product_name + ' ' + product_option_name);
	};

	return {
		GiftListInit: GiftListInit,
		GiftWriteInit: GiftWriteInit,
		GfitAddOptionInit: GfitAddOptionInit,
		GiftAddOption: function(option_idx){
			GiftAddOption(option_idx)
		},
		GiftSelectAddPopupInit: GiftSelectAddPopupInit,
		GiftSelectSelect: GiftSelectSelect,
		GiftListGridReload: GiftListGridReload,
		GiftListXlsDownComplete: GiftListXlsDownComplete
	}

})();