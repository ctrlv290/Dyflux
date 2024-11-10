<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공통 파일 다운로드
 */
//Init
ini_set('memory_limit','-1');
include_once "../_init_.php";

//error_reporting(E_ALL ^ E_NOTICE);
//ini_set("display_errors", 1);

function mb_basename($path) { return end(explode('/',$path)); }
function utf2euc($str) { return iconv("UTF-8","cp949//IGNORE", $str); }
function is_ie() {
	if(!isset($_SERVER['HTTP_USER_AGENT']))return false;
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
	if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) return true; // IE11
	return false;
}

$idx = $_GET["idx"];
$filename = $_GET["filename"];

$C_Order = new Order();
$fileChk = $C_Order -> getOrderInvoiceUploadLogFileInfo($idx, $filename);

if($fileChk) {

	$filepath = DY_ORDER_INVOICE_PATH . "/" . $filename;
	$user_filename = $fileChk;



	if (file_exists($filepath)) {
		$filesize = filesize($filepath);
		//$filename = mb_basename($filepath);
		if (is_ie()) $user_filename = utf2euc($user_filename);

		header("Pragma: public");
		header("Expires: 0");
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename=\"$user_filename\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: $filesize");

		readfile($filepath);
	}
}
?>