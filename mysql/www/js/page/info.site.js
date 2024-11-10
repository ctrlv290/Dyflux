/*
 * 사이트 정보 관리 js
 */
var SiteInfo = (function() {
	var root = this;

	var init = function() {
	};

	var SiteInfoInit = function(){

		//변경 이력 팝업
		$(".btn-change-log-viewer-pop").on("click", function(){
			Common.changeLogViewerPopup("site_info");
		});

		//이메일 다중 입력 적용
		Common.setMultiEmailInput('email_default', 'email_default_dummy');
		Common.setMultiEmailInput('email_account', 'email_account_dummy');
		Common.setMultiEmailInput('email_order', 'email_order_dummy');

		//폼 바인딩
		bindWriteForm();
	};

	var bindWriteForm = function(){
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
				if (!valForm.chkValue(objForm.site_name, "상호명을 정확히 입력해주세요.", 2, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.ceo_name, "대표이사를 정확히 입력해주세요.", 2, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.license_no1, "사업자등록번호를 정확히 입력해주세요.", 3, 3, RegexpPattern.number)) return returnType;
				if (!valForm.chkValue(objForm.license_no2, "사업자등록번호를 정확히 입력해주세요.", 2, 2, RegexpPattern.number)) return returnType;
				if (!valForm.chkValue(objForm.license_no3, "사업자등록번호를 정확히 입력해주세요.", 5, 5, RegexpPattern.number)) return returnType;

				if (!valForm.chkValue(objForm.zipcode, "우편번호 버튼을 이용해서 주소를 정확히 입력해주세요.", 5, 6, null)) return returnType;
				if (!valForm.chkValue(objForm.addr1, "우편번호 버튼을 이용해서 주소를 정확히 입력해주세요.", 1, 100, null)) return returnType;

				if (!valForm.chkValue(objForm.email_default, "대표 이메일을 정확히 입력해주세요.", 1, 300, null)) return returnType;
				if (!valForm.chkValue(objForm.email_account, "회계용 이메일을 정확히 입력해주세요.", 1, 300, null)) return returnType;
				if (!valForm.chkValue(objForm.email_order, "발주용 이메일을 정확히 입력해주세요.", 1, 300, null)) return returnType;

				if (!valForm.chkValue(objForm.officer1_name, "담당자를 정확히 입력해주세요.", 1, 30, null)) return returnType;
				if (!valForm.chkValue(objForm.officer1_tel2, "담당자 연락처를 정확히 입력해주세요.", 1, 4, null)) return returnType;
				if (!valForm.chkValue(objForm.officer1_tel3, "담당자 연락처를 정확히 입력해주세요.", 1, 4, null)) return returnType;
				if (!valForm.chkValue(objForm.officer1_mobile2, "담당자 휴대폰번호를 정확히 입력해주세요.", 1, 4, null)) return returnType;
				if (!valForm.chkValue(objForm.officer1_mobile3, "담당자 휴대폰번호를 정확히 입력해주세요.", 1, 4, null)) return returnType;
				if (!valForm.chkValue(objForm.officer1_email1, "담당자 이메일 주소를 정확히 입력해주세요.", 1, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.officer1_email2, "담당자 이메일 주소를 정확히 입력해주세요.", 1, 50, null)) return returnType;

				this.action = "site_info_proc.php";
				$("#btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});

	};

	var PersonalDataInit = function(){
		$("#btn-save").on("click", function(){

			if(!confirm('저장하시겠습니까?')) return;

			$("#mainForm").submit();

		});

		$("#btn-sell-save").on("click", function(){
			if(!confirm('저장하시겠습니까?')) return;

			$("#sellForm").submit();
		});
	};

	return {
		SiteInfoInit : SiteInfoInit,
		PersonalDataInit: PersonalDataInit
	}
})();