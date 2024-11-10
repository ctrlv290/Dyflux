var wasClosing = [];
var intEditOption = {
	size: 25, maxlengh: 30,
	dataInit: function(element) {
		$(element).keypress(function(e){
			console.log(e.which);
			if (e.which != 8 && e.which != 45 && e.which != 46 && e.which != 0 && (e.which < 48 || e.which > 57)) {
				return false;
			}
		});
	}
};
var lastSelection;

/*
 * 정산통계 - 매입매출현황 관리 js
 */
var SettleTransaction = (function() {
	var root = this;

	var init = function () {
	};

	var xlsDownIng = false;
	var xlsDownInterval;

	/**
	 * 매입매출현황[판매일보] 페이지 초기화
	 * @constructor
	 */
	var TransactionListInit = function(){
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

		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "1");

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

		//jqGrid 초기화
		TransactionListGridInit();

		//추가주문등록 버튼
		$(".btn-cs-open").on("click", function(){
			Common.newWinPopup('/cs/cs.php', 'menu_205', 0, 0);
		});

		//매입/매출 보정
		$(".btn-purchase-adjust, .btn-sale-adjust").on("click", function(){
			var pop_title = "";
			var pop_type = $(this).data("type");
			var settle_type = "";

			if(pop_type == "purchase"){
				pop_title = "매입보정";
				settle_type = "ADJUST_PURCHASE";
			}else if(pop_type == "sale"){
				pop_title = "매출보정";
				settle_type = "ADJUST_SALE";
			}

			var p_url = "transaction_adjust_pop.php";
			var dataObj = new Object();
			dataObj.settle_type = settle_type;
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					PopupCommonPopOpen(800, 0, pop_title, response);
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});

		//발주마감
		$(".btn-closing").on("click", function(){
			var p_url = "transaction_pop_closing.php";
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

					PopupCommonPopOpen(500, 0, "마감처리", response);
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});

		//다운로드 버튼 이벤트
		$(".btn-xls-down").on("click", function(){
			TransactionListXlsDown();
		});

        //판매일보 업로드
        $(".btn-xls-up").on("click", function(){
            var p_url = "transaction_upload_pop.php";
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

                    PopupCommonPopOpen(600, 0, "판매일보 업로드", response);
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
	 * 매입매출현황[판매일보] jqGrid 초기화
	 * @constructor
	 */
	var TransactionListGridInit = function(){

		var isolateList = [];

		var searchDateColLabel = "날짜";
		searchDateColLabel = $("select[name='period_type'] option:selected").text();

		var _colModel = [
			{ label: 'settle_closing', name: 'settle_closing', index: 'settle_closing', width: 80, sortable: false, hidden: true, frozen: true, formatter: function(cellvalue, options, rowobject){
					if(cellvalue == "Y")
					{
						wasClosing.push(options.rowId);
					}
					return cellvalue;
				}},
			{ label: '판매처 주문번호', name: 'market_order_no', index: 'market_order_no', width: 120, sortable: false, hidden: true, frozen: true},
			{ label: 'settle_idx', name: 'settle_idx', index: 'settle_idx', width: 80, sortable: false, hidden: true, frozen: true},
			{ label: '관리번호', name: 'order_idx', index: 'T.order_idx', width: 80, sortable: true, frozen: true, formatter: function(cellvalue, options, rowobject){
					var val = cellvalue;
					if(cellvalue == "0"){
						val = "";
					}
					return val;
				}},
			{ label: '<span class="span_search_date" style="font-weight: bold;">' + searchDateColLabel + '</span>', name: 'search_date', index: 'search_date', width: 100, sortable: true, frozen: true, formatter: function(cellvalue, options, rowobject){
			    var val = cellvalue;
			    if(rowobject.settle_type == "ADJUST_SALE" || rowobject.settle_type == "ADJUST_PURCHASE") {
                    val = cellvalue;
                    if(rowobject.settle_date != Common.toDateTimeOnlyDate(rowobject.settle_regdate)){
						val = Common.toDateTimeOnlyDate(cellvalue);
					}
                }
			    if ($("select[name='period_type']").val() == 'settle_date' && rowobject.settle_date != Common.toDateTimeOnlyDate(rowobject.settle_regdate)){
                    val = Common.toDateTimeOnlyDate(cellvalue);
                }
			    return val;
                }
			},
			{ label: '처리', name: 'order_cs_status', index: 'order_cs_status', width: 100, sortable: false, frozen: true, formatter: function(cellvalue, options, rowobject){
					var val = "";
					if(cellvalue == "NORMAL"){
						val = "";
					}
					if(rowobject.settle_type == "ADJUST_SALE"){
						val = "매출보정";
					}else if(rowobject.settle_type == "ADJUST_PURCHASE") {
						val = "매입보정";
					}else if(rowobject.settle_type == "CANCEL"){
						val = "취소";
					}else if(rowobject.settle_type == "AD_COST_CHARGE"){
						val = "광고비";
					}else if(rowobject.settle_type == "EXCHANGE"){
						val = "교환";
					}else if(rowobject.settle_type == "UPLOAD"){
						val = "업로드";
					}

					return val;
				}},
			{ label: '사유', name: 'cs_reason_cancel_text', index: 'T.order_cs_status', width: 100, sortable: false, frozen: true},
			{ label: '마켓', name: 'seller_name', index: 'seller_name', width: 150, sortable: true, frozen: true, align: 'left'},
			{ label: '수취인', name: 'receive_name', index: 'receive_name', width: 100, sortable: false, frozen: true},
			{ label: '전화번호', name: 'receive_tp_num', index: 'receive_tp_num', width: 100, sortable: false},
			{ label: '핸드폰', name: 'receive_hp_num', index: 'receive_hp_num', width: 100, sortable: false},
			{ label: '우편번호', name: 'receive_zipcode', index: 'receive_zipcode', width: 100, sortable: false},
			{ label: '주소', name: 'receive_addr1', index: 'receive_addr1', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
					var addr = cellvalue + ' ' + rowobject.receive_addr2;
					var chkIsolated = isolatedAddr.isIsolated(addr);
					if(chkIsolated){
						addr = '<span class="color_red" title="도서산간지역">' + addr + '</span>';
						isolateList.push(options.rowId);
					}


					return addr;
				}},
			{ label: '배송메세지', name: 'receive_memo', index: 'receive_memo', width: 100, sortable: false, align: 'left'},
			{ label: '상품명+옵션', name: 'product_name_full', index: 'product_name_full', width: 150, sortable: false, align: 'left', formatter: function(cellvalue, options, rowobject){
					var name = "";
					if(typeof rowobject.product_name_full != "undefined"){
						name = rowobject.product_name_full;
					}else if(rowobject.product_name != null) {
						name = rowobject.product_name + ' ' + rowobject.product_option_name;
					}else{
						name= "";
					}
					return name;
				}},
			{ label: '상품세금종류', name: 'product_tax_type', index: 'product_tax_type', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
					var rst = "";
					if(cellvalue == "TAXATION"){
						rst = "과세";
					}else if(cellvalue == "FREE"){
						rst = "면세";
					}else if(cellvalue == "SMALL"){
						rst = "영세";
					}
					return rst;
				}},
			{ label: '판매수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, sortable: false, editable: true, formatter: 'integer'},
			{ label: '거래처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: true, align: 'left'},
			{ label: '판매단가', name: 'order_unit_price', index: 'order_unit_price', width: 80, sortable: false, align: 'right', editable: true, formatter: 'integer', edittype:"text", editoptions: intEditOption},
			/*{ label: '판매가', name: 'order_amt', index: 'order_amt', width: 80, sortable: false, editable: true, formatter: 'integer', edittype:"text", editoptions:{
					size: 25, maxlengh: 30,
					dataInit: function(element) {
						$(element).keypress(function(e){
							if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
								return false;
							}
						});
					}
				}},
				*/
			// { label: '매입가', name: 'product_option_purchase_price', index: 'product_option_purchase_price', width: 80, sortable: false, editable: true, formatter: 'integer'},



			//매출공급가액 -> 판매가
			{ label: '판매가', name: 'settle_sale_supply', index: 'settle_sale_supply', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},
			{ label: '공급가액', name: 'settle_sale_supply_ex_vat', index: 'settle_sale_supply_ex_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},

			//판매수수료료
			{ label: '수수료', name: 'settle_sale_commission_in_vat', index: 'settle_sale_commission_in_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},
			{ label: '공급가액', name: 'settle_sale_commission_ex_vat', index: 'settle_sale_commission_ex_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},

			//배송비 -> 매출배송비
			{ label: '배송비', name: 'settle_delivery_in_vat', index: 'settle_delivery_in_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},
			{ label: '공급가액', name: 'settle_delivery_ex_vat', index: 'settle_delivery_ex_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},

			//판매배송비수수료
			{ label: '수수료', name: 'settle_delivery_commission_in_vat', index: 'settle_delivery_commission_in_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},
			{ label: '공급가액', name: 'settle_delivery_commission_ex_vat', index: 'settle_delivery_commission_ex_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},

			//매출합계
			{ label: '합계', name: 'sale_sum', index: 'sale_sum', width: 120, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
					var sum = 0;
					sum += Number(rowobject.settle_sale_supply) - Number(rowobject.settle_sale_commission_in_vat) + Number(rowobject.settle_delivery_in_vat) - Number(rowobject.settle_delivery_commission_in_vat);
					return Common.addCommas(sum);
				}},
			{ label: '공급가액', name: 'sale_sum_ex_vat', index: 'sale_sum_ex_vat', width: 120, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
					var sum = 0;
					sum += Number(rowobject.settle_sale_supply_ex_vat) - Number(rowobject.settle_sale_commission_ex_vat) + Number(rowobject.settle_delivery_ex_vat) - Number(rowobject.settle_delivery_commission_ex_vat);
					return Common.addCommas(sum);
				}},

			//매입단가(매출원가) 공급가액 -> 매입가
			{ label: '매입가', name: 'settle_purchase_unit_supply', index: 'settle_purchase_unit_supply', width: 120, sortable: false, align: 'right', classes: 'sale', editable: true, formatter: 'integer'},
			{ label: '공급가액', name: 'settle_purchase_unit_supply_ex_vat', index: 'settle_purchase_unit_supply_ex_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},

			//매입가(매출원가) 공급가액
			{ label: '단가', name: 'settle_purchase_supply', index: 'settle_purchase_supply', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},
			{ label: '공급가액', name: 'settle_purchase_supply_ex_vat', index: 'settle_purchase_supply_ex_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},

			//매입배송비
			{ label: '배송비', name: 'settle_purchase_delivery_in_vat', index: 'settle_purchase_delivery_in_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},
			{ label: '공급가액', name: 'settle_purchase_delivery_ex_vat', index: 'settle_purchase_delivery_ex_vat', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},

			//매입합계
			{ label: '합계', name: 'purchase_sum', index: 'purchase_sum', width: 120, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
					var sum = 0;
					sum += Number(rowobject.settle_purchase_supply) + Number(rowobject.settle_purchase_delivery_in_vat);
					return Common.addCommas(sum);
				}},
			{ label: '공급가액', name: 'purchase_sum_ex_vat', index: 'purchase_sum_ex_vat', width: 120, sortable: false, align: 'right', formatter: function(cellvalue, options, rowobject){
					var sum = 0;
					sum += Number(rowobject.settle_purchase_supply_ex_vat) + Number(rowobject.settle_purchase_delivery_ex_vat);
					return Common.addCommas(sum);
				}},

			{ label: '정산/배송비', name: 'settle_settle_amt', index: 'settle_settle_amt', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},
			{ label: '광고비', name: 'settle_ad_amt', index: 'settle_ad_amt', width: 120, sortable: false, align: 'right', editable: true, formatter: 'integer'},
			{ label: '매출이익', name: 'settle_sale_profit', index: 'settle_sale_profit', width: 120, sortable: false, align: 'right', editable: false, formatter: 'integer'},
			// { label: '매출액', name: 'settle_sale_amount', index: 'settle_sale_amount', width: 120, sortable: false, editable: true, formatter: 'integer'},
			// { label: '매출원가', name: 'settle_sale_cost', index: 'settle_sale_cost', width: 120, sortable: false, editable: true, formatter: 'integer'},
			// { label: '매출이익률', name: 'settle_sale_profit_ratio', index: 'settle_sale_profit_ratio', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
			//
			// 	var ratio = ((rowobject.settle_sale_amount - rowobject.settle_sale_cost) / rowobject.settle_sale_amount) * 100;
			//
			// 	return ratio.toFixed(1)+'%';
			// }},

			{ label: '메모', name: 'settle_memo', index: 'settle_memo', width: 200, sortable: false, editable: true, align: 'left', edittype:"text"},
		];

		if(!isDYLogin) {
			var _colModelVendor = new Array();
			//관리번호	날짜	처리	사유	마켓	수취인	전화번호	핸드폰	우편번호	주소	배송메세지
			//상품명	옵션	상품세금종류	판매수량	판매단가	판매가	판매가-공급가액	매출배송비	매출배송비-공급가액	매출합계	매출합계-공급가액
			var vendorLabel = ["관리번호", "날짜", "처리", "사유", "마켓", "수취인", "전화번호", "핸드폰", "우편번호", "주소", "배송메세지", "상품명+옵션", "상품세금종류", "판매수량", "판매단가", "판매가", "판매가-공급가액", "매출배송비", "매출배송비-공급가액", "매출합계", "매출합계-공급가액"];
			var vendorName = ["settle_sale_supply_ex_vat", "settle_delivery_in_vat", "settle_delivery_ex_vat", "sale_sum", "sale_sum_ex_vat"];
			$.each(_colModel, function(i, o){

				if($.inArray(o.label, vendorLabel) > -1 || $.inArray(o.name, vendorName) > -1){

					if(o.name == "settle_sale_supply_ex_vat") {
						o.label = "판매가-공급가액";
					}else if(o.name == "settle_delivery_in_vat") {
						o.label = "매출배송비";
					}else if(o.name == "settle_delivery_ex_vat") {
						o.label = "매출배송비-공급가액";
					}else if(o.name == "sale_sum") {
						o.label = "매출합계";
					}else if(o.name == "sale_sum_ex_vat") {
						o.label = "매출합계-공급가액";
					}

					_colModelVendor.push(o);
				}
			});

			_colModel = _colModelVendor;

		}

		$("#grid_list_transaction").jqGrid({
			url: './transaction_list_grid.php',
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
			colModel: _colModel,
			rowNum: Common.jsSiteConfig.jqGridRowListBig[0],
			rowList: Common.jsSiteConfig.jqGridRowListBig,
			pager: '#grid_pager',
			sortname: 'search_date',
			sortorder: "asc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			rownumWidth: 65,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			editNextRowCell: (isDYLogin) ? true : false,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list_transaction");

				//마감 확인
				$.each(wasClosing, function(k, v){
					$("#grid_list_transaction #"+v).addClass("closing");
					wasClosing = [];
				});

				if(isDYLogin) {
					$(".ui-th-column-header").eq(0).addClass("sale");
					$("#grid_list_transaction_settle_sale_supply").addClass("sale").find("div").css({"top": "auto"});
					$("#grid_list_transaction_settle_sale_supply_ex_vat").addClass("sale").find("div").css({"top": "auto"});

					$(".ui-th-column-header").eq(1).addClass("sale");
					$("#grid_list_transaction_settle_sale_commission_in_vat").addClass("sale").find("div").css({"top": "auto"});
					$("#grid_list_transaction_settle_sale_commission_ex_vat").addClass("sale").find("div").css({"top": "auto"});

					$(".ui-th-column-header").eq(2).addClass("sale");
					$("#grid_list_transaction_settle_delivery_in_vat").addClass("sale").find("div").css({"top": "auto"});
					$("#grid_list_transaction_settle_delivery_ex_vat").addClass("sale").find("div").css({"top": "auto"});

					$(".ui-th-column-header").eq(3).addClass("sale");
					$("#grid_list_transaction_settle_delivery_commission_in_vat").addClass("sale").find("div").css({"top": "auto"});
					$("#grid_list_transaction_settle_delivery_commission_ex_vat").addClass("sale").find("div").css({"top": "auto"});

					$(".ui-th-column-header").eq(4).addClass("sale_sum");
					$("#grid_list_transaction_sale_sum").addClass("sale_sum").find("div").css({"top": "auto"});
					$("#grid_list_transaction_sale_sum_ex_vat").addClass("sale_sum").find("div").css({"top": "auto"});

					$(".ui-th-column-header").eq(5).addClass("purchase");
					$("#grid_list_transaction_settle_purchase_unit_supply").addClass("purchase").find("div").css({"top": "auto"});
					$("#grid_list_transaction_settle_purchase_unit_supply_ex_vat").addClass("purchase").find("div").css({"top": "auto"});

					$(".ui-th-column-header").eq(6).addClass("purchase");
					$("#grid_list_transaction_settle_purchase_supply").addClass("purchase").find("div").css({"top": "auto"});
					$("#grid_list_transaction_settle_purchase_supply_ex_vat").addClass("purchase").find("div").css({"top": "auto"});

					$(".ui-th-column-header").eq(7).addClass("purchase");
					$("#grid_list_transaction_settle_purchase_delivery_in_vat").addClass("purchase").find("div").css({"top": "auto"});
					$("#grid_list_transaction_settle_purchase_delivery_ex_vat").addClass("purchase").find("div").css({"top": "auto"});

					$(".ui-th-column-header").eq(8).addClass("purchase_sum");
					$("#grid_list_transaction_purchase_sum").addClass("purchase_sum").find("div").css({"top": "auto"});
					$("#grid_list_transaction_purchase_sum_ex_vat").addClass("purchase_sum").find("div").css({"top": "auto"});

					$("#grid_list_transaction_settle_sale_profit").addClass("profit");

				}
				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("transaction_list", $("#grid_list_transaction"));

				//틀고정 시 높이가 맞지 않는 것을 강제로 맞춤
				//버그로 인해 비추천
				if(Common.detectIE() !== false && Common.detectIE() < 12 && isDYLogin) {
					$(".frozen-bdiv").height($(".ui-jqgrid-bdiv").height());
				}

				var userData = $("#grid_list_transaction").jqGrid("getGridParam", "userData");
				var sale_sum = userData.sale_sum;
				var purchase_sum = userData.purchase_sum;
				var profit_sum = userData.profit_sum;

				$(".total_sale_sum").text(Common.addCommas(sale_sum));
				$(".total_purchase_sum").text(Common.addCommas(purchase_sum));
				$(".total_profit_sum").text(Common.addCommas(profit_sum));

				$.each(isolateList, function(k, v){
					$("#grid_list_transaction_frozen #"+v).addClass("upload_err");
					$("#grid_list_transaction #"+v).addClass("upload_err");
					isolateList = [];
				});
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list_transaction").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "transaction_list");
			},
			onSelectRow: function(id){
				if(isDYLogin) {
					var rowData = $('#grid_list_transaction').getRowData(id);
					var settle_closing = rowData.settle_closing;

					if (settle_closing == "N") {

						if (id && id !== lastSelection) {

							var settle_idx = rowData.settle_idx;
							$('#grid_list_transaction').jqGrid('restoreRow', lastSelection);
							$('#grid_list_transaction').jqGrid('editRow', id, {
								keys: true,
								oneditfunc: function () {
								},
								aftersavefunc: function () {
									//$('#grid_list_transaction').trigger("reloadGrid");
								},
								successfunc: function (response) {
									if (response.readyState == 4) {
										var data = JSON.parse(response.responseText);
										$('#grid_list_transaction').trigger("reloadGrid");
									} else {
										alert("오류가 발생하였습니다.");
										$('#grid_list_transaction').jqGrid('restoreRow', lastSelection);
									}
								},
								extraparam: {mode: "row_edit", settle_idx: settle_idx}

							});

						}
					}
					lastSelection = id;
				}
			},
			editurl: "transaction_proc.php"
		});

		//틀고정
		//버그로 인해 비추천
		if(isDYLogin) {
			$("#grid_list_transaction").jqGrid('setFrozenColumns');
			$(".frozen-div").height(58);
			$(".frozen-div tr").height(58);
		}

		setTimeout(function(){
			//$.jgrid.loadState("grid_list_transaction", {restoreData: false, clearAfterLoad : true});
			if(isDYLogin) {
				$("#grid_list_transaction").jqGrid('setGroupHeaders', {
					useColSpanStyle: true,
					groupHeaders: [
						{startColumnName: 'settle_sale_supply', numberOfColumns: 2, titleText: '판매가'},
						{startColumnName: 'settle_sale_commission_in_vat', numberOfColumns: 2, titleText: '판매수수료'},
						{startColumnName: 'settle_delivery_in_vat', numberOfColumns: 2, titleText: '매출배송비'},
						{startColumnName: 'settle_delivery_commission_in_vat', numberOfColumns: 2, titleText: '배송비수수료'},
						{startColumnName: 'sale_sum', numberOfColumns: 2, titleText: '매출합계'},
						{startColumnName: 'settle_purchase_supply', numberOfColumns: 2, titleText: '매입가'},
						{startColumnName: 'settle_purchase_delivery_in_vat', numberOfColumns: 2, titleText: '매입 배송비'},
						{startColumnName: 'settle_purchase_unit_supply', numberOfColumns: 2, titleText: '매입단가'},
						{startColumnName: 'purchase_sum', numberOfColumns: 2, titleText: '매입합계'},
					]
				});
			}
			TransactionListSearch();

		}, 500);

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list_transaction");

			//틀고정 시 높이가 맞지 않는 것을 강제로 맞춤
			//버그로 인해 비추천
			if(Common.detectIE() && isDYLogin) {
				$(".frozen-bdiv").height($(".ui-jqgrid-bdiv").height());
			}
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
	 * 매입매출현황[판매일보] 목록/검색
	 * @constructor
	 */
	var TransactionListSearch = function(){

		TransactionListGridHeaderReset();

		$("#grid_list_transaction").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};
	var TransactionListReload = function(){

		TransactionListGridHeaderReset();

		$("#grid_list_transaction").setGridParam({
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var TransactionListGridHeaderReset = function(){
		$(".span_search_date").text($("select[name='period_type'] option:selected").text());
	};

	/**
	 * 매입매출현황[판매일보]  엑셀 다운로드
	 * @constructor
	 */
	var TransactionListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};

		var url = "transaction_list_xls_down.php?"+$.param(dataObj);
		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("XLS_TRANSACTION_LIST", function(){
				SettleTransaction.TransactionListXlsDownComplete();
			});
		}, 500);
	};

	var TransactionListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 매입/매출 보정 팝업 페이지 초기화
	 * @constructor
	 */
	var TransactionAdjustPopInit = function(){

		//판매처 그룹 및 판매처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".adjust_product_seller_group_idx", ".adjust_seller_idx");
		$(".adjust_seller_idx").SumoSelect({
			placeholder: '판매처를 선택하세요.',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			PopupCommonPopClose();
		});

		//상품 검색 팝업
		$(".btn-product-search-pop").on("click", function(){
			TransactionAdjustProductAddPopup()
		});

		Common.setDatePickerForDynamicElement($(".adjust_settle_date"));

		//판매처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".adjust_product_seller_group_idx", ".adjust_seller_idx");

		$("form[name='dyForm2']").on("submit", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);
		});

		$("#btn-save").on("click", function(){
			TransactionAdjustSave();
		});

		_TransactionAdjustFormIng = false;
	};

	var _TransactionAdjustFormIng = false;
	/**
	 * 매입/매출 보정 저장
	 * @returns {boolean}
	 * @constructor
	 */
	var TransactionAdjustSave = function(){

		if(_TransactionAdjustFormIng) return;

		var $fm = $("form[name='dyForm2']");

		if($.trim($("input[name='settle_date']", $fm).val()) == ""){
			alert("날짜를 입력해주세요.");
			return;
		}
		if($.trim($("select[name='seller_idx']", $fm).val()) == "0" || $.trim($("select[name='seller_idx']", $fm).val()) == ""){
			alert("판매처를 선택해주세요.");
			return;
		}
		if($.trim($("input[name='product_idx']", $fm).val()) == "" || $.trim($("input[name='product_option_idx']", $fm).val()) == ""){
			alert("상품을 선택해주세요.");
			return;
		}

		if(!confirm("저장하시겠습니까?\n입력하진 않은 란은 \'0\'원으로 입력됩니다.")){
			return;
		}

		_TransactionAdjustFormIng = true;
		showLoader();
		var p_url = "transaction_proc.php";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: $("form[name='dyForm2']").serialize()
		}).done(function (response) {
			console.log(response);
			if(response.result)
			{
				alert('저장되었습니다.');
				TransactionListReload();
				PopupCommonPopClose();

			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
			_TransactionAdjustFormIng = false;
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
			_TransactionAdjustFormIng = false;
		});
	};

	/**
	 * 매입/매출 보정 팝업 - 상품 검색 팝업 Open
	 * @constructor
	 */
	var TransactionAdjustProductAddPopup = function(){
		Common.newWinPopup("settle_product_search_pop.php", 'settle_product_search_pop', 850, 720, 'yes');
	};

	/**
	 * 매입/매출 보정 팝업 - 상품 검색 팝업 페이지 초기화
	 * @constructor
	 */
	var TransactionAdjustProductAddPopupInit = function(){
		//Grid 초기화
		$("#grid_list_pop").jqGrid({
			url: '/settle/settle_product_search_pop_grid.php',
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
					label: '선택', name: '선택', width: 60, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
						return '<a href="javascript:;" class="xsmall_btn btn-product-select" data-idx="' + rowobject.product_idx + '" data-num="' + options.rowId+ '">선택</a>';
					}
				},
				{ label: '상품IDX', name: 'product_idx', index: 'product_idx', width: 0, hidden: true},
				{ label: '상품코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, is_use : true},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: true},
				{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: true},
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
				{ label: '공급업체', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false, align: 'left'},
				{ label: 'supplier_idx', name: 'supplier_idx', index: 'supplier_idx', width: 0, hidden: true},
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
					var supplier_idx = rowData.supplier_idx;
					var supplier_name = rowData.supplier_name;
					TransactionAdjustProductAddPopupSelect(product_idx, product_option_idx, product_name, product_option_name, supplier_idx, supplier_name);
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
				TransactionAdjustProductAddPopupSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar_pop").on("click", function(){
			TransactionAdjustProductAddPopupSearch();
		});
	};

	/**
	 * 매입/매출 보정 팝업 - 상품 검색 팝업 목록/검색
	 * @constructor
	 */
	var TransactionAdjustProductAddPopupSearch = function(){

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
			url: '/settle/settle_product_search_pop_grid.php',
			postData:{
				param: $("#searchFormPop").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 매입/매출 보정 팝업 - 상품 검색 팝업 - 상품 선택
	 * opener 를 검사 후 opener 의 상품 입력 함수 실행
	 * @param product_idx
	 * @param product_option_idx
	 * @param product_name
	 * @param product_option_name
	 * @constructor
	 */
	var TransactionAdjustProductAddPopupSelect = function(product_idx, product_option_idx, product_name, product_option_name, supplier_idx, supplier_name){
		var openerName = window.opener &&
			window.opener.document &&
			window.opener.name;
		if (openerName == "transaction_list") {

			window.opener.SettleTransaction.TransactionAdjustProductSelect(product_idx, product_option_idx, product_name, product_option_name, supplier_idx, supplier_name);
			self.close();
		}else{

		}
	};

	/**
	 * 매입/매출 보정 팝업  - 상품 정보 입력 함수
	 * @param product_idx
	 * @param product_option_idx
	 * @param product_name
	 * @param product_option_name
	 * @constructor
	 */
	var TransactionAdjustProductSelect = function(product_idx, product_option_idx, product_name, product_option_name, supplier_idx, supplier_name){
		$("form[name='dyForm2'] input[name='product_idx']").val(product_idx);
		$("form[name='dyForm2'] input[name='product_option_idx']").val(product_option_idx);
		$("form[name='dyForm2'] input[name='product_name']").val(product_name);
		$("form[name='dyForm2'] input[name='product_option_name']").val(product_option_name);
		$("form[name='dyForm2'] input[name='supplier_idx']").val(supplier_idx);
		$("form[name='dyForm2'] input[name='supplier_name']").val(supplier_name);
	};

	/**
	 * 매입/매출 일괄보정 업로드 관련 내용 저장 변수
	 * @type {Object}
	 * @private
	 */
	var __TransactionAdjust = new Object();
	__TransactionAdjust.xlsValueRow = 0;                 //업로드된 엑셀 Row 중 정상인 Row 수
	__TransactionAdjust.xlsUploadedFileName = "";        //업로드 된 엑셀 파일명
	__TransactionAdjust.xlsWritePageMode = "";           //일괄등록 / 일괄수정 Flag
	__TransactionAdjust.xlsWriteReturnStyle = "";        //리스트 반환 또는 적용
	__TransactionAdjust.xlsUserFileName = "";            //사용자가 업로드한 파일명

	/**
	 * 매입/매출 일괄보정 페이지 초기화
	 * @constructor
	 */
	var TransactionAdjustUploadInit = function(){

		$(".btn-upload").on("click", function(){
			if($("input[name='xls_file']").val() == "")
			{
				alert("업로드 할 파일을 선택해주세요.");
				return false;
			}
			showLoader();
			$("#searchForm").submit();
		});

		$(".btn-xls-insert").on("click", function(){
			if(__TransactionAdjust.xlsValidRow < 1)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = __TransactionAdjust.xlsValidRow + "건을 적용 하시겠습니까?";
				if(confirm(msg)) {
					TransactionAdjustUploadXlsInsert();
				}
			}
		});

		TransactionAdjustUploadGridInit();
	};

	/**
	 * 매입/매출 일괄보정 목록 jqGrid 초기화
	 * @constructor
	 */
	var TransactionAdjustUploadGridInit = function(){

		__TransactionAdjust.xlsWritePageMode = $("#xlswrite_mode").val();
		__TransactionAdjust.xlsWriteReturnStyle = $("#xlswrite_act").val();

		var validErr = [];

		$("#grid_list").jqGrid({
			url: './transaction_adjust_proc_xls.php',
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
				{ label: '유형', name: 'A', index: 'invoice_no', width: 80, sortable: false},
				{ label: '날짜', name: 'B', index: 'shipped_status', width: 100, sortable: false},
				{ label: '판매처명', name: 'C', index: 'invoice_status', width: 100, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'order_progress_step_han', width: 100, sortable: false},
				{ label: '옵션명', name: 'D', index: 'order_progress_step_han', width: 100, sortable: false},
				{ label: '공급처명', name: 'supplier_name', index: 'order_progress_step_han', width: 100, sortable: false},
				{ label: '판매단가', name: 'E', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '판매수량', name: 'F', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '판매가', name: 'G', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '공급가액', name: 'H', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '수수료', name: 'I', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '공급가액', name: 'J', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '배송비', name: 'K', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '공급가액', name: 'L', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '수수료', name: 'M', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '공급가액', name: 'N', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '매입가', name: 'O', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '공급가액', name: 'P', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '단가', name: 'Q', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '공급가액', name: 'R', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '배송비', name: 'S', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '공급가액', name: 'T', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '정산/배송비', name: 'U', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '광고비', name: 'V', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '매출이익', name: 'W', index: 'order_progress_step_accept_date', width: 80, sortable: false},
				{ label: '비고', name: 'valid', index: 'valid', width: 50, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;
						if(cellvalue)
						{
							rst = "정상";
							__TransactionAdjust.xlsValidRow++;
						}else{
							rst = "오류";
							validErr.push(options.rowId);
						}
						return rst;
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
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				$.each(validErr, function(k, v){
					$("#grid_list #"+v).addClass("upload_err");
					validErr = [];
				});
			},
			beforeRequest: function(){
				__TransactionAdjust.xlsValidRow = 0;
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});
		$("#grid_list").jqGrid('setGroupHeaders', {
			useColSpanStyle: true,
			groupHeaders:[
				{startColumnName: 'G', numberOfColumns: 2, titleText: '<strong>판매가</strong>'},
				{startColumnName: 'I', numberOfColumns: 2, titleText: '<strong>판매수수료</strong>'},
				{startColumnName: 'K', numberOfColumns: 2, titleText: '<strong>매출배송비</strong>'},
				{startColumnName: 'M', numberOfColumns: 2, titleText: '<strong>배송비수수료</strong>'},
				{startColumnName: 'O', numberOfColumns: 2, titleText: '<strong>매입단가</strong>'},
				{startColumnName: 'Q', numberOfColumns: 2, titleText: '<strong>매입가</strong>'},
				{startColumnName: 'S', numberOfColumns: 2, titleText: '<strong>매입배송비</strong>'}
			]
		});
		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	//매입/매출 일괄보정 업로드 된 엑셀 파일 로딩
	var TransactionAdjustUploadXlsRead = function(xls_file_path_name){
		//console.log(xls_file_path_name);
		__TransactionAdjust.xlsUploadedFileName = xls_file_path_name;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		__TransactionAdjust.xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list").setGridParam({
			datatype: "json",
			postData:{
				mode: __TransactionAdjust.xlsWritePageMode,
				act: __TransactionAdjust.xlsWriteReturnStyle,
				xls_filename: xls_file_path_name
			}
		}).trigger("reloadGrid");
	};

	//업로드 된 엑셀 파일 적용
	var TransactionAdjustUploadXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "transaction_adjust_proc_xls.php";
		var dataObj = new Object();
		dataObj.mode = __TransactionAdjust.xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = __TransactionAdjust.xlsUploadedFileName;
		dataObj.xls_validrow = __TransactionAdjust.xlsValidRow;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert(response.msg+"건이 정상 적용 되었습니다.");
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
	 * 판매일보 업로드 관련 내용 저장 변수
	 * @type {Object}
	 * @private
	 */
	var __TransactionUpload = new Object();
	__TransactionUpload.xlsValueRow = 0;                 //업로드된 엑셀 Row 중 정상인 Row 수
	__TransactionUpload.xlsUploadedFileName = "";        //업로드 된 엑셀 파일명
	__TransactionUpload.xlsWritePageMode = "";           //일괄등록 / 일괄수정 Flag
	__TransactionUpload.xlsWriteReturnStyle = "";        //리스트 반환 또는 적용
	__TransactionUpload.xlsUserFileName = "";            //사용자가 업로드한 파일명

	/**
	 * 판매일보 페이지 초기화
	 * @constructor
	 */
	var TransactionUploadInit = function(){

        __TransactionUpload.xlsValidRow = 0;

		$(".btn-upload").on("click", function(){
			if($("input[name='xls_file']").val() == "")
			{
				alert("업로드 할 파일을 선택해주세요.");
				return false;
			}
			showLoader();
			$("#uploadForm").submit();
		});

		$(".btn-xls-insert").on("click", function(){
			if(__TransactionUpload.xlsValidRow < 1)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = __TransactionUpload.xlsValidRow + "건을 적용 하시겠습니까?";
				if(confirm(msg)) {
					TransactionUploadXlsInsert();
				}
			}
		});
	};

	//판매일보 업로드 된 엑셀 파일 로딩
	var TransactionUploadXlsRead = function(xls_file_path_name){

		__TransactionUpload.xlsWritePageMode = $("#xlswrite_mode").val();
		__TransactionUpload.xlsWriteReturnStyle = $("#xlswrite_act").val();

		//console.log(xls_file_path_name);
		__TransactionUpload.xlsUploadedFileName = xls_file_path_name;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		__TransactionUpload.xlsValidRow = 0;

		var p_url = "transaction_upload_proc_xls.php";
		var dataObj = new Object();
		dataObj.mode = __TransactionUpload.xlsWritePageMode;
		dataObj.act = __TransactionUpload.xlsWriteReturnStyle;
		dataObj.xls_filename = __TransactionUpload.xlsUploadedFileName;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
                __TransactionUpload.xlsValidRow = response.normal_count;
				$("#total_rows").text(response.total_rows);
				$("#normal_count").text(response.normal_count);
				$("#error_count").text(response.error_count);
				$("#error_rows").text(response.error_rows);
				if(response.error_count > 0){
					$("#error_count").css("background-color", "#ffebeb");
					$("#error_rows").css("background-color", "#ffebeb");
				}
				hideLoader();
				console.log(response)
				// location.reload();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	//업로드 된 엑셀 파일 적용
	var TransactionUploadXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "transaction_upload_proc_xls.php";
		var dataObj = new Object();
		dataObj.mode = __TransactionUpload.xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = __TransactionUpload.xlsUploadedFileName;
		dataObj.xls_validrow = __TransactionUpload.xlsValidRow;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				alert(response.msg+"건이 정상 적용 되었습니다.");
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
	 * 발주 마감 팝업 페이지 초기화
	 * @constructor
	 */
	var TransactionClosingPopInit = function(){
		//창 닫기 버튼 바인딩
		$(".btn-common-pop-close").on("click", function(){
			PopupCommonPopClose();
		});

		_TransactionAdjustFormIng = false;

		//마감처리 버튼 바인딩
		$("#btn-save").on("click", function(){
			if(_TransactionAdjustFormIng) return;
			if(!confirm("마감처리 하시겠습니까?")){
				return;
			}

			_TransactionAdjustFormIng = true;
			showLoader();
			var p_url = "transaction_proc.php";
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: $("form[name='dyForm2']").serialize()
			}).done(function (response) {
				console.log(response);
				if(response.result)
				{
					alert('처리되었습니다.');
					TransactionListReload();
					PopupCommonPopClose();

				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
				_TransactionAdjustFormIng = false;
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
				_TransactionAdjustFormIng = false;
			});
		});
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
		TransactionListInit: TransactionListInit,
		TransactionAdjustPopInit: TransactionAdjustPopInit,
		TransactionAdjustProductAddPopupInit: TransactionAdjustProductAddPopupInit,
		TransactionAdjustProductSelect: TransactionAdjustProductSelect,
		TransactionClosingPopInit: TransactionClosingPopInit,
		TransactionListXlsDownComplete: TransactionListXlsDownComplete,
		TransactionAdjustUploadInit : TransactionAdjustUploadInit,
		TransactionAdjustUploadXlsRead : TransactionAdjustUploadXlsRead,
		TransactionUploadInit : TransactionUploadInit,
		TransactionUploadXlsRead : TransactionUploadXlsRead,
	}
})();