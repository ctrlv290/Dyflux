/*
 * 벤더사관리 js
 */
var Vendor = (function() {
	var root = this;

	var init = function() {
	};

	//벤더사 등록 수정 팝업 함수
	var VendorWritePopup = function(vendor_idx) {
		var url = '/info/vendor_write_pop.php';
		url += (vendor_idx != '') ? '?vendor_idx=' + vendor_idx : '';
		Common.newWinPopup(url, 'vendor_write_pop', 700, 720, 'yes');
	};

	//벤더사목록 초기화
	var VendorListInit = function(){
		//신규등록 팝업
		$(".btn-vendor-write-pop").on("click", function(){
			VendorWritePopup('');
		});

		//벤더사 그룹 관리 팝업
		$(".btn-manage-group-pop").on("click", function(){
			VendorGroupPopup();
		});

		//엑셀 다운로드
		$(".btn-vendor-xls-down").on("click", function(){
			VendorListXlsDown();
		});

		//변경 이력 팝업
		$(".btn-change-log-viewer-pop").on("click", function(){
			Common.changeLogViewerPopup("vendor");
		});

		//벤더사 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './vendor_list_grid.php',
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
				{ label:'수정', name: '수정', width: 90,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '<a href="javascript:;" class="xsmall_btn btn-vendor-modify-pop" data-idx="'+rowobject.idx+'">수정</a>';
					}, sortable: false
				},
				{ label: '벤더사코드', name: 'idx', index: 'idx', width: 100},
				{ label: '벤더사명', name: 'vendor_name', index: 'vendor_name', width: 120},
				{ label: '등급', name: 'vendor_grade_name', index: 'vendor_grade_name', width: 60},
				{ label: '로그인아이디', name: 'member_id', index: 'member_id', width: 120},
				{ label: '연락처', name: 'vendor_officer1_tel', index: 'vendor_officer1_tel', width: 120, sortable: false},
				{ label: '주소', name: 'vendor_address', index: 'vendor_address', width: 150,formatter: function(cellvalue, options, rowobject){
						return '[' + rowobject.vendor_zipcode + '] ' + rowobject.vendor_addr1 + ' ' + rowobject.vendor_addr2;
					}, sortable: false},
				{ label: '이메일', name: 'vendor_email_default', index: 'vendor_email_default', width: 150, sortable: false},
				{ label: '그룹', name: 'manage_group_name', index: 'manage_group_name', width: 100, sortable: false},
				/* { label: 'MD', name: 'vendor_md', index: 'vendor_md', width: 150, sortable: false},*/
				{ label: '담당자', name: 'vendor_officer1_name', index: 'vendor_officer1_name', width: 80, sortable: false},
				{ label: '휴대폰', name: 'vendor_officer1_mobile', index: 'vendor_officer1_mobile', width: 120, sortable: false},
				{ label: '충전금 잔액', name: 'cash_balance', index: 'cash_balance', width: 150, sortable: false},
				{ label: '계좌번호', name: 'vendor_bank_account_number', index: 'vendor_bank_account_number', width: 150, sortable: false},
				{ label: '은행', name: 'vendor_bank_name', index: 'vendor_bank_name', width: 150, sortable: false},
				{ label: '예금주', name: 'vendor_bank_holder_name', index: 'vendor_bank_holder_name', width: 150, sortable: false},
				{ label: '충전금 사용여부', name: 'vendor_use_charge', index: 'vendor_use_charge', width: 150, sortable: false},
				{ label: '등록일', name: 'regdate', index: 'regdate', width: 150,formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '사용여부', name: 'is_use', index: 'is_use', width: 150, sortable: false},
				{ label: '승인', name: 'vendor_status', index: 'vendor_status', width: 150,formatter: function(cellvalue, options, rowobject){
						var returnValue = '';
						if(cellvalue == 'VENDOR_PENDDING')
						{
							returnValue = '<a href="javascript:;" class="normal_link btn-vendor-status-change" data-status="'+cellvalue+'" data-idx="'+rowobject.idx+'"><span class="lb_gray">미승인</span></a>';
						}else if(cellvalue == 'VENDOR_APPLY') {
							returnValue = '<a href="javascript:;" class="normal_link btn-vendor-status-change" data-status="'+cellvalue+'" data-idx="'+rowobject.idx+'"><span class="lb_blue">승인</span></a>';
						}else if(cellvalue == 'VENDOR_REJECT') {
							returnValue = '<a href="javascript:;" class="normal_link btn-vendor-status-change" data-status="'+cellvalue+'" data-idx="'+rowobject.idx+'"><span class="lb_orange">반려</span></a>';

						}
						return returnValue;

					}, sortable: false},

			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: false,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//수정 팝업
				$(".btn-vendor-modify-pop").on("click", function(){
					VendorWritePopup($(this).data("idx"));
				});

				//승인처라 버튼
				$(".btn-vendor-status-change").on("click", function(){
					VendorStatusChange($(this));
				});
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
				VendorListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			VendorListSearch();
		});
	};

	//벤더사 목록/검색
	var VendorListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	//벤더사 엑셀 다운로드
	var VendorListXlsDown = function(){
		var param = $("#searchForm").serialize();
		location.href="vendor_xls_down.php?"+param;
	};

	//벤더사 등록/수정 초기화
	var VendorWriteInit = function(){

		//수정일 경우 비밀번호 변경 버튼 활성화
		if($("form[name='dyForm']").hasClass("mod"))
		{
			$(".mod_pass_btn").removeClass("dis_none");
			$(".mod_pass").addClass("dis_none");
		}

		//비밀번호 변경 버튼 바인딩
		$(".btn-password-change").on("click", function(){

			$("form[name='dyForm']").addClass("mod_pass");

			$(".mod_pass_btn").addClass("dis_none");
			$(".mod_pass").removeClass("dis_none");
		});

		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn-vendor-license-file', {
			_target_table : 'DY_MEMBER_VENDOR',
			_target_table_column : 'vendor_license_file',
			_target_filename : '.span_vendor_license_file',
			_target_input_hidden : '#vendor_license_file',
			_upload_no: 1

		});

		var file2 = new FileUpload2('btn-vendor-bank-book-copy-file', {
			_target_table : 'DY_MEMBER_VENDOR',
			_target_table_column : 'vendor_bank_book_copy_file',
			_target_filename : '.span_vendor_bank_book_copy_file',
			_target_input_hidden : '#vendor_bank_book_copy_file',
			_upload_no: 1
		});
		$(".btn-vendor-license-file").on("click", function(){
			//FileUpload.callFileDialog('DY_MEMBER_VENDOR', 'vendor_license_file', '.span_vendor_license_file', '#vendor_license_file', 1);
		});

		//업로드 버튼 바인딩..
		$(".btn-vendor-bank-book-copy-file").on("click", function(){
			//FileUpload.callFileDialog('DY_MEMBER_VENDOR', 'vendor_bank_book_copy_file', '.span_vendor_bank_book_copy_file', '#vendor_bank_book_copy_file', 1);
		});

		//이메일 다중 입력 적용
		Common.setMultiEmailInput('vendor_email_default', 'vendor_email_default_dummy');
		Common.setMultiEmailInput('vendor_email_account', 'vendor_email_account_dummy');
		Common.setMultiEmailInput('vendor_email_order', 'vendor_email_order_dummy');

		//폼 바인딩
		bindWriteForm();

		//벤더사 그룹 신규등록 및 관리 팝업
		$(".btn-vendor_group_pop").on("click", function(){
			VendorGroupPopup();
		});
	};

	//벤더사 등록/수정 폼 초기화
	var bindWriteForm = function () {

		//로그인 아이디 입력 체크
		$("#login_id").on("keyup", function(){
			if($(this).val().trim().length > 3 && $(this).val().trim().length < 13) {
				var p_url = "/info/user_proc.php";
				var dataObj = new Object();
				dataObj.mode = "id_check";
				dataObj.login_id = $(this).val();
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: dataObj
				}).done(function (response) {
					if(response.result)
					{
						$(".login_id_check_txt").removeClass("col_red").addClass("col_blue").html("사용가능한 아이디입니다.").show();
						$("#dupcheck").val("Y");
					}else{
						$(".login_id_check_txt").removeClass("col_blue").addClass("col_red").html("사용이 불가능한 아이디입니다.").show();
						$("#dupcheck").val("N");
					}
				});
			}else{
				$(".login_id_check_txt").removeClass("col_red col_blue").html("").hide();
				$("#dupcheck").val("N");
			}
		});

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

			if($(this).hasClass("add")) {
				if ($("#dupcheck").val() != "Y") {
					alert('사용가능한 아이디가 아닙니다.');
					objForm.login_id.focus();
					return false;
				}
			}

			if($(this).hasClass("add") || $(this).hasClass("mod_pass")) {
				if ($.trim(objForm.login_pw.value) != $.trim(objForm.login_pw_re.value)) {
					alert('비밀번호와 비밀번호 확인이 일치하지 않습니다.');
					return false;
				}
			}

			try{

				if(typeof objForm.vendor_name !== 'undefined'){
					if (!valForm.chkValue(objForm.vendor_name, "벤더사명을 정확히 입력해주세요.", 2, 40, null)) return returnType;
				}

				if($(this).hasClass("add"))
				{
					if (!valForm.chkValue(objForm.login_id, "로그인 아이디를 정확히 입력해주세요.", 4, 12, RegexpPattern.userID)) return returnType;
				}
				if($(this).hasClass("add") || $(this).hasClass("mod_pass")) {
					if (!valForm.chkValue(objForm.login_pw, "로그인 비밀번호을 정확히 입력해주세요.", 4, 12, null)) return returnType;
					if (!valForm.chkValue(objForm.login_pw_re, "비밀번호 확인을 정확히 입력해주세요.", 4, 12, null)) return returnType;
				}

				if($("input[name='vendor_grade']").length > 0) {
					if (typeof $("input[name='vendor_grade']:checked").val() == "undefined") {
						alert("등급을 선택해주세요.");
						return false;
					}
				}

				if (!valForm.chkValue(objForm.vendor_ceo_name, "대표이사를 정확히 입력해주세요.", 2, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_license_no1, "사업자등록번호를 정확히 입력해주세요.", 3, 3, RegexpPattern.number)) return returnType;
				if (!valForm.chkValue(objForm.vendor_license_no2, "사업자등록번호를 정확히 입력해주세요.", 2, 2, RegexpPattern.number)) return returnType;
				if (!valForm.chkValue(objForm.vendor_license_no3, "사업자등록번호를 정확히 입력해주세요.", 5, 5, RegexpPattern.number)) return returnType;

				if (!valForm.chkValue(objForm.vendor_zipcode, "우편번호 버튼을 이용해서 주소를 정확히 입력해주세요.", 5, 6, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_addr1, "우편번호 버튼을 이용해서 주소를 정확히 입력해주세요.", 1, 100, null)) return returnType;

				if (!valForm.chkValue(objForm.vendor_license_file, "사업자등록증 파일을 업로드해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_bank_account_number, "계좌번호를 정확히 입력해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_bank_name, "은행명을 정확히 입력해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_bank_holder_name, "예금주명을 정확히 입력해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_bank_book_copy_file, "통장사본 파일을 업로드해주세요.", 1, 100, null)) return returnType;

				if (!valForm.chkValue(objForm.vendor_email_default, "대표 이메일을 정확히 입력해주세요.", 1, 300, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_email_account, "회계용 이메일을 정확히 입력해주세요.", 1, 300, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_email_order, "발주용 이메일을 정확히 입력해주세요.", 1, 300, null)) return returnType;

				if (!valForm.chkValue(objForm.vendor_officer1_name, "담당자를 정확히 입력해주세요.", 1, 30, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_officer1_tel2, "담당자 연락처를 정확히 입력해주세요.", 1, 4, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_officer1_tel3, "담당자 연락처를 정확히 입력해주세요.", 1, 4, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_officer1_mobile2, "담당자 휴대폰번호를 정확히 입력해주세요.", 1, 4, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_officer1_mobile3, "담당자 휴대폰번호를 정확히 입력해주세요.", 1, 4, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_officer1_email1, "담당자 이메일 주소를 정확히 입력해주세요.", 1, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.vendor_officer1_email2, "담당자 이메일 주소를 정확히 입력해주세요.", 1, 50, null)) return returnType;

				this.action = "vendor_proc.php";
				$("#btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	//그룹관리 팝업
	var VendorGroupPopup = function(){
		Common.newWinPopup("vendor_group_pop.php", 'seller_group_pop', 700, 720, 'yes');
	};

	//입력/수정 이후 본창 리스트를 reload 할때 실행되는 함수
	//window.name 이 'vendor_list' 일 때 만 실행
	//다른 페이지에서 입력/수정 팝업을 띄워 입력/수정 된 경우 실행하지 않도록 설정
	var VendorListReload = function(){
		if(window.name == 'vendor_list'){
			VendorListSearch();
		}
	};

	//벤더사 승인 상태 클릭 시 액션
	var VendorStatusChange = function($obj) {
		var idx = $obj.data("idx");

		Common.newWinPopup("vendor_status_change_pop.php?idx="+idx, 'vendor_status_pop', 600, 400, 'yes');
	};

	//상태 변경 진행 중 여부
	var isStatusChangeIng = false;

	//벤더사 상태 변경 팝업 초기화
	var VendorStatusPopInit = function() {
		//저장 버튼 승인 반려
		$(".btn-save").on("click", function (e) {
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			if(!isStatusChangeIng) {
				var stat = $(this).data("status");
				$("input[name='vendor_status']").val(stat);
				var msg = "";
				if(stat == "VENDOR_APPLY") {
					msg = "승인처리 하시겠습니까?";

				}else if(stat == "VENDOR_REJECT"){
					msg = "반려처리 하시겠습니까?";
				}else{
					alert('Err');
					return;
				}

				if(confirm(msg)) {
					$("form[name='dyForm']").submit();
				}
			}
		});

		VendorStatusFormBind();
	};

	//벤더사 상태 변경 팝업 폼 바인딩
	var VendorStatusFormBind = function() {

		//폼 Submit 이벤트
		$("form[name='dyForm']").submit(function(){
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.vendor_status_msg, "처리 메모를 입력해주세요. (200자 이내)", 1, 200, null)) return returnType;

				isStatusChangeIng = true;
				this.action = "vendor_proc.php";
				$(".btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	var xlsValidRow = 0;                //업로드된 엑셀 Row 중 정상인 Row 수
	var xlsUploadedFileName = "";       //업로드 된 엑셀 파일명
	var xlsWritePageMode = "";          //일괄등록 / 일괄수정 Flag
	var xlsWriteReturnStyle = "";       //리스트 반환 또는 적용
	//벤더사 일괄등록 / 일괄수정 페이지 초기화
	var VendorXlsWriteInit = function(){

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
					VendorXlsInsert();
				}
			}
		});

		VendorXlsWriteGridInit();
	};

	//벤더사 일괄 등록/수정 페이지 jqGrid 초기화
	var VendorXlsWriteGridInit = function(){
		var validErr = [];

		var colModel = [];
		if(xlsWritePageMode == "add")
		{
			colModel = [
				{ label: '벤더사명', name: 'A', index: 'vendor_name', width: 150, sortable: false},
				{ label: '등급', name: 'B', index: 'vendor_grade', width: 80, sortable: false},
				{ label: '로그인아이디', name: 'C', index: 'member_id', width: 150, sortable: false},
				{ label: '로그인<br>비밀번호', name: 'D', index: 'member_pw', width: 150, sortable: false},
				{ label: '대표이사', name: 'E', index: 'vendor_ceo', width: 150, sortable: false},
				{ label: '사업자<br>등록번호', name: 'F', index: 'vendor_license_number', width: 150, sortable: false},
				{ label: '담당자', name: 'G', index: 'vendor_officer1_name', width: 150, sortable: false},
				{ label: '연락처', name: 'H', index: 'vendor_officer1_tel', width: 150, sortable: false},
				{ label: '휴대폰번호', name: 'I', index: 'vendor_officer1_mobile', width: 150, sortable: false},
				{ label: '이메일', name: 'J', index: 'vendor_officer1_email', width: 150, sortable: false},
				{ label: '주소<br>우편번호', name: 'K', index: 'vendor_zipcode', width: 150, sortable: false},
				{ label: '주소<br>기본주소', name: 'L', index: 'vendor_addr1', width: 150, sortable: false},
				{ label: '주소<br>상세주소', name: 'M', index: 'vendor_addr2', width: 150, sortable: false},
				{ label: 'MD', name: 'N', index: 'vendor_md', width: 150, sortable: false},
				{ label: '계좌번호', name: 'O', index: 'vendor_bank_account_number', width: 150, sortable: false},
				{ label: '은행', name: 'P', index: 'vendor_bank_name', width: 150, sortable: false},
				{ label: '예금주', name: 'Q', index: 'vendor_bank_holder_name', width: 150, sortable: false},
				{ label: '비고', name: 'R', index: 'vendor_etc', width: 150, sortable: false},
				{ label: '충전금 사용여부', name: 'S', index: 'is_use', width: 150, sortable: false},
				{ label: '사용여부', name: 'T', index: 'vendor_use_charge', width: 150, sortable: false},
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
				{ label: '벤더사코드', name: 'A', index: 'idx', width: 150, sortable: false},
				{ label: '벤더사명', name: 'B', index: 'vendor_name', width: 150, sortable: false},
				{ label: '등급', name: 'C', index: 'vendor_grade', width: 80, sortable: false},
				{ label: '로그인<br>비밀번호', name: 'D', index: 'member_pw', width: 150, sortable: false},
				{ label: '대표이사', name: 'E', index: 'vendor_ceo', width: 150, sortable: false},
				{ label: '사업자<br>등록번호', name: 'F', index: 'vendor_license_number', width: 150, sortable: false},
				{ label: '담당자', name: 'G', index: 'vendor_officer1_name', width: 150, sortable: false},
				{ label: '연락처', name: 'H', index: 'vendor_officer1_tel', width: 150, sortable: false},
				{ label: '휴대폰번호', name: 'I', index: 'vendor_officer1_mobile', width: 150, sortable: false},
				{ label: '이메일', name: 'J', index: 'vendor_officer1_email', width: 150, sortable: false},
				{ label: '주소<br>우편번호', name: 'K', index: 'vendor_zipcode', width: 150, sortable: false},
				{ label: '주소<br>기본주소', name: 'L', index: 'vendor_addr1', width: 150, sortable: false},
				{ label: '주소<br>상세주소', name: 'M', index: 'vendor_addr2', width: 150, sortable: false},
				{ label: 'MD', name: 'N', index: 'vendor_md', width: 150, sortable: false},
				{ label: '계좌번호', name: 'O', index: 'vendor_bank_account_number', width: 150, sortable: false},
				{ label: '은행', name: 'P', index: 'vendor_bank_name', width: 150, sortable: false},
				{ label: '예금주', name: 'Q', index: 'vendor_bank_holder_name', width: 150, sortable: false},
				{ label: '비고', name: 'R', index: 'vendor_etc', width: 150, sortable: false},
				{ label: '충전금 사용여부', name: 'S', index: 'vendor_use_charge', width: 150, sortable: false},
				{ label: '사용여부', name: 'T', index: 'is_use', width: 150, sortable: false},
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
			url: './vendor_proc_xls.php',
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
			sortname: 'regdate',
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
	var VendorXlsRead = function(xls_file_path_name){
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
	var VendorXlsInsert = function(){
		//xlsUploadedFileName
		xls_hidden_frame.location.replace("about:_blank");

		var p_url = "vendor_proc_xls.php";
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

	return {
		VendorListInit : VendorListInit,
		VendorWriteInit : VendorWriteInit,
		VendorListReload : VendorListReload,
		VendorStatusPopInit : VendorStatusPopInit,
		VendorXlsWriteInit : VendorXlsWriteInit,
		VendorXlsRead : VendorXlsRead,
	}
})();