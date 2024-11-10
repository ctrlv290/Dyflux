/*
 * 파일업로드 관련 js
 * Creator : woox
 */
(function (window, document) {
	FileUpload2 = function (id, options) {

		var that = this;
		that.id = id;
		that.obj = $('#' + id);
		that.outputDiv = that.obj;

		that.options = {
			_target_table : ''             //업로드 대상 테이블
			, _target_table_column : ''    //업로드 대상 테이블 컬럼
			, _target_filename : ''        //업로드 후 파일명이 표시될 jQuery Selector
			, _target_input_hidden : ''    //업로드 후 파일 IDX 를 넣을 Input 객체의 jQuery Selector
			, _file_idx : 0
			, _upload_no : 0
			, _upload_type : 'document'     //업로드 타입 [document : 문서(pdf, image), image: 이미지(png, jpg, gif)]
			, _onComplete : null            //function(path, filename){}   업로드 완료 후 Callback [args : path, filename]
			, _onDeleted : null             //function(path, filename, file_idx){}   업로드 완료 후 Callback [args : path, filename]
			, _upload_delete_btn : ''       //업로드 삭제 버튼 바인딩 jquerySelector
			, _thumbnail : false            //썸네일 생성 여부
			, _thumbnail_width : 0          //썸네일 가로 사이즈
			, _thumbnail_height : 0          //썸네일 세로 사이즈
			, _upload_input_accept : ''
		};

		// 기본 옵션 설정
		for (i in options) that.options[i] = options[i];

		// 초기화!!
		that.init();
	};

	FileUpload2.prototype = {

		//초기화
		//기존 업로드된 파일 IDX 얻기
		//업로드 관련 HTML 생성
		//지정된 버튼에 클릭 이벤트 바인딩
		init: function(){
			var that = this;

			that.options._file_idx = $(that.options._target_input_hidden).val();

			//Upload Accept Setting
			if(that.options._upload_type == "document")
			{
				that.options._upload_input_accept = "image/*,.pdf";
			}else if(that.options._upload_type == "product"){
				that.options._upload_input_accept = "image/*";
			}else if(that.options._upload_type == "stock_document"){
				that.options._upload_input_accept = "image/*,.pdf,.txt,..xls,.xlsx";
			}



			//hidden Frame HTML Create
			that.initUploadHTML();
			that.obj.on("click", function(){
				that.callFileDialog();
			});

			if(that.options._file_idx)
			{
				that.getUploadedFileInfo();
			}
		},

		//업로드를 위한 hidden Form 생성
		//파일 Input 객체에 Change 바인딩
		//파일 선택 시 자동 업로드
		initUploadHTML : function(){
			var that = this;
			var html = '<div id="_dy_upload_wrap" class="_dy_upload_wrap dis_none">\n' +
				'\t<form name="_dy_upload_hiddenUploadForm" id="_dy_upload_hiddenUploadForm" method="post" target="_dy_upload_hiddenUploadIframe" enctype="multipart/form-data" action="/proc/_fileupload.php">\n' +
				'\t\t<input type="hidden" name="_dy_upload_type" id="_dy_upload_type"/>\n' +
				'\t\t<input type="hidden" name="_dy_upload_input_name" id="_dy_upload_input_name"/>\n' +
				'\t\t<input type="hidden" name="_dy_upload_no" id="_dy_upload_no"/>\n' +
				'\t\t<input type="hidden" name="_dy_upload_file_idx" id="_dy_upload_file_idx"/>\n' +
				'\t\t<input type="hidden" name="_dy_upload_target_table" id="_dy_upload_target_table"/>\n' +
				'\t\t<input type="hidden" name="_dy_upload_target_filename" id="_dy_upload_target_filename"/>\n' +
				'\t\t<input type="hidden" name="_dy_upload_target_input_hidden" id="_dy_upload_target_input_hidden"/>\n' +
				'\t</form>\n' +
				'</div>';
			if($("#_dy_upload_hiddenUploadForm").length == 0) {
				$("html").append(html);
			}

			that.hiddenFrameID = '_dy_upload_hiddenUploadIframe_' + that.options._target_table_column + '_' + that.options._upload_no;
			//기존 생성된 Iframe 삭제
			$("#"+that.hiddenFrameID).remove();
			var hiddenFrame = '<iframe name="' + that.hiddenFrameID + '" id="' + that.hiddenFrameID + '" frameborder="0" class="dis_none"></iframe>';
			that.hiddenFrame = $("#"+that.hiddenFrameID);
			$("#_dy_upload_wrap").append(hiddenFrame);

			that.fileInputID = '_dy_upload_file_search_' + that.options._target_table_column + '_' + that.options._upload_no;
			//기존 생성된 Input 삭제
			$("#"+that.fileInputID).remove();
			var fileInputHtml =	'<input type="file" name="' + that.fileInputID + '" id="' + that.fileInputID + '" accept="' + that.options._upload_input_accept + '" />';
			that.fileInput = $("#"+that.fileInputID);
			$("#_dy_upload_hiddenUploadForm").append(fileInputHtml);

			$("#"+that.fileInputID).on('change', function () {
				showLoader();
				if($(this).val()) {
					$("#_dy_upload_type").val(that.options._upload_type);
					$("#_dy_upload_input_name").val(that.fileInputID);
					$("#_dy_upload_no").val(that.options._upload_no);
					$("#_dy_upload_file_idx").val(that.options._file_idx);
					$("#_dy_upload_target_table").val(that.options._target_table);
					$("#_dy_upload_target_table_column").val(that.options._target_table_column);
					$("#_dy_upload_target_filename").val(that.options._target_filename);
					$("#_dy_upload_target_input_hidden").val(that.options._target_input_hidden);

					if (that.isAjaxUploadSupported()) {
						try {
							// other modern browsers
							formData = new FormData(document.getElementById("_dy_upload_hiddenUploadForm"));
						} catch (e) {
							// IE10 MUST have all form items appended as individual form key / value pairs
							formData = new FormData();
							formData.append('_dy_upload_file_search',$("#"+that.fileInputID).files[0]);
							formData.append('_dy_upload_input_name', that.options._upload_type);
							formData.append('_dy_upload_input_name', that.fileInputID);
							formData.append('_dy_upload_no', that.options._upload_no);
							formData.append('_dy_upload_file_idx', that.options._file_idx);
							formData.append('_dy_upload_target_table', that.options._target_table);
							formData.append('_dy_upload_target_table_column', that.options._target_table_column);
							formData.append('_dy_upload_target_filename', that.options._target_filename);
							formData.append('_dy_upload_target_input_hidden', that.options._target_input_hidden);
						}

						$.ajax({
							url: '/proc/_fileupload.php',
							dataType: 'json',
							data: formData,
							context: document.body,
							cache: false,
							contentType: false,
							processData: false,
							type: 'POST'
						}).always(function (response) {
							that.afterUpload(response);
							hideLoader();
						});
					} else {
						$("#_dy_upload_hiddenUploadForm").submit();

						var iframe = document.getElementById(that.hiddenFrameID);
						var eventHandlermyFile = function () {
							if (iframe.detachEvent)
								iframe.detachEvent("onload", eventHandlermyFile);
							else
								iframe.removeEventListener("load", eventHandlermyFile, false);

							var response = that.getIframeContentJSON(iframe);
							that.afterUpload(response);
							hideLoader();
						};

						if (iframe.attachEvent)
							iframe.attachEvent("onload", eventHandlermyFile);
					}
				}
			});
		},

		//파일 선택창 호출
		//target_table  : 업로드 대상 테이블
		//target_filename : 업로드 후 파일명이 표시될 jQuery Selector
		//target_input_hidden : 업로드 후 파일 IDX 를 넣을 Input 객체의 jQuery Selector (업로드 대상 테이블의 업로드 컬럼명)
		callFileDialog : function(){
			var that = this;
			$("#_dy_upload_hiddenUploadForm").attr("target", that.hiddenFrameID);

			that.options._file_idx = $(that.options._target_input_hidden).val();
			$("#"+that.fileInputID).trigger("click");
		},

		//업로드 리턴 처리
		//upload_response : JSON
		afterUpload : function(upload_response) {
			var that = this;
			$('#'+that.fileInputID).val('');
			if(upload_response)
			{
				if(upload_response.result)
				{
					//업로드 성공
					that.setUploadComplete(upload_response.uploadInfo);
				}else{

					if(upload_response.result == undefined) {
						alert('업로드 중 오류가 발생하였습니다.');
						return;
					}else {
						alert(upload_response.msg);
						return;
					}
				}
			}
		},

		//업로드 결과 세팅
		setUploadComplete : function(uploadInfo) {
			var that = this;

			var file_idx = uploadInfo.file_idx;
			var userfilename = uploadInfo.userfilename;
			var extension = uploadInfo.extension;
			that.options._file_idx = file_idx;

			var delteBtnClass = 'btn-upload-delete-'+ that.options._target_table_column + that.options._upload_no;
			if(that.options._upload_delete_btn != "")
			{
				delteBtnClass = that.options._upload_delete_btn;
			}

			var fileBtnClass = 'btn-upload-file-'+ that.options._target_table_column + that.options._upload_no;
			var html = '' +
				'<a href="javascript:;" class="'+fileBtnClass+'">' + userfilename + '</a>' +
				' <a href="javascript:;" class="'+delteBtnClass+'" data-target="' + that.options._target_filename + '" data-input="'+that.options._target_input_hidden+'" data-no="'+that.options._upload_no+'"><i class="far fa-times-circle"></i></a>';

			$(that.options._target_input_hidden).val(file_idx);

			if(that.options._target_filename != "") {
				$(that.options._target_filename).html(html);
			}

			$("."+fileBtnClass).attr("data-filename", uploadInfo.new_file_name);
			$("."+fileBtnClass).attr("data-path", uploadInfo.path);
			$("."+fileBtnClass).on("click", function(){
				that.linkToUploaded($(this));
			});

			$("."+delteBtnClass).on("click", function(){
				that.clearUploaded();
			});

			if(typeof that.options._onComplete == "function")
			{
				that.options._onComplete(uploadInfo.path, uploadInfo.new_file_name, file_idx);
			}
		},

		//삭제 버튼 클릭 시 Clear
		clearUploaded : function(){
			var that = this;
			$(that.options._target_input_hidden).val("");
			$(that.options._target_filename).html("");

			if(typeof that.options._onComplete == "function") {
				that.options._onDeleted(that.options._file_idx);
			}
		},

		//파일 링크 클릭 시 다운로드
		linkToUploaded : function($obj){
			var that = this;
			//window.open('/' + $obj.data("path") + '/' + $obj.data("filename"));
			var url = "/proc/_filedownload.php?idx=" + that.options._file_idx + "&filename=" + $obj.data("filename") ;
			$("#hidden_ifrm_common_filedownload").attr("src", url);
		},

		//Iframe 에 출력된 String 을 Json 으로 변환
		getIframeContentJSON : function (iframe) {
			var that = this;

			try {
				var doc = iframe.contentDocument ? iframe.contentDocument : iframe.contentWindow.document,
					response;

				var innerHTML = doc.body.innerHTML;
				if (innerHTML.slice(0, 5).toLowerCase() == "<pre>" && innerHTML.slice(-6).toLowerCase() == "</pre>") {
					innerHTML = doc.body.firstChild.firstChild.nodeValue;
				}
				response = jQuery.parseJSON(innerHTML);
			} catch (err) {
				response = { success: false };
			}

			return response;
		},

		//Ajax 업로드가 가능한 브라우저인지 판단
		isAjaxUploadSupported : function () {
			var input = document.createElement("input");
			input.type = "file";

			return (
				"multiple" in input &&
				typeof File != "undefined" &&
				typeof FormData != "undefined" &&
				typeof (new XMLHttpRequest()).upload != "undefined");
		},

		//기 업로드된 파일 정보 얻기 및 세팅
		getUploadedFileInfo : function() {
			var that = this;

			if(that.options._file_idx && that.options._file_idx != 0)
			{
				var p_url = "/proc/_fileinfo.php";
				var dataObj = new Object();
				dataObj.mode = "get_file_info";
				dataObj.file_idx = that.options._file_idx;
				$.ajax({
					type: 'POST',
					url: p_url,
					dataType: "json",
					data: dataObj
				}).done(function (response) {
					if(response.result)
					{
						that.setUploadComplete(response.fileInfo);
					}else{
						//
					}
				});
			}
		}
	};

})(window, document);

