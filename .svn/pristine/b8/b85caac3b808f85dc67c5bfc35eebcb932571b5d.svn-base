<?php
include_once "../_init_.php";

$C_Menu = new Menu();

$response = array(
	"result" => false,
	"msg" => "",
	"menuVal" => ""
);

$mode       = $_POST["mode"];
$idx        = $_POST["idx"];
$parent_idx = $_POST["parent_idx"];
$name       = $_POST["name"];
$name_short = $_POST["name_short"];
$url        = $_POST["url"];
$target     = $_POST["target"];
$popup_x    = $_POST["popup_x"];
$popup_y    = $_POST["popup_y"];
$css_class  = $_POST["css_class"];
$is_hidden  = $_POST["is_hidden"];
$is_use     = $_POST["is_use"];

if(!$name_short) $name_short = $name;

if($mode == "get"){
	$rst = $C_Menu->getMenuInfo($idx);
	if($rst){
		$response["result"] = true;
		$response["menuVal"] = $rst;
	}else{
		$response["result"] = false;
		$response["msg"] = "존재하지 않는 메뉴입니다.";
	}

}elseif($mode == "add"){

	$args = array();
	$args["parent_idx"]     = $parent_idx;
	$args["name"]           = $name;
	$args["name_short"]     = $name_short;
	$args["url"]            = $url;
	$args["target"]         = $target;
	$args["popup_size"]     = ($target == "popup") ? $popup_x . "|" . $popup_y : "";
	$args["css_class"]      = $css_class;
	$args["is_hidden"]      = $is_hidden;
	$args["is_use"]         = $is_use;

	$new_menu_idx = $C_Menu->insertMenu($args);

	if($new_menu_idx){
		$response["result"] = true;
	}else{
		$response["result"] = false;
		$response["msg"] = "오류가 발생하였습니다.";
	}

}elseif ($mode == "mod"){

	if($target == "popup"){
		$popup_x = (!$popup_x) ? "0" : $popup_x;
		$popup_y = (!$popup_y) ? "0" : $popup_y;
	}

	$args = array();
	$args["idx"]            = $idx;
	$args["parent_idx"]     = $parent_idx;
	$args["name"]           = $name;
	$args["name_short"]     = $name_short;
	$args["url"]            = $url;
	$args["target"]         = $target;
	$args["popup_size"]     = ($target == "popup") ? $popup_x . "|" . $popup_y : "";
	$args["css_class"]      = $css_class;
	$args["is_hidden"]      = $is_hidden;
	$args["is_use"]         = $is_use;

	$rst = $C_Menu->updateMenu($args);

	$response["result"] = true;
}elseif($mode == "del"){

	$sub_count = $C_Menu->getSubMenuCount($idx);
	if($sub_count > 0)
	{
		$response["result"] = false;
		$response["msg"] = "하위메뉴가 있는 메뉴는 삭제할 수 없습니다.";
	}else{
		$rst = $C_Menu->deleteMenu($idx);
		$response["result"] = true;
	}
}elseif($mode == "move"){

	$dir = $_POST["dir"];

	$args = array();
	$args["dir"] = $dir;
	$args["idx"] = $idx;
	$chk = $C_Menu->checkCanSortChange($args);

	if($chk["result"]) {

		$C_Menu->moveMenuSort($args);
		$response["result"] = true;
	}else{
		$response["result"] = false;
		$response["msg"] = $chk["msg"];
	}
}

echo json_encode($response);
?>