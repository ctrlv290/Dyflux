/*
 * 사용자관리 js
 */
var User = (function() {
	var root = this;

	var init = function() {
	};

	//사용자 목록 초기화 jqGrid Loading
	var UserListInit = function(){
		//엑셀 다운로드
		$(".btn-user-xls-down").on("click", function(){
			UserListXlsDown();
		});

		//변경 이력 팝업
		$(".btn-change-log-viewer-pop").on("click", function(){
			Common.changeLogViewerPopup("user");
		});

		$("#grid_list").jqGrid({
			url: './user_list_grid.php',
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
				{ label: '사용자 코드', name: 'idx', index: 'A.idx', width: 100},
				{ label: '아이디', name: 'member_id', index: 'A.member_id', width: 100},
				{ label: '이름', name: 'name', index: 'name', width: 150},
				{ label: '등급', name: 'member_type_han', index: 'member_type_han', width: 150, sortable: false},
				{ label: '메모', name: 'etc', index: 'etc', width: 150, sortable: false,formatter: function(cellvalue, options, rowobject){
						return (cellvalue != null) ? cellvalue.replace(/\r\n/g, ' ') : '';
					}},
				{ label: '등록일', name: 'regdate', index: 'A.regdate', width: 150, formatter: function(cellvalue, options, rowobject){ return Common.toDateTime(cellvalue); }},
				{ label: '최근로그인시간', name: 'lastlogin_date', index: 'lastlogin_date', width: 150},
				{ label: '사용여부', name: 'is_use', index: 'is_use', width: 150, sortable: false},
				{ label:'수정', name: '수정', width: 150,formatter: function(cellvalue, options, rowobject){
						//console.log(rowobject);
						return '<a href="user_write.php?idx='+rowobject.idx+'" class="xsmall_btn">수정</a>';
					}, sortable: false
				},
			],
			rowNum:1000,
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: 'A.regdate',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight
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
				UseListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			UseListSearch();
		});
	};

	//사용자 목록/검색
	var UseListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	//사용자 엑셀 다운로드
	var UserListXlsDown = function(){
		var param = $("#searchForm").serialize();
		location.href="user_xls_down.php?"+param;
	};

	//사용자 입력 폼 초기화
	var UserWriteInit = function(){

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

		bindWriteForm();

		//이메일 셀렉트박스 선택 시..
		$("#email3").on("change", function(){
			if($(this).val() == "") {
				$("#email2").val('').focus();
			}else{
				$("#email2").val($(this).val());
			}
		});
	};

	//사용자 등록/수정 폼 초기화
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

		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		$("form[name='dyForm']").submit(function(){
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			if($(this).hasClass("add")) {
				if ($("#dupcheck").val() != "Y") {
					alert('사용가능한 아이디가 아닙니다.');
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
				if($(this).hasClass("add"))
				{
					if (!valForm.chkValue(objForm.login_id, "로그인 아이디를 정확히 입력해주세요.", 4, 12, RegexpPattern.userID)) return returnType;
				}
				if($(this).hasClass("add") || $(this).hasClass("mod_pass")) {
					if (!valForm.chkValue(objForm.login_pw, "로그인 비밀번호을 정확히 입력해주세요.", 4, 12, null)) return returnType;
					if (!valForm.chkValue(objForm.login_pw_re, "비밀번호 확인을 정확히 입력해주세요.", 4, 12, null)) return returnType;
				}
				if (!valForm.chkValue(objForm.name, "이름을 정확히 입력해주세요.", 2, 20, null)) return returnType;
				if (!valForm.chkValue(objForm.mobile2, "휴대폰번호를 정확히 입력해주세요.", 3, 4, RegexpPattern.number)) return returnType;
				if (!valForm.chkValue(objForm.mobile3, "휴대폰번호를 정확히 입력해주세요.", 4, 4, RegexpPattern.number)) return returnType;
				if (!valForm.chkValue(objForm.email1, "이메일을 정확히 입력해주세요.", 2, 50, null)) return returnType;
				if (!valForm.chkValue(objForm.email2, "이메일을 정확히 입력해주세요.", 4, 50, null)) return returnType;

				this.action = "user_proc.php";
				$("#btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	var passwordChecker = function (pw) {
		// 4~12 자 + 숫자 + 문자 체크
		var reg_pwd = /^.*(?=.{4,12})(?=.*[0-9])(?=.*[a-zA-Z]).*$/;
		if(!reg_pwd.test(pw)){
			return false;
		}
		return true;
	};

	return {
		UserListInit : UserListInit,
		UserWriteInit : UserWriteInit,
	}
})();