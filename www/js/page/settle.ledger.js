/*
 * 정산통계 - 원장 관련 js
 */
var SettleLedge = (function() {
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

		//창 닫기 버튼 바인딩
		$("body").on("click", ".btn-common-pop-close", function(){
			PopupCommonPopClose();
		});
	};

	/**
	 * 매입거래처별원장 페이지 초기화
	 * @constructor
	 */
	var PurchaseLedgeInit = function(){
		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
		$(".supplier_idx").SumoSelect({
			placeholder: '공급처를 선택해주세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '공급처 검색',
			noMatch : '검색결과가 없습니다.'
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
				PurchaseLedgeSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			PurchaseLedgeSearch();
		});

		//매입추가등록
		$(".btn-ledger-detail-add").on("click", function(){
			if(_LedgeTargetIdx == "" || _LedgeTargetIdx == 0){
				alert("검색 후 등록 하실 수 있습니다.");
				return;
			}
			var ledger_add_type = $(this).data("type");

			if(ledger_add_type == "TRAN") {
				Common.newWinPopup("report_pop.php?mode=add&tran_date=&tran_type=BANK_CUSTOMER_OUT&tran_inout=OUT&target_idx=" + _LedgeTargetIdx, 'report_pop', 960, 600, 'yes');
			}else {
				Common.newWinPopup("ledger_purchase_pop.php?supplier_idx=" + _LedgeTargetIdx + "&ledger_add_type=" + ledger_add_type, 'purchase_ledger_pop', 960, 550, 'yes');
			}
		});

		//이메일발송이력 버튼
		$(".btn-email-log-pop").on("click", function(){
			Common.newWinPopup("ledger_log_email_pop.php?ledger_type=LEDGER_PURCHASE", 'ledger_log_email_pop', 1200, 720, 'yes');
		});
		//다운로드이력 버튼
		$(".btn-down-log-pop").on("click", function(){
			Common.newWinPopup("ledger_log_down_pop.php?ledger_type=LEDGER_PURCHASE", 'ledger_log_down_pop', 1200, 720, 'yes');
		});
		//파일생성이력 버튼
		$(".btn-file-log-pop").on("click", function(){
			Common.newWinPopup("ledger_log_file_pop.php?ledger_type=LEDGER_PURCHASE", 'ledger_log_file_pop', 1400, 720, 'yes');
		});


		//테이블 상세내역 제외 바인딩
		$("body").on("change", ".chk-ledge-minimize", function(){

			var dt = $(this).data("date");

			if($(this).is(":checked")) {
				$("table[data-date='" + dt + "']").addClass("shrink");
			}else{
				$("table[data-date='" + dt + "']").removeClass("shrink");
			}
		});

		//테이블 메모 버튼 바인딩
		$("body").on("click", ".btn-ledger-memo-modify", function(){
			var p_url = "ledger_memo_pop.php";
			var dataObj = new Object();
			dataObj.ledger_memo_idx = $(this).parent().parent().data("memo_idx");
			dataObj.target_idx = $(this).data("target_idx");
			dataObj.ledger_type = $(this).data("type");
			dataObj.ledger_date = $(this).data("date");
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					PopupCommonPopOpen(600, 0, "메모", response);
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

		//테이블 파일생성 버튼 바인딩
		$("body").on("click", ".btn-ledger-create-xls", function(){
			var p_url = "ledger_create_xls.php";
			var dataObj = new Object();
			dataObj.target_idx = $(this).data("target_idx");
			dataObj.ledger_type = $(this).data("type");
			dataObj.date_start = $(this).data("month_start");
			dataObj.date_end = $(this).data("month_end");
			if($(this).hasClass("btn-ledger-all-down")) {
				dataObj.is_shrink = ($("#chk-ledger-all-down-shrink").is(":checked")) ? "Y" : "N";
			}else{
				dataObj.is_shrink = ($(".chk-ledge-minimize[data-date='" + $(this).data("month") + "']").is(":checked")) ? "Y" : "N";
			}

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if(response) {
					if(response.result) {
						LedgeDocumentDownload(response.target_idx, response.file_idx)
					}
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});

		//테이블 메모2 버튼 바인딩
		$("body").on("click", ".btn-ledger-memo-modify2", function(){
			var p_url = "ledger_memo_pop.php";
			var dataObj = new Object();
			dataObj.ledger_idx = $(this).data("idx");
			dataObj.ledger_type = "PURCHASE";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					PopupCommonPopOpen(600, 0, "메모", response);
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

		//테이블 삭제 버튼 바인딩
		$("body").on("click", ".btn-ledger-memo-delete", function(){

			if(!confirm("정말 삭제하시겠습니까?")){
				return;
			}

			var p_url = "ledger_proc_ajax.php";
			var dataObj = new Object();
			dataObj.mode = "ledger_delete";
			dataObj.ledger_idx = $(this).data("idx");
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					PurchaseLedgeSearch();
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * 원장 파일 다운로드 함수
	 * @constructor
	 */
	var LedgeDocumentDownload = function(target_idx, file_idx){

		var url = "/proc/_ledger_download_xls.php?target_idx="+target_idx+"&file_idx="+file_idx;
		xls_hidden_frame.location.replace(url);
	};

	var _LedgeTargetIdx = 0;
	var _LedgeMonthStart = "";
	var _LedgeMonthEnd = "";

	/**
	 * 매입거래처별원장 데이터 로딩
	 * @constructor
	 */
	var PurchaseLedgeSearch = function(){

		var start_date = $("#date_start_year").val() + "-" + Common.LeftPad($("#date_start_month").val(), 2);
		var end_date = $("#date_end_year").val() + "-" + Common.LeftPad($("#date_end_month").val(), 2);

		var diff = moment(end_date, 'YYYY-M').diff(start_date, 'month');
		if(diff < 0){
			alert("날짜 설정이 정확하지 않습니다.");
			return;
		}

		if(diff > 12){
			alert("기간은 12개월 이내로만 설정 가능합니다.");
			return;
		}

		if($("#supplier_idx").val() == null || $("#supplier_idx").val() == ""){
			alert("공급처를 선택해주세요.");
			return;
		}

		_LedgeTargetIdx = $("#supplier_idx").val();
		_LedgeMonthStart = start_date;
		_LedgeMonthEnd = end_date;

		$(".btn-ledger-all-down").data("month_start", _LedgeMonthStart);
		$(".btn-ledger-all-down").data("month_end", _LedgeMonthEnd);
		$(".btn-ledger-all-down").data("target_idx", _LedgeTargetIdx);

		showLoader();
		var p_url = "ledger_purchase_list.php";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: $("form[name='searchForm']").serialize()
		}).done(function (response) {
			if(response)
			{
				$(".top_btn_set").removeClass("dis_none");

				$(".wrap_ledge").empty();
				$(".wrap_ledge").append(response);
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
	 * 매입거래처별 원장 매입추가 팝업 페이지 초기화
	 * @constructor
	 */
	var PurchaseLedgePopInit = function(){

		$(".inp_ledger_amount").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 2, autoGroup: true, rightAlign: false});


		$("#btn-save-pop").on("click", function(){
			if(!confirm('저장 하시겠습니까?')){
				return;
			}

			$("#dyFormLedgePop").submit();
		});

	};

	/**
	 * 거래처별원장 메모 팝업 페이지 초기화
	 * @constructor
	 */
	var LedgerMemoPopInit = function(ledger_type){
		$("#btn-save-pop").on("click", function(){
			if(!confirm('저장 하시겠습니까?')){
				return;
			}

			var p_url = "ledger_proc_ajax.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("#dyFormLedgerPop").serialize()
			}).done(function (response) {
				if(response.result)
				{
					if(response.data != "") {
						LedgerMemoReload(response.data, response.date);
					}else{
						if(ledger_type == "LEDGER_SALE"){
							SaleLedgeSearch();
						}else if(ledger_type == "LEDGER_PURCHASE"){
							PurchaseLedgeSearch();
						}
						//LedgerMemoReload2(response.ledger_idx);
					}
					PopupCommonPopClose();

				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});
	};

	/**
	 * 거래처별원장 메모 새로고침
	 * @param ledger_memo_idx
	 * @constructor
	 */
	var LedgerMemoReload = function(ledger_memo_idx, date){
		var p_url = "ledger_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_ledger_memo";
		dataObj.ledger_memo_idx = ledger_memo_idx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				$(".ledger_memo_wrap[data-date='"+date+"']").html(response.data);
				$(".ledger_memo_wrap[data-date='"+date+"']").parent().data("memo_idx", ledger_memo_idx);
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
	 * 거래처별원장 메모 새로고침
	 * @param ledger_idx
	 * @constructor
	 */
	var LedgerMemoReload2 = function(ledger_idx){
		var p_url = "ledger_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_ledger_memo2";
		dataObj.ledger_idx = ledger_idx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				$("tr[data-ledger_idx='"+ledger_idx+"'] span.ledger_memo").html(response.data);
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
	 * 파일생성 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var LedgerLogFileInit = function(ledger_type){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

		if(ledger_type == "LEDGER_PURCHASE") {
			//공급처 그룹 및 공급처 선택창 초기화
			CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
			$(".supplier_idx").SumoSelect({
				placeholder: '전체 공급처',
				captionFormat: '{0}개 선택됨',
				captionFormatAllSelected: '{0}개 모두 선택됨',
				search: true,
				searchText: '공급처 검색',
				noMatch: '검색결과가 없습니다.'
			});
		}else{
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
		}

		$("#grid_list").jqGrid({
			url: './ledger_log_file_pop_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize(),
				ledger_type: ledger_type
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
				{ label: '파일생성이력IDX', name: 'file_idx', index: 'file_idx', width: 80, sortable: false, hidden: true},
				{ label: '거래처IDX', name: 'target_idx', index: 'target_idx', width: 80, sortable: false, hidden: true},
				{ label: '거래처', name: 'target_name', index: 'target_name', width: 80, sortable: false},
				{ label: '거래처주소', name: 'target_addr', index: 'target_addr', width: 100, sortable: false},
				{ label: '담당자', name: 'officer_name', index: 'officer_name', width: 100, sortable: false},
				{ label: '회계용 이메일', name: 'email', index: 'email', width: 100, sortable: false},
				{ label: '파일 기간', name: 'ledger_period', index: 'ledger_period', width: 100, sortable: false},
				{ label: '상세내역', name: 'ledger_is_shrink', index: 'ledger_is_shrink', width: 70, sortable: false, formatter: function(cellvalue, options, rowobject){
						return (cellvalue == "Y") ? "제외" : "포함";
					}},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 120, sortable: false},
				{ label: '생성시간', name: 'file_regdate', index: 'file_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '-', name: 'btn_action', index: 'btn_action', width: 160, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-ledger-file-down" data-target_idx="' + rowobject.target_idx + '" data-file_idx="' + rowobject.file_idx + '">다운받기</a>'
						if(rowobject.email == "" || rowobject.email == null){

						}else {
							btnz += ' <a href="javascript:;" class="xsmall_btn red_btn btn-ledger-send-email" data-target_idx="' + rowobject.target_idx + '" data-file_idx="' + rowobject.file_idx + '">이메일발송</a>';
						}
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.file_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: false,
			shrinkToFit: true,
			multiselect: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){

				//이메일발송
				$(".btn-ledger-send-email").on("click", function(){
					var target_idx_ary = new Array();
					target_idx_ary.push($(this).data("target_idx"));

					var file_idx_ary = new Array();
					file_idx_ary.push($(this).data("file_idx"));

					LedgerEmailSendPopOpen(target_idx_ary, file_idx_ary, ledger_type);
				});
				//다운받기
				$(".btn-ledger-file-down").on("click", function(){
					LedgeDocumentDownload($(this).data("target_idx"), $(this).data("file_idx"));
				});

				//브라우저 리사이즈 trigger
				$(window).trigger("resize");

				// we make all even rows "protected", so that will be not selectable
				//var cbs = $("tr.jqgrow > td > input.cbox:even", $("#grid_list"));
				//cbs.attr("disabled", "disabled");

				var allRowId = $("#grid_list").getDataIDs();

				$.each(allRowId, function(i, o){
					console.log(o);
					var rowData =$("#grid_list").getRowData(o);
					if(rowData.email == "" || rowData.email == null){
						$("#grid_list #"+o+" > td > input.cbox").attr("disabled", "disabled");
					}
				});

			},
			beforeSelectRow: function(rowid, e) {
				var cbsdis = $("tr#"+rowid+".jqgrow > td > input.cbox:disabled", $("#grid_list"));
				if (cbsdis.length === 0) {
					return true;    // allow select the row
				} else {
					return false;   // not allow select the row
				}
			},
			onSelectAll: function(aRowids,status) {
				if (status) {
					// uncheck "protected" rows
					var cbs = $("tr.jqgrow > td > input.cbox:disabled", $("#grid_list"));
					cbs.removeAttr("checked");

					//modify the selarrrow parameter
					$("#grid_list")[0].p.selarrrow = $("#grid_list").find("tr.jqgrow:has(td > input.cbox:checked)")
						.map(function() { return this.id; }) // convert to set of ids
						.get(); // convert to instance of Array
				}
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
				LedgerLogFileListSearch(ledger_type);
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			LedgerLogFileListSearch(ledger_type);
		});

		//선택 이메일발송
		$("#btn-send-email-selected").on("click", function(){
			var selRowId = $("#grid_list").getGridParam("selarrrow");
			console.log(selRowId);

			if(selRowId == null || selRowId.length == 0){
				alert('발송할 거래처를 선택해주세요.');
				return;
			}

			var target_idx_ary = new Array();
			var file_idx_ary = new Array();
			$.each(selRowId, function(i, o){
				var rowData =$("#grid_list").getRowData(o);
				target_idx_ary.push(rowData.target_idx);
				file_idx_ary.push(rowData.file_idx);
			});

			LedgerEmailSendPopOpen(target_idx_ary, file_idx_ary, ledger_type);
		});
	};

	/**
	 * 파일생성 목록/검색
	 * @constructor
	 */
	var LedgerLogFileListSearch = function(ledger_type){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize(),
				ledger_type: ledger_type
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 이메일 발송 모달 팝업 Open
	 * @param $obj
	 * @constructor
	 */
	var LedgerEmailSendPopOpen = function(target_idx_ary, file_idx_ary, ledger_type){
		var p_url = "ledger_email_pop.php";
		var dataObj = new Object();
		dataObj.ledger_type = ledger_type;
		dataObj.target_idx_list = target_idx_ary;
		dataObj.file_idx_list = file_idx_ary;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				PopupCommonPopOpen(800, "", "이메일 발송", response);
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
	 * 이메일 발송 팝업 페이지 초기화
	 * @param stock_order_idx : "발주서" IDX
	 * @constructor
	 */
	var LedgerEmailSendPopInit = function(){

		//첨부파일 다운받기 버튼 바인딩
		$(".btn-xls-down").on("click", function(){
			//xls_hidden_frame.location.replace($("#stock_order_document_short_url").val());
			xls_hidden_frame.location.replace('/proc/_ledger_download_xls.php?file_idx='+$(this).data("idx"));
		});

		//폼 초기화
		LedgerEmailSendFormInit();
	};

	/**
	 * 이메일 발송 폼 진행 여부
	 * @type {boolean}
	 * @private
	 */
	var _LedgerEmailSendIng = false;

	/**
	 * 이메일 발송 폼 초기화
	 * @constructor
	 */
	var LedgerEmailSendFormInit = function(){
		//저장 버튼
		$("#btn-send-email").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if(!_LedgerEmailSendIng) {
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
				if($("input[name='target_email']").length > 0) {
					if (!valForm.chkValue(objForm.target_email, "수신이메일을 선택해주세요.", 1, 100, null)) return returnType;
				}
				if (!valForm.chkValue(objForm.email_title, "메일제목을 정확히 입력해주세요..", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.email_content, "메일내용을 정확히 입력해주세요.", 1, 8000, null)) return returnType;

				_LedgerEmailSendIng = true;
				showLoader();
				var p_url = "ledger_proc_ajax.php";
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: $("form[name='dyFormEmail']").serialize()
				}).done(function (response) {
					if(response.result)
					{
						alert(response.msg);
						PopupCommonPopClose();

					}else{
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
					_LedgerEmailSendIng = false;
				}).fail(function(jqXHR, textStatus){
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
					_StockOrderEmailSendIng = false;
				});
				return false;

			}catch(e){
				alert(e);
				_LedgerEmailSendIng = false;
				return false;
			}
		});
	};

	/**
	 * 이메일 발송 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var LedgerLogEmailInit = function(ledger_type){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

		if(ledger_type == "LEDGER_PURCHASE") {
			//공급처 그룹 및 공급처 선택창 초기화
			CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
			$(".supplier_idx").SumoSelect({
				placeholder: '전체 공급처',
				captionFormat: '{0}개 선택됨',
				captionFormatAllSelected: '{0}개 모두 선택됨',
				search: true,
				searchText: '공급처 검색',
				noMatch: '검색결과가 없습니다.'
			});
		}else{
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
		}

		$("#grid_list").jqGrid({
			url: './ledger_log_email_pop_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize(),
				ledger_type: ledger_type
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
				{ label: '파일생성로그IDX', name: 'file_idx', index: 'file_idx', width: 0, sortable: false, hidden: true},
				{ label: 'target_idx', name: 'target_idx', index: 'target_idx', width: 0, sortable: false, hidden: true},
				{ label: '발송시간', name: 'email_regdate', index: 'email_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '거래처', name: 'target_name', index: 'target_name', width: 120, sortable: false},
				{ label: '수신자', name: 'email_receiver', index: 'email_receiver', width: 100, sortable: false},
				{ label: '제목', name: 'email_title', index: 'order_download_email_title', width: 120, sortable: false},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 80, sortable: false},
				{ label: '첨부파일', name: 'btn_action', index: 'btn_action', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-file-down" data-file_idx="'+rowobject.file_idx+'" data-target_idx="'+rowobject.target_idx+'">다운받기</a>'
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.email_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//다운받기
				$(".btn-file-down").on("click", function(){
					LedgeDocumentDownload($(this).data("target_idx"), $(this).data("file_idx"));
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
				LedgerLogEmailListSearch(ledger_type);
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			LedgerLogEmailListSearch(ledger_type);
		});
	};

	/**
	 * 이메일 발송 이력 목록/검색
	 * @constructor
	 */
	var LedgerLogEmailListSearch = function(ledger_type){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize(),
				ledger_type: ledger_type
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 다운로드 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var LedgerLogDownInit = function(ledger_type){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

		if(ledger_type == "LEDGER_PURCHASE") {
			//공급처 그룹 및 공급처 선택창 초기화
			CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
			$(".supplier_idx").SumoSelect({
				placeholder: '전체 공급처',
				captionFormat: '{0}개 선택됨',
				captionFormatAllSelected: '{0}개 모두 선택됨',
				search: true,
				searchText: '공급처 검색',
				noMatch: '검색결과가 없습니다.'
			});
		}else{
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
		}

		$("#grid_list").jqGrid({
			url: './ledger_log_down_pop_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize(),
				ledger_type: ledger_type
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
				{ label: '파일생성이력IDX', name: 'file_idx', index: 'file_idx', width: 0, sortable: false, hidden: true},
				{ label: '다운로드시간', name: 'file_down_regdate', index: 'file_down_regdate', width: 120, sortable: false, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '거래처', name: 'target_name', index: 'target_name', width: 120, sortable: false},
				{ label: '수신자', name: 'email_receiver', index: 'email_receiver', width: 100, sortable: false},
				{ label: '제목', name: 'email_title', index: 'email_title', width: 120, sortable: false},
				{ label: '첨부파일', name: 'btn_action', index: 'btn_action', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-file-down" data-file_idx="'+rowobject.file_idx+'" data-target_idx="'+rowobject.target_idx+'">다운받기</a>'
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'A.file_down_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//다운받기
				$(".btn-file-down").on("click", function(){
					LedgeDocumentDownload($(this).data("target_idx"), $(this).data("file_idx"));
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
				LedgerLogDownListSearch(ledger_type);
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			LedgerLogDownListSearch(ledger_type);
		});
	};

	/**
	 * 다운로드 이력 목록/검색
	 * @constructor
	 */
	var LedgerLogDownListSearch = function(ledger_type){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize(),
				ledger_type: ledger_type
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 매출거래처별원장 페이지 초기화
	 * @constructor
	 */
	var SaleLedgeInit = function(){
		//판매처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '판매처를 선택해주세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
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
				SaleLedgeSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			SaleLedgeSearch();
		});

		setTimeout(function(){
			if($("#seller_idx").data("selected") != "" && $("#seller_idx").data("selected") != null){
				$("#seller_idx").data("selected-one", $("#seller_idx").data("selected"));
				SaleLedgeSearch();
			}
		}, 800);

		//매출추가등록
		$(".btn-ledger-detail-add").on("click", function(){
			if(_LedgeTargetIdx == "" || _LedgeTargetIdx == 0){
				alert("검색 후 등록 하실 수 있습니다.");
				return;
			}
			var ledger_add_type = $(this).data("type");

			if(ledger_add_type == "TRAN") {
				Common.newWinPopup("report_pop.php?mode=add&tran_date=&tran_type=BANK_CUSTOMER_IN&tran_inout=IN&target_idx=" + _LedgeTargetIdx, 'report_pop', 960, 600, 'yes');
			}else{
				Common.newWinPopup("ledger_sale_pop.php?seller_idx=" + _LedgeTargetIdx + "&ledger_add_type=" + ledger_add_type, 'ledger_sale_pop', 960, 550, 'yes');
			}
		});

		//이메일발송이력 버튼
		$(".btn-email-log-pop").on("click", function(){
			Common.newWinPopup("ledger_log_email_pop.php?ledger_type=LEDGER_SALE", 'ledger_log_email_pop', 1200, 720, 'yes');
		});
		//다운로드이력 버튼
		$(".btn-down-log-pop").on("click", function(){
			Common.newWinPopup("ledger_log_down_pop.php?ledger_type=LEDGER_SALE", 'ledger_log_down_pop', 1200, 720, 'yes');
		});
		//파일생성이력 버튼
		$(".btn-file-log-pop").on("click", function(){
			Common.newWinPopup("ledger_log_file_pop.php?ledger_type=LEDGER_SALE", 'ledger_log_file_pop', 1400, 720, 'yes');
		});


		//테이블 상세내역 제외 바인딩
		$("body").on("change", ".chk-ledge-minimize", function(){

			var dt = $(this).data("date");

			if($(this).is(":checked")) {
				$("table[data-date='" + dt + "']").addClass("shrink");
			}else{
				$("table[data-date='" + dt + "']").removeClass("shrink");
			}
		});

		//테이블 메모 버튼 바인딩
		$("body").on("click", ".btn-ledger-memo-modify", function(){
			var p_url = "ledger_memo_pop.php";
			var dataObj = new Object();
			dataObj.ledger_memo_idx = $(this).parent().parent().data("memo_idx");
			dataObj.target_idx = $(this).data("target_idx");
			dataObj.ledger_type = $(this).data("type");
			dataObj.ledger_date = $(this).data("date");
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					PopupCommonPopOpen(600, 0, "메모", response);
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

		//테이블 파일생성 버튼 바인딩
		$("body").on("click", ".btn-ledger-create-xls", function(){
			var p_url = "ledger_create_xls.php";
			var dataObj = new Object();
			dataObj.target_idx = $(this).data("target_idx");
			dataObj.ledger_type = $(this).data("type");
			dataObj.date_start = $(this).data("month_start");
			dataObj.date_end = $(this).data("month_end");
			if($(this).hasClass("btn-ledger-all-down")) {
				dataObj.is_shrink = ($("#chk-ledger-all-down-shrink").is(":checked")) ? "Y" : "N";
			}else{
				dataObj.is_shrink = ($(".chk-ledge-minimize[data-date='" + $(this).data("month") + "']").is(":checked")) ? "Y" : "N";
			}

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if(response) {
					if(response.result) {
						LedgeDocumentDownload(response.target_idx, response.file_idx)
					}
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

		//테이블 메모2 버튼 바인딩
		$("body").on("click", ".btn-ledger-memo-modify2", function(){
			var p_url = "ledger_memo_pop.php";
			var dataObj = new Object();
			dataObj.ledger_idx = $(this).data("idx");
			dataObj.ledger_type = "SALE";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					PopupCommonPopOpen(600, 0, "메모", response);
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

		//테이블 삭제 버튼 바인딩
		$("body").on("click", ".btn-ledger-memo-delete", function(){

			if(!confirm("정말 삭제하시겠습니까?")){
				return;
			}

			var p_url = "ledger_proc_ajax.php";
			var dataObj = new Object();
			dataObj.mode = "ledger_delete";
			dataObj.ledger_idx = $(this).data("idx");
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					SaleLedgeSearch();
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * 매입거래처별원장 데이터 로딩
	 * @constructor
	 */
	var SaleLedgeSearch = function(){

		var start_date = $("#date_start_year").val() + "-" + Common.LeftPad($("#date_start_month").val(), 2);
		var end_date = $("#date_end_year").val() + "-" + Common.LeftPad($("#date_end_month").val(), 2);

		var diff = moment("2019-03", 'YYYY-M').diff("2019-03", 'month');
		if(diff < 0){
			alert("날짜 설정이 정확하지 않습니다.");
			return;
		}

		if(diff > 12){
			alert("기간은 12개월 이내로만 설정 가능합니다.");
			return;
		}

		if($("#seller_idx").data("selected-one") != null && $("#seller_idx").data("selected-one") != ""){
			_LedgeTargetIdx = $("#seller_idx").val($("#seller_idx").data("selected-one"));
			$("#seller_idx").data("selected-one", "");
		}else {

			if ($("#seller_idx").val() == null || $("#seller_idx").val() == "") {
				alert("판매처를 선택해주세요.");
				return;
			}

			_LedgeTargetIdx = $("#seller_idx").val();
		}
		_LedgeMonthStart = start_date;
		_LedgeMonthEnd = end_date;

		$(".btn-ledger-all-down").data("month_start", _LedgeMonthStart);
		$(".btn-ledger-all-down").data("month_end", _LedgeMonthEnd);
		$(".btn-ledger-all-down").data("target_idx", _LedgeTargetIdx);

		showLoader();
		var p_url = "ledger_sale_list.php";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: $("form[name='searchForm']").serialize()
		}).done(function (response) {
			if(response)
			{
				$(".top_btn_set").removeClass("dis_none");

				$(".wrap_ledge").empty();
				$(".wrap_ledge").append(response);

				if(!isDYLogin) {
					$(".wrap_ledge .btn-ledger-memo-delete").remove();
					$(".wrap_ledge .btn-ledger-memo-modify").remove();
					$(".wrap_ledge .btn-ledger-memo-modify2").remove();
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
		PurchaseLedgeInit: PurchaseLedgeInit,
		PurchaseLedgeSearch: PurchaseLedgeSearch,
		PurchaseLedgePopInit: PurchaseLedgePopInit,
		LedgerMemoPopInit: function(ledger_type) { LedgerMemoPopInit(ledger_type) },
		LedgerMemoReload: function(ledger_memo_idx){
			LedgerMemoReload(ledger_memo_idx);
		},
		LedgerLogFileInit: function(ledger_type){
			LedgerLogFileInit(ledger_type);
		},
		LedgerEmailSendPopInit: LedgerEmailSendPopInit,
		LedgerLogEmailInit: function(ledger_type){
			LedgerLogEmailInit(ledger_type);
		},
		LedgerLogDownInit: function(ledger_type){
			LedgerLogDownInit(ledger_type);
		},
		SaleLedgeInit: SaleLedgeInit,
		SaleLedgeSearch: SaleLedgeSearch,
	}
})();
$(function(){
	SettleLedge.init();
});