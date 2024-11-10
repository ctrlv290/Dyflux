/*
 * 모바일 - index 관련 js
 */
var Index = (function() {
	var root = this;

	var init = function(){

		$("#btn-login").on("click", function(){
			var id = $("#id").val();
			var password = $("#password").val();
			var save_id = ($("#save-id").is(":checked")) ? "Y" : "N";

			showLoaderM();

			if($.trim(id) == "" || $.trim(password) == ""){
				alert("아이디와 비밀번호를 입력해주세요.");
				hideLoaderM();
				return;
			}

			var p_url = "proc/_login_check_ajax.php";
			var dataObj = new Object();
			dataObj.id = id;
			dataObj.pw = password;
			dataObj.save_id = save_id;
			$.ajax({
				type: 'POST',
				url: p_url,
				dataType: "json",
				data: dataObj,
				traditional: true
			}).done(function (response) {
				if(response.result)
				{
					location.replace("main.php");
				}else{
					alert(response.msg);
				}
				hideLoaderM();
			}).fail(function(jqXHR, textStatus){
				alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
				hideLoaderM();
			});

		});

	};

	return {
		init: init,
	}
})();
$(function(){
	Index.init();
});