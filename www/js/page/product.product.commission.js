/*
 * 수수료정보 관리js
 */
var ProductCommission = (function() {
	var root = this;

	var init = function() {
	};

	/**
	 * 수수료 정보 등록 팝업 오픈
	 * @param seller_idx
	 * @constructor
	 */
	var ProductCommissionWritePopup = function(comm_idx, is_copy) {
		var url = '/product/product_commission_write_pop.php';
		url += (comm_idx != '') ? '?comm_idx=' + comm_idx : '';
		if(is_copy){
			url += '&is_copy=Y';
		}
		Common.newWinPopup(url, 'product_commission_write_pop', 700, 720, 'yes');
	};

	/**
	 * 수수료정보 목록 초기화
	 * @constructor
	 */
	var ProductCommissionListInit = function(){

		//신규등록 팝업
		$(".btn-product-commission-add-pop").on("click", function(){
			ProductCommissionWritePopup('', false);
		});

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

		//판매처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//엑셀 다운로드
		$(".btn-product-matching-xls-down").on("click", function(){
			ProductMatchingListXlsDown();
		});

		//상품 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './product_commission_list_grid.php',
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
				{label: 'comm_idx', name: 'comm_idx', index: 'comm_idx', width: 100, sortable: false, hidden: true, cellattr:jsFormatterComparePrimaryKey},
				{label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
				{label: '판매처<br>상품코드', name: 'market_product_no', index: 'market_product_no', width: 100, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan, formatter: function(cellvalue, options, rowobject){
					var btnz = '';
					btnz = '<a href="javascript:;" class="link btn-product-commission-modify" data-comm_idx="'+rowobject.comm_idx+'">'+cellvalue+'</a>'
					return btnz;

				}},
				{label: '판매수수료(%)', name: 'market_commission', index: 'market_commission', width: 100, sortable: false, formatter: 'number', cellattr:jsFormatterCompareAndSetRowSpan},
				{label: '배송비수수료(%)', name: 'delivery_commission', index: 'delivery_commission', width: 100, sortable: false, formatter: 'number', cellattr:jsFormatterCompareAndSetRowSpan},
				{label: '상품코드', name: 'product_idx', index: 'product_idx', width: 100, sortable: false},
				{label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100, sortable: false},
				{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left'},
				{label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left'},
				{label: '등록일', name: 'comm_regdate', index: 'comm_regdate', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}
					, cellattr:jsFormatterCompareAndSetRowSpan
				},
				{label: '등록계정', name: 'member_id', index: 'member_id', width: 80, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
				{ label: '수수료관리', name: 'btn_action', index: 'btn_action', width: 150, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn link btn-product-commission-modify" data-comm_idx="'+rowobject.comm_idx+'">수정</a> '
						btnz += '<a href="javascript:;" class="xsmall_btn red_btn btn-product-commission-delete" data-comm_idx="'+rowobject.comm_idx+'">삭제</a> '
						btnz += '<a href="javascript:;" class="xsmall_btn green_btn btn-product-commission-copy" data-comm_idx="'+rowobject.comm_idx+'">복사</a>'
						return btnz;

					}
					, cellattr:jsFormatterCompareAndSetRowSpan
				},
				// { label: '복사', name: 'btn_copy', index: 'btn_copy', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
				// 		var btnz = '';
				// 		btnz = '<a href="javascript:;" class="xsmall_btn green_btn btn-product-commission-copy" data-comm_idx="'+rowobject.comm_idx+'">복사</a>'
				// 		return btnz;
				//
				// 	}
				// 	, cellattr:jsFormatterCompareAndSetRowSpan
				// },
			],
			rowNum: Common.jsSiteConfig.jqGridRowListBig[0],
			rowList: Common.jsSiteConfig.jqGridRowListBig,
			pager: '#grid_pager',
			sortname: 'C.comm_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				var grid = this;
				$('td[name="cellRowspan"]', grid).each(function() {
					var spans = $('td[rowspanid="'+this.id+'"]',grid).length+1;
					if(spans>1){
						$(this).attr('rowspan',spans).addClass("bg-force-white");
					}
				});

				$("td[data-is-key='1']").parent().find("td").addClass("bold_top_line");

				//삭제 버튼 바인딩
				$(".btn-product-commission-delete").on("click", function(){
					ProductCommissionDeleteOne($(this).data("comm_idx"));
				});

				//복사 버튼 바인딩
				$(".btn-product-commission-copy").on("click", function(){
					ProductCommissionCopy($(this).data("comm_idx"));
				});

				//수정 링크
				$(".btn-product-commission-modify").on("click", function(){
					ProductCommissionModify($(this).data("comm_idx"));
				});
			},
			beforeRequest: function(){
				chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크
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
				ProductCommissionListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ProductCommissionListSearch();
		});
	};

	/**
	 * jqGrid 셀 병합을 위한 임시 저장변수
	 * @type {{chkval: undefined, rowNo: number, cellId: undefined}}
	 */
	var chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크

	/**
	 * 목록 셀병합 함수 1
	 * jqGrid 셀 병합을 위한 함수 (Key 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterComparePrimaryKey = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		//console.log(this.id);
		var cellId = this.id + '_row_'+rowObject.comm_idx+'-'+cm.name;
		if(chkcell.chkval != rowObject.comm_idx && rowid != chkcell.rowNo){ //check 값이랑 비교값이 다른 경우
			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan" data-is-key="1"';
			//alert(result);
			chkcell = {cellId:cellId, chkval:rowObject.comm_idx, rowNo: rowid};
		}else{
			result = 'style="display: none;"  rowspanid="'+cellId+'"'; //같을 경우 display none 처리
			//alert(result);
		}
		return result;
	};

	/**
	 *  목록 셀병합 함수 2
	 * jqGrid 셀병합을 위한 함수 (일반 Cell 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterCompareAndSetRowSpan = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		//console.log(cm);

		var cellId = this.id + '_row_'+rowObject.comm_idx+'-'+cm.name;
		if(chkcell.chkval == rowObject.comm_idx && rowid == chkcell.rowNo){ //check 값이랑 비교값이 다른 경우
			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan"';
		}else{
			result = 'style="display: none;" rowspanid="'+cellId+'"'; //같을 경우 display none 처리
		}
		return result;
	};

	/**
	 * 수수료정보 목록/검색
	 * @constructor
	 */
	var ProductCommissionListSearch = function(){

		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var ProductCommissionModify = function(comm_idx){
		ProductCommissionWritePopup(comm_idx, false);
	};

	var ProductCommissionCopy = function(comm_idx){
		ProductCommissionWritePopup(comm_idx, true);
	};

	/**
	 * 수수료정보 단일 삭제
	 * @param matching_info_idx
	 * @constructor
	 */
	var ProductCommissionDeleteOne = function(comm_idx){
		if(confirm('수수료정보를 삭제하시겠습니까?')){

			var p_url = "/product/product_commission_proc_ajax.php";
			var dataObj = new Object();
			dataObj.mode = "commission_delete";
			dataObj.comm_idx = comm_idx;
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					alert('삭제되었습니다.');
					ProductCommissionListSearch();
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

	/**
	 * 수수료등록 팝업 페이지 초기화
	 * @constructor
	 */
	var ProductCommissionWritePopInit = function(){

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

		//판매처 상품코드 중복확인 버튼 바인딩
		$(".btn-market-product-no-check").on("click", function(){
			ProductCommissionCheckMarketProductNo();
		});

		//상품 선택 Grid 초기화
		$("#grid_list_pop_target").jqGrid({
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
				{ label: '상품IDX', name: 'product_idx_hidden', index: 'product_idx_hidden', width: 0, hidden: true, formatter: function(cellvalue, options, rowobject){
						return '<input type="hidden" class="w100per product_idx" name="product_idx[]" value="'+rowobject.product_idx+'" />';

					}},
				{ label: '옵션IDX', name: 'product_option_idx_hidden', index: 'product_option_idx_hidden', width: 0, hidden: true, formatter: function(cellvalue, options, rowobject){
						return '<input type="hidden" class="w100per product_option_idx" name="product_option_idx[]" value="'+rowobject.product_option_idx+'" />';
					}},
				{ label: '상품코드', name: 'product_idx', index: 'product_idx', width: 80, hidden: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left'},
				{ label: '옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, hidden: false},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left'},
				{ label: '삭제', name: 'btnz', index: 'btnz', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						return ' <a href="javascript:;" class="xsmall_btn red_btn btn-delete-add-selected" data-rowid="'+options.rowId+'">삭제</a>';
					}}
			],
			data: product_list_txt,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			sortname: 'product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			idPrefix: 'selected_',
			height: 300,
			loadComplete: function(){
				//삭제 버튼
				$(".btn-delete-add-selected").on("click", function(){
					ProductCommissionDeleteforSeledted($(this));
				});
			},
			afterInsertRow : function(rowid){
				$("#"+rowid + " .btn-delete-add-selected").on("click", function(){
					ProductCommissionDeleteforSeledted($(this));
				});

				//Input Mask 바인딩
				$(".stock_unit_price, .stock_due_amount").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 6, autoGroup: true, rightAlign: false});
			}
		});

		//상품 추가 버튼 바인딩
		$(".btn-product-commission-product-add").on("click", function(){
			var url = '/product/product_commission_product_search_pop.php';
			Common.newWinPopup(url, 'product_commission_product_search_pop', 800, 650, 'yes');
		});

		//폼 바인딩
		bindProductCommissionWriteForm();
	};

	/**
	 * 판매처상품코드 중복확인
	 * @constructor
	 */
	var ProductCommissionCheckMarketProductNo = function(){
		var seller_idx = $("select[name='seller_idx']").val();
		var market_product_no = $.trim($("input[name='market_product_no']").val());

		if(seller_idx == "" || seller_idx == "0"){
			alert("판매처를 선택해주세요.");
			return;
		}

		if(market_product_no == ""){
			alert("판매처 상품코드를 입력해주세요.");
			return;
		}
		showLoader();
		var p_url = "product_commission_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "check_market_product_no";
		dataObj.seller_idx = seller_idx;
		dataObj.market_product_no = market_product_no;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {
				alert('등록가능한 상품코드입니다.');
				$("#isdup").val("Y");
				$("#dup_seller_idx").val(seller_idx);
				$("#dup_market_product_no").val(market_product_no);
			} else {
				alert('이미 등록된 상품코드입니다.');
				$("#isdup").val("N");
				$("#dup_seller_idx").val("");
				$("#dup_market_product_no").val("");
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 발주내역 선택된 상품 삭제
	 * @param $obj : "삭제" 버튼 Object
	 * @constructor
	 */
	var ProductCommissionDeleteforSeledted = function($obj){
		var rowId = $obj.data("rowid");
		$("#grid_list_pop_target").delRowData("selected_"+rowId);
	};

	/**
	 * 수수료등록 폼 진행 여부
	 * @type {boolean}
	 */
	var ProductCommissionWriteFormIng= false;
	/**
	 * 수수료등록 폼 바인딩
	 */
	var bindProductCommissionWriteForm = function () {
		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			if(ProductCommissionWriteFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if($("input[name='mode']").val() == "add") {
					if (objForm.seller_idx.value == "0") {
						alert("판매처를 선택해주세요.");
						return returnType;
					}
					if (!valForm.chkValue(objForm.seller_idx, "판매처를 선택해주세요.", 1, 10, null)) return returnType;
					if (!valForm.chkValue(objForm.market_product_no, "판매처 상품코드를 입력해주세요.", 1, 20, null)) return returnType;
				}
				if (!valForm.chkValue(objForm.market_commission, "판매수수료를 입력해주세요.", 1, 6, null)) return returnType;
				if (!valForm.chkValue(objForm.delivery_commission, "배송비 수수료를 입력해주세요.", 1, 6, null)) return returnType;

				if($("input[name='mode']").val() == "add") {
					//중복확인
					var isdup = $("#isdup").val();
					var seller_idx = objForm.seller_idx.value;
					var market_product_no = objForm.market_product_no.value;

					var dup_seller_idx = $("#dup_seller_idx").val();
					var dup_market_product_no = $("#dup_market_product_no").val();

					if (isdup != "Y" || seller_idx != dup_seller_idx || market_product_no != dup_market_product_no) {
						alert("중복확인을 클릭해주세요.");
						return returnType;
					}
				}

				//내역 확인
				var rowIds = $("#grid_list_pop_target").getRowData();
				if(rowIds.length == 0){
					alert("등록하실 상품을 추가해주세요.");
					return false;
				}

				if(!confirm('수수료 정보를 저장하시겠습니까?')){
					return false;
				}

				this.action = "product_commission_proc.php";
				$("#btn-save").attr("disabled", true);
				ProductCommissionWriteFormIng = true;

				//상품추가 팝업 닫기
				try {
					window.product_commission_product_search_pop.close();
				}catch (e) {

				}

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 수수료등록 상품 추가 팝업 페이지 초기화
	 * @constructor
	 */
	var ProductCommissionAddOptionInit = function(){

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
			ProductCommissionAddOptionExec();
		});

		//발주 상품 Grid 초기화
		var colModel =  [
			{ label: '재고IDX', name: 'stock_idx', index: 'stock_idx', width: 0, hidden: true},
			{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
			{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, is_use : true},
			{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: true},
			{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: true},
			{ label: '매입가', name: 'product_option_purchase_price', index: 'product_option_purchase_price', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
					return Common.addCommas(cellvalue);
				}},
		];
		$("#grid_list_pop").jqGrid({
			url: '/stock/product_commission_product_search_grid.php',
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
				//수정
				$(".btn-product-option-matching-select").on("click", function(){
					//OrderMatchingProductOptionSelect($(this));
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
				ProductCommissionAddOptionSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_pop").on("click", function(){
			ProductCommissionAddOptionSearch();
		});
	};

	/**
	 * 상품 옵션 목록/검색
	 * @constructor
	 */
	var ProductCommissionAddOptionSearch = function(){
		$("#grid_list_pop").setGridParam({
			datatype: "json",
			url: '/product/product_commission_product_search_grid.php',
			postData:{
				param: $("#searchFormPop").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 수수료등록 상품 추가 실행 함수
	 * @constructor
	 */
	var ProductCommissionAddOptionExec = function(){

		//선택된 rowid 가져오기
		var rowIds = $("#grid_list_pop").jqGrid('getGridParam', "selarrrow" );

		if(rowIds.length > 0) {
			try {
				$.each(rowIds, function (i, o) {

					var rowData = $("#grid_list_pop").getRowData(o);
					var product_option_idx = rowData.product_option_idx;
					var targetData = $("#grid_list_pop_target", opener.document).getRowData();
					var isExist = false;
					for (tRow in targetData) {
						if (targetData[tRow].product_option_idx == product_option_idx) {
							isExist = true;
						}
					}
					if (isExist) {
						return;
					} else {
						$("#grid_list_pop_target", opener.document).jqGrid('addRowData', product_option_idx, rowData);
					}
				});
			}catch (e) {
				alert("수수료등록 팝업페이지가 없습니다.");
				self.close();
			}
		}

	};

	return {
		ProductCommissionListInit : ProductCommissionListInit,
		ProductCommissionListReload: ProductCommissionListSearch,
		ProductCommissionWritePopInit: ProductCommissionWritePopInit,
		ProductCommissionAddOptionInit: ProductCommissionAddOptionInit,
	}
})();