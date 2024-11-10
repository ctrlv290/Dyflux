/*
 * 모바일 - 자금일보 관련 js
 */
var Report = (function() {
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
			}else if(val == "week"){
				$(".select_set.day").removeClass("dis_none");
				$(".select_set.day .txt").removeClass("dis_none");
				$(".select_set.month").addClass("dis_none");
			}else if(val == "month"){
				$(".select_set.day").addClass("dis_none");
				$(".select_set.month").removeClass("dis_none");
			}

			$(".table_style05 tbody").empty();
		});

		//날짜 설정 : 기간 1주일
		$("#transaction_date").on("change", function(){
			var date_end = $(this).val();
			var date_start = moment(date_end).add(-6, "days").format("YYYY-MM-DD");
			$(".prev_date").text(date_start + " ~ ");
		}).trigger("change");

		//검색 버튼
		$("#btn-search").on("click", function(){

			if($("#period").val() == "month"){
				__ReportDate = $("#date_year").val() + "-" + Common.LeftPad($("#date_month").val(), 2) + "-01";

			}else {
				__ReportDate = $("#transaction_date").val();
			}

			RepotBankGetData(".bank_domestic", "DOMESTIC");
			RepotBankGetData(".bank_foreign", "FOREIGN");

			ReportListSearch();
		});

	};


	/**
	 * 계좌 데이터 불러오기
	 * @param table_class
	 * @param bank_type
	 * @constructor
	 */
	var RepotBankGetData = function(table_class, bank_type){

		showLoaderM();
		$(table_class + " tbody").empty();

		var p_url = "/settle/report_proc_ajax.php";
		var dataObj = new Object();
		dataObj.period = $("#period").val();
		dataObj.mode = "get_bank_data";
		dataObj.tran_date = __ReportDate;
		dataObj.bank_type = bank_type;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				var total = new Object();
				total.prev = 0;
				total.in = 0;
				total.out= 0;
				total.sum = 0;
				$.each(response.data, function(i, o){
					var bank_name = o.bank_name;
					var prev = Number(o.prev_sum);
					var in_amt = Number(o.tran_in);
					var out_amt = Number(o.tran_out);
					var sum_amt = Number(o.tran_sum);
					var today_sum = ((prev * 100) + (sum_amt * 100)) / 100;
					var memo = o.tran_memo;

					total.prev += prev;
					total.in += in_amt;
					total.out += out_amt;
					total.sum += today_sum;

					var html = "";
					html =  '<tr>' +
						'<td class="text_left">'+bank_name+'</td>' +
						'<td class="text_right ">'+Common.addCommas(prev)+'</td>' +
						'<td class="text_right ">'+Common.addCommas(in_amt)+'</td>' +
						'<td class="text_right ">'+Common.addCommas(out_amt)+'</td>' +
						'<td class="text_right ">'+Common.addCommas(today_sum)+'</td>' +
						'</tr>';

					$(table_class + " tbody").append(html);

				});

				var html = "";
				html =  '<tr>' +
					'<th>합계</th>' +
					'<th class="text_right">'+Common.addCommas(total.prev)+'</th>' +
					'<th class="text_right BANK_SUM_IN" data-bank_type="'+bank_type+'" data-amt="'+total.in+'">'+Common.addCommas(total.in)+'</th>' +
					'<th class="text_right BANK_SUM_OUT" data-bank_type="'+bank_type+'" data-amt="'+total.out+'">'+Common.addCommas(total.out)+'</th>' +
					'<th class="text_right">'+Common.addCommas(total.sum)+'</th>' +
					'</tr>';

				$(table_class + " tbody").append(html);

				if(bank_type == "DOMESTIC"){
					html = '';
					html =  '<tr>' +
						'<th>&nbsp;</th>' +
						'<th>&nbsp;</th>' +
						'<th class="COMPARE_RESULT_IN"></th>' +
						'<th class="COMPARE_RESULT_OUT"></th>' +
						'<th>&nbsp;</th>' +
						'</tr>';

					$(table_class + " tbody").append(html);

					ReportAmountCompare();
				}
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoaderM();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoaderM();
		});

	};

	/**
	 * 자금일보 각 항목 불러오기 실행
	 * @constructor
	 */
	var ReportListSearch = function(){
		ReportListGetData(".CASH_IN", "CASH_IN", "IN");
		ReportListGetData(".CASH_OUT", "CASH_OUT", "OUT");
		ReportListGetData(".BANK_CUSTOMER_IN", "BANK_CUSTOMER_IN", "IN");
		ReportListGetData(".BANK_CUSTOMER_OUT", "BANK_CUSTOMER_OUT", "OUT");
		ReportListGetData(".BANK_ETC_IN", "BANK_ETC_IN", "IN");
		ReportListGetData(".BANK_ETC_OUT", "BANK_ETC_OUT", "OUT");
		ReportListGetData(".TRANSFER_IN", "TRANSFER_IN", "IN");
		ReportListGetData(".TRANSFER_OUT", "TRANSFER_OUT", "OUT");
		ReportListGetData(".CARD_OUT", "CARD_OUT", "OUT");

		ReportListGetSumData("IN");
		ReportListGetSumData("OUT");
	};

	/**
	 * 자금일보 개별 항목 데이터 불러오기
	 * @param table_class
	 * @param tran_type
	 * @param tran_inout
	 * @constructor
	 */
	var ReportListGetData = function(table_class, tran_type, tran_inout){
		$(table_class + " tbody").empty();

		showLoaderM();

		var p_url = "/settle/report_proc_ajax.php";
		var dataObj = new Object();
		dataObj.period = $("#period").val();
		dataObj.mode = "get_report_data";
		dataObj.tran_date = __ReportDate;
		dataObj.tran_type = tran_type;
		dataObj.tran_inout = tran_inout;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				var total = 0;
				$.each(response.data, function(i, o){
					var account_name = o.account_name;
					var tran_memo = o.tran_memo;
					var tran_amount = Number(o.tran_amount);

					if(tran_type == "BANK_CUSTOMER_IN" || tran_type == "BANK_CUSTOMER_OUT" || tran_type == "CASH_CUSTOMER_IN" || tran_type == "CASH_CUSTOMER_OUT"){
						var target_name = o.target_name;
					}

					if(tran_type == "CARD_OUT"){
						var tran_user = o.tran_user;
						var tran_card_no = o.tran_card_no;
						var tran_purpose = o.tran_purpose;
					}

					total += tran_amount;

					var html = "";
					html += '<tr>';

					if(tran_type == "CARD_OUT"){
						html +=	'<td class="text_left">'+tran_user+'</td>';
						html +=	'<td class="text_left">'+tran_card_no+'</td>';
						html +=	'<td class="text_left">'+tran_purpose+'</td>';
					}

					html +=	'<td class="text_left">'+account_name+'</td>';
					if(tran_type == "BANK_CUSTOMER_IN" || tran_type == "BANK_CUSTOMER_OUT" || tran_type == "CASH_CUSTOMER_IN" || tran_type == "CASH_CUSTOMER_OUT"){
						html += '<td class="text_left">'+target_name+'</td>';
					}
					html += '<td class="text_left">'+tran_memo+'</td>';
					html += '<td class="text_right ">'+Common.addCommas(tran_amount)+'</td>';
					html += '</tr>';

					$(table_class + " tbody").append(html);

				});

				var html = "";

				var total_colpan = 2;
				if(tran_type == "BANK_CUSTOMER_IN" || tran_type == "BANK_CUSTOMER_OUT" || tran_type == "CASH_CUSTOMER_IN" || tran_type == "CASH_CUSTOMER_OUT") {
					total_colpan = 3;
				}else if(tran_type == "CARD_OUT"){
					total_colpan = 4;
				}

				html +=  '<tr>';
				html +=  '<th colspan="'+total_colpan+'">합계</th>';
				if(tran_type == "CARD_OUT") {
					html += '<th class=""></th>';
				}
				html +=  '<th class="text_right '+tran_type+'_SUM" data-amt="'+total+'">'+Common.addCommas(total)+'</th>';
				html +=  '</tr>';

				$(table_class + " tbody").append(html);


				//월합계 년합계
				if(tran_type == "CARD_OUT") {

					var expand_data = response.expand_data;
					console.log(expand_data);


					html = '';
					html += '<tr>';
					html += '<th colspan="4">월합계</th>';
					html += '<th class="">'+expand_data.month.text+'</th>';
					html += '<th class="text_right">'+Common.addCommas(expand_data.month.sum)+'</th>';
					html += '</tr>';

					$(table_class + " tbody").append(html);

					html = '';
					html += '<tr>';
					html += '<th colspan="4">년합계</th>';
					html += '<th class="">'+expand_data.year.text+'</th>';
					html += '<th class="text_right">'+Common.addCommas(expand_data.year.sum)+'</th>';
					html += '</tr>';

					$(table_class + " tbody").append(html);
				}


			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoaderM();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoaderM();
		});
	};

	/**
	 * 계정과목별 집계 데이터 구하기
	 * @param tran_inout
	 * @constructor
	 */
	var ReportListGetSumData = function(tran_inout){
		var table_class = ".ACCOUNT_"+tran_inout;

		$(table_class + " tbody").empty();

		showLoaderM();

		var p_url = "/settle/report_proc_ajax.php";
		var dataObj = new Object();
		dataObj.period = $("#period").val();
		dataObj.mode = "get_report_account_data";
		dataObj.tran_date = __ReportDate;
		dataObj.tran_inout = tran_inout;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response)
			{
				var total = 0;
				$.each(response.data, function(i, o){
					var account_name = o.account_name;
					var tran_amount = Number(o.tran_amount);

					total += tran_amount;

					var html = "";
					html += '<tr>';
					html +=	'<td class="text_left">'+account_name+'</td>';
					html += '<td class="text_right ">'+Common.addCommas(tran_amount)+'</td>';
					html += '</tr>';

					$(table_class + " tbody").append(html);

				});

				var html = "";

				var total_colpan = 1;

				html +=  '<tr>';
				html +=  '<th colspan="'+total_colpan+'">합계</th>';
				html +=  '<th class="text_right ACCOUNT_SUM_'+tran_inout+'" data-amt="'+total+'">'+Common.addCommas(total)+'</th>';
				html +=  '</tr>';

				$(table_class + " tbody").append(html);

				//합계 맞추기
				html = '';
				html +=  '<tr>';
				html +=  '<th colspan="'+total_colpan+'">&nbsp;</th>';
				html +=  '<th class="COMPARE_RESULT_'+tran_inout+'">OK</th>';
				html +=  '</tr>';
				$(table_class + " tbody").append(html);

				ReportAmountCompare();


			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoaderM();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoaderM();
		});
	};


	/**
	 * 수입, 지출 합계 비교
	 * @constructor
	 */
	var ReportAmountCompare = function(){

		//입금액 비교
		var acc_in_amt = $(".ACCOUNT_SUM_IN").data("amt");
		var bank_in_amt = $(".BANK_SUM_IN[data-bank_type='DOMESTIC']").data("amt");

		acc_in_amt = Number(acc_in_amt);
		bank_in_amt = Number(bank_in_amt);

		if(acc_in_amt == NaN || bank_in_amt == NaN) return;

		if(acc_in_amt == bank_in_amt){
			$(".COMPARE_RESULT_IN").text("OK");
		}else{
			$(".COMPARE_RESULT_IN").text("X");
		}

		//출금액 비교
		var acc_out_amt = $(".ACCOUNT_SUM_OUT").data("amt");
		var bank_out_amt = $(".BANK_SUM_OUT[data-bank_type='DOMESTIC']").data("amt");
		//var card_out_amt = $(".CARD_OUT_SUM").data("amt");
		var card_out_amt = 0;

		acc_out_amt = Number(acc_out_amt);
		bank_out_amt = Number(bank_out_amt);
		card_out_amt = Number(card_out_amt);

		if(acc_out_amt == NaN || bank_out_amt == NaN ||card_out_amt == NaN) return;

		if(acc_out_amt == (bank_out_amt + card_out_amt)){
			//$(".COMPARE_RESULT_OUT").text("OK");
		}else{
			//$(".COMPARE_RESULT_OUT").text("X");
		}

	};

	return {
		init: init,
	}
})();

$(function(){
	Report.init();
});
