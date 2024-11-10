var menu_dev = (function() {
	var root = this;

	var init = function() {
	};

	var MenuListInit = function() {
		$(".btn-menu-add").on("click", function () {
			addMenuNewForm($(this), null);
		});

		$(".btn-menu-modify").on("click", function () {
			modMenuForm($(this));
		});

		$(".btn-menu-delete").on("click", function () {
			delMenu($(this));
		});

		$(".btn-menu-move").on("click", function () {
			moveMenu($(this));
		});

		$("body").on("click", ".btn-menu-new-save", function () {
			checkNewMenuAdd();
		});

		$("body").on("click", ".btn-menu-new-cancel", function () {
			removeMenuNewForm();
		});

		$("body").on("change", "select[name='target']", function () {
			if ($(this).val() == "popup") {
				$(".target_popup_size").removeClass("dis_none");
			} else {
				$(".target_popup_size").addClass("dis_none");
			}
		});
	};

	var removeMenuNewForm = function()
	{
		$("tr").removeClass("dis_none");
		$("tr.active_tr").remove();
	};

	var checkNewMenuAdd = function()
	{
		if($(".active_tr input[name='name']").val().trim() == "")
		{
			alert("메뉴명을 입력해주세요.");
			$(".active_tr input[name='name']").eq(0).focus();
			return;
		}

		// if($(".active_tr input[name='url']").val().trim() == "")
		// {
		// 	alert("URL을 입력해주세요.");
		// 	$(".active_tr input[name='url']").eq(0).focus();
		// 	return;
		// }

		if($(".active_tr select[name='target']").val().trim() == "popup")
		{
			if($(".active_tr input[name='popup_x']").val().trim() == "")
			{
				alert("팝업창의 가로 크기를 입력해주세요.");
				$(".active_tr input[name='popup_x']").eq(0).focus();
				return;
			}
			if($(".active_tr input[name='popup_y']").val().trim() == "")
			{
				alert("팝업창의 가로 크기를 입력해주세요.");
				$(".active_tr input[name='popup_y']").eq(0).focus();
				return;
			}
		}

		showLoader();
		var p_url = "menu_proc_ajax.php";
		var fileData = new FormData();
		fileData.append('mode', "add");
		fileData.append('parent_idx', $(".active_tr input[name='parent_idx']").val().trim());
		fileData.append('name', $(".active_tr input[name='name']").val().trim());
		fileData.append('name_short', $(".active_tr input[name='name_short']").val().trim());
		fileData.append('url', $(".active_tr input[name='url']").val().trim());
		fileData.append('target', $(".active_tr select[name='target']").val().trim());
		fileData.append('popup_x', $(".active_tr input[name='popup_x']").val().trim());
		fileData.append('popup_y', $(".active_tr input[name='popup_y']").val().trim());
		fileData.append('css_class', $(".active_tr input[name='css_class']").val().trim());
		fileData.append('is_hidden', $(".active_tr select[name='is_hidden']").val().trim());
		fileData.append('is_use', $(".active_tr select[name='is_use']").val().trim());

		var data = new Object();
		data.idx = $(".active_tr input[name='idx']").val().trim();
		data.parent_idx = $(".active_tr input[name='parent_idx']").val().trim();
		data.name = $(".active_tr input[name='name']").val().trim();
		data.name_short = $(".active_tr input[name='name_short']").val().trim();
		data.url = $(".active_tr input[name='url']").val().trim();
		data.target = $(".active_tr select[name='target']").val().trim();
		data.popup_x = $(".active_tr input[name='popup_x']").val().trim();
		data.popup_y = $(".active_tr input[name='popup_y']").val().trim();
		data.css_class = $(".active_tr input[name='css_class']").val().trim();
		data.is_hidden = $(".active_tr select[name='is_hidden']").val().trim();
		data.is_use = $(".active_tr select[name='is_use']").val().trim();

		data.mode = (data.idx == "") ? "add" : "mod";
		var dataJson = JSON.stringify(data);
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType : "json",
			data: data
		}).done(function (response) {
			if(response.result)
			{
				location.reload();
			}else{
				alert(response.msg);
			}
			hideLoader();
		});
	};

	var addMenuNewForm = function($obj, menuValObj)
	{
		var isModify = false;
		var idx = $obj.data("idx");
		var depth = $obj.data("depth");
		//console.log(idx, depth);
		var colspan = 4;
		colspan = colspan - depth;
		removeMenuNewForm();

		var _name = "", _name_short = "", _url = "", _target = "", _popup_size = "", _css_class = "", _is_hidden = "", _is_use = "";
		var _popup_size_x = "", _popup_size_y = "";
		var _mod_idx = "";
		if(menuValObj != null)
		{
			_mod_idx = idx;
			isModify = true;

			_name = menuValObj.name;
			_name_short = menuValObj.name_short;
			_url = menuValObj.url;
			_target = menuValObj.target;
			_popup_size = menuValObj.popup_size;
			console.log(_popup_size);
			if(_popup_size != "" && _popup_size != null)
			{
				var _popup_size_ary = _popup_size.split("|");
				_popup_size_x = _popup_size_ary[0];
				_popup_size_y = _popup_size_ary[1];
			}
			_css_class = menuValObj.css_class;
			_is_hidden = menuValObj.is_hidden;
			_is_use = menuValObj.is_use;
		}

		var addForm = '';
		addForm += '<tr class="active_tr">';
		addForm += '<td>'+_mod_idx+'<input type="hidden" name="idx" value="' + _mod_idx + '"/><input type="hidden" name="parent_idx" value="' + idx + '"/></td>';
		addForm += '<td></td>';
		for(var i = 1;i<= depth;i++)
		{
			addForm += '<td class="blank_td">';
			if(i == depth) addForm += '└';
			addForm += '</td>';
		}
		addForm += '<td colspan="'+colspan+'" class="text_left">';
		addForm += '<input type="text" name="name" class="w150px" id="form_name" placeholder="메뉴명" maxlength="50" value="'+_name+'" /> ';
		addForm += '<input type="text" name="name_short" class="w150px" placeholder="짧은 메뉴명" maxlength="50" value="'+_name_short+'" /> ';
		addForm += '<input type="text" name="url" class="w300px" placeholder="URL" maxlength="200" value="'+_url+'" />';
		addForm += '</td>';
		addForm += '<td>';
		addForm += '<select name="target"><option>_self</option><option>_blank</option><option value="popup">팝업</option></select>';
		addForm += '<div class="target_popup_size dis_none"><input type="text" name="popup_x" class="w40px onlyNumberDynamic" placeholder="가로" maxlength="4" value="'+_popup_size_x+'" /> x <input type="text" name="popup_y" class="w40px onlyNumberDynamic" placeholder="세로" maxlength="4" value="'+_popup_size_y+'" /></div>';
		addForm += '</td>';
		addForm += '<td><input type="text" name="css_class" class="w80px" placeholder="CSS" value="'+_css_class+'" /></td>';
		addForm += '<td><select name="is_hidden"><option>N</option><option>Y</option></select></td>';
		addForm += '<td><select name="is_use"><option>Y</option><option>N</option></select></td>';
		addForm += '<td>';
		addForm += '<a href="javascript:;" class="large_btn blue_btn btn-menu-new-save">저장</a> ';
		addForm += '<a href="javascript:;" class="large_btn red_btn btn-menu-new-cancel">취소</a>';
		addForm += '</td>';
		addForm += '</tr>';

		//console.log(isModify);
		if(!isModify) {
			if (idx == "0") {
				$obj.parent().parent().parent().parent().find("tbody tr:last").after(addForm);
			} else {
				$obj.parent().parent().after(addForm);
			}
		}else{
			$obj.parent().parent().after(addForm);
			$obj.parent().parent().addClass("dis_none");
			setTimeout(function(){
				$("select[name='target']").trigger("change");
			}, 300);

			$(".active_tr select[name='target']").val(_target);
			$(".active_tr select[name='is_hidden']").val(_is_hidden);
			$(".active_tr select[name='is_use']").val(_is_use);
		}

		$("#form_name").focus();
	};

	var modMenuForm = function($obj)
	{
		var idx = $obj.data("idx");
		var data = new Object();
		data.mode = "get";
		data.idx = idx;

		var p_url = "menu_proc_ajax.php";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType : "json",
			data: data
		}).done(function (response) {
			if(response.result)
			{
				addMenuNewForm($obj, response.menuVal);
			}else{
				alert(response.msg);
			}
			hideLoader();
		});
	};

	var delMenu = function($obj)
	{
		showLoader();
		if(confirm('삭제하시겠습니까?'))
		{
			var idx = $obj.data("idx");
			var data = new Object();
			data.mode = "del";
			data.idx = idx;

			var p_url = "menu_proc_ajax.php";
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType : "json",
				data: data
			}).done(function (response) {
				if(response.result)
				{
					location.reload();
				}else{
					alert(response.msg);
				}

			});
		}
		hideLoader();
	};

	var moveMenu = function($obj)
	{
		var idx = $obj.data("idx");
		var dir = $obj.data("dir");
		var data = new Object();
		data.mode = "move";
		data.idx = idx;
		data.dir = dir;

		var p_url = "menu_proc_ajax.php";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType : "json",
			data: data
		}).done(function (response) {
			if(response.result)
			{
				location.reload();
			}else{
				alert(response.msg);
			}

		});
	};

	return {
		MenuListInit : MenuListInit
	}
})();