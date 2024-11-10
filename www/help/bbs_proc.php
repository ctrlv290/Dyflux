<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 시스템공지사항 Process 페이지
 */
//Page Info
$pageMenuIdx = 145;
//Init
include_once "../_init_.php";

$C_BBS = new BBS();

$mode    = $_POST["mode"];
$bbs_id  = $_POST["bbs_id"];
$bbs_idx = $_POST["bbs_idx"];

//페이지 파라미터
$page_param_column_ary = array("bbs_id", "page", "date_start", "date_end", "search_column", "search_keyword");
$page_param_ary = array();
foreach($page_param_column_ary as $col) $page_param_ary[] = $col . "=" . $_GET[$col];
$page_parameters = implode("&", $page_param_ary);

$page = "notice_php";
if($bbs_id == "notice") {
	$page = "notice_list.php";
}elseif($bbs_id == "biz"){
	$page = "biz_list.php";
}elseif($bbs_id == "design"){
	$page = "design_list.php";
}elseif($bbs_id == "work"){
	$page = "work_list.php";
}elseif($bbs_id == "faq"){
	$page = "faq_list.php";
}

if($mode == "add") {

	$bbs_target          = $_POST["bbs_target"];
	$bbs_target_vendor_A = ($_POST["bbs_target_vendor_A"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_B = ($_POST["bbs_target_vendor_B"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_C = ($_POST["bbs_target_vendor_C"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_D = ($_POST["bbs_target_vendor_D"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_E = ($_POST["bbs_target_vendor_E"] == "Y") ? "Y" : "N";
	$bbs_category        = $_POST["bbs_category"];
	$bbs_is_main         = $_POST["bbs_is_main"];
	$bbs_is_notice       = $_POST["bbs_is_notice"];
	$bbs_title           = $_POST["bbs_title"];
	$bbs_contents        = $_POST["bbs_contents"];
	$bbs_file_idx_1      = $_POST["bbs_file_idx_1"];
	$bbs_file_idx_2      = $_POST["bbs_file_idx_2"];
	$bbs_file_idx_3      = $_POST["bbs_file_idx_3"];
	$bbs_file_idx_4      = $_POST["bbs_file_idx_4"];
	$bbs_file_idx_5      = $_POST["bbs_file_idx_5"];

	$args                        = array();
	$args["bbs_id"]              = $bbs_id;
	$args["bbs_target"]          = $bbs_target;
	$args["bbs_target_vendor_A"] = $bbs_target_vendor_A;
	$args["bbs_target_vendor_B"] = $bbs_target_vendor_B;
	$args["bbs_target_vendor_C"] = $bbs_target_vendor_C;
	$args["bbs_target_vendor_D"] = $bbs_target_vendor_D;
	$args["bbs_target_vendor_E"] = $bbs_target_vendor_E;
	$args["bbs_category"]        = $bbs_category;
	$args["bbs_is_main"]         = $bbs_is_main;
	$args["bbs_is_notice"]       = $bbs_is_notice;
	$args["bbs_title"]           = $bbs_title;
	$args["bbs_contents"]        = $bbs_contents;
	$args["bbs_file_idx_1"]      = $bbs_file_idx_1;
	$args["bbs_file_idx_2"]      = $bbs_file_idx_2;
	$args["bbs_file_idx_3"]      = $bbs_file_idx_3;
	$args["bbs_file_idx_4"]      = $bbs_file_idx_4;
	$args["bbs_file_idx_5"]      = $bbs_file_idx_5;

	$rst = $C_BBS->insertBBS($args);

	go_replace($page);
}elseif($mode == "update") {

	$bbs_idx             = $_POST["bbs_idx"];
	$bbs_target          = $_POST["bbs_target"];
	$bbs_target_vendor_A = ($_POST["bbs_target_vendor_A"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_B = ($_POST["bbs_target_vendor_B"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_C = ($_POST["bbs_target_vendor_C"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_D = ($_POST["bbs_target_vendor_D"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_E = ($_POST["bbs_target_vendor_E"] == "Y") ? "Y" : "N";
	$bbs_category        = $_POST["bbs_category"];
	$bbs_is_main         = $_POST["bbs_is_main"];
	$bbs_is_notice       = $_POST["bbs_is_notice"];
	$bbs_title           = $_POST["bbs_title"];
	$bbs_contents        = $_POST["bbs_contents"];
	$bbs_file_idx_1      = $_POST["bbs_file_idx_1"];
	$bbs_file_idx_2      = $_POST["bbs_file_idx_2"];
	$bbs_file_idx_3      = $_POST["bbs_file_idx_3"];
	$bbs_file_idx_4      = $_POST["bbs_file_idx_4"];
	$bbs_file_idx_5      = $_POST["bbs_file_idx_5"];

	$args                        = array();
	$args["bbs_idx"]             = $bbs_idx;
	$args["bbs_id"]              = $bbs_id;
	$args["bbs_target"]          = $bbs_target;
	$args["bbs_target_vendor_A"] = $bbs_target_vendor_A;
	$args["bbs_target_vendor_B"] = $bbs_target_vendor_B;
	$args["bbs_target_vendor_C"] = $bbs_target_vendor_C;
	$args["bbs_target_vendor_D"] = $bbs_target_vendor_D;
	$args["bbs_target_vendor_E"] = $bbs_target_vendor_E;
	$args["bbs_category"]        = $bbs_category;
	$args["bbs_is_main"]         = $bbs_is_main;
	$args["bbs_is_notice"]       = $bbs_is_notice;
	$args["bbs_title"]           = $bbs_title;
	$args["bbs_contents"]        = $bbs_contents;
	$args["bbs_file_idx_1"]      = $bbs_file_idx_1;
	$args["bbs_file_idx_2"]      = $bbs_file_idx_2;
	$args["bbs_file_idx_3"]      = $bbs_file_idx_3;
	$args["bbs_file_idx_4"]      = $bbs_file_idx_4;
	$args["bbs_file_idx_5"]      = $bbs_file_idx_5;

	$rst = $C_BBS->updateBBS($args);

	go_replace($page . "?bbs_idx=" . $bbs_idx . "&" . $page_parameters);
}elseif($mode == "delete"){
	$bbs_id              = $_POST["bbs_id"];
	$bbs_idx             = $_POST["bbs_idx"];

	$_view = $C_BBS->getBBSView($bbs_id, $bbs_idx);

	if($_view["member_idx"] == $GL_Member["member_idx"]) {
		$rst = $C_BBS->deleteBBS($bbs_idx);
	}

	go_replace($page);

}elseif($mode == "reply") {

	$ref_idx             = $_POST["ref_idx"];
	$ref_level           = $_POST["ref_level"];
	$bbs_target          = $_POST["bbs_target"];
	$bbs_target_vendor_A = ($_POST["bbs_target_vendor_A"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_B = ($_POST["bbs_target_vendor_B"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_C = ($_POST["bbs_target_vendor_C"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_D = ($_POST["bbs_target_vendor_D"] == "Y") ? "Y" : "N";
	$bbs_target_vendor_E = ($_POST["bbs_target_vendor_E"] == "Y") ? "Y" : "N";
	$bbs_category        = $_POST["bbs_category"];
	$bbs_is_main         = $_POST["bbs_is_main"];
	$bbs_title           = $_POST["bbs_title"];
	$bbs_contents        = $_POST["bbs_contents"];
	$bbs_file_idx_1      = $_POST["bbs_file_idx_1"];
	$bbs_file_idx_2      = $_POST["bbs_file_idx_2"];
	$bbs_file_idx_3      = $_POST["bbs_file_idx_3"];
	$bbs_file_idx_4      = $_POST["bbs_file_idx_4"];
	$bbs_file_idx_5      = $_POST["bbs_file_idx_5"];

	$args                        = array();
	$args["ref_idx"]             = $ref_idx;
	$args["ref_level"]           = $ref_level;
	$args["bbs_id"]              = $bbs_id;
	$args["bbs_target"]          = $bbs_target;
	$args["bbs_target_vendor_A"] = $bbs_target_vendor_A;
	$args["bbs_target_vendor_B"] = $bbs_target_vendor_B;
	$args["bbs_target_vendor_C"] = $bbs_target_vendor_C;
	$args["bbs_target_vendor_D"] = $bbs_target_vendor_D;
	$args["bbs_target_vendor_E"] = $bbs_target_vendor_E;
	$args["bbs_category"]        = $bbs_category;
	$args["bbs_is_main"]         = $bbs_is_main;
	$args["bbs_title"]           = $bbs_title;
	$args["bbs_contents"]        = $bbs_contents;
	$args["bbs_file_idx_1"]      = $bbs_file_idx_1;

	$rst = $C_BBS->insertBBS($args);

	go_replace($page);

}elseif($mode == "comment_add"){

	$comment = $_POST["comment"];

	$rst = $C_BBS->insertComment($bbs_idx, $comment);

	$response = array();
	$response["result"] = true;
	$response["data"] = "";

	echo json_encode($response);
}
?>