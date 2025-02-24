/*
 * 정산통계 - 매입/지출관리 js
 */
var SettlePurchase = (function() {
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
	 * 거래현황 페이지 초기화
	 * @constructor
	 */
	var TransactionStateInit = function(){
		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				TransactionStateSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			TransactionStateSearch();
		});

		TransactionStateSearch();

		//매출현황(외상매출금) 저장 버튼
		$("#btn-save-sale-credit").on("click", function(){
			TransactionStateSaveTableData(".sale-credit", "save_transaction_table", "SALE_CREDIT_IN_AMOUNT");
		});

		//매입현황(외상매입금) 저장 버튼
		$("#btn-save-purchase-credit").on("click", function(){
			TransactionStateSaveTableData(".sale-credit", "save_transaction_table", "SALE_CREDIT_IN_AMOUNT");
		});

		//매입현황(선급금) 저장 버튼
		$("#btn-save-purchase-prepay").on("click", function(){
			TransactionStateSaveTableData(".purchase-prepay", "save_transaction_table", "PURCHASE_PREPAY_IN_AMOUNT")
		});

		$("#btn-add-sale-etc, #btn-add-purchase-etc").on("click", function(){
			var tran_type = $(this).data("tran_type");

			if(tran_type == "SALE_ETC"){

			}else if(tran_type == "PURCHASE_ETC"){

			}

			Common.newWinPopup("transaction_state_etc_add_pop.php?tran_type="+tran_type, 'settle_product_search_pop', 1200, 550, 'yes');
		});

		$("#transaction_state_date").on("change", function(){
			$(".prev_date").text(moment($(this).val()).subtract(6, 'days').format('YYYY-MM-DD'));
		});

		//메모 수정
		$("body").on("click", ".btn-sale-credit-modify", function(){
			TransactionStateEditPop($(this).data("tran_type"), $(this).data("idx"), $(this).data("name"), $(this).data("will"), $(this).data("today"), $(this).data("tran"), $(this).data("total"));
		});

		//다운로드 버튼
		$(".btn-xls-down").on("click", function(){
			TransactionStateXlsDown();
		});

	};

	var __TransactionStateSearchDate = "";
	var __TransactionStateTranType = "";

	/**
	 * 거래현황 목록/검색
	 * @constructor
	 */
	var TransactionStateSearch = function(){
		__TransactionStateSearchDate = $("#transaction_state_date").val();
		TransactionStateGetTableData(".sale-credit", "get_sale_credit", "SALE_CREDIT_IN_AMOUNT");
		TransactionStateGetTableData(".sale-prepay", "get_sale_prepay", "SALE_PREPAY_IN_AMOUNT");

		TransactionStateGetTableData(".purchase-credit", "get_purchase_credit", "PURCHASE_CREDIT_IN_AMOUNT");
		TransactionStateGetTableData(".purchase-prepay", "get_purchase_prepay", "PURCHASE_PREPAY_IN_AMOUNT");

		if($("#period").val() == "day") {
			TransactionStateGetTableData(".sale-etc", "get_sale_etc", "SALE_ETC");
			TransactionStateGetTableData(".purchase-etc", "get_purchase_etc", "PURCHASE_ETC");
		}
	};

	/**
	 * 거래현황 - 매출현황 2개, 매입현황 2개 테이블 데이터 가져오기
	 * @constructor
	 */
	var TransactionStateGetTableData = function(table_class, ajax_mode, tran_type){
		$(table_class + " tbody").empty();
		tableLoader.on(table_class);

		var p_url = "transaction_state_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = ajax_mode;
		dataObj.period_type = $("#period").val();
		dataObj.tran_type = tran_type;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='searchForm']").serialize() + '&' + $.param(dataObj)
		}).done(function (response) {
			if(response)
			{
				var total = new Object();
				total.prev_total = 0;
				total.today_settle_amount = 0;
				total.today_tran_amount = 0;
				total.today_total = 0;
				$.each(response.data, function(i, o){
					var target_idx = o.customer_idx;
					var customer_name = o.customer_name;

					//hardcoding for 회계 20200316
					if(customer_name.indexOf("덕윤(") !== -1) return true;

					var prev_settle_amount = parseInt(o.prev_settle_amount);
					var today_settle_amount = parseInt(o.today_settle_amount);
					var prev_tran_amount = parseInt(o.prev_tran_amount);
					var today_tran_amount = parseInt(o.today_tran_amount);
					var today_tran_memo = o.today_tran_memo;

					var sum_due = 0;
					var sum_order_cnt = o.sum_order_cnt;


					/**
					 *각 테이블 마다 계산 방식이 다름
					 */
					if(tran_type == "SALE_CREDIT_IN_AMOUNT" || tran_type == "SALE_PREPAY_IN_AMOUNT") {
						//매출현황 (외상매출금/선입금)

						var prev_total = prev_settle_amount - prev_tran_amount;
						var today_total = prev_total + today_settle_amount - today_tran_amount;

					}else if(tran_type == "PURCHASE_CREDIT_IN_AMOUNT" || tran_type == "PURCHASE_PREPAY_IN_AMOUNT"){
						//매입현황 (외상매입금/선급금)

						var prev_total = prev_settle_amount - prev_tran_amount;
						var today_total = prev_total + today_settle_amount - today_tran_amount;

					}else if(tran_type == "SALE_ETC" || tran_type == "PURCHASE_ETC"){
						prev_total = prev_settle_amount;
						today_total = o.tran_remain_amount;
						var tran_idx = o.tran_idx
					}

					total.prev_total += prev_total;
					total.today_settle_amount += today_settle_amount;
					total.today_tran_amount += today_tran_amount;
					total.today_total += today_total;

					//매출현황(선입금) - 벤더사 판매처 일 경우 잔액을 마이너스로 표시
					if(tran_type == "SALE_PREPAY_IN_AMOUNT" || tran_type == "PURCHASE_PREPAY_IN_AMOUNT"){
						prev_total = prev_total * -1;
						today_total = today_total * -1;
					}

					var html = "";
					html =  '<tr>' +
						'<td class="text_left">'+customer_name+'</td>' +
						'<td class="text_right td_prev" data-val="'+prev_total+'">'+Common.addCommas(prev_total)+'</td>' +
						'<td class="text_right td_today" data-val="'+today_settle_amount+'">'+Common.addCommas(today_settle_amount)+'</td>';

					//if(tran_type != "SALE_PREPAY_IN_AMOUNT" && tran_type != "SALE_ETC" && tran_type != "PURCHASE_ETC" && $("#period").val() == "day") {
					//	html += '<td class="text_right"><input type="text" class="w80px onlyNumberDynamic input_tran_amount" value="' + today_tran_amount + '" /></td>';
					//}else{
						html += '<td class="text_right td_tran" data-val="'+today_tran_amount+'">' + Common.addCommas(today_tran_amount) + '</td>';
					//}

					html += '<td class="text_right td_total" data-val="'+today_total+'">'+Common.addCommas(today_total)+'</td>' +
						'<td class="text_left">'+today_tran_memo+'</td>';

					if(tran_type == "SALE_ETC" || tran_type == "PURCHASE_ETC") {
						html += '<td><a href="javascript:;" data-idx="' + tran_idx + '" data-name="' + customer_name + '" data-tran_type="' + tran_type + '" data-will="' + prev_total + '" data-today="' + today_settle_amount + '" data-tran="' + today_tran_amount + '" data-total="' + today_total + '" class="xsmall_btn btn-sale-credit-modify" tabindex="-1">수정</a></td>';
					}else if(tran_type == "SALE_PREPAY_IN_AMOUNT" || $("#period").val() == "week" || $("#period").val() == "month") {
						html += '<td></td>';
					}else{
						html += '<td><a href="javascript:;" data-idx="' + target_idx + '" data-name="' + customer_name + '" data-tran_type="' + tran_type + '" data-will="' + prev_total + '" data-today="' + today_settle_amount + '" data-tran="' + today_tran_amount + '" data-total="' + today_total + '" class="xsmall_btn btn-sale-credit-modify" tabindex="-1">메모</a></td>';
					}
					html += '</tr>';

					$(table_class + " tbody").append(html);

				});


				//매출현황(선입금) - 벤더사 판매처 일 경우 잔액을 마이너스로 표시
				if(tran_type == "SALE_PREPAY_IN_AMOUNT" || tran_type == "PURCHASE_PREPAY_IN_AMOUNT"){
					total.prev_total = total.prev_total * -1;
					total.today_total = total.today_total * -1;
				}


				var html = "";
				html =  '<tr>' +
					'<th>합계</th>' +
					'<th class="text_right">'+Common.addCommas(total.prev_total)+'</th>' +
					'<th class="text_right">'+Common.addCommas(total.today_settle_amount)+'</th>' +
					'<th class="text_right">'+Common.addCommas(total.today_tran_amount)+'</th>' +
					'<th class="text_right">'+Common.addCommas(total.today_total)+'</th>' +
					'<th></th>' +
					'<th></th>' +
					'</tr>';

				$(table_class + " tbody").append(html);

				$(table_class + " tbody").on("keyup", ".input_tran_amount", function(){
					var prev = Number($(this).parent().parent().find(".td_prev").data("val"));
					var today = Number($(this).parent().parent().find(".td_today").data("val"));
					var tran = Number($(this).val());



					if(isNaN(prev)) prev = 0;
					if(isNaN(today)) today = 0;
					if(isNaN(tran)) tran = 0;

					var total = 0;


					if(tran_type == "SALE_CREDIT_IN_AMOUNT" || tran_type == "SALE_PREPAY_IN_AMOUNT") {
						total = prev + today - tran;
					}else if(tran_type == "PURCHASE_CREDIT_IN_AMOUNT" || tran_type == "PURCHASE_PREPAY_IN_AMOUNT")
					{
						//total = prev - today + tran;
						total = prev + today - tran;
					}

					//매출현황(선입금) - 벤더사 판매처 일 경우 잔액을 마이너스로 표시
					if(tran_type == "SALE_PREPAY_IN_AMOUNT" || tran_type == "PURCHASE_PREPAY_IN_AMOUNT"){
						total = total * -1;
					}

					$(this).parent().parent().find(".td_total").text(Common.addCommas(total));


				});

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(table_class);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(table_class);
		});
	};


	/**
	 * 거래현황 다운로드
	 * @constructor
	 */
	var TransactionStateXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "transaction_state_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_TRANSACTION_STATE", function(){
				SettlePurchase.TransactionStateXlsDownComplete();
			});
		}, 500);
	};

	var TransactionStateXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 메모 수정 팝업
	 * @param tran_type
	 * @param target_idx
	 * @param target_name
	 * @param will_amount
	 * @param today_amount
	 * @param tran_amount
	 * @param total_remain
	 * @constructor
	 */
	var TransactionStateEditPop = function(tran_type, target_idx, target_name, will_amount, today_amount, tran_amount, total_remain){

		var p_url = "transaction_state_pop.php";
		if(tran_type == "SALE_ETC" || tran_type == "PURCHASE_ETC"){
			p_url = "transaction_state_pop_etc.php";
		}

		__TransactionStateTranType = tran_type;

		var dataObj = new Object();
		dataObj.tran_type = tran_type;
		dataObj.date = __TransactionStateSearchDate;
		dataObj.target_idx = target_idx;
		dataObj.target_name = target_name;
		dataObj.will_amount = will_amount;
		dataObj.today_amount = today_amount;
		dataObj.tran_amount = tran_amount;
		dataObj.total_remain = total_remain;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				PopupCommonPopOpen(600, 0, "수정", response);
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
	 * 각 테이블 별 몽땅 저장
	 * @param table_class
	 * @param ajax_mode
	 * @param tran_type
	 * @constructor
	 */
	var TransactionStateSaveTableData = function(table_class, ajax_mode, tran_type){
		if(!confirm('저장하시겠습니까?')){
			return;
		}

		var tran_list = new Array();
		var row_info = new Object();

		var cnt = 0;
		$(table_class + " tbody tr").each(function(i, o){

			row_info = new Object();

			var $input = $(this).find(".input_tran_amount");
			var $btn = $(this).find(".btn-sale-credit-modify");


			if($input.length == 1){

				row_info.target_idx = $btn.data("idx");
				row_info.tran_amount = $input.val();

				tran_list.push(row_info);
			}
			cnt++;
		});

		if(cnt > 0){

			__TransactionStateTranType = tran_type;

			var p_url = "transaction_state_proc_ajax.php";
			var dataObj = new Object();
			dataObj.mode = ajax_mode;
			dataObj.tran_type = tran_type;
			dataObj.date = __TransactionStateSearchDate;
			dataObj.tran_list = tran_list;
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if(response.result)
				{
					if(__TransactionStateTranType == "SALE_CREDIT_IN_AMOUNT"){
						TransactionStateGetTableData(".sale-credit", "get_sale_credit", "SALE_CREDIT_IN_AMOUNT");
					}else if(__TransactionStateTranType == "PURCHASE_CREDIT_IN_AMOUNT"){
						TransactionStateGetTableData(".purchase-credit", "get_purchase_credit", "PURCHASE_CREDIT_IN_AMOUNT");
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

		}
	};

	/**
	 * 메모 수정 팝업 페이지 초기화
	 * @constructor
	 */
	var TransactionStateEditPopInit = function(){
		$("input[name='tran_amount']").on("keyup blur", function(){
			var rst = Number($(this).data("will")) + Number($(this).data("today")) - Number($(this).val());
			$(".tran_remain").html(Common.addCommas(rst));
		}).trigger("keyup");


		$("#btn-save-pop").on("click", function(){

			/*
			var tran_amount = $("input[name='tran_amount']").val();
			if($.trim(tran_amount) == ""){
				alert("금액을 입력해주세요.");
				return;
			}
			*/

			var p_url = "transaction_state_proc_ajax.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("#dyFormStatePop").serialize()
			}).done(function (response) {
				if(response.result)
				{
					if(__TransactionStateTranType == "SALE_CREDIT_IN_AMOUNT"){
						TransactionStateGetTableData(".sale-credit", "get_sale_credit", "SALE_CREDIT_IN_AMOUNT");
					}else if(__TransactionStateTranType == "PURCHASE_CREDIT_IN_AMOUNT"){
						TransactionStateGetTableData(".purchase-credit", "get_purchase_credit", "PURCHASE_CREDIT_IN_AMOUNT");
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
	 * 기타 현황 추가 팝업 페이지 초기화
	 * @constructor
	 */
	var TransactionStateEtcPopInit = function(){

		$("#btn-pop-save").on("click", function(){
			$("#searchFormPop").submit();
		});

		$("#searchFormPop").on("submit", function(){
			if(!confirm('저장하시겠습니까?')){
				return false;
			}
		});
	};

	/**
	 * 기타 현황 수정 팝업 페이지 초기화
	 * @constructor
	 */
	var TransactionStateEditEtcPopInit = function(){

		$("#btn-save-pop").on("click", function(){
			var p_url = "transaction_state_proc_ajax.php";
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("#dyFormStatePop").serialize()
			}).done(function (response) {
				if(response.result)
				{
					if(__TransactionStateTranType == "SALE_ETC"){
						TransactionStateGetTableData(".sale-etc", "get_sale_etc", "SALE_ETC");
					}else if(__TransactionStateTranType == "PURCHASE_ETC"){
						TransactionStateGetTableData(".purchase-etc", "get_purchase_etc", "PURCHASE_ETC");
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
		TransactionStateInit: TransactionStateInit,
		TransactionStateEditPopInit: TransactionStateEditPopInit,
		TransactionStateEtcPopInit: TransactionStateEtcPopInit,
		TransactionStateGetTableData: TransactionStateGetTableData,
		TransactionStateEditEtcPopInit: TransactionStateEditEtcPopInit,
		TransactionStateXlsDownComplete: TransactionStateXlsDownComplete,
	}
})();
$(function(){
	SettlePurchase.init();
});