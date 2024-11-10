/*
 * 정산통계 - 정산예정금 관련 js
 */
var SettleLoss = (function() {
	var root = this;

	var xlsDownIng = false;
	var xlsDownInterval;

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
	 * 정산예정금 엑셀 업로드 관련 내용 저장 변수
	 * @type {Object}
	 * @private
	 */
	var __LossXlsVar = new Object();
	__LossXlsVar.xlsValueRow = 0;                 //업로드된 엑셀 Row 중 정상인 Row 수
	__LossXlsVar.xlsUploadedFileName = "";        //업로드 된 엑셀 파일명
	__LossXlsVar.xlsWritePageMode = "";           //일괄등록 / 일괄수정 Flag
	__LossXlsVar.xlsWriteReturnStyle = "";        //리스트 반환 또는 적용
	__LossXlsVar.xlsUserFileName = "";            //사용자가 업로드한 파일명
	__LossXlsVar.seller_idx = "";                 //판매처 IDX

	/**
	 * 정산예정금 일괄등록 페이지 초기화
	 * @constructor
	 */
	var LossUploadInit = function(){
		__LossXlsVar.xlsWritePageMode = $("#xlswrite_mode").val();
		__LossXlsVar.xlsWriteReturnStyle = $("#xlswrite_act").val();

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_ALL_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '판매처를 선택해주세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		$(".btn-upload").on("click", function(){
			if($("select[name='seller_idx']").val() == "" || $("select[name='seller_idx']").val() == null)
			{
				alert("판매처를 선택해주세요.");
				return false;
			}

			if($("input[name='xls_file']").val() == "")
			{
				alert("업로드 할 파일을 선택해주세요.");
				return false;
			}
	
			showLoader();
			$("#searchForm").submit();
		});
	
		$(".btn-xls-insert").on("click", function(){
			if(__LossXlsVar.xlsValidRow < 1)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = __LossXlsVar.xlsValidRow + "건의 데이터를 적용 하시겠습니까?";
				if(confirm(msg)) {
					LossUploadXlsInsert();
				}
			}
		});
	
		//이력 버튼 바인딩
		$(".btn-upload-log-pop").on("click", function(){
			Common.newWinPopup("/order/order_invoice_upload_log_pop.php", 'order_invoice_upload_log_pop', 800, 720, 'no');
		});

		LossUploadGridInit();
	};
	
	/**
	 * 정산예정금 일괄등록 목록 jqGrid 초기화
	 * @constructor
	 */
	var LossUploadGridInit = function(){
		var validErr = [];
	
		$("#grid_list").jqGrid({
			url: './loss_upload_proc_xls.php',
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
				{ label: '주문번호', name: 'A', index: 'market_order_no', width: 150, sortable: false},
				{ label: '구매자명', name: 'B', index: 'order_name', width: 100, sortable: false},
				{ label: '제품명', name: 'C', index: 'market_product_name', width: 120, sortable: false},
				{ label: '판매수량', name: 'D', index: 'order_cnt', width: 80, sortable: false, align: 'right'},
				{ label: '매출금액', name: 'E', index: 'order_amt', width: 100, sortable: false, align: 'right'},
				{ label: '수수료', name: 'F', index: 'commission', width: 100, sortable: false, align: 'right'},
				{ label: '공제/환급내역<br>기타수수료', name: 'G', index: 'commission_etc', width: 100, sortable: false, align: 'right'},
				{ label: '배송비', name: 'H', index: 'delivery_fee', width: 100, sortable: false, align: 'right'},
				{ label: '배송비수수료', name: 'I', index: 'delivery_commission', width: 100, sortable: false, align: 'right'},
				{ label: '정산금액', name: 'J', index: 'settle_amount', width: 100, sortable: false, align: 'right'},
				{ label: '정산일자', name: 'K', index: 'loss_date', width: 150, sortable: false, align: 'center'},
				{ label: '비고', name: 'valid', index: 'valid', width: 150, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;
						if(cellvalue)
						{
							rst = "정상";
							__LossXlsVar.xlsValidRow++;
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
				__LossXlsVar.xlsValidRow = 0;
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});
	
		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};
	
	/**
	 * 정산예정금 - 업로드 된 엑셀 파일 로딩
	 * @param xls_file_path_name
	 * @constructor
	 */
	var LossUploadXlsRead = function(xls_file_path_name, xls_user_filename){
		//console.log(xls_file_path_name);
		__LossXlsVar.xlsUploadedFileName = xls_file_path_name;
		__LossXlsVar.xlsUserFileName = xls_user_filename;
		$("input[name='xls_file']").val('');
	
		//적용할 Row 수 초기화
		__LossXlsVar.xlsValidRow = 0;

		__LossXlsVar.seller_idx = $("select[name='seller_idx']").val();
	
		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				mode: __LossXlsVar.xlsWritePageMode,
				act: __LossXlsVar.xlsWriteReturnStyle,
				xls_filename: xls_file_path_name,
				user_filename: xls_user_filename,
				seller_idx: __LossXlsVar.seller_idx
			}
		}).trigger("reloadGrid");
	};
	
	/**
	 * 정산예정금 - 업로드 된 엑셀 파일 적용
	 * @constructor
	 */
	var LossUploadXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");
	
		var p_url = "loss_upload_proc_xls.php";
		var dataObj = new Object();
		dataObj.mode = __LossXlsVar.xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = __LossXlsVar.xlsUploadedFileName;
		dataObj.user_filename = __LossXlsVar.xlsUserFileName;
		dataObj.xls_validrow = __LossXlsVar.xlsValidRow;
		dataObj.seller_idx = __LossXlsVar.seller_idx;
	
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
	 * 정산예정금 업로드 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var LossUploadLogPopInit = function(){
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
				LossUploadLogPopSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			LossUploadLogPopSearch();
		});
	};

	/**
	 * 정산예정금 업로드 이력 팝업 목록/검색
	 * @constructor
	 */
	var LossUploadLogPopSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 정산예정금 관리 페이지 초기화
	 * @constructor
	 */
	var LossListInit = function(){
		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_ALL_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '판매처를 선택해주세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//날짜 설정 : 기간 1주일
		$("input[name='date_end'], input[name='date_start']").on("change", function(){

			var date_start = $("input[name='date_start']").val();
			var date_end = $("input[name='date_end']").val();

			var x = moment(date_end).diff(moment(date_start), "days");

			if(x > 39 || x < 0){
				alert("기간은 40일 이내로만 설정가능합니다.");
				var date_start = moment(date_end).add(-39, "days").format("YYYY-MM-DD");
				$("input[name='date_start']").val(date_start);
			}

		});

		$(".btn-go-stats").on("click", function(){
			var date_start = $("input[name='date_start']").val();
			var date_end = $("input[name='date_end']").val();
			var seller_idx = $("#seller_idx").val();
			var url = "/settle/loss_statistics.php?date_start="+date_start+"&date_end="+date_end+"&seller_idx="+seller_idx;
			location.href=url;
		});

		$(".btn-xls-down").on("click", function(){
			LossListXlsDown();
		});

		//항목설정 팝업
		$(".btn-column-setting-pop").on("click", function(){
			Common.newWinPopup("/common/column_setting_pop.php?target=LOSS_LIST&mode=list", 'column_setting_pop', 700, 720, 'no');
		});



		LossListGridInit();

	};


	/**
	 * 정산예정금 Grid 초기화
	 * @constructor
	 */
	var LossListGridInit = function(){

		$("#grid_list_loss").jqGrid({
			url: './loss_list_grid.php',
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
			colModel: _gridColModel,
			rowNum: 1000,
			rowList: [],
			pager: '#grid_pager',
			sortname: 'A.loss_date',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list_loss");

				$(".btn-confirm").on("click", function(){
					if(!confirm('확인 처리 하시겠습니까?')){
						return;
					}

					SettleLoss.LossListConfirm($(this).data("idx"));

				});

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("loss_list", $("#grid_list_loss"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list_loss").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "loss_list");
			}
		});

		setTimeout(function(){
			//$.jgrid.loadState("grid_list_loss", {restoreData: false, clearAfterLoad : true});
		}, 500);

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list_loss");
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
				LossListGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			LossListGridSearch();
			LossGetBankCustomeIn();
			LossGetRefundTran();
		});
	};

	/**
	 * 정산예정금 페이지 Grid 목록/검색
	 * @constructor
	 */
	var LossListGridSearch = function(){

		if($("#seller_idx").val() == null || $("#seller_idx").val() == "" ){
			alert("판매처를 선택해주세요.");
			return;
		}

		$("#grid_list_loss").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 정산예정금 페이지 Grid 목록/검색
	 * @constructor
	 */
	var LossListGridReload = function(){

		if($("#seller_idx").val() == null || $("#seller_idx").val() == "" ){
			alert("판매처를 선택해주세요.");
			return;
		}

		$("#grid_list_loss").setGridParam({
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 정산예정금 관리 엑셀 다운로드
	 * @constructor
	 */
	var LossListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "loss_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_LOSS_LIST", function(){
				SettleLoss.LossListXlsDownComplete();
			});
		}, 500);
	};

	var LossListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	var LossListConfirm = function(loss_idx){
		var p_url = "loss_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "update_confirm";
		dataObj.loss_idx = loss_idx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response) {
				if(response.result) {
					LossListGridReload();
				}
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	var LossGetBankCustomeIn = function(){

		var table_class = ".bank_customer_in";

		$(table_class + " tbody").empty();
		tableLoader.on(table_class);

		var p_url = "loss_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "bank_customer_in";
		dataObj.date_start = $("#date_start").val();
		dataObj.date_end = $("#date_end").val();
		dataObj.seller_idx = $("#seller_idx").val();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response) {
				var total = 0;
				$.each(response.data, function(i, o){
					var tran_date = o.tran_date;
					var account_name = o.account_name;
					var tran_amount = Number(o.tran_amount);
					var tran_memo = o.tran_memo;

					total += tran_amount;

					var html = "";
					if(tran_date == null || tran_date == "" || tran_date == "null"){
					html += '<tr class="sum">';
						html += '<td class="" colspan="2">합계</td>';
						tran_memo = "";
					}else {
					html += '<tr class="">';
						html += '<td class="text_left">' + tran_date + '</td>';
						html += '<td class="text_left">' + account_name + '</td>';
					}
					html += '<td class="text_right ">'+Common.addCommas(tran_amount)+'</td>';
					html +=	'<td class="text_left">'+tran_memo+'</td>';
					html += '</tr>';

					$(table_class + " tbody").append(html);
				});
			}
			tableLoader.off(table_class);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(table_class);
		});

	};

	var LossGetRefundTran = function(){

		var table_class = ".refund";

		$(table_class + " tbody").empty();
		tableLoader.on(table_class);

		var p_url = "loss_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "refund";
		dataObj.date_start = $("#date_start").val();
		dataObj.date_end = $("#date_end").val();
		dataObj.seller_idx = $("#seller_idx").val();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response) {
				var total = 0;
				$.each(response.data, function(i, o){
					var tran_date = o.ledger_date;
					var ledger_title = o.ledger_title;
					var tran_amount = Number(o.tran_amount);
					var tran_memo = o.tran_memo;

					total += tran_amount;

					var html = "";
					if(tran_date == null || tran_date == "" || tran_date == "null"){
						html += '<tr class="sum">';
						html += '<td class="" colspan="2">합계</td>';
						tran_memo = "";
					}else {
						html += '<tr class="">';
						html += '<td class="text_left">' + tran_date + '</td>';
						html += '<td class="text_left">' + ledger_title + '</td>';
					}
					html += '<td class="text_right ">'+Common.addCommas(tran_amount)+'</td>';
					html +=	'<td class="text_left">'+tran_memo+'</td>';
					html += '</tr>';

					$(table_class + " tbody").append(html);
				});
			}
			tableLoader.off(table_class);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(table_class);
		});

	};

	/**
	 * 정산예정금 통계 페이지 초기화
	 * @constructor
	 */
	var LossStatisticsInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "8");

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_ALL_GROUP", ".product_seller_group_idx", ".seller_idx");
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
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){

			if($("#seller_idx").val() == null || $("#seller_idx").val() == "" ){
				alert("판매처를 선택해주세요.");
				return;
			}

			$("#searchForm").submit();
		});

		//다운로드 버튼 클릭 이벤트
		$(".btn-xls-down").on("click", function(){
			LossStatisticsXlsDown();
		});
	};

	/**
	 * 정산예정금 통계 엑셀 다운로드
	 * @constructor
	 */
	var LossStatisticsXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "loss_statistics_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("LOASS_STATISTICS", function(){
				SettleLoss.LossStatisticsXlsDownComplete();
			});
		}, 500);
	};

	var LossStatisticsXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 일별매출차트 매출 그래프 초기화
	 * @constructor
	 */
	var LossStatisticsChart = function(){
		// Themes begin
		am4core.useTheme(am4themes_animated);
		// Themes end

		var chart = am4core.create("chartdiv", am4charts.XYChart);
		chart.hiddenState.properties.opacity = 0; // this creates initial fade-in
		chart.language.locale = am4lang_ko_KR;
		chart.fontFamilly = "dotum";

		// Add chart title
		var title = chart.titles.create();
		title.text = "통계";
		title.fontSize = 14;
		title.marginBottom = 10;


		chart.data = chartData;

		var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
		categoryAxis.renderer.grid.template.location = 0;
		categoryAxis.dataFields.category = "date";
		categoryAxis.renderer.grid.template.disabled = true;
		categoryAxis.renderer.minGridDistance = 10;
		categoryAxis.dashLength = 5;
		//categoryAxis.renderer.labels.template.rotation = 45;

		var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
		valueAxis.min = 0;
		valueAxis.strictMinMax = false;
		valueAxis.renderer.minGridDistance = 30;
		valueAxis.title.text = "Money";
		valueAxis.dashLength = 5;

		var series = chart.series.push(new am4charts.LineSeries());
		series.dataFields.categoryX = "date";
		series.dataFields.valueY = "settle";
		series.name = "정산";
		series.tooltipText = "{value}";
		series.strokeWidth = 2;
		series.bullets.push(new am4charts.CircleBullet());
		series.tooltipText = "{name} - {categoryX}: {valueY}";
		series.legendSettings.valueText = "{valueY}";
		series.visible  = false;


		var series2 = chart.series.push(new am4charts.LineSeries());
		series2.dataFields.categoryX = "date";
		series2.dataFields.valueY = "site";
		series2.name = "사이트";
		series2.tooltipText = "{value}";
		series2.strokeWidth = 2;
		series2.bullets.push(new am4charts.CircleBullet());
		series2.tooltipText = "{name} - {categoryX}: {valueY}";
		series2.legendSettings.valueText = "{valueY}";
		series2.visible  = false;

		var series3 = chart.series.push(new am4charts.LineSeries());
		series3.dataFields.categoryX = "date";
		series3.dataFields.valueY = "etc";
		series3.name = "공제/환급액";
		series3.tooltipText = "{value}";
		series3.strokeWidth = 2;
		series3.bullets.push(new am4charts.CircleBullet());
		series3.tooltipText = "{name} - {categoryX}: {valueY}";
		series3.legendSettings.valueText = "{valueY}";
		series3.visible  = false;

		var series4 = chart.series.push(new am4charts.LineSeries());
		series4.dataFields.categoryX = "date";
		series4.dataFields.valueY = "tran";
		series4.name = "실입금액";
		series4.tooltipText = "{value}";
		series4.strokeWidth = 2;
		series4.bullets.push(new am4charts.CircleBullet());
		series4.tooltipText = "{name} - {categoryX}: {valueY}";
		series4.legendSettings.valueText = "{valueY}";
		series4.visible  = false;


		// Add chart cursor
		chart.cursor = new am4charts.XYCursor();
		chart.cursor.behavior = "zoomY";

		// Add legend
		chart.legend = new am4charts.Legend();

		chart.exporting.menu = new am4core.ExportMenu();
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

	/***
	 * 테이블 로더
	 * @type {{off: off, on: on}}
	 */
	var tableLoader = {
		on : function(sel){
			//var loader = '<div class="table-loading-wrap"><div class="table-loading"><div></div><div></div><div></div></div></div>';
			var loader = '<div class="table-loading-wrap"><div class="sk-fading-circle"><div class="sk-circle1 sk-circle"></div><div class="sk-circle2 sk-circle"></div><div class="sk-circle3 sk-circle"></div><div class="sk-circle4 sk-circle"></div><div class="sk-circle5 sk-circle"></div><div class="sk-circle6 sk-circle"></div><div class="sk-circle7 sk-circle"></div><div class="sk-circle8 sk-circle"></div><div class="sk-circle9 sk-circle"></div><div class="sk-circle10 sk-circle"></div><div class="sk-circle11 sk-circle"></div><div class="sk-circle12 sk-circle"></div></div></div>';
			$(sel).after(loader);
		},
		off : function(sel){
			$(sel).siblings(".table-loading-wrap").remove();
		}
	};

	return {
		init: init,
		LossUploadInit: LossUploadInit,
		LossUploadXlsRead: LossUploadXlsRead,
		LossUploadLogPopInit: LossUploadLogPopInit,
		LossListInit: LossListInit,
		LossListConfirm: LossListConfirm,
		LossStatisticsInit: LossStatisticsInit,
		LossStatisticsChart: LossStatisticsChart,
		LossListXlsDownComplete: LossListXlsDownComplete,
		LossStatisticsXlsDownComplete: LossStatisticsXlsDownComplete,
	}
})();
$(function(){
	SettleLoss.init();
});