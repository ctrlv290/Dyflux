var SMS = (function() {
	var root = this;

	var init = function () {
	};

	/**
	 * 확장주문검색 페이지 초기화
	 * @constructor
	 */
	var PersonalSendListInit = function(){
		//날짜 검색 초기화 및 프리셋
		Common.setDateTimePreSetSelectbox("period_preset_select", 'period_preset_start_input', 'period_preset_end_input', 'period_preset_start_time_input', 'period_preset_end_time_input',  "8");

		//발송 버튼 바인딩
		$('#send_btn').on("click", function(){

			if($('#sms_con').val() == '') {
				alert('전송할 메세지를 입력하세요');
				return false;
			}

			if($('#reciver_phone').val() == '') {
				alert('받는 사람을 선택하거나 입력해주세요');
				return false;
			}

			var sms_tit = $('#sms_title').val();
			var sms_msg = $('#sms_con').val();
			var rphone = $('#reciver_phone').val();
			var mecro_idx = $('#mecro_idx').val();
			var sms_sender = $('#sms_sender option:selected').val();

			var order_idx = $("#order_idx").val();
			var order_pack_idx = $("#order_pack_idx").val();

			if(sms_sender == ""){
				alert('발신자를 선택해주세요.');
				return false;
			}

			if(confirm('메세지를 전송하시겠습니까?')) {
				showLoader();
				$.ajax({
					type: 'POST',
					url: 'sms_send_proc.php',
					dataType: "json",
					data: {'mode': 'PERSONAL', 'rphone' : rphone, 'sms_msg' : sms_msg, 'sms_tit' : sms_tit, 'mecro_idx' : mecro_idx,'sms_sender' : sms_sender, 'order_idx' : order_idx, 'order_pack_idx' : order_pack_idx}
				}).done(function (response) {
					if (response.result) {
						alert('발송 되었습니다');
						location.reload();
					} else {
						alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					}
					hideLoader();
				}).fail(function (jqXHR, textStatus) {
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
					hideLoader();
				});
			}
		});

		//툴팁 세팅
		$( ".toolTipTrigger" ).tooltip({
			show: {
				effect: "slideDown",
				delay: 250
			}
		});

		//글자수 바인딩
		$('#sms_con').keyup(function(){
			bytesHandler(this);
		});


		//SMS 매크로 모달팝업 세팅
		$( "#modal_sms_mecro_list" ).dialog({
			width: 750,
			autoOpen: false,
			modal: true,
			classes: {
				"ui-dialog-titlebar": "blue-theme"
			},
			open : function(event, ui) { windowScrollHide() },
			close : function(event, ui) { windowScrollShow() },
		});

		$('#sms_mecro').on("click", function() {
			var p_url = "sms_mecro_list.php";
			var dataObj = new Object();
			dataObj.mode = $('#lmode').val();

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					$("#modal_sms_mecro_list").html(response);
					$("#modal_sms_mecro_list").dialog( "open" );
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		});

		$('#sms_mecro_save').on("click", function(){
			var con = $('#sms_con').val();

			if(con == '') {
				alert('저장할 내용을 입력해주세요');
				return false;
			}
			var p_url = "sms_send_proc.php";
			var dataObj = new Object();
			dataObj.mode = 'MECRO_INSERT';
			dataObj.sms_msg = con;

			showLoader();
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "html",
				data: dataObj
			}).done(function (response) {
				if(response)
				{
					alert('처리 되었습니다.');
					location.reload();
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});

		});

		PersonalSendListGridInit();
	};

	/**
	 *
	 * @constructor
	 */
	var PersonalSendListGridInit = function(){

		// 목록 바인딩 jqGrid
		$("#grid_list").jqGrid({
			url: './sms_personal_send_grid.php',
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
			colModel: [
				{label: '일자', name: 'sms_send_date', index: 'sms_send_date', width: 100, sortable: false, is_use : true},
				{label: '시간', name: 'sms_send_time', index: 'sms_send_time', width: 100, sortable: false, is_use : true},
				{label: '작업자', name: 'member_id', index: 'member_id', width: 100, sortable: false, is_use : true},
				{label: '발신번호', name: 'sms_send_num', index: 'sms_send_num', width: 120, sortable: false, is_use : true},
				{label: '수신번호', name: 'sms_receive_num', index: 'sms_receive_num', width: 120, sortable: false, is_use : true},
				{label: '내용', name: 'sms_msg', index: 'sms_msg', width: 100, sortable: false, is_use : true, align: 'left'},
				{label: '번호', name: 'member_idx', index: 'member_idx', width: 100, sortable: false, is_use : true, hidden:true},
			],
			rowNum: Common.jsSiteConfig.jqGridRowList[1],
			rowList: Common.jsSiteConfig.jqGridRowList,
			pager: '#grid_pager',
			sortname: '',
			sortorder: "desc",
			viewrecords: true,
			autowidth: true,
			rownumbers: true,
			shrinkToFit: true,
			height: Common.jsSiteConfig.jqGridDefaultHeight,
			loadComplete: function(){
				//Grid 사이즈 reSize
				Common.jqGridResize("#grid_list");

				//컬럼 사이즈 복구
				Common.getGridColumnSizeFromStorage("personal_send_list", $("#grid_list"));
			},
			resizeStop: function(newwidth, index){
				//컬럼 사이즈 저장
				var col_ary = $("#grid_list").jqGrid('getGridParam', 'colModel');
				Common.setGridColumnSizeToStorage(col_ary, "personal_send_list");
			},
			onSelectRow: function(rowid, status){
				var rowData = $("#grid_list").getRowData(rowid);
				var content = rowData.sms_msg;
				var rphone = rowData.sms_receive_num;
				$('#sms_con').val('');
				$('#sms_con').val(content);
				$('#reciver_phone').val('');
				$('#reciver_phone').val(rphone);
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
				OrderSearchListSearch();
			}
		});

		//검색 버튼 클릭 이벤트
		$("#btn_searchBar").on("click", function(){
			OrderSearchListSearch();
		});
	};

	/**
	 * 확장주문검색 목록/검색
	 * @constructor
	 */
	var OrderSearchListSearch = function(){
		$("#grid_list").setGridParam({
			datatype: "json",
			page: 1,
			postData:{
				param: $("#searchForm").serialize()
			}
		}).trigger("reloadGrid");
	};

	/**
	 *
	 * @param str
	 * @returns {number}
	 */
	var getTextLength = function(str) {
		var len = 0;

		for (var i = 0; i < str.length; i++) {
			if (escape(str.charAt(i)).length == 6) {
				len++;
			}
			len++;
		}
		return len;
	};

	/**
	 *
	 * @param obj
	 * @returns {boolean}
	 */
	var bytesHandler = function(obj){
		var text = $(obj).val();
		var r_lng = getTextLength(text);

		if(r_lng >= 90) {
			$('#m_cnt').text('2000');
		}else {
			$('#m_cnt').text('90');
		}

		if(r_lng >= 2000) {
			alert('LMS는 2000 바이트까지 전송가능합니다');
			var str = cut_str(obj, 2000);
			$(obj).val(str);
			$('#r_cnt').text(r_lng);
			return false;
		}
		$('#r_cnt').text(r_lng);
	};

	/**
	 *
	 * @param obj
	 * @param lng
	 * @returns {jQuery|string}
	 */
	var cut_str = function(obj, lng){
		var text = $(obj).val();
		var leng = text.length;
		while(getTextLength(text) > lng){
			leng--;
			text = text.substring(0, leng);
		}
		return text;
	};



	return {
		PersonalSendListInit: PersonalSendListInit,
	}
})();