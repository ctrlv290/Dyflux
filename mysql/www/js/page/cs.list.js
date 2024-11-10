/*
 * CS 팝업 js
 */
var CSList = (function() {
	var root = this;
	var orderTabs = null;

	var xlsDownIng = false;
	var xlsDownInterval;

	var init = function() {
	};

	/**
	 * CS내역조회 페이지 초기화
	 * @constructor
	 */
	var CSListInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input', "9");

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

		$(".btn-cs-write").on("click", function(){
			CSPopupCSWritePopOpen();
		});

		//C/S남기기 모달 팝업 세팅
		$("#modal_order_cs_write").dialog({
			width: 600,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow(); $( "#modal_order_cs_write" ).html(""); },
		});

		//CS선택완료 바인딩
		$(".btn-cs-confirm").on("click", function(){
			CSListConfirmChecked();
		});

		//CS일괄완료 바인딩
		$(".btn-cs-confirm-batch").on("click", function(){
			CSPopupSetCSConfirmBatch();
		});

		//다운로드 바인딩
		$(".btn-xls-down").on("click", function(){
			CSListXlsDown();
		});

		CSListGridInit();
	};

	/**
	 * CS내역조회 Grid 초기화
	 * @constructor
	 */
	var CSListGridInit = function(){

		//Grid 초기화
		$("#grid_list").jqGrid({
			url: '/cs/cs_list_grid.php',
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
				{ label: 'cs_idx', name: 'cs_idx', index: 'cs_idx', width: 0, hidden: true},
				{ label: 'order_pack_idx', name: 'order_pack_idx', index: 'order_pack_idx', width: 0, hidden: true},
				{ label: 'cs_full_text', name: 'cs_full_text', index: 'cs_full_text', width: 0, hidden: true},
				{ label: 'cs_confirm', name: 'cs_confirm', index: 'cs_confirm', width: 0, hidden: true},
				{ label: '판매처 상품명', name: 'market_product_name', index: 'market_product_name', width: 0, hidden: true},
				{ label: '판매처 옵션', name: 'market_product_option', index: 'market_product_option', width: 0, hidden: true},
				{ label: '등록일', name: 'cs_regdate', index: 'cs_regdate', width: 120, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 80, is_use : true},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 80, sortable: true},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 100, sortable: true},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 80, sortable: true},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 80, sortable: true},
				{ label: 'CS 내역', name: 'cs_comment', index: 'cs_comment', width: 200, sortable: true, align: 'left'},
				{ label: '작업', name: 'cs_task_han', index: 'cs_task_han', width: 100, sortable: true, align: 'left'},
				{ label: '처리상태', name: 'cs_confirm', index: 'cs_confirm', width: 60, sortable: true, align: 'center', formatter: function(cellvalue, options, rowobject){
						return (cellvalue == "Y") ? "완료" : "미처리";
					}},
				{ label: '주문상태', name: 'order_progress_step', index: 'order_progress_step', width: 60, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.convertOrderStatusTextToLabel(rowobject.order_progress_step_han);
					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'CS.cs_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			multiselect : true,
			subGrid : true,
			subGridRowExpanded : function(subGridId, rowId) {
				CSListGridExpand(subGridId, rowId);
			},
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//상품 선택 버튼 바인딩
				$(".btn-product-select").on("click", function(){
					var rowNum = $(this).data("num");
					var rowData = $("#grid_list_pop").getRowData(rowNum);
					var product_idx = rowData.product_idx;
					var product_option_idx = rowData.product_option_idx;
					var product_name = rowData.product_name;
					var product_option_name = rowData.product_option_name;
					TransactionAdjustProductAddPopupSelect(product_idx, product_option_idx, product_name, product_option_name);
				});

				//합계 표시
				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				var ORDER_PRODUCT_MATCHING = 0;
				var ORDER_ACCEPT  = 0;
				var ORDER_INVOICE = 0;
				var ORDER_SHIPPED = 0;
				if(Object.size(userData) > 0) {
					ORDER_PRODUCT_MATCHING = Common.addCommas(userData.ORDER_PRODUCT_MATCHING);
					ORDER_ACCEPT = Common.addCommas(userData.ORDER_ACCEPT);
					ORDER_INVOICE = Common.addCommas(userData.ORDER_INVOICE);
					ORDER_SHIPPED = Common.addCommas(userData.ORDER_SHIPPED);
				}
				$(".sum_total").text("발주: " + ORDER_PRODUCT_MATCHING + "건, 접수: " + ORDER_ACCEPT + "건, 송장: " + ORDER_INVOICE + "건, 배송: " + ORDER_SHIPPED + "건");
			}
		});

		console.log($("#grid_list").jqGrid('getGridParam'));

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
				CSListGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			CSListGridSearch();
		});

	};

	/**
	 * CS내역조회 Grid 확장
	 * @param subGridId
	 * @param rowId
	 * @constructor
	 */
	var CSListGridExpand = function(subGridId, rowId){
		var rowData = $("#grid_list").jqGrid ('getRowData', rowId);

		//이력 가져오기
		p_url = "/cs/cs_cs_list_ajax.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = rowData.order_pack_idx;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result){
				var html = "";
				$.each(response.data, function(i, o){
					//console.log(o);
					var msg = o.cs_comment.replace(/\n/g, '<br>');
					var is_confirm = (o.cs_confirm == "Y") ? '<span class="span_red">완료</span>' : '미처리';
					html += Common.toDateTime(o.cs_regdate) + ' / ' + o.member_id + ' / ' + o.cs_task_name + ' ' + o.cs_reason_text + ' / ' + is_confirm + "<br>";
					if(msg) {
						html += msg + "<br>";
					}
					html += "============================================================<br>";
				});

				var strHtml = '<table style="border-top: 1px solid #d2d9df;">' +
					'<colgroup><col width="150" /><col width="*" /></colgroup>' +
					'<tr>' +
					'<th>판매처 상품명</th>' +
					'<td class="text_left">' + rowData.market_product_name + '</td>' +
					'</tr>' +
					'<tr>' +
					'<th>판매처 옵션명</th>' +
					'<td class="text_left">' + rowData.market_product_option + '</td>' +
					'</tr>' +
					'<tr>' +
					'<th>CS 내역</th>' +
					'<td class="text_left">' + html + '</td>' +
					'</tr>' +
					'</table>';

				$("#"+subGridId).html(strHtml);
			}
		}).fail(function(jqXHR, textStatus){
		});


	};

	/**
	 * CS내역조회 목록/검색
	 * @constructor
	 */
	var CSListGridSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * CS내역조회 목록 reload
	 * @constructor
	 */
	var CSListGridReload = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * CS내역조회 엑셀 다운로드
	 * @constructor
	 */
	var CSListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "cs_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_CS_LIST", function(){
				CSList.CSListXlsDownComplete();
			});
		}, 500);
	};

	var CSListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	var CSListConfirmChecked = function(){
		var selRowId = $("#grid_list").getGridParam("selarrrow");

		if(selRowId == null || selRowId.length == 0){
			alert('완료하실 CS 를 선택해주세요.');
			return;
		}

		var idx_list = new Array();
		$.each(selRowId, function(i, o){
			var rowData =$("#grid_list").getRowData(o);
			idx_list.push(rowData.cs_idx);
		});

		if(!confirm('선택하신 CS를 완료 처리하시겠습니까?')){
			return;
		}
		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "set_list_confirm";
		dataObj.cs_idx_list = idx_list;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSListGridReload();
		}).fail(function(jqXHR, textStatus){
		});
	};
	
	/**
	 * CS 남기기 팝업 Open
	 * @constructor
	 */
	var CSPopupCSWritePopOpen = function(){
		var selRowId = $("#grid_list").getGridParam("selarrrow");

		if(selRowId == null || selRowId.length == 0){
			alert('등록하실 CS 를 선택해주세요.');
			return;
		}

		var p_url = "cs_pop_write2.php";
		showLoader();
		$.ajax({
			type: 'GET',
			url: p_url,
			dataType: "html"
		}).done(function (response) {
			if(response)
			{
				$("#modal_order_cs_write").html(response);
				$("#modal_order_cs_write").dialog( "open" );
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
	 * CS 남기기 팝업 Close
	 * @constructor
	 */
	var CSPopupCSWritePopClose = function() {
		$("#modal_order_cs_write").html("");
		$("#modal_order_cs_write").dialog( "close" );
	};

	/**
	 * CS 남기기 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupCSWritePopInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-cs-write-pop-close").on("click", function(){
			CSPopupCSWritePopClose();
		});

		$("#btn-save-order-write").on("click", function(){

			if($.trim($(".commonCsContent").val()) == ""){
				alert("CS 내용을 입력해주세요.");
				return;
			}

			var selRowId = $("#grid_list").getGridParam("selarrrow");

			var idx_list = new Array();
			var idxObj = new Object();
			$.each(selRowId, function(i, o){

				var rowData =$("#grid_list").getRowData(o);

				idxObj = new Object();
				idxObj.order_idx = rowData.order_idx;
				idxObj.order_pack_idx = rowData.order_pack_idx;
				idx_list.push(idxObj);
			});

			if(!confirm('C/S를 남기시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_proc.php";
			var dataObj = new Object();
			dataObj.mode = "selected_insert_cs";
			dataObj.idx_list = idx_list;
			dataObj.comment = $.trim($(".commonCsContent").val());
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {

				if(response.result){

				}else{
					//alert(response.msg);
				}
				CSListGridReload();
				CSPopupCSWritePopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

	};

	/**
	 * CS 일괄 완료 처리
	 * @constructor
	 */
	var CSPopupSetCSConfirmBatch = function(){
		var idx_list = new Array();
		var rowData =$("#grid_list").getRowData();

		$.each(rowData, function(i, o){
			idx_list.push(o.cs_idx);
		});

		if(idx_list.length == 0){
			alert("목록이 없습니다.");
			return;
		}

		if(!confirm('일괄 완료 처리하시겠습니까?')){
			return;
		}

		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "set_list_confirm";
		dataObj.cs_idx_list = idx_list;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSListGridReload();
		}).fail(function(jqXHR, textStatus){
		});
	};

	return {
		CSListInit: CSListInit,
		CSPopupCSWritePopInit: CSPopupCSWritePopInit,
		CSListXlsDownComplete: CSListXlsDownComplete,
	}

})();