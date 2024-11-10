/*
 * 벤더사 등급관리 js
 */
var VendorGrade = (function() {
	var root = this;

	var init = function() {
	};

	//벤더사 등급 관리 리스트 초기화
	var VendorGradeListInit = function(){

		//저장 버튼 클릭 이벤트
		$(".btn-vendor-grade-save").on("click", function(){
			VendorGradeSave($(this));
		});
	};

	//벤더사 등급 "저장" 버튼 클릭
	var VendorGradeSave = function($obj){
		var idx = $obj.data("idx");
		var $tr = $obj.parent().parent();
		var $name = $tr.find("input[name='vendor_grade_name']");
		var $discount = $tr.find("input[name='vendor_grade_discount']");
		var $etc = $tr.find("input[name='vendor_grade_etc']");

		if($name.val().trim().length == 0 || $name.val().trim().length > 20)
		{
			alert("등급명을 입력해주세요.\n등급명은 20자 이하만 가능합니다.");
			$name.focus();
			return false;
		}

		if(!$.isNumeric($discount.val().trim()) || $discount.val().trim().length > 3)
		{
			alert("할인율은 숫자만 입력가능합니다.");
			$discount.focus();
			return false;
		}

		if(confirm('등급을 저장하시겠습니까?')){
			var p_url = "vendor_grade_proc.php" +
				"";
			var dataObj = new Object();
			dataObj.mode = "save";
			dataObj.vendor_grade_idx = idx;
			dataObj.vendor_grade_name = $name.val().trim();
			dataObj.vendor_grade_discount = $discount.val().trim();
			dataObj.vendor_grade_etc = $etc.val().trim();

			showLoader();
			$.ajax({
				type: 'GET',
				url: p_url,
				dataType: "json",
				data: dataObj
			}).done(function (response) {
				if(response.result)
				{
					alert("저장 되었습니다.");
				}else{
					alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				}
				hideLoader();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoader();
			});
		}
	};

	return {
		VendorGradeListInit : VendorGradeListInit
	}
})();