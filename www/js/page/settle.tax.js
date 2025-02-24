/*
 * 정산통계 - 계산서 관련 js
 */
var SettleTax = (function() {
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
	 * 정산 - 매출 페이지 초기화
	 * @constructor
	 */
	var TaxSaleWriteInit = function(tax_type){

		if(tax_type == "SALE") {
			
			$target_idx = $("#seller_idx");
			
			//판매처 그룹 및 공급처 선택창 초기화
			CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
			$(".seller_idx").SumoSelect({
				placeholder: '판매처를 선택해주세요.',
				captionFormat: '{0}개 선택됨',
				captionFormatAllSelected: '{0}개 모두 선택됨',
				search: true,
				searchText: '판매처 검색',
				noMatch: '검색결과가 없습니다.'
			});
		}else{

			$target_idx = $("#supplier_idx");
			
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
		}

		$(".money").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 0, autoGroup: true, rightAlign: true});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){

			if($target_idx.val() == null || $target_idx.val() == ""){
				alert("거래처를 선택해주세요.");
				return;
			}

			$("#searchForm").submit();
		});

		//계산서 금액 변경
		$(".money").on("keyup", function(){
			var name = $(this).attr("name");
			var val = $.trim($(this).val().replace(/,/g, ""));

			if(val == "") val = 0;

			var target_val = $("span." + name).text().replace(/,/g, "");

			//console.log(target_val);

			if(val == target_val){
				$("span.chk_"+name).text("OK");
			}else{
				$("span.chk_"+name).text("X");
			}

			var sum = 0;
			$(".money").each(function(i, o){

				var val = $.trim($(this).val().replace(/,/g, ""));
				if(val == "") val = 0;
				val = Number(val);
				sum += val;
			});

			$("span.tax_input_sum").text(Common.addCommas(sum));

			var tax_sum_val = $("span.tax_sum").text().replace(/,/g, "");

			if(tax_sum_val == sum){
				$("span.chk_tax_sum").text("OK");
			}else{
				$("span.chk_tax_sum").text("X");
			}


		}).trigger("keyup");

		//저장
		$("#btn-save").on("click", function(){
			if(!confirm('저장하시겠습니까?')){
				return;
			}

			TaxSave(null);
		});

		//이메일발송이력 버튼
		$(".btn-email-log-pop").on("click", function(){
			Common.newWinPopup("tax_log_email_pop.php?tax_type="+tax_type, 'tax_log_email_pop', 1200, 720, 'yes');
		});
		//다운로드이력 버튼
		$(".btn-down-log-pop").on("click", function(){
			Common.newWinPopup("tax_log_down_pop.php?tax_type="+tax_type, 'tax_log_down_pop', 1200, 720, 'yes');
		});
		//파일생성이력 버튼
		$(".btn-file-log-pop").on("click", function(){
			Common.newWinPopup("tax_log_file_pop.php?tax_type="+tax_type, 'tax_log_file_pop', 1400, 720, 'yes');
		});

		//확인
		$(".btn-confirm").on("click", function(){
			var what = $(this).data("what");
			TaxSave(function(){
				TaxConfirm(what);
			});
		});

		//파일생성 버튼
		$(".btn-create-tax-xls").on("click", function(){
			TaxXlsCreate();
		});

		//일별 버튼
		$(".btn-daily-pop").on("click", function(){

			var url = "tax_daily_pop.php";
			url += "?mode=" + $(this).data("mode");
			url += "&tax_type=" + $("#tax_type").val();
			url += "&target_idx=" + $("#target_idx").val();
			url += "&date_ym=" + $(this).data("date");
			url += "&name=" + $(this).data("name");

			Common.newWinPopup(url, 'tax_daily_pop', 380, 600, 'yes');

		});
	};

	/**
	 * 계산서 내용 저장
	 * @param onComplete
	 * @constructor
	 */
	var TaxSave = function(onComplete){

		var p_url = "tax_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "tax_save";
		dataObj.tax_type = $("#tax_type").val();
		dataObj.date_ym = $("#date_ym").val();
		dataObj.target_idx = $("#target_idx").val();
		dataObj.taxation_amount = $("#taxation_amount").val();
		dataObj.taxation_memo = $("#taxation_memo").val();
		dataObj.free_amount = $("#free_amount").val();
		dataObj.free_memo = $("#free_memo").val();
		dataObj.small_amount = $("#small_amount").val();
		dataObj.small_memo = $("#small_memo").val();

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response) {
				if(typeof onComplete == "function"){
					onComplete();
				}
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});

	};

	/**
	 * 계산서 확인 Update
	 * @param what
	 * @constructor
	 */
	var TaxConfirm = function(what){

		var p_url = "tax_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "tax_confirm";
		dataObj.tax_type = $("#tax_type").val();
		dataObj.date_ym = $("#date_ym").val();
		dataObj.target_idx = $("#target_idx").val();
		dataObj.what = what;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response) {
				location.reload();
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 계산서 엑셀 생성
	 * @constructor
	 */
	var TaxXlsCreate = function(){
		var p_url = "tax_create_xls.php";
		var dataObj = new Object();
		dataObj.tax_type = $("#tax_type").val();
		dataObj.date_ym = $("#date_ym").val();
		dataObj.target_idx = $("#target_idx").val();

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response) {
				if(response.result) {
					TaxDocumentDownload(response.target_idx, response.file_idx)
				}
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 계산서 파일 다운로드 함수
	 * @constructor
	 */
	var TaxDocumentDownload = function(target_idx, file_idx){

		var url = "/proc/_tax_download_xls.php?target_idx="+target_idx+"&file_idx="+file_idx;
		xls_hidden_frame.location.replace(url);
	};

	/**
	 * 일별 팝업
	 * @param title
	 * @constructor
	 */
	var TaxDailyPopInit = function(title){

		var popup_title = $("body>.wrap>.container>.con_tit>p.title").text();
		popup_title = popup_title.replace("일별", "일별 (" + title + ")");
		$("body>.wrap>.container>.con_tit>p.title").text(popup_title);

		$('table.floatThead').floatThead({
			position: 'fixed',
			top: 0,
			zIndex: 900
		});
	};


	/**
	 * 파일생성 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var TaxLogFileInit = function(tax_type){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

		if(tax_type == "PURCHASE") {
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
			url: './tax_log_file_pop_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize(),
				tax_type: tax_type
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
				{ label: '파일 기간', name: 'tax_period', index: 'tax_period', width: 100, sortable: false},
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

					TaxEmailSendPopOpen(target_idx_ary, file_idx_ary, tax_type);
				});
				//다운받기
				$(".btn-ledger-file-down").on("click", function(){
					TaxDocumentDownload($(this).data("target_idx"), $(this).data("file_idx"));
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
				TaxLogFileListSearch(tax_type);
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			TaxLogFileListSearch(tax_type);
		});

		//선택 이메일발송
		$("#btn-send-email-selected").on("click", function(){
			var selRowId = $("#grid_list").getGridParam("selarrrow");

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

			TaxEmailSendPopOpen(target_idx_ary, file_idx_ary, tax_type);
		});
	};

	/**
	 * 파일생성 목록/검색
	 * @constructor
	 */
	var TaxLogFileListSearch = function(tax_type){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize(),
				tax_type: tax_type
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 이메일 발송 모달 팝업 Open
	 * @param $obj
	 * @constructor
	 */
	var TaxEmailSendPopOpen = function(target_idx_ary, file_idx_ary, tax_type){
		var p_url = "tax_email_pop.php";
		var dataObj = new Object();
		dataObj.tax_type = tax_type;
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
	var TaxEmailSendPopInit = function(){

		//첨부파일 다운받기 버튼 바인딩
		$(".btn-xls-down").on("click", function(){
			//xls_hidden_frame.location.replace($("#stock_order_document_short_url").val());
			xls_hidden_frame.location.replace('/proc/_tax_download_xls.php?file_idx='+$(this).data("idx"));
		});

		//폼 초기화
		TaxEmailSendFormInit();
	};

	/**
	 * 이메일 발송 폼 진행 여부
	 * @type {boolean}
	 * @private
	 */
	var _TaxEmailSendIng = false;

	/**
	 * 이메일 발송 폼 초기화
	 * @constructor
	 */
	var TaxEmailSendFormInit = function(){
		//저장 버튼
		$("#btn-send-email").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if(!_TaxEmailSendIng) {
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

				_TaxEmailSendIng = true;
				showLoader();
				var p_url = "tax_proc_ajax.php";
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
					_TaxEmailSendIng = false;
				}).fail(function(jqXHR, textStatus){
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
					_StockOrderEmailSendIng = false;
				});
				return false;

			}catch(e){
				alert(e);
				_TaxEmailSendIng = false;
				return false;
			}
		});
	};

	/**
	 * 이메일 발송 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var TaxLogEmailInit = function(tax_type){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

		if(tax_type == "PURCHASE") {
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
			url: './tax_log_email_pop_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize(),
				tax_type: tax_type
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
					TaxDocumentDownload($(this).data("target_idx"), $(this).data("file_idx"));
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
				TaxLogEmailListSearch(tax_type);
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			TaxLogEmailListSearch(tax_type);
		});
	};

	/**
	 * 이메일 발송 이력 목록/검색
	 * @constructor
	 */
	var TaxLogEmailListSearch = function(tax_type){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize(),
				tax_type: tax_type
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 다운로드 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var TaxLogDownInit = function(tax_type){
		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "6");

		if(tax_type == "PURCHASE") {
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
			url: './tax_log_down_pop_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize(),
				tax_type: tax_type
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
					TaxDocumentDownload($(this).data("target_idx"), $(this).data("file_idx"));
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
				TaxLogDownListSearch(tax_type);
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			TaxLogDownListSearch(tax_type);
		});
	};

	/**
	 * 다운로드 이력 목록/검색
	 * @constructor
	 */
	var TaxLogDownListSearch = function(tax_type){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize(),
				tax_type: tax_type
			}
		}).trigger("reloadGrid");
	};


	var TaxHistoryInit = function(tax_type){

		if(tax_type == "SALE") {

			$target_idx = $("#seller_idx");

			//판매처 그룹 및 공급처 선택창 초기화
			CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
			$(".seller_idx").SumoSelect({
				placeholder: '전체 판매처',
				captionFormat: '{0}개 선택됨',
				captionFormatAllSelected: '{0}개 모두 선택됨',
				search: true,
				searchText: '판매처 검색',
				noMatch: '검색결과가 없습니다.'
			});
		}else{

			$target_idx = $("#supplier_idx");

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
		}

		$(".money").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 0, autoGroup: true, rightAlign: true});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});

		//파일생성 버튼
		$(".btn-file-create-pop").on("click", function(){

			var url = "tax_history_create_pop.php";
			url += "?tax_type=" + tax_type;
			url += "&target_idx=" + $("#target_idx").val();
			url += "&date_year=" + $("#date_end_year").val();
			url += "&date_month=" + $("#date_end_month").val();

			Common.newWinPopup(url, 'tax_history_create_pop', 550, 300, 'yes');
		});

		//이메일발송이력 버튼
		$(".btn-email-log-pop").on("click", function(){
			Common.newWinPopup("tax_log_email_pop.php?tax_type="+tax_type, 'tax_log_email_pop', 1200, 720, 'yes');
		});
		//다운로드이력 버튼
		$(".btn-down-log-pop").on("click", function(){
			Common.newWinPopup("tax_log_down_pop.php?tax_type="+tax_type, 'tax_log_down_pop', 1200, 720, 'yes');
		});
		//파일생성이력 버튼
		$(".btn-file-log-pop").on("click", function(){
			Common.newWinPopup("tax_log_file_pop.php?tax_type="+tax_type, 'tax_log_file_pop', 1400, 720, 'yes');
		});

		//일별 버튼
		$(".btn-daily-pop").on("click", function(){

			var url = "tax_daily_pop.php";
			url += "?mode=" + $(this).data("mode");
			url += "&tax_type=" + tax_type;
			url += "&target_idx=" + $(this).data("idx");
			url += "&date_ym=" + $(this).data("ym");
			url += "&name=" + $(this).data("name");

			Common.newWinPopup(url, 'tax_daily_pop', 380, 600, 'yes');
		});

		window.floatThead = $('table.floatThead');
		window.timer = null;
		$(window).on('resize', function(){
			clearTimeout(window.timer);
			window.timer = setTimeout(function(){
				window.floatThead.floatThead('destroy'); //may not need this settimeout

				window.floatThead.floatThead({
					position: 'fixed',
					top: 50,
					zIndex: 900
				});
			}, 100);
		}).trigger("resize");
	};

	var TaxHistoryCreatePopInit = function(tax_type){
		if(tax_type == "SALE") {

			$target_idx = $("#seller_idx");

			//판매처 그룹 및 공급처 선택창 초기화
			CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
			// $(".seller_idx").SumoSelect({
			// 	placeholder: '전체 판매처',
			// 	captionFormat: '{0}개 선택됨',
			// 	captionFormatAllSelected: '{0}개 모두 선택됨',
			// 	search: true,
			// 	searchText: '판매처 검색',
			// 	noMatch: '검색결과가 없습니다.'
			// });
		}else{

			$target_idx = $("#supplier_idx");

			//공급처 그룹 및 공급처 선택창 초기화
			CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");
			// $(".supplier_idx").SumoSelect({
			// 	placeholder: '전체 공급처',
			// 	captionFormat : '{0}개 선택됨',
			// 	captionFormatAllSelected : '{0}개 모두 선택됨',
			// 	search: true,
			// 	searchText: '공급처 검색',
			// 	noMatch : '검색결과가 없습니다.'
			// });
		}

		//파일생성 버튼
		$(".btn-create-tax-xls").on("click", function(){
			
			if($("#target_idx").val() == null || $("#target_idx").val() == "" ){
				alert("거래처를 선택해주세요.");
				return;
			}

			var date_y = $("#date_year").val();
			var date_m = $("#date_month").val();

			date_m = Common.LeftPad(date_m, 2);

			$("#date_ym").val(date_y+"-"+date_m);
			
			TaxXlsCreate();
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
		TaxSaleWriteInit : function(tax_type){
			TaxSaleWriteInit(tax_type);
		},
		TaxDailyPopInit: function(title){
			TaxDailyPopInit(title);
		},
		TaxLogFileInit: function(tax_type){
			TaxLogFileInit(tax_type);
		},
		TaxEmailSendPopInit: TaxEmailSendPopInit,
		TaxLogEmailInit: function(tax_type){
			TaxLogEmailInit(tax_type);
		},
		TaxLogDownInit: function(tax_type){
			TaxLogDownInit(tax_type);
		},
		TaxHistoryInit: function(tax_type){
			TaxHistoryInit(tax_type);
		},
		TaxHistoryCreatePopInit: function(tax_type){
			TaxHistoryCreatePopInit(tax_type);
		}
	}
})();
$(function(){
	SettleTax.init();
});