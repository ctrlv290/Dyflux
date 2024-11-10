/*
 * 재고 입고예정 관리 js
 */
var StockDue = (function() {
	var root = this;

	var init = function () {
	};

	/**
	 * 입고예정 페이지 초기화
	 * @constructor
	 */
	var StockDueListInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

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

		//신규발주 버튼 바인딩
		$(".btn-stock-order-write-pop").on("click", function(){
			StockOrderWritePop('');
		});

		//Grid 초기화
		StockDueListGridInit();

		//입고지연이력 팝업 바인딩
		$(".btn-log-for-delay").on("click", function(){
			StockDueDelayListPopup();
		});

		//다운로드 버튼 바인딩
		$(".btn-stock-due-xls-down").on("click", function(){
			StockDueListXlsDown();
		});

	};

	/**
	 * 입고예정 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockDueListGridInit = function(){
		var grid_cookie_name = "stock_due_list";
		$("#grid_list").jqGrid({
			url: './stock_due_list_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
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
				{ label: '재고IDX', name: 'stock_idx', index: 'A.stock_idx', width: 0, sortable: false, hidden: true},
				{ label: '구분', name: 'stock_kind_han', index: 'stock_kind_han', width: 80, sortable: false},
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
				{ label: '입고<br>예정일', name: 'stock_due_date', index: 'stock_due_date', width: 100, sortable: false},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 100, sortable: false},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false},
				{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 150, align: 'left', sortable: false},
				{ label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 150, align: 'left', sortable: false},
				{ label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 80, align: 'right', sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '구매자정보', name: 'receive_name', index: 'receive_name', width: 150, sortable: false, align: 'left', classes: 'multiline', formatter: function(cellvalue, options, rowobject){
					var rst = "";
					if(cellvalue != "null" && cellvalue != null){
						rst += cellvalue + '/' + rowobject.receive_hp_num + '/' + rowobject.receive_addr1 + ' ' + rowobject.receive_addr2;
					}
						return rst;
					}},
				{ label: '예정수량', name: 'stock_due_amount', index: 'stock_due_amount', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '입고수량', name: 'stock_amount', index: 'stock_amount', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						//입고 처리 된 재고만 입고 수량 표시
						if(rowobject.stock_is_proc == "Y") {
							return Common.addCommas(cellvalue);
						}else{
							return '-';
						}
					}},
				{ label: '상태', name: 'stock_status_name', index: 'stock_status_name', width: 100, sortable: false},
				{ label: '작업', name: 'result', index: 'result', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject) {

						if (rowobject.stock_is_proc == "N") {
							if(rowobject.stock_order_is_ready == "Y"){
								return '미처리';
							}else if(rowobject.stock_kind == "RETURN"){
								return '미처리';
							}else{
								return '미처리<br>(추가입고)';
							}

						} else if (rowobject.stock_is_proc == "Y") {
							if(rowobject.stock_is_confirm == "Y") {
								return '처리완료<br>(입고확정)';
							}else{
								return '처리완료';
							}
						}
					}},
				{ label: '입고확인', name: 'btn_action', index: 'btn_action', width: 200, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						if (rowobject.stock_is_proc == "N") {
							btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-due-part" data-stock_idx="' + rowobject.stock_idx + '">입고처리</a>'
						} else if (rowobject.stock_is_proc == "Y") {
						}
						btnz += ' <a href="javascript:;" class="xsmall_btn btn-stock-detail" data-stock_idx="' + rowobject.stock_idx + '">상세</a>';
						btnz += ' <a href="javascript:;" class="xsmall_btn red_btn btn-stock-due-add" data-stock_idx="' + rowobject.stock_idx + '">추가입고</a>';
						return btnz;

					}},
				{ label: '이력관리', name: 'btn_delay', index: 'btn_delay', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						if(rowobject.stock_is_proc == "N") {
							btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-due-delay" data-stock_idx="' + rowobject.stock_idx + '">지연</a>';
						}
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'stock_request_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){

				//전체입고 - 삭제
				/*
				$(".btn-stock-due-finish").on("click", function(){
					StockReceivingAll($(this).data("stock_idx"));
				});
				*/

				//입고처리
				$(".btn-stock-due-part").on("click", function(){
					var stock_idx = $(this).data("stock_idx");
					StockReceivingPartialPopup(stock_idx);
				});

				//추가입고
				$(".btn-stock-due-add").on("click", function(){
					var stock_idx = $(this).data("stock_idx");
					StockDueAddPopup(stock_idx);
				});

				//지연
				$(".btn-stock-due-delay").on("click", function(){
					var stock_idx = $(this).data("stock_idx");
					StockDueDelayWritePopup(stock_idx);
				});

				//상세
				$(".btn-stock-detail").on("click", function(){
					StockConfirmDetailPopup($(this).data("stock_idx"));
				});

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_due_list", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_due_list");
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
				StockDueListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockDueListSearch();
		});
	};

	/**
	 * 입고예정 목록/검색
	 * @constructor
	 */
	var StockDueListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 입고예정 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockDueListXlsDown = function(){
		var param = $("#searchForm").serialize();
		location.href="stock_due_xls_down.php?"+param;
	};

	/**
	 * 전체입고 실행
	 * @param stock_idx
	 * @constructor
	 */
	var StockReceivingAll = function(stock_idx){
		if(confirm('전체입고 처리 하시겠습니까?')){
			var p_url = "/stock/stock_due_proc.php";
			var dataObj = new Object();
			dataObj.mode = "stock_receiving_all";
			dataObj.stock_idx = stock_idx;

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					alert('전체입고 처리 되었습니다.');
					StockDueListSearch();
				} else {
					alert(response.msg);
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	/**
	 * 부분입고확인 팝업 Open
	 * @param stock_idx
	 * @constructor
	 */
	var StockReceivingPartialPopup = function(stock_idx){
		Common.newWinPopup("stock_due_write_pop.php?stock_idx="+stock_idx, 'stock_due_write_pop', 800, 750, 'yes');
	}

	/**
	 * 부분입고확인 페이지 폼 진행 여부
	 * @type {boolean}
	 */
	var stockDueWriteFormIng = false;
	/**
	 * 부분입고확인 페이지 초기화
	 * @constructor
	 */
	var StockReceivingPartialInit = function(){

		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn-stock-file-idx', {
			_target_table : 'DY_STOCK',
			_target_table_column : 'stock_file_idx',
			_target_filename : '.span_stock_file_idx',
			_target_input_hidden : '#stock_file_idx',
			_upload_no: 1,
			_upload_type : "stock_document",
		});

		//Input Mask 바인딩
		$(".stock_amount").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 6, autoGroup: true, rightAlign: false, nullable: false, clearIncomplete: 0});

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			if(stockDueWriteFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				var checkTable = true;
				var amount_sum = 0;
				$.each($(".stock_amount"), function(i, o){
					var val = $(this).val().replace(/,/gi, '');   //콤마제거

					if(val == ""){
						alert("수량을 입력해주세요.");
						$(this).focus();
						checkTable = false;
						return false;
					}else if(val == "0"){
					}else{
						var dt = $(".stock_due_date").eq(i).val();
						if(dt.length != 10){
							alert("입고일을 입력해주세요.");
							checkTable = false;
							return false;
						}

						amount_sum += parseInt(val);
					}
				});

				if(!checkTable){
					return false;
				}

				if(amount_sum == 0){
					alert("입고처리는 하나 이상의 항목의 갯수가 0 이상이어야 합니다.");
					return false;
				}

				var txt_stock_due_amount = parseInt($(".txt_stock_due_amount").text().replace(/,/gi, ''));

				if(amount_sum != txt_stock_due_amount){
					if(!confirm('예정수량과 입력하신 수량의 합이 맞이 않습니다.\n그대로 진행하시겠습니까?')){
						return false;
					}
				}

				if(!confirm('입고처리 하시겠습니까?')){
					return false;
				}

				this.action = "stock_due_proc.php";
				$("#btn-save").attr("disabled", true);
				stockDueWriteFormIng = true;

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 추가입고 팝업 Open
	 * @param stock_idx
	 * @constructor
	 */
	var StockDueAddPopup = function(stock_idx){
		Common.newWinPopup("stock_due_add_pop.php?stock_idx="+stock_idx, 'stock_due_add_pop', 800, 600, 'yes');
	};

	/**
	 * 추가입고 페이지 폼 진행 여부
	 * @type {boolean}
	 */
	var stockDueAddFormIng = false;
	/**
	 * 추가입고 페이지 초기화
	 * @constructor
	 */
	var StockDueAddInit = function(){

		//Input Mask 바인딩
		$(".stock_due_amount").inputmask("numeric", {radixPoint: '.', groupSeparator: ',', digits: 6, autoGroup: true, rightAlign: false, nullable: false, clearIncomplete: 0});


		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			if(stockDueAddFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.stock_due_date, "입고예정일을 정확히 입력해주세요.", 10, 10, null)) return returnType;
				if (!valForm.chkValue(objForm.stock_due_amount, "예정수량을 정확히 입력해주세요.", 1, 10, null)) return returnType;

				if(parseInt(objForm.stock_due_amount.value) == 0){
					alert('예정수량은 0보다 커야합니다.');
					objForm.stock_due_amount.focus();
					return false;
				}

				if(!confirm('추가입고를 등록하시겠습니까?')){
					return false;
				}

				this.action = "stock_due_proc.php";
				$("#btn-save").attr("disabled", true);
				stockDueAddFormIng = true;


			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 입고지연 팝업 Open
	 * @param stock_idx
	 * @constructor
	 */
	var StockDueDelayWritePopup = function(stock_idx){
		Common.newWinPopup("stock_due_delay_write_pop.php?stock_idx="+stock_idx, 'stock_due_delay_write_pop', 800, 600, 'yes');
	};

	/**
	 * 입고지연 페이지 폼 진행 여부
	 * @type {boolean}
	 */
	var stockDueDelayWriteFormIng = false;
	/**
	 * 입고지연 페이지 초기화
	 * @constructor
	 */
	var StockDueDelayWriteInit = function(){

		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn-stock-file-idx', {
			_target_table : 'DY_STOCK_DUE_DELAY',
			_target_table_column : 'stock_due_delay_file_idx',
			_target_filename : '.span_stock_due_delay_file_idx',
			_target_input_hidden : '#stock_due_delay_file_idx',
			_upload_no: 1,
			_upload_type : "stock_document"
		});

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			if(stockDueDelayWriteFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.stock_due_delay_date, "지연 입고일을 정확히 입력해주세요.", 10, 10, null)) return returnType;


				if(!confirm('입고지연 내역을 등록하시겠습니까?')){
					return false;
				}

				this.action = "stock_due_proc.php";
				$("#btn-save").attr("disabled", true);
				stockDueDelayWriteFormIng = true;


			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 입고확정 페이지 초기화
	 * @constructor
	 */
	var StockConfirmListInit = function(){

		//Grid 초기화
		StockConfirmListGridInit();

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

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

		//다운로드 버튼 바인딩
		$(".btn-stock-confirm-list-xls-down").on("click", function(){
			StockConfirmListXlsDown();
		});

		//항목설정 팝업
		$(".btn-column-setting-pop").on("click", function(){
			Common.newWinPopup("/common/column_setting_pop.php?target=STOCK_CONFIRM_LIST&mode=list", 'column_setting_pop', 700, 720, 'no');
		});
	};

	/**
	 * 입고확정 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockConfirmListGridInit = function(){
		var search_params = $("#searchForm").serialize();
		var supplier_idx = $(".supplier_idx").attr("data-selected");

		if (supplier_idx && (search_params.indexOf("supplier_idx") < 0)) {
			search_params += "&supplier_idx%5B%5D=" + supplier_idx;
		}

		$("#grid_list").jqGrid({
			url: './stock_confirm_list_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: search_params
			},
			jsonReader : {
				page: "page",
				total: "total",
				root: "rows",
				records: "records",
				repeatitems: true,
				id: "idx"
			},
			colModel: _gridColModel,
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			multiselect:true,
			multiselectPosition:'none',
			pager: '#grid_pager',
			sortname: 'stock_request_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(complete_data){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//상세
				$(".btn-stock-detail").on("click", function(){
					StockConfirmDetailPopup($(this).data("stock_idx"));
				});
				//첨부파일 다운로드
				$(".btn-stock-confirm-file-down").on("click", function(){
					var file_idx = $(this).data("stock_file_idx");
					var file_name = $(this).data("stock_file_name");

					Common.simpleUploadedFileDown(file_idx, file_name);
				});
				//입고확정
				$(".btn-stock-confirm-exec").on("click", function(){
					var stock_idx = $(this).data("stock_idx");
					StockConfirmExec(stock_idx);
				});

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("stock_confirm_list", $("#grid_list"));


				var rowIds = $(this).jqGrid('getDataIDs');
				var total = 0;
				for (i = 1; i <= rowIds.length; i++) {
					var rowData = $(this).jqGrid('getRowData', i);

					//총 합계
					var cnt = Number(rowData["stock_amount"].replace(/,/gi, ''));
					var price = Number(rowData["stock_unit_price"].replace(/,/gi, ''));
					total += cnt * price;

					// 확정처리된 데이터 체크박스 disable
					if(rowData.stock_is_confirm_date != ""){
						$("#jqg_grid_list_"+rowIds[i-1])
							.attr("disabled", "disabled")
							.css("display", "none");
					}
				}
				$(".total_stock_sum").text(Common.addCommas(total));

			},
			beforeSelectRow: function(rowid, e) { //disabled 처리된 행 선택 방지
				var cbsdis = $("tr#"+rowid+".jqgrow > td > input.cbox:disabled", jQuery("#grid_list")[0]);
				if (cbsdis.length === 0) {
					return true;
				} else {
					return false;
				}
			},
			onSelectAll: function(aRowids,status) {
				if (status) {
					//모두선택시 disabled 처리된 행 선택 방지
					$("#grid_list").find("tr.jqgrow:has(td > input.cbox:disabled)")
						.attr('aria-selected', 'false')
						.removeClass('ui-state-highlight');
				}
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "stock_confirm_list");
			}
		});

		//다중확정
		$(".btn-stock-multi-confirm").on("click", function() {
			var params = new Array();
			var idArry = $("#grid_list").jqGrid('getDataIDs');

			for (var i = 0; i < idArry.length; i++) { //row id수만큼 실행
				//체크 된 데이터 확인
				if ($("input:checkbox[id='jqg_grid_list_" + idArry[i] + "']").is(":checked") === true) {
					//체크 된 데이터 중 disable 처리된 행 제외 (전체 체크 선택시 숨겨진행 도 체크 됨)
					if ($("input:checkbox[id='jqg_grid_list_" + idArry[i] + "']").is(":disabled") === false) {
						var rowdata = $("#grid_list").getRowData(idArry[i]);

						params.push(rowdata.stock_idx);
					}
				}
			}
			if(params.length === 0){
				alert("선택 된 값이 없습니다.")
			} else {
				console.log(params.join(','))
				StockConfirmExec(params)
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
				StockConfirmListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockConfirmListSearch();
		});
	};

	/**
	 * 입고확정 목록/검색
	 * @constructor
	 */
	var StockConfirmListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 입고확정 현재 페이지 재로딩
	 * @constructor
	 */
	var StockConfirmListReload = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	var xlsDownIng = false;
	var xlsDownInterval;

	/**
	 * 일자별 재고조회[입고량] 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockConfirmListXlsDown = function(){
		if(xlsDownIng) return;

		xlsDownIng = true;

		var param = $("#searchForm").serialize();
		var dataObj = {
			param: $("#searchForm").serialize()
		};
		var url = "stock_confirm_xls_down.php?"+$.param(dataObj);

		showLoader();
		$("#hidden_ifrm_common_filedownload").attr("src", url);

		clearInterval(xlsDownInterval);
		xlsDownInterval = setInterval(function(){
			Common.checkXlsDownWait("STOCK_CONFIRM_LIST", function(){
				StockDue.StockConfirmListXlsDownComplete();
			});
		}, 500);
	};

	var StockConfirmListXlsDownComplete = function(){
		xlsDownIng = false;
		clearInterval(xlsDownInterval);
		hideLoader();
	};

	/**
	 * 입고확정 실행!!
	 * @param stock_idx
	 * @constructor
	 */
	var StockConfirmExec = function(stock_idx){
		var confirm_msg = "";
		var p_url = "/stock/stock_confirm_proc_ajax.php";
		var dataObj = new Object();

		if(Array.isArray(stock_idx)){
			confirm_msg = " 총 "+ stock_idx.length +" 건의 데이터를 \n 입고확정 처리 하시겠습니까?";
			// stock_idx = stock_idx.join(',')
			dataObj.mode = "stock_multi_confirm_exec";
		}else{
			confirm_msg = '입고확정 처리 하시겠습니까?'
			dataObj.mode = "stock_confirm_exec";
		}
		if(confirm(confirm_msg)){
			dataObj.stock_idx = stock_idx;
			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					alert('확인 처리 되었습니다.');
					StockConfirmListReload();
				} else {
					alert(response.msg);
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	/**
	 * 입고지연 내역 페이지 팝업 Open
	 * @param stock_idx
	 * @constructor
	 */
	var StockDueDelayListPopup = function(){
		Common.newWinPopup("stock_due_delay_list_pop.php", 'stock_due_delay_list_pop', 1200, 750, 'yes');
	};

	/**
	 * 입고지연 변경이력 페이지 초기화
	 * @constructor
	 */
	var StockDueDelayListInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select2", 'period_preset_start_input2', 'period_preset_end_input2', "6");

		//Grid 초기화
		StockDueDelayListGridInit();
	};
	/**
	 * 입고지연 변경이력 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockDueDelayListGridInit = function(){
		//입고지연 변경이력 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './stock_due_delay_list_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
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
				{ label: '지연IDX', name: 'stock_due_delay_idx', index: 'A.stock_due_delay_idx', width: 0, sortable: false, hidden: true},
				{ label: '수정일', name: 'stock_due_delay_regdate', index: 'stock_due_delay_regdate', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTimeOnlyDate(cellvalue);
					}},
				{ label: '구분', name: 'stock_kind_han', index: 'stock_kind_han', width: 80, sortable: false},
				{ label: '코드', name: 'stock_order_idx', index: 'A.stock_order_idx', width: 80, sortable: false},
				{ label: '입고예정일', name: 'stock_due_date', index: 'stock_due_date', width: 100, sortable: false},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 100, sortable: false},
				{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, align: 'left', sortable: false},
				{ label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 100, align: 'left', sortable: false},
				{ label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 100, align: 'right', sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '예정수량', name: 'stock_due_amount', index: 'stock_due_amount', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '메모', name: 'btn_action', index: 'btn_action', width: 200, align: 'left', sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz += rowobject.stock_due_delay_msg;

						if(rowobject.stock_due_delay_file_idx != 0){
							btnz += ' <a href="javascript:;" class="btn-stock-due-delay-file-down" data-stock_due_delay_file_idx="' + rowobject.stock_due_delay_file_idx + '" data-stock_due_delay_file_name="' + rowobject.stock_due_delay_file_name + '" title="첨부파일"><i class="far fa-file"></i></a>';
						}

						if(rowobject.confirm_list != ''){
							btnz += '<br>';
							btnz += rowobject.confirm_list.replace(/,/gi, "<br>");
						}

						return btnz;

					}},
				{ label: '확인', name: 'btn_confirm', index: 'btn_confirm', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
							if(rowobject.is_confirm == 0) {
								btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-delay-confirm" data-stock_due_delay_idx="' + rowobject.stock_due_delay_idx + '">확인</a>';
							}
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'stock_request_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//확인 처리
				$(".btn-stock-delay-confirm").on("click", function(){
					StockDueDelayConfirm($(this).data("stock_due_delay_idx"));
				});

				//첨부파일 다운로드
				$(".btn-stock-due-delay-file-down").on("click", function(){

					var file_idx = $(this).data("stock_due_delay_file_idx");
					var file_name = $(this).data("stock_due_delay_file_name");

					Common.simpleUploadedFileDown(file_idx, file_name);

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
				StockDueDelayListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockDueDelayListSearch();
		});
	};

	/**
	 * 입고지연 변경이력 목록/검색
	 * @constructor
	 */
	var StockDueDelayListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 입고지연 확인 처리
	 * @param stock_due_delay_idx
	 * @constructor
	 */
	var StockDueDelayConfirm = function(stock_due_delay_idx){

		if(confirm('확인처리 하시겠습니까?')){
			var p_url = "/stock/stock_due_proc.php";
			var dataObj = new Object();
			dataObj.mode = "stock_due_delay_confirm";
			dataObj.stock_due_delay_idx = stock_due_delay_idx;

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if (response.result) {
					alert('확인 처리 되었습니다.');
					StockDueDelayListSearch();
				} else {
					alert(response.msg);
				}
				hideLoader();
			}).fail(function (jqXHR, textStatus) {
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}

	};

	/**
	 * 입고내역 페이지 팝업 Open
	 * @param stock_idx
	 * @constructor
	 */
	var StockConfirmDetailPopup = function(stock_idx){
		Common.newWinPopup("stock_confirm_detail_pop.php?stock_idx="+stock_idx, 'stock_confirm_detail_pop', 860, 750, 'yes');
	};

	/**
	 * 입고내역 페이지 초기화
	 * @constructor
	 */
	var StockConfirmDetailInit = function(){
		//입고처리완료 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './stock_confirm_detail_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				stock_idx: $("#stock_idx").val(),
				stock_ref_idx: $("#stock_ref_idx").val(),
				stock_is_proc: 'Y',
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
				{ label: '재고IDX', name: 'stock_idx', index: 'A.stock_idx', width: 0, sortable: false, hidden: true},
				{ label: '상태', name: 'stock_status_name', index: 'stock_status_name', width: 60, sortable: false},
				{ label: '생성일', name: 'stock_request_date', index: 'stock_request_date', width: 80, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTimeOnlyDate(cellvalue);
					}},
				{ label: '입고예정일', name: 'stock_due_date', index: 'stock_due_date', width: 80, sortable: true},
				{ label: '입고일', name: 'stock_in_date', index: 'stock_in_date', width: 80, sortable: true},
				{ label: '예정수량', name: 'stock_due_amount', index: 'stock_due_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '입고수량', name: 'stock_amount', index: 'stock_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '메모', name: 'stock_msg', index: 'stock_msg', width: 150, align: 'left', sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz += (rowobject.stock_msg != null) ? rowobject.stock_msg : '' + ' ';
						if(rowobject.stock_file_idx != 0){
							//btnz += ' <a href="javascript:;" class="btn-stock-confirm-file-down" data-stock_file_idx="' + rowobject.stock_file_idx + '" data-stock_file_name="' + rowobject.stock_file_name + '" title="첨부파일"><i class="far fa-file"></i></a>';
						}
						return btnz;

					}},
			],
			rowNum: 0,
			pgbuttons : false,
			pgtext: null,
			sortname: 'stock_request_date',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResizeWidth("#grid_list");

				//첨부파일 다운로드
				$(".btn-stock-confirm-file-down").on("click", function(){
					var file_idx = $(this).data("stock_file_idx");
					var file_name = $(this).data("stock_file_name");

					Common.simpleUploadedFileDown(file_idx, file_name);
				});
			}
		});

		//입고미처리 목록 바인딩 jqGrid
		$("#grid_list2").jqGrid({
			url: './stock_confirm_detail_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				stock_idx: $("#stock_idx").val(),
				stock_ref_idx: $("#stock_ref_idx").val(),
				stock_is_proc: 'N',
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
				{ label: '재고IDX', name: 'stock_idx', index: 'A.stock_idx', width: 0, sortable: false, hidden: true},
				{ label: '상태', name: 'stock_status_name', index: 'stock_status_name', width: 60, sortable: false},
				{ label: '생성일', name: 'stock_request_date', index: 'stock_request_date', width: 80, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTimeOnlyDate(cellvalue);
					}},
				{ label: '입고예정일', name: 'stock_due_date', index: 'stock_due_date', width: 80, sortable: true},
				{ label: '예정수량', name: 'stock_due_amount', index: 'stock_due_amount', width: 60, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '메모', name: 'stock_msg', index: 'stock_msg', width: 150, align: 'left', sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						btnz += (rowobject.stock_msg != null) ? rowobject.stock_msg : '' + ' ';
						if(rowobject.stock_file_idx != 0){
							//btnz += ' <a href="javascript:;" class="btn-stock-confirm-file-down" data-stock_file_idx="' + rowobject.stock_file_idx + '" data-stock_file_name="' + rowobject.stock_file_name + '" title="첨부파일"><i class="far fa-file"></i></a>';
						}
						return btnz;

					}},
			],
			rowNum: 0,
			pgbuttons : false,
			pgtext: null,
			sortname: 'stock_request_date',
			sortorder: "asc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: 150,
			loadComplete: function(){

				//Grid 사이즈 reSize
				Common.jqGridResizeWidth("#grid_list2");

				//첨부파일 다운로드
				$(".btn-stock-confirm-file-down").on("click", function(){
					var file_idx = $(this).data("stock_file_idx");
					var file_name = $(this).data("stock_file_name");

					Common.simpleUploadedFileDown(file_idx, file_name);
				});
			}
		});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResizeWidth("#grid_list");
			Common.jqGridResizeWidth("#grid_list2");
		}).trigger("resize");

		//첨부파일 다운로드
		$(".btn-stock-proc-file-down").on("click", function(){
			var file_idx = $(this).data("stock_file_idx");
			var file_name = $(this).data("stock_file_name");

			Common.simpleUploadedFileDown(file_idx, file_name);
		});
	};

	/**
	 * 입고지연관리 페이지 초기화
	 * @constructor
	 */
	var StockDelayListInit = function(){

		//날짜 검색 초기화 및 프리셋
		Common.setDatePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', "8");

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

		//신규발주 버튼 바인딩
		$(".btn-stock-order-write-pop").on("click", function(){
			StockOrderWritePop('');
		});

		//Grid 초기화
		StockDelayListGridInit();

		//입고지연이력 팝업 바인딩
		$(".btn-log-for-delay").on("click", function(){
			StockDueDelayListPopup();
		});

		//다운로드 버튼 바인딩
		$(".btn-stock-delay-xls-down").on("click", function(){
			StockDelayListXlsDown();
		});

	};

	/**
	 * 입고지연관리 목록 바인딩 jqGrid
	 * @constructor
	 */
	var StockDelayListGridInit = function(){
		$("#grid_list").jqGrid({
			url: './stock_delay_list_grid.php',
			mtype: "GET",
			datatype: "json",
			postData:{
				param: $("#searchForm").serialize()
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
				{ label: '재고IDX', name: 'stock_idx', index: 'A.stock_idx', width: 0, sortable: false, hidden: true},
				{ label: '구분', name: 'stock_kind_han', index: 'stock_kind_han', width: 80, sortable: false},
				{ label: '코드', name: 'stock_order_idx', index: 'A.stock_order_idx', width: 80, sortable: false},
				{ label: '생성일', name: 'stock_request_date', index: 'stock_request_date', width: 100, sortable: true, formatter: function(cellvalue, options, rowobject){
						return Common.toDateTimeOnlyDate(cellvalue);
					}},
				{ label: '입고<br>예정일', name: 'stock_due_date', index: 'stock_due_date', width: 100, sortable: false},
				{ label: '작업자', name: 'member_id', index: 'member_id', width: 100, sortable: false},
				{ label: '공급처', name: 'supplier_name', index: 'supplier_name', width: 100, sortable: false},
				{ label: '상품옵션코드', name: 'product_option_idx', index: 'product_option_idx', width: 100, sortable: false},
				{ label: '상품명', name: 'product_name', index: 'product_name', width: 100, align: 'left', sortable: false},
				{ label: '옵션명', name: 'product_option_name', index: 'product_option_name', width: 100, align: 'left', sortable: false},
				{ label: '원가', name: 'stock_unit_price', index: 'stock_unit_price', width: 100, align: 'right', sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '구매자정보', name: 'order_info', index: 'order_info', width: 100, sortable: false},
				{ label: '예정수량', name: 'stock_due_amount', index: 'stock_due_amount', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						return Common.addCommas(cellvalue);
					}},
				{ label: '입고수량', name: 'stock_amount', index: 'stock_amount', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject){
						//입고 처리 된 재고만 입고 수량 표시
						if(rowobject.stock_is_proc == "Y") {
							return Common.addCommas(cellvalue);
						}else{
							return '-';
						}
					}},
				{ label: '상태', name: 'stock_status_name', index: 'stock_status_name', width: 100, sortable: false},
				{ label: '작업', name: 'result', index: 'result', width: 100, sortable: false, formatter: function(cellvalue, options, rowobject) {

						if (rowobject.stock_is_proc == "N") {
							if(rowobject.stock_order_is_ready == "Y"){
								return '미처리';
							}else{
								return '미처리<br>(추가입고)';
							}
						} else if (rowobject.stock_is_proc == "Y") {
							if(rowobject.stock_is_confirm == "Y") {
								return '처리완료<br>(입고확정)';
							}else{
								return '처리완료';
							}
						}
					}},
				{ label: '입고확인', name: 'btn_action', index: 'btn_action', width: 200, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						if (rowobject.stock_is_proc == "N") {
							btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-due-part" data-stock_idx="' + rowobject.stock_idx + '">입고처리</a>'
								+ ' <a href="javascript:;" class="xsmall_btn red_btn btn-stock-due-add" data-stock_idx="' + rowobject.stock_idx + '">추가입고</a>';

						} else if (rowobject.stock_is_proc == "Y") {
							btnz = '<a href="javascript:;" class="xsmall_btn red_btn btn-stock-due-add" data-stock_idx="' + rowobject.stock_idx + '">추가입고</a>';
						}
						return btnz;

					}},
				{ label: '이력관리', name: 'btn_delay', index: 'btn_delay', width: 80, sortable: false, formatter: function(cellvalue, options, rowobject){
						var btnz = '';
						if(rowobject.stock_is_proc == "N") {
							btnz = '<a href="javascript:;" class="xsmall_btn blue_btn btn-stock-due-delay" data-stock_idx="' + rowobject.stock_idx + '">지연</a>';
						}
						return btnz;

					}},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			sortname: 'stock_request_date',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){

				//전체입고 - 삭제
				/*
				$(".btn-stock-due-finish").on("click", function(){
					StockReceivingAll($(this).data("stock_idx"));
				});
				*/

				//입고처리
				$(".btn-stock-due-part").on("click", function(){
					var stock_idx = $(this).data("stock_idx");
					StockReceivingPartialPopup(stock_idx);
				});

				//추가입고
				$(".btn-stock-due-add").on("click", function(){
					StockOrderEmailSendPopOpen($(this));
				});

				//지연
				$(".btn-stock-due-delay").on("click", function(){
					var stock_idx = $(this).data("stock_idx");
					StockDueDelayWritePopup(stock_idx);
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
				StockDelayListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			StockDelayListSearch();
		});
	};

	/**
	 * 입고지연관리 목록/검색
	 * @constructor
	 */
	var StockDelayListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 * 입고지연관리 목록 엑셀 다운로드
	 * @constructor
	 */
	var StockDelayListXlsDown = function(){
		var param = $("#searchForm").serialize();
		location.href="stock_delay_xls_down.php?"+param;
	};

	return {
		StockDueListInit: StockDueListInit,
		StockDueListReload : StockDueListSearch,
		StockReceivingPartialInit: StockReceivingPartialInit,
		StockDueDelayWriteInit: StockDueDelayWriteInit,
		StockConfirmListInit: StockConfirmListInit,
		StockDueDelayListInit: StockDueDelayListInit,
		StockConfirmDetailInit: StockConfirmDetailInit,
		StockDelayListInit: StockDelayListInit,
		StockDelayListReload : StockDelayListSearch,
		StockDueAddInit: StockDueAddInit,
		StockConfirmListXlsDownComplete: StockConfirmListXlsDownComplete,
	}
})();