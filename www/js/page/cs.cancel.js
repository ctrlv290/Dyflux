/*
 * CS 취소관리 js
 */
var CSCancel = (function() {
	var root = this;
	var orderTabs = null;

	var xlsDownIng = false;
	var xlsDownInterval;

	var init = function() {
	};

	/**
	 * 판매처취소 엑셀 업로드 관련 내용 저장 변수
	 * @type {Object}
	 * @private
	 */
	var __CSCancelXls = new Object();
	__CSCancelXls.xlsValueRow = 0;                 //업로드된 엑셀 Row 중 정상인 Row 수
	__CSCancelXls.xlsUploadedFileName = "";        //업로드 된 엑셀 파일명
	__CSCancelXls.xlsWritePageMode = "";           //일괄등록 / 일괄수정 Flag
	__CSCancelXls.xlsWriteReturnStyle = "";        //리스트 반환 또는 적용
	__CSCancelXls.xlsUserFileName = "";            //사용자가 업로드한 파일명
	__CSCancelXls.seller_idx = "";                 //판매처 IDX

	var CSCancelUploadInit = function(){

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '판매처를 선택해주세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});


		//포맷설정 팝업
		$("#modal_format").dialog({
			width: 600,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow(); $( "#modal_format" ).html(""); },
		});

		//포맷설정 버튼 바인딩
		$(".btn-format-pop").on("click", function(){
			CSCancelFormatPopOpen();
		});

		__CSCancelXls.xlsWritePageMode = $("#xlswrite_mode").val();
		__CSCancelXls.xlsWriteReturnStyle = $("#xlswrite_act").val();

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
			if(__CSCancelXls.xlsValidRow < 1)
			{
				alert("취소할 데이터가 없습니다.");
				return;
			}else{
				//var msg = __OrderShippedCancelXls.xlsValidRow + "건의 송장번호를 삭제 하시겠습니까?";
				var msg = "취소된 배송정보는 복구되지 않습니다.\n일괄 취소 하시겠습니까?";
				if(confirm(msg)) {
					CSCancelXlsInsert();
				}
			}
		});

		CSCancelXlsGridInit();

	};

	/**
	 * 판매처취소 목록 jqGrid 초기화
	 * @constructor
	 */
	var CSCancelXlsGridInit = function(){
		var validErr = [];

		$("#grid_list").jqGrid({
			url: './seller_cancel_xls_proc.php',
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
				{ label: '관리번호', name: 'order_idx', index: 'invoice_no', width: 80, sortable: false},
				{ label: '상품번호', name: 'market_product_no', index: 'invoice_no', width: 80, sortable: false},
				{ label: '판매처', name: 'seller_name', index: 'invoice_no', width: 80, sortable: false},
				{ label: '주문자', name: 'order_name', index: 'invoice_no', width: 80, sortable: false},
				{ label: '상품명', name: 'market_product_name', index: 'invoice_no', width: 80, sortable: false},
				{ label: '수량', name: 'order_cnt', index: 'invoice_no', width: 80, sortable: false},
				{ label: '취소사유', name: 'reason', index: 'invoice_no', width: 80, sortable: false},
				{ label: '취소요청일', name: 'cancel_date', index: 'invoice_no', width: 80, sortable: false},
				{ label: '반품송장번호', name: 'return_invoice_no', index: 'invoice_no', width: 80, sortable: false},
				{ label: '작업', name: 'cs', index: 'cs', width: 80, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						__CSCancelXls.xlsValidRow++;
						return '<a href="javascript:;" class="btn btn-cs-pop" data-idx="'+rowobject.order_pack_idx+'">CS</a>';
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

				$(".btn-cs-pop").on("click", function(){
					Common.newWinPopup2('/cs/cs.php?order_idx='+$(this).data("idx"), 'cs_pop', 0, 0, 0, 1);
				});
			},
			beforeRequest: function(){
				__CSCancelXls.xlsValidRow = 0;

			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	/**
	 * 판매처취소 - 업로드 된 엑셀 파일 로딩
	 * @param xls_file_path_name
	 * @constructor
	 */
	var CSCancelXlsRead = function(xls_file_path_name, xls_user_filename){
		//console.log(xls_file_path_name);
		__CSCancelXls.xlsUploadedFileName = xls_file_path_name;
		__CSCancelXls.xlsUserFileName = xls_user_filename;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		__CSCancelXls.xlsValidRow = 0;

		__CSCancelXls.seller_idx = $("select[name='seller_idx']").val();

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				mode: __CSCancelXls.xlsWritePageMode,
				act: __CSCancelXls.xlsWriteReturnStyle,
				xls_filename: xls_file_path_name,
				user_filename: xls_user_filename,
				seller_idx: $("select[name='seller_idx']").val()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 판매처취소 - 업로드 된 엑셀 파일 적용
	 * @constructor
	 */
	var CSCancelXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "seller_cancel_xls_proc.php";
		var dataObj = new Object();
		dataObj.mode = __CSCancelXls.xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = __CSCancelXls.xlsUploadedFileName;
		dataObj.user_filename = __CSCancelXls.xlsUserFileName;
		dataObj.xls_validrow = __CSCancelXls.xlsValidRow;
		dataObj.seller_idx = __CSCancelXls.seller_idx;

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
				alert("일괄 취소 되었습니다.");
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
	 * 판매처취소 포맷설정 팝업 오픈
	 * @constructor
	 */
	var CSCancelFormatPopOpen = function(){
		var p_url = "seller_cancel_format_pop.php";
		showLoader();
		$.ajax({
			type: 'GET',
			url: p_url,
			dataType: "html"
		}).done(function (response) {
			if(response)
			{
				$("#modal_format").html(response);
				$("#modal_format").dialog( "open" );
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
	 * 판매처취소 포맷설정 팝업 닫기
	 * @constructor
	 */
	var CSCancelFormatPopClose = function(){
		$("#modal_format").html("");
		$("#modal_format").dialog( "close" );
	};

	/**
	 * 판매처취소 포맷설정 페이지 초기화
	 * @constructor
	 */
	var CSCancelFormatPopInit = function(){

		$(".btn-common-pop-close").on("click", function(){
			CSCancelFormatPopClose();
		});

		$(".seller_cancel_format_pop .seller_idx").on("change", function(){

			var seller_idx = $(this).val();

			if(seller_idx == "") return;

			//판매처 포맷
			p_url = "/cs/seller_cancel_format_ajax.php";
			var dataObj = new Object();
			dataObj.mode = "get_seller_format";
			dataObj.seller_idx = seller_idx;
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if(response.result){
					var data = response.data;
					$.each(data, function(i, o){
						$("select[name='"+i+"']").val(o);
					});
				}else{
					$(".header_sel").val("A");
				}
			}).fail(function(jqXHR, textStatus){
			});

		});

		$("#btn-save").on("click", function(){

			var seller_idx = $(".seller_cancel_format_pop .seller_idx").val();
			if(seller_idx == ""){
				alert("판매처를 선택해주세요.");
				return;
			}

			if(!confirm('저장하시겠습니까?')){
				return;
			}

			p_url = "/cs/seller_cancel_format_ajax.php";
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormFormat']").serialize()
			}).done(function (response) {
				if(response.result){
					CSCancelFormatPopClose();
				}
			}).fail(function(jqXHR, textStatus){
			});

		});

	};

	/**
	 * 취소결과조회 페이지 초기화
	 * @constructor
	 */
	var CSCancelListInit = function(){
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


		//시간 inputMask
		$(".time_start, .time_end").inputmask("datetime", {
				placeholder: 'hh:mm:ss',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);

		$(".btn-cs-write").on("click", function(){
			CSPopupCSWritePopOpen();
		});

		//전체확인 버튼 바인딩
		$(".btn-cancel-confirm-all").on("click", function(){
			CSCancelListConfirmAll();
		});

		//선택확인
		$(".btn-cancel-confirm-selected").on("click", function(){
			CSCancelListConfirmSelected();
		});

		//다운로드
		$(".btn-xls-down").on("click", function(){
			CSCancelListXlsDown();
		});

		CSCancelListGridInit();
	};

	/**
	 * 취소결과조회 Grid 초기화
	 * @constructor
	 */
	var CSCancelListGridInit = function(){

		//Grid 초기화
		$("#grid_list").jqGrid({
			url: '/cs/seller_cancel_list_grid.php',
			mtype: "GET",
			datatype: "json",
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
				{ label: 'order_pack_idx', name: 'order_pack_idx', index: 'order_pack_idx', width: 0, hidden: true},
				{ label: 'order_seller_cancel_confirm', name: 'order_seller_cancel_confirm', index: 'order_seller_cancel_confirm', width: 0, hidden: true},
				{ label: 'order_is_seller_cancel_val', name: 'order_is_seller_cancel_val', index: 'order_is_seller_cancel_val', width: 0, hidden: true, formatter: function(cellvalue, options, rowobject){
						return rowobject.order_is_seller_cancel;
					}},
				{ label: '접수일', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 180, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 120, sortable: false},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 100, sortable: false},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 150, sortable: false},
				{ label: '주문자', name: 'order_name', index: 'order_name', width: 100, sortable: false},
				{ label: '주문수량', name: 'order_cnt', index: 'order_cnt', width: 80, sortable: false},
				{ label: '송장번호', name: 'invoice_no', index: 'invoice_no', width: 150, sortable: false},
				{ label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 150, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '주문상태', name: 'order_progress_step', index: 'order_progress_step', width: 60, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.convertOrderStatusTextToLabel(rowobject.order_progress_step_han);
					}},
				{ label: '취소일', name: 'order_seller_cancel_date', index: 'order_seller_cancel_date', width: 180, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '메모', name: 'order_seller_cancel_reason', index: 'order_seller_cancel_reason', width: 180, sortable: false, align: 'left'},

				{ label: '작업', name: 'order_seller_cancel_confirm_btn', index: 'order_seller_cancel_confirm_btn', width: 80, sortable: true, align: 'center', formatter: function(cellvalue, options, rowobject){

					var txt = "확인";
					var a_class = "green_btn";
					var val = "Y";
					if(rowobject.order_seller_cancel_confirm == "Y") {
						txt = "확인취소";
						a_class = "red_btn";
						val = "N";
					}

						return '<a href="javascript:;" class="btn '+a_class+' btn-seller-cancel-confirm" data-idx="'+rowobject.order_idx+'" data-val="'+val+'">'+txt+'</a>';
					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'O.order_progress_step_accept_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			multiselect : true,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//상품 선택 버튼 바인딩
				$(".btn-seller-cancel-confirm").on("click", function(){
					var rowNum = $(this).data("num");

					CSCancelListConfirmOne($(this));

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
				CSCancelListGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			CSCancelListGridSearch();
		});

	};

	/**
	 * 취소결과조회 목록/검색
	 * @constructor
	 */
	var CSCancelListGridSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 취소결과조회 reload
	 * @constructor
	 */
	var CSCancelListGridReload = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 취소결과조회 엑셀 다운로드
	 * @constructor
	 */
	var CSCancelListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "seller_cancel_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_SELLER_CANCEL_LIST", function(){
				CSCancel.CSCancelListXlsDownComplete();
			});
		}, 500);
	};

	var CSCancelListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 취소결과조회 개별 확인/취소 처리
	 * @param $obj
	 * @constructor
	 */
	var CSCancelListConfirmOne = function($obj){
		var order_idx = $obj.data("idx");
		var confirm_val = $obj.data("val");

		var confirm_text = (confirm_val == "Y") ? "확인" : "확인취소";


		if(!confirm(confirm_text+'처리 하시겠습니까?')){
			return;
		}

		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "set_seller_cancel_confirm_one";
		dataObj.order_idx = order_idx;
		dataObj.confirm_val = confirm_val;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSCancelListGridReload();
		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * 취소결과조회 확인처리 Batch
	 * @param idx_list
	 * @constructor
	 */
	var CSCancelListConfirmBatch = function(idx_list){
		var confirm_val = "Y";

		var confirm_text = (confirm_val == "Y") ? "확인" : "확인취소";


		if(!confirm(confirm_text+'처리 하시겠습니까?')){
			return;
		}

		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "set_seller_cancel_confirm_selected";
		dataObj.order_idx_list = idx_list;
		dataObj.confirm_val = confirm_val;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSCancelListGridReload();
		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * 취소결과조회 전체확인
	 * @constructor
	 */
	var CSCancelListConfirmAll = function(){
		var rowData = $("#grid_list").getRowData();

		var idx_list = new Array();

		$.each(rowData, function(i, o){
			console.log(o);
			if(o.order_seller_cancel_confirm == "N"){
				idx_list.push(o.order_idx);
			}
		});

		if(idx_list.length == 0){
			alert("이미 모두 확인상태입니다.");
			return;
		}

		CSCancelListConfirmBatch(idx_list);

	};

	/**
	 * 취소결과조회 선택확인
	 * @constructor
	 */
	var CSCancelListConfirmSelected = function(){
		var selRowId = $("#grid_list").getGridParam("selarrrow");

		if(selRowId == null || selRowId.length == 0){
			alert('선택된 내용이 없습니다.');
			return;
		}

		var idx_list = new Array();

		$.each(selRowId, function(i, o){

			var rowData =$("#grid_list").getRowData(o);

			if(rowData.order_seller_cancel_confirm == "N"){
				idx_list.push(rowData.order_idx);
			}
		});

		if(idx_list.length == 0){
			alert("이미 모두 확인상태입니다.");
			return;
		}

		CSCancelListConfirmBatch(idx_list);
	};


	/**
	 * 취소철회조회 페이지 초기화
	 * @constructor
	 */
	var CSCancelOffListInit = function(){
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


		//시간 inputMask
		$(".time_start, .time_end").inputmask("datetime", {
				placeholder: 'hh:mm:ss',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);

		$(".btn-cs-write").on("click", function(){
			CSPopupCSWritePopOpen();
		});

		//선택취소 버튼 바인딩
		$(".btn-cancel-off-confirm-N").on("click", function(){
			CSCancelOffConfirmSelected("N");
		});

		//선택확인
		$(".btn-cancel-off-confirm-Y").on("click", function(){
			CSCancelOffConfirmSelected("Y");
		});

		//다운로드
		$(".btn-xls-down").on("click", function(){
			CSCancelOffListXlsDown("Y");
		});

		CSCancelOffListGridInit();
	};

	/**
	 * 취소철회조회 Grid 초기화
	 * @constructor
	 */
	var CSCancelOffListGridInit = function(){

		//Grid 초기화
		$("#grid_list").jqGrid({
			url: '/cs/seller_cancel_off_list_grid.php',
			mtype: "GET",
			datatype: "json",
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
				{ label: 'order_pack_idx', name: 'order_pack_idx', index: 'order_pack_idx', width: 0, hidden: true},
				{ label: 'order_seller_cancel_off_confirm', name: 'order_seller_cancel_off_confirm', index: 'order_seller_cancel_off_confirm', width: 0, hidden: true},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 100, sortable: false},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 150, sortable: false},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 120, sortable: false},
				{ label: '주문자', name: 'order_name', index: 'order_name', width: 100, sortable: false},
				{ label: '주문상태', name: 'order_progress_step', index: 'order_progress_step', width: 60, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.convertOrderStatusTextToLabel(rowobject.order_progress_step_han);
					}},
				{ label: '접수일', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 180, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 150, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '취소일', name: 'order_seller_cancel_date', index: 'order_seller_cancel_date', width: 180, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '취소철회접수일', name: 'order_seller_cancel_off_date', index: 'order_seller_cancel_off_date', width: 180, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 180, sortable: false, align: 'left'},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'O.order_progress_step_accept_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			multiselect : true,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//상품 선택 버튼 바인딩
				$(".btn-seller-cancel-confirm").on("click", function(){
					var rowNum = $(this).data("num");

					CSCancelListConfirmOne($(this));

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
				CSCancelOffListGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			CSCancelOffListGridSearch();
		});

	};

	/**
	 * 취소철회조회 목록/검색
	 * @constructor
	 */
	var CSCancelOffListGridSearch = function(){
		$("#grid_list").find("input[type='checkbox']").prop("checked", false);
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 취소철회조회 reload
	 * @constructor
	 */
	var CSCancelOffListGridReload = function(){
		$("#grid_list").find("input[type='checkbox']").prop("checked", false);
		$("#grid_list").setGridParam({
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};


	/**
	 * 취소결과조회 엑셀 다운로드
	 * @constructor
	 */
	var CSCancelOffListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "seller_cancel_off_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_SELLER_CANCEL_OFF_LIST", function(){
				CSCancel.CSCancelOffListXlsDownComplete();
			});
		}, 500);
	};

	var CSCancelOffListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	var CSCancelOffConfirmBatch = function(idx_list, val){
		var confirm_text = (val == "Y") ? "확인" : "취소";


		if(!confirm(confirm_text+'처리 하시겠습니까?')){
			return;
		}

		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "set_seller_cancel_off_confirm";
		dataObj.order_idx_list = idx_list;
		dataObj.confirm_val = val;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSCancelOffListGridReload();
		}).fail(function(jqXHR, textStatus){
		});
	};

	var CSCancelOffConfirmSelected = function(val){
		console.log(val);
		var confirm_text = (val == "Y") ? "확인" : "취소";
		var selRowId = $("#grid_list").getGridParam("selarrrow");

		if(selRowId == null || selRowId.length == 0){
			alert('선택된 내용이 없습니다.');
			return;
		}

		var idx_list = new Array();

		$.each(selRowId, function(i, o){

			var rowData =$("#grid_list").getRowData(o);
			console.log(rowData.order_seller_cancel_off_confirm);
			if(rowData.order_seller_cancel_off_confirm != val){
				idx_list.push(rowData.order_idx);
			}
		});

		if(idx_list.length == 0){
			alert("이미 모두 "+confirm_text+"상태입니다.");
			return;
		}

		CSCancelOffConfirmBatch(idx_list, val);
	};



	return {
		CSCancelUploadInit: CSCancelUploadInit,
		CSCancelFormatPopInit: CSCancelFormatPopInit,
		CSCancelXlsRead: CSCancelXlsRead,
		CSCancelListInit: CSCancelListInit,
		CSCancelListGridReload: CSCancelListGridReload,
		CSCancelOffListInit: CSCancelOffListInit,
		CSCancelListXlsDownComplete: CSCancelListXlsDownComplete,
		CSCancelOffListXlsDownComplete: CSCancelOffListXlsDownComplete,
	}

})();