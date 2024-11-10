/*
 * 배너관리 관련 js
 */
var Banner = (function() {
	var root = this;

	var init = function() {
	};

	var BannerListInit = function(){
		lightbox.option({
			'resizeDuration': 100,
			'fadeDuration': 200,
			'imageFadeDuration': 200,
			'albumLabel': "배너이미지 %1/%2",
		});


		$(".btn-banner-delete").on("click", function(){

			if(confirm('삭제하시겠습니까?')){
				if($(this).data("type") == "main") {
					location.href = "banner_proc.php?mode=del&banner_idx=" + $(this).data("idx");
				}else if($(this).data("type") == "home") {
					location.href = "banner_home_proc.php?mode=del&banner_idx=" + $(this).data("idx");
				}
			}

		});

	};

	var BannerWriteInit = function(){
		lightbox.option({
			'resizeDuration': 100,
			'fadeDuration': 200,
			'imageFadeDuration': 200,
			'albumLabel': "배너이미지 %1/%2",
		});

		$("#btn-save").on("click", function(){
			$("#dyForm").submit();
		});

		$("input[name='banner_use_period']").on("click", function(){

			if($("input[name='banner_use_period']:checked").val() == "Y"){
				$(".period_tr").show();
			}else{
				$(".period_tr").hide();
			}

		});
		//$("input[name='banner_use_period']").eq(0).trigger("click");

		if($("input[name='banner_use_period']:checked").val() == "Y"){
			$(".period_tr").show();
		}else{
			$(".period_tr").hide();
		}

		$("form[name='dyForm']").submit(function(){
			var returnType = false;        // "" or false;
			var valForm = new FormValidation();
			var objForm = this;

			try{
				if($(this).hasClass("add")) {
					if (!valForm.chkValue(objForm.banner_image, "이미지를 선택해주세요.", 2, 40, null)) return returnType;
				}

				if(objForm.banner_use_period.value == "Y"){
					if (!valForm.chkValue(objForm.banner_period_start, "배너기간(시작일)을 정확히 입력해주세요.", 10, 10, null)) return returnType;
					if (!valForm.chkValue(objForm.banner_period_end, "배너기간(종료일)을 정확히 입력해주세요.", 10, 10, null)) return returnType;
				}

				if (!valForm.chkValue(objForm.banner_sort, "배너순서를 정확히 입력해주세요.", 1, 3, null)) return returnType;

				if($(this).hasClass("add")) {
					var ext = objForm.banner_image.value;
					ext = ext.slice(ext.indexOf(".") + 1).toLowerCase(); //파일 확장자를 잘라내고, 비교를 위해 소문자로 만듭니다.
					if (ext != "jpg" && ext != "png" && ext != "gif") { //확장자를 확인합니다.
						alert('배너 이미지 파일(jpg, png, gif)만 등록 가능합니다.');
						return returnType;
					}
				}

				//this.action = "banner_proc.php";
				$("#btn-save").attr("disabled", true);

			}catch(e){
				alert(e);
				return false;
			}
		});
	};

	return {
		BannerListInit: BannerListInit,
		BannerWriteInit: BannerWriteInit,
	}
})();