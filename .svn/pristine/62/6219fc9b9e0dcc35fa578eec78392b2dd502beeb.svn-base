<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 썸네일 URL 리턴
 */

include_once "../_init_.php";


$file_idx = $_GET["file_idx"];
$save_filename = $_GET["save_filename"];
$width = $_GET["width"];
$height = $_GET["height"];
$img_type = $_GET["img_type"];
$force_create = $_GET["force_create"];
$is_crop = $_GET["is_crop"];

$img_type = (strtolower($img_type) != "png" && strtolower($img_type) != "jpg") ? "jpg" : strtolower($img_type);

if($is_crop == "Y")
{
	$is_crop = SCALE_EXACT_FIT;
}else{
	$is_crop = SCALE_SHOW_ALL;
}

if($force_create == "Y")
{
	$force_create = true;
}else{
	$force_create = false;
}

$response = array();
$response["result"] = false;

//$C_Thumb = new Thumbnail();
$C_Thumb2 = new Thumbnail2();
$C_WaterMark = new ThumbnailWatermark();
$C_Files = new Files();

$thumb_info = $C_Thumb2 -> create($file_idx, $save_filename, $width, $height , $is_crop, $img_type, $force_create);

if($thumb_info)
{
	$response["result"] = true;
	$response["thumb"] = $thumb_info;
}


echo json_encode($response, true);
?>