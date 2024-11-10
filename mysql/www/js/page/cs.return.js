/*
 * CS 반품관리 js
 */
var CSReturn = (function() {
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
	var CSReturnInit = function(){
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

		//반품통계 버튼 바인딩
		$(".btn-pop-return-statistics").on("click", function(){
			//cs_return_statistics.php
			var search_start_date = $("#period_preset_start_input").val();
			var search_end_date = $("#period_preset_end_input").val();
			var url = '/cs/cs_return_statistics.php?start_date='+search_start_date+'&end_date='+search_end_date;
			Common.newWinPopup(url, 'cs_return_statistics', 1200, 600, 'yes');
		});

		//반품선택완료
		$(".btn-return-confirm-batch").on("click", function(){
			CSReturnConfirmChecked();
		});

		//반품선택완료
		$(".btn-xls-down").on("click", function(){
			CSReturnXlsDown();
		});

		CSReturnGridInit();
	};

	/**
	 * CS내역조회 Grid 초기화
	 * @constructor
	 */
	var CSReturnGridInit = function(){

		//Grid 초기화
		$("#grid_list").jqGrid({
			url: '/cs/cs_return_list_grid.php',
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
				{ label: 'return_idx', name: 'return_idx', index: 'return_idx', width: 0, hidden: true},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 0, hidden: false},
				{ label: 'order_pack_idx', name: 'order_pack_idx', index: 'order_pack_idx', width: 0, hidden: true},
				{ label: 'return_is_confirm_val', name: 'return_is_confirm_val', index: 'return_is_confirm_val', width: 0, hidden: true, formatter: function(cellvalue, options, rowobject){
						return rowobject.return_is_confirm;
					}},
				{ label: '회수요청일', name: 'return_regdate', index: 'return_regdate', width: 180, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '발주일', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 180, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				{ label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 180, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}},
				// { label: '도착일', name: 'receive_date', index: 'receive_date', width: 180, sortable: false, formatter: function (cellvalue, options, rowobject) {
				// 		return Common.toDateTime(cellvalue);
				// 	}},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 150, sortable: false},
				{ label: '구매자', name: 'order_name', index: 'order_name', width: 150, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 200, align: 'left', sortable: false},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 200, align: 'left', sortable: false},
				{ label: '상태', name: 'delivery_status_han', index: 'delivery_status_han', width: 80, align: 'center', sortable: false},

				{ label: '예정', name: 'pay_site', index: 'pay_site', width: 80, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '도착', name: 'paid_site', index: 'paid_site', width: 80, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
						return (rowobject.return_is_confirm == "N") ? '<input type="text" name="paid_site" class="paid_site w40px onlyNumberDynamic" value="'+cellvalue+'" />' : Common.addCommas(cellvalue);
					}},

				{ label: '예정', name: 'pay_pack', index: 'pay_pack', width: 80, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '도착', name: 'paid_pack', index: 'paid_pack', width: 80, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
						return (rowobject.return_is_confirm == "N") ? '<input type="text" name="paid_pack" class="paid_pack w40px onlyNumberDynamic" value="'+cellvalue+'" />' : Common.addCommas(cellvalue);
					}},

				{ label: '예정', name: 'pay_account', index: 'pay_account', width: 80, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '도착', name: 'paid_account', index: 'paid_account', width: 80, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
						return (rowobject.return_is_confirm == "N") ? '<input type="text" name="paid_account" class="paid_account w40px onlyNumberDynamic" value="'+cellvalue+'" />' : Common.addCommas(cellvalue);
					}},

				{ label: '미수', name: 'unpaid_amount', index: 'unpaid_amount', width: 80, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
						return (rowobject.return_is_confirm == "N") ? '<input type="text" name="unpaid_amount" class="unpaid_amount w40px onlyNumberDynamic" value="'+cellvalue+'" />' : Common.addCommas(cellvalue);
					}},

				{ label: '완료처리', name: 'return_is_confirm', index: 'return_is_confirm', width: 60, sortable: true, align: 'center', formatter: function(cellvalue, options, rowobject){
						return (cellvalue == "Y") ? "" : '<a href="javascript:;" class="btn red_btn btn-return-confirm" data-idx="'+rowobject.return_idx+'">완료</a>';
					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'R.return_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: false,
			height: 150,
			multiselect : true,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//상품 선택 버튼 바인딩
				$(".btn-return-confirm").on("click", function(){
					var rowNum = $(this).data("num");

					CSReturnConfirmProc($(this));

				});
			}
		});

		$("#grid_list").jqGrid('setGroupHeaders', {
			useColSpanStyle: true,
			groupHeaders:[
				{startColumnName: 'pay_site', numberOfColumns: 2, titleText: '사이트결제'},
				{startColumnName: 'pay_pack', numberOfColumns: 2, titleText: '동봉'},
				{startColumnName: 'pay_account', numberOfColumns: 2, titleText: '계좌'},
			]
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
				CSReturnGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			CSReturnGridSearch();
		});

	};

	/**
	 * CS내역조회 목록/검색
	 * @constructor
	 */
	var CSReturnGridSearch = function(){
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
	var CSReturnGridReload = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var CSReturnConfirmProc = function($obj){
		var $tr = $obj.parent().parent();

		var return_idx = $obj.data("idx");
		var paid_site = $(".paid_site", $tr).val();
		var paid_pack = $(".paid_pack", $tr).val();
		var paid_account = $(".paid_account", $tr).val();
		var unpaid_amount = $(".unpaid_amount", $tr).val();

		if(paid_site == "") {
			alert("금액을 입력해주세요.");
			return;
		}
		if(paid_pack == "") {
			alert("금액을 입력해주세요.");
			return;
		}
		if(paid_account == "") {
			alert("금액을 입력해주세요.");
			return;
		}
		if(unpaid_amount == "") {
			alert("금액을 입력해주세요.");
			return;
		}

		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "set_return_confirm";
		dataObj.return_idx = return_idx;
		dataObj.paid_site = paid_site;
		dataObj.paid_pack = paid_pack;
		dataObj.paid_account = paid_account;
		dataObj.unpaid_amount = unpaid_amount;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSReturnGridReload();
		}).fail(function(jqXHR, textStatus){
		});
	};

	var CSReturnConfirmChecked = function(){
		var selRowId = $("#grid_list").getGridParam("selarrrow");

		if(selRowId == null || selRowId.length == 0){
			alert('완료처리하실 반품내역을 선택해주세요.');
			return;
		}

		var return_confirm_list = new Array();
		$.each(selRowId, function(i, o){
			var rowData =$("#grid_list").getRowData(o);
			if($.trim(rowData.return_is_confirm_val) == "N") {
				$tr = $("#grid_list #" + o);

				var return_idx = rowData.return_idx;
				var paid_site = $tr.find(".paid_site").val();
				var paid_pack = $tr.find(".paid_pack").val();
				var paid_account = $tr.find(".paid_account").val();
				var unpaid_amount = $tr.find(".unpaid_amount").val();

				if (paid_site == "") {
					paid_site = 0;
				}
				if (paid_pack == "") {
					paid_pack = 0;
				}
				if (paid_account == "") {
					paid_account = 0;
				}
				if (unpaid_amount == "") {
					unpaid_amount = 0;
				}

				var rObj = new Object();
				rObj.return_idx = return_idx;
				rObj.paid_site = paid_site;
				rObj.paid_pack = paid_pack;
				rObj.paid_account = paid_account;
				rObj.unpaid_amount = unpaid_amount;

				return_confirm_list.push(rObj);
			}
		});

		if(!confirm('선택반품완료 처리하시겠습니까?')){
			return;
		}
		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "set_return_list_confirm";
		dataObj.return_confirm_list = return_confirm_list;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSReturnGridReload();
		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * 하부주문관리 엑셀 다운로드
	 * @constructor
	 */
	var CSReturnXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "cs_return_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_CS_RETURN_LIST", function(){
				CSReturn.CSReturnXlsDownComplete();
			});
		}, 500);
	};

	var CSReturnXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	return {
		CSReturnInit: CSReturnInit,
		CSReturnXlsDownComplete: CSReturnXlsDownComplete,
	}

})();