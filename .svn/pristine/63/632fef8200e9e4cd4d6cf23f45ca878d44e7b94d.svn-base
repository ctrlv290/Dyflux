<?php
/**
 * User: ssawoona
 * Date: 2018
 * Desc: API 사용가능 한 몰들에 대한 order List 조회 및 처리
 * Request : SellerID, StartDate, EndDate
 * Session : 로그인상태
 */

//Init
include_once "../_init_.php";
set_time_limit(600);

$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기


$ret_json = array(
	'result' => false,
	'status_code' => 0,
	'result_text' => '',
	'order_count' => 0,
	'upload_filename' => '',
);

$seller_idx = $_GET["seller_idx"];
$mk_code    = $_GET["market_code"];
$s_date     = $_GET["s_date"];
$e_date     = $_GET["e_date"];

if (!$s_date || !$e_date || !$market_code) {
	$ret_json['status_code'] = -9999;
	$ret_json['result_text'] = "잘못된 접근 입니다.";
}
$C_Seller = new Seller();
if ($seller_idx) {
	$_view = $C_Seller->getSellerData($seller_idx);
	if ($_view) {
		$mode = "mod";
		extract($_view);
		if ($market_code != $mk_code) {
			$ret_json['status_code'] = -9997;
			$ret_json['result_text'] = "존재하지 않은 판매처 입니다.";
		}
	} else {
		$ret_json['status_code'] = -9998;
		$ret_json['result_text'] = "존재하지 않은 판매처 입니다.";
	}
} else {
	$ret_json['status_code'] = -9999;
	$ret_json['result_text'] = "잘못된 접근 입니다.";
}
if ($ret_json['status_code'] < 0) {
	echo json_encode($ret_json);
	return;
}

/*if($_GET["test"] == "1") {
	$C_API_Cafe24 = new API_Cafe24();
	$ret         = $C_API_Cafe24->execDeliveryProc_test(array(
		's_date' => $s_date,
		'e_date' => $e_date,
		'seller_idx' => $seller_idx,
	));

	echo "test";
	exit();
}*/
$xls_headers;
switch ($market_code) {
	case "11ST" :
		$C_API_11st  = new API_11st();
		$ret         = $C_API_11st->getOrderList(array(
			's_date' => str_replace("-", "", $s_date) . "0000",
			'e_date' => str_replace("-", "", $e_date) . "2359",
			'api_key' => $market_auth_code,
		));
		$xls_headers = $C_API_11st->XLS_HEADERS;
		break;
	case "COUPANG" :
		$C_API_Coupang = new API_Coupang();
		$ret           = $C_API_Coupang->getOrderList(array(
			's_date' => $s_date,
			'e_date' => $e_date,
			'VENDOR_ID' => $market_login_id,
			'ACCESS_KEY' => $market_auth_code,
			'SECRET_KEY' => $market_auth_code2,
		));
		$xls_headers   = $C_API_Coupang->XLS_HEADERS;
		break;
	case "INTERPARK" :
		$C_API_Interpark = new API_Interpark();
		$ret           = $C_API_Interpark->getOrderList(array(
			's_date' => $s_date,
			'e_date' => $e_date,
			'sc_entrId' => $market_login_id,
			'sc_supplyEntrNo' => $market_auth_code,
			'sc_supplyCtrtSeq' => $market_auth_code2,
		));
		$xls_headers   = $C_API_Interpark->XLS_HEADERS;
		break;
	case "SSGMALL" :
		//http://localhost/dy_auto/_get_order_list.php?seller_idx=90050&s_date=2019-03-17&e_date=2019-03-22&market_code=SSGMALL
		$C_API_SSGmall = new API_SSGmall();
		$ret         = $C_API_SSGmall->getOrderList(array(
			's_date' => str_replace("-", "", $s_date),
			'e_date' => str_replace("-", "", $e_date),
			'api_key' => $market_auth_code,
		));
		$xls_headers = $C_API_SSGmall->XLS_HEADERS;
		break;
	case "LOTTECOM" :
		//http://localhost/dy_auto/_get_order_list.php?seller_idx=90050&s_date=2019-03-17&e_date=2019-03-22&market_code=SSGMALL
		$C_API_LotteCom = new API_LotteCom();
		$ret         = $C_API_LotteCom->getOrderList(array(
			's_date' => str_replace("-", "", $s_date),
			'e_date' => str_replace("-", "", $e_date),
			'UserId' => $market_login_id,
			'PassWd' => $market_login_pw,
		));
		$xls_headers = $C_API_LotteCom->XLS_HEADERS;
		break;
	case "CAFE24" :
		//http://localhost/dy_auto/_get_order_list.php?seller_idx=90050&s_date=2019-03-17&e_date=2019-03-22&market_code=SSGMALL
		$C_API_Cafe24 = new API_Cafe24();
		$ret         = $C_API_Cafe24->getOrderList(array(
			's_date' => $s_date,
			'e_date' => $e_date,
			'seller_idx' => $seller_idx,
		));
		$xls_headers = $C_API_Cafe24->XLS_HEADERS;
		break;

	default:
		$ret['status_code']      = -9996;
		$ret_json['status_code'] = -9996;
		$ret_json['result_text'] = "잘못된 접근 입니다.";
		break;
}
//print_r2($xls_headers);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$new_file_name = "";
$row_cnt = 0;

