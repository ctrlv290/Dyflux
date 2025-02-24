<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 카테고리관리 - 실행 프로세스 (by Ajax)
 */

//Page Info
$pageCategoryIdx = 52;
include_once "../_init_.php";

$C_Category = new Category();

$response = array(
	"result" => false,
	"msg" => "",
	"categoryVal" => ""
);

$mode       = $_POST["mode"];
$idx        = $_POST["idx"];
$parent_idx = $_POST["parent_idx"];
$name       = $_POST["name"];
$is_use     = $_POST["is_use"];

if($mode == "get"){
	$rst = $C_Category->getCategoryInfo($idx);
	if($rst){
		$response["result"] = true;
		$response["categoryVal"] = $rst;
	}else{
		$response["result"] = false;
		$response["msg"] = "존재하지 않는 카테고리입니다.";
	}

}elseif($mode == "add"){

	$args                        = array();
	$args["parent_category_idx"] = $parent_idx;
	$args["name"]                = $name;
	$args["is_hidden"]           = "N";
	$args["is_use"]              = $is_use;

	$new_category_idx = $C_Category->insertCategory($args);

	if($new_category_idx){
		$response["result"] = true;
	}else{
		$response["result"] = false;
		$response["msg"] = "오류가 발생하였습니다.";
	}

}elseif ($mode == "mod"){
	$args                        = array();
	$args["idx"]                 = $idx;
	$args["parent_category_idx"] = $parent_idx;
	$args["name"]                = $name;
	$args["is_hidden"]           = "N";
	$args["is_use"]              = $is_use;

	$rst = $C_Category->updateCategory($args);

	$response["result"] = true;
}elseif($mode == "del"){

	$sub_count = $C_Category->getSubCategoryCount($idx);
	if($sub_count > 0)
	{
		$response["result"] = false;
		$response["msg"] = "하위메뉴가 있는 메뉴는 삭제할 수 없습니다.";
	}else{
		$rst = $C_Category->deleteCategory($idx);
		$response["result"] = true;
	}
}elseif($mode == "move"){

	$dir = $_POST["dir"];

	$args = array();
	$args["dir"] = $dir;
	$args["idx"] = $idx;
	$chk = $C_Category->checkCanSortChange($args);

	if($chk["result"]) {

		$C_Category->moveCategorySort($args);
		$response["result"] = true;
	}else{
		$response["result"] = false;
		$response["msg"] = $chk["msg"];
	}
}elseif($mode == "get_category_list"){


	$list = $C_Category->getCategoryList($idx);
	$response["result"] = true;
	$response["list"] = $list;

}

echo json_encode($response);
?>