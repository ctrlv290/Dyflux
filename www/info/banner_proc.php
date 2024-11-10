<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 배너관리 관련 Process
 */
//Page Info
$pageMenuIdx = 280;
include "../_init_.php";

$C_Banner = new Banner();

$mode                = $_POST["mode"];
$banner_idx          = $_POST["banner_idx"];
$banner_click_url    = $_POST["banner_click_url"];
$banner_click_target = $_POST["banner_click_target"];
$banner_use_period   = $_POST["banner_use_period"];
$banner_period_start = $_POST["banner_period_start"];
$banner_period_end   = $_POST["banner_period_end"];
$banner_sort         = $_POST["banner_sort"];
$bank_is_use         = $_POST["bank_is_use"];

if($mode == "add") {
	$imagePath = DY_BANNER_FILE_PATH;
	$allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
	$temp        = explode(".", $_FILES["banner_image"]["name"]);
	$extension   = end($temp);

	list($usec, $sec) = explode(" ", microtime());
	$uploadFilename = (round(((float)$usec + (float)$sec))) . rand(1, 10000);        //  업로드 파일명 날짜에 따라 변환
	$new_file_name  = $uploadFilename . "." . $extension;                            //  새로운 파일명 생성

	if (!is_writable($imagePath)) {
		put_msg_and_back("업로드에 실패하였습니다.");
	}

	$mimetype = mime_content_type($_FILES['banner_image']['tmp_name']);
	if(in_array($mimetype, array('image/jpeg', 'image/gif', 'image/png'))) {
		if ($_FILES["banner_image"]["error"] > 0) {
			put_msg_and_back("업로드에 실패하였습니다.\n".$_FILES["banner_image"]["error"]);
		} else {

			$filename = $_FILES["banner_image"]["tmp_name"];
			list($width, $height) = getimagesize($filename);

			move_uploaded_file($filename, $imagePath . '/' . $new_file_name);

			$args                        = array();
			$args["banner_type"]         = "MAIN";
			$args["banner_click_url"]    = $banner_click_url;
			$args["banner_click_target"] = $banner_click_target;
			$args["banner_use_period"]   = $banner_use_period;
			$args["banner_period_start"] = ($banner_use_period == "Y") ? $banner_period_start : "";
			$args["banner_period_end"]   = ($banner_use_period == "Y") ? $banner_period_end : "";
			$args["banner_sort"]         = $banner_sort;
			$args["banner_is_use"]       = $banner_is_use;
			$args["banner_image"]        = $new_file_name;

			$inserted_idx = $C_Banner -> insertBanner($args);

			put_msg_and_go("등록되었습니다.", "banner_list.php");

		}
	} else {
		put_msg_and_back("업로드가 불가능한 파일입니다.");
	}
}elseif($mode == "mod"){
	if($_FILES['banner_image']['tmp_name']){
		$imagePath = DY_BANNER_FILE_PATH;
		$allowedExts = array("gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG");
		$temp        = explode(".", $_FILES["banner_image"]["name"]);
		$extension   = end($temp);

		list($usec, $sec) = explode(" ", microtime());
		$uploadFilename = (round(((float)$usec + (float)$sec))) . rand(1, 10000);        //  업로드 파일명 날짜에 따라 변환
		$new_file_name  = $uploadFilename . "." . $extension;                            //  새로운 파일명 생성

		if (!is_writable($imagePath)) {
			put_msg_and_back("업로드에 실패하였습니다.");
		}

		$mimetype = mime_content_type($_FILES['banner_image']['tmp_name']);
		if(in_array($mimetype, array('image/jpeg', 'image/gif', 'image/png'))) {
			if ($_FILES["banner_image"]["error"] > 0) {
				put_msg_and_back("업로드에 실패하였습니다.\n".$_FILES["banner_image"]["error"]);
			} else {

				$filename = $_FILES["banner_image"]["tmp_name"];
				list($width, $height) = getimagesize($filename);

				move_uploaded_file($filename, $imagePath . '/' . $new_file_name);
			}
		} else {
			put_msg_and_back("업로드가 불가능한 파일입니다.");
		}
	}

	$args                        = array();
	$args["banner_idx"]          = $banner_idx;
	$args["banner_click_url"]    = $banner_click_url;
	$args["banner_click_target"] = $banner_click_target;
	$args["banner_use_period"]   = $banner_use_period;
	$args["banner_period_start"] = ($banner_use_period == "Y") ? $banner_period_start : "";
	$args["banner_period_end"]   = ($banner_use_period == "Y") ? $banner_period_end : "";
	$args["banner_is_use"]       = $banner_is_use;
	if($new_file_name) {
		$args["banner_image"] = $new_file_name;
	}

	$inserted_idx = $C_Banner -> updateBanner($args);

	put_msg_and_go("수정되었습니다.", "banner_list.php");
}elseif($_GET["mode"] == "del" && $_GET["banner_idx"]) {

	print_r2($_GET);

	$tmp = $C_Banner->deleteBanner($_GET["banner_idx"]);
	put_msg_and_go("삭제되었습니다.", "banner_list.php");

}elseif($_GET["mode"] == "move" && $_GET["banner_idx"] && $_GET["dir"]){


	$args = array();
	$args["dir"] = $_GET["dir"];
	$args["banner_idx"] = $_GET["banner_idx"];
	$rst = $C_Banner->moveBanner($args);

	//print_r2($rst);
	if($rst["result"]){
		go_replace("banner_list.php");
	}else{
		put_msg_and_back($rst["msg"]);
	}

}
?>