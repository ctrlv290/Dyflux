<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 시스템공지사항 Process 페이지
 */
//Page Info
$pageMenuIdx = 145;
//Init
$GL_JsonHeader = true; //Json Header
include_once "../_init_.php";

$C_BBS = new BBS();

$mode    = $_POST["mode"];
$bbs_idx = $_POST["bbs_idx"];

$response = array();
$response["result"] = false;
$response["data"] = "";

if($mode == "comment_add"){

	$comment = $_POST["comment"];

	$rst = $C_BBS->insertComment($bbs_idx, $comment);

	$response["result"] = true;

}elseif($mode == "comment_list"){

	$rst = $C_BBS->getCommentList($bbs_idx);

	foreach($rst as $k => $v){
		$v["comment"] = htmlentities_utf8($v["comment"]);
		$rst[$k] = $v;
 	}

	$response["result"] = true;
	$response["data"] = $rst;
}elseif($mode == "comment_delete"){
	$comment_idx = $_POST["comment_idx"];
	$rst = $C_BBS->deleteComment($comment_idx);

	$response["result"] = true;
}
echo json_encode($response);
?>