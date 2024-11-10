/*
 * 재고 발주/입고 관리 js
 */
var StockOrder = (function() {
	var root = this;

	var init = function () {
	};

	/**
	 * 발주관리 페이지 초기화
	 * @constructor
	 */
	var StockOrderListInit = function(){

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

		//신규발주 버튼 바인딩
		$(".btn-stock-order-write-pop").on("click", function(){
			StockOrderWritePop('');
		});

		//Grid 초기화
		StockOrderListGridInit();

		//이메일발송 팝업 세팅
		$( "#modal_stock_order_email" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		//파일생성 이력 팝업 바인딩
		$(".btn-log-for-file-create-pop").on("click", function(){
			Common.newWinPopup("stock_order_log_file_pop.php", 'stock_order_log_file_pop', 1200, 720, 'yes');
		});

		//다운로드 이력 팝업 바인딩
		$(".btn-log-for-download-pop").on("click", function(){
			Common.newWinPopup("stock_order_log_down_pop.php", 'stock_order_log_down_pop', 1200, 720, 'yes');
		});

		//이메일발송 이력 팝업 바인딩
		$(".btn-log-for-email-send-pop").on("click", function(){
			Common.newWinPopup("stock_order_log_email_pop.php", 'stock_order_log_email_pop', 1200, 720, 'yes');
		});

		//다운로드 버튼 바인딩
		$(".btn-stock_order-xls-down").on("click", function(){
			StockOrderListXlsDown();
		});

	};

	/**
	 * 발주관리 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockOrderListGridInit = function(){
		var grid_cookie_name = "stock_order_list";
		$("#grid_list").jqGrid({
			url: './stock_order_list_grid.php',
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
				{ label: '발주코드', name: 'stock_order_idx', index: 'A.stock_order_idx', width: 80, sortable: true, formatter: function(cellvalue, options, rowobject){
						return '<a href="javascript:;" class="link btn-stock-order-pop" data-stock_order_idx="'+cellvalue+'">'+cellvalue+'</a>';
					}},
				{ label: '발주일', name: 'stock_order_date', index: 'stock_order_date', width: 100, sortable: true},
				{ label: '입고예정일', name: 'stock_order_in_date', index: 'stock_order_in_date', width: 100, sortable: true},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false},
				{ label: '공급처 주소', name: 'supplier_addr', index: 'supplier_addr', width: 250, sortable: false, align: 'left'},
				{ label: '담당자', name: 'stock_order_supplier_name', index: 'stock_order_supplier_name', width: 100, sortable: false},
				{ label: '연락처', name: 'stock_order_supplier_tel', index: 'stock_order_supplier_tel', width: 120, sortable: false},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 120, sortable: false},
				{ label: '발주처리', name: 'btn_action', index: 'btn_action', width: 200, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-order-xls-down" data-stock_order_idx="'+rowobject.stock_order_idx+'">다운로드</a>'
							+' <a href="javascript:;" class="xsmall_btn blue_btn btn-stock-order-send-email" data-stock_order_idx="'+rowobject.stock_order_idx+'">이메일발송</a>';

						if(rowobject.stock_order_is_order == 'N') {
							btnz += ' <a href="javascript:;" class="xsmall_btn green_btn btn-stock-order-execute" data-stock_order_idx="' + rowobject.stock_order_idx + '">발주하기</a>';
						}else if(rowobject.stock_order_is_order == 'Y') {
							btnz += ' <a href="javascript:;" class="xsmall_btn red_btn btn-stock-order-cancel-execute" data-stock_order_idx="' + rowobject.stock_order_idx + '">발주취소</a>';
						}else if(rowobject.stock_order_is_order == 'C') {

						}else if(rowobject.stock_order_is_order == 'T') {


						}
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.stock_order_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){

				//발주코드 링크 바인딩
				$(".btn-stock-order-pop").on("click", function(){
					StockOrderWritePop($(this).data("stock_order_idx"));
				});
				//다운로드
				$(".btn-stock-order-xls-down").on("click", function(){
					var stock_order_idx = $(this).data("stock_order_idx");
					StockOrderDocumentDownload(stock_order_idx, '');
				});
				//이메일발송
				$(".btn-stock-order-send-email").on("click", function(){
					StockOrderEmailSendPopOpen($(this));
				});
				//발주하기
				$(".btn-stock-order-execute").on("click", function(){
					var stock_order_idx = $(this).data("stock_order_idx");
					StockOrderExecute(stock_order_idx);
				});
				//발주최소하기
				$(".btn-stock-order-cancel-execute").on("click", function(){
					var stock_order_idx = $(this).data("stock_order_idx");
					StockOrderCancelExecute(stock_order_idx);
				});

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_order_list", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_order_list");
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
				StockOrderListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockOrderListSearch();
		});
	};

	/**
	 * 발주관리 목록/검색
	 * @constructor
	 */
	var StockOrderListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 발주관리 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockOrderListXlsDown = function(){
		var param = $("#searchForm").serialize();
		location.href="stock_order_xls_down.php?"+param;
	};

	/**
	 * 발주하기 실행!!!!!!!!!!!!!
	 * * @param stock_order_idx : "발주" IDX
	 * @constructor
	 */
	var StockOrderExecute = function(stock_order_idx){
		if(confirm('발주하시겠습니까?')){
			var p_url = "/stock/stock_order_proc_ajax.php";
			var dataObj = new Object();
			dataObj.mode = "stock_order_place_order";
			dataObj.stock_order_idx = stock_order_idx;

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					alert('발주 하였습니다.');

					//발주관리 목록 reLoad
					StockOrderListSearch();
				} else {
					//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					alert(response.msg);
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	/**
	 * 발주취소 실행!!!!!!!!!!!!!
	 * * @param stock_order_idx : "발주" IDX
	 * @constructor
	 */
	var StockOrderCancelExecute = function(stock_order_idx){
		if(confirm('발주를 취소 하시겠습니까?')){
			var p_url = "/stock/stock_order_proc_ajax.php";
			var dataObj = new Object();
			dataObj.mode = "stock_order_cancel_order";
			dataObj.stock_order_idx = stock_order_idx;

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					alert('발주가 취소되었습니다.');

					//발주관리 목록 reLoad
					StockOrderListSearch();
				} else {
					//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					alert(response.msg);
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	/**
	 * 신규발주 팝업 열기
	 * @param stock_order_idx : "발주" IDX
	 * @constructor
	 */
	var StockOrderWritePop = function(stock_order_idx){
		var url = '/stock/stock_order_write_pop.php';
		url += (stock_order_idx != '') ? '?stock_order_idx=' + stock_order_idx : '';
		Common.newWinPopup(url, 'stock_order_write_pop', 950, 750, 'yes');
	};

	/**
	 * 발주 복사 팝업 열기
	 * @param stock_order_idx : "발주" IDX
	 * @constructor
	 */
	var StockOrderCopyWritePop = function(stock_order_idx){
		var url = '/stock/stock_order_write_pop.php?stock_order_idx=' + stock_order_idx + '&iscopy=Y';
		Common.newWinPopup(url, 'stock_order_copy_write_pop', 950, 750, 'yes');
	};

	/**
	 * 신규발주 / 발주확인 팝업 페이지 초기화
	 * @constructor
	 */
	var StockOrderWritePopInit = function(){

		//발주서 상태에 따라 수정 가능 여부 확인
		StockOrderWriteableChange();

		//발주사 담당자 SelectBox 바인딩
		$("select[name='stock_order_officer_name_sel']").on("change", function(){
			StockOrderOfficerChange($(this));
		});

		//발주사 담당자 첫번째 자동 선택
		$("select[name='stock_order_officer_name_sel'] option:eq(1)").prop("selected", true);
		$("select[name='stock_order_officer_name_sel']").trigger("change");

		//공급처 SelectBox 바인딩
		$("select[name='supplier_idx']").on("change", function(){
			$('#grid_list_pop_target').jqGrid('clearGridData');
			StockOrderWriteSupplierChange($(this));
		}).trigger("change");

		//공급처 담당자 SelectBox 바인딩
		$("select[name='stock_order_supplier_name_sel']").on("change", function(){

			var name = $(this).find("option:selected").text();
			var val = $(this).find("option:selected").val();
			var tel;
			if(val != "") {
				tel = $(this).find("option:selected").data("tel");
			}else{
				name = "";
				tel = ""
			}

			$("input[name='stock_order_supplier_name']").val(name);
			$("input[name='stock_order_supplier_tel']").val(tel);
		});

		//발주내역 상품 선택 Grid 초기화
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
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
				{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 0, hidden: true},
				{ label: '재고IDX', name: 'stock_idx', index: 'stock_idx', width: 0, hidden: true},
				{ label: '상품IDX', name: 'product_idx_hidden', index: 'product_idx_hidden', width: 0, hidden: true, formatter: function(cellvalue, options, rowobject){
						return '<input type="hidden" class="w100per product_idx" name="product_idx[]" value="'+rowobject.product_idx+'" />';

					}},
				{ label: '상품코드', name: 'product_option_idx_hidden', index: 'product_option_idx_hidden', width: 0, hidden: true, formatter: function(cellvalue, options, rowobject){
						return '<input type="hidden" class="w100per product_option_idx" name="product_option_idx[]" value="'+rowobject.product_option_idx+'" />';
					}},
				{ label: '재고IDX', name: 'stock_idx_hidden', index: 'stock_idx_hidden', width: 0, hidden: true, formatter: function(cellvalue, options, rowobject){
						return '<input type="hidden" class="w100per stock_idx" name="stock_idx[]" value="'+rowobject.stock_idx+'" />';
					}},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: false},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: false},
				{ label: '단가', name: 'stock_unit_price', index: 'stock_unit_price', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						var val = "0";
						if(typeof cellvalue != "undefined" && cellvalue != "")
						{
							val = cellvalue;
						}
						return '<input type="text" class="w100per stock_unit_price" name="stock_unit_price[]" value="' + val + '" />';

					}},
				{ label: '수량', name: 'stock_due_amount', index: 'stock_due_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						var val = "0";
						if(typeof cellvalue != "undefined" && cellvalue != "")
						{
							val = cellvalue;
						}
						return '<input type="text" class="w100per stock_due_amount" name="stock_due_amount[]" value="'+val+'" />';

					}},
				{ label: '금액', name: 'stock_cal_price', index: 'stock_cal_price', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return '<span class="stock_cal_price"></span>';

					}},

				{ label: '비고', name: 'stock_order_msg', index: 'stock_order_msg', width: 150, sortable: false, formatter: function(cellvalue, options, rowobject){
						var val = "";
						if(typeof cellvalue != "undefined" && cellvalue != "")
						{
							val = cellvalue;
						}
						return '<input type="text" class="w100per stock_order_msg" name="stock_order_msg[]" value="'+val+'" />';

					}},
				{ label: '삭제', name: 'btnz', index: 'btnz', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						return ' <a href="javascript:;" class="xsmall_btn red_btn btn-delete-add-selected" data-rowid="'+options.rowId+'">삭제</a>';
					}}
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			sortname: 'product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			idPrefix: 'selected_',
			height: 150,
			loadComplete: function(){
				$("#grid_list_pop_target").on("keyup", ".stock_unit_price, .stock_due_amount", function(){

					$pTr = $(this).parent().parent();

					var stock_unit_price = $pTr.find(".stock_unit_price").val().replace(/,/gi, '');
					var stock_due_amount = $pTr.find(".stock_due_amount").val().replace(/,/gi, '');

					//console.log(stock_unit_price, stock_due_amount);
					if(stock_unit_price == "") stock_unit_price = 0;
					if(stock_due_amount == "") stock_due_amount = 0;

					var total = stock_unit_price * stock_due_amount;


					$pTr.find(".stock_cal_price").html(Common.addCommas(total));
				});

				$(".stock_unit_price").trigger("keyup");

				//삭제 버튼
				$(".btn-delete-add-selected").on("click", function(){
					StockOrderOptionDeleteforSeledted($(this));
				});


				//발주서 상태에 따라 수정 가능 여부 확인
				StockOrderWriteableChange();

			},
			afterInsertRow : function(rowid){
				console.log(rowid);
				$("#"+rowid + " .btn-delete-add-selected").on("click", function(){
					StockOrderOptionDeleteforSeledted($(this));
				});

				//Input Mask 바인딩
				$(".stock_unit_price, .stock_due_amount").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 6, autoGroup: true, rightAlign: false});
			}
		});

		//상품 추가 버튼 바인딩
		$(".btn-stock-order-add-option").on("click", function(){
			var supplier_idx = $("select[name='supplier_idx'] option:selected").val();
			if(typeof supplier_idx == "undefined"){
				supplier_idx = $("input[name='supplier_idx']").val();
			}

			if(supplier_idx != "") {
				var url = '/stock/stock_order_add_option_pop.php';
				url += '?supplier_idx=' + supplier_idx;
				Common.newWinPopup(url, 'stock_order_add_option_pop', 800, 550, 'yes');
			}else{
				alert("공급처를 먼저 선택해주세요.");
				return;
			}
		});

		//발주 폼 바인딩
		bindStockOrderWriteForm();

		//발주확인 일 경우 발주내역 불러오기
		if($("input[name='stock_order_idx']").val() != ""){
			StockOrderOptionListLoad();
			StockOrderWriteSupplierChange($("input[name='supplier_idx']"));
		}

		//발주 복사 버튼 바인딩
		$(".btn-stock-order-copy").on("click", function(){
			StockOrderCopyWritePop($("input[name='stock_order_idx']").val());
		});
	};

	/**
	 * 발주서 수정 가능 여부 확인
	 * @constructor
	 */
	var StockOrderWriteableChange = function(){
		//발주서 상태에 따라 수정 가능 여부 확인
		var stock_order_is_order = $("input[name='stock_order_is_order']").val();
		if(stock_order_is_order == "Y" || stock_order_is_order == "C" || stock_order_is_order == "T"){

			//Input Text 모두 Disabled
			$("INPUT[type='text']").prop("disabled", true);

			//셀렉트 박스 숨김
			$("SELECT").hide();

			//발주내역 옵션추카 버튼 숨김
			$(".btn-stock-order-add-option").hide();

			//발주내역 삭제 버튼 숨김
			$(".btn-delete-add-selected").hide();
		}
	};

	/**
	 * 발주 내역 불러오기
	 * @constructor
	 */
	var StockOrderOptionListLoad = function(){
		$("#grid_list_pop_target").setGridParam({
			datatype: "json",
			url: '/stock/stock_order_write_pop_grid.php',
			postData:{
				stock_order_idx: $("input[name='stock_order_idx']").val()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 공급처 SelectBox Change Event 함수
	 * @param $obj : "공급처" SelectBox Object
	 * @constructor
	 */
	var StockOrderWriteSupplierChange = function($obj){
		if($obj.val() == undefined || $obj.val() == "") return;

		var p_url = "/stock/stock_order_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_supplier_info";
		dataObj.supplier_idx = $obj.val();

		if($obj.val() != "") {
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					//console.log(response.data);
					//기존에 선택한 값 초기화
					$("input[name='stock_order_supplier_name']").val('');
					$("input[name='stock_order_supplier_tel']").val('');

					var dt = response.data;
					$(".supplier_info_ceo_name").html(dt.supplier_ceo_name);
					$(".supplier_info_addr").html(dt.supplier_addr1 + ' ' + dt.supplier_addr2);
					$(".supplier_info_license_no").html(dt.supplier_license_number);

					$("select[name='stock_order_supplier_name_sel']").empty();
					$("select[name='stock_order_supplier_name_sel']").append('<option value="" data-tel="">담당자 선택</option>');
					for (var i = 1; i < 5; i++) {
						var of_name = eval('dt.supplier_officer' + i + '_name');
						var of_tel = eval('dt.supplier_officer' + i + '_tel');
						if (typeof of_name != "undefined" && of_name != "") {
							$("select[name='stock_order_supplier_name_sel']").append('<option value="' + of_name + '" data-tel="'+of_tel+'">' + of_name + '</option>');
						}
					}
					if($("select[name='stock_order_supplier_name_sel'] option").length > 1) {
						$("select[name='stock_order_supplier_name_sel'] option:eq(1)").prop("selected", true);
						$("select[name='stock_order_supplier_name_sel']").trigger("change");
					}

				} else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}else{
			$(".supplier_info_ceo_name").html('');
			$(".supplier_info_addr").html('');
			$(".supplier_info_license_no").html('');
			$("select[name='stock_order_supplier_name']").empty();
			$("input[name='stock_order_supplier_tel']").val('');
		}
	};

	/**
	 * 발주사 담당자 SelectBox Change Event 함수
	 * @param $obj : "발주사" 담당자 SelectBox Object
	 * @constructor
	 */
	var StockOrderOfficerChange= function($obj){
		var no = $obj.find("option:selected").val();
		if(no != "") {
			var p_url = "/stock/stock_order_proc_ajax.php";
			var dataObj = new Object();
			dataObj.mode = "get_stock_order_officer_info";
			dataObj.officer_no = no;

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					//console.log(response.data);
					$("input[name='stock_order_officer_name']").val(response.data.name);
					$("input[name='stock_order_officer_tel']").val(response.data.tel);
					//$("input[name='stock_order_receiver_name']").val(response.data.name);
					//$("input[name='stock_order_receiver_tel']").val(response.data.tel);

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
	 * 발주 상품 추가 팝업 페이지 초기화
	 * @constructor
	 */
	var StockOrderAddOptionInit = function(){

		//추가 버튼 바인딩
		$(".btn-stock-order-add-product-exec").on("click", function(){
			StockOrderAddOptionExec();
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
			url: '/stock/stock_order_add_option_pop_grid.php',
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
					OrderMatchingProductOptionSelect($(this));
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
				StockOrderAddOptionSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_pop").on("click", function(){
			StockOrderAddOptionSearch();
		});
	};

	/**
	 * 상품 옵션 목록/검색
	 * @constructor
	 */
	var StockOrderAddOptionSearch = function(){
		$("#grid_list_pop").setGridParam({
			datatype: "json",
			url: '/stock/stock_order_add_option_pop_grid.php',
			postData:{
				param: $("#searchFormPop").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 발주 상품 추가 실행 함수
	 * @constructor
	 */
	var StockOrderAddOptionExec = function(){

		//발주 팝업 페이지에 선택된 공급처가 맞는지 확인
		var target_supplier_idx = $("select[name='supplier_idx'] option:selected", opener.document).val();
		if(typeof target_supplier_idx == "undefined"){
			target_supplier_idx = $("input[name='supplier_idx']", opener.document).val();
		}
		var current_supplier_idx = $("input[name='supplier_idx']").val();

		if(current_supplier_idx != target_supplier_idx){
			alert("발주 팝업페이지에 선택된 공급처가 다릅니다.");
			self.close();
			return;
		}

		//선택된 rowid 가져오기
		var rowIds = $("#grid_list_pop").jqGrid('getGridParam', "selarrrow" );

		if(rowIds.length > 0) {
			try {
				$.each(rowIds, function (i, o) {

					var rowData = $("#grid_list_pop").getRowData(o);
					var product_option_idx = rowData.product_option_idx;
					console.log(rowData);

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
				alert("신규발주 팝업페이지가 없습니다.");
				self.close();
			}
		}

	};

	/**
	 * 발주내역 선택된 상품 삭제
	 * @param $obj : "삭제" 버튼 Object
	 * @constructor
	 */
	var StockOrderOptionDeleteforSeledted = function($obj){
		var rowId = $obj.data("rowid");
		$("#grid_list_pop_target").delRowData("selected_"+rowId);
	};

	/**
	 * 발주 폼 진행 여부
	 * @type {boolean}
	 */
	var stockOrderWriteFormIng= false;
	/**
	 * 발주 폼 바인딩
	 */
	var bindStockOrderWriteForm = function () {
		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			if(stockOrderWriteFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.stock_order_date, "발주일을 선택해주세요.", 10, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.stock_order_in_date, "입고예정일을 선택해주세요.", 10, 10, null)) return returnType;

				if (!valForm.chkValue(objForm.stock_order_officer_name, "발주사 담당자를 입력해주세요.", 1, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.stock_order_officer_tel, "발주사 담당자 연락처를 입력해주세요.", 10, 50, null)) return returnType;

				if (!valForm.chkValue(objForm.supplier_idx, "공급처를 선택해주세요.", 1, 0, null)) return returnType;
				if (!valForm.chkValue(objForm.stock_order_supplier_name, "공급처 담당자를 입력해주세요.", 1, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.stock_order_supplier_tel, "공급처 담당자 연락처를 입력해주세요.", 10, 50, null)) return returnType;

				if (!valForm.chkValue(objForm.stock_order_receiver_name, "배송지 고객명을 입력해주세요.", 1, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.stock_order_receiver_tel, "배송지 연락처를 입력해주세요.", 1, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.stock_order_receiver_addr, "배송지 주소를 입력해주세요.", 1, 500, null)) return returnType;


				//발주내역 확인
				var rowIds = $("#grid_list_pop_target").getRowData();
				if(rowIds.length == 0){
					alert("발주하실 상품을 추가해주세요.");
					return false;
				}else{
					var checkTable = true;
					$(".stock_unit_price, .stock_due_amount").each(function(i, o){
						var val = $(this).val().replace(/,/gi, '');   //콤마제거

						if(val == "0" || val == ""){
							alert("단가 및 수량을 입력해주세요.");
							$(this).focus();
							checkTable = false;
							return false;
						}
					});

					if(!checkTable){
						return false;
					}
				}

				if(!confirm('발주 정보를 저장하시겠습니까?')){
					return false;
				}

				this.action = "stock_order_proc.php";
				$("#btn-save").attr("disabled", true);
				stockOrderWriteFormIng = true;

				//상품추가 팝업 닫기
				try {
					window.stock_order_add_option_pop.close();
				}catch (e) {
					
				}

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 발주서 다운로드 함수
	 * @param stock_order_idx : "발주서" IDX
	 * @constructor
	 */
	var StockOrderDocumentDownload = function(stock_order_idx, stock_order_file_idx){

		var url = "/proc/_stock_order_xls_down.php?stock_order_idx="+stock_order_idx;
		if(typeof stock_order_file_idx != "undefined" || stock_order_file_idx != "")
		{
			url += "&stock_order_file_idx="+stock_order_file_idx;
		}

		xls_hidden_frame.location.replace(url);
	};

	/**
	 * 이메일 발송 모달 팝업 Open
	 * @param $obj
	 * @constructor
	 */
	var StockOrderEmailSendPopOpen = function($obj){
		var p_url = "stock_order_email_pop.php";
		var dataObj = new Object();
		dataObj.stock_order_idx = $obj.data("stock_order_idx");
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_stock_order_email").html(response);
				$("#modal_stock_order_email").dialog( "open" );
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
	 * 이메일 발송 모달 팝업 Close
	 * @constructor
	 */
	var StockOrderEmailSendPopClose = function() {
		$("#modal_stock_order_email").dialog( "close" );
	};

	/**
	 * 이메일 발송 팝업 페이지 초기화
	 * @param stock_order_idx : "발주서" IDX
	 * @constructor
	 */
	var StockOrderEmailSendPopInit = function(stock_order_idx){
		//팝업 취소 버튼 바인딩
		$(".btn-stock-order-email-close").on("click", function(){
			StockOrderEmailSendPopClose();
		});

		//수신이메일 Selectbox 바인딩
		bindStockOrderEmailReceiverList();

		//단축URL만들기
		// var sUrl = Common.makeShortUrl('/proc/_stock_order_xls_down.php?stock_order_idx='+stock_order_idx, function(url){
		// 	$("#stock_order_document_short_url").val(url);
		// });

		//첨부파일 다운받기 버튼 바인딩
		$(".btn-stock-order-xls-down").on("click", function(){
			//xls_hidden_frame.location.replace($("#stock_order_document_short_url").val());
			xls_hidden_frame.location.replace('/proc/_stock_order_xls_down.php?stock_order_idx='+stock_order_idx);
		});

		//폼 초기화
		StockOrderEmailSendFormInit();
	};

	/**
	 * 이메일 발송 폼 진행 여부
	 * @type {boolean}
	 * @private
	 */
	var _StockOrderEmailSendIng = false;

	/**
	 * 이메일 발송 폼 초기화
	 * @constructor
	 */
	var StockOrderEmailSendFormInit = function(){
		//저장 버튼
		$("#btn-send-email").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if(!_StockOrderEmailSendIng) {
				$("form[name='dyFormEmail']").submit();
			}
		});

		//폼 Submit 이벤트
		$("form[name='dyFormEmail']").submit(function(e){
			e.preventDefault();
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{

				if(typeof objForm.product_option_mix_1 !== 'undefined'){
					if (!valForm.chkValue(objForm.product_option_mix_1, "옵션1을 정확히 입력해주세요.", 1, 500, null)) return returnType;
				}

				if (!valForm.chkValue(objForm.supplier_email, "수신이메일을 선택해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.email_title, "메일제목을 정확히 입력해주세요..", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.email_content, "메일내용을 정확히 입력해주세요.", 1, 8000, null)) return returnType;

				_StockOrderEmailSendIng = true;
				showLoader();
				var p_url = "stock_order_proc_ajax.php";
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: $("form[name='dyFormEmail']").serialize()
				}).done(function (response) {
					if(response.result)
					{
						alert('발송되었습니다..');
						StockOrderEmailSendPopClose();

					}else{
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
					_StockOrderEmailSendIng = false;
				}).fail(function(jqXHR, textStatus){
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
					_StockOrderEmailSendIng = false;
				});
				return false;

			}catch(e){
				alert(e);
				_StockOrderEmailSendIng = false;
				return false;
			}
		});
	};

	/**
	 * 이메일 발송 수신 이메일 목록 바인딩
	 */
	var bindStockOrderEmailReceiverList = function(){
		var p_url = "/stock/stock_order_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_supplier_officer_email";
		dataObj.supplier_idx = $("#email_pop_supplier_idx").val();

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {
				//console.log(response.data);
				$("select[name='supplier_email'] option").remove();
				$.each(response.data, function(i, o){
					$("select[name='supplier_email']").append('<option value="'+o+'">'+o+'</option>');
				});

			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 파일생성 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var StockOrderLogFileInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

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

		$("#grid_list").jqGrid({
			url: './stock_order_log_file_pop_grid.php',
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
				{ label: '파일생성이력IDX', name: 'stock_order_file_idx', index: 'stock_order_file_idx', width: 80, sortable: false, hidden: true},
				{ label: '발주코드', name: 'stock_order_idx', index: 'A.stock_order_idx', width: 80, sortable: true},
				{ label: '발주일', name: 'stock_order_date', index: 'stock_order_date', width: 100, sortable: true},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false},
				{ label: '공급처 주소', name: 'supplier_addr', index: 'supplier_addr', width: 250, sortable: false, align: 'left'},
				{ label: '담당자', name: 'stock_order_supplier_name', index: 'stock_order_supplier_name', width: 100, sortable: false},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 120, sortable: false},
				{ label: '생성시간', name: 'stock_order_file_regdate', index: 'stock_order_file_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '-', name: 'btn_action', index: 'btn_action', width: 160, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-order-down" data-stock_order_file_idx="'+rowobject.stock_order_file_idx+'" data-stock_order_idx="'+rowobject.stock_order_idx+'">다운받기</a>'
							+' <a href="javascript:;" class="xsmall_btn red_btn btn-stock-order-send-email" data-stock_order_idx="'+rowobject.stock_order_idx+'">이메일발송</a>';
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.stock_order_file_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){

				//이메일발송
				$(".btn-stock-order-send-email").on("click", function(){
					StockOrderEmailSendPopOpen($(this));
				});
				//다운받기
				$(".btn-stock-order-down").on("click", function(){
					StockOrderDocumentDownload($(this).data("stock_order_idx"), $(this).data("stock_order_file_idx"));
				});

				//브라우저 리사이즈 trigger
				$(window).trigger("resize");
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
				StockOrderLogFileListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockOrderLogFileListSearch();
		});

		//이메일발송 팝업 세팅
		$( "#modal_stock_order_email" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});
	};

	/**
	 * 파일생성 목록/검색
	 * @constructor
	 */
	var StockOrderLogFileListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 이메일 발송 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var StockOrderLogEmailInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

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

		$("#grid_list").jqGrid({
			url: './stock_order_log_email_pop_grid.php',
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
				{ label: '파일생성로그IDX', name: 'stock_order_file_idx', index: 'stock_order_file_idx', width: 0, sortable: false, hidden: true},
				{ label: '발송시간', name: 'stock_order_email_regdate', index: 'stock_order_email_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '발주코드', name: 'stock_order_idx', index: 'A.stock_order_idx', width: 80, sortable: true},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 120, sortable: false},
				{ label: '수신자', name: 'stock_order_email_receiver', index: 'stock_order_email_receiver', width: 100, sortable: false},
				{ label: '제목', name: 'stock_order_email_title', index: 'stock_order_email_title', width: 120, sortable: false},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 80, sortable: false},
				{ label: '첨부파일', name: 'btn_action', index: 'btn_action', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-order-down" data-stock_order_file_idx="'+rowobject.stock_order_file_idx+'" data-stock_order_idx="'+rowobject.stock_order_idx+'">다운받기</a>'
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.stock_order_email_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//다운받기
				$(".btn-stock-order-down").on("click", function(){
					StockOrderDocumentDownload($(this).data("stock_order_idx"), $(this).data("stock_order_file_idx"));
				});

				//브라우저 리사이즈 trigger
				$(window).trigger("resize");

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
				StockOrderLogEmailListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockOrderLogEmailListSearch();
		});

		//이메일발송 팝업 세팅
		$( "#modal_stock_order_email" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});
	};

	/**
	 * 이메일 발송 목록/검색
	 * @constructor
	 */
	var StockOrderLogEmailListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 발주서 다운로드 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var StockOrderLogDownInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

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

		$("#grid_list").jqGrid({
			url: './stock_order_log_down_pop_grid.php',
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
				{ label: '파일생성이력IDX', name: 'stock_order_file_idx', index: 'stock_order_file_idx', width: 0, sortable: false, hidden: true},
				{ label: '다운로드시간', name: 'stock_order_file_down_regdate', index: 'stock_order_file_down_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '발주코드', name: 'stock_order_idx', index: 'A.stock_order_idx', width: 80, sortable: true},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 120, sortable: false},
				{ label: '수신자', name: 'stock_order_email_receiver', index: 'stock_order_email_receiver', width: 100, sortable: false},
				{ label: '제목', name: 'stock_order_email_title', index: 'stock_order_email_title', width: 120, sortable: false},
				{ label: '첨부파일', name: 'btn_action', index: 'btn_action', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-order-down" data-stock_order_file_idx="'+rowobject.stock_order_file_idx+'" data-stock_order_idx="'+rowobject.stock_order_idx+'">다운받기</a>'
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.stock_order_file_down_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//다운받기
				$(".btn-stock-order-down").on("click", function(){
					StockOrderDocumentDownload($(this).data("stock_order_idx"), $(this).data("stock_order_file_idx"));
				});

				//브라우저 리사이즈 trigger
				$(window).trigger("resize");

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
				StockOrderLogEmailListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockOrderLogEmailListSearch();
		});

		//이메일발송 팝업 세팅
		$( "#modal_stock_order_email" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});
	};

	/**
	 * 발주서 다운로드 이력 목록/검색
	 * @constructor
	 */
	var StockOrderLogDownListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	return {
		StockOrderListInit : StockOrderListInit,
		StockOrderWritePopInit: StockOrderWritePopInit,
		StockOrderAddOptionInit: StockOrderAddOptionInit,
		StockOrderListReload: StockOrderListSearch,
		StockOrderDocumentDownload: StockOrderDocumentDownload,
		StockOrderEmailSendPopInit: StockOrderEmailSendPopInit,
		StockOrderLogFileInit: StockOrderLogFileInit,
		StockOrderLogEmailInit: StockOrderLogEmailInit,
		StockOrderLogDownInit: StockOrderLogDownInit,
	}
})();