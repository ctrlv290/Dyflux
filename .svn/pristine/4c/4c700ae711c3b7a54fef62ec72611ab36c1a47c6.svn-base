/*
 * 카테고리 관리 js
 */
var Category = (function() {
	var root = this;

	var init = function() {
	};


	//카테고리 목록 초기화
	//각 버튼들 바인딩
	var CategoryListInit = function() {
		$(".btn-category-add").on("click", function () {
			addCategoryNewForm($(this), null);
		});

		$(".btn-category-modify").on("click", function () {
			modCategoryForm($(this));
		});

		$(".btn-category-delete").on("click", function () {
			delCategory($(this));
		});

		$(".btn-category-move").on("click", function () {
			moveCategory($(this));
		});

		$("body").on("click", ".btn-category-new-save", function () {
			checkNewCategoryAdd();
		});

		$("body").on("click", ".btn-category-new-cancel", function () {
			removeCategoryNewForm();
		});

		$("body").on("change", "select[name='target']", function () {
			//console.log($(this).val());
			if ($(this).val() == "popup") {
				$(".target_popup_size").show();
			} else {
				$(".target_popup_size").hide();
			}
		});
	};

	//카테고리 등록/수정 폼 삭제
	var removeCategoryNewForm = function()
	{
		$("tr").removeClass("dis_none");
		$("tr.active_tr").remove();
	};


	//카테고리 등록/수정 실행
	var checkNewCategoryAdd = function()
	{
		if($(".active_tr input[name='name']").val().trim() == "")
		{
			alert("카테고리명을 입력해주세요.");
			$(".active_tr input[name='name']").eq(0).focus();
			return;
		}

		showLoader();
		var p_url = "category_proc_ajax.php";
		var fileData = new FormData();
		fileData.append('mode', "add");
		fileData.append('parent_idx', $(".active_tr input[name='parent_idx']").val().trim());
		fileData.append('name', $(".active_tr input[name='name']").val().trim());
		fileData.append('is_use', $(".active_tr select[name='is_use']").val().trim());

		var data = new Object();
		data.idx = $(".active_tr input[name='idx']").val().trim();
		data.parent_idx = $(".active_tr input[name='parent_idx']").val().trim();
		data.name = $(".active_tr input[name='name']").val().trim();
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
				opener.Category.CategoryListReload();
			}else{
				alert(response.msg);
			}
			hideLoader();
		});
	};

	//카테고리 등록/수정 폼 생성
	var addCategoryNewForm = function($obj, categoryValObj) {
		var isModify = false;
		var idx = $obj.data("idx");
		var depth = $obj.data("depth");
		//console.log(idx, depth);
		var colspan = 3;
		colspan = colspan - depth;
		removeCategoryNewForm();

		var _name = "", _name_short = "", _url = "", _target = "", _popup_size = "", _css_class = "", _is_hidden = "", _is_use = "";
		var _popup_size_x = "", _popup_size_y = "";
		var _mod_idx = "";
		if(categoryValObj != null)
		{
			_mod_idx = idx;
			isModify = true;

			_name = categoryValObj.name;
			_name_short = categoryValObj.name_short;
			_url = categoryValObj.url;
			_target = categoryValObj.target;
			_popup_size = categoryValObj.popup_size;
			if(_popup_size != "" && _popup_size != null)
			{
				_popup_size_x = _popup_size.split("|")[0];
				_popup_size_y = _popup_size.split("|")[1];
			}
			_css_class = categoryValObj.css_class;
			_is_hidden = categoryValObj.is_hidden;
			_is_use = categoryValObj.is_use;
		}

		var addForm = '';
		addForm += '<tr class="active_tr">';
		addForm += '<td>'+_mod_idx+'<input type="hidden" name="idx" value="' + _mod_idx + '"/><input type="hidden" name="parent_idx" value="' + idx + '"/></td>';
		for(var i = 1;i<= depth;i++)
		{
			addForm += '<td class="blank_td">';
			if(i == depth) addForm += '└';
			addForm += '</td>';
		}
		addForm += '<td colspan="'+colspan+'" class="text_left">';
		addForm += '<input type="text" name="name" class="w150px" id="form_name" placeholder="카테고리명" maxlength="50" value="'+_name+'" /> ';
		addForm += '</td>';
		addForm += '<td><select name="is_use"><option>Y</option><option>N</option></select></td>';
		addForm += '<td>';
		addForm += '<a href="javascript:;" class="large_btn blue_btn btn-category-new-save">저장</a> ';
		addForm += '<a href="javascript:;" class="large_btn red_btn btn-category-new-cancel">취소</a>';
		addForm += '</td>';
		addForm += '</tr>';

		//console.log(isModify);
		if(!isModify) {
			if (idx == "0") {
				$obj.parent().parent().parent().parent().find("tbody").append(addForm);
			} else {
				$obj.parent().parent().after(addForm);
			}
		}else{
			$obj.parent().parent().after(addForm);
			$obj.parent().parent().addClass("dis_none");

			$(".active_tr select[name='target']").val(_target);
			$(".active_tr select[name='is_hidden']").val(_is_hidden);
			$(".active_tr select[name='is_use']").val(_is_use);
		}

		$("#form_name").focus();

		//엔터키 적용
		$("input[name='name']").on("keyup", function(e){
			var keyCode = (event.keyCode ? event.keyCode : event.which);
			if (keyCode == 13) {
				event.preventDefault();
				checkNewCategoryAdd();
			}
		});
	};

	//카테고리 수정 폼 생성 시 기존 내용 불러오기
	var modCategoryForm = function($obj)
	{
		var idx = $obj.data("idx");
		var data = new Object();
		data.mode = "get";
		data.idx = idx;

		var p_url = "category_proc_ajax.php";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType : "json",
			data: data
		}).done(function (response) {
			if(response.result)
			{
				addCategoryNewForm($obj, response.categoryVal);
			}else{
				alert(response.msg);
			}
			hideLoader();
		});
	};

	//카테고리 삭제 실행
	var delCategory = function($obj)
	{
		showLoader();
		if(confirm('삭제하시겠습니까?'))
		{
			var idx = $obj.data("idx");
			var data = new Object();
			data.mode = "del";
			data.idx = idx;

			var p_url = "category_proc_ajax.php";
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType : "json",
				data: data
			}).done(function (response) {
				if(response.result)
				{
					location.reload();
					opener.Category.CategoryListReload();
				}else{
					alert(response.msg);
				}

			});
		}
		hideLoader();
	};

	//카테고리 순서 이동
	var moveCategory = function($obj)
	{
		var idx = $obj.data("idx");
		var dir = $obj.data("dir");
		var data = new Object();
		data.mode = "move";
		data.idx = idx;
		data.dir = dir;

		var p_url = "category_proc_ajax.php";
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType : "json",
			data: data
		}).done(function (response) {
			if(response.result)
			{
				location.reload();
				opener.Category.CategoryListReload();
			}else{
				alert(response.msg);
			}

		});
	};

	//카테고리 세트(카테고리1, 카테고리2) 셀렉트 박스 바인딩
	var bindCategorySetSelect = function(target_category_1_class, target_category_2_class){
		bindCategorySelectBox(target_category_1_class, 0, function(){
			if($(target_category_2_class).data("selected") != "") {
				bindCategorySelectBox(target_category_2_class, $(target_category_1_class).val(), null, null);
			}
		}, function(){
			if($(target_category_1_class).val() != "" && $(target_category_1_class).val() != "0") {
				bindCategorySelectBox(target_category_2_class, $(target_category_1_class).val(), null, null);
			}else{
				$(target_category_2_class + " option").remove();
				$(target_category_2_class).append('<option value="" selected="selected">카테고리 선택</option>');
			}
		});
	};

	var CategoryListReload = function(){
			if(window.name == 'product_write'){
			//상품 등록/수정 페이지에서 카테고리 셀렉트 박스 Reload
			bindCategorySetSelect(".product_category_l_idx",".product_category_m_idx");
		}
	};

	//카테고리 셀렉트 박스 바인딩
	var bindCategorySelectBox = function(target_class, parent_category_idx, onComplete, onChange)
	{
		var p_url = "/info/category_proc_ajax.php";
		var dataObj = new Object();
		dataObj.mode = "get_category_list";
		dataObj.idx = parent_category_idx;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result)
			{
				var category_idx = $(target_class).data("selected");
				var $list = response.list;
				$(target_class + " option").remove();
				$(target_class).append('<option value="0">카테고리 선택</option>');
				$.each($list, function(i, v){
					//console.log(i, v);
					if(category_idx == v.category_idx)
					{
						$(target_class).append('<option value="' + v.category_idx + '" selected="selected">' + v.name + '</option>');
						$(target_class).data("selected", "");
					}else {
						$(target_class).append('<option value="' + v.category_idx + '">' + v.name + '</option>');
					}
				});

				if(typeof onComplete == "function") {
					onComplete();
				}
			}else{
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			}
			hideLoader();
		}).fail(function(jqXHR, textStatus){
			//console.log(jqXHR, textStatus);
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
		});



		if(typeof onChange == "function"){
			$(target_class).on("change", function(){
				onChange();
			});
		}
	};

	//카테고리 리스트 팝업
	var CategoryListPopup = function(){
		Common.newWinPopup("/info/category_list.php?mode=pop", 'category_list_pop', 850, 720, 'yes');
	};

	return {
		CategoryListInit : CategoryListInit,
		CategoryListReload : CategoryListReload,
		bindCategorySetSelect : bindCategorySetSelect,
		CategoryListPopup:CategoryListPopup
	}
})();