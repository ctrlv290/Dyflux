
var SMSAlimtalk = (function() {
	var root = this;

	var init = function () {
	};

	/**
	 * 확장주문검색 페이지 초기화
	 * @constructor
	 */
	var SMSAlimTalkInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "1");

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
				placeholder: '00:00:00',
				inputFormat: 'HH:MM:ss',
				alias: 'hh:mm:ss',
				showMaskOnHover: false,
				hourFormat: 24
			}
		);


		//발송 버튼 바인딩
		$('#send_btn').on("click", function(){

			if($('#sms_con').val() == '') {
				alert('전송할 메세지를 입력하세요');
				return false;
			}

			var pdata = new Array();
			var id = $("#grid_list").getGridParam('selarrrow');
			var ids = $("#grid_list").jqGrid('getDataIDs');
			var count = 0;
			for (var i = 0; i < ids.length; i++) {
				var check = false;
				$.each(id, function (index, value) {
					if (value == ids[i])
						check = true;
				});

				if (check) {
					var rowdata = $("#grid_list").getRowData(ids[i]);
					//pdata.push(rowdata); //배열에 맵처럼 담김

					var obj = new Object();
					obj.idx  = rowdata.order_idx;
					obj.hp  = rowdata.receive_hp_num;
					obj.name  = rowdata.receive_name;
					pdata.push(obj); //배열에 맵처럼 담김
					count++;
				}
			}
			//console.log(count);

			if(count == 0) {
				alert('받는 사람을 선택하세요');
				return false;
			}

			var sms_tit = $('#sms_title').val();
			var sms_msg = $('#sms_con').val();
			var mecro_idx = $('#mecro_idx').val();
			var sms_sender = $('#sms_sender option:selected').val();

			if(sms_sender == '') {
				alert('보내는 사람을 선택하세요');
				return false;
			}

			if(confirm('선택한 수령자들에게 메세지를 전송하시겠습니까?')) {
				showLoader();
				$.ajax({
					type: 'POST',
					url: 'sms_send_proc.php',
					dataType: "json",
					data: {'chk_data': JSON.stringify(pdata), 'sms_msg' : sms_msg, 'sms_tit' : sms_tit, 'mode': 'PUBLIC', 'mecro_idx' : mecro_idx, 'sms_sender' : sms_sender}
				}).done(function (response) {
					if (response.result) {
						alert('발송 되었습니다');
						location.reload();
					} else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
				}).fail(function (jqXHR, textStatus) {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
				});
			}
		});

		//알림톡 발송 버튼 바인딩
		$('#send_al_btn').on("click", function() {

			if($('#sms_con').val() == '') {
				alert('템플릿을 선택해주세요');
				return false;
			}

			if($('#tp_replace_ex_code').val() == '') {
				if (!confirm('템플릿 변수값에 매칭된 내역이 없습니다. 계속 진행하시겠습니까?')) {
					return false;
				}
			}

			// if($('#tp_replace_ex_code').val() == '') {
			// 	alert('템플릿 변수값을 매칭해주세요');
			// 	return false;
			// }

			var pdata = new Array();
			var id = $("#grid_list").getGridParam('selarrrow');
			var ids = $("#grid_list").jqGrid('getDataIDs');
			var count = 0;
			for (var i = 0; i < ids.length; i++) {
				var check = false;
				$.each(id, function (index, value) {
					if (value == ids[i])
						check = true;
				});

				if (check) {
					var rowdata = $("#grid_list").getRowData(ids[i]);
					pdata.push(rowdata); //배열에 맵처럼 담김
					count++;
				}
			}
			//console.log(count);

			if(count == 0) {
				alert('받는 사람을 선택하세요');
				return false;
			}

			var sms_tit = $('#sms_title').val();
			var sms_msg = $('#sms_con').val();
			var mecro_idx = $('#mecro_idx').val();
			var sms_sender = $('#sms_sender option:selected').val();
			var rp_ex_code = $('#tp_replace_ex_code').val();
			var tp_rp_code = $('#tp_replace_code').val();
			var tp_code = $('#tp_code').val();

			if(confirm('메세지를 전송하시겠습니까?')) {
				showLoader();
				$.ajax({
					type: 'POST',
					url: 'sms_send_proc.php',
					dataType: "json",
					data: {'chk_data': JSON.stringify(pdata), 'sms_msg' : sms_msg, 'sms_tit' : sms_tit, 'mode': 'SMS_AL_SEND', 'mecro_idx' : mecro_idx, 'sms_sender' : sms_sender, 'rp_ex_code' : rp_ex_code,'tp_rp_code' : tp_rp_code, 'tp_code' : tp_code}
				}).done(function (response) {
					if (response.result) {
						alert('발송 되었습니다');
						//location.reload();
					} else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
				}).fail(function (jqXHR, textStatus) {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
				});
			}
		});

		//탭 Init..
		$('#sms_tab').on("click", function() {
			$('#al_tab').removeClass('on');
			$(this).addClass('on');

			$('#sms_div').show();
			$('#al_div').hide();
			$('.send_btn_div_1').show();
			$('.send_btn_div_2').hide();
			$('#sms_con').val('');

			$(".forSMSContent").show();
		});

		$('#al_tab').on("click", function() {
			$('#sms_tab').removeClass('on');
			$(this).addClass('on');

			$('#sms_div').hide();
			$('#al_div').show();
			$('.send_btn_div_1').hide();
			$('.send_btn_div_2').show();
			$('#sms_con').val('');

			$(".forSMSContent").hide();
		});

		//톨팁
		$( ".toolTipTrigger" ).tooltip({
			show: {
				effect: "slideDown",
				delay: 250
			}
		});

		//글자수
		$('#sms_con').keyup(function(){
			bytesHandler(this);
		});



		$('#sms_personal').on("click", function() {
			var url = '/sms/sms_personal_send.php';
			Common.newWinPopup(url, 'sms_personal_pop', 1024, 720, 'yes');
		});

		//템플릿 선택 팝업창
		$( "#modal_sms_template_list" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		//템플릿 팝업
		$('#al_template').on("click", function() {
			var p_url = "sms_template_list.php";
			var dataObj = new Object();

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					$("#modal_sms_template_list").html(response);
					$("#modal_sms_template_list").dialog( "open" );
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});

		//템플릿 선택 팝업창
		$( "#modal_sms_template_match" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		$('#al_template_match').on("click", function() {
			var tp_replace_code = $('#tp_replace_code').val();
			var p_url = "sms_template_match.php";
			var dataObj = new Object();
			dataObj.rp_code = tp_replace_code;
			//dataObj.rs_codes = $("#grid_list").getRowData(1);

			var rs_codes = new Object();
			var colModel = $("#grid_list").jqGrid ('getGridParam', 'colModel');
			$.each(colModel, function(i, o){
				rs_codes[o.name] = o.label;
			});
			dataObj.rs_codes = rs_codes;


			//console.log(dataObj);

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					$("#modal_sms_template_match").html(response);
					$("#modal_sms_template_match").dialog( "open" );
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});

		//SMS 매크로 모달팝업 세팅
		$( "#modal_sms_mecro_list" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) {
				$("#modal_sms_mecro_list").html('');
				windowScrollShow();
			},
		});

		$('#sms_mecro').on("click", function() {
			var p_url = "sms_mecro_list.php";
			var dataObj = new Object();
			dataObj.mode = $('#lmode').val();

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					$("#modal_sms_mecro_list").html(response);
					$("#modal_sms_mecro_list").dialog( "open" );
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});

		$('#sms_mecro_save').on("click", function(){
			var con = $('#sms_con').val();

			if(con == '') {
				alert('저장할 내용을 입력해주세요');
				return false;
			}
			var p_url = "sms_send_proc.php";
			var dataObj = new Object();
			dataObj.mode = 'MECRO_INSERT';
			dataObj.sms_msg = con;

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					alert('처리 되었습니다.');
					location.reload();
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

		OrderSearchListGridInit();
	};

	/**
	 * 확장주문검색 목록 바인딩 jqGrid
	 * @constructor
	 */
	var OrderSearchListGridInit = function(){

		var grid_cookie_name = "sms_alimtalk_send";

		//상품재고조회 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './sms_search_list_grid.php',
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
				// { label:'상세', name: '상세', width: 70,formatter: function(cellvalue, options, rowobject){
				//         //console.log(rowobject);
				//         return '<a href="javascript:;" class="xsmall_btn btn-seller-modify-pop" data-idx="'+rowobject.order_idx+'">보기</a>';
				//     }, sortable: false
				// },
				// { label:'삭제', name: '삭제', width: 70,formatter: function(cellvalue, options, rowobject){
				//         //console.log(rowobject);
				//         return '<a href="javascript:;" class="xsmall_btn btn-seller-del-pop" data-idx="'+rowobject.order_idx+'">삭제</a>';
				//     }, sortable: false
				// },
				{label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: false, is_use : true},
				{label: '발주일', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return Common.toDateTime(cellvalue);
					}
				},
				{label: '관리번호', name: 'order_idx', index: 'order_idx', width: 100, is_use : true},
				{label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 120, sortable: false, is_use : true},
				{label: '주문자', name: 'order_name', index: 'order_name', width: 120, sortable: false, is_use : true},
				{label: '수령자', name: 'receive_name', index: 'receive_name', width: 100, sortable: false, is_use : true},
				{label: '수령자연락처', name: 'receive_hp_num', index: 'receive_hp_num', width: 100, sortable: false, is_use : true},
				{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 120, sortable: false, is_use : true},
				{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left', is_use : true},
				{label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left', is_use : true},
				{label: '주문수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
						//console.log(cellvalue);
						return Common.addCommas(cellvalue);
					}},
				{label: '송장번호', name: 'invoice_no', index: 'invoice_no', width: 80, sortable: false, is_use : true, hidden:false},
				{label: '현재고', name: 'current_stock_amount', index: 'current_stock_amount', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{label: '상태', name: 'order_progress_step_han', index: 'order_progress_step_han', width: 80, sortable: false, is_use : true},
				{label: '판매처상품명', name: 'market_product_name', index: 'market_product_name', width: 80, sortable: false, is_use : true,hidden:true},
				{label: '판매처옵션', name: 'market_product_option', index: 'market_product_option', width: 80, sortable: false, is_use : true, hidden:true},
				{label: '배송메모', name: 'receive_memo', index: 'receive_memo', width: 80, sortable: false, is_use : true, hidden:true},
			],
			rowNum: Common.jsSiteConfig.jqGridRowListBig[0],
			rowList: Common.jsSiteConfig.jqGridRowListBig,
			pager: '#grid_pager',
			sortname: 'A.order_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			multiselect : true,
			subGrid : true,
			subGridRowExpanded : function(subGridId, rowId) {
				_subGridRowExpanded(subGridId, rowId);
			},
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				$(".summary_order_amt_sum").text(Common.addCommas(userData.order_amt_sum));
				$(".summary_order_calculation_amt_sum").text(Common.addCommas(userData.order_calculation_amt_sum));

				//삭제 팝업
				$(".btn-seller-del-pop").on("click", function(){
					OrderDeletePopup($(this).data("idx"));
				});

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("alimtalk_send", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "alimtalk_send");
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		var _subGridRowExpanded = function(subGridId, rowId){
			var rowData = $("#grid_list").jqGrid ('getRowData', rowId);

			var strHtml  = "<div style='text-align:left;'>";
			strHtml += "<div class='' >";
			strHtml += "<div class=''>주문번호 : "+rowData.market_order_no+"</div>";
			strHtml += "<div class=''>판매처상품명 : "+rowData.market_product_name+"</div>";
			strHtml += "<div class=''>판매처옵션 : "+rowData.market_product_option+"</div>";
			strHtml += "<div class=''>수령자연락처 : "+rowData.receive_hp_num+"</div>";
			strHtml += "<div class='' style='margin-top:10px;'>++ 배송메모 ++</div>";
			strHtml += "<div class=''>내용 : "+rowData.receive_memo+"</div>";
			strHtml += "</div>";
			strHtml += "</div>";

			$("#"+subGridId).html(strHtml);
		};

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
				OrderSearchListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderSearchListSearch();
		});
	};

	/**
	 * 확장주문검색 목록/검색
	 * @constructor
	 */
	var OrderSearchListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var OrderDeletePopup = function(order_idx) {
		//lert('order_idx : ' + order_idx + ' \r\n 삭제 구현해야함');
		return false;

		var url = '/sms/sms_order_del.php';
		url += (order_idx != '') ? '?order_idx=' + order_idx : '';
		Common.newWinPopup(url, 'sms_order_del', 700, 720, 'yes');
	};


	var getTextLength = function(str) {
		var len = 0;

		for (var i = 0; i < str.length; i++) {
			if (escape(str.charAt(i)).length == 6) {
				len++;
			}
			len++;
		}
		return len;
	};

	var bytesHandler = function(obj){
		var text = $(obj).val();
		var r_lng = getTextLength(text);

		if(r_lng >= 90) {
			$('#m_cnt').text('2000');
		}else {
			$('#m_cnt').text('90');
		}

		if(r_lng >= 2000) {
			alert('LMS는 2000 바이트까지 전송가능합니다');
			var str = cut_str(obj, 2000);
			$(obj).val(str);
			$('#r_cnt').text(r_lng);
			return false;
		}
		//console.log(r_lng);
		$('#r_cnt').text(r_lng);
	};

	var cut_str = function(obj, lng){
		var text = $(obj).val();
		var leng = text.length;
		while(getTextLength(text) > lng){
			leng--;
			text = text.substring(0, leng);
		}
		return text;
	};

	return {
		SMSAlimTalkInit: SMSAlimTalkInit,
	}
})();