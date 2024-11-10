/*
 * 메인 업체신청 js
 */
var MainJoin = (function() {
	var root = this;

	var init = function() {
	};

	var JoinInit = function(){

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

		JoinFormInit();
	};

	//벤더사 등록/수정 폼 초기화
	var JoinFormInit = function () {

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

				this.action = "/info/vendor_proc.php";
				$("#btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	return {
		JoinInit: JoinInit
	}
})();