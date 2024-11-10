var columnModel = {
	PRODUCT_LIST: [
		{
			label: '수정', name: '수정', width: 80, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return '<a href="javascript:;" class="xsmall_btn btn-product-modify" data-idx="' + rowobject.product_idx + '">수정</a>';
			}
		},
		{label: '상품코드', name: 'product_idx', index: 'product_idx', width: 100, is_use : true},
		{label: '상품명', name: 'product_name', index: 'product_name', width: 200, sortable: false, is_use : true, align: 'left', formatter: function(cellvalue, options, rowobject){

				return '<a href="product_write.php?product_idx='+rowobject.product_idx+'" class="link">'+cellvalue+'</a>';
			}},
		{label: '이미지', name: 'product_img', index: 'product_img', width: 150, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){

				var tmp = "";

				if(rowobject.product_img_main > 0)
				{
					var main_img = eval('rowobject.product_img_'+rowobject.product_img_main);
					var main_img_file = eval('rowobject.product_img_filename_'+rowobject.product_img_main);

					if(main_img)
					{
						//console.log(rowobject.product_idx, main_img_file);
						tmp = '<a href="/_data/product/'+ main_img_file +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+main_img+'" data-filename="'+main_img_file+'"></a>';
					}
				}

				// if(rowobject.product_img_1) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_1 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_1+'" data-filename="'+rowobject.product_img_filename_1+'"></a>';
				// if(rowobject.product_img_2) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_2 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_2+'" data-filename="'+rowobject.product_img_filename_2+'"></a>';
				// if(rowobject.product_img_3) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_3 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_3+'" data-filename="'+rowobject.product_img_filename_3+'"></a>';
				// if(rowobject.product_img_4) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_4 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_4+'" data-filename="'+rowobject.product_img_filename_4+'"></a>';
				// if(rowobject.product_img_5) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_5 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_5+'" data-filename="'+rowobject.product_img_filename_5+'"></a>';
				// if(rowobject.product_img_6) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_6 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_6+'" data-filename="'+rowobject.product_img_filename_6+'"></a>';
				return tmp;

			}},
		{label: '품절', name: 'product_soldout', index: 'product_soldout', width: 150, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var rst;
				if(rowobject.soldout_cnt == "0"){
					rst = "";
				}else{
					rst = "품절 (" + rowobject.soldout_cnt + ")";
				}
				return  rst;
			}},
		{label: '판매타입', name: 'product_sale_type', index: 'product_sale_type', width: 150, sortable: false, is_use : true},
		{label: '카테고리', name: 'product_category', index: 'product_category', width: 150, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var category_full_name;
				category_full_name = rowobject.category_l_name;
				if(rowobject.category_m_name != "")
				{
					category_full_name+= '>' + rowobject.category_m_name;
				}
				return  category_full_name;
			}},
		{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, is_use : true},
		{label: '공급처상품명', name: 'product_supplier_name', index: 'product_supplier_name', width: 150, sortable: false, is_use : true, align: 'left'},
		{label: '등록일', name: 'product_regdate', index: 'product_regdate', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return Common.toDateTime(cellvalue);
			}
		},
		{label: '벤더사노출', name: 'product_vendor_show', index: 'product_vendor_show', width: 150, sortable: false, is_use : true},
	],
	PRODUCT_LIST_VENDOR: [
		{label: '상품코드', name: 'product_idx', index: 'product_idx', width: 100, is_use : true},
		{label: '상품명', name: 'product_name', index: 'product_name', width: 200, sortable: false, is_use : true, align: 'left', formatter: function(cellvalue, options, rowobject){
				return '<a href="product_write.php?product_idx='+rowobject.product_idx+'" class="link">'+cellvalue+'</a>';
			}},
		{label: '이미지', name: 'product_img', index: 'product_img', width: 150, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var tmp = "";

				if(rowobject.product_img_main > 0)
				{
					var main_img = eval('rowobject.product_img_'+rowobject.product_img_main);
					var main_img_file = eval('rowobject.product_img_filename_'+rowobject.product_img_main);

					if(main_img)
					{
						//console.log(rowobject.product_idx, main_img_file);
						tmp = '<a href="/_data/product/'+ main_img_file +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+main_img+'" data-filename="'+main_img_file+'"></a>';
					}
				}

				// if(rowobject.product_img_1) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_1 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_1+'" data-filename="'+rowobject.product_img_filename_1+'"></a>';
				// if(rowobject.product_img_2) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_2 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_2+'" data-filename="'+rowobject.product_img_filename_2+'"></a>';
				// if(rowobject.product_img_3) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_3 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_3+'" data-filename="'+rowobject.product_img_filename_3+'"></a>';
				// if(rowobject.product_img_4) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_4 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_4+'" data-filename="'+rowobject.product_img_filename_4+'"></a>';
				// if(rowobject.product_img_5) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_5 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_5+'" data-filename="'+rowobject.product_img_filename_5+'"></a>';
				// if(rowobject.product_img_6) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_6 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_6+'" data-filename="'+rowobject.product_img_filename_6+'"></a>';
				return tmp;

			}},
		{label: '품절', name: 'product_soldout', index: 'product_soldout', width: 150, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var rst;
				if(rowobject.soldout_cnt == "0"){
					rst = "";
				}else{
					rst = "품절 (" + rowobject.soldout_cnt + ")";
				}
				return  rst;
			}},
		{label: '판매타입', name: 'product_sale_type', index: 'product_sale_type', width: 150, sortable: false, is_use : true},
		{label: '카테고리', name: 'product_category', index: 'product_category', width: 150, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var category_full_name;
				category_full_name = rowobject.category_l_name;
				if(rowobject.category_m_name != "")
				{
					category_full_name+= '>' + rowobject.category_m_name;
				}
				return  category_full_name;
			}},
		{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, is_use : true},
		{label: '공급처상품명', name: 'product_supplier_name', index: 'product_supplier_name', width: 150, sortable: false, is_use : true, align: 'left'},
		{label: '등록일', name: 'product_regdate', index: 'product_regdate', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return Common.toDateTime(cellvalue);
			}
		},
		{label: '상태', name: 'product_status', index: 'product_status', width: 150, sortable: false, is_use : true},
		{label: '벤더사노출', name: 'product_vendor_show', index: 'product_vendor_show', width: 150, sortable: false, is_use : true},
	],
	STOCK_PRODUCT_LIST: [
		{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, is_use : true},
		{label: '상품옵션코드', name: 'product_option_idx', index: 'STOCK.product_option_idx', width: 80, is_use : true},
		{label: '이미지', name: 'product_img', index: 'product_img', width: 60, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){

				var tmp = "";

				if(rowobject.product_img_main > 0)
				{
					var main_img = eval('rowobject.product_img_'+rowobject.product_img_main);
					var main_img_file = eval('rowobject.product_img_filename_'+rowobject.product_img_main);

					if(main_img)
					{
						//console.log(rowobject.product_idx, main_img_file);
						tmp = '<a href="/_data/product/'+ main_img_file +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_option_idx+'" data-file_idx="'+main_img+'" data-filename="'+main_img_file+'"></a>';
					}
				}

				// if(rowobject.product_img_1) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_1 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_1+'" data-filename="'+rowobject.product_img_filename_1+'"></a>';
				// if(rowobject.product_img_2) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_2 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_2+'" data-filename="'+rowobject.product_img_filename_2+'"></a>';
				// if(rowobject.product_img_3) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_3 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_3+'" data-filename="'+rowobject.product_img_filename_3+'"></a>';
				// if(rowobject.product_img_4) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_4 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_4+'" data-filename="'+rowobject.product_img_filename_4+'"></a>';
				// if(rowobject.product_img_5) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_5 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_5+'" data-filename="'+rowobject.product_img_filename_5+'"></a>';
				// if(rowobject.product_img_6) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_6 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_6+'" data-filename="'+rowobject.product_img_filename_6+'"></a>';
				return tmp;

			}},
		{label: '상품명+옵션명', name: 'product_full_name', index: 'product_full_name', width: 200, sortable: false, align: 'left', is_use : true, formatter: function(cellvalue, options, rowobject){

				return rowobject.product_name + ' / ' + rowobject.product_option_name
			}},
		{ label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},

		{label: '정상재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="NORMAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}, cellattr: function(rowid, val, rowObject, cm, rdata){
				if(rowObject.stock_amount_normal > 0 && rowObject.product_option_warning_count > val ){
					return ' name="warning" ';
				}
			}},

		{label: '정상재고금액', name: 'stock_price_NORMAL', index: 'stock_price_NORMAL', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(rowobject.stock_amount_NORMAL * rowobject.stock_unit_price);
			}},
		{label: '불량재고', name: 'stock_amount_BAD', index: 'stock_amount_BAD', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="BAD"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},

		{label: '불량재고금액', name: 'stock_price_BAD', index: 'stock_price_BAD', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(rowobject.stock_amount_BAD * rowobject.stock_unit_price);
			}},
		{label: '불량재고<br>교환출고', name: 'stock_amount_BAD_OUT_EXCHANGE', index: 'stock_amount_BAD_OUT_EXCHANGE', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '불량재고<br>반품출고', name: 'stock_amount_BAD_OUT_RETURN', index: 'stock_amount_BAD_OUT_RETURN', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '양품재고', name: 'stock_amount_ABNORMAL', index: 'stock_amount_ABNORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="ABNORMAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '보류', name: 'stock_amount_HOLD', index: 'stock_amount_HOLD', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="HOLD"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '출고지회송<br>교환회송', name: 'stock_amount_FAC_RETURN_EXCHNAGE', index: 'stock_amount_FAC_RETURN_EXCHNAGE', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '출고지회송<br>반품회송', name: 'stock_amount_FAC_RETURN_BACK', index: 'stock_amount_FAC_RETURN_BACK', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '분실재고', name: 'stock_amount_LOSS', index: 'stock_amount_LOSS', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="LOSS"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '일반폐기', name: 'stock_amount_DISPOSAL', index: 'stock_amount_DISPOSAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="DISPOSAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '영구폐기', name: 'stock_amount_DISPOSAL_PERMANENT', index: 'stock_amount_DISPOSAL_PERMANENT', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},

		{label: '미배송<br>(송장)', name: 'stock_amount_INVOICE', index: 'stock_amount_INVOICE', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '미배송<br>(접수)', name: 'stock_amount_ACCEPT', index: 'stock_amount_ACCEPT', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '재고위협수량', name: 'product_option_warning_count', index: 'product_option_warning_count', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{
			label: '재고처리', name: '재고처리', width: 80, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return '<a href="javascript:;" class="xsmall_btn btn-stock-control" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '" ' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'>재고</a>';
			}},
		{
			label: '차트', name: '차트', width: 60, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return '<a href="javascript:;" class="xsmall_btn btn-stock-chart" data-product_option_idx="' + rowobject.product_option_idx + '" data-stock_unit_price="' + rowobject.stock_unit_price + '">차트</a>';
			}
		},
		{
			label: '로그', name: '로그', width: 60, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return '<a href="javascript:;" class="xsmall_btn btn-stock-product-log" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '" ' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-date_start="' + $("input[name='date_start']").val() + '" ' +
					'data-date_end="' + $("input[name='date_end']").val() + '" ' +
					'">로그</a>';
			}
		},
	],
	STOCK_LIST: [
		{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, is_use : true},
		{label: '상품옵션코드', name: 'product_option_idx', index: 'STOCK.product_option_idx', width: 100, is_use : true},
		{label: '이미지', name: 'product_img', index: 'product_img', width: 60, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){

				var tmp = "";

				if(rowobject.product_img_main > 0)
				{
					var main_img = eval('rowobject.product_img_'+rowobject.product_img_main);
					var main_img_file = eval('rowobject.product_img_filename_'+rowobject.product_img_main);

					if(main_img)
					{
						//console.log(rowobject.product_idx, main_img_file);
						tmp = '<a href="/_data/product/'+ main_img_file +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_option_idx+'" data-file_idx="'+main_img+'" data-filename="'+main_img_file+'"></a>';
					}
				}

				// if(rowobject.product_img_1) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_1 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_1+'" data-filename="'+rowobject.product_img_filename_1+'"></a>';
				// if(rowobject.product_img_2) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_2 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_2+'" data-filename="'+rowobject.product_img_filename_2+'"></a>';
				// if(rowobject.product_img_3) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_3 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_3+'" data-filename="'+rowobject.product_img_filename_3+'"></a>';
				// if(rowobject.product_img_4) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_4 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_4+'" data-filename="'+rowobject.product_img_filename_4+'"></a>';
				// if(rowobject.product_img_5) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_5 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_5+'" data-filename="'+rowobject.product_img_filename_5+'"></a>';
				// if(rowobject.product_img_6) tmp += '<a href="/_data/product/'+ rowobject.product_img_filename_6 +'" class="product_img_thumb product_img_link" data-lightbox="btn_product_img_set_'+rowobject.product_idx+'" data-file_idx="'+rowobject.product_img_6+'" data-filename="'+rowobject.product_img_filename_6+'"></a>';
				return tmp;

			}},
		{label: '상품명+옵션명', name: 'product_full_name', index: 'product_full_name', width: 200, sortable: false, align: 'left', is_use : true, formatter: function(cellvalue, options, rowobject){

				return rowobject.product_name + ' / ' + rowobject.product_option_name
			}},
		{label: '배송', name: 'stock_amount_SHIPPED', index: 'stock_amount_SHIPPED', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '정상재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_status="NORMAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}, cellattr: function(rowid, val, rowObject, cm, rdata){
				if(rowObject.stock_amount_normal > 0 && rowObject.product_option_warning_count > val ){
					return ' name="warning" ';
				}
			}},
		{label: '불량재고', name: 'stock_amount_BAD', index: 'stock_amount_BAD', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_status="BAD"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '불량재고<br>교환출고', name: 'stock_amount_BAD_OUT_EXCHANGE', index: 'stock_amount_BAD_OUT_EXCHANGE', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '불량재고<br>반품출고', name: 'stock_amount_BAD_OUT_RETURN', index: 'stock_amount_BAD_OUT_RETURN', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '양품재고', name: 'stock_amount_ABNORMAL', index: 'stock_amount_ABNORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_status="ABNORMAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '보류', name: 'stock_amount_HOLD', index: 'stock_amount_HOLD', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_status="HOLD"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '출고지회송<br>교환회송', name: 'stock_amount_FAC_RETURN_EXCHNAGE', index: 'stock_amount_FAC_RETURN_EXCHNAGE', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '출고지회송<br>반품회송', name: 'stock_amount_FAC_RETURN_BACK', index: 'stock_amount_FAC_RETURN_BACK', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '분실재고', name: 'stock_amount_LOSS', index: 'stock_amount_LOSS', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_status="LOSS"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '일반폐기', name: 'stock_amount_DISPOSAL', index: 'stock_amount_DISPOSAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_status="DISPOSAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '영구폐기', name: 'stock_amount_DISPOSAL_PERMANENT', index: 'stock_amount_DISPOSAL_PERMANENT', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{
			label: '재고처리', name: '재고처리', width: 80, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return '<a href="javascript:;" class="xsmall_btn btn-stock-control" data-product_option_idx="' + rowobject.product_option_idx + '">재고</a>';
			}
		},
		{label: '송장', name: 'stock_amount_INVOICE', index: 'stock_amount_INVOICE', width: 80, sortable: false, is_use : true},
		{label: '접수', name: 'stock_amount_ACCEPT', index: 'stock_amount_ACCEPT', width: 80, sortable: false, is_use : true},
		{label: '재고위협수량', name: 'product_option_warning_count', index: 'product_option_warning_count', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{
			label: '로그', name: '로그', width: 60, sortable: false, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return '<a href="javascript:;" class="xsmall_btn btn-stock-product-log" data-product_option_idx="' + rowobject.product_option_idx + '">로그</a>';
			}
		},
	],

	STOCK_PERIOD_LIST: [
		{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, is_use : true},
		{label: '상품옵션코드', name: 'product_option_idx', index: 'STOCK.product_option_idx', width: 100, is_use : true},
		{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left', is_use : true},
		{label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left', is_use : true},
		{ label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{ label: '판매가', name: 'product_option_sale_price', index: 'product_option_sale_price', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
			return Common.addCommas(cellvalue);
		}},
		{ label: '원가합', name: 'stock_unit_price_sum', index: 'stock_unit_price_sum', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
			return Common.addCommas(rowobject.stock_unit_price * rowobject.stock_amount_NORMAL) ;
		}},
		{label: '현재<br>정상재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="NORMAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '현재<br>불량재고', name: 'stock_amount_BAD', index: 'stock_amount_BAD', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="BAD"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '현재<br>불량재고<br>교환출고', name: 'stock_amount_BAD_OUT_EXCHANGE', index: 'stock_amount_BAD_OUT_EXCHANGE', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '현재<br>불량재고<br>반품출고', name: 'stock_amount_BAD_OUT_RETURN', index: 'stock_amount_BAD_OUT_RETURN', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '현재<br>양품재고', name: 'stock_amount_ABNORMAL', index: 'stock_amount_ABNORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="ABNORMAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '현재<br>보류재고', name: 'stock_amount_HOLD', index: 'stock_amount_HOLD', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="HOLD"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '현재<br>출고지회송<br>교환회송', name: 'stock_amount_FAC_RETURN_EXCHNAGE', index: 'stock_amount_FAC_RETURN_EXCHNAGE', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '현재<br>출고지회송<br>반품회송', name: 'stock_amount_FAC_RETURN_BACK', index: 'stock_amount_FAC_RETURN_BACK', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '현재<br>분실재고', name: 'stock_amount_LOSS', index: 'stock_amount_LOSS', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="LOSS"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '현재<br>일반폐기', name: 'stock_amount_DISPOSAL', index: 'stock_amount_DISPOSAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="DISPOSAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '현재<br>영구폐기', name: 'stock_amount_DISPOSAL_PERMANENT', index: 'stock_amount_DISPOSAL_PERMANENT', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '조회일<br><span class="th_last_date">YYYY-MM-DD</span><br>정상재고', name: 'stock_amount_NORMAL_last', index: 'stock_amount_NORMAL_last', width: 80, sortable: false, is_use : true, is_readonly: true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '조회일<br><span class="th_last_date">YYYY-MM-DD</span><br>불량재고', name: 'stock_amount_BAD_last', index: 'stock_amount_BAD_last', width: 80, sortable: false, is_use : true, is_readonly: true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '입고', name: 'stock_amount_IN', index: 'stock_amount_IN', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '입고금액', name: 'stock_price_IN', index: 'stock_price_IN', width: 120,align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(rowobject.stock_amount_IN * rowobject.stock_unit_price);
			}},
		{label: '반품입고', name: 'stock_amount_RETURN', index: 'stock_amount_RETURN', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '반품금액', name: 'stock_price_RETURN', index: 'stock_price_RETURN', width: 120,align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(rowobject.stock_amount_RETURN * rowobject.stock_unit_price);
			}},
		{label: '출고', name: 'stock_amount_OUT', index: 'stock_amount_OUT', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '출고금액', name: 'stock_price_OUT', index: 'stock_price_OUT', width: 120,align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(rowobject.stock_amount_OUT * rowobject.stock_unit_price);
			}},
		{label: '송장', name: 'stock_amount_INVOICE', index: 'stock_amount_INVOICE', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '송장금액', name: 'stock_price_INVOICE', index: 'stock_price_INVOICE', width: 120,align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(rowobject.stock_amount_INVOICE * rowobject.stock_unit_price);
			}},
		{label: '배송', name: 'stock_amount_SHIPPED', index: 'stock_amount_SHIPPED', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '배송금액', name: 'stock_price_SHIPPED', index: 'stock_price_SHIPPED', width: 120,align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(rowobject.stock_amount_SHIPPED * rowobject.stock_unit_price);
			}},
	],
	STOCK_DAILY_LIST : [
		{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 150, sortable: false, is_use : true},
		{label: '상품옵션코드', name: 'product_option_idx', index: 'STOCK.product_option_idx', width: 100, is_use : true},
		{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left', is_use : true},
		{label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left', is_use : true},
		{label: '품절', name: 'product_option_soldout', index: 'product_option_soldout', width: 60, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return (cellvalue == 'Y') ? '품절' : '';
			}},
		{label: '공급처<br>상품명+옵션', name: 'product_supplier_name', index: 'product_supplier_name', width: 150, sortable: false, align: 'left', is_use : true},
		{ label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 100, align: 'right', sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '현재<br>정상재고', name: 'stock_amount_NORMAL', index: 'stock_amount_NORMAL', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="link btn-stock-control-status" ' +
					'data-product_option_idx="' + rowobject.product_option_idx + '"' +
					'data-stock_unit_price="' + rowobject.stock_unit_price + '" ' +
					'data-stock_status="NORMAL"' +
					' >' + Common.addCommas(cellvalue) + '</a>';
			}},
		{label: '기간 배송', name: 'stock_amount_OUT', index: 'stock_amount_OUT', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '조회기간<br>평균송장', name: 'stock_amount_INVOICE_AVG', index: 'stock_amount_INVOICE_AVG', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				var in_amount = rowobject.stock_amount_INVOICE;
				var date_count = userData.date_count;
				var rst = in_amount / date_count;
				return rst.toFixed(2);
			}},
		{label: '조회기간<br>평균입고', name: 'stock_amount_IN_AVG', index: 'stock_amount_IN_AVG', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				var in_amount = rowobject.stock_amount_IN;
				var date_count = userData.date_count;
				var rst = in_amount / date_count;
				return rst.toFixed(2);
			}},
		{label: '조회기간<br>평균배송', name: 'stock_amount_SHIPPED_AVG', index: 'stock_amount_SHIPPED_AVG', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var userData = $("#grid_list").jqGrid("getGridParam", "userData");
				var out_amount = rowobject.stock_amount_SHIPPED;
				var date_count = userData.date_count;
				var rst = out_amount / date_count;
				return rst.toFixed(2);
			}},
		{label: '직전7일<br>평균송장', name: 'stock_amount_INVOICE_prevweek_AVG', index: 'stock_amount_INVOICE_prevweek_AVG', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var in_amount = rowobject.stock_amount_INVOICE_prevweek;
				var date_count = 7;
				var rst = in_amount / date_count;
				return rst.toFixed(2);
			}},
		{label: '소진일', name: 'stock_spend', index: 'stock_spend', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var in_amount = rowobject.stock_amount_INVOICE_prevweek;
				var date_count = 7;
				var prevWeekInvoiceAVG = in_amount / date_count;

				var normal = rowobject.stock_amount_NORMAL;

				var rst = 0
				if(normal == 0 || prevWeekInvoiceAVG == 0){
				}else {
					rst = normal / prevWeekInvoiceAVG;
				}

				return rst.toFixed(2);
			}},
		{label: '입고요청', name: 'stock_order_amount', index: 'stock_order_amount', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '입고<br><span class="th_period">MM/DD ~ MM/DD</span>', name: 'stock_amount_IN', index: 'stock_amount_IN', width: 120, sortable: false, is_use : true, is_readonly: true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '입고대기<br>(발주)', name: 'stock_amount_STOCKORDER', index: 'stock_amount_STOCKORDER', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
			{label: '입고대기<br>(CS)', name: 'stock_amount_ORDER_STOCKIN', index: 'stock_amount_ORDER_STOCKIN', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '차트', name: 'btn_chart', index: 'btn_chart', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return '<a href="javascript:;" class="btn btn-stock-chart" data-product_option_idx="'+rowobject.product_option_idx+'" data-stock_unit_price="">차트</a>'
			}},
	],
	STOCK_CONFIRM_LIST: [
		{ label: '재고IDX', name: 'stock_idx', index: 'A.stock_idx', width: 0, sortable: false, hidden: true},
		{ label: '구분', name: 'stock_kind_han', index: 'stock_kind_han', width: 50, sortable: false},
		{ label: '코드', name: 'stock_order_idx', index: 'A.stock_order_idx', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
				if(rowobject.stock_kind == 'STOCK_ORDER'){
					return cellvalue;
				}else{
					return rowobject.order_idx;
				}

			}},
		{ label: '생성일', name: 'stock_request_date', index: 'stock_request_date', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
				return Common.toDateTimeOnlyDate(cellvalue);
			}},
		{ label: '입고일', name: 'stock_in_date', index: 'stock_in_date', width: 100, sortable: false},
		{ label: '확정일', name: 'stock_is_confirm_date', index: 'stock_is_confirm_date', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
			return Common.toDateTimeOnlyDate(cellvalue);
		}},
		{ label: '작업자', name: 'member_id', index: 'member_id', width: 80, sortable: false},
		{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false},
		{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100, sortable: false},
		{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, align: 'left', sortable: false},
		{ label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, align: 'left', sortable: false},
		{ label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 80, align: 'right', sortable: false, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{ label: '구매자정보', name: 'order_info', index: 'order_info', width: 100, sortable: false, align: 'left'},
		{ label: '예정수량', name: 'stock_due_amount', index: 'stock_due_amount', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{ label: '입고수량', name: 'stock_amount', index: 'stock_amount', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{ label: '상태', name: 'stock_status_name', index: 'stock_status_name', width: 60, sortable: false},
		{ label: '비고', name: 'btn_action', index: 'btn_action', width: 200, align: 'left', sortable: false, formatter: function(cellvalue, options, rowobject){
				var btnz = '';
				btnz += rowobject.stock_msg + ' ';
				btnz += '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-detail" data-stock_idx="' + rowobject.stock_idx + '">상세</a>';

				if(rowobject.stock_file_idx != 0){
					btnz += ' <a href="javascript:;" class="btn-stock-confirm-file-down" data-stock_file_idx="' + rowobject.stock_file_idx + '" data-stock_file_name="' + rowobject.stock_file_name + '" title="첨부파일"><i class="far fa-file"></i></a>';
				}
				return btnz;

			}},
		{ label: '입고확정', name: 'btn_confirm', index: 'btn_confirm', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
				var btnz = '';
				if(rowobject.stock_is_confirm == "N") {
					btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-confirm-exec" data-stock_idx="' + rowobject.stock_idx + '">확정</a>';
				}
				return btnz;

			}},
	],
	ORDER_SEARCH_LIST: [
		{label: '가접수 시간', name: 'order_progress_step_accept_temp_date', index: 'order_progress_step_accept_temp_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return Common.toDateTime(cellvalue);
			}
		},
		{label: '발주시간', name: 'order_progress_step_accept_date', index: 'order_progress_step_accept_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return Common.toDateTime(cellvalue);
			}
		},

		{label: '주문일', name: 'order_pay_date', index: 'order_pay_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return Common.toDateTime(cellvalue);
			}
		},
		{label: '관리번호', name: 'order_idx', index: 'order_idx', width: 100, is_use : true},
		{label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: false, is_use : true},
		{label: '수령자<br>이름', name: 'receive_name', index: 'receive_name', width: 100, sortable: false, is_use : true},
		{label: '구분', name: 'product_sale_type', index: 'product_sale_type', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				if(cellvalue == 'SELF'){
					return '사입';
				}else{
					return '위탁';
				}

			}},
		{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left', is_use : true},
		{label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left', is_use : true},
		{label: '주문수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				//console.log(cellvalue);
				return Common.addCommas(cellvalue);
			}},
		{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 120, sortable: false, is_use : true},
		{label: '현재고', name: 'current_stock_amount', index: 'current_stock_amount', width: 80, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				return Common.addCommas(cellvalue);
			}},
		{label: '상태', name: 'order_progress_step_han', index: 'order_progress_step_han', width: 80, sortable: false, is_use : true},
		{label: 'C/S', name: 'order_cs_status_han', index: 'order_cs_status_han', width: 80, sortable: false, is_use : true},
		{label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 120, sortable: false, is_use : true},
		{label: '송장입력일', name: 'invoice_date', index: 'invoice_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return Common.toDateTimeOnlyDate(cellvalue);
			}
		},
		{label: '송장번호', name: 'invoice_no', index: 'invoice_no', width: 100, sortable: false, is_use : true},
		{label: '배송일', name: 'shipping_date', index: 'shipping_date', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
				return Common.toDateTimeOnlyDate(cellvalue);
			}
		},
		{label: '판매처<br>상품코드', name: 'market_product_no', index: 'market_product_no', width: 150, sortable: false, is_use : true}
	],
	LOSS_LIST: [
		{ label: 'loss_idx', name: 'loss_idx', index: 'loss_idx', width: 100, sortable: false, hidden: true, is_use : false, is_readonly: true},
		{ label: 'loss_confirm', name: 'loss_confirm', index: 'loss_confirm', width: 100, sortable: false, hidden: true, is_use : false, is_readonly: true},
		{ label: '정산일', name: 'loss_date', index: 'loss_date', width: 100, sortable: false, classes : 'loss', is_use : true},

		{ label: '판매일', name: 'settle_date', index: 'settle_date', width: 100, sortable: false, is_use : true},
		{ label: '주문번호', name: 'market_order_no2', index: 'market_order_no2', width: 100, sortable: false, is_use : true},
		{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, sortable: false, is_use : true},
		{ label: '옵션', name: 'product_option_name', index: 'product_option_name', width: 100, sortable: false, is_use : true},
		{ label: '고객명', name: 'order_name2', index: 'order_name2', width: 100, sortable: false, is_use : true},
		{ label: '수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 100, sortable: false, is_use : true, align: 'right', formatter: jqGridFormatInteger},
		{ label: '판매가', name: 'settle_sale_supply', index: 'settle_sale_supply', width: 100, sortable: false, is_use : true, align: 'right', formatter: jqGridFormatInteger},
		{ label: '배송비', name: 'settle_delivery_in_vat', index: 'settle_delivery_in_vat', width: 100, sortable: false, is_use : true, align: 'right', formatter: jqGridFormatInteger},
		{ label: '합계<br>(판매가+배송비)', name: 'sale_sum', index: 'sale_sum', width: 100, sortable: false, is_use : true, align: 'right', formatter: jqGridFormatInteger},
		{ label: '판매가 수수료', name: 'settle_sale_commission_in_vat', index: 'settle_sale_commission_in_vat', width: 100, sortable: false, is_use : true, align: 'right', formatter: jqGridFormatInteger},
		{ label: '배송비 수수료', name: 'settle_delivery_commission_in_vat', index: 'settle_delivery_commission_in_vat', width: 100, sortable: false, is_use : true, align: 'right', formatter: jqGridFormatInteger},
		{ label: '합계<br>(수수료 제외)', name: 'total_sum', index: 'total_sum', width: 100, sortable: false, is_use : true, align: 'right', formatter: jqGridFormatInteger},
		{ label: '정산/배송비', name: 'settle_settle_amt', index: 'settle_settle_amt', width: 100, sortable: false, is_use : true, align: 'right', formatter: jqGridFormatInteger},

		{ label: '주문번호', name: 'market_order_no', index: 'market_order_no', width: 100, sortable: false, is_use : true, classes : 'loss'},
		{ label: '구매자명', name: 'order_name', index: 'order_name', width: 100, sortable: false, is_use : true, classes : 'loss'},
		{ label: '제품명', name: 'market_product_name', index: 'market_product_name', width: 100, sortable: false, is_use : true, align: 'left', classes : 'loss'},
		{ label: '옵션', name: 'market_product_option', index: 'market_product_option', width: 100, sortable: false, is_use : true, align: 'left', classes : 'loss'},
		{ label: '판매수량', name: 'order_cnt', index: 'order_cnt', width: 100, sortable: false, is_use : true, align: 'right', classes : 'loss', formatter: jqGridFormatInteger},
		{ label: '매출금액', name: 'order_amt', index: 'order_amt', width: 100, sortable: false, is_use : true, align: 'right', classes : 'loss', formatter: jqGridFormatInteger},
		{ label: '수수료', name: 'commission', index: 'commission', width: 100, sortable: false, is_use : true, align: 'right', classes : 'loss', formatter: jqGridFormatInteger},
		{ label: '공제/환급내역<br>기타수수료', name: 'commission_etc', index: 'commission_etc', width: 100, sortable: false, is_use : true, align: 'right', classes : 'loss', formatter: jqGridFormatInteger},
		{ label: '배송비', name: 'delivery_fee', index: 'delivery_fee', width: 100, sortable: false, is_use : true, align: 'right', classes : 'loss', formatter: jqGridFormatInteger},
		{ label: '정산금액', name: 'settle_amount', index: 'settle_amount', width: 100, sortable: false, is_use : true, align: 'right', classes : 'loss', formatter: jqGridFormatInteger},
		{ label: '확인', name: 'confirm', index: 'confirm', width: 100, sortable: false, is_use : true, formatter: function(cellvalue, options, rowobject){
				var val = "";
				if(rowobject.settle_settle_amt == rowobject.settle_amount)
				{
					val = "OK";
				}else{
					if(rowobject.loss_confirm == "Y"){
						val = "확인";
					}else{
						val = '<a href="javascript:;" class="xsmall_btn btn-confirm" data-idx="'+rowobject.loss_idx+'">확인</a>'
					}
				}
				return val;
			}},
	],
};