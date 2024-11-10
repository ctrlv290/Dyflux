$(document).ready(function(){

	if($.cookie("gnb_menu_hide") == "Y")
	{
		$('.wrap').addClass("menu_hide");
	}

	$('.hide_btn').click(function(){
		$('.wrap').toggleClass('menu_hide');
		if($(".wrap").hasClass("menu_hide"))
		{
			$.cookie("gnb_menu_hide", "Y");
		}else{
			$.cookie("gnb_menu_hide", "N");
		}
		$(window).trigger("resize");
	});

	$('.find_hide_btn').click(function(){
		$('.find_wrap').toggleClass('active')
	});

	$(".nav .gnb>li>a").on("click", function(){
		$(".nav .gnb>li>a").removeClass("on");
		$(this).addClass("on");
	});

	if(!$("body>.wrap").hasClass("popup")) {
		$(".scrollbar-macosx").scrollbar();
	}

	$(".btn-fav").on("click", function(){
		setFav($(this));
	});

	if($("body>.wrap").hasClass("popup")){
		$(".btn-fav").hide();
	}
});


function showLoader()
{
	$(".loading_dimmer").show();
}
function hideLoader()
{
	$(".loading_dimmer").hide();
}

function windowScrollShow(){
	$("html").removeClass("no-scroll")
}
function windowScrollHide(){
	$("html").addClass("no-scroll")
}



var setFav = function($obj){

	var menu_idx = $obj.data("idx");
	var p_url = "/proc/_fav_proc.php";


	if($obj.hasClass("fav_menu"))
	{
		//제거
		var mode = "remove_fav";
	}else{
		//추가
		var mode = "add_fav";
	}

	var dataObj = new Object();
	dataObj.mode = mode;
	dataObj.menu_idx = menu_idx;
	$.ajax({
		type: 'POST',
		url: p_url,
		dataType: "json",
		data: dataObj
	}).done(function (response) {

		if(response.result)
		{
			if(mode == "add_fav") {
				$obj.addClass("fav_menu");
				$obj.find("i").removeClass("far").addClass("fas");
			}else{
				$obj.removeClass("fav_menu");
				$obj.find("i").removeClass("fas").addClass("far");
			}
		}else{
			alert(response.msg);
		}

	}).fail(function(jqXHR, textStatus){
		//console.log(jqXHR, textStatus);
		alert('오류가 발생하였습니다. 잠시 후 다시 시도하여 주세요.');
	});

};

/*
jqgrid 검색결과 없음 공통 적용
 */
if(jqgridDefaultSetting) {
	$.extend($.jgrid.defaults, {
		responsive: true,
		ajaxGridOptions: {
			beforeSend: function (jqXHR, settings) {
				$(".ui-jqgrid .loading").show();
			},
			complete: function (response) {
				if (response.status == 200) {
					json = response.responseJSON;
					if (json.records == 0) {
						var nodata_html = '<div class="no-data">검색결과가 없습니다.</div>';
						$(".ui-jqgrid-bdiv").eq(0).append(nodata_html);
					}else{

						$(".ui-jqgrid-bdiv .no-data").remove();
					}

					$(".ui-jqgrid .loading").hide();
				}
			}
		},
		autoencode: true
	});
}