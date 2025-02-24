/*
 * 정산통계 - 상품매출통계 관리 js
 */
var SettleManage = (function() {
	var root = this;

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
	};

	/**
	 * 당일판매요약표 페이지 초기화
	 * @constructor
	 */
	var TodaySummaryInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "1");

		//카테고리 바인딩
		Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");

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


		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
			e.preventDefault();
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				TodaySummarySearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			TodaySummarySearch();
		});

		TodaySummaryProductListGridInit();
		TodaySummarySearch();

	};

	/**
	 * 당일판매요약표 목록/검색
	 * @constructor
	 */
	var TodaySummarySearch = function(){
		TodaySummaryProductListSearch();
		TodaySummaryGetSeller();
		TodaySummaryGetCategory();
		TodaySummaryGetOrderCount();
		TodaySummaryGetOrderInvoiceCount();
		TodaySummaryGetOrderShippingCount();
		TodaySummaryGetOrderReturnCount();
		TodaySummaryGetOrderCSCount();
	};

	/**
	 * 당일판매요약표 - 판매처
	 * @constructor
	 */
	var TodaySummaryGetSeller = function(){
		$(".summary-seller tbody").empty();
		tableLoader.on(".summary-seller");

		var p_url = "today_summary_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_seller";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='searchForm']").serialize() + '&' + $.param(dataObj)
		}).done(function (response) {
			if(response)
			{
				$.each(response.data, function(i, o){

					var seller_name = o.seller_name;
					var sum_settle_sale_supply = o.sum_settle_sale_supply;
					var sum_due = 0;
					var sum_order_cnt = o.sum_order_cnt;

					var html = "";
					html =  '<tr>' +
								'<td class="text_left">'+seller_name+'</td>' +
								'<td class="text_right">'+Common.addCommas(sum_settle_sale_supply)+'</td>' +
								'<td>'+sum_due+'</td>' +
								'<td>'+Common.addCommas(sum_order_cnt)+'</td>' +
							'</tr>';

					$(".summary-seller tbody").append(html);

				});

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(".summary-seller");
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(".summary-seller");
		});
	};
	/**
	 * 당일판매요약표 - 카테고리
	 * @constructor
	 */
	var TodaySummaryGetCategory = function(){
		$(".summary-category tbody").empty();
		tableLoader.on(".summary-category");

		var p_url = "today_summary_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_category";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='searchForm']").serialize() + '&' + $.param(dataObj)
		}).done(function (response) {
			if(response)
			{
				$.each(response.data, function(i, o){

					var category_name = o.category_l_name + ' > ' + o.category_m_name;
					var sum_settle_sale_supply = o.sum_settle_sale_supply;
					var sum_settle_purchase_supply = o.sum_settle_purchase_supply;
					var sum_product_option_cnt = o.sum_product_option_cnt;

					if(o.category_m_name == null || o.category_m_name == "null"){
						category_name = o.category_l_name;
					}

					if(o.category_l_name == null || o.category_l_name == "null"){
						category_name = "카테고리 없음";
					}

					var html = "";
					html =  '<tr>' +
						'<td class="text_left">'+category_name+'</td>' +
						'<td>'+Common.addCommas(sum_product_option_cnt)+'</td>' +
						'<td class="text_right">'+Common.addCommas(sum_settle_sale_supply)+'</td>' +
						'<td class="text_right">'+Common.addCommas(sum_settle_purchase_supply)+'</td>' +
						'</tr>';

					$(".summary-category tbody").append(html);

				});

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(".summary-category");
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(".summary-category");
		});
	};
	/**
	 * 당일판매요약표 - 주문
	 * @constructor
	 */
	var TodaySummaryGetOrderCount = function(){
		$(".summary-order-count tbody").empty();
		tableLoader.on(".summary-order-count");

		var p_url = "today_summary_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_order_count";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='searchForm']").serialize() + '&' + $.param(dataObj)
		}).done(function (response) {
			if(response)
			{
				$.each(response.data, function(i, o){

					var sum_order_cnt = o.sum_order_cnt;
					var sum_order_pack_cnt = o.sum_order_pack_cnt;
					var sum_order_notmal_cnt = sum_order_cnt - sum_order_pack_cnt;
					var order_distinct_cnt = o.order_distinct_cnt;

					var html = "";
					html += '<tr>' +
								'<td class="">주문수량 / 주문건</td>' +
								'<td>'+Common.addCommas(sum_order_cnt)+' / '+Common.addCommas(order_distinct_cnt)+'</td>' +
							'</tr>';
					html += '<tr>' +
								'<td class="">일반</td>' +
								'<td>'+Common.addCommas(sum_order_notmal_cnt)+'</td>' +
							'</tr>';
					html += '<tr>' +
								'<td class="">합포</td>' +
								'<td>'+Common.addCommas(sum_order_pack_cnt)+'</td>' +
							'</tr>';

					$(".summary-order-count tbody").append(html);

				});

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(".summary-order-count");
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(".summary-order-count");
		});
	};
	/**
	 * 당일판매요약표 - 송장
	 * @constructor
	 */
	var TodaySummaryGetOrderInvoiceCount = function(){
		$(".summary-order-invoice-count tbody").empty();
		tableLoader.on(".summary-order-invoice-count");

		var p_url = "today_summary_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_order_invoice";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='searchForm']").serialize() + '&' + $.param(dataObj)
		}).done(function (response) {
			if(response)
			{
				$.each(response.data, function(i, o){

					var sum_invoice_cnt = o.sum_invoice_cnt;
					var sum_pack_invoice_cnt = o.sum_pack_invoice_cnt;
					var sum_normal_invoice_cnt = sum_invoice_cnt - sum_pack_invoice_cnt;
					var sum_free_invoice_cnt = o.sum_free_invoice_cnt;
					var sum_notfree_invoice_cnt = sum_invoice_cnt - sum_free_invoice_cnt;

					var html = "";
					html += '<tr>' +
						'<td class="">송장수량</td>' +
						'<td>'+Common.addCommas(sum_invoice_cnt)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">합포 / 일반</td>' +
						'<td>'+Common.addCommas(sum_pack_invoice_cnt)+' / ' + Common.addCommas(sum_normal_invoice_cnt) + '</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">선불 / 착불</td>' +
						'<td>'+Common.addCommas(sum_free_invoice_cnt)+' / ' + Common.addCommas(sum_notfree_invoice_cnt) + '</td>' +
						'</tr>';

					$(".summary-order-invoice-count tbody").append(html);

				});

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(".summary-order-invoice-count");
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(".summary-order-invoice-count");
		});
	};
	/**
	 * 당일판매요약표 - 배송
	 * @constructor
	 */
	var TodaySummaryGetOrderShippingCount = function(){
		var target_table_selector = ".summary-order-shipped-count";
		$(target_table_selector+" tbody").empty();
		tableLoader.on(target_table_selector);

		var p_url = "today_summary_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_order_shipped";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='searchForm']").serialize() + '&' + $.param(dataObj)
		}).done(function (response) {
			if(response)
			{
				$.each(response.data, function(i, o){

					var cnt_accept_invoice_shipped = o.cnt_accept_invoice_shipped;
					var cnt_invoice_shipped = o.cnt_invoice_shipped;
					var sum_accept_shipped = o.sum_accept_shipped;
					var cnt_accept_shipped = o.cnt_accept_shipped;

					var html = "";
					html += '<tr>' +
						'<td class="">당일 출력된 송장 중 당일 배송된 수량</td>' +
						'<td>'+Common.addCommas(cnt_accept_invoice_shipped)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">전체 송장 중 당일 배송된 수량</td>' +
						'<td>'+Common.addCommas(cnt_invoice_shipped)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">당일 배송된 상품 수량 / 배송건수</td>' +
						'<td>'+Common.addCommas(sum_accept_shipped)+' / '+Common.addCommas(cnt_accept_shipped)+'</td>' +
						'</tr>';

					$(target_table_selector+" tbody").append(html);

				});

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(target_table_selector);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(target_table_selector);
		});
	};
	/**
	 * 당일판매요약표 - 반품
	 * @constructor
	 */
	var TodaySummaryGetOrderReturnCount = function(){
		var target_table_selector = ".summary-order-return-count";
		$(target_table_selector+" tbody").empty();
		tableLoader.on(target_table_selector);

		var p_url = "today_summary_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_order_return";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='searchForm']").serialize() + '&' + $.param(dataObj)
		}).done(function (response) {
			if(response)
			{
				$.each(response.data, function(i, o){

					var cnt_cancel_after_shipped = o.cnt_cancel_after_shipped;
					var cnt_change_after_shipped = o.cnt_change_after_shipped;
					var sum = cnt_cancel_after_shipped + cnt_change_after_shipped;

					var html = "";
					html += '<tr>' +
						'<td class="">배송 후 교환</td>' +
						'<td>'+Common.addCommas(cnt_change_after_shipped)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">배송 후 취소</td>' +
						'<td>'+Common.addCommas(cnt_cancel_after_shipped)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">총수량</td>' +
						'<td>'+Common.addCommas(sum)+'</td>' +
						'</tr>';

					$(target_table_selector+" tbody").append(html);

				});

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(target_table_selector);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(target_table_selector);
		});
	};
	/**
	 * 당일판매요약표 - CS이력
	 * @constructor
	 */
	var TodaySummaryGetOrderCSCount = function(){
		var target_table_selector = ".summary-order-cs-count";
		$(target_table_selector+" tbody").empty();
		tableLoader.on(target_table_selector);

		var p_url = "today_summary_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_order_cs";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='searchForm']").serialize() + '&' + $.param(dataObj)
		}).done(function (response) {
			if(response)
			{
				$.each(response.data, function(i, o){

					var C_RETURN_REFUND = o.C_RETURN_REFUND;
					var C_RETURN_POOR = o.C_RETURN_POOR;
					var C_RETURN_DELIVERY_ERR = o.C_RETURN_DELIVERY_ERR;
					var C_CANCEL_LOSS = o.C_CANCEL_LOSS;
					var C_CANCEL_SOLDOUT = o.C_CANCEL_SOLDOUT;
					var C_CANCEL_DELIVERY_DELAY = o.C_CANCEL_DELIVERY_DELAY;
					var C_SUM = C_RETURN_REFUND + C_RETURN_POOR + C_RETURN_DELIVERY_ERR + C_CANCEL_LOSS + C_CANCEL_SOLDOUT + C_CANCEL_DELIVERY_DELAY;
					var X_EXCHANGE_NORMAL = o.X_EXCHANGE_NORMAL;
					var X_EXCHANGE_POOR = o.X_EXCHANGE_POOR;
					var X_EXCHANGE_DELIVERY_ERR = o.X_EXCHANGE_DELIVERY_ERR;
					var X_EXCHANGE_SOLDOUT = o.X_EXCHANGE_SOLDOUT;
					var X_EXCHANGE_PRODUCT_CHANGE = o.X_EXCHANGE_PRODUCT_CHANGE;
					var X_SUM = X_EXCHANGE_NORMAL + X_EXCHANGE_POOR + X_EXCHANGE_DELIVERY_ERR + X_EXCHANGE_SOLDOUT + X_EXCHANGE_PRODUCT_CHANGE;
					var NORMAL = o.NORMAL;
					var SUM = NORMAL;

					var html = "";
					html += '<tr>' +
						'<td rowspan="6" class="">취소</td>' +
						'<td>반품-취소(환불)</td>' +
						'<td>'+Common.addCommas(C_RETURN_REFUND)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td>반품-불량</td>' +
						'<td>'+Common.addCommas(C_RETURN_POOR)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td>반품-오배송</td>' +
						'<td>'+Common.addCommas(C_RETURN_DELIVERY_ERR)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td>취소-분실</td>' +
						'<td>'+Common.addCommas(C_CANCEL_LOSS)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td>취소-상품품절</td>' +
						'<td>'+Common.addCommas(C_CANCEL_SOLDOUT)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td>취소-배송지연</td>' +
						'<td>'+Common.addCommas(C_CANCEL_DELIVERY_DELAY)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td colspan="2">합계</td>' +
						'<td>'+Common.addCommas(C_SUM)+'</td>' +
						'</tr>';

					html += '<tr>' +
						'<td rowspan="5" class="">교환</td>' +
						'<td>단순교환</td>' +
						'<td>'+Common.addCommas(X_EXCHANGE_NORMAL)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">불량교환</td>' +
						'<td>'+Common.addCommas(X_EXCHANGE_POOR)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">오배송교환</td>' +
						'<td>'+Common.addCommas(X_EXCHANGE_DELIVERY_ERR)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">품절교환</td>' +
						'<td>'+Common.addCommas(X_EXCHANGE_SOLDOUT)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td class="">상품교환</td>' +
						'<td>'+Common.addCommas(X_EXCHANGE_PRODUCT_CHANGE)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td colspan="2">합계</td>' +
						'<td>'+Common.addCommas(X_SUM)+'</td>' +
						'</tr>';

					html += '<tr>' +
						'<td rowspan="1" class="">일반</td>' +
						'<td>일반</td>' +
						'<td>'+Common.addCommas(NORMAL)+'</td>' +
						'</tr>';
					html += '<tr>' +
						'<td colspan="2">합계</td>' +
						'<td>'+Common.addCommas(SUM)+'</td>' +
						'</tr>';

					$(target_table_selector+" tbody").append(html);

				});

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			tableLoader.off(target_table_selector);
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			tableLoader.off(target_table_selector);
		});
	};

	/**
	 * 당일판매요약표 - 상품별 jqGrid 초기화
	 * @constructor
	 */
	var TodaySummaryProductListGridInit = function(){

		var lastSelection;
		$("#grid_list").jqGrid({
			url: './today_summary_product_grid.php',
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
			colModel: [
				{ label: '이미지', name: 'settle_idx', index: 'settle_idx', width: 50, sortable: false, formatter: function(cellvalue, options, rowobject){
					var tmp = "";
					if(rowobject.product_img_main > 0)
					{
						var main_img = eval('rowobject.product_img_'+rowobject.product_img_main);
						var main_img_file = eval('rowobject.product_img_filename_'+rowobject.product_img_main);
						if(main_img)
						{
							tmp = '<a href="/_data/product/'+ main_img_file +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+main_img+'" data-filename="'+main_img_file+'"></a>';
						}
					}
					return tmp;

				}},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false},
				{ label: '상품명+옵션', name: 'product_full_name', index: 'product_full_name', width: 150, sortable: false, align: 'left', formatter: function(cellvalue, options, rowobject){
					return rowobject.product_name + ' / ' + rowobject.product_option_name;
				}},
				{ label: '수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '원가', name: 'settle_purchase_unit_supply', index: 'settle_purchase_unit_supply', width: 80, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '판매가', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 80, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '매출이익', name: 'settle_sale_profit', index: 'settle_sale_profit', width: 80, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '현재고', name: 'stock_NORMAL', index: 'stock_NORMAL', width: 80, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
					console.log(rowobject)
						if(rowobject.product_sale_type == "CONSIGNMENT"){
							return "-";
						}
						return Common.addCommas(cellvalue);
					}},
			],
			rowNum: 100,
			rowList: [],
			pager: '#grid_pager',
			sortname: 'A.product_option_cnt',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: false,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				productImgThumb();
				lightbox.option({
					'resizeDuration': 100,
					'fadeDuration': 200,
					'imageFadeDuration': 200,
					'albumLabel': "상품이미지 %1/%2",
				});
			},
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
				TransactionListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			TransactionListSearch();
		});
	};

	/**
	 * 당일판매요약표 - 상품별 목록/검색
	 * @constructor
	 */
	var TodaySummaryProductListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 판매처별통계 페이지 초기화
	 * @constructor
	 */
	var SellerSaleInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "8");

		//카테고리 바인딩
		Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");

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

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});

		//다운로드 버튼 클릭 이벤트
		$(".btn-xls-down").on("click", function(){
			SellerSaleXlsDown();
		});
	};

	var xlsDownIng = false;
	var xlsDownInterval;

	/**
	 * 일자별 재고조회[입고량] 목록 엑셀 다운로드
	 * @constructor
	 */
	var SellerSaleXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "seller_sale_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("SELLER_SALE", function(){
				SettleManage.SellerSaleXlsDownComplete();
			});
		}, 500);
	};

	var SellerSaleXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 월별 판매처별통계 페이지 초기화
	 * @constructor
	 */
	var SellerMonthlyInit = function(){

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

		//검색 폼 Submit 방지
		$("#searchForm").on("submit", function(e){
		});

		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});


		//다운로드 버튼 클릭 이벤트
		$(".btn-xls-down").on("click", function(){
			SellerMonthlyXlsDown();
		});
	};

	/**
	 * 월별 판매처별통계 엑셀 다운로드
	 * @constructor
	 */
	var SellerMonthlyXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", "seller_monthly_xls_down.php?"+param);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("SELLER_MONTHLY", function(){
				SettleManage.SellerMonthlyXlsDownComplete();
			});
		}, 500);
	};

	var SellerMonthlyXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};


	/**
	 * 공급처별정산(재고) 페이지 초기화
	 * @constructor
	 */
	var SupplierStockInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "9");

		//카테고리 바인딩
		Category.bindCategorySetSelect(".product_category_l_idx", ".product_category_m_idx");

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

		$(".btn-xls-down").on("click", function(){
			SupplierStockDown();
		});
		//jqGrid 초기화
		SupplierStockGridInit();
	};


	var __SupplierStockVars = new Object();
	__SupplierStockVars.date_start = "";
	__SupplierStockVars.date_end = "";
	/**
	 * 공급처별정산(재고) Grid 초기화
	 * @constructor
	 */
	var SupplierStockGridInit = function(){

		__SupplierStockVars.date_start = $("input[name='date_start']").val();
		__SupplierStockVars.date_end = $("input[name='date_end']").val();

		$("#grid_list").jqGrid({
			url: './supplier_stock_grid.php',
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
				{ label: '공급처코드', name: 'supplier_idx', index: 'S.member_idx', width: 80, sortable: true},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						return '<a href="javascript:;" class="link btn-supplier-pop" data-idx="'+rowobject.supplier_idx+'" data-name="'+cellvalue+'">'+cellvalue+'</a>'
					}},
				{ label: '입고', name: 'stock_in_amount', index: 'stock_in_amount', width: 100, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '출고', name: 'stock_out_amount', index: 'stock_out_amount', width: 100, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '반품입고', name: 'stock_in_back_amount', index: 'stock_in_back_amount', width: 100, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '반품출고', name: 'stock_out_back_amount', index: 'stock_out_back_amount', width: 100, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '배송', name: 'stock_in_amount', index: 'stock_in_amount', width: 100, sortable: true, align: 'right', formatter: 'integer'},
				{ label: '입고금액', name: 'stock_in_sum', index: 'stock_in_sum', width: 100, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '출고금액', name: 'stock_out_sum', index: 'stock_out_sum', width: 100, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '반품입고<br>금액', name: 'stock_in_back_sum', index: 'stock_in_back_sum', width: 100, sortable: false, align: 'right', formatter: 'integer'},
				{ label: '반품출고<br>금액', name: 'stock_out_back_sum', index: 'stock_out_back_sum', width: 100, sortable: false, align: 'right', formatter: 'integer'},
			],
			rowNum: 1000,
			rowList: [],
			pager: '#grid_pager',
			sortname: 'supplier_name',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//항목설정 팝업
				$(".btn-supplier-pop").on("click", function(){
					var supplier_idx = $(this).data("idx");
					var supplier_name = $(this).data("name");
					Common.newWinPopup("/settle/supplier_stock_pop.php?supplier_idx="+supplier_idx+"&supplier_name="+supplier_name+"&date_start="+__SupplierStockVars.date_start+"&date_end="+__SupplierStockVars.date_end, 'column_setting_pop', 1200, 720, 'no');
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
				SupplierStockSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			SupplierStockSearch();
		});
	};

	/**
	 * 판매처상품별통계 페이지 Grid 목록/검색
	 * @공급처별정산(재고)
	 */
	var SupplierStockSearch = function(){
		__SupplierStockVars.date_start = $("input[name='date_start']").val();
		__SupplierStockVars.date_end = $("input[name='date_end']").val();
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 광고비관리 엑셀 다운로드
	 * @constructor
	 */
	var SupplierStockDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "supplier_stock_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_SUPPLIER_STOCK", function(){
				SettleManage.SupplierStockDownComplete();
			});
		}, 500);
	};

	var SupplierStockDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	//상품 이미지 리스트에서 썸네일 보기
	var productImgThumb = function(){
		$(".product_img_thumb").each(function(i, o) {
			var p_url = "/proc/_thumbnail.php";
			var dataObj = new Object();
			dataObj.file_idx = $(o).data("file_idx");
			dataObj.save_filename = $(o).data("filename");
			dataObj.width = 18;
			dataObj.height = 18;
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

	/***
	 * 당일판매요약표 - 테이블 로더
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
		TodaySummaryInit: TodaySummaryInit,
		SellerSaleInit: SellerSaleInit,
		SellerMonthlyInit: SellerMonthlyInit,
		SupplierStockInit: SupplierStockInit,
		SellerSaleXlsDownComplete: SellerSaleXlsDownComplete,
		SellerMonthlyXlsDownComplete: SellerMonthlyXlsDownComplete,
		SupplierStockDownComplete: SupplierStockDownComplete,
	}
})();
$(function(){
	SettleManage.init();
});