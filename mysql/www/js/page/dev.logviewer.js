var LogViewer = (function() {
	var root = this;

	var Init = function() {

		var box_top = $(".line_box").offset().top;
		var wH = $(window).height();

		$(".line_box").height(wH - box_top - 80);
		$(".date_wrap").scrollbar();

		getLogDateList();
	};

	var getLogDateList = function(){
		var p_url = "./log_viewer_date_ajax.php";
		var dataObj = new Object();
		dataObj.mode = $("#mode").val();
		showLoader();
		$.ajax({
			type: 'GET',
			url: p_url,
			dataType: "json",
			data: dataObj
		}).done(function (response) {
			if(response.result){
				var data = response.data;
				$(".line_box").empty();
				$.each(data, function(i, o){
					$(".line_box").append('<li><a href="javascript:;" class="link date_link" data-file="'+o.file+'">'+o.name+'</a></li>');
				});
			}

			$(".date_link").on("click", function(){
				var filename = $(this).data("file");
				getLogDetail(filename);
			});


			hideLoader();

		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

	var getLogDetail = function(filename){
		var p_url = "./log_viewer_detail.php";
		var dataObj = new Object();
		dataObj.filename = filename;
		showLoader();
		$.ajax({
			type: 'POST',
			url: p_url,
			dataType: "html",
			data: dataObj
		}).done(function (response) {

			$(".log_list").remove();
			$(".wrap_log").append(response);

			$(".json").each(function(i, o){
				var jsonStr = $(o).html();
				try {
					if (jsonStr != "") {
						var json = JSON.parse(jsonStr);
						$(this).html('<pre>' + prettyPrintJson.toHtml(json) + '</pre>');
					}
				}catch(e){
					console.log(e);
				}
			});

			$(".log_detail").on("click", function(){
				var $json = $(this).parent().find(".json");
				if($json.hasClass("dis_none")){
					$json.removeClass("dis_none");
					$(this).addClass("show");
				}else{
					$json.addClass("dis_none");
					$(this).removeClass("show");
				}

			});

			hideLoader();

		}).fail(function(jqXHR, textStatus){
			alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
			hideLoader();
		});
	};

return {
	Init : Init
}
})();