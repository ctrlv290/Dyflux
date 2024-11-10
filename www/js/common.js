/*
 * 기본 공통 js
 * Creator : woox
 */
var Common = {

	//Javascript Site Config
	jsSiteConfig : {
		jqGridDefaultHeight : 450,      //기본 높이 값
		jqGridRowList : [10,20,30],     //페이지 Row 개수 Selectbox 설정
		jqGridRowListBig : [300,500,1000, 2000, 3000],     //페이지 Row 개수 Selectbox 설정
	},

	/**
	 * 팝업 함수
	 * @param url           : 팝업 URL
	 * @param popup_id      : 팝업 ID
	 * @param wW            : 팝업 가로 사이즈 (0 일때 화면 가로 최대 크기)
	 * @param wH            : 팝업 세로 사이즈 (0 일때 화면 세로 최대 크기)
	 * @param isScroll      : 스크롤 여부
	 */
	newWinPopup : function(url, popup_id, wW, wH, isScroll) {
		var winObj;
		var pos_x = (window.screen.width - wW) / 2;
		var pos_y = (window.screen.height - wH) / 2;

		if(wW == 0){
			wW = window.screen.width - 20;
			pos_x = 10;
		}

		if(wH == 0){
			wH = window.screen.height - 110;
			pos_y = 10;
		}

		window[popup_id] = window.open(url, popup_id, "width=" + wW + ",height=" + wH + ",top=" + pos_y + ",left=" + pos_x + ",status=yes,resizable=0,scrollbars=" + isScroll);

		try {
			if (window[popup_id] == null) {
				alert("팝업차단이 설정되어 있습니다. 팝업차단을 해제하여 주세요.");
				return;
			}
			window[popup_id].focus();
		}catch(e){

		}
	},

	/**
	 * 팝업 함수
	 * @param url           : 팝업 URL
	 * @param popup_id      : 팝업 ID
	 * @param wW            : 팝업 가로 사이즈 (0 일때 화면 가로 최대 크기)
	 * @param wH            : 팝업 세로 사이즈 (0 일때 화면 세로 최대 크기)
	 * @param isScroll      : 스크롤 여부
	 * @param isResizeable  : 리사이즈 여부
	 */
	newWinPopup2 : function(url, popup_id, wW, wH, isScroll, isResizeable) {
		var winObj;
		var pos_x = (window.screen.width - wW) / 2;
		var pos_y = (window.screen.height - wH) / 2;

		if(wW == 0){
			wW = window.screen.width - 20;
			pos_x = 10;
		}

		if(wH == 0){
			wH = window.screen.height - 110;
			pos_y = 10;
		}

		window[popup_id] = window.open(url, popup_id, "width=" + wW + ",height=" + wH + ",top=" + pos_y + ",left=" + pos_x + ",status=yes,resizable=" + isResizeable + ",scrollbars=" + isScroll);

		try {
			if (window[popup_id] == null) {
				alert("팝업차단이 설정되어 있습니다. 팝업차단을 해제하여 주세요.");
				return;
			}
			window[popup_id].focus();
		}catch(e){

		}
	},

	//SQLSRV DateTime Convert - OnlyDate
	toDateTime: function(full_datetime_string) {
		var returnValue = "";
		try {
			if (typeof full_datetime_string == "undefined" || full_datetime_string == null || full_datetime_string == "1900-01-01 00:00:00.000") {
				returnValue = "";
			} else {
				returnValue = full_datetime_string.substring(0, 19);
			}
		}catch (e) {
			//console.log(e);
		} finally {

		}
		return returnValue;
	},

	//SQLSRV DateTime Convert - OnlyTime
	toDateTimeOnlyDate: function(full_datetime_string) {
		var returnValue = "";
		try {
			if (typeof full_datetime_string == "undefined" || full_datetime_string == null || full_datetime_string == "1900-01-01 00:00:00.000") {
				returnValue = "";
			} else {
				returnValue = full_datetime_string.substring(0, 10);
			}
		}catch(e){
			//return "";
		} finally {

		}
		return returnValue;
	},

	//SQLSRV DateTime Convert - OnlyTime
	toDateTimeOnlyTime: function(full_datetime_string) {
		try {
			if (typeof full_datetime_string == "undefined" || full_datetime_string == null || full_datetime_string == "1900-01-01 00:00:00.000") {
				returnValue = "";
			} else {
				returnValue = full_datetime_string.substring(11, 19);
			}
		}catch(e){
			//return "";
		} finally {

		}
		return returnValue;
	},

	//JQGrid Auto Resize : No Scroll Page Only
	jqGridResize: function(grid_id) {
		//var wrap_width = $(".tb_wrap.grid_tb").width();
		var wrap_width = $(grid_id).parents(".tb_wrap.grid_tb").eq(0).width();
		$(grid_id).setGridWidth(wrap_width);

		var heightCal = $(window).height() - $(grid_id).closest(".ui-jqgrid-bdiv").offset().top - 60;
		var bottomBtnSetHeight = ($(".btn_set:last-child").length == 1) ? $(".btn_set:last-child").outerHeight(true) : 0;
		heightCal = heightCal - bottomBtnSetHeight;
		$(grid_id).jqGrid('setGridHeight', heightCal );
	},

	//JQGrid Auto Resize Only Width
	jqGridResizeWidth: function(grid_id) {
		//var wrap_width = $(".tb_wrap.grid_tb").width();
		var wrap_width = $(grid_id).parents(".tb_wrap.grid_tb").eq(0).width();
		$(grid_id).setGridWidth(wrap_width);
	},

	//JQGrid Auto Resize Only Width
	jqGridResizeWidthByTarget: function(grid_id, $target) {
		//console.log("width", $target.width());
		var wrap_width = $target.width();
		$(grid_id).setGridWidth(wrap_width);
	},

	jqGridResizeByTargetAndMinusMarginH: function(grid_id, $target, minusMarginH){
		var wrap_width = $target.width();
		var wrap_height = $target.height() - minusMarginH;
		$(grid_id).setGridWidth(wrap_width);
		$(grid_id).jqGrid('setGridHeight', wrap_height );
	},

	jqGridResizeToWindowHeightMinusMarginH: function(grid_id, minusMarginH){
		var wrap_width = $(grid_id).parents(".tb_wrap.grid_tb").eq(0).width();
		$(grid_id).setGridWidth(wrap_width);

		var heightCal = $(window).height() - $(grid_id).closest(".ui-jqgrid-bdiv").offset().top - 60 - minusMarginH;
		$(grid_id).jqGrid('setGridHeight', heightCal );
	},

	// 다음 우편번호 찾기 팝업 실행 함수
	daumZipSearch: function(_zipcode_id, _addr1_id, _addr2_id) {
		//아래 코드처럼 테마 객체를 생성합니다.(color값은 #F00, #FF0000 형식으로 입력하세요.)
		//변경되지 않는 색상의 경우 주석 또는 제거하시거나 값을 공백으로 하시면 됩니다.
		var themeObj = {
			//bgColor: "", //바탕 배경색
			searchBgColor: "#239AEB", //검색창 배경색
			//contentBgColor: "", //본문 배경색(검색결과,결과없음,첫화면,검색서제스트)
			//pageBgColor: "", //페이지 배경색
			//textColor: "", //기본 글자색
			queryTextColor: "#FFFFFF" //검색창 글자색
			//postcodeTextColor: "", //우편번호 글자색
			//emphTextColor: "", //강조 글자색
			//outlineColor: "", //테두리
		};

		new daum.Postcode({
			theme: themeObj,
			oncomplete: function(data) {
				// 팝업에서 검색결과 항목을 클릭했을때 실행할 코드를 작성하는 부분.

				// 각 주소의 노출 규칙에 따라 주소를 조합한다.
				// 내려오는 변수가 값이 없는 경우엔 공백('')값을 가지므로, 이를 참고하여 분기 한다.
				var fullAddr = ''; // 최종 주소 변수
				var extraAddr = ''; // 조합형 주소 변수

				// 사용자가 선택한 주소 타입에 따라 해당 주소 값을 가져온다.
				if (data.userSelectedType === 'R') { // 사용자가 도로명 주소를 선택했을 경우
					fullAddr = data.roadAddress;

				} else { // 사용자가 지번 주소를 선택했을 경우(J)
					fullAddr = data.jibunAddress;
				}

				// 사용자가 선택한 주소가 도로명 타입일때 조합한다.
				if(data.userSelectedType === 'R'){
					//법정동명이 있을 경우 추가한다.
					if(data.bname !== ''){
						extraAddr += data.bname;
					}
					// 건물명이 있을 경우 추가한다.
					if(data.buildingName !== ''){
						extraAddr += (extraAddr !== '' ? ', ' + data.buildingName : data.buildingName);
					}
					// 조합형주소의 유무에 따라 양쪽에 괄호를 추가하여 최종 주소를 만든다.
					fullAddr += (extraAddr !== '' ? ' ('+ extraAddr +')' : '');
				}

				// 우편번호와 주소 정보를 해당 필드에 넣는다.
				document.getElementById(_zipcode_id).value = data.zonecode; //5자리 새우편번호 사용
				document.getElementById(_addr1_id).value = fullAddr;

				// 커서를 상세주소 필드로 이동한다.
				if(_addr2_id != "" && document.getElementById(_addr2_id) != null) {
					document.getElementById(_addr2_id).focus();
				}
			}
		}).open();
	},

	//멀티 이메일 적용
	setMultiEmailInput: function(target_id, dummy_id) {
		//대표 이메일 멀티 입력 적용
		$("#"+target_id).hide();
		var tmp = $("#"+target_id).val();
		var tmpResult = "";
		if(tmp.length > 0)
		{
			var tmpAry = tmp.split(",");
			if(tmpAry.length > 0)
			{
				tmpResult = JSON.stringify(tmpAry);
			}
		}

		var dummy_id = target_id + "_dummy";
		var dummy_html = '<input type="text" name="'+dummy_id+'" id="'+dummy_id+'" class="dis_none" maxlength="300" value="" />';

		$("#"+target_id).after(dummy_html);

		$('#'+dummy_id).val(tmpResult);
		$('#'+dummy_id).multiple_emails();
		$('#'+dummy_id).on("change", function(){
			$("#"+target_id).val((eval($(this).val()).join()));
		});
	},

	//변경이력 공통 팝업
	changeLogViewerPopup: function(log_type) {
		var url = '/info/change_log_viewer.php?view='+log_type;
		Common.newWinPopup(url, 'change_log_viewer_pop', 1200, 800, 'yes');
	},

	//공통 날짜 프리셋 선택 값 배열
	dateSelPresetArr: {
		1 : "오늘",
		2 : "어제",
		3 : "이번주",
		4 : "지난주",
		5 : "최근7일",
		6 : "이번달",
		7 : "지난달",
		8 : "최근30일",
		9 : "최근90일",
	},

	//날짜 프리셋 적용
	getPresetDate: function (period_no)
	{
		var today = _gl_today_text;
		var today_obj = _gl_today_obj;
		var returnVal = [];
		period_no = period_no + "";
		switch (period_no) {
			case "1" :  //오늘
				returnVal.push(today);
				returnVal.push(today);
				break;
			case "2" :  //어제
				returnVal.push(moment(today).add(-1, 'days').format("YYYY-MM-DD"));
				returnVal.push(moment(today).add(-1, 'days').format("YYYY-MM-DD"));
				break;
			case "3" :  //이번주
				returnVal.push(moment(_gl_today_text).startOf("week").format("YYYY-MM-DD"));
				returnVal.push(moment(_gl_today_text).endOf("week").format("YYYY-MM-DD"));
				break;
			case "4" :  //지난주
				returnVal.push(moment(_gl_today_text).add(-1, "weeks").startOf("week").format("YYYY-MM-DD"));
				returnVal.push(moment(_gl_today_text).add(-1, "weeks").endOf("week").format("YYYY-MM-DD"));
				break;
			case "5" :  //최근 7일
				returnVal.push(moment(_gl_today_text).add(-6, "days").format("YYYY-MM-DD"));
				returnVal.push(today);
				break;
			case "6" :  //이번달
				returnVal.push(moment(_gl_today_text).startOf("month").format("YYYY-MM-DD"));
				returnVal.push(moment(_gl_today_text).endOf("month").format("YYYY-MM-DD"));
				break;
			case "7" :  //지난달
				returnVal.push(moment(_gl_today_text).add(-1, "month").startOf("month").format("YYYY-MM-DD"));
				returnVal.push(moment(_gl_today_text).add(-1, "month").endOf("month").format("YYYY-MM-DD"));
				break;
			case "8" :  //최근 30일
				returnVal.push(moment(_gl_today_text).add(-29, "days").format("YYYY-MM-DD"));
				returnVal.push(today);
				break;
			case "9" :  //최근 90일
				returnVal.push(moment(_gl_today_text).add(-89, "days").format("YYYY-MM-DD"));
				returnVal.push(today);
				break;
		}

		return returnVal;
	},

	//날짜 선택 프리셋 셀렉트 박스 세팅 및 Change 바인딩
	//selectbox_id : 셀렉트 박스 ID
	//start_input_id : 셀렉트 선택 시 설정될 시작 날짜 Input
	//end_input_id : 셀렉트 선택 시 설정될 종료 날짜 Input
	setDatePreSetSelectbox: function(selectbox_id, start_input_id, end_input_id, preset_value){
		$("#" + selectbox_id + " option").remove();
		$("#" + selectbox_id).append('<option value="" selected="selected">선택</option>');
		$.each(Common.dateSelPresetArr, function(i, v){
			$("#"+selectbox_id).append('<option value="' + i + '">' + v + '</option>');
		});

		if(Common.dateSelPresetArr.hasOwnProperty(preset_value) )
		{
			var tmp_arr = Common.getPresetDate(preset_value);

			//설정된 값이 있으면 프리셋 무시
			if($("#" + start_input_id).val() == "") {
				$("#" + start_input_id).val(tmp_arr[0]);
			}

			//설정된 값이 있으면 프리셋 무시
			if($("#" + end_input_id).val() == "") {
				$("#" + end_input_id).val(tmp_arr[1]);
			}
		}

		$("#"+selectbox_id).on("change", function(){
			if($(this).val() != "") {
				var tmp_arr = Common.getPresetDate($(this).val());

				$("#" + start_input_id).val(tmp_arr[0]);
				$("#" + end_input_id).val(tmp_arr[1]);
			}
		});
	},

	//날짜 선택 프리셋 셀렉트 박스 세팅 및 Change 바인딩
	//날짜 + 시간 Input 포함
	//시간은 00:00:00 ~ 23:59:59 으로 세팅
	//selectbox_id : 셀렉트 박스 ID
	//start_input_id : 셀렉트 선택 시 설정될 시작 날짜 Input
	//end_input_id : 셀렉트 선택 시 설정될 종료 날짜 Input
	setDateTimePreSetSelectbox: function(selectbox_id, start_input_id, end_input_id, start_time_id, end_time_id, preset_value){
		$("#" + selectbox_id + " option").remove();
		$("#" + selectbox_id).append('<option value="" selected="selected">선택</option>');
		$.each(Common.dateSelPresetArr, function(i, v){
			$("#"+selectbox_id).append('<option value="' + i + '">' + v + '</option>');
		});

		if(Common.dateSelPresetArr.hasOwnProperty(preset_value) )
		{
			var tmp_arr = Common.getPresetDate(preset_value);

			//설정된 값이 있으면 프리셋 무시
			if($("#" + start_input_id).val() == "") {
				$("#" + start_input_id).val(tmp_arr[0]);
			}

			//설정된 값이 있으면 프리셋 무시
			if($("#" + end_input_id).val() == "") {
				$("#" + end_input_id).val(tmp_arr[1]);
			}

			//설정된 값이 있으면 프리셋 무시
			if(start_time_id != "") {
				if ($("#" + start_time_id).val() == "") {
					$("#" + start_time_id).val("00:00:00");
				}
			}

			//설정된 값이 있으면 프리셋 무시
			if(end_time_id != "") {
				if ($("#" + end_time_id).val() == "") {
					$("#" + end_time_id).val("23:59:59");
				}
			}
		}

		$("#"+selectbox_id).on("change", function(){
			if($(this).val() != "") {
				var tmp_arr = Common.getPresetDate($(this).val());

				$("#" + start_input_id).val(tmp_arr[0]);
				$("#" + end_input_id).val(tmp_arr[1]);
				if(start_time_id != "") {
					$("#" + start_time_id).val("00:00:00");
				}
				if(end_time_id != "") {
					$("#" + end_time_id).val("23:59:59");
				}
			}
		});
	},

	//모바일용 날짜 선택 프리셋 버튼 세팅
	//날짜 선택 프리셋 셀렉트 박스 세팅 및 Change 바인딩
	//날짜 + 시간 Input 포함
	//시간은 00:00:00 ~ 23:59:59 으로 세팅
	//btn_set_id : 셀렉트 박스 ID
	//start_input_id : 셀렉트 선택 시 설정될 시작 날짜 Input
	//end_input_id : 셀렉트 선택 시 설정될 종료 날짜 Input
	setDateTimePreSetSelectBtnMobile: function(btn_set_id, start_input_id, end_input_id, start_time_id, end_time_id, preset_value){
		$("#" + btn_set_id).empty();

		$.each(Common.dateSelPresetArr, function(i, o){
			$("#" + btn_set_id).append('<a href="javascript:" class="btn btn-date-preset" data-index="'+i+'">'+o+'</a>');
		});


		if(Common.dateSelPresetArr.hasOwnProperty(preset_value) )
		{
			var tmp_arr = Common.getPresetDate(preset_value);

			//설정된 값이 있으면 프리셋 무시
			if($("#" + start_input_id).val() == "") {
				$("#" + start_input_id).val(tmp_arr[0]);
			}

			//설정된 값이 있으면 프리셋 무시
			if($("#" + end_input_id).val() == "") {
				$("#" + end_input_id).val(tmp_arr[1]);
			}

			//설정된 값이 있으면 프리셋 무시
			if(start_time_id != "") {
				if ($("#" + start_time_id).val() == "") {
					$("#" + start_time_id).val("00:00:00");
				}
			}

			//설정된 값이 있으면 프리셋 무시
			if(end_time_id != "") {
				if ($("#" + end_time_id).val() == "") {
					$("#" + end_time_id).val("23:59:59");
				}
			}
		}

		$("#"+btn_set_id).on("click", ".btn-date-preset", function(){
			if($(this).data("index") != "") {
				var tmp_arr = Common.getPresetDate($(this).data("index"));
				console.log($(this).data("index")+"");
				console.log(tmp_arr+"");

				$("#" + start_input_id).val(tmp_arr[0]);
				$("#" + end_input_id).val(tmp_arr[1]);
				if(start_time_id != "") {
					$("#" + start_time_id).val("00:00:00");
				}
				if(end_time_id != "") {
					$("#" + end_time_id).val("23:59:59");
				}
			}
		});
	},

	//천단위 콤마
	addCommas: function(val){
		if(typeof val != "undefined" && val != null && val != "undefined") {
			return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		}else{
			if(typeof val != "undefined" || val == "undefined"){
				return "";
			}else {
				return val;
			}
		}
	},

	//빈 값 체크
	isEmpty: function(value){
		if(value == "" || value == null || value == undefined || ( value != null && typeof value == "object" && !Object.keys(value).length )){
			return true
		}else{
			return false
		}
	},

	//단축URL만들기
	makeShortUrl: function(url, onComplete){
		var p_url = "/_link/";
		var dataObj = new Object();
		dataObj.url = url;
		dataObj.format = "json";

		showLoader();
		$.ajax({
			type: 'GET',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			//console.log(response);
			var url = "";
			try{
				url = response.url;

				if(typeof onComplete == "function") {
					onComplete(url);
				}

			}catch(e){

			}

			return url;

			hideLoader();
		}).fail(function (jqXHR, textStatus) {
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});

	},

	//첨부파일 다운 받기
	simpleUploadedFileDown: function(file_idx, filename){
		var url = "/proc/_filedownload.php?idx=" + file_idx + "&filename=" + filename;
		$("#hidden_ifrm_common_filedownload").attr("src", url);
	},

	setGridColumnSize: function(grid_name, column_name, size){
		cookie_name = grid_name + "_" + column_name;
		$.cookie(cookie_name, size, {expires: 365});
	},

	getGridColumnSize: function(grid_name, column_name, default_size){
		cookie_name = grid_name + "_" + column_name;
		var rst = $.cookie(cookie_name);
		if(rst){
			return rst;
		}else{
			return default_size;
		}
	},

	setGridColumnSizeToStorage: function(colModel_Ary, save_key){

		var colWidthAry = new Object();
		$.each(colModel_Ary, function(i, o){
			if(i > 0){
				colWidthAry[o.name] = o.width;
			}
		});

		localStorage.setItem("jqgrid_" + save_key, JSON.stringify(colWidthAry));

	},

	getGridColumnSizeFromStorage: function(save_key, $grid_obj){
		var col_string = localStorage.getItem("jqgrid_" + save_key);
		if(col_string != null){
			var col_obj = JSON.parse(col_string);

			$.each(col_obj, function(i, o){
				$grid_obj.setColWidth(i, o, false);
			});
		}

		//$("#mygrid").jqGrid('setColProp','amount',{width:new_width});
	},

	convertOrderStatusTextToLabel: function(status_text){
		var returnHtml = "";
		if(status_text === "주문 정보 수집"){
			returnHtml = '<span class="lbl lb_gold">발주</span>';
		}else if(status_text === "상품매칭") {
			returnHtml = '<span class="lbl lb_gold">발주</span>';
		}else if(status_text === "합포") {
			returnHtml = '<span class="lbl lb_gold">발주</span>';
		}else if(status_text === "발주완료(가접수)") {
			returnHtml = '<span class="lbl lb_gold">가접수</span>';
		}else if(status_text === "접수") {
			returnHtml = '<span class="lb_green">'+status_text+'</span>';
		}else if(status_text === "정상") {
			returnHtml = '<span class="lb_blue">'+status_text+'</span>';
		}else if(status_text === "송장") {
			returnHtml = '<span class="lb_violet2">'+status_text+'</span>';
		}else if(status_text === "배송") {
			returnHtml = '<span class="lb_violet">'+status_text+'</span>';
		}else if(status_text === "보류") {
			returnHtml = '<span class="lb_red">'+status_text+'</span>';
		}else{
			returnHtml = status_text
		}

		return returnHtml;
	},

	setDatePickerForDynamicElement: function($obj){
		$obj.datepicker({
			beforeShow: function() {
				setTimeout(function(){
					$('.ui-datepicker').css('z-index', 99999999999999);
				}, 0);
			}
		});
	},

	LeftPad : function(n, width) {
		n = n + '';
		return n.length >= width ? n : new Array(width - n.length + 1).join('0') + n;
	},

	checkXlsDownWait: function(xls_name, onComplete){

		var dataObj = new Object();
		dataObj.xls_name = xls_name;
		$.ajax({
			type: 'GET',
			url: "/proc/_xls_down_check.php",
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if (response.result) {
				onComplete();
			}
		}).fail(function (jqXHR, textStatus) {
		});

	},

	detectIE : function(){
		var ua = window.navigator.userAgent;

		// Test values; Uncomment to check result …

		// IE 10
		// ua = 'Mozilla/5.0 (compatible; MSIE 10.0; Windows NT 6.2; Trident/6.0)';

		// IE 11
		// ua = 'Mozilla/5.0 (Windows NT 6.3; Trident/7.0; rv:11.0) like Gecko';

		// Edge 12 (Spartan)
		// ua = 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.71 Safari/537.36 Edge/12.0';

		// Edge 13
		// ua = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2486.0 Safari/537.36 Edge/13.10586';

		var msie = ua.indexOf('MSIE ');
		if (msie > 0) {
			// IE 10 or older => return version number
			return parseInt(ua.substring(msie + 5, ua.indexOf('.', msie)), 10);
		}

		var trident = ua.indexOf('Trident/');
		if (trident > 0) {
			// IE 11 => return version number
			var rv = ua.indexOf('rv:');
			return parseInt(ua.substring(rv + 3, ua.indexOf('.', rv)), 10);
		}

		var edge = ua.indexOf('Edge/');
		if (edge > 0) {
			// Edge (IE 12+) => return version number
			return parseInt(ua.substring(edge + 5, ua.indexOf('.', edge)), 10);
		}

		// other browser
		return false;
	},

	getVersionOfIE: function(){
		var word;

		var agent = navigator.userAgent.toLowerCase();

		// IE old version ( IE 10 or Lower )
		if ( navigator.appName == "Microsoft Internet Explorer" ) word = "msie ";

		// IE 11
		else if ( agent.search( "trident" ) > -1 ) word = "trident/.*rv:";

		// Microsoft Edge
		else if ( agent.search( "edge/" ) > -1 ) word = "edge/";

		// 그외, IE가 아니라면 ( If it's not IE or Edge )
		else return -1;

		var reg = new RegExp( word + "([0-9]{1,})(\\.{0,}[0-9]{0,1})" );

		if (  reg.exec( agent ) != null  ) return parseFloat( RegExp.$1 + RegExp.$2 );

		return -1;
	},

	commonInputModalCallback : null,

	registCommonInputModalPop : function() {
		$("#common_input_modal_pop").dialog({
			width: 500,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});
	},

	openCommonInputModalPop : function(title, mode, url, data, doneCallback) {
		let dataObj = {
			mode : mode,
			url : url,
			data : data
		};

		if (doneCallback) Common.commonInputModalCallback = doneCallback;

		showLoader();
		$.ajax({
			type: 'POST',
			url: "/common/form_input_modal.php",
			dataType: "html",
			data: dataObj
		}).done(function (response) {
			if(response) {
				$('#common_input_modal_pop').dialog('option', 'title', title);
				$('#common_input_modal_pop').html(response);
				$('#common_input_modal_pop').dialog( "open" );

				Common.initCommonInputModalPop();
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
		}).fail(function(jqXHR, textStatus){
			alert('오류(' + textStatus + ')가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');

		}).always(function(){
			hideLoader();
		});
	},

	closeCommonInputModalPop : function () {
		$("#common_input_modal_pop").dialog( "close" );
	},

	initCommonInputModalPop : function () {
		$(".btn_common_input_modal_cancel").on("click", function(){
			Common.closeCommonInputModalPop();
		});

		//Input 텍스트 에서 엔터 시 자동 적용
		$(".input_common_modal_pop").on("keyup", function(event){
			let keyCode = event.key
			if (keyCode == 13) {
				Common.closeCommonInputModalPop();
				return false;
			}
		});

		$(".btn_common_input_modal_confirm").on("click", function(){
			showLoader();

			$.ajax({
				type: 'POST',
				url: $("#frm_url").val(),
				dataType: "json",
				data: $("form[name='frm_common_modal_pop']").serialize()
			}).done(function (response) {
				if (response.msg) alert(response.msg);
				if (Common.commonInputModalCallback) Common.commonInputModalCallback();
				Common.closeCommonInputModalPop();

			}).fail(function (jqXHR, textStatus) {
				alert('오류(' + textStatus + ')가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}).always(function(){
				hideLoader();
			});
		});
	}
};

// jQueryUI DatePicker - KR language callendar
$.datepicker.regional['kr'] = {
	closeText: '닫기', // 닫기 버튼 텍스트 변경
	currentText: '오늘', // 오늘 텍스트 변경
	dateFormat: 'yy-mm-dd',
	prevText: '이전 달',
	nextText: '다음 달',
	monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
	monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
	dayNames: ['일', '월', '화', '수', '목', '금', '토'],
	dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
	dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
	showMonthAfterYear: true,
	yearSuffix: '년'
};

// Seeting up default language, Korean
$.datepicker.setDefaults($.datepicker.regional['kr']);

//페이지 로딩 후 실행!!
$(function(){

	// 공통 우편번호 찾기 버튼 클릭
	// 해당 객체에 아래 속성 필요
	// data-zipcode-id=""   : 입력 될 우편번호 객체 ID
	// data-addr1-id=""   : 입력 될 주소1 객체 ID
	// data-addr2-id=""   : 포커스 될 주소2 객체 ID
	$("body").on("click", ".btn-address-zipcode", function(){
		var zipcode = $(this).data("zipcode-id");
		var addr1 = $(this).data("addr1-id");
		var addr2 = $(this).data("addr2-id");

		Common.daumZipSearch(zipcode, addr1, addr2);
	});

	//공통 날짜 입력 적용
	$(".jqDate").datepicker({
		beforeShow: function() {
			setTimeout(function(){
				$('.ui-datepicker').css('z-index', 99999999999999);
			}, 0);
		}
	});

	//숫자만 입력 - Dynamic 객체용
	$("body").on("keyup", ".onlyNumberDynamic",function(){
		var num_check=/^[0-9\-]+$/g;
		var v = $(this).val();

		if (!num_check.test(v)) {
			if(v.length <= 1){
				$(this).val("");
			}else{
				//$(this).val(v.substr(0, v.length - 1));
				$(this).val(v.replace(/[^0-9\-]/g,''));
			}
		}
	});

	//아이디 입력 전용
	$('.userID').css('imeMode','disabled').css('text-transform', 'lowercase').keyup(function(event){
		if( $(this).val() != null && $(this).val() != '' ) {
			$(this).val( $(this).val().replace(/[^0-9a-z\-_]/gi,"").toLowerCase());
		}
	});

	//숫자만 입력
	$('.onlyNumber').css('imeMode','disabled').keypress(function(event) {
		if(event.which && (event.which < 48 || event.which > 57) ) {
			event.preventDefault();
		}
	}).keyup(function(event){
		if( $(this).val() != null && $(this).val() != '' ) {
			$(this).val( $(this).val().replace(/[^0-9\-]/g, '') );
		}
	});

	//숫자, 콤마만 입력
	$('.onlyNumberComma').css('imeMode','disabled').keypress(function(event) {
		console.log(event.which);
		if(event.which && (event.witch != 47 && (event.which < 46 || event.which > 57)) ) {
			event.preventDefault();
		}
	}).keyup(function(event){
		if( $(this).val() != null && $(this).val() != '' ) {
			$(this).val( $(this).val().replace(/[^0-9\-.]/g, '') );
		}
	});

	//숫자 + 영어만 입력
	$('.onlyNumberAlphabet').css('imeMode','disabled').keypress(function(event) {
		if(event.which && (event.which >= 37 && event.which <= 40) ) {
			event.preventDefault();
		}
	}).keyup(function(event){
		if( $(this).val() != null && $(this).val() != '' ) {
			$(this).val( $(this).val().replace(/[^a-z0-9]/gi, '') );
		}
	});

	//숫자 + 영어 ,  - ,  _ 입력
	$('.onlyNumberAlphabet2').css('imeMode','disabled').keypress(function(event) {
		console.log(event.which);
		if(event.which && (event.which >= 37 && event.which <= 40) && event.which != 45 && event.which != 95 ) {
			event.preventDefault();
		}
	}).keyup(function(event){
		if( $(this).val() != null && $(this).val() != '' ) {
			$(this).val( $(this).val().replace(/[^a-z0-9\-_]/gi, '') );
		}
	});

	// 한글입력막기 스크립트
	$("body").css('imeMode','disabled').on("keyup", ".not-kor", function(e) {
		$(this).val( $(this).val().replace( /[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/g, '' ) );
	});


	//전화번호만 입력
	$('.onlyNumberPhone').css('imeMode','disabled').keypress(function(event) {
		if((event.which && (event.which < 48 || event.which > 57) && event.which != 45) ) {
			event.preventDefault();
		}
	}).keyup(function(event){
		if( $(this).val() != null && $(this).val() != '' ) {
			$(this).val( $(this).val().replace(/[^0-9\-]/g, '') );
		}
	});

	//이메일 도메인 셀렉트 박스 바인딩
	$(".email_domain_select").on("change", function(e){
		console.log(e);
		$(this).prev().val($(this).val());

	});
});

Array.prototype.arrayRemove = function() {
	var what, a = arguments, L = a.length, ax;
	while (L && this.length) {
		what = a[--L];
		while ((ax = this.indexOf(what)) !== -1) {
			this.splice(ax, 1);
		}
	}
	return this;
};

if (!Array.prototype.filter) {
	Array.prototype.filter = function(fun/*, thisArg*/) {
		'use strict';

		if (this === void 0 || this === null) {
			throw new TypeError();
		}

		var t = Object(this);
		var len = t.length >>> 0;
		if (typeof fun !== 'function') {
			throw new TypeError();
		}

		var res = [];
		var thisArg = arguments.length >= 2 ? arguments[1] : void 0;
		for (var i = 0; i < len; i++) {
			if (i in t) {
				var val = t[i];

				// NOTE: Technically this should Object.defineProperty at
				//       the next index, as push can be affected by
				//       properties on Object.prototype and Array.prototype.
				//       But that method's new, and collisions should be
				//       rare, so use the more-compatible alternative.
				if (fun.call(thisArg, val, i, t)) {
					res.push(val);
				}
			}
		}

		return res;
	};
}

Object.size = function(obj) {
	var size = 0, key;
	for (key in obj) {
		if (obj.hasOwnProperty(key)) size++;
	}
	return size;
};

function jqGridFormatInteger(cellvalue, options, rowobject){
	var val = cellvalue;
	if(typeof val != "undefined" && val != null && val != "undefined") {
		return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
	}else{
		if(typeof val != "undefined" || val == "undefined"){
			return "";
		}else {
			return val;
		}
	}
}