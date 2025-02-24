/*
 * 배송관리 js
 */
var OrderShipped = (function() {
	var root = this;

	var init = function() {
	};

	/**
	 * 주문다운로드 페이지 초기화
	 * @constructor
	 */
	var OrderDownloadInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "6");

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
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
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

		//Grid 초기화
		OrderDownloadGridInit();

		//상세내역 Grid 초기화
		OrderDownloadDetailGridInit();

		//파일생성 이력 팝업 바인딩
		$(".btn-log-for-file-create-pop").on("click", function(){
			Common.newWinPopup("order_download_log_file_pop.php", 'order_download_log_file_pop', 1400, 720, 'yes');
		});

		//다운로드 이력 팝업 바인딩
		$(".btn-log-for-download-pop").on("click", function(){
			Common.newWinPopup("order_download_log_down_pop.php", 'order_download_log_down_pop', 1200, 720, 'yes');
		});

		//이메일발송 이력 팝업 바인딩
		$(".btn-log-for-email-send-pop").on("click", function(){
			Common.newWinPopup("order_download_log_email_pop.php", 'order_download_log_email_pop', 1200, 720, 'yes');
		});


		//파일다운로드
		$(".btn_file_download").on("click", function(){
			OrderDownloadSupplierFormat(__OrderDownloadCurrentSupplierIdx, true);
		});

		//이메일발송
		$(".btn_email_send").on("click", function(){
			OrderDownloadEmailSendPopOpen();
		});

		//이메일발송 팝업 세팅
		$( "#modal_order_download_email" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		//발주서 포맷 설정 modal popup 초기화
		$( "#modal_order_download_format_pop" ).dialog({
			width: 550,
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


		
	};

	/**
	 * 주문다운로드 - 공급처별 주문현황 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderDownloadGridInit = function(){
		$("#grid_list").jqGrid({
			url: './order_download_order_grid.php',
			mtype: "GET",
			datatype: "local",
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
				{ label: '공급처<br>그룹', name: 'manage_group_name', index: 'manage_group_name', width: 100, sortable: false},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, align: 'left', formatter: function(cellvalue, options, rowobject){
						return '<a href="javascript:;" class="link btn-order-detail-view" data-supplier_idx="'+rowobject.member_idx+'">'+cellvalue+'</a>';
					}},
				{ label: '공급처코드', name: 'member_idx', index: 'member_idx', width: 150, sortable: false},
				{ label: '일반주문<br>요청개수', name: 'order_cnt', index: 'order_cnt', width: 150, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '합포주문<br>요청개수', name: 'package_cnt', index: 'package_cnt', width: 150, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '총 상품<br>개수', name: 'option_cnt', index: 'option_cnt', width: 150, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '-', name: 'btn_group', index: 'btn_group', width: 150, sortable: false, formatter: function(cellvalue, options, rowobject){
						return ' <a href="javascript:;" class="xsmall_btn blue_btn btn-order-download-format" data-supplier_idx="'+rowobject.member_idx+'">포맷설정</a>';

					}},
			],
			rowNum: 10000,
			pager: '#grid_pager',
			pgbuttons : false,
			pgtext: null,
			sortname: 'S.supplier_name',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//공급처 클릭
				$(".btn-order-detail-view").on("click", function(){
					var supplier_idx = $(this).data("supplier_idx");
					OrderDownloadDetailSearch(supplier_idx);
				});

				//주문다운로드 포맷 설정
				$(".btn-order-download-format").on("click", function(){
					OrderDownloadFormatPopOpen($(this));
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
				OrderDownloadSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderDownloadSearch();
		});

		//발주서 포맷 설정 modal popup 초기화
		$( "#modal_order_download_format_pop" ).dialog({
			width: 550,
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
	};

	var __OrderDownloadSearchVars = new Object();
	__OrderDownloadSearchVars.supplier_idx = "";
	__OrderDownloadSearchVars.seller_idx = "";
	__OrderDownloadSearchVars.date_start = "";
	__OrderDownloadSearchVars.date_end = "";
	__OrderDownloadSearchVars.time_start = "";
	__OrderDownloadSearchVars.time_end = "";
	__OrderDownloadSearchVars.receive_name = "";
	__OrderDownloadSearchVars.deilivery_type = "";
	__OrderDownloadSearchVars.order_progress_step = "";

	var __OrderDownloadCurrentSupplierIdx = 0;

	/**
	 * 주문다운로드 - 공급처별 주문현황 목록/검색
	 * @constructor
	 */
	var OrderDownloadSearch = function(){

		__OrderDownloadSearchVars.supplier_idx = $("select[name='supplier_idx']").val();
		__OrderDownloadSearchVars.seller_idx = $("select[name='seller_idx']").val();
		__OrderDownloadSearchVars.date_start = $("input[name='date_start']").val();
		__OrderDownloadSearchVars.date_end = $("input[name='date_end']").val();
		__OrderDownloadSearchVars.time_start = $("input[name='time_start']").val();
		__OrderDownloadSearchVars.time_end = $("input[name='time_end']").val();
		__OrderDownloadSearchVars.receive_name = $("input[name='receive_name']").val();
		__OrderDownloadSearchVars.deilivery_type = $("select[name='deilivery_type']").val();
		__OrderDownloadSearchVars.order_progress_step = $("select[name='order_progress_step']").val();
		__OrderDownloadSearchVars.detail_list = $("#detail_list").val();

		//console.log(__OrderDownloadSearchVars);
		$("#grid_list2").jqGrid("clearGridData", true);

		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 주문다운로드 - 상세내역 Grid 초기화
	 * @constructor
	 */
	var OrderDownloadDetailGridInit = function(){
		$("#grid_list2").jqGrid({
			url: './order_download_detail_grid.php',
			mtype: "GET",
			datatype: "local",
			postData:{
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
				{ label: 'order_matching_idx', name: 'order_matching_idx', index: 'order_matching_idx', width: 120, sortable: false, align: 'left', hidden: true},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 120, sortable: false, align: 'left'},
				{ label: '발주일', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTimeOnlyDate(cellvalue);
					}},
				{ label: '발주시간', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTimeOnlyTime(cellvalue);
					}},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 80, sortable: false},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 60, sortable: false},
				{ label: '상품명+옵션', name: 'product_full_name', index: 'product_full_name', width: 150, sortable: false, align: 'left', formatter: function(cellvalue, options, rowobject){
						return rowobject.product_name + ' / ' + rowobject.product_option_name;
					}}
			],
			rowNum: 1000,
			rowList: [],
			pager: '#grid_pager2',
			sortname: 'O.order_regdate',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			multiselect: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list2");
			}
		});
		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list2");
		}).trigger("resize");
	};


	/**
	 * 주문다운로드 - 상세내역 Grid 실행
	 * @param supplier_idx
	 * @constructor
	 */
	var OrderDownloadDetailSearch = function(supplier_idx){

		__OrderDownloadCurrentSupplierIdx = supplier_idx;

		$("#grid_list2").setGridParam({
			datatype: "json",
			postData:{
				supplier_idx: supplier_idx,
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 *
	 * @param supplier_idx
	 * @constructor
	 */
	var OrderDownloadSupplierFormat = function(supplier_idx, selected){

		$("#detail_list").val("");
		if(selected){
			//$("#detail_list")
			var selRowId = $("#grid_list2").getGridParam("selarrrow");

			if(selRowId == null || selRowId.length == 0){
				alert('선택된 내용이 없습니다.');
				return;
			}

			var idx_list = new Array();

			$.each(selRowId, function(i, o){
				var rowData =$("#grid_list2").getRowData(o);
				idx_list.push(rowData.order_matching_idx);
			});
			$("#detail_list").val(idx_list.join(','));
		}
		__OrderDownloadSearchVars.detail_list = $("#detail_list").val();

		var p_url = "order_download_supplier_xls.php?supplier_idx="+supplier_idx;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: __OrderDownloadSearchVars,
			traditional: true
		}).done(function (response) {
			if(response.result)
			{
				OrderDownloadDocumentDownload(supplier_idx, response.order_download_file_idx);
				$("#detail_list").val("");
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
	 * 주문다운로드 파일 다운로드 함수
	 * @param stock_order_idx : "발주서" IDX
	 * @constructor
	 */
	var OrderDownloadDocumentDownload = function(supplier_idx, order_download_file_idx){

		var url = "/proc/_order_download_xls_down.php?supplier_idx="+supplier_idx+"&order_download_file_idx="+order_download_file_idx;
		xls_hidden_frame.location.replace(url);
	};

	/**
	 * 주문다운로드 포맷 설정 팝업 열기
	 * @param $obj
	 * @constructor
	 */
	var OrderDownloadFormatPopOpen = function($obj){
		var p_url = "order_download_format_pop.php";
		var dataObj = new Object();
		dataObj.supplier_idx = $obj.data("supplier_idx");
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_order_download_format_pop").html(response);
				$("#modal_order_download_format_pop").dialog( "open" );
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
	 * 주문다운로드 포맷 설정 팝업 닫기
	 * @constructor
	 */
	var OrderDownloadFormatPopClose = function(){
		$("#modal_order_download_format_pop").html("");
		$("#modal_order_download_format_pop").dialog( "close" );
	};

	/**
	 * 주문다운로드 포맷 설정 팝업 페이지 초기화
	 * @constructor
	 */
	var OrderDownloadFormatPopInit = function(){

		$(".format_default_list").SumoSelect({
			search: true,
			searchText: '검색',
			noMatch : '검색결과가 없습니다.',
		});

		$("#btn-save-format").on("click", function(){
			OrderDownloadFormatSave();
		});

		$(".btn-order-download-format-pop-close").on("click", function(){
			OrderDownloadFormatPopClose();
		});

	};

	/**
	 * 주문다운로드 포맷 설정 팝업 : 저장
	 * @constructor
	 */
	var OrderDownloadFormatSave = function(){
		if(confirm('저장 하시겠습니까?')) {
			showLoader();
			var p_url = "order_download_format_proc.php";
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyForm2']").serialize()
			}).done(function (response) {
				if (response.result) {
					alert('저장되었습니다.');
					OrderDownloadFormatPopClose();
					//Grid reLoad
					// OrderListGridInit();

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
	 * 이메일 발송 모달 팝업 Open
	 * @param $obj
	 * @constructor
	 */
	var OrderDownloadEmailSendPopOpen = function(supplier_idx, order_download_file_idx){
		var p_url = "order_download_email_pop.php";
		var dataObj = new Object();
		// dataObj.supplier_idx = __OrderDownloadCurrentSupplierIdx
		dataObj.supplier_idx = supplier_idx;
		dataObj.order_download_file_idx = order_download_file_idx;
		console.log(dataObj);
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_order_download_email").html(response);
				$("#modal_order_download_email").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			alert('생성된 파일이 없거나, 존재하지 않는 공급처 입니다.\n 포맷설정 또는 파일다운로드를 통해 파일을 생성해주세요.');
			hideLoader();
		});
	};

	/**
	 * 이메일 발송 모달 팝업 Close
	 * @constructor
	 */
	var OrderDownloadEmailSendPopClose = function() {
		$("#modal_order_download_email").dialog( "close" );
	};

	/**
	 * 이메일 발송 팝업 페이지 초기화
	 * @param stock_order_idx : "발주서" IDX
	 * @constructor
	 */
	var OrderDownloadEmailSendPopInit = function(){
		//팝업 취소 버튼 바인딩
		$(".btn-stock-order-email-close").on("click", function(){
			OrderDownloadEmailSendPopClose();
		});

		//단축URL만들기
		// var sUrl = Common.makeShortUrl('/proc/_stock_order_xls_down.php?stock_order_idx='+stock_order_idx, function(url){
		// 	$("#stock_order_document_short_url").val(url);
		// });

		//첨부파일 다운받기 버튼 바인딩
		$(".btn-order-download-xls-down").on("click", function(){
			//xls_hidden_frame.location.replace($("#stock_order_document_short_url").val());
			xls_hidden_frame.location.replace('/proc/_order_download_xls_down.php?order_download_file_idx='+$(this).data("idx"));
		});

		//폼 초기화
		OrderDownloadEmailSendFormInit();
	};

	/**
	 * 이메일 발송 폼 진행 여부
	 * @type {boolean}
	 * @private
	 */
	var _OrderDownloadEmailSendIng = false;

	/**
	 * 이메일 발송 폼 초기화
	 * @constructor
	 */
	var OrderDownloadEmailSendFormInit = function(){
		//저장 버튼
		$("#btn-send-email").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if(!_OrderDownloadEmailSendIng) {
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
				if (!valForm.chkValue(objForm.supplier_email, "수신이메일을 선택해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.email_title, "메일제목을 정확히 입력해주세요..", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.email_content, "메일내용을 정확히 입력해주세요.", 1, 8000, null)) return returnType;

				_OrderDownloadEmailSendIng = true;
				showLoader();
				var p_url = "order_download_proc_ajax.php";
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: $("form[name='dyFormEmail']").serialize()
				}).done(function (response) {
					if(response.result)
					{
						alert('발송되었습니다..');
						OrderDownloadEmailSendPopClose();

					}else{
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
					_OrderDownloadEmailSendIng = false;
				}).fail(function(jqXHR, textStatus){
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
					_OrderDownloadEmailSendIng = false;
				});
				return false;

			}catch(e){
				alert(e);
				_OrderDownloadEmailSendIng = false;
				return false;
			}
		});
	};

	/**
	 * 파일생성 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var OrderDownloadLogFileInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "1");

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
			url: './order_download_log_file_pop_grid.php',
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
				{ label: '파일생성이력IDX', name: 'order_download_file_idx', index: 'order_download_file_idx', width: 80, sortable: false, hidden: true},
				// { label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 80, sortable: true},
				// { label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: true},
				// { label: '선착불', name: 'sch_delivery_type', index: 'sch_delivery_type', width: 100, sortable: true},
				// { label: '상태', name: 'order_progress_step_han', index: 'order_progress_step_han', width: 100, sortable: true},
				// { label: '수령자', name: 'sch_receive_name', index: 'sch_receive_name', width: 100, sortable: true},
				// { label: '발주기간', name: 'sch_date_start', index: 'sch_date_start', width: 100, sortable: true},
				// { label: '발주기간', name: 'sch_date_end', index: 'sch_date_end', width: 100, sortable: true},
				{ label: '공급처', name: 'target_supplier_name', index: 'target_supplier_name', width: 150, sortable: false},
				{ label: '발주용 이메일', name: 'target_supplier_email_order', index: 'target_supplier_email_order', width: 150, sortable: false},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 120, sortable: false},
				{ label: '생성시간', name: 'order_download_file_regdate', index: 'order_download_file_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '-', name: 'btn_action', index: 'btn_action', width: 160, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-order-download-down" data-supplier_idx="'+rowobject.supplier_idx+'" data-order_download_file_idx="'+rowobject.order_download_file_idx+'">다운받기</a>'
							+' <a href="javascript:;" class="xsmall_btn red_btn btn-order-download-send-email" data-supplier_idx="'+rowobject.supplier_idx+'" data-order_download_file_idx="'+rowobject.order_download_file_idx+'">이메일발송</a>';
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.order_download_file_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: false,
			shrinkToFit: true,
			multiselect: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){



				//이메일발송
				$(".btn-order-download-send-email").on("click", function(){
					__OrderDownloadCurrentSupplierIdx = $(this).data("supplier_idx");
					OrderDownloadEmailSendPopOpen(__OrderDownloadCurrentSupplierIdx, $(this).data("order_download_file_idx"));
				});
				//다운받기
				$(".btn-order-download-down").on("click", function(){
					__OrderDownloadCurrentSupplierIdx = $(this).data("supplier_idx");
					OrderDownloadDocumentDownload(__OrderDownloadCurrentSupplierIdx, $(this).data("order_download_file_idx"));
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
				OrderDownloadLogFileListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderDownloadLogFileListSearch();
		});

		//이메일발송 팝업 세팅
		$( "#modal_order_download_email" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		//선택 이메일 발송 버튼 바인딩
		$(".btn-send-email-selected").on("click", function(){

			OrderDownloadLogFileSelectedEmailSend();
		});
	};

	var OrderDownloadLogFileSelectedEmailSend = function(){

		var selRowId = $("#grid_list").getGridParam("selarrrow");

		if(selRowId == null || selRowId.length == 0){
			alert('선택된 내용이 없습니다.');
			return;
		}

		if(!confirm('선택하신 파일들을 이메일로 발송하시겠습니까?\n메일 주소, 메일 제목, 내용 등을 수정할 수 없습니다.\n공급처 정보에 발주용 이메일이 없을 경우 발송 되지 않습니다. ')) return;

		var idx_list = new Array();

		$.each(selRowId, function(i, o){
			var rowData =$("#grid_list").getRowData(o);
			idx_list.push(rowData.order_download_file_idx);
		});

		var p_url = "order_download_proc_ajax.php";

		var dataObj = new Object();
		dataObj.mode = "send_email_log_selected";
		dataObj.idx_list = idx_list;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {
				//console.log(response);
				//$(o).html('<img src="' + response.thumb.src + '" />');
				var msg = "전체 " + response.data.total + "건 중 " + response.data.send + "건 발송 완료";
				alert(msg);
			} else {
				//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});

	};

	/**
	 * 파일생성 목록/검색
	 * @constructor
	 */
	var OrderDownloadLogFileListSearch = function(){
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
	var OrderDownloadLogEmailInit = function(){
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
			url: './order_download_log_email_pop_grid.php',
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
				{ label: '파일생성로그IDX', name: 'order_download_file_idx', index: 'order_download_file_idx', width: 0, sortable: false, hidden: true},
				{ label: 'supplier_idx', name: 'supplier_idx', index: 'supplier_idx', width: 0, sortable: false, hidden: true},
				{ label: '발송시간', name: 'order_download_email_regdate', index: 'order_download_email_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 120, sortable: false},
				{ label: '수신자', name: 'order_download_email_receiver', index: 'order_download_email_receiver', width: 100, sortable: false},
				{ label: '제목', name: 'order_download_email_title', index: 'order_download_email_title', width: 120, sortable: false},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 80, sortable: false},
				{ label: '첨부파일', name: 'btn_action', index: 'btn_action', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-order-download-file-down" data-order_download_file_idx="'+rowobject.order_download_file_idx+'" data-supplier_idx="'+rowobject.supplier_idx+'">다운받기</a>'
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.order_download_email_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//다운받기
				$(".btn-order-download-file-down").on("click", function(){
					OrderDownloadDocumentDownload($(this).data("supplier_idx"), $(this).data("order_download_file_idx"));
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
				OrderDownloadLogEmailListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderDownloadLogEmailListSearch();
		});

		//이메일발송 팝업 세팅
		$( "#modal_order_download_email" ).dialog({
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
	var OrderDownloadLogEmailListSearch = function(){
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
	var OrderDownloadLogDownInit = function(){
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
			url: './order_download_log_down_pop_grid.php',
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
				{ label: '파일생성이력IDX', name: 'order_download_file_idx', index: 'order_download_file_idx', width: 0, sortable: false, hidden: true},
				{ label: '다운로드시간', name: 'order_download_file_down_regdate', index: 'order_download_file_down_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 120, sortable: false},
				{ label: '수신자', name: 'order_download_email_receiver', index: 'order_download_email_receiver', width: 100, sortable: false},
				{ label: '제목', name: 'order_download_email_title', index: 'order_download_email_title', width: 120, sortable: false},
				{ label: '첨부파일', name: 'btn_action', index: 'btn_action', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-order-download-file-down" data-order_download_file_idx="'+rowobject.order_download_file_idx+'" data-supplier_idx="'+rowobject.supplier_idx+'">다운받기</a>'
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.order_download_file_down_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//다운받기
				$(".btn-order-download-file-down").on("click", function(){
					OrderDownloadDocumentDownload($(this).data("supplier_idx"), $(this).data("order_download_file_idx"));
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
				OrderDownloadLogDownListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderDownloadLogDownListSearch();
		});

		//이메일발송 팝업 세팅
		$( "#modal_order_download_email" ).dialog({
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
	var OrderDownloadLogDownListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};



	/**
	 * 송장입력 엑셀 업로드 관련 내용 저장 변수
	 * @type {Object}
	 * @private
	 */
	var __OrderInvoice = new Object();
	__OrderInvoice.xlsValueRow = 0;                 //업로드된 엑셀 Row 중 정상인 Row 수
	__OrderInvoice.xlsUploadedFileName = "";        //업로드 된 엑셀 파일명
	__OrderInvoice.xlsWritePageMode = "";           //일괄등록 / 일괄수정 Flag
	__OrderInvoice.xlsWriteReturnStyle = "";        //리스트 반환 또는 적용
	__OrderInvoice.xlsUserFileName = "";            //사용자가 업로드한 파일명

	/**
	 * 송장입력 페이지 초기화
	 * @constructor
	 */
	var OrderInvoiceUploadInit = function(){
		__OrderInvoice.xlsWritePageMode = $("#xlswrite_mode").val();
		__OrderInvoice.xlsWriteReturnStyle = $("#xlswrite_act").val();

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
			if(__OrderInvoice.xlsValidRow < 1)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = __OrderInvoice.xlsValidRow + "건의 데이터를 적용 하시겠습니까?";
				if(confirm(msg)) {
					OrderInvoiceUploadXlsInsert();
				}
			}
		});

		//이력 버튼 바인딩
		$(".btn-upload-log-pop").on("click", function(){
			Common.newWinPopup("/order/order_invoice_upload_log_pop.php", 'order_invoice_upload_log_pop', 800, 720, 'no');
		});

		OrderInvoiceUploadGridInit();
	};

	/**
	 * 송장입력 목록 jqGrid 초기화
	 * @constructor
	 */
	var OrderInvoiceUploadGridInit = function(){
		var validErr = [];

		$("#grid_list").jqGrid({
			url: './order_invoice_upload_proc_xls.php',
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
				{ label: '발주일', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 150, sortable: false},
				{ label: '관리번호', name: 'A', index: 'order_idx', width: 100, sortable: false},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 120, sortable: false},
				{ label: '등록송장번호', name: 'invoice_no', index: 'invoice_no', width: 150, sortable: false, align: 'left'},
				{ label: '등록택배사', name: 'delivery_name', index: 'delivery_name', width: 150, sortable: false, align: 'left'},
				{ label: '반영송장번호', name: 'B', index: 'invoice_no2', width: 150, sortable: false, align: 'left'},
				{ label: '반영택배사', name: 'C', index: 'delivery_name2', width: 150, sortable: false, align: 'left'},
				{ label: '상태', name: 'order_progress_step_han', index: 'order_progress_step_han', width: 120, sortable: false},
				{ label: '업로드시간', name: 'invoice_date', index: 'invoice_date', width: 150, sortable: false},
				{ label: '비고', name: 'valid', index: 'valid', width: 150, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;
						if(cellvalue)
						{
							rst = "정상";
							__OrderInvoice.xlsValidRow++;
						}else{
							rst = "오류";
							if(typeof rowobject.err_msg != "undefined" && rowobject.err_msg != "")
							{
								rst+= "\n" + rowobject.err_msg;
							}
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
				__OrderInvoice.xlsValidRow = 0;
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	/**
	 * 송장입력 - 업로드 된 엑셀 파일 로딩
	 * @param xls_file_path_name
	 * @constructor
	 */
	var OrderInvoiceUploadXlsRead = function(xls_file_path_name, xls_user_filename){
		//console.log(xls_file_path_name);
		__OrderInvoice.xlsUploadedFileName = xls_file_path_name;
		__OrderInvoice.xlsUserFileName = xls_user_filename;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		__OrderInvoice.xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				mode: __OrderInvoice.xlsWritePageMode,
				act: __OrderInvoice.xlsWriteReturnStyle,
				xls_filename: xls_file_path_name,
				user_filename: xls_user_filename,
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 송장입력 - 업로드 된 엑셀 파일 적용
	 * @constructor
	 */
	var OrderInvoiceUploadXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "order_invoice_upload_proc_xls.php";
		var dataObj = new Object();
		dataObj.mode = __OrderInvoice.xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = __OrderInvoice.xlsUploadedFileName;
		dataObj.user_filename = __OrderInvoice.xlsUserFileName;
		dataObj.xls_validrow = __OrderInvoice.xlsValidRow;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj,
			traditional: true
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

	/**
	 * 송장입력 업로드 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var OrderInvoiceUploadLogPopInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "1");

		$("#grid_list").jqGrid({
			url: './order_invoice_upload_log_pop_grid.php',
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
				{ label: '업로드시간', name: 'order_invoice_upload_log_regdate', index: 'order_invoice_upload_log_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '송장입력 수', name: 'order_invoice_upload_log_apply_count', index: 'order_invoice_upload_log_apply_count', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '파일명', name: 'order_invoice_upload_log_userfilename', index: 'order_invoice_upload_log_userfilename', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){
						return '<a href="javascript:;" class="link btn-xls-down" data-idx="'+rowobject.order_invoice_upload_log_idx+'" data-xls_filename="'+rowobject.order_invoice_upload_log_savefilename+'">'+cellvalue+'</a>';
					}},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 120, sortable: false},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.order_invoice_upload_log_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				Common.jqGridResize("#grid_list");

				//브라우저 리사이즈 trigger
				$(window).trigger("resize");

				//파일다운로드
				$(".btn-xls-down").on("click", function(){
					var order_invoice_upload_log_idx = $(this).data("idx");
					var order_invoice_upload_log_savefilename = $(this).data("xls_filename");
					var url = "/proc/_filedownload_invoice.php?idx=" + order_invoice_upload_log_idx + "&filename=" + order_invoice_upload_log_savefilename;
					$("#hidden_ifrm_common_filedownload").attr("src", url);
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
				OrderInvoiceUploadLogPopSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderInvoiceUploadLogPopSearch();
		});
	};

	/**
	 * 송장입력 업로드 이력 팝업 목록/검색
	 * @constructor
	 */
	var OrderInvoiceUploadLogPopSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 송장일괄삭제 엑셀 업로드 관련 내용 저장 변수
	 * @type {Object}
	 * @private
	 */
	var __OrderInvoiceDeleteXls = new Object();
	__OrderInvoiceDeleteXls.xlsValueRow = 0;                 //업로드된 엑셀 Row 중 정상인 Row 수
	__OrderInvoiceDeleteXls.xlsUploadedFileName = "";        //업로드 된 엑셀 파일명
	__OrderInvoiceDeleteXls.xlsWritePageMode = "";           //일괄등록 / 일괄수정 Flag
	__OrderInvoiceDeleteXls.xlsWriteReturnStyle = "";        //리스트 반환 또는 적용
	__OrderInvoiceDeleteXls.xlsUserFileName = "";            //사용자가 업로드한 파일명
	__OrderInvoiceDeleteXls.isIncludeShipped = false;           //배송주문 포함 여부

	/**
	 * 송장일괄삭제 페이지 초기화
	 * @constructor
	 */
	var OrderInvoiceDeleteXlsInit = function(){
		__OrderInvoiceDeleteXls.xlsWritePageMode = $("#xlswrite_mode").val();
		__OrderInvoiceDeleteXls.xlsWriteReturnStyle = $("#xlswrite_act").val();

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
			if(__OrderInvoiceDeleteXls.xlsValidRow < 1)
			{
				alert("삭제할 데이터가 없습니다.");
				return;
			}else{
				//var msg = __OrderInvoiceDeleteXls.xlsValidRow + "건의 송장번호를 삭제 하시겠습니까?";
				var msg = "삭제된 송장정보는 복구되지 않습니다.\n송장정보가 삭제된 주문은 접수상태로 변경됩니다.\n송장정보를 삭제 하시겠습니까?";
				if(confirm(msg)) {
					OrderInvoiceDeleteXlsInsert();
				}
			}
		});

		OrderInvoiceDeleteXlsGridInit();
	};

	/**
	 * 송장일괄삭제 목록 jqGrid 초기화
	 * @constructor
	 */
	var OrderInvoiceDeleteXlsGridInit = function(){
		var validErr = [];

		$("#grid_list").jqGrid({
			url: './order_invoice_delete_xls_proc.php',
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
				{ label: '송장번호', name: 'A', index: 'invoice_no', width: 80, sortable: false},
				{ label: '비고', name: 'valid', index: 'valid', width: 150, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;
						if(cellvalue)
						{
							rst = "정상";
							__OrderInvoiceDeleteXls.xlsValidRow++;
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
				__OrderInvoiceDeleteXls.xlsValidRow = 0;
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	/**
	 * 송장일괄삭제 - 업로드 된 엑셀 파일 로딩
	 * @param xls_file_path_name
	 * @constructor
	 */
	var OrderInvoiceDeleteXlsRead = function(xls_file_path_name, xls_user_filename){
		//console.log(xls_file_path_name);
		__OrderInvoiceDeleteXls.xlsUploadedFileName = xls_file_path_name;
		__OrderInvoiceDeleteXls.xlsUserFileName = xls_user_filename;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		__OrderInvoiceDeleteXls.xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				mode: __OrderInvoiceDeleteXls.xlsWritePageMode,
				act: __OrderInvoiceDeleteXls.xlsWriteReturnStyle,
				xls_filename: xls_file_path_name,
				user_filename: xls_user_filename,
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 송장일괄삭제 - 업로드 된 엑셀 파일 적용
	 * @constructor
	 */
	var OrderInvoiceDeleteXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "order_invoice_delete_xls_proc.php";
		var dataObj = new Object();
		dataObj.mode = __OrderInvoiceDeleteXls.xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = __OrderInvoiceDeleteXls.xlsUploadedFileName;
		dataObj.user_filename = __OrderInvoiceDeleteXls.xlsUserFileName;
		dataObj.xls_validrow = __OrderInvoiceDeleteXls.xlsValidRow;
		dataObj.is_include_shipped = ($("input[name='include_shipped']").is(":checked")) ? "Y" : "N";

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj,
			traditional: true
		}).done(function (response) {
			if(response.result)
			{
				//alert(response.msg+"건이 송장이 삭제 되었습니다.");
				alert("송장정보가 삭제 되었습니다.");
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

	/**
	 * 송장일괄삭제(조회) 페이지 초기화
	 * @constructor
	 */
	var OrderInvoiceDeleteViewInit = function(){
		//시간 inputMask
		$(".time_start, .time_end").inputmask("datetime", {
				placeholder: 'hh:mm:ss',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);

		//송장삭제 버튼 바인딩
		$(".btn-order-invoice-delete-all").on("click", function(){
			OrderInvoiceDeleteViewExecute();
		});

		OrderInvoiceDeleteViewGridInit();
	};

	/**
	 * 송장일괄삭제(조회) 관련 내용 저장 변수
	 * @type {Object}
	 */
	var OrderInvoiceDeleteSearchParam = new Object();
	OrderInvoiceDeleteSearchParam.date = "";
	OrderInvoiceDeleteSearchParam.time_start = "";
	OrderInvoiceDeleteSearchParam.time_end = "";
	OrderInvoiceDeleteSearchParam.is_include_shipped = "N";

	/**
	 * 송장일괄삭제(조회) 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderInvoiceDeleteViewGridInit = function(){
		//주문일괄삭제 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './order_invoice_delete_grid.php',
			mtype: "GET",
			postData:{
				param: $("#searchForm").serialize()
			},
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
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 100, sortable: false},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 100, sortable: false},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 80, sortable: false},
				{ label: '발주일시', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 120, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '송장입력일', name: 'invoice_date', index: 'invoice_date', width: 120, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 120, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '송장번호', name: 'invoice_no', index: 'invoice_no', width: 100, sortable: false}
			],
			rowNum: 100,
			pager: '#grid_pager',
			sortname: 'A.order_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//주문삭제
				$(".btn-order-format-seller").on("click", function(){
					var seller_idx = $(this).data("seller_idx");
					OrderBatchDeleteExecute(seller_idx);
				});

				OrderInvoiceDeleteSearchParam.date = $("#searchForm input[name='date']").val();
				OrderInvoiceDeleteSearchParam.time_start = $("#searchForm input[name='time_start']").val();
				OrderInvoiceDeleteSearchParam.time_end = $("#searchForm input[name='time_end']").val();
				OrderInvoiceDeleteSearchParam.is_include_shipped = $("#searchForm input[name='include_shipped']").is(":checked") ? "Y" : "N";
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
				OrderInvoiceDeleteViewSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderInvoiceDeleteViewSearch();
		});
	};

	/**
	 * 송장일괄삭제(조회) 목록/검색
	 * @constructor
	 */
	var OrderInvoiceDeleteViewSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 송장일괄삭제(조회) - 송장삭제 실행!!
	 * @param seller_idx
	 * @constructor
	 */
	var OrderInvoiceDeleteViewExecute = function(seller_idx){

		var records_count = $("#grid_list").getGridParam("records");
		if(records_count < 1){
			alert("삭제할 내역이 없습니다.");
			return;
		}

		if(!confirm(records_count+'건의 송장정보를 삭제하시겠습니까?')) {
			return;
		}
		var p_url = "/order/order_invoice_delete_proc.php";
		var dataObj = new Object();
		dataObj.mode = "order_invoice_delete_all";
		dataObj.invoice_date = OrderInvoiceDeleteSearchParam.date;
		dataObj.invoice_time_start = OrderInvoiceDeleteSearchParam.time_start;
		dataObj.invoice_time_end = OrderInvoiceDeleteSearchParam.time_end;
		dataObj.is_include_shipped = OrderInvoiceDeleteSearchParam.is_include_shipped;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert(response.data+"건의 송장정보가 삭제되었습니다.");
				OrderInvoiceDeleteViewSearch();
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
	 * 배송일괄취소(파일) 엑셀 업로드 관련 내용 저장 변수
	 * @type {Object}
	 * @private
	 */
	var __OrderShippedCancelXls = new Object();
	__OrderShippedCancelXls.xlsValueRow = 0;                 //업로드된 엑셀 Row 중 정상인 Row 수
	__OrderShippedCancelXls.xlsUploadedFileName = "";        //업로드 된 엑셀 파일명
	__OrderShippedCancelXls.xlsWritePageMode = "";           //일괄등록 / 일괄수정 Flag
	__OrderShippedCancelXls.xlsWriteReturnStyle = "";        //리스트 반환 또는 적용
	__OrderShippedCancelXls.xlsUserFileName = "";            //사용자가 업로드한 파일명

	/**
	 * 배송일괄취소(파일) 페이지 초기화
	 * @constructor
	 */
	var OrderShippedCancelXlsInit = function(){
		__OrderShippedCancelXls.xlsWritePageMode = $("#xlswrite_mode").val();
		__OrderShippedCancelXls.xlsWriteReturnStyle = $("#xlswrite_act").val();

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
			if(__OrderShippedCancelXls.xlsValidRow < 1)
			{
				alert("취소할 데이터가 없습니다.");
				return;
			}else{
				//var msg = __OrderShippedCancelXls.xlsValidRow + "건의 송장번호를 삭제 하시겠습니까?";
				var msg = "취소된 배송정보는 복구되지 않습니다.\n배송이 취소된 주문은 송장상태로 변경됩니다.\n배송정보를 일괄 취소 하시겠습니까?";
				if(confirm(msg)) {
					OrderShippedCancelXlsInsert();
				}
			}
		});

		OrderShippedCancelXlsGridInit();
	};

	/**
	 * 배송일괄취소(파일) 목록 jqGrid 초기화
	 * @constructor
	 */
	var OrderShippedCancelXlsGridInit = function(){
		var validErr = [];

		$("#grid_list").jqGrid({
			url: './order_shipped_cancel_xls_proc.php',
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
				{ label: '송장번호', name: 'A', index: 'invoice_no', width: 80, sortable: false},
				{ label: '비고', name: 'valid', index: 'valid', width: 150, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;
						if(cellvalue)
						{
							rst = "정상";
							__OrderShippedCancelXls.xlsValidRow++;
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
				__OrderShippedCancelXls.xlsValidRow = 0;
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	/**
	 * 배송일괄취소(파일) - 업로드 된 엑셀 파일 로딩
	 * @param xls_file_path_name
	 * @constructor
	 */
	var OrderShippedCancelXlsRead = function(xls_file_path_name, xls_user_filename){
		//console.log(xls_file_path_name);
		__OrderShippedCancelXls.xlsUploadedFileName = xls_file_path_name;
		__OrderShippedCancelXls.xlsUserFileName = xls_user_filename;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		__OrderShippedCancelXls.xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				mode: __OrderShippedCancelXls.xlsWritePageMode,
				act: __OrderShippedCancelXls.xlsWriteReturnStyle,
				xls_filename: xls_file_path_name,
				user_filename: xls_user_filename,
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 배송일괄취소(파일) - 업로드 된 엑셀 파일 적용
	 * @constructor
	 */
	var OrderShippedCancelXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "order_shipped_cancel_xls_proc.php";
		var dataObj = new Object();
		dataObj.mode = __OrderShippedCancelXls.xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = __OrderShippedCancelXls.xlsUploadedFileName;
		dataObj.user_filename = __OrderShippedCancelXls.xlsUserFileName;
		dataObj.xls_validrow = __OrderShippedCancelXls.xlsValidRow;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj,
			traditional: true
		}).done(function (response) {
			if(response.result)
			{
				//alert(response.msg+"건이 송장이 삭제 되었습니다.");
				alert("배송정보가 일괄 취소 되었습니다.");
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


	/**
	 * 배송일괄취소(조회) 페이지 초기화
	 * @constructor
	 */
	var OrderShippedCancelViewInit = function(){
		//시간 inputMask
		$(".time_start, .time_end").inputmask("datetime", {
				placeholder: 'hh:mm:ss',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);

		//배송취소 버튼 바인딩
		$(".btn-order-shipped-cancel-all").on("click", function(){
			OrderShippedCancelViewExecute();
		});

		OrderShippedCancelViewGridInit();
	};

	/**
	 * 배송일괄취소(조회) 관련 내용 저장 변수
	 * @type {Object}
	 */
	var OrderShippedCancelSearchParam = new Object();
	OrderShippedCancelSearchParam.date = "";
	OrderShippedCancelSearchParam.time_start = "";
	OrderShippedCancelSearchParam.time_end = "";

	/**
	 * 배송일괄취소(조회) 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderShippedCancelViewGridInit = function(){
		//주문일괄삭제 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './order_shipped_cancel_grid.php',
			mtype: "GET",
			postData:{
				param: $("#searchForm").serialize()
			},
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
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 100, sortable: false},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 100, sortable: false},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 80, sortable: false},
				{ label: '발주일시', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 120, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '송장입력일', name: 'invoice_date', index: 'invoice_date', width: 120, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 120, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTime(cellvalue);
					}},
				{ label: '송장번호', name: 'invoice_no', index: 'invoice_no', width: 100, sortable: false}
			],
			rowNum: 100,
			pager: '#grid_pager',
			sortname: 'A.order_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//주문삭제
				$(".btn-order-format-seller").on("click", function(){
					var seller_idx = $(this).data("seller_idx");
					OrderBatchDeleteExecute(seller_idx);
				});

				OrderShippedCancelSearchParam.date = $("#searchForm input[name='date']").val();
				OrderShippedCancelSearchParam.time_start = $("#searchForm input[name='time_start']").val();
				OrderShippedCancelSearchParam.time_end = $("#searchForm input[name='time_end']").val();
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
				OrderShippedCancelViewSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderShippedCancelViewSearch();
		});
	};

	/**
	 * 배송일괄취소(조회) 목록/검색
	 * @constructor
	 */
	var OrderShippedCancelViewSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 배송일괄취소(조회) - 배송취소 실행!!
	 * @param seller_idx
	 * @constructor
	 */
	var OrderShippedCancelViewExecute = function(seller_idx){

		var records_count = $("#grid_list").getGridParam("records");
		if(records_count < 1){
			alert("배송취소 할 내역이 없습니다.");
			return;
		}

		if(!confirm(records_count+'건의 주문을 배송취소 처리하시겠습니까?')) {
			return;
		}
		var p_url = "/order/order_shipped_cancel_proc.php";
		var dataObj = new Object();
		dataObj.mode = "order_shipped_cancel_all";
		dataObj.shipping_date = OrderShippedCancelSearchParam.date;
		dataObj.shipping_time_start = OrderShippedCancelSearchParam.time_start;
		dataObj.shipping_time_end = OrderShippedCancelSearchParam.time_end;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert(response.data+"건이 배송취소되었습니다.");
				OrderInvoiceDeleteViewSearch();
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
	 * 배송일괄처라(파일) 엑셀 업로드 관련 내용 저장 변수
	 * @type {Object}
	 * @private
	 */
	var __OrderShippedUpload = new Object();
	__OrderShippedUpload.xlsValueRow = 0;                 //업로드된 엑셀 Row 중 정상인 Row 수
	__OrderShippedUpload.xlsUploadedFileName = "";        //업로드 된 엑셀 파일명
	__OrderShippedUpload.xlsWritePageMode = "";           //일괄등록 / 일괄수정 Flag
	__OrderShippedUpload.xlsWriteReturnStyle = "";        //리스트 반환 또는 적용
	__OrderShippedUpload.xlsUserFileName = "";            //사용자가 업로드한 파일명

	/**
	 * 배송일괄처라(파일) 페이지 초기화
	 * @constructor
	 */
	var OrderShippedUploadInit = function(){
		__OrderShippedUpload.xlsWritePageMode = $("#xlswrite_mode").val();
		__OrderShippedUpload.xlsWriteReturnStyle = $("#xlswrite_act").val();

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
			if(__OrderShippedUpload.xlsValidRow < 1)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = __OrderShippedUpload.xlsValidRow + "건의 주문을 배송처리 하시겠습니까?";
				if(confirm(msg)) {
					OrderShippedUploadXlsInsert();
				}
			}
		});

		OrderShippedUploadGridInit();
	};

	/**
	 * 배송일괄처라(파일) 목록 jqGrid 초기화
	 * @constructor
	 */
	var OrderShippedUploadGridInit = function(){
		var validErr = [];

		$("#grid_list").jqGrid({
			url: './order_shipped_upload_xls_proc.php',
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
				{ label: '송장번호', name: 'A', index: 'invoice_no', width: 150, sortable: false},
				// { label: '관리번호', name: 'order_idx', index: 'order_idx', width: 150, sortable: false},
				{ label: '배송처리', name: 'shipped_status', index: 'shipped_status', width: 150, sortable: false, formatter: function(cellvalue, options, rowobject){
					return (rowobject.valid) ? "가능" : "불가";
					}},
				{ label: '송장등록', name: 'invoice_status', index: 'invoice_status', width: 150, sortable: false},
				{ label: '상태', name: 'order_progress_step_han', index: 'order_progress_step_han', width: 120, sortable: false},
				{ label: 'C/S', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '보류', name: 'order_is_hold', index: 'order_is_hold', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){

						return (cellvalue == "Y") ? "보류" : "";

					}},
				{ label: '비고', name: 'valid', index: 'valid', width: 150, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;
						if(cellvalue)
						{
							rst = "정상";
							__OrderShippedUpload.xlsValidRow++;
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
				__OrderInvoice.xlsValidRow = 0;
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	/**
	 * 배송일괄처라(파일) - 업로드 된 엑셀 파일 로딩
	 * @param xls_file_path_name
	 * @constructor
	 */
	var OrderShippedUploadXlsRead = function(xls_file_path_name, xls_user_filename){
		//console.log(xls_file_path_name);
		__OrderShippedUpload.xlsUploadedFileName = xls_file_path_name;
		__OrderShippedUpload.xlsUserFileName = xls_user_filename;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		__OrderShippedUpload.xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				mode: __OrderShippedUpload.xlsWritePageMode,
				act: __OrderShippedUpload.xlsWriteReturnStyle,
				xls_filename: xls_file_path_name,
				user_filename: xls_user_filename,
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 배송일괄처라(파일) - 업로드 된 엑셀 파일 적용
	 * @constructor
	 */
	var OrderShippedUploadXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "order_shipped_upload_xls_proc.php";
		var dataObj = new Object();
		dataObj.mode = __OrderShippedUpload.xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = __OrderShippedUpload.xlsUploadedFileName;
		dataObj.user_filename = __OrderShippedUpload.xlsUserFileName;
		dataObj.xls_validrow = __OrderShippedUpload.xlsValidRow;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj,
			traditional: true
		}).done(function (response) {
			if(response.result)
			{
				alert(response.msg+"건의 주문이 배송처리 되었습니다.");
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

	return {
		OrderDownloadInit : OrderDownloadInit,
		OrderDownloadFormatPopInit: OrderDownloadFormatPopInit,
		OrderDownloadEmailSendPopInit: OrderDownloadEmailSendPopInit,
		OrderDownloadLogFileInit: OrderDownloadLogFileInit,
		OrderDownloadLogEmailInit: OrderDownloadLogEmailInit,
		OrderDownloadLogDownInit: OrderDownloadLogDownInit,
		OrderInvoiceUploadInit: OrderInvoiceUploadInit,
		OrderInvoiceUploadXlsRead: OrderInvoiceUploadXlsRead,
		OrderInvoiceUploadLogPopInit: OrderInvoiceUploadLogPopInit,
		OrderInvoiceDeleteXlsInit: OrderInvoiceDeleteXlsInit,
		OrderInvoiceDeleteXlsRead: OrderInvoiceDeleteXlsRead,
		OrderInvoiceDeleteViewInit: OrderInvoiceDeleteViewInit,
		OrderShippedCancelXlsInit: OrderShippedCancelXlsInit,
		OrderShippedCancelXlsRead: OrderShippedCancelXlsRead,
		OrderShippedCancelViewInit: OrderShippedCancelViewInit,
		OrderShippedUploadInit: OrderShippedUploadInit,
		OrderShippedUploadXlsRead: OrderShippedUploadXlsRead,
	}
})();