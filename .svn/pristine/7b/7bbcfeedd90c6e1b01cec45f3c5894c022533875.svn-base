<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 벤더사등급관리 관련 Process
 */
//Page Info
$pageMenuIdx = 61;
//Init
include "../_init_.php";

$C_VendorGrade = new VendorGrade();

$response = array(
	"result" => false,
	"msg" => "",
);

$mode                   = $_GET["mode"];
$vendor_grade_idx       = $_GET["vendor_grade_idx"];
$vendor_grade_name      = $_GET["vendor_grade_name"];
$vendor_grade_discount  = $_GET["vendor_grade_discount"];
$vendor_grade_etc       = $_GET["vendor_grade_etc"];


if($mode == "save")
{
	$args = array();
	$args["vendor_grade_idx"] = $vendor_grade_idx;
	$args["vendor_grade_name"] = $vendor_grade_name;
	$args["vendor_grade_discount"] = $vendor_grade_discount;
	$args["vendor_grade_etc"] = $vendor_grade_etc;

	$rst = $C_VendorGrade -> saveVendorGrade($args);
	$response["result"] = true;
}

echo json_encode($response, true);
?>