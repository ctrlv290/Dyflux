/*
 * CS 팝업 js
 */
var CSPopup = (function() {
	var root = this;
	var orderTabs = null;

	var init = function() {
	};

	/**
	 * CS 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupInit = function(){
		var wH = $(window).height();
		var findWrap_Height = $(".find_wrap").outerHeight();
		var infoSection_Height = $(".info_wrap_02").outerHeight();

		//var totalH = $(".find_wrap").outerHeight() + $(".list_wrap").outerHeight() + infoSection_Height;
		//console.log(wH-totalH);

		var wrap_splitter_H = wH - (findWrap_Height + infoSection_Height);
		$(".wrap_splitter").height(wrap_splitter_H);

		//console.log(wrap_splitter_H);

		var splitter_position = "50%";
		if(wrap_splitter_H > 490){
			splitter_position = "287px";
		}

		var splitter = $('#wrap_splitter').height(wrap_splitter_H).split({
			orientation: 'horizontal',
			limit: 10,
			position: splitter_position, // if there is no percentage it interpret it as pixels
			onDrag: function(event) {
				$(window).trigger("resize");
			}
		});

		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "8");

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

		//주문생성
		$(".btn-order-write").on("click", function(){
			CSPopupOrderWritePopOpen();
		});

		//주문 생성 모달팝업 세팅
		$( "#modal_order_write" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow(); $( "#modal_order_write" ).html(""); },
		});

		//보류 설정 모달팝업 세팅
		$( "#modal_order_hold" ).dialog({
			width: 600,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow(); $( "#modal_order_hold" ).html(""); },
		});

		//공통 모달팝업 세팅
		$( "#modal_common" ).dialog({
			width: 600,
			autoOpen: false,
			modal: true,
			maxHeight: 820,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow(); $( "#modal_common" ).html(""); },
		});
		$(".ui-dialog").draggable( "option", "containment", false);

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

		CSPopupTabInit();

		CSPopupGridInit();

		CSPopupAlarmInit();

		//CS 이력 바인딩 시작
		
		//제목만 보기 바인딩
		$(".cs_show_only_title").on("click", function(){
			var chk = $(this).is(":checked");
			if(chk){
				$(".cs_list").addClass("only_title");
			}else{
				$(".cs_list").removeClass("only_title");
			}

		});

		//전체보기 바인딩
		$(".cs_show_all").on("click", function(){
			var chk = $(this).is(":checked");
			if(chk){
				$(".cs_list").addClass("auto_show");
			}else{
				$(".cs_list").removeClass("auto_show");
			}
		}).trigger("click");

		//C/S남기기 버튼 바인딩
		$(".btn-cs-write").on("click", function(){
			CSPopupCSWritePopOpen();
		});

		//일괄완료 처리 바인딩
		$(".btn-cs-confirm-batch").on("click", function(){
			CSPopupSetCSConfirmBatch();
		});

		//CS 이력 팝업 바인딩
		$(".btn-cs-history-pop").on("click", function(){
			if(typeof _CSPopupTabCurrentTabOrderIdx == "undefined" || _CSPopupTabCurrentTabOrderIdx == ""){
				alert("주문을 선택해주세요.");
			}else {
				CSHistoryPopupOpen(_CSPopupTabCurrentTabOrderIdx);
			}
		});

		//개별 문자 보내기 바인딩
		$("body").on("click", ".btn-send-sms", function(){
			var url = "/sms/sms_personal_send.php?mobile="+$(this).data("tel")+"&order_idx=" + _CSPopupTabCurrentTabOrderIdx + "&order_pack_idx=" + _CSPopupTabCurrentTabOrderPackIdx;
			Common.newWinPopup(url, 'send_personal_send', 1100, 750, 'yes');
		});
	};

	/**
	 * CS 팝업 페이지 상단 jqGrid 초기화
	 * @constructor
	 */
	var CSPopupGridInit = function(){

		$("#list_top").jqGrid({
			url: './cs_grid.php',
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
				{ label: 'order_progress_step', name: 'order_progress_step', index: 'order_progress_step', width: 80, sortable: true, hidden: true},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 80, sortable: true},

				{ label: '접수일', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTimeOnlyDate(cellvalue);
					}},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 120, sortable: false},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 120, sortable: true, align: 'left', formatter: function(cellvalue, options, rowobject){
						var is_lock = "<i class=\"fas fa-lock fa-red\"></i>";
						var market_order_no = cellvalue;

						if(rowobject.order_is_lock == "Y"){
							market_order_no = is_lock + ' ' + market_order_no;
						}

						return market_order_no;
					}},
				{ label: '주소', name: 'receive_addr', index: 'receive_addr1', width: 350, sortable: true, align: 'left'},
				{ label: '전화', name: 'receive_tp_num', index: 'receive_tp_num', width: 100, sortable: true},
				{ label: '핸드폰', name: 'receive_hp_num', index: 'receive_hp_num', width: 100, sortable: true},
				{ label: '구매자', name: 'order_name', index: 'order_name', width: 80, sortable: true},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 80, sortable: true},
				{ label: '총수량', name: 'product_option_cnt_total', index: 'product_option_cnt_total', width: 80, sortable: true},
				{ label: '상태', name: 'status', index: 'status', width: 60, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
						return Common.convertOrderStatusTextToLabel(rowobject.order_progress_step_han);
					}},
				{ label: 'C/S', name: 'order_cs_status_list', index: 'order_cs_status_list', width: 150, sortable: false, align: 'left', formatter: function(cellvalue, options, rowobject){
						var cs_total_cnt = rowobject.cs_total_cnt;
						var cs_manual_cnt = rowobject.cs_manual_cnt;
						var cs_order_return = rowobject.order_return_request;
						var cs_order_is_return_due = rowobject.order_is_return_due;
						var cs_invoice_priority = rowobject.invoice_priority;
						var cs_order_stock_return = rowobject.order_stock_return;
						return CSPopupOrderProductCSStatusConvert(cellvalue, cs_total_cnt, cs_manual_cnt, cs_order_return, cs_order_is_return_due, cs_invoice_priority, cs_order_stock_return);
					}},
				{ label: '보류', name: 'order_is_hold', index: 'order_is_hold', width: 60, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
						return (cellvalue == 'Y') ? Common.convertOrderStatusTextToLabel("보류") : '';
					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[0],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#pager_top',
			sortname: 'A.order_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResizeWidth("#list_top");

				//상단 목록 선택 고정 시
				if(_CSPopupListTopHoldSelection){
					$('#list_top').jqGrid('setSelection', _CSPopupListTopSelectionNum).trigger("onSelectRow");
					_CSPopupListTopHoldSelection = false;
				}else {
					_CSPopupListTopSelectionNum = 1;
					$('#list_top').jqGrid('setSelection', '1').trigger("onSelectRow");
				}

				if($("#list_top").getGridParam("records") == 0) {
					var nodata_html = '<div class="no-data">검색결과가 없습니다.</div>';
					$(".list_wrap .ui-jqgrid-bdiv").eq(0).append(nodata_html);
				}else{
					$(".list_wrap .no-data").remove();
				}
			},
			onSelectRow: function(rowid, status){
				_CSPopupListTopSelectionNum = rowid;
				var rowData = $("#list_top").getRowData(rowid);
				var order_idx = rowData.order_idx;
				var order_name = rowData.receive_name;
				var order_progress_step = rowData.order_progress_step;
				//console.log(order_progress_step);

				CSPopupTabAdd(order_name, order_idx, order_progress_step);
			}
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeByTargetAndMinusMarginH("#list_top", $("#wrap_splitter_top"), 55);
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
				CSPopupSearchFirstPage();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			CSPopupSearchFirstPage();
		});

		//초기화 버튼 클릭 이벤트
		$(".btn_form_reset").on("click", function(){
			$("#searchForm")[0].reset();
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			//Common.jqGridResizeByTarget("#list_bottom", $("#wrap_splitter_bottom"));
		}).trigger("resize");

	};

	/**
	 * CS 팝업 페이지 상단 목록/검색
	 * @constructor
	 */
	var CSPopupSearch = function(){
		$("#list_top").setGridParam({
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * CS 팝업 페이지 상단 목록/검색
	 * @constructor
	 */
	var CSPopupSearchFirstPage = function(){
		$("#list_top").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 중단 탭 선택 시 세팅 되는 변수들
	 * 현재 선택된 주문 IDX, 합포 IDX, 주문 매칭 IDX, 상품 IDX, 상품 옵션 IDX, 주문 상태
	 */
	var _CSPopupTabCurrentTabOrderIdx = 0;
	var _CSPopupTabCurrentTabOrderPackIdx = 0;
	var _CSPopupTabCurrentTabOrderMatchingIdx = 0;
	var _CSPopupTabCurrentTabOrderProductIdx = 0;
	var _CSPopupTabCurrentTabOrderProductOptionIdx = 0;
	var _CSPopupTabCurrentTabOrderProgressStep = "";
	var _CSPopupTabCurrentTabOrderProductChangeShipped = "";
	var _CSPopupTabCurrentTabOrderCsStatus = "";
	var _CSPopupTabCurrentTabMidRowData = [];

	//탭으로 열린 모든 주문 정보 배열
	var _CSPopupCurrentTabInfoAry = new Object();

	//하단 상세정보 배열
	var _CSPopupCurrentDetailContent = new Object();

	//상단 목록 갱신 시 선택된 Row 를 유지 할 지 여부
	var _CSPopupListTopHoldSelection = false;
	//상단 목록에서 선택 된 Row Num
	var _CSPopupListTopSelectionNum = 0;

	//중단 목록 갱신 시 선택된 Row 를 유지 할 지 여부
	var _CSPopupListMidHoldSelection = false;
	//중단 목록에서 선택 된 Row Num
	var _CSPopupListMidSelectionNum = 0;

	/**
	 * CS 팝업 페이지 중단 탭 기능 초기화
	 * @constructor
	 */
	var CSPopupTabInit = function(){
		orderTabs = $( "#order_detail_tabs" ).tabs({
			activate: function(event, ui){
				//console.log("Active::"+ui.newPanel.data("order_pack_idx"));
				_CSPopupTabCurrentTabOrderPackIdx = ui.newPanel.data("order_pack_idx");
				_CSPopupTabCurrentTabOrderProgressStep = ui.newPanel.data("order_progress_step");
				//탭 Active 시 jqGrid reLoad
				CSPopupTabSearch(_CSPopupTabCurrentTabOrderPackIdx);
			}
		});

		orderTabs.on( "click", "span.ui-icon-close", function() {
			var panelId = $( this ).closest( "li" ).remove().attr( "aria-controls" );
			$( "#" + panelId ).remove();
			orderTabs.tabs( "refresh" );
		});

	};

	/**
	 * CS 팝업 페이지 중단 탭 순번
	 * @type {number}
	 */
	var CSDetailTabId = 0;

	/**
	 * CS 팝업 페이지 중단 탭 추가 함수
	 * @param tabName
	 * @param order_pack_idx
	 * @constructor
	 */
	var CSPopupTabAdd = function(tabName, order_pack_idx, order_progress_step){

		//열린탭 확인
		var $existsTab = $("#order_detail_tabs > ul > li[data-order_pack_idx='"+order_pack_idx+"']");
		if($existsTab.length == 1){

			var tabIndex = $("#order_detail_tabs > ul > li").index($existsTab);
			orderTabs.tabs({ active: tabIndex });

			CSPopupTabSearch(order_pack_idx);

		}else{

			//현재 탭 개수 확인
			var TabCount =  $("#order_detail_tabs ul li").length;
			if(TabCount > 9){
				var tabID = $("#order_detail_tabs > ul > li:first").data("id");
				$("#order_detail_tabs > ul > li:first").remove();
				$("#orderDetailTab" + tabID).remove();
			}

			var id = CSDetailTabId;
			var li = '<li data-id="'+CSDetailTabId+'" data-order_pack_idx="'+order_pack_idx+'" data-order_progress_step="'+1+'"><a href="#orderDetailTab'+id+'">'+tabName+'</a> <span class="ui-icon ui-icon-close" role="presentation">탭 닫기</span></li>';
			orderTabs.find( ".ui-tabs-nav" ).append( li );

			//상단 버튼 셋
			htmlTopBtnSet = '' +
				'<div class="tab_btn_wrap">\n' +
				'<div class="btn_set font_small tab_btn_set_'+order_pack_idx+'">\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn icon-size-m btn-cs-order-hold-toggle"><i class="fas fa-exclamation-circle fa-red"></i>보류설정</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-address-toggle">배송정보변경</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-invoice-toggle">송장관리</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-shipped-toggle">배송처리</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-package-add">합포추가</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-package-except-exec">합포제외</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-package-lock">합포금지</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-cancel-all">전체취소</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-restore-all">전체정상복귀</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-copy">주문복사</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-copy-all">주문전체복사</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-delete">주문삭제</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-priority-change">우선순위</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-return">회수</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-stock-return">재고회수</a>\n' +
				'<a href="javascript:;" data-order_pack_idx="'+order_pack_idx+'" class="xsmall_btn sky_btn btn-cs-order-talk-hold">알림톡 보류</a>' +
				'</div>\n' +
				'</div>';

			//탭내용
			htmlTabContents = '<div class="tb_wrap font_small"><table id="list_bottom_'+order_pack_idx+'" data-order_pack_idx="'+order_pack_idx+'" class="font_small color_table"></table><div id="pager_bottom_'+order_pack_idx+'"></div></div>';

			orderTabs.append( "<div id='orderDetailTab" + id + "' data-order_pack_idx='"+order_pack_idx+"' class='orderDetailTab'>"+htmlTopBtnSet + htmlTabContents+"</div>" );
			orderTabs.tabs( "refresh" );

			var lastTabIndex = $("#order_detail_tabs > ul > li").length - 1;
			orderTabs.tabs({ active: lastTabIndex });

			//탭 생성 시 중단 jqGrid 초기화
			CSPopupTabGridInit(order_pack_idx, order_progress_step);

			CSDetailTabId++;

		}
	};

	/**
	 * CS 팝업 페이지 중단 탭 Grid 초기화
	 * @param order_pack_idx
	 * @constructor
	 */
	var CSPopupTabGridInit = function(order_pack_idx, order_progress_step){

		$("#list_bottom_"+order_pack_idx).jqGrid({
			url: './cs_pack_grid.php',
			mtype: "GET",
			postData:{
				order_pack_idx: order_pack_idx
			},
			datatype: "json",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: function(obj){return obj.length;},
				repeatitems: false,
				id: "idx"
			},
			colModel: [
				{ label: '내부번호', name: 'inner_no', index: 'inner_no', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '매칭IDX', name: 'order_matching_idx', index: 'order_matching_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '자동매칭여부', name: 'order_matching_is_auto', index: 'order_matching_is_auto', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '자동매칭IDX', name: 'matching_info_idx', index: 'matching_info_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '관리번호', name: 'order_idx2', index: 'O.order_idx', width: 80, align: 'center', sortable: true, formatter: function (cellvalue, options, rowobject) {
					return (rowobject.inner_no > 1) ? '' : rowobject.order_idx;
				}},
				{ label: '발주일', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 80, align: 'center', sortable: true, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : Common.toDateTimeOnlyDate(cellvalue);
					}},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 150, sortable: true, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : cellvalue;
					}},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 150, align: 'center', sortable: true, align: 'left', formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : cellvalue;
					}},
				{ label: '개수', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, sortable: true, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return Common.addCommas(cellvalue);
					}},
				{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100,  sortable: true},
				{ label: '<input type="checkbox" id="pack_grid_chk_all_'+order_pack_idx+'" class="chk_package_except_all" data-order_pack_idx="'+order_pack_idx+'" class="cc" onclick="checkBox(event);" />', name: 'chk', index: 'chk', width: 80, align: 'center', sortable: false, formatter: function(cellvalue, options, rowobject){

						var btnz = '';
						btnz = '<div class="div_except_set div_except_set_'+rowobject.order_idx+'" data-order_pack_idx="'+rowobject.order_pack_idx+'" data-order_matching_idx="'+rowobject.order_matching_idx+'">' +
							'<input type="checkbox" class="chk_package_except chk_package_except_'+rowobject.order_idx+'" data-product_option_idx="'+rowobject.product_option_idx+'" data-order_pack_idx="'+rowobject.order_pack_idx+'" data-order_idx="'+rowobject.order_idx+'" data-order_matching_idx="'+rowobject.order_matching_idx+'" data-order_cs_status="'+rowobject.order_cs_status+'" data-order_progress_step="'+rowobject.order_progress_step+'" data-product_change_shipped="'+rowobject.product_change_shipped+'" >' +
							' <input type="text" class="onlyNumberDynamic w30px dis_none input_package_except input_package_except_'+rowobject.order_matching_idx+'" data-product_option_idx="'+rowobject.product_option_idx+'" data-order_matching_idx="'+rowobject.order_matching_idx+'" data-maxlength="3" value="'+rowobject.product_option_cnt+'" disabled="disabled"/>' +
							'<input type="hidden" name="product_option_cnt_remain" class="product_option_cnt_remain product_option_cnt_remain_'+rowobject.order_matching_idx+'" data-order_pack_idx="'+rowobject.order_pack_idx+'" data-product_option_cnt="'+rowobject.product_option_cnt+'"  data-order_matching_idx="'+rowobject.order_matching_idx+'" value="'+rowobject.product_option_cnt+'" />' +
							'</div>';
						return btnz;

					}},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 200, align: 'left', sortable: true, formatter: function(cellvalue, options, rowobject){
					var rst = $.jgrid.htmlEncode(cellvalue);
					if(rowobject.is_gift == 'Y'){
						rst = '<span class="lb_black">사은품</span> ' + rst;
					}
					return rst;
					}},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 200,  align: 'left', sortable: true},
				{ label: '가격', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 100, align: 'right', sortable: true, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return Common.addCommas(cellvalue * rowobject.product_option_cnt);
					}},
				{ label: '재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 100, align: 'right', sortable: true, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return Common.addCommas(cellvalue);
					}},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: true},
				{ label: '품절', name: 'product_option_soldout', index: 'product_option_soldout', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return (cellvalue == 'Y') ? '품절' : '-';
					}},
				{ label: '일시품절', name: 'product_option_soldout_temp', index: 'product_option_soldout_temp', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return (cellvalue == 'Y') ? '일시품절' : '-';
					}},
				{ label: '상태', name: 'status', index: 'status', width: 100, align: 'center', sortable: false, formatter: function(cellvalue, options, rowobject) {
						if(rowobject.order_cs_status_han == '취소'){
							return '';
						}else {
							return Common.convertOrderStatusTextToLabel(rowobject.order_progress_step_han);
						}
					}
				},
				{ label: 'C/S', name: 'cs', index: 'cs', width: 100, align: 'center', sortable: false, formatter: function(cellvalue, options, rowobject) {
						return CSPopupOrderCSStatusConvert(rowobject.order_cs_status_han);
					}
				},
				{ label: 'order_cs_status', name: 'order_cs_status', index: 'order_cs_status', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: 'order_progress_step', name: 'order_progress_step', index: 'order_progress_step', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: 'product_change_shipped', name: 'product_change_shipped', index: 'product_change_shipped', width: 0, align: 'center', sortable: false, hidden: true},
			],
			viewrecords: true,
			rowNum:100,
			rowList:[],
			pager: '#pager_bottom_'+order_pack_idx,
			viewrecords: true,
			sortname: 'O.order_idx',
			sortorder: "asc",
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			loadComplete: function(data){
				Common.jqGridResizeByTargetAndMinusMarginH("#list_bottom_"+order_pack_idx, $("#wrap_splitter_bottom"), 110);

				//체크박스 바인딩
				$("#pack_grid_chk_all_"+order_pack_idx).on("click", function(){
					//($(this).data("order_pack_idx"));
				});

				$.each(data.rows, function(i, o){
					_CSPopupCurrentTabInfoAry[o.order_idx] = o;
				});

				//중단 탭 row 데이터
				_CSPopupTabCurrentTabMidRowData = data.rows;

				//선택 영역 고정 시
				if(_CSPopupListMidHoldSelection) {
					$('#list_bottom_' + order_pack_idx).jqGrid('setSelection', _CSPopupListMidSelectionNum).trigger("onSelectRow");
					_CSPopupListMidHoldSelection = false;
				}else {
					_CSPopupListMidSelectionNum = 1;
					//Row 선택 시 바인딩 - 주문 상세 정보
					$('#list_bottom_' + order_pack_idx).jqGrid('setSelection', '1').trigger("onSelectRow");
				}
			},
			onSelectRow: function(rowid, status){

				_CSPopupListMidSelectionNum = rowid;
				
				var rowData = $('#list_bottom_'+order_pack_idx).getRowData(rowid);
				var order_idx = rowData.order_idx;
				var order_matching_idx = rowData.order_matching_idx;
				var product_idx = rowData.product_idx;
				var product_option_idx = rowData.product_option_idx;
				var order_progress_step = rowData.order_progress_step;
				var order_cs_status =  rowData.order_cs_status;
				var product_change_shipped =  rowData.product_change_shipped;

				//체크 박스 클릭 시
				$("#list_bottom_"+order_pack_idx+" .chk_package_except").on("click", function(){
					var chk = $(this).is(":checked");
					var idx = $(this).data("order_matching_idx");

					$(".input_package_except_"+idx).prop("disabled", !chk);//.val("1");
					if(chk){
						$(".input_package_except_"+idx).removeClass("dis_none");
					}else{
						$(".input_package_except_"+idx).addClass("dis_none");
					}
				});

				//헤더 체크 박스 클릭 시
				$("#pack_grid_chk_all_"+order_pack_idx).on("click", function(){
					$("#list_bottom_"+order_pack_idx+" .chk_package_except").trigger("click");
				});

				_CSPopupTabCurrentTabOrderIdx = order_idx;
				_CSPopupTabCurrentTabOrderMatchingIdx = order_matching_idx;
				_CSPopupTabCurrentTabOrderProductIdx = product_idx;
				_CSPopupTabCurrentTabOrderProductOptionIdx = product_option_idx;
				_CSPopupTabCurrentTabOrderProductIdx = product_idx;
				_CSPopupTabCurrentTabOrderProductOptionIdx = product_option_idx;
				_CSPopupTabCurrentTabOrderProgressStep = order_progress_step;
				_CSPopupTabCurrentTabOrderCsStatus = order_cs_status;
				_CSPopupTabCurrentTabOrderProductChangeShipped = product_change_shipped

				CSPopupDetailView(order_pack_idx, order_idx, product_option_idx);
			}
		});

		$("#pager_bottom_"+order_pack_idx+"_left").width(0);

		//하단 버튼 SET
		var btn_bottom_html = '<div class="tab_btn_bottom_set tab_btn_set_'+order_pack_idx+'">' +
			'<a href="javascript:;" class="small_btn sky_btn">다운로드</a>\n' +
			'<a href="javascript:;" class="small_btn sky_btn btn-cs-order-cancel-one">개별취소</a>\n' +
			'<a href="javascript:;" class="small_btn sky_btn btn-cs-order-restore-one">개별정상복귀</a>\n' +
			'<a href="javascript:;" class="small_btn sky_btn btn-cs-product-change">상품교환</a>\n' +
			'<a href="javascript:;" class="small_btn sky_btn btn-cs-product-add">상품추가</a>\n' +
			'<a href="javascript:;" class="small_btn sky_btn btn-cs-matching-delete">매칭삭제</a>\n' +
			'<a href="javascript:;" class="small_btn sky_btn btn-tracking">화물추적</a>\n' +
			'<a href="javascript:;" class="small_btn sky_btn btn-cs-return-due">반품예정</a>' +
			'</div>';

		//하단 버튼 append
		$("#pager_bottom_"+order_pack_idx+"_right").prepend(btn_bottom_html);


		//상단 버튼 바인딩 - 시작

		//주문 보류
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-hold-toggle").on("click", function(){
			CSPopupOrderHold();
		});
		//배송정보변경
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-address-toggle").on("click", function(){
			CSPopupAddressChangeOpen();
		});
		//송장관리
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-invoice-toggle").on("click", function(){
			CSPopupInvoiceChangeOpen();
		});
		//배송처리
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-shipped-toggle").on("click", function(){
			CSPopupShippedChangeOpen();
		});
		//합포추가
		$(".tab_btn_set_"+order_pack_idx+" .btn-package-add").on("click", function(){
			CSPopupPackageAddOpen();
		});
		//합포제외
		$(".tab_btn_set_"+order_pack_idx+" .btn-package-except-exec").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			OrderPackageExceptExecOne(_order_pack_idx);
		});
		//합포금지
		$(".tab_btn_set_"+order_pack_idx+" .btn-package-lock").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupPackageLockOpen(_order_pack_idx);
		});
		//전체취소
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-cancel-all").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupOrderCancelAllOpen(_order_pack_idx);
		});
		//전체정상복귀
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-restore-all").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupOrderRestoreAllOpen(_order_pack_idx);
		});


		//주문복사
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-copy").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupOrderCopyOneOpen(_order_pack_idx);
		});
		//주문전체복사
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-copy-all").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupOrderCopyWholeOpen(_order_pack_idx);
		});
		//주문삭제
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-delete").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupOrderDeleteOpen(_order_pack_idx);
		});
		//우선순위
		$(".tab_btn_set_"+order_pack_idx+" .btn-priority-change").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupInvoicePriorityOpen(_order_pack_idx);
		});
		//회수
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-return").on("click", function(){
			CSPopupReturnOpen("ORDER");
		});
		//재고회수
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-stock-return").on("click", function(){
			CSPopupReturnOpen("STOCK");
		});


		//알림톡 보류
		//다운로드
		//개별취소
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-cancel-one").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupOrderCancelOneOpen(_order_pack_idx);
		});
		//개별정상복귀
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-order-restore-one").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupOrderRestoreOneOpen(_order_pack_idx);
		});
		//상품교환
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-product-change").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupProductChangeOpen(_order_pack_idx);
		});
		//상품추가
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-product-add").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupProductAddOpen(_order_pack_idx);
		});
		//매칭삭제
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-matching-delete").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupMatchingDeleteOpen(_order_pack_idx);
		});
		//화물추적
		$(".tab_btn_set_"+order_pack_idx+" .btn-tracking").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupTracking(_order_pack_idx);
		});
		//반품예정
		$(".tab_btn_set_"+order_pack_idx+" .btn-cs-return-due").on("click", function(){
			var _order_pack_idx = order_pack_idx;
			CSPopupOrderReturnDueOpen(_order_pack_idx);
		});

		//상단 버튼 바인딩 - 끝

		//하단 버튼 바인딩 - 시작
		//하단 버튼 바인딩 - 끝

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeByTargetAndMinusMarginH("#list_bottom_"+order_pack_idx, $("#wrap_splitter_bottom"), 110);
		}).trigger("resize");
	};

	/**
	 * CS 팝업 페이지 중단 목록/검색
	 * @constructor
	 */
	var CSPopupTabSearch = function(order_pack_idx){
		$("#list_bottom_"+order_pack_idx).setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				order_pack_idx: order_pack_idx
			}
		}).trigger("reloadGrid");
	};

	/**
	 * CS 팝업 페이지 중단 탭 삭제 합수
	 * @constructor
	 */
	var CSPopupTabRemove = function(){};

	/**
	 * CS 팝업 페이지 중단 탭 활성화 함수
	 * @param event
	 * @param ui
	 * @constructor
	 */
	var CSPopupTabActivate = function(event, ui){

		// 생성된 탭 활성화..
		var tabNo = ui.newTab.index();

		var tab_id = $("#order_detail_tabs ul>li a").eq(tabNo).attr("href");
		selected_tab_jqgrid_id = "#" + $(tab_id).find(".ui-jqgrid-btable").attr("id");

		var selrow = $(selected_tab_jqgrid_id).getGridParam( "selrow" );

		order_info_all = order_info_tab[tab_id.replace('#','')];
		loadDetail(order_info_all, selrow);

	};

	/**
	 * CS 팝업 페이지 - 주문 정보 상세 내역 반환 함수
	 * @param order_idx
	 * @param product_option_idx
	 * @constructor
	 */
	var CSPopupDetailView = function(order_pack_idx, order_idx, product_option_idx){
		var p_url = "/cs/cs_detail_view.php";
		var dataObj = new Object();
		dataObj.order_idx = order_idx;
		dataObj.product_option_idx = product_option_idx;

		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			$("#CSDetailView").html(response);
		}).fail(function(jqXHR, textStatus){
		});


		//CS 이력 가져오기
		CSPopupGetCSList(order_pack_idx);
	};

	/**
	 * CS 이력 가져오기
	 * @param order_pack_idx
	 * @constructor
	 */
	var CSPopupGetCSList = function(order_pack_idx) {

		//CS 이력 내용 비우기
		$("#cs_list").empty();

		//이력 가져오기
		p_url = "/cs/cs_cs_list_ajax.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = order_pack_idx;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result){
				$.each(response.data, function(i, o){
					//console.log(o);
					var msg = o.cs_comment.replace(/\n/g, '<br>');
					var is_auto_cs = (o.cs_is_auto == "Y") ? "auto_hide" : "";

					var is_confirm = (o.cs_confirm == "Y") ? '<span class="span_red">완료</span>' : '<a href="javascript:;" class="link_default btn-cs-one-confirm" data-idx="'+o.cs_idx+'">미처리</a>';

					var has_file = false;
					var file_list = [];

					if(o.cs_file_idx1 > 0){
						has_file = true;
						file_list.push(o.cs_file_idx1 + '|' + o.filename1);
					}
					if(o.cs_file_idx2 > 0){
						has_file = true;
						file_list.push(o.cs_file_idx2 + '|' + o.filename2);
					}
					if(o.cs_file_idx3 > 0){
						has_file = true;
						file_list.push(o.cs_file_idx3 + '|' + o.filename3);
					}
					if(o.cs_file_idx4 > 0){
						has_file = true;
						file_list.push(o.cs_file_idx4 + '|' + o.filename4);
					}
					if(o.cs_file_idx5 > 0) {
						has_file = true;
						file_list.push(o.cs_file_idx5 + '|' + o.filename5);
					}

					var html = '' +
						'<div class="cs_cm_box ' + is_auto_cs + '">\n' +
						'<div class="row">\n' +
						'<div class="field_box">'+Common.toDateTime(o.cs_regdate)+'</div>\n' +
						'<div class="field_box">작업자 : '+o.name+'('+ o.member_id +')</div>\n' +
						'<div class="field_box bold blue">'+o.cs_task_name + ' ' + o.cs_reason_text +'</div>\n' +
						'<div class="field_box fr">'+is_confirm+'</div>\n' +
						'</div>\n' +
						'<div class="row">\n' +
						'<div class="field_box">[관리번호 : '+o.order_idx+' ('+o.order_pack_idx+')]' +
						' <a href="javascript:;" class="btn-cs-one-delete" data-idx="'+o.cs_idx+'"><i class="far fa-trash-alt"></i></a>' +
						'</div>\n' +
						'</div>\n' +
						'<div class="row">\n' +
						'<div class="field_box">'+msg+'</div>\n' +
						'</div>\n';
					if(has_file) {
						html += '<div class="row">\n';
						$.each(file_list, function(ii, oo){

							var o_ary = oo.split("|");
							var file_idx = o_ary[0];
							var user_filename = o_ary[1];
							var save_filename = o_ary[2];

							var extension = user_filename.substr( (user_filename.lastIndexOf('.') +1) ).toLocaleLowerCase();

							var link_class = "cs_file_down";
							var is_img = false;
							var link_text = user_filename;
							var href_url = "javascript:;";
							var lightbox_attr = "";

							if(extension == "png" || extension == "jpg" || extension == "jpeg" || extension == "gif"){
								is_img = true;
								link_class = "cs_img_thumb";
								link_text = "";
								href_url = "/_data/cs/"+save_filename;
								lightbox_attr = 'data-lightbox="cs_img_thumb_' + o.cs_idx + '"';
							}

							html += '<div class="file_box"><a href="'+href_url+'" '+lightbox_attr+' data-file_idx="'+file_idx+'" data-filename="'+save_filename+'" class="link_default ' + link_class + '">' + user_filename + '</a></div>\n';
						});
						html += '</div>\n';
					}
					html += '</div>';
					$("#cs_list").append(html);
				});

				//CS 내역 첨부파일 이미지 썸네일 보기
				CSPopupCSListImgThumb();

				lightbox.option({
					'resizeDuration': 100,
					'fadeDuration': 200,
					'imageFadeDuration': 200,
					'albumLabel': "첨부파일 이미지 %1/%2",
				})

				//CS 내역 첨부파일 다운로드 바인딩
				$(".cs_file_down").on("click", function(){
					Common.simpleUploadedFileDown($(this).data("file_idx"), $(this).data("filename"));
				});


				//미처리 버튼 바인딩
				$(".btn-cs-one-confirm").on("click", function(){
					CSPopupSetCSConfirm($(this).data("idx"));
				});

				//삭제 버튼 바인딩
				$(".btn-cs-one-delete").on("click", function(){
					CSPopupDeleteCS($(this).data("idx"));
				});
			}
		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * 이미지 리스트에서 썸네일 보기
	 * @constructor
	 */
	var CSPopupCSListImgThumb = function(){
		$(".cs_img_thumb").each(function(i, o) {
			var p_url = "/proc/_thumbnail.php";
			var dataObj = new Object();
			dataObj.file_idx = $(o).data("file_idx");
			dataObj.save_filename = $(o).data("filename");
			dataObj.width = 36;
			dataObj.height = 36;
			dataObj.is_crop = "Y";
			dataObj.force_create = "N";

			$.ajax({
				type: 'GET',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					//console.log(response);
					$(o).html('<img src="' + response.thumb.src + '" />');
				} else {
					//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				//alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});
	};

	var CSPopupOrderProductCSStatusConvert = function(order_cs_status_list, cs_total_cnt, cs_manual_cnt, cs_order_return, cs_order_is_return_due, cs_invoice_priority, cs_order_stock_return){

		var rst = "";
		var tmpRst = [];
		var tmp = "";
		//var arr = order_cs_status_list.split(',');
		var partialCancel = false;  //부분취소 여부

		if(order_cs_status_list == null || typeof order_cs_status_list == null){
			return "";
		}

		//정상이 있으면 ADD
		if(order_cs_status_list.indexOf('정상') > -1){
			tmpRst.push('<span class="lb_small lb_sky">정상</span>');
		}

		//교환이 있으면 ADD
		if(order_cs_status_list.indexOf('교환') > -1){
			tmpRst.push('<span class="lb_small lb_blue2">교환</span>');
		}

		//취소는 전체 인지 부분인지 분리
		if(order_cs_status_list.indexOf('취소') > -1){
			//console.log(tmpRst.length);
			if(tmpRst.length == 0){
				//이미 추가된게 없으면 전체 취소
				tmpRst.push('<span class="lb_small lb_red2">취소</span>');
			}else {

				//이미 추가된 CS가 있으면 부분 취소
				tmpRst.push('<span class="lb_small lb_orange2">부분취소</span>');

			}
		}


		//자동입력된 CS 이외에 추가한 CS가 있으면 표시
		if(Number(cs_total_cnt) > 0){
			if(Number(cs_manual_cnt) > 0){
				tmpRst.push('<span class="lb_small lb_red2">CS</span>');
			}else{
				tmpRst.push('<span class="lb_small lb_blue2">CS</span>');
			}

		}

		//회수요청 확인
		if(cs_order_return == "Y"){
			tmpRst.push('<span class="lb_small lb_brown">회수</span>');
		}

		//반품예정 확인
		if(cs_order_is_return_due == "Y"){
			tmpRst.push('<span class="lb_small lb_green2">반품예정</span>');
		}

		//우선순위 설정 확인
		if(cs_invoice_priority > 0){
			tmpRst.push('<i class="fas fa-star color_yellow2"></i>');
		}
		

		//재고회수 확인
		if(cs_order_stock_return == "Y"){
			tmpRst.push('<span class="lb_small lb_brown">재고회수</span>');
		}

		rst = tmpRst.join(" ");

		return rst;
	};

	var CSPopupOrderCSStatusConvert = function(order_cs_status_han){
		//console.log(order_cs_status_han);
		if(order_cs_status_han != null) {
			if (order_cs_status_han.indexOf('정상') > -1) {
				return '<span class="lb_small lb_sky">정상</span>';
			} else if (order_cs_status_han.indexOf('교환') > -1) {
				return '<span class="lb_small lb_blue2">교환</span>';
			} else if (order_cs_status_han.indexOf('취소') > -1) {
				return '<span class="lb_small lb_red2">취소</span>';
			}
		}else{
			return '';
		}
	};

	/**
	 * CS 팝업 페이지 - 주문 생성 모달 팝업 Open
	 * @constructor
	 */
	let CSPopupOrderWritePopOpen = function(){
		showLoader();
		$.ajax({
			type: 'POST',
			url: 'cs_order_write.php',
			dataType: "html",
			data: {}
		}).done(function (response){
			if(response){
				$("#modal_order_write").html(response);
				$("#modal_order_write").dialog( "open" );
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
		}).always(function(){
			hideLoader();
		});
	};

	/**
	 * CS 팝업 페이지 - 주문 생성 모달 팝업 Close
	 * @constructor
	 */
	let CSPopupOrderWritePopClose = function() {
		$("#modal_order_write").html("");
		$("#modal_order_write").dialog( "close" );
	};

	let _CSPopupOrderWriteIng = false;
	/**
	 * CS 팝업 페이지 - 주문 생성 팝업 페이지 초기화
	 * @constructor
	 */
	let CSPopupOrderWriteInit = function() {

		//창 닫기 버튼 바인딩
		$(".btn-cs-order-write-pop-close").on("click", function(){
			CSPopupOrderWritePopClose();
		});

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".cs_order_write_popup .product_seller_group_idx", ".cs_order_write_popup .seller_idx");
		$(".cs_order_write_popup .seller_idx").SumoSelect({
			placeholder: '판매처를 선택해주세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//상품 검색 팝업
		$(".btn-product-search-pop").on("click", function(){
			CSPopupOrderWriteProductAddPopup()
		});

		//판매단가 Update
		$("#newOrderForm input[name='product_option_cnt'], #newOrderForm input[name='order_amt']").on("keyup", function(){

			let product_option_cnt = $("#newOrderForm input[name='product_option_cnt']").val();
			let order_amt = $("#newOrderForm input[name='order_amt']").val();

			if(product_option_cnt == "") product_option_cnt = 0;
			if(order_amt == "") order_amt = 0;

			let product_sale_price = 0;
			if(order_amt > 0 && product_option_cnt > 0){
				product_sale_price = Math.floor(order_amt / product_option_cnt);
			}
			$("#newOrderForm input[name='product_sale_price']").val(product_sale_price);

		});

		//최근정보사용 Update
		CSPopupOrderWriteGetLatestInfo();

		//구매자 정보와 동일하게 설정 버튼 바인딩
		$(".btn_copy_from_buyer").on("click", function(){
			$("input[name='receive_name']").val($("input[name='order_name']").val());
			$("input[name='receive_tp_num']").val($("input[name='order_tp_num']").val());
			$("input[name='receive_hp_num']").val($("input[name='order_hp_num']").val());
			$("input[name='receive_zipcode']").val($("input[name='order_zipcode']").val());
			$("input[name='receive_addr1']").val($("input[name='order_addr1']").val());
			$("input[name='receive_addr2']").val($("input[name='order_addr2']").val());
		});


		//저장 버튼
		$("#btn-save-order").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if(!_CSPopupOrderWriteIng) {
				$("form[name='dyForm2']").submit();
			}
		});

		//폼 Submit 이벤트
		$("form[name='dyForm2']").submit(function(e){
			e.preventDefault();
			let returnType = false;        // "" or false;
			let valForm = new FormValidation();
			let objForm = this;

			try{


				if (!valForm.chkValue(objForm.seller_idx, "판매처를 선택해주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_idx, "상품 검색 버튼을 이용하여 상품을 선택해 주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_idx, "상품 검색 버튼을 이용하여 상품을 선택해 주세요.", 1, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.product_option_cnt, "수량을 정확히 입력해주세요.", 1, 10, RegexpPattern.number)) return returnType;
				if (!valForm.chkValue(objForm.order_amt, "판매금액을 정확히 입력해주세요.", 1, 10, RegexpPattern.number)) return returnType;
				if (!valForm.chkValue(objForm.order_name, "구매자를 정확히 입력해주세요.", 1, 20, null)) return returnType;
				if (!valForm.chkValue(objForm.receive_name, "수령자를 정확히 입력해주세요.", 1, 20, null)) return returnType;
				if (!valForm.chkValue(objForm.receive_hp_num, "수령자 휴대폰번호를 정확히 입력해주세요.", 1, 20, null)) return returnType;
				if (!valForm.chkValue(objForm.receive_zipcode, "수령자 주소를 정확히 입력해주세요.", 5, 5, null)) return returnType;
				if (!valForm.chkValue(objForm.receive_addr1, "수령자 주소를 정확히 입력해주세요.", 1, 200, null)) return returnType;

				_CSPopupOrderWriteIng = true;

				showLoader();

				$.ajax({
					type: 'POST',
					url: 'cs_order_proc.php',
					dataType: "json",
					data: $("form[name='dyForm2']").serialize()
				}).done(function (response) {
					if(response.result) {
						alert('저장되었습니다.');
						CSPopupOrderWritePopClose();
					}else{
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}).fail(function(jqXHR, textStatus){
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}).always(function(){
					hideLoader();
					_CSPopupOrderWriteIng = false;
				});

				return false;

			}catch(e){
				alert(e);
				_CSPopupOrderWriteIng = false;
				return false;
			}
		});
	};

	var CSPopupOrderWriteGetLatestInfoAry = {
		0 : {
			receive_name : "",
			receive_tp_num : "",
			receive_hp_num : "",
			receive_zipcode : "",
			receive_addr1 : "",
			receive_addr2 : "",
		}
	};
	var CSPopupOrderWriteGetLatestInfo = function(){

		//사용안함 버튼 기본추가
		$(".set_latest_shipping_info").append('<a href="javascript:;" class="btn btn_latest_shipping_info" data-index="0">초기화</a> ');

		var p_url = "/cs/cs_order_proc.php";
		var dataObj = new Object();
		dataObj.mode = "get_latest_shipping_info";
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {

			if(response.result){
				$.each(response.data, function(i, o){
					CSPopupOrderWriteGetLatestInfoAry[(i+1)] = o;
					$(".set_latest_shipping_info").append('<a href="javascript:;" class="btn blue_btn btn_latest_shipping_info" data-index="'+(i+1)+'">'+o.receive_name+'</a> ');
				});

				$(".btn_latest_shipping_info").on("click", function(e){
					e.preventDefault ? e.preventDefault() : (e.returnValue = false);

					var idx = $(this).data("index");
					var obj = CSPopupOrderWriteGetLatestInfoAry[idx];
					$("input[name='receive_name']").val(obj.receive_name);
					$("input[name='receive_tp_num']").val(obj.receive_tp_num);
					$("input[name='receive_hp_num']").val(obj.receive_hp_num);
					$("input[name='receive_zipcode']").val(obj.receive_zipcode);
					$("input[name='receive_addr1']").val(obj.receive_addr1);
					$("input[name='receive_addr2']").val(obj.receive_addr2);

				});
			}else{
				//alert(response.msg);
			}
			CSPopupCommonPopClose();

			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * CS 팝업 페이지 - 주문 생성 - 상품 검색 팝업 Open
	 * @constructor
	 */
	var CSPopupOrderWriteProductAddPopup = function(){
		Common.newWinPopup("cs_product_search_pop.php", 'cs_product_search_pop', 1000, 720, 'yes');
	};

	/**
	 * CS 팝업 페이지 - 주문 생성 - 상품 검색 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupOrderWriteProductAddPopupInit = function(){
		//Grid 초기화
		$("#grid_list_pop").jqGrid({
			url: '/cs/cs_product_search_pop_grid.php',
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
					label: '선택', name: '선택', width: 62, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return '<a href="javascript:;" class="xsmall_btn btn-product-select" data-idx="' + rowobject.product_idx + '" data-num="' + options.rowId+ '">선택</a>';
					}
				},
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
				{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, is_use : true},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 140, sortable: true},
				{ label: '판매타입', name: 'code_name', index: 'code_name', width: 100, sortable: true},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 110, sortable: true},
				{ label: '판매가(A)', name: 'product_option_sale_price_A', index: 'product_option_sale_price_A', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '판매가(B)', name: 'product_option_sale_price_B', index: 'product_option_sale_price_B', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '판매가(C)', name: 'product_option_sale_price_C', index: 'product_option_sale_price_C', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '판매가(D)', name: 'product_option_sale_price_D', index: 'product_option_sale_price_D', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '판매가(E)', name: 'product_option_sale_price_E', index: 'product_option_sale_price_E', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '공급업체', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, align: 'left'},
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
					CSPopupOrderWriteProductAddPopupSelect(product_idx, product_option_idx, product_name, product_option_name);
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
				CSPopupOrderWriteProductAddPopupSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_pop").on("click", function(){
			CSPopupOrderWriteProductAddPopupSearch();
		});
	};

	/**
	 * CS 팝업 페이지 - 주문 생성 - 상품 검색 팝업 목록/검색
	 * @constructor
	 */
	var CSPopupOrderWriteProductAddPopupSearch = function(){

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
			url: '/cs/cs_product_search_pop_grid.php',
			postData:{
				param: $("#searchFormPop").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * CS 팝업 페이지 - 주문 생성 - 상품 검색 팝업 - 상품 선택
	 * opener 를 검사 후 opener 의 상품 입력 함수 실행
	 * @param product_idx
	 * @param product_option_idx
	 * @param product_name
	 * @param product_option_name
	 * @constructor
	 */
	var CSPopupOrderWriteProductAddPopupSelect = function(product_idx, product_option_idx, product_name, product_option_name){
		var openerName = window.opener &&
			window.opener.document &&
			window.opener.name;
		if (openerName == "cs_pop") {

			window.opener.CSPopup.CSPopupOrderWriteProductSelect(product_idx, product_option_idx, product_name, product_option_name)
			self.close();
		}else{

		}
	};

	/**
	 * CS 팝업 페이지 - 주문 생성 - 상품 정보 입력 함수
	 * @param product_idx
	 * @param product_option_idx
	 * @param product_name
	 * @param product_option_name
	 * @constructor
	 */
	var CSPopupOrderWriteProductSelect = function(product_idx, product_option_idx, product_name, product_option_name){
		$("form[name='dyForm2'] input[name='product_idx']").val(product_idx);
		$("form[name='dyForm2'] input[name='product_option_idx']").val(product_option_idx);
		//$(".td_product_name").text(product_name);
		//$(".td_product_option_name").text(product_option_name);
		$("form[name='dyForm2'] input[name='product_name']").val(product_name);
		$("form[name='dyForm2'] input[name='product_option_name']").val(product_option_name);
	};

	/**
	 * CS 팝업 페이지 - 버튼 - 보류설정 팝업 열기
	 * @constructor
	 */
	var CSPopupOrderHold = function(){
		//현재 상태가 배송일 경우 변경 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_SHIPPED") {
			alert("현재 상태가 배송이면, 사용할 수 없습니다.");
			return;
		}

		var p_url = "cs_pop_order_hold.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				$("#modal_order_hold").html(response);
				$("#modal_order_hold").dialog( "open" );
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
	 * CS 팝업 페이지 - 보류 설정 팝업 닫기
	 * @constructor
	 */
	var CSPopupOrderHoldClose = function(){
		$("#modal_order_hold").html("");
		$("#modal_order_hold").dialog( "close" );
	};

	/**
	 * CS 팝업 페이지 - 보류 설정 팝업 페이지 초기화
	 * @constructor
	 */
	var CSpopupOrderHoldInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-order-hold-pop-close").on("click", function(){
			CSPopupOrderHoldClose();
		});

		//알람 일자 datepicker 설정
		Common.setDatePickerForDynamicElement($(".cs_order_hold_popup .jqDateDynamic"));

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-save-order-hold").on("click", function(){

			var save_text = $("input[name='save-text']").val();

			if(!confirm(save_text + ' 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					/*
					if(typeof response.order_pack_idx != "undefined") {
						var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
						$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					}
					*/
				}else{
					alert(response.msg);
				}
				CSPopupOrderHoldClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

	};

	/**
	 * CS 팝업 페이지 - 배송정보변경 팝업 오픈
	 * @constructor
	 */
	var CSPopupAddressChangeOpen = function(){

		//현재 상태가 송장, 배송일 경우 변경 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_INVOICE"
		|| _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_SHIPPED") {
			alert("현재 상태가 송장, 배송이면 사용할 수 없습니다.");
			return;
		}

		var p_url = "cs_pop_address_change.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				CSPopupCommonPopOpen(600, 0, "배송정보변경", response);
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
	 * CS 팝업 페이지 - 배송정보변경 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupAddressChangeInit = function(){

		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-save-address").on("click", function(){

			if(!confirm('배송정보를 변경 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {

				if(response.result){
					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					if(typeof response.order_pack_idx != "undefined") {
						var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
						$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					}
				}else{
					alert(response.msg);
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 - 송장관리 팝업 오픈
	 * @constructor
	 */
	var CSPopupInvoiceChangeOpen = function(){
		//보류 상태 주문은 배송처리 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_is_hold == "Y"){
			alert("보류상태의 주문입니다.");
			return;
		}

		//현재 상태가 접수, 송장일 경우에만 가능
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_ACCEPT"
		&& _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_INVOICE") {
			alert("현재 상태가 접수, 송장일 경우에만 가능합니다.");
			return;
		}

		var p_url = "cs_pop_invoice_change.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				CSPopupCommonPopOpen(600, 0, "송장관리", response);
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
	 * CS 팝업 페이지 - 송장관리 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupInvoiceChangeInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-save-address").on("click", function(){

			if($(".cs_order_common_popup input[name='invoice_no']").length == 1){
				if($.trim($(".cs_order_common_popup input[name='invoice_no']").val()) == ""){
					alert("송장번호를 입력해주세요.");
					return;
				}
			}

			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('송장정보를 ' + confirm_text + ' 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					/*
					if(typeof response.order_pack_idx != "undefined") {
						var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
						$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					}
					*/
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 - 배송처리 팝업 오픈
	 * @constructor
	 */
	var CSPopupShippedChangeOpen = function(){
		//보류 상태 주문은 배송처리 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_is_hold == "Y"){
			alert("보류상태의 주문입니다.");
			return;
		}
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_cs_status == "ORDER_CANCEL"){
			alert("취소상태의 주문입니다.");
			return;
		}
		//현재 상태가 접수 상태일 경우 변경 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_INVOICE"
		&& _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_SHIPPED") {
			alert("현재 상태가 송장, 배송일 경우에만 가능합니다.");
			return;
		}

		var p_url = "cs_pop_shipped_change.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				CSPopupCommonPopOpen(600, 0, "배송처리", response);
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
	 * CS 팝업 페이지 - 배송처리 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupShippedChangeInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-save-address").on("click", function(){

			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {

				if(response.result){
					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 - 합포추가 팝업 오픈
	 * @constructor
	 */
	var CSPopupPackageAddOpen = function(){
		//합포 금지 설정 확인
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_is_lock == "Y") {
			alert("합포금지 설정이 되어 있는 주문입니다.");
			return;
		}

		//현재 상태가 접수 상태일 아닐 경우 변경 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_ACCEPT"
		&& _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_ACCEPT_TEMP") {
			alert("접수 상태의 주문만 합포추가가 가능합니다.");
			return;
		}

		var p_url = "cs_pop_package_add.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response){
				CSPopupCommonPopOpen(600, 0, "합포추가", response);
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
	 * CS 팝업 페이지 - 합포추가 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupPackageAddInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		//관리번호 입력 창
		$('.order_idx_selectize').selectize({
			plugins: ['remove_button'],
			delimiter: ',',
			persist: false,
			createOnBlur: true,
			create: function(input) {
				return {
					value: input,
					text: input
				}
			},
			createFilter: function(input){
				var patt = /[^0-9]/g;
				return (input.indexOf(' ') == -1 && !patt.test(input) ) ? 1 : 0;
			}
		});

		$("#btn-save-address").on("click", function(){

			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	var OrderPackageExceptExeVars = new Array();
	var OrderPackageExceptTargetObj = new Object();
	/**
	 * CS 팝업 페이지 - 일괄합포제외 단일 제외
	 * @param order_pack_idx
	 * @constructor
	 */
	var OrderPackageExceptExecOne = function(order_pack_idx){
		//합포 금지 설정 확인
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_is_lock == "Y") {
			alert("합포금지 설정이 되어 있는 주문입니다.");
			return;
		}

		//현재 상태가 접수 상태일 아닐 경우 변경 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_ACCEPT") {
			alert("접수 상태의 주문만 합포제외가 가능합니다.");
			return;
		}

		//현재 상태가 송장, 배송일 경우 합포 금지 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_INVOICE"
		|| _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_SHIPPED") {
			alert("현재 상태가 송장, 배송일 경우 진행할 수 없습니다.");
			return;
		}

		OrderPackageExceptExeVars = new Array();
		//OrderPackageExceptExeVars.push(order_pack_idx);

		//제외 대상
		OrderPackageExceptTargetObj = new Object();


		//제외 No 가져오기
		var except_no = parseInt($(".btn-package-multi-except[data-order_pack_idx='"+order_pack_idx+"']").data("except_no"));

		//if(except_no == 0){

		var last_except_no = Object.size(OrderPackageExceptTargetObj) + 1;

		var $chk_list = $(".chk_package_except[data-order_pack_idx='"+order_pack_idx+"']:checked");

		var tmpOrderPackageExceptAry = new Object();
		var packObj = new Array();
		var isOk = true;
		$chk_list.each(function(i, o){
			$_chk = $(this);
			var _order_idx = $_chk.data("order_idx");
			var _order_pack_idx = $_chk.data("order_pack_idx");
			var _order_matching_idx = $_chk.data("order_matching_idx");
			var _product_option_idx = $_chk.data("product_option_idx");
			var $_input = $(".input_package_except_"+_order_matching_idx);
			var _num = $_input.val();
			var _remain = parseInt($(".div_except_set_"+_order_idx+" .product_option_cnt_remain_"+_order_matching_idx).val());

			//합포관리번호 주문은 전체를 따로 합포분리 할 수 없다
			//합포관리번호의 주문의 전체 갯수를 따로 합포 할 경우 에러 발생
			// if(_order_idx == _order_pack_idx && _num == _remain){
			// 	alert("합포대표 주문은 전체 수량을 합포제외 할 수 없습니다.");
			// 	isOk = false;
			// 	return false;
			// }

			if($_chk.is(":checked")){

				if(Math.floor(_num) != _num || !$.isNumeric(_num) || _num < 1){
					alert("숫자만 입력 가능합니다.");
					isOk = false;
					return false;
				}else{
					_num = parseInt(_num);
				}

				if(_remain < _num) {
					alert("제외 수량이 잘못되었습니다.");
					isOk = false;
					return false;
				}

				var tmpObj = new Object();
				tmpObj.except_no = last_except_no;
				tmpObj.order_matching_idx = _order_matching_idx;
				tmpObj.order_pack_idx = order_pack_idx;
				tmpObj.order_idx = _order_idx;
				tmpObj.product_option_idx = _product_option_idx;
				tmpObj.product_option_cnt = _num;

				packObj.push(tmpObj);

				OrderPackageExceptExeVars.push(_order_matching_idx);
			}
		});

		if(!isOk) return;

		//함께 일괄제외 할 대상 For~
		if(packObj.length > 0) {
			OrderPackageExceptTargetObj[last_except_no] = new Array();
			$.each(packObj, function (i, o) {
				OrderPackageExceptTargetObj[last_except_no].push(o);
			});
		}

		if(Object.size(OrderPackageExceptTargetObj) == 0){
			alert('제외 할 대상이 없습니다');
			return;
		}

		OrderPackageExceptExecOnePopupOpen();
	};

	/**
	 * CS 팝업 페이지 - 일괄합포제외 CS 메시지 입력 팝업창 오픈
	 * @constructor
	 */
	var OrderPackageExceptExecOnePopupOpen = function(){
		var p_url = "cs_pop_package_except.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response) {
				CSPopupCommonPopOpen(600, 0, "합포제외", response);
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
	 * CS 팝업 페이지 - 일괄합포제외 CS 메시지 입력 팝업페이지 초기화
	 * @constructor
	 */
	var OrderPackageExceptExecOnePopupInit = function(){

		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		$("#btn-exec-except").on("click", function(){

			var cs_msg = $("textarea[name='cs_msg']").val();

			if(!confirm('제외를 실행하시겠습니까?')) {
				return;
			}else{
				OrderPackageExceptExecOneAjax(cs_msg);
			}

		});
	};

	/**
	 * CS 팝업 페이지 - 일괄합포제외 실행!!
	 * @param cs_msg
	 * @constructor
	 */
	var OrderPackageExceptExecOneAjax = function(cs_msg){

		var p_url = "/order/order_package_proc.php";
		var dataObj = new Object();
		dataObj.mode = "package_except_exec_one";
		dataObj.except = OrderPackageExceptTargetObj;
		dataObj.cs_msg = cs_msg;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			// if(response.result)
			// {
			// 	alert(response.data+"건이 일괄접수처리 되었습니다.");
			// }else{
			// 	alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			// }

			//합포제외 TD 내용 Hide
			//제외 No TD 내용 Hide
			//복수제외 TD 내용 Hide
			//제외실행 TD 내용 Hide
			//console.log(OrderPackageExceptExeVars.order_pack_idx);
			$.each(OrderPackageExceptExeVars, function(i, o){
				//$("*[data-order_pack_idx='"+o+"']").hide();
				//$(".div_except_set_"+o).hide();
				$(".div_except_set[data-order_matching_idx='"+o+"']").remove();
			});

			CSPopupCommonPopClose();

			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * CS 팝업 페이지 - 합포금지 팝업 오픈
	 * @constructor
	 */
	var CSPopupPackageLockOpen = function(){
		//현재 상태가 송장, 배송일 경우 합포 금지 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_INVOICE"
		|| _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_SHIPPED") {
			alert("현재 상태가 송장, 배송일 경우 진행할 수 없습니다.");
			return;
		}

		var p_url = "cs_pop_package_lock.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response){
				CSPopupCommonPopOpen(600, 0, "합포금지", response);
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
	 * CS 팝업 페이지 - 합포금지 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupPackageLockInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-save-address").on("click", function(){

			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 - 전체취소 팝업 오픈
	 * @constructor
	 */
	var CSPopupOrderCancelAllOpen = function(){
		//현재 상태가 접수, 배송일 경우에만 취소 가능
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_ACCEPT_TEMP"
		&& _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_ACCEPT"
		&& _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_SHIPPED") {
			alert("현재 상태가 접수, 배송일 경우에만 취소 가능합니다.");
			return;
		}

		//이미 전체 취소 인지 확인
		var isNotCancel = false;
		$.each(_CSPopupCurrentTabInfoAry, function(i, o){

			//현재 선택된 pack_idx 안의 주문일 경우 확인
			if(o.order_pack_idx == _CSPopupTabCurrentTabOrderPackIdx){
				if(o.order_cs_status != "ORDER_CANCEL"){
					isNotCancel = true;
				}
			}
		});

		if(!isNotCancel){
			alert("이미 모든 주문이 취소된 상태입니다.");
			return;
		}

		console.log()
		//교환처리 된 주문 예외
        var productChangeChk = false;
        $.each(_CSPopupTabCurrentTabMidRowData, function(i, o){
        	if(o.order_cs_status == "PRODUCT_CHANGE" && o.order_progress_step == "ORDER_SHIPPED" && o.product_change_shipped == "Y"){
				productChangeChk = true;
			}
        });
		if(productChangeChk){
			alert("배송 후 교환처리 된 주문이 있습니다.");
			return;
		}
		var p_url = "cs_pop_cancel_all.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				CSPopupCommonPopOpen(600, 0, "전체취소", response);
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
	 * CS 팝업 페이지 - 전체취소 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupOrderCancelAllInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-cancel-all").on("click", function(){

			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 - 전체정상복귀 팝업 오픈
	 * @constructor
	 */
	let CSPopupOrderRestoreAllOpen = function() {
		//현재 상태가 송장 상태일 아닐 경우 변경 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step === "ORDER_INVOICE") {
			alert("송장상태일 경우 전체정상복귀가  불가능합니다.");
			return;
		}

		//취소 주문이 포함되어 있는지 확인
		let isCancel = false;
		$.each(_CSPopupCurrentTabInfoAry, function(i, o) {
			//현재 선택된 pack_idx 안의 주문일 경우 확인
			if(o.order_pack_idx == _CSPopupTabCurrentTabOrderPackIdx) {
				if(o.order_cs_status === "ORDER_CANCEL") {
					isCancel = true;
				}
			}
		});

		if(!isCancel) {
			alert("취소된 주문이 없습니다.");
			return;
		}

		let dataObj = {};
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: "cs_pop_restore_all.php",
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response) {
				CSPopupCommonPopOpen(600, 0, "전체정상복귀", response);
			} else {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(ajaxFailWithHideLoader);
	};

	/**
	 * CS 팝업 페이지 - 전체정상복귀 팝업 페이지 초기화
	 * @constructor
	 */
	let CSPopupOrderRestoreAllInit = function() {
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function() {
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-cancel-all").on("click", function() {
			let confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')) {
				return;
			}

			showLoader();
			$.ajax({
				type: 'POST',
				url: "/cs/cs_order_proc.php",
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {
				if(response.result) {
					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

				}else{
					if(response.msg != "" && response.msg != null) {
						alert(response.msg);
					} else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}

				CSPopupCommonPopClose();
				hideLoader();

			}).fail(ajaxFailWithHideLoader);
		});
	};

	/**
	 * CS 팝업 페이지 - 개별취소 팝업 오픈
	 * @constructor
	 */
	var CSPopupOrderCancelOneOpen = function(){
		//현재 상태가 접수와 배송일 경우 가능
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_ACCEPT"
		&& _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_SHIPPED") {
			alert("현재 상태가 접수와 배송일 경우에만 개별 취소가 가능합니다.");
			return;
		}

		if(!CSPopupTabCheckBoxCalculate("cancel_one")){
			return;
		}

		var p_url = "cs_pop_cancel_one.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				CSPopupCommonPopOpen(600, 0, "개별취소", response);
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
	 * CS 팝업 페이지 - 개별취소 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupOrderCancelOneInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-cancel-one").on("click", function(){

			//console.log(OrderPackageExceptTargetObj);

			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')){
				return;
			}

			var cs_reason_code1 = "CS_REASON_CANCEL";
			var cs_reason_code2 = $("select[name='cs_reason_code2']").val();
			var cs_msg = $("textarea[name='cs_msg']").val();

			var p_url = "/cs/cs_order_proc.php";
			var dataObj = new Object();
			dataObj.mode = "cancel_one";
			dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
			dataObj.except = OrderPackageExceptTargetObj;
			dataObj.cs_msg = cs_msg;
			dataObj.cs_reason_code1 = cs_reason_code1;
			dataObj.cs_reason_code2 = cs_reason_code2;
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 - 개별정상복귀 팝업 오픈
	 * @constructor
	 */
	let CSPopupOrderRestoreOneOpen = function(){
		//현재 상태가 접수, 배송 상태만 가능
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step !== "ORDER_ACCEPT"
		&& _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step !== "ORDER_SHIPPED"){
			alert("현재 상태가 접수, 배송만 가능합니다.");
			return;
		}

		if(_CSPopupTabCurrentTabOrderCsStatus !== "ORDER_CANCEL") {
			alert("취소된 상품만 정상 복귀 가능합니다.");
			return;
		}

		let dataObj = {};
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		dataObj.order_matching_idx = _CSPopupTabCurrentTabOrderMatchingIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: "cs_pop_restore_one.php",
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response){
				CSPopupCommonPopOpen(600, 0, "개별정상복귀", response);
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(ajaxFailWithHideLoader);
	};

	/**
	 * CS 팝업 페이지 - 개별정상복귀 팝업 페이지 초기화
	 * @constructor
	 */
	let CSPopupOrderRestoreOneInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-restore-one").on("click", function(){
			let confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')){
				return;
			}

			let dataObj = {};
			dataObj.mode = "restore_one";
			dataObj.order_matching_idx = _CSPopupTabCurrentTabOrderMatchingIdx;
			dataObj.cs_msg = $("textarea[name='cs_msg']").val();
			showLoader();
			$.ajax({
				type: 'POST',
				url: "/cs/cs_order_proc.php",
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if(response.result){
					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

				}else{
					if(response.msg !== "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}

				CSPopupCommonPopClose();
				hideLoader();
			}).fail(ajaxFailWithHideLoader);

		});
	};

	/**
	 * CS 팝업 페이지 - 탭 주문 리스트에서 체크 된 내역 확인 및 계산
	 * OrderPackageExceptTargetObj 배열 변수에 선택된 내용을 담는다
	 * @param chk_type : string 체크 타입 (합포제외 : except, 개별취소 : cancel_one, 개별정상복귀 : restore_one
	 * @returns {boolean}
	 * @constructor
	 */
	var CSPopupTabCheckBoxCalculate = function(chk_type){
		var order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;

		OrderPackageExceptExeVars = [];

		//대상
		OrderPackageExceptTargetObj = {};


		//No 가져오기
		var except_no = parseInt($(".btn-package-multi-except[data-order_pack_idx='"+order_pack_idx+"']").data("except_no"));

		//if(except_no == 0){

		var last_except_no = Object.size(OrderPackageExceptTargetObj) + 1;

		var $chk_list = $(".chk_package_except[data-order_pack_idx='"+order_pack_idx+"']:checked");

		var tmpOrderPackageExceptAry = new Object();
		var packObj = new Array();
		var isOk = true;
		$chk_list.each(function(i, o){
			$_chk = $(this);
			var _order_idx = $_chk.data("order_idx");
			var _order_matching_idx = $_chk.data("order_matching_idx");
			var _product_option_idx = $_chk.data("product_option_idx");
			var _order_cs_status = $_chk.data("order_cs_status");
			var _order_progress_step = $_chk.data("order_progress_step");
			var _product_change_shipped = $_chk.data("product_change_shipped");
			var $_input = $(".input_package_except_"+_order_matching_idx);
			var _num = $_input.val();
			var _remain = parseInt($(".div_except_set_"+_order_idx+" .product_option_cnt_remain_"+_order_matching_idx).val());
			if($_chk.is(":checked")){

				//개별 취소 일 경우
				if(chk_type == "cancel_one"){
					if(_order_cs_status == "PRODUCT_CHANGE" && _order_progress_step == "ORDER_SHIPPED" && _product_change_shipped =="Y"){
						alert("배송 후 교환처리된 주문입니다.");
						isOk = false;
						return false;
					}else if(_order_cs_status == "ORDER_CANCEL"){
						alert("이미 취소된 주문을 선택하셨습니다.");
						isOk = false;
						return false;
					}
				}

				//개별 복구 일 경우
				if(chk_type == "restore_one" && _order_cs_status != "ORDER_CANCEL"){
					alert("취소된 주문만 선택해 주세요.");
					isOk = false;
					return false;
				}

				if(Math.floor(_num) != _num || !$.isNumeric(_num) || _num < 1){
					alert("숫자만 입력 가능합니다.");
					isOk = false;
					return false;
				}else{
					_num = parseInt(_num);
				}

				if(_remain < _num) {
					alert("수량이 잘못되었습니다.");
					isOk = false;
					return false;
				}

				var tmpObj = new Object();
				tmpObj.except_no = last_except_no;
				tmpObj.order_matching_idx = _order_matching_idx;
				tmpObj.order_pack_idx = order_pack_idx;
				tmpObj.order_idx = _order_idx;
				tmpObj.product_option_idx = _product_option_idx;
				tmpObj.product_option_cnt = _num;

				packObj.push(tmpObj);

				OrderPackageExceptExeVars.push(_order_matching_idx);
			}
		});

		if(!isOk) return false;

		//함께 할 대상 For~
		if(packObj.length > 0) {
			OrderPackageExceptTargetObj[last_except_no] = new Array();
			$.each(packObj, function (i, o) {
				OrderPackageExceptTargetObj[last_except_no].push(o);
			});
		}

		//}else {

		//}

		if(Object.size(OrderPackageExceptTargetObj) == 0){
			alert('선택된 대상이 없습니다');
			return false;
		}

		return true;
	};

	/**
	 * CS 팝업 페이지 - 재고회수 페이지 초기화
	 * @param order_pack_idx
	 * @constructor
	 */
	var CSPopupStockReturnInit = function(order_pack_idx, cs_status){

		//상단 제품 목록 Grid List
		$("#grid_return_order_list").jqGrid({
			url: './cs_sub_order_grid.php',
			mtype: "GET",
			postData:{
				order_pack_idx: order_pack_idx,
				order_cs_status : cs_status
			},
			datatype: "json",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: function(obj){return obj.length;},
				repeatitems: false,
				id: "idx"
			},
			colModel: [
				{ label: '내부번호', name: 'inner_no', index: 'inner_no', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '합포IDX', name: 'order_pack_idx', index: 'order_pack_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '매칭IDX', name: 'order_matching_idx', index: 'order_matching_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '관리번호', name: 'order_idx2', index: 'order_idx2', width: 80, align: 'center', sortable: false, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : rowobject.order_idx;
					}},
				{ label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 100, align: 'center', sortable: false, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : Common.toDateTimeOnlyDate(cellvalue);
					}},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 150, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : cellvalue;
					}},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 150, align: 'center', sortable: false, align: 'left', formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : cellvalue;
					}},
				{ label: '개수', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return '<input type="text" name="return_cnt" id="return_cnt_'+rowobject.order_matching_idx+'" value="' + cellvalue + '" class="w100per onlyNumberDynamic" maxlength="5" />';
					}},
				{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100,  sortable: false, classes: "multiline"},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 200, align: 'left', sortable: false, classes: "multiline"},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 200,  align: 'left', sortable: false},
				{ label: '가격', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 100, align: 'right', sortable: false, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return Common.addCommas(cellvalue);
					}},
				{ label: '주문자', name: 'order_name', index: 'order_name', width: 120, sortable: false},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 120, sortable: false},
			],
			rowNum:10,
			rowList:[10,20,30],
			pager: '#grid_return_order_pager',
			viewrecords: true,
			sortorder: "desc",
			autowidth: true,
			height: 150,
			rownumbers: true,
			shrinkToFit: false,
			multiselect: true,
			loadComplete: function(data){

				Common.jqGridResizeWidth("#grid_return_order_list");

				if (data.rows.length == 0 || data.rows == null) {
					var nodata_html = '<div class="no-data">검색결과가 없습니다.</div>';
					$("#gbox_grid_return_order_list .ui-jqgrid-bdiv").eq(0).append(nodata_html);
				}else{
					$("#gbox_grid_return_order_list .ui-jqgrid-bdiv .no-data").remove();
				}
			},
			loadBeforeSend: function(){
				$("#grid_return_order_list .ui-jqgrid .loading").show();
			}
		});
		//재고회수
		$("#btn-stock-return-insert").on("click", function(){
			CSPopupStockReturnRequest();
		});
	};

	/**
	 * 재고회수
	 * @constructor
	 */
	var CSPopupStockReturnRequest = function(){
		var _s = CSPopupStockReturnFormValue();

		if(_s === false){
			alert("상품을 선택해주세요.");
			return;
		}

		if(!confirm('재고회수 처리 된 상품은 입고예정으로 등록됩니다.\n\n재고회수 처리 하시겠습니까?' )){
			return;
		}

		var p_url = "/cs/cs_order_proc.php";
		_s.mode = "stock_return";
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: _s
		}).done(function (response) {
			if(response.result){
				try{
					opener.CSPopup.CSPopupReload(true, true);
				}catch (e) {
					console.log(e);
				}
				self.close();
			}else{
				if(response.msg != "" && response.msg != null){
					alert(response.msg);
				}else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});

	};

	var AddressBookObject = new Object();   //주소록 리스트 배열 변수
	var AddresdsBookFirstLoad = true;       //주소록 최초 로딩 여부 변수

	/**
	 * CS 팝업 페이지 - 회수 모달 팝업 Open
	 * @constructor
	 */
	let CSPopupReturnOpen = function(type){
		AddresdsBookFirstLoad = true;
		//현재 상태가 배송 상태일 아닐 경우 변경 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step !== "ORDER_SHIPPED") {
			alert("배송된 주문만 회수가 가능합니다.");
			return;
		}

		if(_CSPopupTabCurrentTabMidRowData[_CSPopupListMidSelectionNum-1].product_sale_type !== "SELF") {
			alert("회수는 사입상품만 가능합니다.");
			return;
		}

		if (type === "ORDER") {
			let url = '/cs/cs_pop_return.php?order_pack_idx='+_CSPopupTabCurrentTabOrderPackIdx+'&cs_status='+_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_cs_status;
			Common.newWinPopup(url, 'cs_return_pop', 1200, 860, 'yes');
		} else if (type === "STOCK") {
			if(_CSPopupTabCurrentTabMidRowData[_CSPopupListMidSelectionNum-1].order_cs_status !== "ORDER_CANCEL" &&
				_CSPopupTabCurrentTabMidRowData[_CSPopupListMidSelectionNum-1].order_cs_status !== "PRODUCT_CHANGE") {
				alert("재고회수는 취소, 교환된 주문만 가능합니다.");
				return;
			}

			let url = '/cs/cs_pop_stock_return.php?order_pack_idx='+_CSPopupTabCurrentTabOrderPackIdx+'&cs_status='+_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_cs_status;
			Common.newWinPopup(url, 'cs_return_pop', 800, 550, 'yes');
		}
	};

	/**
	 * CS 팝업 페이지 - 회수 페이지 초기화
	 * @param order_pack_idx
	 * @constructor
	 */
	let CSPopupOrderReturnRequestList = {};
	let CSPopupOrderReturnInit = function(order_pack_idx, cs_status){

		$(".btn-return-search").on("click", function(){
			location.href="cs_pop_return.php?order_idx="+$("input.enterDoSearchReturn").val();
		});

		$("input.enterDoSearchReturn").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode === 13) {
				event.preventDefault();
				location.href="cs_pop_return.php?order_idx="+$(this).val();
			}
		});

		$("#btn-return-update").hide();
		$("#btn-return-delete").hide();

		//상단 제품 목록 Grid List
		$("#grid_return_order_list").jqGrid({
			url: './cs_sub_order_grid.php',
			mtype: "GET",
			postData:{
				order_pack_idx: order_pack_idx,
				order_cs_status : cs_status
			},
			datatype: "json",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: function(obj){return obj.length;},
				repeatitems: false,
				id: "idx"
			},
			colModel: [
				{ label: '내부번호', name: 'inner_no', index: 'inner_no', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '합포IDX', name: 'order_pack_idx', index: 'order_pack_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '관리번호', name: 'order_idx', index: 'order_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '매칭IDX', name: 'order_matching_idx', index: 'order_matching_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, align: 'center', sortable: false, hidden: true},
				{ label: '관리번호', name: 'order_idx2', index: 'order_idx2', width: 80, align: 'center', sortable: false, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : rowobject.order_idx;
					}},
				{ label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 100, align: 'center', sortable: false, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : Common.toDateTimeOnlyDate(cellvalue);
					}},
				{ label: '판매처', name: 'seller_name', index: 'seller_name', width: 150, sortable: false, formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : cellvalue;
					}},
				{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 150, align: 'center', sortable: false, align: 'left', formatter: function (cellvalue, options, rowobject) {
						return (rowobject.inner_no > 1) ? '' : cellvalue;
					}},
				{ label: '개수', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return '<input type="text" name="return_cnt" id="return_cnt_'+rowobject.order_matching_idx+'" value="' + cellvalue + '" class="w100per onlyNumberDynamic" maxlength="5" />';
					}},
				{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100,  sortable: false, classes: "multiline"},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 200, align: 'left', sortable: false, classes: "multiline"},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 200,  align: 'left', sortable: false},
				{ label: '가격', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 100, align: 'right', sortable: false, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return Common.addCommas(cellvalue);
					}},
				{ label: '주문자', name: 'order_name', index: 'order_name', width: 120, sortable: false},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 120, sortable: false},
			],
			viewrecords: true,
			rowNum:10,
			rowList:[10,20,30],
			pager: '#grid_return_order_pager',
			viewrecords: true,
			sortorder: "desc",
			autowidth: true,
			height: 100,
			rownumbers: true,
			shrinkToFit: true,
			multiselect: true,
			loadComplete: function(data){

				Common.jqGridResizeWidth("#grid_return_order_list");

				if (data.rows.length === 0) {
					let nodata_html = '<div class="no-data">검색결과가 없습니다.</div>';
					$("#gbox_grid_return_order_list .ui-jqgrid-bdiv").eq(0).append(nodata_html);
				}else{
					$("#gbox_grid_return_order_list .ui-jqgrid-bdiv .no-data").remove();
				}
			},
			loadBeforeSend: function(){
				$("#grid_return_order_list .ui-jqgrid .loading").show();
			}
		});

		CSPopupOrderReturnRequestList = {};
		//중단 회수 요청 목록 Grid List
		$("#grid_return_call_list").jqGrid({
			url: './cs_delivery_call_grid.php',
			mtype: "GET",
			postData:{
				order_idx: order_pack_idx
			},
			datatype: "json",
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: function(obj){return obj.length;},
				repeatitems: false,
				id: "idx"
			},
			colModel: [
				{ label: 'return_idx', name: 'return_idx', index: 'return_idx', width: 100, align: 'center', sortable: false, hidden: true},
				{ label: '택배등록', name: 'delivery_status_han', index: 'delivery_status_han', width: 100, align: 'center', sortable: false, formatter: function (cellvalue, options, rowobject) {
						CSPopupOrderReturnRequestList[rowobject.return_idx] = rowobject;
						return cellvalue;
					}},
				{ label: '택배사', name: 'delivery_name', index: 'delivery_name', width: 100, align: 'center', sortable: false},
				{ label: '수령자', name: 'receive_name', index: 'receive_name', width: 100, align: 'center', sortable: false},
				{ label: '접수', name: 'accept_date', index: 'accept_date', width: 200, align: 'center', sortable: false},
				{ label: '송장', name: 'invoice_date', index: 'invoice_date', width: 200, align: 'center', sortable: false},
				{ label: '수령', name: 'receive_date', index: 'receive_date', width: 200, align: 'center', sortable: false}
			],
			rowNum:100,
			rowList:[],
			pager: '#grid_return_call_pager',
			viewrecords: true,
			sortorder: "desc",
			autowidth: true,
			height: 100,
			rownumbers: false,
			shrinkToFit: true,
			onSelectRow: function(rowid, status){
				var rowData = $("#grid_return_call_list").getRowData(rowid);
				var return_idx = rowData.return_idx;

				CSPopupOrderReturnSetRequest(return_idx);
			},
			loadComplete: function(data){

				Common.jqGridResizeWidth("#grid_return_call_list");

				if (data.rows.length == 0 || data.rows == null) {
					var nodata_html = '<div class="no-data">검색결과가 없습니다.</div>';
					$("#gbox_grid_return_call_list .ui-jqgrid-bdiv").eq(0).append(nodata_html);
				}else{
					$("#gbox_grid_return_call_list .ui-jqgrid-bdiv .no-data").remove();
				}
			},
			loadBeforeSend: function(){
				$("#gbox_grid_return_call_list .ui-jqgrid .loading").show();
			}
		});

		//주소록 Bind
		CSPopupOrderReturnAddressBookChangeEvent();
		CSPopupOrderReturnAddressBookInit();

		//주소록 추가
		$(".btn-addressbook-add").on("click", function(){
			CSPopupOrderReturnAddressBookAdd();
		});
		//주소록 수정
		$(".btn-addressbook-update").on("click", function(){
			CSPopupOrderReturnAddressBookUpdate();
		});
		//주소록 삭제
		$(".btn-addressbook-delete").on("click", function(){
			CSPopupOrderReturnAddressBookDelete();
		});
		//주소록 초기화
		$(".btn-addressbook-reset").on("click", function(){
			$("select[name='address_book']").trigger("change");
		});

		//반품접수
		$("#btn-return-add").on("click", function(){
			CSPopupOrderReturnRequest();
		});

		//반품수정
		$("#btn-return-update").on("click", function(){
			CSPopupOrderReturnRequestUpdate();
		});

		//반품삭제
		$("#btn-return-delete").on("click", function(){
			CSPopupOrderReturnRequestDelete();
		});
	};

	/**
	 * 회수 요청 - 반품접수
	 * @constructor
	 */
	let CSPopupOrderReturnRequest = function(){
		let args = CSPopupOrderReturnFormValue("add");

		if(args === false){
			alert("반품 접수하실 상품을 선택해주세요.");
			return;
		}

		if(!confirm('반품 접수 하시겠습니까?')){
			return;
		}

		args.mode = "return_add";
		showLoader();
		$.ajax({
			type: 'POST',
			url: "/cs/cs_order_proc.php",
			dataType: "json",
			data: args
		}).done(function (response) {
			if (response.result) {
				try {
					opener.CSPopup.CSPopupReload(true, true);
				} catch (e) {
					console.log(e);
				}
				self.close();
			} else {
				if (response.msg !== "" && response.msg != null) {
					alert(response.msg);
				} else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
			}
			hideLoader();
		}).fail(ajaxFailWithHideLoader);
	};

	/**
	 * 회수 요청 수정
	 * @constructor
	 */
	var CSPopupOrderReturnRequestUpdate = function(){
		var _s = CSPopupOrderReturnFormValue("update");

		if(!confirm('반품 수정 하시겠습니까?')){
			return;
		}

		var $call_grid = $("#grid_return_call_list");

		var selRowId = $call_grid.getGridParam("selrow");
		var rowData = $call_grid.getRowData(selRowId);
		_s.return_idx = rowData.return_idx;


		var p_url = "/cs/cs_order_proc.php";
		_s.mode = "return_update";
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: _s
		}).done(function (response) {

			if(response.result){

				try{
					opener.CSPopup.CSPopupReload(true, true);
				}catch (e) {
					console.log(e);
				}
				self.close();

				// if(typeof response.order_pack_idx != "undefined") {
				// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
				// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
				// }
			}else{
				if(response.msg != "" && response.msg != null){
					alert(response.msg);
				}else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
			}
			hideLoader();
		}).fail(ajaxFailWithHideLoader);
	};

	/**
	 * 회수 요청 삭제
	 * @constructor
	 */
	var CSPopupOrderReturnRequestDelete = function(){
		var _s = new Object();

		if(!confirm('반품 삭제 하시겠습니까?')){
			return;
		}

		var cs_msg = $("textarea[name='cs_msg']").val();

		var $call_grid = $("#grid_return_call_list");

		var selRowId = $call_grid.getGridParam("selrow");
		var rowData = $call_grid.getRowData(selRowId);
		_s.return_idx = rowData.return_idx;

		_s.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		_s.cs_msg = cs_msg;

		var p_url = "/cs/cs_order_proc.php";
		_s.mode = "return_delete";
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: _s
		}).done(function (response) {

			if(response.result){

				//상단 리스트 선택 상태 고정
				_CSPopupListTopHoldSelection = true;
				//중단 리스트 선택 상태 고정
				_CSPopupListMidHoldSelection = true;
				//상단 리스트 reload
				$("#list_top").trigger("reloadGrid");

				// if(typeof response.order_pack_idx != "undefined") {
				// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
				// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
				// }
			}else{
				if(response.msg != "" && response.msg != null){
					alert(response.msg);
				}else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
			}
			CSPopupCommonPopClose();

			hideLoader();
		}).fail(ajaxFailWithHideLoader);
	};

	let CSPopupOrderReturnFormValue = function(chk_type){
		let _s = CSPopupOrderReturnAddressBookReceiveFormValue();

		let tel1 = $(".cs_order_return_popup input[name='send_tel_num1']").val();
		let tel2 = $(".cs_order_return_popup input[name='send_tel_num2']").val();
		let tel3 = $(".cs_order_return_popup input[name='send_tel_num3']").val();

		let hp1 = $(".cs_order_return_popup input[name='send_hp_num1']").val();
		let hp2 = $(".cs_order_return_popup input[name='send_hp_num2']").val();
		let hp3 = $(".cs_order_return_popup input[name='send_hp_num3']").val();

		_s.order_idx = $("#return_order_idx").val();
		_s.order_pack_idx = $("#return_order_pack_idx").val();

		_s.is_auto_stock_order = ($("#is_auto_stock_order").is(":checked")) ? "Y" : "N";

		_s.send_name = $(".cs_order_return_popup input[name='send_name']").val();
		_s.send_tel_num = tel1 + '-' + tel2 + '-' + tel3;
		_s.send_hp_num = hp1 + '-' + hp2 + '-' + hp3;
		_s.send_zipcode = $(".cs_order_return_popup input[name='send_zipcode']").val();
		_s.send_address = $(".cs_order_return_popup input[name='send_address']").val();
		_s.send_memo = $(".cs_order_return_popup input[name='send_memo']").val();
		//정산구분
		_s.delivery_pay_type = $(".cs_order_return_popup input[name='delivery_pay_type']:checked").val();
		//접수구분
		_s.delivery_return_type = $(".cs_order_return_popup input[name='delivery_return_type']:checked").val();

		//박스수량
		_s.box_num = $(".cs_order_return_popup input[name='box_num']").val();
		//물품가격
		_s.product_price = $(".cs_order_return_popup input[name='product_price']").val();
		//배송운임
		_s.delivery_price = $(".cs_order_return_popup input[name='delivery_price']").val();
		//사이트결제
		_s.pay_site = $(".cs_order_return_popup input[name='pay_site']").val();
		//동봉
		_s.pay_pack = $(".cs_order_return_popup input[name='pay_pack']").val();
		//계좌
		_s.pay_account = $(".cs_order_return_popup input[name='pay_account']").val();

		//CS 메시지
		_s.cs_msg = $(".cs_order_return_popup textarea[name='cs_msg']").val();

		//선택된 rowid 가져오기
		let rowIds = $("#grid_return_order_list").jqGrid('getGridParam', "selarrrow" );
		let returnProduct = [];

		if(rowIds.length > 0) {
			try {

				$.each(rowIds, function (i, o) {
					let rowData = $("#grid_return_order_list").getRowData(o);

					let order_matching_idx = rowData.order_matching_idx;

					let _chkObj = {};
					_chkObj.order_idx = rowData.order_idx;
					_chkObj.order_pack_idx = rowData.order_pack_idx;
					_chkObj.product_idx = rowData.product_idx;
					_chkObj.product_option_idx = rowData.product_option_idx;
					_chkObj.order_matching_idx = order_matching_idx;
					_chkObj.return_cnt = $("#return_cnt_"+order_matching_idx).val();

					returnProduct.push(_chkObj);
				});

			}catch(e){
				alert("오류가 발생했습니다. 오류: " + e.message);
				return;
			}
		}else{
			//alert("회수 요청 하실 상품을 선택해주세요.");
			if(chk_type === "add") {
				return false;
			}
		}

		let returnProductObj = {};
		let product_no = 0;
		//함께 할 대상 For~
		if(returnProduct.length > 0) {
			returnProductObj[product_no] = [];
			$.each(returnProduct, function (i, o) {
				returnProductObj[product_no] = o;
				product_no++;
			});
		}

		_s.product_list = returnProductObj;
		return _s;
	};

	var CSPopupStockReturnFormValue = function(){
		var _s = new Object();

		_s.order_idx = $("#return_order_idx").val();
		_s.order_pack_idx = $("#return_order_pack_idx").val();

		//CS 메시지
		_s.cs_msg = $(".cs_order_return_popup textarea[name='cs_msg']").val();

		//선택된 rowid 가져오기
		var rowIds = $("#grid_return_order_list").jqGrid('getGridParam', "selarrrow" );

		var returnProduct = new Array();

		if(rowIds.length > 0) {
			try {

				$.each(rowIds, function (i, o) {
					var rowData = $("#grid_return_order_list").getRowData(o);

					var order_idx = rowData.order_idx;
					var order_pack_idx = rowData.order_pack_idx;
					var product_idx = rowData.product_idx;
					var product_option_idx = rowData.product_option_idx;
					var order_matching_idx = rowData.order_matching_idx;
					var return_cnt = $("#return_cnt_"+order_matching_idx).val();

					var _chkObj = new Object();
					_chkObj.order_idx = order_idx;
					_chkObj.order_pack_idx = order_pack_idx;
					_chkObj.product_idx = product_idx;
					_chkObj.product_option_idx = product_option_idx;
					_chkObj.order_matching_idx = order_matching_idx;
					_chkObj.return_cnt = return_cnt;

					returnProduct.push(_chkObj);
				});

			}catch(e){
				alert("오류!");
				return;
			}
		}else{
			return false;
		}

		var returnProductObj = new Object();
		var product_no = 0;
		//함께 할 대상 For~
		if(returnProduct.length > 0) {
			returnProductObj[product_no] = new Array();
			$.each(returnProduct, function (i, o) {
				returnProductObj[product_no] = o;
				product_no++;
			});
		}

		_s.product_list = returnProductObj;
		return _s;
	};

	/**
	 * 회수 내역 선택 시 내용 바인딩
	 * @constructor
	 */
	var CSPopupOrderReturnSetRequest = function(return_idx){
		var _data = CSPopupOrderReturnRequestList[return_idx];

		if(_data.delivery_status == "RETURN_REQUEST"){
			$("#btn-return-update").show();
			$("#btn-return-delete").show();
		}else{
			$("#btn-return-update").hide();
			$("#btn-return-delete").hide();
		}

		$(".cs_order_return_popup input[name='send_name']").val(_data.send_name);

		var send_tel = _data.send_tel_num;
		send_tel = send_tel.split('-');
		send_tel1 = send_tel[0];
		send_tel2 = send_tel[1];
		send_tel3 = send_tel[2];

		var send_hp = _data.send_hp_num;
		send_hp = send_hp.split('-');
		send_hp1 = send_hp[0];
		send_hp2 = send_hp[1];
		send_hp3 = send_hp[2];

		$(".cs_order_return_popup input[name='send_tel_num1']").val(send_tel1);
		$(".cs_order_return_popup input[name='send_tel_num2']").val(send_tel2);
		$(".cs_order_return_popup input[name='send_tel_num3']").val(send_tel3);

		$(".cs_order_return_popup input[name='send_hp_num1']").val(send_hp1);
		$(".cs_order_return_popup input[name='send_hp_num2']").val(send_hp2);
		$(".cs_order_return_popup input[name='send_hp_num3']").val(send_hp3);

		$(".cs_order_return_popup input[name='send_zipcode']").val(_data.send_zipcode);
		$(".cs_order_return_popup input[name='send_address']").val(_data.send_address);
		$(".cs_order_return_popup input[name='send_memo']").val(_data.send_memo);


		$(".cs_order_return_popup input[name='receive_name']").val(_data.receive_name);

		var receive_tel = _data.receive_tel_num;
		receive_tel = receive_tel.split('-');
		receive_tel1 = receive_tel[0];
		receive_tel2 = receive_tel[1];
		receive_tel3 = receive_tel[2];

		var receive_hp = _data.receive_hp_num;
		receive_hp = receive_hp.split('-');
		receive_hp1 = receive_hp[0];
		receive_hp2 = receive_hp[1];
		receive_hp3 = receive_hp[2];

		$(".cs_order_return_popup input[name='receive_tel_num1']").val(receive_tel1);
		$(".cs_order_return_popup input[name='receive_tel_num2']").val(receive_tel2);
		$(".cs_order_return_popup input[name='receive_tel_num3']").val(receive_tel3);

		$(".cs_order_return_popup input[name='receive_hp_num1']").val(receive_hp1);
		$(".cs_order_return_popup input[name='receive_hp_num2']").val(receive_hp2);
		$(".cs_order_return_popup input[name='receive_hp_num3']").val(receive_hp3);

		$(".cs_order_return_popup input[name='receive_zipcode']").val(_data.receive_zipcode);
		$(".cs_order_return_popup input[name='receive_address']").val(_data.receive_address);

		$(".cs_order_return_popup input:radio[name='delivery_pay_type']:input[value='" + _data.pay_type + "']").attr("checked", true);
		$(".cs_order_return_popup input:radio[name='delivery_return_type']:input[value='" + _data.return_type + "']").attr("checked", true);

		$(".cs_order_return_popup .return_invoice_no").text(_data.invoice_no);
		$(".cs_order_return_popup .return_invoice_no_original").text(_data.invoice_no_original);
		$(".cs_order_return_popup .return_accept_date").text(Common.toDateTimeOnlyDate(_data.accept_date));
		$(".cs_order_return_popup .return_invoice_date").text(Common.toDateTimeOnlyDate(_data.invoice_date));
		$(".cs_order_return_popup .return_delivery_date").text(Common.toDateTimeOnlyDate(_data.delivery_date));
		$(".cs_order_return_popup .return_receive_date").text(Common.toDateTimeOnlyDate(_data.receive_date));
		$(".cs_order_return_popup .return_customer_use_no").text(_data.customer_use_no);
		$(".cs_order_return_popup .return_box_num").val(_data.box_num);
		$(".cs_order_return_popup .return_product_price").val(_data.product_price);
		$(".cs_order_return_popup .return_delivery_price").val(_data.delivery_price);
		$(".cs_order_return_popup .return_pay_site").val(_data.pay_site);
		$(".cs_order_return_popup .return_pay_pack").val(_data.pay_pack);
		$(".cs_order_return_popup .return_pay_account").val(_data.pay_account);
	};

	/**
	 * 주소록 초기화
	 * @constructor
	 */
	var CSPopupOrderReturnAddressBookInit = function(){
		//$(".address_book")
		CSPopupOrderReturnAddressBookBindList();
	};

	/**
	 * 주소록 리스트 가져오기 및 배열에 담기
	 * @constructor
	 */
	var CSPopupOrderReturnAddressBookBindList = function(){
		var p_url = "/cs/cs_pop_return_addressbook_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "addressbook_list";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result){
				AddressBookObject = new Object();
				var data = response.data;
				$("select[name='address_book'] option").remove();
				$.each(data, function(i, o){
					AddressBookObject[o.address_idx] = o;
					$("select[name='address_book']").append('<option value="'+o.address_idx+'">'+o.address_name+'</option>');
				});

				if(AddresdsBookFirstLoad){
					$("select[name='address_book']").trigger("change");
					AddresdsBookFirstLoad = false;
				}
			}
		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * 주소록 선택 이벤트 바인딩
	 * @constructor
	 */
	var CSPopupOrderReturnAddressBookChangeEvent = function(){
		$("select[name='address_book']").on("change", function(){

			var idx = $(this).val();
			if(idx != "") {
				var _s = AddressBookObject[idx];

				var _tel = _s.address_tel_num;
				var _tel = _tel.split('-');
				var _tel1 = _tel[0];
				var _tel2 = _tel[1];
				var _tel3 = _tel[2];

				var _hp = _s.address_hp_num;
				var _hp = _hp.split('-');
				var _hp1 = _hp[0];
				var _hp2 = _hp[1];
				var _hp3 = _hp[2];

				$(".cs_order_return_popup input[name='receive_name']").val(_s.address_name);
				$(".cs_order_return_popup input[name='receive_tel_num1']").val(_tel1);
				$(".cs_order_return_popup input[name='receive_tel_num2']").val(_tel2);
				$(".cs_order_return_popup input[name='receive_tel_num3']").val(_tel3);
				$(".cs_order_return_popup input[name='receive_hp_num1']").val(_hp1);
				$(".cs_order_return_popup input[name='receive_hp_num2']").val(_hp2);
				$(".cs_order_return_popup input[name='receive_hp_num3']").val(_hp3);
				$(".cs_order_return_popup input[name='receive_zipcode']").val(_s.address_zipcode);
				$(".cs_order_return_popup input[name='receive_address']").val(_s.address_address);
			}
		});
	};

	/**
	 * 받는 분 폼 값 리턴
	 * @returns {Object}
	 * @constructor
	 */
	var CSPopupOrderReturnAddressBookReceiveFormValue = function(){

		var _s = new Object();

		var tel1 = $(".cs_order_return_popup input[name='receive_tel_num1']").val();
		var tel2 = $(".cs_order_return_popup input[name='receive_tel_num2']").val();
		var tel3 = $(".cs_order_return_popup input[name='receive_tel_num3']").val();

		var hp1 = $(".cs_order_return_popup input[name='receive_hp_num1']").val();
		var hp2 = $(".cs_order_return_popup input[name='receive_hp_num2']").val();
		var hp3 = $(".cs_order_return_popup input[name='receive_hp_num3']").val();

		_s.address_name = $(".cs_order_return_popup input[name='receive_name']").val();
		_s.address_tel_num = tel1 + '-' + tel2 + '-' + tel3;
		_s.address_hp_num = hp1 + '-' + hp2 + '-' + hp3;
		_s.address_zipcode = $(".cs_order_return_popup input[name='receive_zipcode']").val();
		_s.address_address = $(".cs_order_return_popup input[name='receive_address']").val();

		return _s;
	};

	/**
	 * 주소록 폼 값 리턴
	 * @returns {Object}
	 * @constructor
	 */
	var CSPopupOrderReturnAddressBookFormValue = function(){

		var _s = new Object();

		var tel1 = $(".cs_order_return_popup input[name='receive_tel_num1']").val();
		var tel2 = $(".cs_order_return_popup input[name='receive_tel_num2']").val();
		var tel3 = $(".cs_order_return_popup input[name='receive_tel_num3']").val();

		var hp1 = $(".cs_order_return_popup input[name='receive_hp_num1']").val();
		var hp2 = $(".cs_order_return_popup input[name='receive_hp_num2']").val();
		var hp3 = $(".cs_order_return_popup input[name='receive_hp_num3']").val();

		_s.address_name = $(".cs_order_return_popup input[name='receive_name']").val();
		_s.address_tel_num = tel1 + '-' + tel2 + '-' + tel3;
		_s.address_hp_num = hp1 + '-' + hp2 + '-' + hp3;
		_s.address_zipcode = $(".cs_order_return_popup input[name='receive_zipcode']").val();
		_s.address_address = $(".cs_order_return_popup input[name='receive_address']").val();

		return _s;
	};

	/**
	 * 주소록 추가
	 * @constructor
	 */
	var CSPopupOrderReturnAddressBookAdd = function(){

		if(!confirm('주소록에 추가하시겠습니까?')){
			return;
		}

		var p_url = "/cs/cs_pop_return_addressbook_ajax.php";
		var dataObj = new Object();
		dataObj = CSPopupOrderReturnAddressBookReceiveFormValue();
		dataObj.mode = "addressbook_add";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result){
				CSPopupOrderReturnAddressBookBindList();
			}
		}).fail(function(jqXHR, textStatus){
		});

	};

	/**
	 * 주소록 수정
	 * @constructor
	 */
	var CSPopupOrderReturnAddressBookUpdate = function(){

		if(!confirm('선택된 주소록을 수정하시겠습니까?')){
			return;
		}

		var p_url = "/cs/cs_pop_return_addressbook_ajax.php";
		var dataObj = new Object();
		dataObj = CSPopupOrderReturnAddressBookFormValue();
		dataObj.address_idx = $("select[name='address_book']").val();
		dataObj.mode = "addressbook_update";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result){
				CSPopupOrderReturnAddressBookBindList();
			}
		}).fail(function(jqXHR, textStatus){
		});

	};

	/**
	 * 주소록 삭제
	 * @constructor
	 */
	var CSPopupOrderReturnAddressBookDelete = function(){

		if(!confirm('선택된 주소록을 삭제하시겠습니까?')){
			return;
		}

		var p_url = "/cs/cs_pop_return_addressbook_ajax.php";
		var dataObj = new Object();
		dataObj.address_idx = $("select[name='address_book']").val();
		dataObj.mode = "addressbook_delete";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result){
				CSPopupOrderReturnAddressBookBindList();
			}
		}).fail(function(jqXHR, textStatus){
		});

	};

	/**
	 * CS 팝업 페이지 - 상품교환 팝업 오픈
	 * @constructor
	 */
	var CSPopupProductChangeOpen = function(){
		//상품이 매칭되지 않으면 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_COLLECT") {
			alert("상품 매칭이 되지 않으면 교환을 실행할 수 없습니다.");
			return;
		}

		//현재 상태가 송장이라면 교환 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_INVOICE") {
			alert("송장 상태일 경우 상품교환이 불가능합니다.");
			return;
		}

        if(_CSPopupTabCurrentTabOrderCsStatus == "ORDER_CANCEL") {
            alert("취소 상태일 경우 상품교환이 불가능합니다.");
            return;
        }

		if(_CSPopupTabCurrentTabOrderCsStatus == "PRODUCT_CHANGE"
			&& _CSPopupTabCurrentTabOrderProgressStep == "ORDER_SHIPPED"
			&& _CSPopupTabCurrentTabOrderProductChangeShipped =="Y"){
			alert("배송 후 교환처리 된 주문입니다.");
			return;
		}

		var p_url = "cs_pop_product_change.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		dataObj.order_matching_idx = _CSPopupTabCurrentTabOrderMatchingIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				CSPopupCommonPopOpen(800, 0, "상품교환", response);
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
	 * CS 팝업 페이지 -상품교환 페이지 초기화
	 * @constructor
	 */
	var CSPopupProductChangeInit = function(){

		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		//교환 후 상품 판매가/수량/추가금액 Update
		$(".cs_product_change_popup input[name='c_product_sale_price'], .cs_product_change_popup input[name='c_product_option_cnt'], .cs_product_change_popup input[name='c_add_price']  ").on("keyup", function(){

			if($(".cs_product_change_popup input[name='seller_type']").val() == "VENDOR_SELLER"){

				var c_product_sale_price_unit = $(".c_product_sale_price_unit").val();
				var c_product_option_cnt = $("input[name='c_product_option_cnt']").val();
				var c_add_price = $("input[name='c_add_price']").val();

				if(c_product_sale_price_unit == "") c_product_sale_price_unit = 0;
				if(c_product_option_cnt == "") c_product_option_cnt = 0;
				if(c_add_price == "") c_add_price = 0;

				var c_product_sale_price = 0;
				if(c_product_sale_price_unit > 0 && c_product_option_cnt > 0){
					c_product_sale_price = c_product_sale_price_unit * c_product_option_cnt;
				}

				if(c_product_sale_price + c_add_price > 0 && c_product_option_cnt > 0){
					var cal = 0;
					cal = Math.floor((c_product_sale_price + parseInt(c_add_price)) / c_product_option_cnt);
					$(".c_product_sale_price_unit_cal").val(cal);
				}


				$("input[name='c_product_sale_price']").val(c_product_sale_price);

			}else{

				var c_product_sale_price = $(".cs_product_change_popup input[name='c_product_sale_price']").val();
				var c_product_option_cnt = $(".cs_product_change_popup input[name='c_product_option_cnt']").val();
				var c_add_price = $("input[name='c_add_price']").val();

				if(c_product_sale_price == "") c_product_sale_price = 0;
				if(c_product_option_cnt == "") c_product_option_cnt = 0;
				if(c_add_price == "") c_add_price = 0;

				c_product_sale_price = Number(c_product_sale_price);
				c_add_price = Number(c_add_price);
				c_product_option_cnt = Number(c_product_option_cnt);

				//console.log(c_product_sale_price);
				//console.log(c_add_price);
				//console.log(c_product_option_cnt);

				if(c_product_sale_price + c_add_price > 0 && c_product_option_cnt > 0){
					var cal = 0;
					cal = Math.floor((c_product_sale_price + c_add_price) / c_product_option_cnt);
					$(".c_product_sale_price_unit_cal").val(cal);
				}
			}

		});

		var colModel =  [
			{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
			{ label: 'product_option_sale_price', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 0, hidden: true},
			{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, is_use : true},
			{ label: '타입', name: 'code_name', index: 'code_name', width: 80, is_use : true},
			{ label: '상품명', name: 'product_name', index: 'product_name', width: 200, sortable: true},
			{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 200, sortable: true},
			{ label: '선택', name: 'product_select', index: 'product_select', width: 130, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
					return '<a href="javascript:;" data-rownum="'+options.rowId+'" class="btn btn_default btn-product-change-select">선택</a>';
				}},
		];
		$("#grid_product_change_list").jqGrid({
			url: 'cs_product_search_grid.php',
			mtype: "GET",
			datatype: "local",
			postData:{
				param: $("#searchFormPop_ProductChangeSearch").serialize()
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
			pager: '#grid_product_change_pager',
			sortname: 'A.product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: true,
			height: 100,
			loadComplete: function(){

				Common.jqGridResizeWidth("#grid_product_change_list");

				//선택
				$(".btn-product-change-select").on("click", function(){
					var rowNum = $(this).data("rownum");
					var rowData = $("#grid_product_change_list").getRowData(rowNum);

					//console.log(rowNum);

					//상품코드
					$(".cs_product_change_popup input[name='c_product_idx']").val(rowData.product_idx);
					$(".cs_product_change_popup input[name='c_product_option_idx']").val(rowData.product_option_idx);
					$(".cs_product_change_popup input[name='c_product_name']").val(rowData.product_name);
					$(".cs_product_change_popup input[name='c_product_option_name']").val(rowData.product_option_name);
					$(".cs_product_change_popup .c_product_sale_price_unit").val(rowData.product_option_sale_price);

					var product_option_sale_price_total = 0;
					var product_option_cnt = $(".cs_product_change_popup input[name='c_product_option_cnt']").val();

					if(product_option_cnt == "") product_option_cnt = 1;

					product_option_sale_price_total = parseInt(rowData.product_option_sale_price) * parseInt(product_option_cnt);

					$(".cs_product_change_popup input[name='c_product_sale_price']").val(product_option_sale_price_total);

					$(".cs_product_change_popup input[name='c_product_option_cnt']").trigger("keyup");
				});

				if($("#grid_product_change_list").getGridParam("records") == 0) {
					var nodata_html = '<div class="no-data">검색결과가 없습니다.</div>';
					$(".cs_pop_product_change_wrap .ui-jqgrid-bdiv").eq(0).append(nodata_html);
				}else{
					$(".cs_pop_product_change_wrap .no-data").remove();
				}
			}
		});

		setTimeout(function(){
			Common.jqGridResizeWidth("#grid_product_change_list");
		}, 200);
		setTimeout(function(){
			Common.jqGridResizeWidth("#grid_product_change_list");
		}, 500);

		//검색 폼 Submit 방지
		$("#searchFormPop_ProductChangeSearch").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearchPop_ProductChangeSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				CSPopupProductChangeGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_ProductChangeSearch").on("click", function(){
			CSPopupProductChangeGridSearch();
		});

		Common.jqGridResizeWidth("#grid_product_change_list");

		$("#btn-product-change").on("click", function(){
			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('교환 처리 하시겠습니까?')){
				return;
			}

			var cs_msg = $("textarea[name='cs_msg']").val();
			$("#c_cs_msg").val(cs_msg);
			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormChange']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = false;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;

					if(typeof response.order_pack_idx != "undefined") {
						$("#list_bottom_" + response.order_pack_idx).trigger("reloadGrid");
					}

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 -상품교환 페이지 - 상품 검색 Grid 검색
	 * @constructor
	 */
	var CSPopupProductChangeGridSearch = function(){
		$("#grid_product_change_list").setGridParam({
			datatype: "json",
			page: 1,
			url: 'cs_product_search_grid.php',
			postData:{
				param: $("#searchFormPop_ProductChangeSearch").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * CS 팝업 페이지 - 상품추가 팝업 오픈
	 * @constructor
	 */
	var CSPopupProductAddOpen = function(){
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_INVOICE"
		|| _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_SHIPPED") {
			alert("송장상태 또는 배송된 경우 상품 추가가 불가능합니다.");
			return;
		}

		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_COLLECT"){
			alert("상품 추가는 매칭 이후 가능합니다.");
			return;
		}

		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_cs_status == "ORDER_CANCEL"){
			alert("취소된 주문입니다.");
			return;
		}

		var p_url = "cs_pop_product_add.php";
		var dataObj = new Object();
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response){
				CSPopupCommonPopOpen(800, 0, "상품추가", response);
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	var CSPopupProductAdd_rowid = 1;
	/**
	 * CS 팝업 페이지 - 상품추가 팝업 초기화
	 * @constructor
	 */
	var CSPopupProductAddInit = function(){

		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		CSPopupProductAdd_rowid = 1;

		//상단 추가 상품 리스트
		$("#grid_list_pop_target").jqGrid({
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
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
				{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 0, hidden: true},
				{ label: '상품IDX', name: 'product_idx_hidden', index: 'product_idx_hidden', width: 0, hidden: true, formatter: function(cellvalue, options, rowobject){
						return '<input type="hidden" class="w100per product_idx" name="product_idx[]" value="'+rowobject.product_idx+'" />';

					}},
				{ label: '상품코드', name: 'product_option_idx_hidden', index: 'product_option_idx_hidden', width: 0, hidden: true, formatter: function(cellvalue, options, rowobject){
						return '<input type="hidden" class="w100per product_option_idx" name="product_option_idx[]" value="'+rowobject.product_option_idx+'" />';
					}},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: false},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: false},
				{ label: '수량', name: 'stock_due_amount', index: 'stock_due_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						var val = "0";
						if(typeof cellvalue != "undefined" && cellvalue != "")
						{
							val = cellvalue;
						}
						return '<input type="text" class="w100per product_option_cnt" name="product_option_cnt[]" value="'+val+'" />';

					}},
				{ label: '판매가', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 80, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){

						var isReadOnly = "";
						if($("form[name='dyFormProductAdd'] input[name='seller_type']").val() == "VENDOR_SELLER"){
							isReadOnly = 'readonly="readonly" onclick="alert(\'벤더사 판매처 주문은 판매가를 수정할 수 없습니다.\');"';
						}

						return '<input type="text" name="product_option_sale_price[]" class="product_option_sale_price" value="'+cellvalue+'" '+isReadOnly+' />';

					}},
				{ label: '판매단가', name: 'product_option_sale_unit_price', index: 'product_option_sale_unit_price', width: 80, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){

						var val = 0;
						if($("form[name='dyFormProductAdd'] input[name='seller_type']").val() == "VENDOR_SELLER"){
							val = rowobject.product_option_sale_price;
						}
						return '<input type="text" name="product_option_sale_unit_price" class="product_option_sale_unit_price" value="'+val+'" readonly="readonly" />';

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
			width: 500,
			height: 150,
			loadComplete: function(){

			},
			afterInsertRow : function(rowid){
				//console.log(rowid);
				$("#"+rowid + " .btn-delete-add-selected").on("click", function(){
					var rowId = $(this).data("rowid");
					$("#grid_list_pop_target").delRowData("selected_"+rowId);
				});

				//Input Mask 바인딩
				$(".product_option_sale_price, .product_option_cnt").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 6, autoGroup: true, rightAlign: false});


				$("#"+rowid + " .product_option_cnt, #"+rowid + " .product_option_sale_price").on("keyup", function(){



					if($("form[name='dyFormProductAdd'] input[name='seller_type']").val() == "VENDOR_SELLER"){

						var product_option_cnt = $(this).parent().parent().find(".product_option_cnt").val();
						var product_option_sale_price = 0;
						var product_option_sale_unit_price = $(this).parent().parent().find(".product_option_sale_unit_price").val();


						if (product_option_cnt == "") product_option_cnt = 0;
						if (product_option_sale_unit_price == "") product_option_sale_unit_price = 0;

						if (product_option_cnt > 0 && product_option_sale_unit_price > 0) {
							product_option_sale_price = Math.floor(product_option_sale_unit_price * product_option_cnt);
						}


						$(this).parent().parent().find(".product_option_sale_price").val(product_option_sale_price);

					}else {
						var product_option_cnt = $(this).parent().parent().find(".product_option_cnt").val();
						var product_option_sale_price = $(this).parent().parent().find(".product_option_sale_price").val().replace(/,/g, "");

						//console.log(product_option_cnt, product_option_sale_price);

						var product_option_sale_unit_price = 0;

						if (product_option_cnt == "") product_option_cnt = 0;
						if (product_option_sale_price == "") product_option_sale_price = 0;

						if (product_option_cnt > 0 && product_option_sale_price > 0) {
							product_option_sale_unit_price = Math.floor(product_option_sale_price / product_option_cnt);
						}

						$(this).parent().parent().find(".product_option_sale_unit_price").val(product_option_sale_unit_price);
					}



				});
			}
		});



		setTimeout(function(){
			Common.jqGridResizeWidth("#grid_list_pop_target");
		}, 200);

		setTimeout(function(){
			Common.jqGridResizeWidth("#grid_list_pop_target");
		}, 500);


		//하단 상품 검색 리스트
		$("#grid_product_change_list").jqGrid({
			url: 'cs_product_search_grid.php',
			mtype: "GET",
			datatype: "local",
			postData:{
				param: $("#searchFormPop_ProductAddSearch").serialize()
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
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
				{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, is_use : true},
				{ label: '타입', name: 'code_name', index: 'code_name', width: 80},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 200, sortable: true},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 200, sortable: true},
				{ label: 'product_option_sale_price', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 200, sortable: true, hidden: true},
				{ label: '선택', name: 'product_select', index: 'product_select', width: 150, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
						return '<a href="javascript:;" data-rownum="'+options.rowId+'" class="btn btn_default btn-product-change-select">선택</a>';
					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_product_change_pager',
			sortname: 'A.product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: true,
			height: 100,
			loadComplete: function(){

				Common.jqGridResizeWidth("#grid_product_change_list");

				//선택
				$(".btn-product-change-select").on("click", function(){
					var rowNum = $(this).data("rownum");

					var rowData = $("#grid_product_change_list").getRowData(rowNum);
					var product_option_idx = rowData.product_option_idx;
					//console.log(rowData);

					//var targetData = $("#grid_list_pop_target", opener.document).getRowData();
					$("#grid_list_pop_target").jqGrid('addRowData', CSPopupProductAdd_rowid, rowData);
					CSPopupProductAdd_rowid++;
				});

				if($("#grid_product_change_list").getGridParam("records") == 0) {
					var nodata_html = '<div class="no-data">검색결과가 없습니다.</div>';
					$(".cs_pop_product_add_wrap .ui-jqgrid-bdiv").eq(0).append(nodata_html);
				}else{
					$(".cs_pop_product_add_wrap .no-data").remove();
				}
			}
		});

		setTimeout(function(){
			Common.jqGridResizeWidth("#grid_product_change_list");
		}, 200);
		setTimeout(function(){
			Common.jqGridResizeWidth("#grid_product_change_list");
		}, 500);


		//검색 폼 Submit 방지
		$("#searchFormPop_ProductAddSearch").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearchPop_ProductChangeSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				CSPopupProductAddGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_ProductAddSearch").on("click", function(){
			CSPopupProductAddGridSearch();
		});

		//상품추가 클릭!!
		$("#btn-product-add").on("click", function(){

			//추가 내역 확인
			var rowIds = $("#grid_list_pop_target").getRowData();
			if(rowIds.length == 0){
				alert("추가하실 상품을 선택해주세요.");
				return false;
			}else{
				var checkTable = true;
				$(".cs_product_add_popup .product_option_cnt").each(function(i, o){
					var val = $(this).val().replace(/,/gi, '');   //콤마제거

					if(val == "0" || val == ""){
						alert("수량을 입력해주세요.");
						$(this).focus();
						checkTable = false;
						return false;
					}
				});

				if(!checkTable){
					return false;
				}

				$(".cs_product_add_popup .product_option_sale_price").each(function(i, o){
					var val = $(this).val().replace(/,/gi, '');   //콤마제거

					if(val == ""){
						alert("판매금액을 입력해주세요.");
						$(this).focus();
						checkTable = false;
						return false;
					}
				});

				if(!checkTable){
					return false;
				}
			}

			if(!confirm('상품을 추가 하시겠습니까?')){
				return;
			}

			var cs_msg = $("textarea[name='cs_msg']").val();
			$("#c_cs_msg").val(cs_msg);
			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormProductAdd']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = false;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;

					if(typeof response.order_pack_idx != "undefined") {
						$("#list_bottom_" + response.order_pack_idx).trigger("reloadGrid");
					}

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

	};

	/**
	 * CS 팝업 페이지 - 상품 추가 팝업 페이지 상품 검색
	 * @constructor
	 */
	var CSPopupProductAddGridSearch = function(){
		$("#grid_product_change_list").setGridParam({
			datatype: "json",
			url: 'cs_product_search_grid.php',
			page: 1,
			postData:{
				param: $("#searchFormPop_ProductAddSearch").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * CS 팝업 페이지 - 주문전체복사 오픈
	 * @constructor
	 */
	var CSPopupOrderCopyWholeOpen = function(){
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_COLLECT") {
			alert("상품 매칭 전에는 주문 복사를 실행할 수 없습니다.");
			return;
		}

		var p_url = "cs_pop_order_copy_whole.php";
		var dataObj = new Object();
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response) {
				CSPopupCommonPopOpen(600, 0, "주문전체복사", response);
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
	 * CS 팝업 페이지 - 주문전체복사 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupOrderCopyWholeInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupMemberList("SELLER_ALL_GROUP", ".copy_seller_idx", '0');

		$("#btn-copy-whole").on("click", function(){
			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormOrderCopyWhole']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 - 주문복사 오픈
	 * @constructor
	 */
	var CSPopupOrderCopyOneOpen = function(){
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_COLLECT") {
			alert("상품 매칭 전에는 주문 복사를 실행할 수 없습니다.");
			return;
		}

		var p_url = "cs_pop_order_copy_one.php";
		var dataObj = new Object();
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		dataObj.product_idx = _CSPopupTabCurrentTabOrderProductIdx;
		dataObj.product_option_idx = _CSPopupTabCurrentTabOrderProductOptionIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				CSPopupCommonPopOpen(800, 0, "주문복사", response);
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
	 * CS 팝업 페이지 - 주문복사 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupOrderCopyOneInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupMemberList("SELLER_ALL_GROUP", ".copy_seller_idx", '0');

		$(".copy_seller_idx").on("change", function(){
			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: {mode: "check_seller_type", seller_idx: $(this).val()}
			}).done(function (response) {
				if(response.result){
					if(response.seller_type == "VENDOR_SELLER"){
						$(".copy_product_option_sale_price").prop("readonly", true).off("click").on("click", function(){
							alert('벤더사 판매처는 가격을 수정할 수 없습니다.');
						});
					}else{
						$(".copy_product_option_sale_price").prop("readonly", false).off("click");
					}

					$(".copy_product_option_sale_price").val($(".copy_product_option_sale_price").data("price"));

				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});

		//Input Mask 바인딩
		$(".copy_product_option_cnt, .copy_product_option_sale_price").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 6, autoGroup: true, rightAlign: false});

		//하단 상품 검색 리스트
		$("#grid_product_change_list").jqGrid({
			url: 'cs_product_search_grid.php',
			mtype: "GET",
			datatype: "local",
			postData:{
				param: $("#searchFormPop_ProductAddSearch").serialize()
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
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
				{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 120, is_use : true},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 200, sortable: true},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 200, sortable: true},
				{ label: 'product_option_sale_price', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 200, sortable: true, hidden: true},
				{ label: '선택', name: 'product_select', index: 'product_select', width: 150, sortable: false, align: 'center', formatter: function(cellvalue, options, rowobject){
						return '<a href="javascript:;" data-rownum="'+options.rowId+'" class="btn btn_default btn-product-change-select">선택</a>';
					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_product_change_pager',
			sortname: 'A.product_option_idx',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: true,
			height: 100,
			loadComplete: function(){

				Common.jqGridResizeWidth("#grid_product_change_list");

				//선택
				$(".btn-product-change-select").on("click", function(){
					var rowNum = $(this).data("rownum");

					var rowData = $("#grid_product_change_list").getRowData(rowNum);
					var product_idx = rowData.product_idx;
					var product_option_idx = rowData.product_option_idx;
					var product_name = rowData.product_name;
					var product_option_name = rowData.product_option_name;
					var product_option_sale_price = rowData.product_option_sale_price;
					//console.log(rowData);

					$(".cs_order_copy_one input[name='copy_product_idx']").val(product_idx);
					$(".cs_order_copy_one input[name='copy_product_option_idx']").val(product_option_idx);
					$(".cs_order_copy_one input[name='copy_product_name']").val(product_name);
					$(".cs_order_copy_one input[name='copy_product_option_name']").val(product_option_name);
					$(".cs_order_copy_one input[name='copy_product_option_cnt']").val(1);
					$(".cs_order_copy_one input[name='copy_product_option_sale_price']").val(product_option_sale_price);

				});
			}
		});

		setTimeout(function(){
			Common.jqGridResizeWidth("#grid_product_change_list");
		}, 200);
		setTimeout(function(){
			Common.jqGridResizeWidth("#grid_product_change_list");
		}, 500);


		//검색 폼 Submit 방지
		$("#searchFormPop_ProductAddSearch").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearchPop_ProductChangeSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				CSPopupProductAddGridSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_ProductAddSearch").on("click", function(){
			CSPopupProductAddGridSearch();
		});

		//주문복사 클릭!!
		$("#btn-product-add").on("click", function(){

			//선택 내역 확인
			var checkTable = true;
			$(".cs_order_copy_one .copy_product_option_cnt").each(function(i, o){
				var val = $(this).val().replace(/,/gi, '');   //콤마제거

				if(val == "0" || val == ""){
					alert("수량을 입력해주세요.");
					$(this).focus();
					checkTable = false;
					return false;
				}
			});

			if(!checkTable){
				return false;
			}

			if(!confirm('주문복사 하시겠습니까?')){
				return;
			}

			var cs_msg = $("textarea[name='cs_msg']").val();
			$("#c_cs_msg").val(cs_msg);
			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormOrderCopyOne']").serialize()
			}).done(function (response) {
				if(response.result){
					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = false;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;

					$("#list_top").trigger("reloadGrid");

					if(typeof response.order_pack_idx != "undefined") {
						$("#list_bottom_" + response.order_pack_idx).trigger("reloadGrid");
					}
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 - 주문삭제 팝업 오픈
	 * @constructor
	 */
	var CSPopupOrderDeleteOpen = function(){
		//현재 상태가 송장 배송이면 삭제 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_INVOICE"
		|| _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_SHIPPED") {
			alert("송장, 배송 주문은 삭제할 수 없습니다.");
			return;
		}

		var p_url = "cs_pop_order_delete.php";
		var dataObj = new Object();
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response){
				CSPopupCommonPopOpen(600, 0, "주문삭제", response);
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
	 * CS 팝업 페이지 - 주문삭제 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupOrderDeleteInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-order-delete-all, #btn-order-delete-one").on("click", function(){

			var id = $(this).attr("id");

			if(id == "btn-order-delete-one"){
				var confirm_text = "주문삭제";
				$("form[name='dyFormDelete'] input[name='mode']").val("order_cancel");
			}else if(id == "btn-order-delete-all"){
				var confirm_text = "합포된 주문을 모두 삭제";
				$("form[name='dyFormDelete'] input[name='mode']").val("order_cancel_all");
			}else{
				return;
			}

			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormDelete']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * CS 팝업 페이지 - 매칭삭제 팝업 오픈
	 * @constructor
	 */
	var CSPopupMatchingDeleteOpen = function(){
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_matching_is_auto == "N"){
			alert("자동매칭된 상품이 아닙니다.");
			return;
		}

		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_COLLECT") {
			alert("주문 매칭이 되지 않으면 매칭 삭제가 불가능합니다.");
			return;
		}

		var p_url = "cs_pop_matching_delete.php";
		var dataObj = new Object();
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		dataObj.matching_info_idx = _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].matching_info_idx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				CSPopupCommonPopOpen(600, 0, "매칭삭제", response);
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
	 * CS 팝업 페이지 - 매칭삭제 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupMatchingDeleteInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		$("#btn-matching-delete").on("click", function(){

			if(!confirm('매칭삭제 처리 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormMatchingDelete']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};

	/**
	 * 화물 추적
	 * TODO : 추가 택배사 연동 필요!!
	 * @constructor
	 */
	var CSPopupTracking = function(){
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_INVOICE"
		&& _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_SHIPPED") {
			alert("송장상태 또는 배송상태인 주문만 화물추적이 가능합니다.");
			return;
		}

		/*
		//하드코딩 -> 변경 19.08.01
		var delivery_code = $(".order_content_delivery_code").data("value");
		var invoice_no = $(".order_content_invoice_no").data("value");

		if(delivery_code == "CJGLS"){   //CJ대한통운
			window.open("https://www.doortodoor.co.kr/parcel/doortodoor.do?fsp_action=PARC_ACT_002&fsp_cmd=retrieveInvNoACT&invc_no="+invoice_no);
		}else{
			alert('CJ대한통운 이외의 택배사는 준비중입니다.');
		}
		*/

		//하단 주문 정보 상의 송장번호 링크를 강제로 클릭!
		if($(".btn_invoice_no_tracking").length > 0){
			window.open($(".btn_invoice_no_tracking").eq(0).attr("href"));
		}
	};

	/**
	 * CS 팝업 페이지 - 반품예정 팝업 오픈
	 * @constructor
	 */
	var CSPopupOrderReturnDueOpen = function(){
		//현재 상태가 송장, 배송만 가능
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_INVOICE"
		&& _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step != "ORDER_SHIPPED") {
			alert("송장상태 또는 배송된 주문만 반품예정이 가능합니다.");
			return;
		}

		var p_url = "cs_pop_order_return_due.php";
		var dataObj = new Object();
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				CSPopupCommonPopOpen(600, 0, "전체취소", response);
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
	 * CS 팝업 페이지 - 반품예정 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupOrderReturnDueInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-save-order-return-due").on("click", function(){

			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {

				if(response.result){

					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");

					// if(typeof response.order_pack_idx != "undefined") {
					// 	var no = $("#list_bottom_" + _CSPopupTabCurrentTabOrderPackIdx).jqGrid('getGridParam', "selrow" );
					// 	$("#list_bottom_" + response.order_pack_idx).jqGrid('setSelection', no).trigger("onSelectRow");
					// }
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});
	};


	/**
	 * CS 팝업 페이지 - 우선순위 팝업 오픈
	 * @constructor
	 */
	var CSPopupInvoicePriorityOpen = function(){
		//상품 매칭되지 않으면 불가
		if(_CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].order_progress_step == "ORDER_COLLECT") {
			alert("상품이 매칭되어야 배송 우선순위를 변경할 수 있습니다.");
			return;
		}

		var p_url = "cs_pop_invoice_priority.php";
		var dataObj = new Object();
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		dataObj.product_option_idx = _CSPopupCurrentTabInfoAry[_CSPopupTabCurrentTabOrderIdx].product_option_idx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response){
				CSPopupCommonPopOpen(800, 0, "전체취소", response);
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
	 * CS 팝업 페이지 - 우선순위 팝업 페이지 초기화
	 * @constructor
	 */
	var CSPopupInvoicePriorityInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			CSPopupCommonPopClose();
		});

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-save-priority").on("click", function(){
			var confirm_text = $("#js_confirm_text").val();
			if(!confirm('' + confirm_text + ' 처리 하시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_order_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormHold']").serialize()
			}).done(function (response) {
				if(response.result){
					//상단 리스트 선택 상태 고정
					_CSPopupListTopHoldSelection = true;
					//중단 리스트 선택 상태 고정
					_CSPopupListMidHoldSelection = true;
					//상단 리스트 reload
					$("#list_top").trigger("reloadGrid");
				}else{
					if(response.msg != "" && response.msg != null){
						alert(response.msg);
					}else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
				}
				CSPopupCommonPopClose();

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});
	};



	/**
	 * CS 팝업 페이지 - 공통 모달 팝업 Open
	 * @param width
	 * @param height
	 * @param title
	 * @param html
	 * @constructor
	 */
	var CSPopupCommonPopOpen = function(width, height, title, html){
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
	 * CS 팝업 페이지 - 공통 모달 팝업 Close
	 * @constructor
	 */
	var CSPopupCommonPopClose = function() {
		$("#modal_common").html("");
		$("#modal_common").dialog( "close" );
	};

	var jqGridCheckBox = function(e) {
		e = e||event;   /* get IE event ( not passed ) */
		e.stopPropagation? e.stopPropagation() : e.cancelBubble = true;
	};

	/**
	 * 선택되어 있는 주문 상품의 정보 가져오기
	 * @param get_type
	 * @returns {*}
	 */
	var getActiveTabProductInfo = function(get_type){

		var $currentTabGrid = $("#list_bottom_"+_CSPopupTabCurrentTabOrderPackIdx);

		var selRowId = $currentTabGrid.getGridParam("selrow");
		var rowData = $currentTabGrid.getRowData(selRowId);

		//console.log(rowData);

		if(get_type === "product_name"){
			return rowData.product_name;
		}else if(get_type === "product_option_name") {
			return rowData.product_option_name;
		}else if(get_type === "product_full_name") {
			return rowData.product_name + ' ' + rowData.product_option_name;
		}
	};

	/**
	 * CS 내용 붙여넣기 버튼 바인딩
	 */
	var bindPasteCSContentBtn = function(){

		$(".btn-cs-paste").on("click", function(){
			var from = $(this).data("paste-from");
			pasteCSContent(from);
		});

	};

	/**
	 * CS 내용 붙여넣기 실행
	 * 공통 Class : .commonCsContent 인 TextArea 의 내용에 붙여 넣기
	 * @param tp
	 */
	var pasteCSContent = function(tp){
		var paste = getActiveTabProductInfo(tp);
		var it = $(".commonCsContent").val();
		var rst = it + ' ' +paste;
		$(".commonCsContent").val(rst);
	};

	/**
	 * CS 개별 완료 처리
	 * @param cs_idx
	 * @constructor
	 */
	var CSPopupSetCSConfirm = function(cs_idx){
		if(!confirm('완료 처리하시겠습니까?')){
			return;
		}
		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "set_one_confirm";
		dataObj.cs_idx = cs_idx;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSPopupGetCSList(_CSPopupTabCurrentTabOrderPackIdx);
		}).fail(function(jqXHR, textStatus){
		});

	};

	/**
	 * CS 남기기 팝업 Open
	 * @constructor
	 */
	var CSPopupCSWritePopOpen = function(){
		var p_url = "cs_pop_write.php";
		var dataObj = new Object();
		dataObj.order_idx = _CSPopupTabCurrentTabOrderIdx;
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		dataObj.order_matching_idx = _CSPopupTabCurrentTabOrderMatchingIdx;
		dataObj.product_idx = _CSPopupTabCurrentTabOrderProductIdx;
		dataObj.product_option_idx = _CSPopupTabCurrentTabOrderProductOptionIdx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
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

		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn_cs_file1', {
			_target_table : 'DY_ORDER_CS',
			_target_table_column : 'cs_file_idx1',
			_target_filename : '.span_cs_file1',
			_target_input_hidden : '#cs_file1',
			_upload_no: 1,
			_upload_type : "cs_file",
			_upload_delete_btn : "btn_cs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file2 = new FileUpload2('btn_cs_file2', {
			_target_table : 'DY_ORDER_CS',
			_target_table_column : 'cs_file_idx2',
			_target_filename : '.span_cs_file2',
			_target_input_hidden : '#cs_file2',
			_upload_no: 2,
			_upload_type : "cs_file",
			_upload_delete_btn : "btn_cs_file2_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file3 = new FileUpload2('btn_cs_file3', {
			_target_table : 'DY_ORDER_CS',
			_target_table_column : 'cs_file_idx3',
			_target_filename : '.span_cs_file3',
			_target_input_hidden : '#cs_file3',
			_upload_no: 3,
			_upload_type : "cs_file",
			_upload_delete_btn : "btn_cs_file3_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file4 = new FileUpload2('btn_cs_file4', {
			_target_table : 'DY_ORDER_CS',
			_target_table_column : 'cs_file_idx4',
			_target_filename : '.span_cs_file4',
			_target_input_hidden : '#cs_file4',
			_upload_no: 4,
			_upload_type : "cs_file",
			_upload_delete_btn : "btn_cs_file4_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file5 = new FileUpload2('btn_cs_file5', {
			_target_table : 'DY_ORDER_CS',
			_target_table_column : 'cs_file_idx5',
			_target_filename : '.span_cs_file5',
			_target_input_hidden : '#cs_file5',
			_upload_no: 5,
			_upload_type : "cs_file",
			_upload_delete_btn : "btn_cs_file5_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});

		//알람 일자 datepicker 설정
		Common.setDatePickerForDynamicElement($(".cs_order_hold_popup .jqDateDynamic"));

		//붙여넣기 버튼 바인딩
		bindPasteCSContentBtn();

		$("#btn-save-order-write").on("click", function(){

			if($.trim($(".commonCsContent").val()) == ""){
				alert("CS 내용을 입력해주세요.");
				return;
			}

			if(!confirm('C/S를 남기시겠습니까?')){
				return;
			}

			var p_url = "/cs/cs_proc.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyFormCSInsert']").serialize()
			}).done(function (response) {

				if(response.result){

				}else{
					//alert(response.msg);
				}
				CSPopupGetCSList(_CSPopupTabCurrentTabOrderPackIdx);
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
		if(!confirm('일괄 완료 처리하시겠습니까?')){
			return;
		}
		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "set_all_confirm";
		dataObj.include_auto = ($(".cs_show_all").is(":checked")) ? "Y" : "N";
		dataObj.order_pack_idx = _CSPopupTabCurrentTabOrderPackIdx;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSPopupGetCSList(_CSPopupTabCurrentTabOrderPackIdx);
		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * CS 삭제 처리
	 * @param cs_idx
	 * @constructor
	 */
	var CSPopupDeleteCS = function(cs_idx){
		if(!confirm('삭제 하시겠습니까?')){
			return;
		}
		var p_url = "/cs/cs_proc.php";
		var dataObj = new Object();
		dataObj.mode = "delete_one_cs";
		dataObj.cs_idx = cs_idx;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			CSPopupGetCSList(_CSPopupTabCurrentTabOrderPackIdx);
		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * CS 팝업 페이지 - 알람 초기화
	 * @constructor
	 */
	var CSPopupAlarmInit = function(){

		//알람 사운드 초기화
		ion.sound({
			sounds: [
				{
					name: "alarm_sound"
				}
			],
			volume: 1,
			path: "/images/",
			preload: true
		});

		//내 알람 가져오기
		CSPopupAlarmGetMyAlarm();
	};

	/**
	 * CS 팝업 페이지 - 내 알람 가져오기
	 * @constructor
	 */
	var CSPopupAlarmGetMyAlarm = function(){
		var p_url = "/cs/cs_alarm_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_my_alarm_list";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			try {
				if (response.result) {
					$.each(response.data, function (i, o) {
						//console.log(o);
						var title = Common.toDateTime(o.cs_alarm_datetime);
						var msg = o.cs_comment;
						var cs_alarm_idx = o.cs_alarm_idx;
						CSPopupAlarmToast(title, msg, cs_alarm_idx);
					});
				}
			}catch(e){

			}

			setTimeout(function(){
				CSPopupAlarmGetMyAlarm();
			}, 10000);

		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * 알람 토스트 띄우기 (With Sound)
	 * @param title         : 타이틀
	 * @param msg           : 내용
	 * @param cs_alarm_idx  : 알람 해제를 위한 알람 IDX
	 * @constructor
	 */
	var CSPopupAlarmToast = function(title, msg, cs_alarm_idx){

		msg = msg.replace(/\n/g, '<br>');

		$.toast({
			text: msg, // Text that is to be shown in the toast
			heading: title, // Optional heading to be shown on the toast

			showHideTransition: 'slide', // fade, slide or plain
			allowToastClose: true, // Boolean value true or false
			hideAfter: false, // false to make it sticky or number representing the miliseconds as time after which toast needs to be hidden
			stack: 10, // false if there should be only one toast at a time or a number representing the maximum number of toasts to be shown at a time
			position: 'top-right', // bottom-left or bottom-right or bottom-center or top-left or top-right or top-center or mid-center or an object representing the left, right, top, bottom values

			bgColor: '#880000',  // Background color of the toast
			textColor: '#eeeeee',  // Text color of the toast
			textAlign: 'left',  // Text alignment i.e. left, right or center
			loader: true,  // Whether to show loader or not. True by default
			loaderBg: '#9EC600',  // Background color of the toast loader
			beforeShow: function () {}, // will be triggered before the toast is shown
			afterShown: function () {
				ion.sound.play("alarm_sound");
				CSPopupAlarmClearAlarm(cs_alarm_idx);
			}, // will be triggered after the toat has been shown
			beforeHide: function () {}, // will be triggered before the toast gets hidden
			afterHidden: function () {}  // will be triggered after the toast has been hidden
		});
	};

	/**
	 * 알람 해제 하기
	 * @param cs_alarm_idx
	 * @constructor
	 */
	var CSPopupAlarmClearAlarm = function(cs_alarm_idx){
		var p_url = "/cs/cs_alarm_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "clear_my_alarm_list";
		dataObj.cs_alarm_idx = cs_alarm_idx;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {


		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * CS 이력 팝업창 오픈
	 * @param order_idx
	 * @constructor
	 */
	var CSHistoryPopupOpen = function(order_idx){
		Common.newWinPopup("cs_pop_history.php?order_idx="+order_idx, "cs_history", 750, 800, "no");
	};

	/**
	 * CS 이력 팝업 페이지 초기화
	 * @constructor
	 */
	var CSHistoryInit = function(){

		//검색버튼 바인딩
		$(".btn_searchBar_pop").on("click", function(e){
			e.preventDefault();
			CSHistoryGetList();
		});

		//폼전송 방지
		$("#searchFormPop").on("submit", function(e){
			e.preventDefault();
			CSHistoryGetList();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				CSHistoryGetList();
			}
		});

		//CS 이력 내용 비우기
		$(".cs_list tbody").empty();

		$("select[name='cs_task']").on("change", function(){

			CSHistoryGetList();

		}).trigger("change");
	};

	var CSHistoryGetList = function(){
		//이력 가져오기
		p_url = "/cs/cs_cs_list_ajax.php";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("#searchFormPop").serialize()
		}).done(function (response) {
			$(".cs_list tbody").empty();
			if(response.result){

				$.each(response.data, function(i, o){
					//console.log(o);
					var msg = o.cs_comment.replace(/\n/g, '<br>');
					var is_auto_cs = (o.cs_is_auto == "Y") ? "auto_hide" : "";

					var is_confirm = (o.cs_confirm == "Y") ? '<span class="color_blue bold">완료</span>' : '<span class="color_red bold">미처리</span>';

					var has_file = false;
					var file_list = [];

					if(o.cs_file_idx1 > 0){
						has_file = true;
						file_list.push(o.cs_file_idx1 + '|' + o.filename1);
					}
					if(o.cs_file_idx2 > 0){
						has_file = true;
						file_list.push(o.cs_file_idx2 + '|' + o.filename2);
					}
					if(o.cs_file_idx3 > 0){
						has_file = true;
						file_list.push(o.cs_file_idx3 + '|' + o.filename3);
					}
					if(o.cs_file_idx4 > 0){
						has_file = true;
						file_list.push(o.cs_file_idx4 + '|' + o.filename4);
					}
					if(o.cs_file_idx5 > 0) {
						has_file = true;
						file_list.push(o.cs_file_idx5 + '|' + o.filename5);
					}

					var rowSpan = 1;
					if(msg != "" && has_file) {
						var rowSpan = 2;
					}

					var html = '' +
						'<tr>' +
						'<td rowspan="'+rowSpan+'">'+Common.toDateTimeOnlyDate(o.cs_regdate)+'<br>'+Common.toDateTimeOnlyTime(o.cs_regdate)+'<br><br>'+o.member_id+'</td>' +
						'<th>작업</th>' +
						'<td class="text_left color_blue bold">'+o.cs_task_name + ' ' + o.cs_reason_text+'</td>' +
						'<th>처리</th>' +
						'<td class="text_left">'+is_confirm+'</td>' +
						'</tr>' +
						'<tr>';

					if(msg != "" && has_file) {

						html += '<td colspan="4" class="text_left">' + msg;

						if (has_file) {
							html += '<div class="row">\n';
							$.each(file_list, function (ii, oo) {

								var o_ary = oo.split("|");
								var file_idx = o_ary[0];
								var user_filename = o_ary[1];
								var save_filename = o_ary[2];

								var extension = user_filename.substr((user_filename.lastIndexOf('.') + 1)).toLocaleLowerCase();

								var link_class = "cs_file_down";
								var is_img = false;
								var link_text = user_filename;
								var href_url = "javascript:;";
								var lightbox_attr = "";

								if (extension == "png" || extension == "jpg" || extension == "jpeg" || extension == "gif") {
									is_img = true;
									link_class = "cs_img_thumb";
									link_text = "";
									href_url = "/_data/cs/" + save_filename;
									lightbox_attr = 'data-lightbox="cs_img_thumb_' + o.cs_idx + '"';
								}

								html += '<div class="file_box"><a href="' + href_url + '" ' + lightbox_attr + ' data-file_idx="' + file_idx + '" data-filename="' + save_filename + '" class="link_default ' + link_class + '">' + user_filename + '</a></div>\n';
							});
							html += '</div>\n';
						}

						html += '</td>' +
							'</tr>';
					}

					html += '</div>';
					$(".cs_list tbody").append(html);
				});

				//CS 내역 첨부파일 이미지 썸네일 보기
				CSPopupCSListImgThumb();

				lightbox.option({
					'resizeDuration': 100,
					'fadeDuration': 200,
					'imageFadeDuration': 200,
					'albumLabel': "첨부파일 이미지 %1/%2",
				})

				//CS 내역 첨부파일 다운로드 바인딩
				$(".cs_file_down").on("click", function(){
					Common.simpleUploadedFileDown($(this).data("file_idx"), $(this).data("filename"));
				});

			}
		}).fail(function(jqXHR, textStatus){
		});
	};

	/**
	 * CS 창 Grid Reload
	 * @param top_fix       : 상단 고정여부
	 * @param middle_fix    : 중단 고정여부
	 * @constructor
	 */
	var CSPopupReload = function(top_fix, middle_fix){
		if(top_fix) {
			//상단 리스트 선택 상태 고정
			_CSPopupListTopHoldSelection = true;
		}

		if(middle_fix) {
			//중단 리스트 선택 상태 고정
			_CSPopupListMidHoldSelection = true;
		}
		//상단 리스트 reload
		$("#list_top").trigger("reloadGrid");
	};

	return {
		CSPopupInit : function(){
			CSPopupInit();
		},
		CSPopupTabAdd: function(){
			CSPopupTabAdd();
		},
		CSPopupOrderWriteInit: function(){
			CSPopupOrderWriteInit();
		},
		CSPopupOrderWriteProductAddPopupInit: function(){
			CSPopupOrderWriteProductAddPopupInit();
		},
		CSPopupOrderWriteProductSelect: function(product_idx, product_option_idx, product_name, product_option_name){
			CSPopupOrderWriteProductSelect(product_idx, product_option_idx, product_name, product_option_name);
		},
		CSpopupOrderHoldInit: function(){
			CSpopupOrderHoldInit();
		},
		CSPopupCSWritePopInit: function(){
			CSPopupCSWritePopInit();
		},
		CSPopupAddressChangeInit: function(){
			CSPopupAddressChangeInit();
		},
		CSPopupInvoiceChangeInit: function(){
			CSPopupInvoiceChangeInit();
		},
		CSPopupShippedChangeInit: function(){
			CSPopupShippedChangeInit();
		},
		CSPopupPackageAddInit: function(){
			CSPopupPackageAddInit();
		},
		OrderPackageExceptExecOnePopupInit: function(){
			OrderPackageExceptExecOnePopupInit();
		},
		CSPopupPackageLockInit: function(){
			CSPopupPackageLockInit();
		},
		CSPopupOrderCancelAllInit: function(){
			CSPopupOrderCancelAllInit();
		},
		CSPopupOrderRestoreAllInit: function(){
			CSPopupOrderRestoreAllInit();
		},
		CSPopupOrderCancelOneInit: function(){
			CSPopupOrderCancelOneInit();
		},
		CSPopupOrderRestoreOneInit: function(){
			CSPopupOrderRestoreOneInit();
		},
		CSPopupOrderReturnInit: function(order_pack_idx, cs_status){
			CSPopupOrderReturnInit(order_pack_idx, cs_status)
		},
		CSPopupStockReturnInit: function(order_pack_idx, cs_status){
			CSPopupStockReturnInit(order_pack_idx, cs_status)
		},
		CSPopupProductChangeInit: function(){
			CSPopupProductChangeInit();
		},
		CSPopupProductAddInit: function(){
			CSPopupProductAddInit();
		},
		CSPopupOrderCopyOneInit: function(){
			CSPopupOrderCopyOneInit();
		},
		CSPopupOrderCopyWholeInit: function(){
			CSPopupOrderCopyWholeInit();
		},
		CSPopupOrderDeleteInit: function(){
			CSPopupOrderDeleteInit();
		},
		CSPopupMatchingDeleteInit: function(){
			CSPopupMatchingDeleteInit();
		},
		CSPopupOrderReturnDueInit: function(){
			CSPopupOrderReturnDueInit();
		},
		CSPopupInvoicePriorityInit: function(){
			CSPopupInvoicePriorityInit();
		},
		CSHistoryInit: function(){
			CSHistoryInit();
		},
		CSPopupReload: function(top_fix, middle_fix){
			CSPopupReload(top_fix, middle_fix);
		},
	}

})();

function checkBox(e)
{
	e = e||event;/* get IE event ( not passed ) */
	e.stopPropagation? e.stopPropagation() : e.cancelBubble = true;
}