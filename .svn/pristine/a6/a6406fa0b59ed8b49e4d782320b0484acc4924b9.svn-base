/*
 * 모바일 - 거래현황 관련 js
 */
var TransactionState = (function() {
	var root = this;

	var init = function(){
		//폼 전송 방지
		$("#dyForm").on("submit", function(e){
			e.preventDefault();
		});

		//일별,주별,월별 버튼
		$(".btn-period").on("click", function(){
			$(".btn-period").removeClass("on");
			$(this).addClass("on");

			var val = $(this).data("period");
			$("#period").val(val);

			if(val == "day"){
				$(".select_set.day").removeClass("dis_none");
				$(".select_set.day .txt").addClass("dis_none");
				$(".select_set.month").addClass("dis_none");

				$(".wrap_purchase-etc, .wrap_sale-etc").removeClass("dis_none");
			}else if(val == "week"){
				$(".select_set.day").removeClass("dis_none");
				$(".select_set.day .txt").removeClass("dis_none");
				$(".select_set.month").addClass("dis_none");

				$(".wrap_purchase-etc, .wrap_sale-etc").addClass("dis_none");
			}else if(val == "month"){
				$(".select_set.day").addClass("dis_none");
				$(".select_set.month").removeClass("dis_none");

				$(".wrap_purchase-etc, .wrap_sale-etc").addClass("dis_none");
			}

			$(".table_style05 tbody").empty();
		});

		//날짜 설정 : 기간 1주일
		$("#transaction_state_date").on("change", function(){
			var date_end = $(this).val();
			var date_start = moment(date_end).add(-6, "days").format("YYYY-MM-DD");
			$(".prev_date").text(date_start + " ~ ");
		}).trigger("change");

		//검색 버튼
		$("#btn-search").on("click", function(){
			TransactionStateSearch();
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

		showLoaderM();

		var p_url = "/settle/transaction_state_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = ajax_mode;
		dataObj.period_type = $("#period").val();
		dataObj.tran_type = tran_type;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='dyForm']").serialize() + '&' + $.param(dataObj)
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
					var prev_settle_amount = o.prev_settle_amount;
					var today_settle_amount = o.today_settle_amount;
					var prev_tran_amount = o.prev_tran_amount;
					var today_tran_amount = o.today_tran_amount;
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
						today_total = o.tran_remain_amount
						var tran_idx = o.tran_idx
					}

					total.prev_total += prev_total;
					total.today_settle_amount += today_settle_amount;
					total.today_tran_amount += today_tran_amount;
					total.today_total += today_total;

					//매출현황(선입금) - 벤더사 판매처 일 경우 잔액을 마이너스로 표시
					if(tran_type == "SALE_PREPAY_IN_AMOUNT"){
						prev_total = prev_total * -1;
						today_total = today_total * -1;
					}

					var html = "";
					html =  '<tr>' +
						'<td class="text_left">'+customer_name+'</td>' +
						'<td class="text_right td_prev" data-val="'+prev_total+'">'+Common.addCommas(prev_total)+'</td>' +
						'<td class="text_right td_today" data-val="'+today_settle_amount+'">'+Common.addCommas(today_settle_amount)+'</td>';

					html += '<td class="text_right">' + Common.addCommas(today_tran_amount) + '</td>';

					html += '<td class="text_right td_total">'+Common.addCommas(today_total)+'</td>' +
						'<td class="text_left">'+today_tran_memo+'</td>';

					html += '</tr>';

					$(table_class + " tbody").append(html);

				});


				//매출현황(선입금) - 벤더사 판매처 일 경우 잔액을 마이너스로 표시
				if(tran_type == "SALE_PREPAY_IN_AMOUNT"){
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

				$(".btn-sale-credit-modify").on("click", function(){
					TransactionStateEditPop($(this).data("tran_type"), $(this).data("idx"), $(this).data("name"), $(this).data("will"), $(this).data("today"));
				});
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
					if(tran_type == "SALE_PREPAY_IN_AMOUNT"){
						total = total * -1;
					}

					$(this).parent().parent().find(".td_total").text(Common.addCommas(total));


				});

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoaderM();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoaderM();
		});
	};

	return {
		init: init,
	}
})();

$(function(){
	TransactionState.init();
});
