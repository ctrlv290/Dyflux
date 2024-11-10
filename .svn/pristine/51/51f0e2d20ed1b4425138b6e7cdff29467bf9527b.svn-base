function gnb(){
	$("ul.gnb>li>a").on("click", function(){
		if( $(this).next("ul").css("display")=="none" ){
			$("ul.depth").slideUp();
			$("ul.gnb>li>a").removeClass("on");
			$(this).next(".depth").slideDown();
			$(this).addClass("on");
		}else{
			$(this).next(".depth").slideUp();
			$(this).removeClass("on");
		}
	});
}

function gnb_open(){
	var open = true;
	$(".menu").on("click", function(){
		if( open ){
			open = false;
			$(".wrap_menu").animate({"left":"0"},200)
			$(".wrap_bg").fadeIn();
			$("body").css({"position":"fixed"});
		}else{
			open = true;
			$(".wrap_menu").animate({"left":"-150%"},200)
			$(".wrap_bg").stop(true, true).fadeOut(200);
			$("body").css({"position":"static"});
		}
	});

	$(".wrap_bg").on("click", function(){
		open = true;
		$(".wrap_menu").animate({"left":"-150%"},200)
		$(this).stop(true, true).fadeOut(200);
		$("body").css({"position":"static"});
	});

	$(".close_btn").on("click", function(){
		open = true;
		$(".wrap_menu").animate({"left":"-150%"},200)
		$(this).stop(true, true).fadeOut(200);
		$("body").css({"position":"static"});
		$(this).fadeIn();
		$(".wrap_bg").stop(true, true).fadeOut(200);
	});
};

var gl_loader_cnt = 0;

function showLoaderM(){
	if(gl_loader_cnt == 0) {
		if(!$(".dimmer_set").hasClass("show")) {
			$(".dimmer_set").addClass("show");
		}
	}
	gl_loader_cnt++;
}
function hideLoaderM(){
	if(gl_loader_cnt > 0) {
		gl_loader_cnt--;
	}

	if(gl_loader_cnt == 0) {
		if($(".dimmer_set").hasClass("show")) {
			$(".dimmer_set").removeClass("show");
		}
	}
}

function showLoader(){
	$(".dimmer_set").addClass("show");
}
function hideLoader(){
	$(".dimmer_set").removeClass("show");
}


$(document).ready(function(){
	gnb();
	gnb_open();
});