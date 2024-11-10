/*
 * 매칭 정보 관리js
 */
var ProductMatching = (function() {
	var root = this;

	var init = function() {
	};

	/**
	 * 매칭정보 목록 초기화
	 * @constructor
	 */
	var ProductMatchingListInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

		//판매처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

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

		//엑셀 다운로드
		$(".btn-product-matching-xls-down").on("click", function(){
			ProductMatchingListXlsDown();
		});

		var _colModel = [
			{label: '매칭IDX', name: 'matching_info_idx', index: 'matching_info_idx', width: 100, sortable: false, hidden: true, cellattr:jsFormatterComparePrimaryKey},
			{label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
			{label: '판매처<br>상품코드', name: 'market_product_no', index: 'market_product_no', width: 100, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
			{label: '판매처 상품명', name: 'market_product_name', index: 'market_product_name', width: 100, sortable: false, align: 'left', cellattr:jsFormatterCompareAndSetRowSpan},
			{label: '판매처 옵션', name: 'market_product_option', index: 'market_product_option', width: 100, sortable: false, align: 'left', cellattr:jsFormatterCompareAndSetRowSpan},
			{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false},
			{label: '상품코드', name: 'product_idx', index: 'product_idx', width: 100, sortable: false, hidden: true},
			{label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100, sortable: false},
			{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left'},
			{label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left'},
			{label: '수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 60, sortable: false},
			{label: '등록일', name: 'matching_info_regdate', index: 'matching_info_regdate', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
					return Common.toDateTime(cellvalue);
				}
				, cellattr:jsFormatterCompareAndSetRowSpan
			},
			{label: '등록계정', name: 'member_id', index: 'member_id', width: 80, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
			{ label: '삭제', name: 'btn_action', index: 'btn_action', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
					var btnz = '';
					btnz = '<a href="javascript:;" class="xsmall_btn red_btn btn-product-matching-delete" data-matching_info_idx="'+rowobject.matching_info_idx+'">삭제</a>'
					return btnz;

				}
				, cellattr:jsFormatterCompareAndSetRowSpan
			},
		];

		if(!isDYLogin) {
			shrinkToFit = true;
			var _colModelVendor = new Array();
			$.each(_colModel, function (i, o) {
				if(o.name != "supplier_name")
				{
					_colModelVendor.push(o);
				}
			});

			_colModel = _colModelVendor;
		}

		//상품 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './product_matching_list_grid.php',
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
			colModel: _colModel,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'A.matching_info_idx',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			multiselect: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				var grid = this;
				$('td[name="cellRowspan"]', grid).each(function() {
					var spans = $('td[rowspanid="'+this.id+'"]',grid).length+1;
					if(spans>1){
						$(this).attr('rowspan',spans).addClass("bg-force-white");
					}
				});

				$("td[data-is-key='1']").parent().find("td").addClass("bold_top_line");

				//삭제 버튼 바인딩
				$(".btn-product-matching-delete").on("click", function(){
					ProductMatchingDeleteOne($(this).data("matching_info_idx"));
				});
			},
			beforeRequest: function(){
				chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

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
				ProductMatchingListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ProductMatchingListSearch();
		});

		//다중삭제
		$(".btn-product-matching-multi-delete").on("click", function() {
			var params = new Array();
			var idArry = $("#grid_list").jqGrid('getDataIDs');

			for (var i = 0; i < idArry.length; i++) { //row id수만큼 실행
				//체크 된 데이터 확인
				if ($("input:checkbox[id='jqg_grid_list_" + idArry[i] + "']").is(":checked") === true) {
					//체크 된 데이터 중 disable 처리된 행 제외 (전체 체크 선택시 숨겨진행 도 체크 됨);
					var rowdata = $("#grid_list").getRowData(idArry[i]);
					params.push(rowdata.matching_info_idx);
				}
			}
			if(params.length === 0){
				alert("선택 된 값이 없습니다.")
			} else {
				console.log(params.join(','))
				ProductMatchingDeleteMulti(params)
			}
		});
	};

	/**
	 * jqGrid 셀 병합을 위한 임시 저장변수
	 * @type {{chkval: undefined, rowNo: number, cellId: undefined}}
	 */
	var chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크

	/**
	 * 매칭정보 목록 셀병합 함수 1
	 * jqGrid 셀 병합을 위한 함수 (Key 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterComparePrimaryKey = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		//console.log(this.id);
		var cellId = this.id + '_row_'+rowObject.matching_info_idx+'-'+cm.name;
		if(chkcell.chkval != rowObject.matching_info_idx && rowid != chkcell.rowNo){ //check 값이랑 비교값이 다른 경우
			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan" data-is-key="1"';
			//alert(result);
			chkcell = {cellId:cellId, chkval:rowObject.matching_info_idx, rowNo: rowid};
		}else{
			result = 'style="display: none;"  rowspanid="'+cellId+'"'; //같을 경우 display none 처리
			//alert(result);
		}
		return result;
	};

	/**
	 * 매칭정보 목록 셀병합 함수 2
	 * jqGrid 셀병합을 위한 함수 (일반 Cell 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterCompareAndSetRowSpan = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		//console.log(cm);

		var cellId = this.id + '_row_'+rowObject.matching_info_idx+'-'+cm.name;
		if(chkcell.chkval == rowObject.matching_info_idx && rowid == chkcell.rowNo){ //check 값이랑 비교값이 다른 경우

			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan"';

		}else{
			result = 'style="display: none;" rowspanid="'+cellId+'"'; //같을 경우 display none 처리

		}
		return result;
	};

	/**
	 * 매칭정보 목록/검색
	 * @constructor
	 */
	var ProductMatchingListSearch = function(){

		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 매칭정보 단일 삭제
	 * @param matching_info_idx
	 * @constructor
	 */
	var ProductMatchingDeleteOne = function(matching_info_idx){
		if(confirm('매칭정보를 삭제하시겠습니까?')){

			var p_url = "/product/product_matching_proc.php";
			var dataObj = new Object();
			dataObj.mode = "product_matching_info_delete_one";
			dataObj.matching_info_idx = matching_info_idx;
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					alert('삭제되었습니다.');
					ProductMatchingListSearch();
				} else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	/**
	 * 매칭정보 다중 삭제
	 * @param matching_info_idx
	 * @constructor
	 */
	var ProductMatchingDeleteMulti = function(matching_info_idx){

		var confirm_msg = " 총 "+ matching_info_idx.length +" 건의 데이터를 \n 삭제 처리 하시겠습니까?";
		if(confirm(confirm_msg)){

			var p_url = "/product/product_matching_proc.php";
			var dataObj = new Object();
			dataObj.mode = "product_matching_info_delete_multi";
			dataObj.matching_info_idx = matching_info_idx;
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					alert('삭제되었습니다.');
					ProductMatchingListSearch();
				} else {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	/**
	 * 매칭정보 엑셀 다운로드
	 * @constructor
	 */
	var ProductMatchingListXlsDown = function(){
		var param = $("#searchForm").serialize();
		location.href="product_matching_xls_down.php?"+param;
	};

	/**
	 * 매칭정보삭제로그 페이지 초기화
	 * @constructor
	 */
	var ProductMatchingDeleteListInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

		//판매처 그룹 및 공급처 선택창 초기화
		CommonFunction.bindManageGroupList("SELLER_GROUP", ".product_seller_group_idx", ".seller_idx");
		$(".seller_idx").SumoSelect({
			placeholder: '전체 판매처',
			captionFormat : '{0}개 선택됨',
			captionFormatAllSelected : '{0}개 모두 선택됨',
			search: true,
			searchText: '판매처 검색',
			noMatch : '검색결과가 없습니다.'
		});

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

		//엑셀 다운로드
		$(".btn-product-matching-xls-down").on("click", function(){
			ProductMatchingDeleteListXlsDown();
		});

		var _colModel = [
			{label: '매칭IDX', name: 'matching_info_idx', index: 'matching_info_idx', width: 100, sortable: false, hidden: true, cellattr:jsFormatterComparePrimaryKey},
			{label: '판매처', name: 'seller_name', index: 'seller_name', width: 100, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
			{label: '판매처<br>상품코드', name: 'market_product_no', index: 'market_product_no', width: 100, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
			{label: '판매처 상품명', name: 'market_product_name', index: 'market_product_name', width: 100, sortable: false, align: 'left', cellattr:jsFormatterCompareAndSetRowSpan},
			{label: '판매처 옵션', name: 'market_product_option', index: 'market_product_option', width: 100, sortable: false, align: 'left', cellattr:jsFormatterCompareAndSetRowSpan},
			{label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false},
			{label: '상품코드', name: 'product_idx', index: 'product_idx', width: 100, sortable: false},
			{label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left'},
			{label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left'},
			{label: '수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 60, sortable: false},
			{label: '등록일', name: 'matching_info_regdate', index: 'matching_info_regdate', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
					return Common.toDateTime(cellvalue);
				}
				, cellattr:jsFormatterCompareAndSetRowSpan
			},
			{label: '등록계정', name: 'member_id', index: 'member_id', width: 80, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
			{label: '삭제일', name: 'matching_info_moddate', index: 'matching_info_moddate', width: 150, is_use : true, formatter: function (cellvalue, options, rowobject) {
					return Common.toDateTime(cellvalue);
				}
				, cellattr:jsFormatterCompareAndSetRowSpan
			},
			{label: '삭제계정', name: 'last_member_id', index: 'last_member_id', width: 80, sortable: false, cellattr:jsFormatterCompareAndSetRowSpan},
		];

		if(!isDYLogin) {
			shrinkToFit = true;
			var _colModelVendor = new Array();
			$.each(_colModel, function (i, o) {
				if(o.name != "supplier_name")
				{
					_colModelVendor.push(o);
				}
			});

			_colModel = _colModelVendor;
		}

		//상품 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './product_matching_delete_list_grid.php',
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
			colModel: _colModel,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'A.matching_info_moddate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				var grid = this;
				$('td[name="cellRowspan"]', grid).each(function() {
					var spans = $('td[rowspanid="'+this.id+'"]',grid).length+1;
					if(spans>1){
						$(this).attr('rowspan',spans);
					}
				});

				//삭제 버튼 바인딩
				$(".btn-product-matching-delete").on("click", function(){
					ProductMatchingDeleteOne($(this).data("matching_info_idx"));
				});
			},
			beforeRequest: function(){
				chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

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
				ProductMatchingDeleteListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			ProductMatchingDeleteListSearch();
		});
	};

	/**
	 * 매칭정보삭제로그 목록/검색
	 * @constructor
	 */
	var ProductMatchingDeleteListSearch = function(){

		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 매칭정보삭제로그 엑셀 다운로드
	 * @constructor
	 */
	var ProductMatchingDeleteListXlsDown = function(){
		// var param = $("#searchForm").serialize();
		// location.href="product_matching_delete_xls_down.php?"+param;
		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};
		location.href="product_matching_delete_xls_down.php?"+$.param(dataObj);
	};

	var xlsValidRow = 0;                //업로드된 엑셀 Row 중 정상인 Row 수
	var xlsUploadedFileName = "";       //업로드 된 엑셀 파일명
	var xlsWritePageMode = "";          //일괄등록 / 일괄수정 Flag
	var xlsWriteReturnStyle = "";       //리스트 반환 또는 적용
	//벤더사 일괄등록 / 일괄수정 페이지 초기화
	var xlsWriteInsertExcludeList = []; //반영 제외 리스트 xls_idx array
	/**
	 * 매칭일괄등록 페이지 초기화
	 * @constructor
	 */
	var ProductMatchingXlsWriteInit = function(){

		xlsWritePageMode = $("#xlswrite_mode").val();
		xlsWriteReturnStyle = $("#xlswrite_act").val();

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
			if(xlsValidRow < 1)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = xlsValidRow + "건의 데이터를 적용 하시겠습니까?";
				if(confirm(msg)) {
					ProductMatchingXlsInsert();
				}
			}
		});

		ProductMatchingXlsWriteGridInit();
	};

	/**
	 * 매칭정보 일괄 등록/수정 페이지 jqGrid 초기화
	 * @constructor
	 */
	var ProductMatchingXlsWriteGridInit = function(){
		var validErr = [];

		var colModel = [];
		if(xlsWritePageMode == "add")
		{
			colModel = [
				{ label: '엑셀일련번호', name: 'xls_idx', index: 'xls_idx', width: 50, sortable: false, hidden: true, cellattr:jsFormatterComparePrimaryKeyXlsRegist},
				{ label: '판매처', name: 'A', index: 'supplier_name', width: 100, sortable: false, cellattr:jsFormatterCompareAndSetRowSpanXlsRegist},
				{ label: '판매처<br>상품코드', name: 'B', index: 'market_product_no', width: 120, sortable: false, cellattr:jsFormatterCompareAndSetRowSpanXlsRegist},
				{ label: '판매처 상품명', name: 'C', index: 'market_product_name', width: 150, sortable: false, align: 'left', cellattr:jsFormatterCompareAndSetRowSpanXlsRegist},
				{ label: '판매처 옵션', name: 'D', index: 'market_product_option', width: 150, sortable: false, align: 'left', cellattr:jsFormatterCompareAndSetRowSpanXlsRegist},
				{ label: '상품코드', name: 'product_idx', index: 'product_idx', width: 80, sortable: false, hidden: true},
				{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 80, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, sortable: false, align: 'left'},
				{ label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, sortable: false, align: 'left'},
				{ label: '수량', name: 'product_option_cnt', index: 'product_option_cnt', width: 80, sortable: false},
				{ label: '등록계정', name: 'member_id', index: 'member_id', width: 80, sortable: false, cellattr:jsFormatterCompareAndSetRowSpanXlsRegist},
				{ label: '등록제외', name: 'btnz', index: 'btnz', width: 80, sortable: false, cellattr:jsFormatterCompareAndSetRowSpanXlsRegist, formatter: function(cellvalue, options, rowobject){
					if(rowobject.valid) {
						return ' <a href="javascript:;" class="xsmall_btn red_btn btn-regist-exclude" data-xls_idx="' + rowobject.xls_idx + '">제외</a>';
					}else{
						return '';
					}
					}},
				{ label: '비고', name: 'valid', index: 'valid', width: 80, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;
						if(cellvalue)
						{
							rst = "정상";
							xlsValidRow++;
						}else{
							rst = "오류";
							validErr.push(options.rowId);
						}
						return rst;
					}
					, cellattr:jsFormatterCompareAndSetRowSpanXlsRegist
				},
			];
		}else{
			colModel = [
				{ label: '엑셀일련번호', name: 'xls_idx', index: 'xls_idx', width: 50, sortable: false, hidden: true},
				{ label: '판매처', name: 'A', index: 'supplier_name', width: 100, sortable: false},
				{ label: '판매처<br>상품코드', name: 'B', index: 'market_product_no', width: 120, sortable: false},
				{ label: '판매처 상품명', name: 'C', index: 'market_product_name', width: 150, sortable: false, align: 'left'},
				{ label: '판매처 옵션', name: 'D', index: 'market_product_option', width: 150, sortable: false, align: 'left'},
				{ label: '등록계정', name: 'member_id', index: 'member_id', width: 80, sortable: false},
				{ label: '삭제제외', name: 'btnz', index: 'btnz', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						if(rowobject.valid) {
							return ' <a href="javascript:;" class="xsmall_btn red_btn btn-regist-exclude" data-xls_idx="' + rowobject.xls_idx + '">제외</a>';
						}else{
							return '';
						}
					}},
				{ label: '비고', name: 'valid', index: 'valid', width: 80, sortable: false
					, formatter: function(cellvalue, options, rowobject){
						var rst;
						if(cellvalue)
						{
							rst = "정상";
							xlsValidRow++;
						}else{
							rst = "오류";
							validErr.push(options.rowId);
						}
						return rst;
					}
				},
			];
		}

		$("#grid_list").jqGrid({
			url: './product_matching_proc_xls.php',
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
			colModel: colModel,
			rowNum:1000,
			pager: '#grid_pager',
			sortname: 'regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){

				//제외 리스트 초기화
				xlsWriteInsertExcludeList = [];

				var grid = this;
				$('td[name="cellRowspan"]', grid).each(function() {
					var spans = $('td[rowspanid="'+this.id+'"]',grid).length+1;
					if(spans>1){
						$(this).attr('rowspan',spans);

						//병합된 셀일 경우 적용 가능한 Row수에서 빼준다.
						if($(this).attr("aria-describedby") == "grid_list_btnz") {
							xlsValidRow = xlsValidRow - (spans - 1);
						}
					}
				});

				$.each(validErr, function(k, v){
					$("#grid_list #"+v).addClass("upload_err");
					validErr = [];
				});


				//등록제외버튼
				$(".btn-regist-exclude").on("click", function(){
					var xls_idx = $(this).data("xls_idx");
					var rowSpan = $(this).parent().attr("rowspan");
					$(this).parent().parent().addClass("exclude");
					if(rowSpan > 1){
						$(this).parent().parent().nextAll("*:lt("+(rowSpan - 1)+")").addClass("exclude");
					}
					$(this).remove();

					xlsWriteInsertExcludeList.push(xls_idx);

					//적용 가능한 Row수에서 빼준다.
					xlsValidRow = xlsValidRow - 1;
				});

			},
			beforeRequest: function(){
				chkcell = {cellId:undefined, chkval:undefined, rowNo: 0}; //cell rowspan 중복 체크
			}
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	/**
	 * 매칭일괄등록 엑셀 목록 셀병합 함수 1
	 * jqGrid 셀 병합을 위한 함수 (Key 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterComparePrimaryKeyXlsRegist = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		var cellId = this.id + '_row_'+rowObject.xls_idx+'-'+cm.name;
		if(chkcell.chkval != rowObject.xls_idx && rowid != chkcell.rowNo){ //check 값이랑 비교값이 다른 경우
			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan"';
			//alert(result);
			chkcell = {cellId:cellId, chkval:rowObject.xls_idx, rowNo: rowid};
		}else{
			result = 'style="display: none;"  rowspanid="'+cellId+'"'; //같을 경우 display none 처리
			//alert(result);
		}
		return result;
	};

	/**
	 * 매칭일괄등록 엑셀 목록 셀병합 함수 2
	 * jqGrid 셀병합을 위한 함수 (일반 Cell 용)
	 * @param rowid
	 * @param val
	 * @param rowObject
	 * @param cm
	 * @param rdata
	 * @returns {string}
	 */
	var jsFormatterCompareAndSetRowSpanXlsRegist = function(rowid, val, rowObject, cm, rdata){
		var result = "";
		//console.log(cm);

		var cellId = this.id + '_row_'+rowObject.xls_idx+'-'+cm.name;
		if(chkcell.chkval == rowObject.xls_idx && rowid == chkcell.rowNo){ //check 값이랑 비교값이 다른 경우

			result = ' rowspan="1" id ="'+cellId+'" name="cellRowspan"';

		}else{
			result = 'style="display: none;" rowspanid="'+cellId+'"'; //같을 경우 display none 처리

		}
		return result;
	};

	/**
	 * 업로드 된 엑셀 파일 로딩
	 * @param xls_file_path_name
	 * @constructor
	 */
	var ProductMatchingXlsRead = function(xls_file_path_name){
		//console.log(xls_file_path_name);
		xlsUploadedFileName = xls_file_path_name;
		$("input[name='xls_file']").val('');

		//적용할 Row 수 초기화
		xlsValidRow = 0;

		//업로드된 엑셀 바인딩 jqGrid
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				mode: xlsWritePageMode,
				act: xlsWriteReturnStyle,
				xls_filename: xls_file_path_name,
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 업로드 된 엑셀 파일 적용
	 * @constructor
	 */
	var ProductMatchingXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "product_matching_proc_xls.php";
		var dataObj = new Object();
		dataObj.mode = xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = xlsUploadedFileName;
		dataObj.xls_validrow = xlsValidRow;
		dataObj.exclude_list = xlsWriteInsertExcludeList.join(",");

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj,
			traditional: true
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

	return {
		ProductMatchingListInit : ProductMatchingListInit,
		ProductMatchingXlsWriteInit: ProductMatchingXlsWriteInit,
		ProductMatchingXlsRead: ProductMatchingXlsRead,
		ProductMatchingDeleteListInit: ProductMatchingDeleteListInit,
	}
})();