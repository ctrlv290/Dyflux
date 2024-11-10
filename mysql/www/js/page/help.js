/*
 * 고객센터 js
 */
var Help = (function() {
	var root = this;

	var init = function () {
	};

	/**
	 * 공지사항 리스트 페이지 초기화
	 * @constructor
	 */
	var NoticeListInit = function(){
		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});

		//검색버튼
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});
	};

	/**
	 * 공지사항 쓰기 페이지 초기화
	 * @constructor
	 */
	var NoticeWriteInit = function(){
		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn-bbs_file_idx_1', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_1',
			_target_filename : '.span_bbs_file_idx_1',
			_target_input_hidden : '#bbs_file_idx_1',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});

		//대상 변경 이벤트
		$("select[name='bbs_target']").on("change", function(){

			var v = $(this).val();
			console.log(v);

			if(v == "SELLER"){
				$(".tr_target_seller").removeClass("dis_none");
			}else{
				$(".tr_target_seller").addClass("dis_none");
			}

		}).trigger("change");

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		var writeFormIng = false;
		$("form[name='dyForm']").submit(function(){
			if(writeFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.bbs_target, "대상을 선택해주세요.", 1, 100, null)) return returnType;
				if(objForm.bbs_target.value == "SELLER"){
					if($(".bbs_target_vendor:checked").length == 0){
						alert("대상 판매처 등급을 하나 이상 선택해주세요.");
						return false;
					}
				}
				if (!valForm.chkValue(objForm.bbs_category, "카테고리를 선택해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.bbs_title, "제목을 정확히 입력해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.bbs_contents, "내용을 정확히 입력해주세요.", 1, 20000, null)) return returnType;

				//this.action = "notice_proc.php";
				$("#btn-save").attr("disabled", true);
				writeFormIng = true;

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 공지사항 보기 페이지 초기화
	 * @constructor
	 */
	var NoticeViewInit = function(){

		//첨부파일 다운로드
		$(".btn-download").on("click", function(){
			var file_idx = $(this).data("file_idx");
			var file_name = $(this).data("file_name");

			Common.simpleUploadedFileDown(file_idx, file_name);
		});

		$("#btn-delete").on("click", function(){
			if(confirm('삭제하시겠습니까?')) {
				$("form[name='dyForm'] input[name='mode']").val("delete");
				$("form[name='dyForm']").attr("action", "bbs_proc.php");
				$("form[name='dyForm']").submit();
			}
		});
	};

	/**
	 * 업체게시판 리스트 페이지 초기화
	 * @constructor
	 */
	var BizListInit = function(){
		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});

		//검색버튼
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});
	};

	/**
	 * 업체게시판 쓰기 페이지 초기화
	 * @constructor
	 */
	var BizWriteInit = function(){
		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn-bbs_file_idx_1', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_1',
			_target_filename : '.span_bbs_file_idx_1',
			_target_input_hidden : '#bbs_file_idx_1',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		var writeFormIng = false;
		$("form[name='dyForm']").submit(function(){
			if(writeFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if(typeof objForm.bbs_category != "undefined") {
					if (!valForm.chkValue(objForm.bbs_category, "카테고리를 선택해주세요.", 1, 100, null)) return returnType;
				}
				if (!valForm.chkValue(objForm.bbs_title, "제목을 정확히 입력해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.bbs_contents, "내용을 정확히 입력해주세요.", 1, 20000, null)) return returnType;

				//this.action = "notice_proc.php";
				$("#btn-save").attr("disabled", true);
				writeFormIng = true;

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 업체게시판 보기 페이지 초기화
	 * @constructor
	 */
	var BizViewInit = function(){

		//첨부파일 다운로드
		$(".btn-download").on("click", function(){
			var file_idx = $(this).data("file_idx");
			var file_name = $(this).data("file_name");

			Common.simpleUploadedFileDown(file_idx, file_name);
		});

		$("#btn-delete").on("click", function(){
			if(confirm('삭제하시겠습니까?')) {
				$("form[name='dyForm'] input[name='mode']").val("delete");
				$("form[name='dyForm']").attr("action", "bbs_proc.php");
				$("form[name='dyForm']").submit();
			}
		});

		getCommnetList();

		$("#btn-comment-add").on("click", function(){

			var comment = $.trim($(".comment_input").eq(0).val());

			if(comment == ""){
				alert("댓글 내용을 입력해주세요.");
				return;
			}

			var p_url = "bbs_comment_proc.php";
			var dataObj = new Object();
			dataObj.mode = "comment_add";
			dataObj.bbs_idx = $("input[name='bbs_idx']").eq(0).val();
			dataObj.comment = $(".comment_input").eq(0).val();

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {

				if(response.result){
					$(".comment_input").val("");
					getCommnetList();
				}

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});


	};

	/**
	 * 업체게시판 댓글 목록 가져오기
	 */
	var getCommnetList = function(){

		var p_url = "bbs_comment_proc.php";
		var dataObj = new Object();
		dataObj.mode = "comment_list";
		dataObj.bbs_idx = $("input[name='bbs_idx']").eq(0).val();

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {

			if(response.result){
				var data = response.data;
				console.log
				$(".comment_wrap").empty();
				$(".comment_wrap").append("<ul></ul>");
				$.each(data, function(i, o){
					var html = '<li>' +
									'<div class="comment_item">' +
										'<div class="comment_left">' +
											'<div class="author">'+o.name;
					if(o.is_mine == 1) {
						html +=	'<div class="btn_del"><a href="javascript:;" class="btn-comment-delete" data-idx="' + o.comment_idx + '"><i class="far fa-times-circle"></i></a></div>';
					}
					html +=                 '</div>' +
											'<div class="time">'+o.regdate+'</div>' +
										'</div>' +
										'<div class="comment_right">' +
											'<div class="comment_contents">' +
											o.comment.replace(/(?:\r\n|\r|\n)/g, '<br />') +
											'</div>' +
										'</div>' +
									'</div>' +
								'</li>';
					$(".comment_wrap ul").append(html);
				});


				$(".btn-comment-delete").on("click", function(){
					if(confirm("삭제하시겠습니까?")){
						deleteBizViewComment($(this).data("idx"));
					}
				});
			}

			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 업체 게시판 댓글 쓰기
	 * @param comment_idx
	 */
	var deleteBizViewComment = function(comment_idx){
		var p_url = "bbs_comment_proc.php";
		var dataObj = new Object();
		dataObj.mode = "comment_delete";
		dataObj.comment_idx = comment_idx;

		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {

			if(response.result){
				getCommnetList();
			}

			hideLoader();
		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	/**
	 * 디자인게시판 리스트 페이지 초기화
	 * @constructor
	 */
	var DesignListInit = function(){
		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});

		//검색버튼
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});
	};

	/**
	 * 디자인게시판 쓰기 페이지 초기화
	 * @constructor
	 */
	var DesignWriteInit = function(){
		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn-bbs_file_idx_1', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_1',
			_target_filename : '.span_bbs_file_idx_1',
			_target_input_hidden : '#bbs_file_idx_1',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file2 = new FileUpload2('btn-bbs_file_idx_2', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_2',
			_target_filename : '.span_bbs_file_idx_2',
			_target_input_hidden : '#bbs_file_idx_2',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file3 = new FileUpload2('btn-bbs_file_idx_3', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_3',
			_target_filename : '.span_bbs_file_idx_3',
			_target_input_hidden : '#bbs_file_idx_3',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file4 = new FileUpload2('btn-bbs_file_idx_4', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_4',
			_target_filename : '.span_bbs_file_idx_4',
			_target_input_hidden : '#bbs_file_idx_4',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file5 = new FileUpload2('btn-bbs_file_idx_5', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_5',
			_target_filename : '.span_bbs_file_idx_5',
			_target_input_hidden : '#bbs_file_idx_5',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file5_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		var writeFormIng = false;
		$("form[name='dyForm']").submit(function(){
			if(writeFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if($(".bbs_target_vendor:checked").length == 0){
						alert("벤더사 등급을 하나 이상 선택해주세요.");
						return false;
					}
				if (!valForm.chkValue(objForm.bbs_category, "카테고리를 선택해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.bbs_title, "제목을 정확히 입력해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.bbs_contents, "내용을 정확히 입력해주세요.", 1, 20000, null)) return returnType;

				//this.action = "notice_proc.php";
				$("#btn-save").attr("disabled", true);
				writeFormIng = true;

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 디자인 게시판 보기 페이지 초기화
	 * @constructor
	 */
	var DesignViewInit = function(){

		//첨부파일 다운로드
		$(".btn-download").on("click", function(){
			var file_idx = $(this).data("file_idx");
			var file_name = $(this).data("file_name");

			Common.simpleUploadedFileDown(file_idx, file_name);
		});

		$("#btn-delete").on("click", function(){
			if(confirm('삭제하시겠습니까?')) {
				$("form[name='dyForm'] input[name='mode']").val("delete");
				$("form[name='dyForm']").attr("action", "bbs_proc.php");
				$("form[name='dyForm']").submit();
			}
		});

		getCommnetList();

		$("#btn-comment-add").on("click", function(){

			var comment = $.trim($(".comment_input").eq(0).val());

			if(comment == ""){
				alert("댓글 내용을 입력해주세요.");
				return;
			}

			var p_url = "bbs_comment_proc.php";
			var dataObj = new Object();
			dataObj.mode = "comment_add";
			dataObj.bbs_idx = $("input[name='bbs_idx']").eq(0).val();
			dataObj.comment = $(".comment_input").eq(0).val();

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {

				if(response.result){
					$(".comment_input").val("");
					getCommnetList();
				}

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

	};

	/**
	 * 업무게시판 리스트 페이지 초기화
	 * @constructor
	 */
	var WorkListInit = function(){
		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});

		//검색버튼
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});
	};

	/**
	 * 업무게시판 쓰기 페이지 초기화
	 * @constructor
	 */
	var WorkWriteInit = function(){
		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn-bbs_file_idx_1', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_1',
			_target_filename : '.span_bbs_file_idx_1',
			_target_input_hidden : '#bbs_file_idx_1',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file2 = new FileUpload2('btn-bbs_file_idx_2', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_2',
			_target_filename : '.span_bbs_file_idx_2',
			_target_input_hidden : '#bbs_file_idx_2',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file3 = new FileUpload2('btn-bbs_file_idx_3', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_3',
			_target_filename : '.span_bbs_file_idx_3',
			_target_input_hidden : '#bbs_file_idx_3',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file4 = new FileUpload2('btn-bbs_file_idx_4', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_4',
			_target_filename : '.span_bbs_file_idx_4',
			_target_input_hidden : '#bbs_file_idx_4',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file5 = new FileUpload2('btn-bbs_file_idx_5', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_5',
			_target_filename : '.span_bbs_file_idx_5',
			_target_input_hidden : '#bbs_file_idx_5',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file5_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		var writeFormIng = false;
		$("form[name='dyForm']").submit(function(){
			if(writeFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.bbs_title, "제목을 정확히 입력해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.bbs_contents, "내용을 정확히 입력해주세요.", 1, 20000, null)) return returnType;

				//this.action = "notice_proc.php";
				$("#btn-save").attr("disabled", true);
				writeFormIng = true;

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * 업무게시판 보기 페이지 초기화
	 * @constructor
	 */
	var WorkViewInit = function(){

		//첨부파일 다운로드
		$(".btn-download").on("click", function(){
			var file_idx = $(this).data("file_idx");
			var file_name = $(this).data("file_name");

			Common.simpleUploadedFileDown(file_idx, file_name);
		});

		$("#btn-delete").on("click", function(){
			if(confirm('삭제하시겠습니까?')) {
				$("form[name='dyForm'] input[name='mode']").val("delete");
				$("form[name='dyForm']").attr("action", "bbs_proc.php");
				$("form[name='dyForm']").submit();
			}
		});

		getCommnetList();

		$("#btn-comment-add").on("click", function(){

			var comment = $.trim($(".comment_input").eq(0).val());

			if(comment == ""){
				alert("댓글 내용을 입력해주세요.");
				return;
			}

			var p_url = "bbs_comment_proc.php";
			var dataObj = new Object();
			dataObj.mode = "comment_add";
			dataObj.bbs_idx = $("input[name='bbs_idx']").eq(0).val();
			dataObj.comment = $(".comment_input").eq(0).val();

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {

				if(response.result){
					$(".comment_input").val("");
					getCommnetList();
				}

				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

	};

	/**
	 * FAQ 리스트 페이지 초기화
	 * @constructor
	 */
	var FAQListInit = function(){
		//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
		$("input.enterDoSearch").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				$("#searchForm").submit();
			}
		});

		//검색버튼
		$("#btn_searchBar").on("click", function(){
			$("#searchForm").submit();
		});
	};

	/**
	 * FAQ 쓰기 페이지 초기화
	 * @constructor
	 */
	var FAQWriteInit = function(){
		//업로드 버튼 바인딩..
		var file1 = new FileUpload2('btn-bbs_file_idx_1', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_1',
			_target_filename : '.span_bbs_file_idx_1',
			_target_input_hidden : '#bbs_file_idx_1',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file2 = new FileUpload2('btn-bbs_file_idx_2', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_2',
			_target_filename : '.span_bbs_file_idx_2',
			_target_input_hidden : '#bbs_file_idx_2',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file3 = new FileUpload2('btn-bbs_file_idx_3', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_3',
			_target_filename : '.span_bbs_file_idx_3',
			_target_input_hidden : '#bbs_file_idx_3',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file4 = new FileUpload2('btn-bbs_file_idx_4', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_4',
			_target_filename : '.span_bbs_file_idx_4',
			_target_input_hidden : '#bbs_file_idx_4',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file1_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});
		var file5 = new FileUpload2('btn-bbs_file_idx_5', {
			_target_table : 'DY_BBS',
			_target_table_column : 'bbs_file_idx_5',
			_target_filename : '.span_bbs_file_idx_5',
			_target_input_hidden : '#bbs_file_idx_5',
			_upload_no: 1,
			_upload_type : "bbs_file",
			_upload_delete_btn : "btn_bbs_file5_delete",
			_onComplete : function(path, filename, file_idx){
			},
			_onDeleted : function(file_idx) {
			}
		});

		//저장 버튼
		$("#btn-save").on("click", function(e){
			e.preventDefault ? e.preventDefault() : (e.returnValue = false);

			$("form[name='dyForm']").submit();
		});

		//폼 Submit 이벤트
		var writeFormIng = false;
		$("form[name='dyForm']").submit(function(){
			if(writeFormIng) return false;
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if (!valForm.chkValue(objForm.bbs_title, "제목을 정확히 입력해주세요.", 1, 100, null)) return returnType;
				if (!valForm.chkValue(objForm.bbs_contents, "내용을 정확히 입력해주세요.", 1, 20000, null)) return returnType;

				//this.action = "notice_proc.php";
				$("#btn-save").attr("disabled", true);
				writeFormIng = true;

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	/**
	 * FAQ 보기 페이지 초기화
	 * @constructor
	 */
	var FAQViewInit = function(){

		//첨부파일 다운로드
		$(".btn-download").on("click", function(){
			var file_idx = $(this).data("file_idx");
			var file_name = $(this).data("file_name");

			Common.simpleUploadedFileDown(file_idx, file_name);
		});

		$("#btn-delete").on("click", function(){
			if(confirm('삭제하시겠습니까?')) {
				$("form[name='dyForm'] input[name='mode']").val("delete");
				$("form[name='dyForm']").attr("action", "bbs_proc.php");
				$("form[name='dyForm']").submit();
			}
		});
	};

	return {
		NoticeListInit : NoticeListInit,
		NoticeWriteInit : NoticeWriteInit,
		NoticeViewInit: NoticeViewInit,
		BizListInit : BizListInit,
		BizWriteInit : BizWriteInit,
		BizViewInit : BizViewInit,
		DesignListInit : DesignListInit,
		DesignWriteInit : DesignWriteInit,
		DesignViewInit : DesignViewInit,
		WorkListInit : WorkListInit,
		WorkWriteInit : WorkWriteInit,
		WorkViewInit : WorkViewInit,
		FAQListInit : FAQListInit,
		FAQWriteInit : FAQWriteInit,
		FAQViewInit : FAQViewInit,
	}
})();