if($ret['status_code'] == 0 && $_GET['debug'] == "") {
	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	$spreadsheet->setActiveSheetIndex(0);
	$activesheet = $spreadsheet->getActiveSheet();
	$xls_header_end = getNameFromNumber(count($xls_headers) - 1);

	$activesheet->fromArray($xls_headers, NULL, 'A1');
	$activesheet->getStyle('A1:' . $xls_header_end . '1')->applyFromArray(
		array(
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'rotation' => 90,
				'startColor' => [
					'argb' => 'FFED7D31',
				],
				'endColor' => [
					'argb' => 'FFED7D31',
				],
			],
		)
	);

	$row_num = 2;
	$startColumn = "A";
	foreach ($ret['result_data'] as $order) {
		$currentColumn = $startColumn;
		foreach ($order as $cellValue) {
			$activesheet->setCellValueExplicit($currentColumn.$row_num, $cellValue,
				PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
			++$currentColumn;
		}
		$row_num++;
		$row_cnt++;
	}
	//return;


	$target_dir = DY_ORDER_XLS_UPLOAD_PATH;
	$extension  = "xlsx";
	list($usec, $sec) = explode(" ", microtime());
	$uploadFilename     = (round(((float)$usec + (float)$sec))) . rand(1, 10000);        //  업로드 파일명 날짜에 따라 변환
	$new_file_name      = $uploadFilename . "." . $extension;                            //  새로운 파일명 생성
	$new_file_name_path = $target_dir . '/' . $new_file_name;

	foreach (range('A', $xls_header_end) as $columnID) {
		$activesheet->getColumnDimension($columnID)->setAutoSize(true);
	}

	$Excel_writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);  /*----- Excel (Xls) Object*/

	ob_end_clean();
	$Excel_writer->save($new_file_name_path);

	$ret_json['result'] = true;

}


$ret_json['status_code']     = $ret['status_code'];
$ret_json['result_text']     = $ret['result_text'];
$ret_json["order_count"]     = $row_cnt;
$ret_json["upload_filename"] = $new_file_name;
//echo json_encode($ret_json);

if($_GET['down'] == "1" ) {

	function mb_basename($path)
	{
		return end(explode('/', $path));
	}

	function utf2euc($str)
	{
		return iconv("UTF-8", "cp949//IGNORE", $str);
	}

	function is_ie()
	{
		if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false) return true; // IE8
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'Windows NT 6.1') !== false) return true; // IE11
		if(strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== false) return true; // IE11
		return false;
	}


	$filepath      = DY_ORDER_XLS_UPLOAD_PATH . "/" . $new_file_name;
	$user_filename = $new_file_name;

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
else if($_GET['dp'] == "1" ) {
	//echo date("Y-m-d");
	echo json_encode($ret['result_data'], true);
	//echo json_encode($ret_json, true);
}
else {
	echo json_encode($ret_json, true);
}

//print_r2($ret);

//echo($result);

?>
