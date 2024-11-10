/*
 * 판매처관리 js
 */
var Seller = (function() {
	var root = this;

	var init = function() {
	};

	//판매처 등록 수정 팝업 함수
	var SellerWritePopup = function(seller_idx) {
		var url = '/info/seller_write_pop.php';
		url += (seller_idx != '') ? '?seller_idx=' + seller_idx : '';
		Common.newWinPopup(url, 'seller_write_pop', 700, 720, 'yes');
	};

	//판매처목록 초기화
	var SellerListInit = function(){
		//신규등록 팝업
		$(".btn-seller-write-pop").on("click", function(){
			SellerWritePopup('');
		});

		//판매처 그룹 관리 팝업
		$(".btn-manage-group-pop").on("click", function(){
			SellerGroupPopup();
		});

		//엑셀 다운로드
		$(".btn-seller-xls-down").on("click", function(){
			SellerListXlsDown();
		});

		//변경 이력 팝업
		$(".btn-change-log-viewer-pop").on("click", function(){
			Common.changeLogViewerPopup("seller");
		});

		//판매처 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './seller_list_grid.php',
			mtype: "GET",
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
				{ label:'수정', name: '수정', width: 150,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '<a href="javascript:;" class="xsmall_btn btn-seller-modify-pop" data-idx="'+rowobject.seller_idx+'">수정</a>';
					}, sortable: false
				},
				{ label: '판매처코드', name: 'seller_idx', index: 'seller_idx', width: 100},
				{ label: '판매처', name: 'market_name', index: 'market_name', width: 150, sortable: false},
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 150},
				{ label: '로그인아이디', name: 'market_login_id', index: 'market_login_id', width: 150},
				{ label: '그룹', name: 'seller_group_name', index: 'seller_group_name', width: 150, sortable: false},
				{ label: '보안코드', name: 'market_auth_code', index: 'market_auth_code', width: 150, sortable: false,formatter: function(cellvalue, options, rowobject){
					var rst = cellvalue == null ? '' : cellvalue;

					if(rowobject.market_code == "CAFE24"){
						var url = "https://dymallkr.cafe24api.com/api/v2/oauth/authorize?response_type=code&client_id="+rowobject.market_auth_code+"&state="+rowobject.seller_idx+"&redirect_uri="+_gl_url+"/dy_auto/_cafe24_request_token.php&scope=mall.read_order,mall.write_order";
						var html = '[<a href="'+url+'" class="link" target="_blank">토큰갱신</a>] ';
						rst = html + rst;
					}

					return rst;
				}},
				{ label: '보안코드2', name: 'market_auth_code2', index: 'market_auth_code2', width: 150, sortable: false},
				{ label: '자동발주사용', name: 'seller_auto_order', index: 'seller_auto_order', width: 80, sortable: false},
				{ label: 'API 사용여부', name: 'seller_use_api', index: 'seller_use_api', width: 100, sortable: false},
				{ label: '등록일', name: 'seller_regdate', index: 'seller_regdate', width: 150,formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '사용여부', name: 'seller_is_use', index: 'seller_is_use', width: 150, sortable: false},

			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'seller_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: false,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//수정 팝업
				$(".btn-seller-modify-pop").on("click", function(){
					SellerWritePopup($(this).data("idx"));
				});

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("seller_list", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "seller_list");
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
				SellerListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			SellerListSearch();
		});
	};

	//판매처 목록/검색
	var SellerListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	//판매처 엑셀 다운로드
	var SellerListXlsDown = function(){
		var param = $("#searchForm").serialize();
		location.href="seller_xls_down.php?"+param;
	};

	//판매처 등록/수정 초기화
	var SellerWriteInit = function(){

		$(".market_type").on("change", function(e){
			//console.log(e);
			getMarketList($(this).val());
		});
		getMarketList($(".market_type").val());
		bindWriteForm();

		//판매처 그룹 신규등록 및 관리 팝업
		$(".btn-seller_group_pop").on("click", function(){
			SellerGroupPopup();
		});
	};

	//판매처 리스트 Ajax
	var getMarketList = function(val){
		var p_url = "/info/seller_proc.php";
		var dataObj = new Object();
		dataObj.mode = "get_market_type_list";
		dataObj.market_type = val;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			console.log(response);
			if(response.result)
			{
				var market_code_selected = $("#market_code").data("selected");

				//사용자정의판매처일 경우 판매처 선택 불가 (고정 및 숨김)
				if(val == "CUSTOM_SELLER") {
					market_code_selected = "MARKETDEFINE01";
					$("#market_code").hide();
				}else{
					$("#market_code").show();
				}


				var $list = response.list;
				$("#market_code option").remove();
				$("#market_code").append('<option value="">판매처를 선택해주세요.</option>');
				$.each($list, function(i, v){
					if(market_code_selected == v.SEL_VALUE) {
						$("#market_code").append('<option value="' + v.SEL_VALUE + '" selected="selected">' + v.SEL_TEXT + '</option>');
					}else{
						$("#market_code").append('<option value="' + v.SEL_VALUE + '">' + v.SEL_TEXT + '</option>');
					}
				});
				$('#market_code').trigger("change");
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			//console.log(jqXHR, textStatus);
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			$(".market_type").eq(0).trigger("click");
		});
	};

	//판매처 등록/수정 폼 초기화
	var bindWriteForm = function () {
		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{

				if($("select[name='market_code']").val() == "")
				{
					alert("판매처를 선택하여 주세요.");
					return false;
				}

				if (!valForm.chkValue(objForm.seller_name, "판매처명을 정확히 입력해주세요.", 2, 40, null)) return returnType;
				// if (!valForm.chkValue(objForm.market_login_id, "로그인 아이디를 정확히 입력해주세요.", 1, 50, null)) return returnType;
				// if (!valForm.chkValue(objForm.market_login_pw, "로그인 비밀번호를 정확히 입력해주세요.", 1, 50, null)) return returnType;

				if(objForm.market_admin_url.value != "")
				{
					if(objForm.market_admin_url.value.indexOf('http://') != 0 && objForm.market_admin_url.value.indexOf('https://') != 0)
					{
						alert("관리자 URL 은 http:// 또는 https:// 로 시작하여야 합니다.");
						objForm.market_admin_url.focus();
						return false;
					}
				}
				if(objForm.market_mall_url.value != "")
				{
					if(objForm.market_mall_url.value.indexOf('http://') != 0 && objForm.market_mall_url.value.indexOf('https://') != 0)
					{
						alert("쇼핑몰 URL 은 http:// 또는 https:// 로 시작하여야 합니다.");
						objForm.market_mall_url.focus();
						return false;
					}
				}
				if(objForm.market_product_url.value != "")
				{
					if(objForm.market_product_url.value.indexOf('http://') != 0 && objForm.market_product_url.value.indexOf('https://') != 0)
					{
						alert("상품페이지 URL 은 http:// 또는 https:// 로 시작하여야 합니다.");
						objForm.market_product_url.focus();
						return false;
					}
				}

				this.action = "seller_proc.php";
				$("#btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	//그룹관리 팝업
	var SellerGroupPopup = function(){
		Common.newWinPopup("seller_group_pop.php", 'seller_group_pop', 700, 720, 'yes');
	};

	//입력/수정 이후 본창 리스트를 reload 할때 실행되는 함수
	//window.name 이 'seller_list' 일 때 만 실행
	//다른 페이지에서 입력/수정 팝업을 띄워 입력/수정 된 경우 실행하지 않도록 설정
	var SellerListReload = function(){
		if(window.name == 'seller_list'){
			SellerListSearch();
		}
	};

	var xlsValidRow = 0;                //업로드된 엑셀 Row 중 정상인 Row 수
	var xlsUploadedFileName = "";       //업로드 된 엑셀 파일명
	var xlsWritePageMode = "";          //일괄등록 / 일괄수정 Flag
	var xlsWriteReturnStyle = "";       //리스트 반환 또는 적용
	//판매처 일괄등록 / 일괄수정 페이지 초기화
	var SellerXlsWriteInit = function(){

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
			if(xlsValidRow == 0)
			{
				alert("적용할 데이터가 없습니다.");
				return;
			}else{
				var msg = xlsValidRow + "건의 데이터를 적용 하시겠습니까?";
				if(confirm(msg)) {
					SellerXlsInsert();
				}
			}
		});

		SellerXlsWriteGridInit();
	};

	//판매처 일괄 등록/수정 페이지 jqGrid 초기화
	var SellerXlsWriteGridInit = function(){
		var validErr = [];

		var colModel = [];
		if(xlsWritePageMode == "add")
		{
			colModel = [
				{ label: '마켓 코드', name: 'A', index: 'seller_idx', width: 100, sortable: false},
				{ label: '판매처', name: 'market_name', index: 'market_name', width: 150, sortable: false},
				{ label: '판매처명', name: 'B', index: 'seller_name', width: 150, sortable: false},
				{ label: '로그인아이디', name: 'C', index: 'market_login_id', width: 150, sortable: false},
				{ label: '로그인비밀번호', name: 'D', index: 'market_login_pw', width: 150, sortable: false},
				{ label: '보안코드', name: 'E', index: 'market_auth_code', width: 150, sortable: false},
				{ label: '보안코드2', name: 'F', index: 'market_auth_code2', width: 150, sortable: false},
				{ label: '관리자 URL', name: 'G', index: 'market_auth_code2', width: 150, sortable: false},
				{ label: '쇼핑몰 URL', name: 'H', index: 'market_auth_code2', width: 150, sortable: false},
				{ label: '상품페이지 URL', name: 'I', index: 'market_auth_code2', width: 150, sortable: false},
				{ label: '비고', name: 'valid', index: 'valid', width: 150, sortable: false
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
		}else{
			colModel = [
				{ label: '판매처 코드', name: 'A', index: 'seller_idx', width: 100, sortable: false},
				{ label: '마켓 코드', name: 'B', index: 'seller_idx', width: 100, sortable: false},
				{ label: '판매처', name: 'market_name', index: 'market_name', width: 150, sortable: false},
				{ label: '판매처명', name: 'C', index: 'seller_name', width: 150, sortable: false},
				{ label: '로그인아이디', name: 'D', index: 'market_login_id', width: 150, sortable: false},
				{ label: '로그인비밀번호', name: 'E', index: 'market_login_pw', width: 150, sortable: false},
				{ label: '보안코드', name: 'F', index: 'market_auth_code', width: 150, sortable: false},
				{ label: '보안코드2', name: 'G', index: 'market_auth_code2', width: 150, sortable: false},
				{ label: '관리자 URL', name: 'H', index: 'market_auth_code2', width: 150, sortable: false},
				{ label: '쇼핑몰 URL', name: 'U', index: 'market_auth_code2', width: 150, sortable: false},
				{ label: '상품페이지 URL', name: 'J', index: 'market_auth_code2', width: 150, sortable: false},
				{ label: '비고', name: 'valid', index: 'valid', width: 150, sortable: false
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
			url: './seller_proc_xls.php',
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
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'seller_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//console.log(validErr);
				$.each(validErr, function(k, v){
					$("#grid_list #"+v).addClass("upload_err");
					validErr = [];
				});
			},
		});
		//$("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false});

		//브라우저 리사이즈 시 jqgrid 리사이징
		$(window).on("resize", function(){
			Common.jqGridResize("#grid_list");
		}).trigger("resize");
	};

	//업로드 된 엑셀 파일 로딩
	var SellerXlsRead = function(xls_file_path_name){
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

	//업로드 된 엑셀 파일 적용
	var SellerXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "seller_proc_xls.php";
		var dataObj = new Object();
		dataObj.mode = xlsWritePageMode;
		dataObj.act = "save";
		dataObj.xls_filename = xlsUploadedFileName;
		dataObj.xls_validrow = xlsValidRow;

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

	//공통으로 사용되는 판매처 검색 팝업창 초기화
	var SellerSearchPopInit = function(){
		//판매처 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './seller_list_grid.php',
			mtype: "GET",
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
				{ label: '판매처코드', name: 'seller_idx', index: 'A.seller_idx', width: 100},
				{ label: '판매처명', name: 'seller_name', index: 'seller_name', width: 250, align: 'left'},
				{ label: '메모', name: 'seller_etc', index: 'seller_etc', width: 180, sortable: false},
				{ label: '선택', name: 'btn_mod', index: 'btn_mod', width: 100, sortable: false
					,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '<a href="javascript:;" class="xsmall_btn btn-seller-select" data-manage-group-idx="'+rowobject.manage_group_idx+'" data-idx="'+rowobject.seller_idx+'">선택</a>';
					}
				},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			pager: '#grid_pager',
			pgbuttons : false,
			pgtext: null,
			sortname: 'seller_regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//선택 버튼 클릭 이벤트
				$(".btn-seller-select").on("click", function(){
					console.log("선택!!");
					try{
						if(opener.window.name == "product_write"){
							opener.Product.setGroupMemberSelect("SELLER_ALL_GROUP", $(this).data("manage-group-idx"), $(this).data("idx"));
						}
						self.close();
					}catch (e) {
						console.log(e);
					}
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
				SellerSearchPopSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			SellerSearchPopSearch();
		});
	};

	//공급처 목록/검색
	var SellerSearchPopSearch = function(){

		var isExistsSearchKeyword = false;
		$("#searchForm").find("select, input[type='text']").each(function(i, o){
			if($(o).val() != "" && $(o).val() != "0")
			{
				isExistsSearchKeyword = true;
			}
		});

		if(!isExistsSearchKeyword) {
			alert("그룹을 선택하시거나 검색어를 입력해주세요.");
			return;
		}

		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				is_seller_search_pop: "Y",
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	return {
		SellerListInit : SellerListInit,
		SellerWriteInit : SellerWriteInit,
		SellerListReload : SellerListReload,
		SellerXlsWriteInit : SellerXlsWriteInit,
		SellerXlsRead : SellerXlsRead,
		SellerSearchPopInit: SellerSearchPopInit,
	}
})();