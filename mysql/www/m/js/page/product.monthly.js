/*
 * 모바일 - 월별상품별 통계 관련 js
 */
var ProductMonthly = (function() {
	var root = this;

	var init = function(){

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");

		//공급처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SUPPLIER_GROUP", ".product_supplier_group_idx", ".supplier_idx");

		//폼 전송 방지
		$("#dyForm").on("submit", function(e){
			e.preventDefault();
		});

		$("#btn-search").on("click", function(){
			$(".sum_total").text("판매 총액 : ");
			var p_url = "/settle/product_monthly_grid.php";
			var dataObj = new Object();
			dataObj.param = $("#dyForm").serialize();
			dataObj.rows = 100000;
			dataObj.page = 1;
			showLoaderM();
			$.ajax({
				type: 'GET',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if(typeof response == "object"){
					if(typeof response.records != "undefined"){
						if(response.records > 0){
							console.log(response);
							console.log(response.userdata.sum_total);
							$(".sum_total").text("판매 총액 : " + Common.addCommas(response.userdata.sum_total) + " 원");
							bindTable(response.rows);
						}else{
							$(".table_result").empty();
						}
					}
				}

				hideLoaderM();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoaderM();
			});

		});

	};

	var bindTable = function(rows){

		var start_date = $("#period_start_year_input").val() + '-' + $("#period_start_month_input").val() + '-1';
		var end_date = $("#period_end_year_input").val() + '-' + $("#period_end_month_input").val() + '-1';

		start_date = moment(start_date, 'YYYY-MM-DD');
		end_date = moment(end_date, 'YYYY-MM-DD');

		var value_view = $("select[name='value_view']").val();

		var addCol = new Array();
		// If you want an inclusive end date (fully-closed interval)
		for (var m = start_date; m.diff(end_date, 'days') <= 0; m.add(1, 'month')) {
			var colLabel = m.format('YYYY/MM');
			var colName = m.format('YYYYMM');

			addCol.push({
				label: colLabel
				, name: 's'+colName+'_'+value_view
			});
		}

		var html = '<table class="table_style03">' +
			'<colgroup>' +
			'<col width="100">' +
			'<col width="150">' +
			'<col width="150">' +
			'<col width="100">' +
			'<col width="100">' +
			'<col width="100">';
			$.each(addCol, function(i, o){
				html += '<col width="100">';
			});
		html += '</colgroup>' +
			'<thead>' +
			'<tr>' +
			'<th>옵션코드</th>' +
			'<th>상품명</th>' +
			'<th>옵션</th>' +
			'<th>원가X수량</th>' +
			'<th>판매가합</th>' +
			'<th>기간배송수량</th>';
			$.each(addCol, function(i, o){
				html += '<th>'+o.label+'</th>';
			});
		html += '</tr>' +
			'</thead>' +
			'<tbody>';
		$.each(rows, function(i, r){
		html += '<tr>' +
			'<td>'+r.product_option_idx2+'</td>' +
			'<td class="text_left">'+r.product_name+'</td>' +
			'<td class="text_left">'+r.product_option_name+'</td>' +
			'<td class="text_right">'+Common.addCommas(r.product_option_purchase_price * r.sum_product_option_cnt)+'</td>' +
			'<td class="text_right">'+Common.addCommas(r.sum_settle_sale_supply)+'</td>' +
			'<td class="text_right">'+Common.addCommas(r.shipping_count)+'</td>';
			$.each(addCol, function(j, o){
				html += '<td class="text_right">'+Common.addCommas(eval("r."+o.name))+'</td>';
			});
		html += '</tr>';
		});
		html += '</tbody>' +
			'</table>';

		$(".table_result").html(html);

		// var table_pos_top = $(".table_result").offset().top;
		// var winH = $(window).height();
		// var table_result_h = winH - table_pos_top - 20;
		//
		// $(".table_result").height(table_result_h);
		//
		// var $table = $('.table_style03');
		// $table.floatThead({
		// 	scrollContainer: function($table){
		// 		return $table.closest('.table_result');
		// 	}
		// });
	};

	return {
		init: init,
	}
})();

$(function(){
	ProductMonthly.init();
});
