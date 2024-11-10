<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 발주서 다운로드
 *        발주코드 만 있으면 최신 파일 다운로드
 *       파일생성 IDX 가 있으면 해당 파일 다운로드
 *       이메일 발송 로그 IDX 가 있으면 다운로드 로그 Insert
 * TODO : 관리자가 아닌 판매처 일 경우 권한 체크 필요!
 * TODO : 이메일로 발송되는 발주서 다운로드 시 현 파일을 Access 하므로 검토 필요!
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

//$idx = $_GET["idx"];
$stock_order_idx = $_GET["stock_order_idx"];            //발주서 IDX
$stock_order_file_idx = $_GET["stock_order_file_idx"];  //발주서 파일 생성 로그 IDX

$C_Stock = new Stock();

//발주서 정보 얻기
$_view = $C_Stock -> getStockOrderData($stock_order_idx);

if($_view) {

	$stock_order_filename = "";
	if($stock_order_file_idx)
	{
		//파일 생성 로그 IDX 가 있으면 해당 파일을 다운로드
		$_file_view = $C_Stock -> getStockOrderFileLog($stock_order_file_idx);
		$stock_order_filename = $_file_view["stock_order_file_name"];
	}else{
		//파일 생성 로그 IDX 가 없으면 최신 파일을 다운로드
		$stock_order_file_idx = $_view["stock_order_file_idx"];
		$stock_order_filename = $_view["stock_order_filename"];
	}

	$filepath      = DY_STOCK_ORDER_PATH . "/" . $stock_order_filename;
	$user_filename = "발주서[".$stock_order_idx."].xlsx";

	if (file_exists($filepath)) {
		$filesize = filesize($filepath);
		//$filename = mb_basename($filepath);
		if (is_ie()) $user_filename = utf2euc($user_filename);

		//발주서 파일 다운로드 로그
		//이메일에서 다운받기 하였을 경우 로그 Insert
		//사이트에서 다운받기 시 로그 Insert 안함!!
		//stock_order_email_idx : 발주소 이메일 발송 로그 IDX
		if(isset($_GET["stock_order_email_idx"]))
		{
			$C_Stock -> insertStockOrderDocumentDownLog($stock_order_idx, $stock_order_file_idx, $_GET["stock_order_email_idx"]);
		}

		header("Pragma: public");
		header("Expires: 0");
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header("Content-Disposition: attachment; filename=\"$user_filename\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: $filesize");

		readfile($filepath);
	}else{
		put_msg("다운로드 할 수 없습니다.");
	}
}else{
	put_msg("다운로드 할 수 없습니다..");
	exit;
}
?>