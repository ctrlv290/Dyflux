$(function(){

	$("form[name='login_form']").on("submit", function(e){
		e.preventDefault ? e.preventDefault() : (e.returnValue = false);
	});

	//
	$("input[name='member_id']").on("keyup", function(e){
		var keyCode = (event.keyCode ? event.keyCode : event.which);
		if (keyCode == 13) {
			$("input[name='member_pw']").focus();
		}
	});

	//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
	$("input.enterDoSearch").on("keyup", function(e){
		var keyCode = (event.keyCode ? event.keyCode : event.which);
		if (keyCode == 13) {
			event.preventDefault();
			loginCheck();
		}
	});

	$(".btn-login").on("click", function(){
		loginCheck();
	});

	//Input 텍스트 에서 엔터 시 자동 검색 (class = enterDoSearch)
	$("input.enterDoSearch").on("keyup", function(e){
		var keyCode = (event.keyCode ? event.keyCode : event.which);
		if (keyCode == 13) {
			event.preventDefault();
			loginCheck
		}
	});

	$('.visual_slide').slick({
		dots: true,
		arrows: true,
		autoplay:true,
		infinite: true,
		fade: true,
		speed: 1000
	});

	$(".btn-notice-more").on("click", function(){
		$(".notice_iframe").attr("src", "notice_list.php");
		$(".notice_wrap").addClass("show");
	});
	$(".btn-notice-close").on("click", function(){
		$(".notice_wrap").removeClass("show");
		$(".notice_iframe").attr("src", "about:_blank");
	});

	$(".btn-notice-view").on("click", function(){
		$(".notice_iframe").attr("src", "notice_view.php?bbs_idx="+$(this).data("idx"));
		$(".notice_wrap").addClass("show");
	});

	// $(".btn-notice-view").on("click", function(){
	// 	var idx = $(this).data("idx");
	// 	$(".notice_iframe").attr("src", "notice_view.php?bbs_idx="+idx);
	// 	$(".notice_wrap").addClass("show");
	// });
});

var loginCheck = function(){

	var member_id = $("input[name='member_id']").val();
	var member_pw = $("input[name='member_pw']").val();
	var save_id = ($("#save_id").is(":checked")) ? "Y" : "N";

	if(member_id == "" || member_pw == "")
	{
		alert("로그인ID 또는 비밀번호를 정확하게 입력해주세요.");
		return false;
	}else{
		var p_url = "/proc/_login_check_ajax.php";

		returnUrl = (returnUrl == "") ? "/main.php" : returnUrl;
		var dataObj = new Object();
		dataObj.member_id = member_id;
		dataObj.member_pw = member_pw;
		dataObj.save_id = save_id;
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			console.log(response);

			if(response.result)
			{
				location.href = returnUrl;
			}else{
				alert(response.msg);
			}

		}).fail(function(jqXHR, textStatus){
			//console.log(jqXHR, textStatus);
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
		});
	}
};