var FileUpload = (function() {
	var that = this;

	var init = function () {
	};

	var _target_table = "";             //업로드 대상 테이블
	var _target_table_column = "";      //업로드 대상 테이블 컬럼
	var _target_filename = "";          //업로드 후 파일명이 표시될 jQuery Selector
	var _target_input_hidden = "";      //업로드 후 파일 IDX 를 넣을 Input 객체의 jQuery Selector
	var _file_idx = 0;
	var _upload_no = 0;
	var that;
	var formData;

	//업로드를 위한 hidden Form 생성
	//파일 Input 객체에 Change 바인딩
	//파일 선택 시 자동 업로드
	var initUploadHTML = function(){
		var html = '<div class="dis_none">\n' +
			'\t<form name="_dy_upload_hiddenUploadForm" id="_dy_upload_hiddenUploadForm" method="post" target="_dy_upload_hiddenUploadIframe" enctype="multipart/form-data" action="/proc/_fileupload.php">\n' +
			'\t\t<input type="file" name="_dy_upload_file_search" id="_dy_upload_file_search" accept="image/*,.pdf" />\n' +
			'\t\t<input type="hidden" name="_dy_upload_no" id="_dy_upload_no"/>\n' +
			'\t\t<input type="hidden" name="_dy_upload_file_idx" id="_dy_upload_file_idx"/>\n' +
			'\t\t<input type="hidden" name="_dy_upload_target_table" id="_dy_upload_target_table"/>\n' +
			'\t\t<input type="hidden" name="_dy_upload_target_filename" id="_dy_upload_target_filename"/>\n' +
			'\t\t<input type="hidden" name="_dy_upload_target_input_hidden" id="_dy_upload_target_input_hidden"/>\n' +
			'\t</form>\n' +
			'<iframe name="_dy_upload_hiddenUploadIframe" id="_dy_upload_hiddenUploadIframe" frameborder="0" class="dis_none"></iframe>\n'+
			'</div>';

		$("html").append(html);

		$('#_dy_upload_file_search').on('change', function () {
			showLoader();
			if($(this).val()) {
				if (isAjaxUploadSupported()) {
					try {
						// other modern browsers
						formData = new FormData(document.getElementById('_dy_upload_hiddenUploadForm'));
					} catch (e) {
						alert(e);
						// IE10 MUST have all form items appended as individual form key / value pairs
						formData = new FormData();
						formData.append('_dy_upload_file_search', $("#_dy_upload_file_search").files[0]);
					}

					formData.append('_dy_upload_no', _upload_no);
					formData.append('_dy_upload_file_idx', _file_idx);
					formData.append('_dy_upload_target_table', _target_table);
					formData.append('_dy_upload_target_table_column', _target_table_column);
					formData.append('_dy_upload_target_filename', _target_filename);
					formData.append('_dy_upload_target_input_hidden', _target_input_hidden);

					$.ajax({
						url: '/proc/_fileupload.php',
						dataType: 'json',
						data: formData,
						context: document.body,
						cache: false,
						contentType: false,
						processData: false,
						type: 'POST'
					}).always(function (response) {
						afterUpload(response);
						hideLoader();
					});
				} else {
					$("#_dy_upload_no").val(_upload_no);
					$("#_dy_upload_file_idx").val(_file_idx);
					$("#_dy_upload_target_table").val(_target_table);
					$("#_dy_upload_target_table_column").val(_target_table_column);
					$("#_dy_upload_target_filename").val(_target_filename);
					$("#_dy_upload_target_input_hidden").val(_target_input_hidden);
					$("#hiddenUploadForm").submit();

					var iframe = document.getElementById('hiddenUploadIframe');
					var eventHandlermyFile = function () {
						if (iframe.detachEvent)
							iframe.detachEvent("onload", eventHandlermyFile);
						else
							iframe.removeEventListener("load", eventHandlermyFile, false);

						var response = getIframeContentJSON(iframe);
						afterUpload(response);
						hideLoader();
					};

					if (iframe.attachEvent)
						iframe.attachEvent("onload", eventHandlermyFile);
				}
			}
		});
	};

	//파일 선택창 호출
	//target_table  : 업로드 대상 테이블
	//target_filename : 업로드 후 파일명이 표시될 jQuery Selector
	//target_input_hidden : 업로드 후 파일 IDX 를 넣을 Input 객체의 jQuery Selector (업로드 대상 테이블의 업로드 컬럼명)
	var callFileDialog = function(target_table, target_table_column, target_filename, target_input_hidden_id, upload_no)
	{
		_target_table = target_table;
		_target_table_column = target_table_column;
		_target_filename = target_filename;
		_target_input_hidden = target_input_hidden_id;
		_upload_no = upload_no;

		_file_idx = $(_target_input_hidden).val();

		$("#_dy_upload_file_search").trigger("click");
	};

	//업로드 리턴 처리
	//upload_response : JSON
	var afterUpload = function(upload_response)
	{
		$('#_dy_upload_file_search').val('');
		if(upload_response)
		{
			if(upload_response.result)
			{
				//업로드 성공
				setUploadComplete(upload_response.uploadInfo);
			}else{

				if(upload_response.result == undefined) {
					alert('업로드 중 오류가 발생하였습니다.');
					return;
				}else {
					alert(upload_response.msg);
					return;
				}
			}
		}
	};

	//업로드 결과 세팅
	var setUploadComplete = function(uploadInfo)
	{
		var file_idx = uploadInfo.file_idx;
		var userfilename = uploadInfo.userfilename;
		var extension = uploadInfo.extension;
		var new_file_name = uploadInfo.new_file_name;

		var delteBtnClass = 'btn-upload-delete-'+ _target_table_column + _upload_no;
		var html = '' +
			userfilename +
			' <a href="javascript:;" class="'+delteBtnClass+'" data-target="' + _target_filename + '" data-input="'+_target_input_hidden+'" data-no="'+_upload_no+'"><i class="far fa-times-circle"></i></a>';

		$(_target_input_hidden).val(file_idx);
		$(_target_filename).html(html);
		$("."+delteBtnClass).on("click", function(){
			clearUploaded($(this));
		});
	};

	var clearUploaded = function(obj){
		var target = $(obj).data("target");
		var inp = $(obj).data("input");

		$(target).html("");
		$(inp).val("");
	};

	//Iframe 에 출력된 String 을 Json 으로 변환
	var getIframeContentJSON = function (iframe) {
		try {
			var doc = iframe.contentDocument ? iframe.contentDocument : iframe.contentWindow.document,
				response;

			var innerHTML = doc.body.innerHTML;
			if (innerHTML.slice(0, 5).toLowerCase() == "<pre>" && innerHTML.slice(-6).toLowerCase() == "</pre>") {
				innerHTML = doc.body.firstChild.firstChild.nodeValue;
			}
			response = jQuery.parseJSON(innerHTML);
		} catch (err) {
			response = { success: false };
		}

		return response;
	};

	//Ajax 업로드가 가능한 브라우저인지 판단
	var isAjaxUploadSupported = function () {
		var input = document.createElement("input");
		input.type = "file";

		return (
			"multiple" in input &&
			typeof File != "undefined" &&
			typeof FormData != "undefined" &&
			typeof (new XMLHttpRequest()).upload != "undefined");
	};

	return {
		initUploadHTML : initUploadHTML,
		callFileDialog: callFileDialog
	}
})();
