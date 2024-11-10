<?php
/**
 * User: ssawoona
 * Date: 2018
 * Desc: 11번가 API 작업
 * Request : SellerID, StartDate, EndDate
 * Session : 로그인상태
 */

//Init
include_once "../_init_.php";

$ret_json = array(
	'result' => false,
	'status_code' => 0,
	'result_text' => '',
	'order_count' => 0,
	'upload_filename' => '',
);

$seller_idx  = $_GET["seller_idx"];
$market_code = $_GET["market_code"];
$s_date      = $_GET["s_date"];
$e_date      = $_GET["e_date"];

if(!$s_date || !$e_date || $market_code) {
	$ret_json['status_code']     = -9999;
	$ret_json['result_text']     = "잘못된 접근 입니다.";
}
$C_Seller = new Seller();
if($seller_idx)
{
	$_view = $C_Seller->getSellerData($seller_idx);
	if($_view)
	{
		$mode = "mod";
		extract($_view);
		if($market_code != "COUPANG") {
			$ret_json['status_code']     = -9997;
			$ret_json['result_text']     = "존재하지 않은 판매처 입니다.";
		}
	}else{
		$ret_json['status_code']     = -9998;
		$ret_json['result_text']     = "존재하지 않은 판매처 입니다.";
	}
} else {
	$ret_json['status_code']     = -9999;
	$ret_json['result_text']     = "잘못된 접근 입니다.";
}
if($ret_json['status_code'] < 0) {
	echo json_encode($ret_json);
	return;
}
//print_r2($_view);

//$tmp_xls_header = "묶음배송번호|주문번호|택배사|운송장번호|분리배송 Y/N|주문시 출고예정일|주문일|등록상품명|등록옵션명|노출상품명(옵션명)|노출상품ID|옵션ID|최초등록옵션명|업체상품코드|결제액|배송비구분|배송비|도서산간 추가배송비|구매수(수량)|옵션판매가(판매단가)|구매자|구매자이메일|구매자전화번호|수취인이름|수취인전화번호|우편번호|수취인 주소|배송메세지|결제위치";
//$tmp_api_header = "shipmentBoxId|orderId|deliveryCompanyName|invoiceNumber|splitShipping|orderItems_estimatedShippingDate|orderedAt|orderItems_sellerProductName|orderItems_sellerProductItemName|orderItems_vendorItemName|orderItems_productId|orderItems_vendorItemId|orderItems_firstSellerProductItemName|orderItems_externalVendorSkuCode|orderItems_orderPrice|orderItems_deliveryChargeTypeName|shippingPrice|remotePrice|orderItems_shippingCount|orderItems_salesPrice|orderer_name|orderer_email|orderer_safeNumber|receiver_name|receiver_safeNumber|receiver_postCode|receiver_addr1|parcelPrintMessage|refer";
//$arr_xls_header = explode("|", $tmp_xls_header);
//$arr_api_header = explode("|", $tmp_api_header);

$VENDOR_ID  = $market_login_id;
$ACCESS_KEY = $market_auth_code;
$SECRET_KEY = $market_auth_code2;

$C_API_Coupang = new API_Coupang();
$ret = $C_API_Coupang -> getOrderList(array(
	'VENDOR_ID' => $VENDOR_ID,
	'ACCESS_KEY' => $ACCESS_KEY,
	'SECRET_KEY' => $SECRET_KEY,
	's_date' => $s_date,
	'e_date' => $e_date,
));
$arr_xls_header = $C_API_Coupang->arr_xls_header;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$new_file_name = "";
$row_cnt = 0;

if($ret['status_code'] == 10) {
	$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
	$spreadsheet->setActiveSheetIndex(0);
	$activesheet = $spreadsheet->getActiveSheet();
	$xls_header_end = getNameFromNumber(count($arr_xls_header) - 1);

	$activesheet->fromArray($arr_xls_header, NULL, 'A1');
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
} else {
	echo json_encode($ret_json, true);
}

//print_r2($ret);

//echo($result);

class API_Coupang2
{
	public $tmp_xls_header = "묶음배송번호|주문번호|택배사|운송장번호|분리배송 Y/N|주문시 출고예정일|주문일|등록상품명|등록옵션명|노출상품명(옵션명)|노출상품ID|옵션ID|최초등록옵션명|업체상품코드|결제액|배송비구분|배송비|도서산간 추가배송비|구매수(수량)|옵션판매가(판매단가)|구매자|구매자이메일|구매자전화번호|수취인이름|수취인전화번호|우편번호|수취인 주소|배송메세지|결제위치";
	public $tmp_api_header = "shipmentBoxId|orderId|deliveryCompanyName|invoiceNumber|splitShipping|orderItems_estimatedShippingDate|orderedAt|orderItems_sellerProductName|orderItems_sellerProductItemName|orderItems_vendorItemName|orderItems_productId|orderItems_vendorItemId|orderItems_firstSellerProductItemName|orderItems_externalVendorSkuCode|orderItems_orderPrice|orderItems_deliveryChargeTypeName|shippingPrice|remotePrice|orderItems_shippingCount|orderItems_salesPrice|orderer_name|orderer_email|orderer_safeNumber|receiver_name|receiver_safeNumber|receiver_postCode|receiver_addr1|parcelPrintMessage|refer";
	public $arr_xls_header;
	public $arr_api_header;
	function __construct()
	{
		$this->arr_xls_header = explode("|", $this->tmp_xls_header);
		$this->arr_api_header = explode("|", $this->tmp_api_header);
	}

	public function repCode2String($key, $data)
	{
		$ret = "";
		//echo $key, $data;
		$arr_ableSplitShipping = array(
			'0' => 'N',
			'1' => 'Y',
			'' => '분리배송불가',
		);
		if ($key == "ableSplitShipping") {
			$ret = $arr_ableSplitShipping[$data];
		}
		//echo $ret;
		return $ret;
	}

	public function getCurl($args)
	{
		$arrRet = array(
			'status_code' => 0,
			'result_text' => '',
		);
		$ACCESS_KEY = "";
		$SECRET_KEY = "";
		$path = "";
		$query = "";
		extract($args);

		date_default_timezone_set("GMT+0");
		$datetime = date("ymd").'T'.date("His").'Z';
		$method = "GET";
		$message = $datetime.$method.$path.$query;
		$signature = hash_hmac('sha256', $message, $SECRET_KEY);
		$authorization  = "CEA algorithm=HmacSHA256, access-key=".$ACCESS_KEY.", signed-date=".$datetime.", signature=".$signature;
		$url = 'https://api-gateway.coupang.com'.$path.'?'.$query;

		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type:  application/json;charset=UTF-8", "Authorization:".$authorization));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		$arrRet['result_text'] = curl_exec($curl);
		$arrRet['status_code'] = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $arrRet;
	}

	public function getOrderList($args)
	{
		$arrRet = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
		);
		$VENDOR_ID = "";
		$ACCESS_KEY = "";
		$SECRET_KEY = "";
		$s_date = "";
		$e_date = "";
		$API_header = $this->arr_api_header;
		extract($args);

		$path = "/v2/providers/openapi/apis/api/v4/vendors/".$VENDOR_ID."/ordersheets";
		$query = "createdAtFrom=".$s_date."&createdAtTo=".$e_date."&maxPerPage=50&status=INSTRUCT&nextToken=";

		// 신규주문 조회
		$api_url = "https://api.11st.co.kr/rest/ordservices/complete/" . $s_date . "/" . $e_date;
		$ret_curl    = $this->getCurl(array(
				'path' => $path,
				'query' => $query,
				'ACCESS_KEY' => $ACCESS_KEY,
				'SECRET_KEY' => $SECRET_KEY,
			)
		);
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		$data_array = json_decode($response, true);

		$arrData   = array();
		$tmp_array = array();
		foreach ($API_header as $val) {
			$tmp_array[$val] = "";
		}
		$row_num = 0;

		//$arrRet["status_code"] = $status_code;
		//echo $response;
		//print_r2($data_array);
		//$arrRet["status_code"] = $status_code;
		if ($status_code == 200) {
			$arrRet["status_code"]  = 0;
			foreach ($data_array as $r_key => $r_val) {
				//echo $r_key."->".$r_val."<br />";
				if($r_key == "data") {
					foreach ($r_val as $o_key => $order) {
						$arr_row = $tmp_array;
						//echo $o_key."->".$order."<br />";
						for ($i = 0; $i < count($API_header); $i++) {
							$tmp = "";
							foreach ($order as $i_key => $item) {
								//echo $i_key . "->" . $item . "<br />";
								if ($i_key == $API_header[$i]) {
									$tmp = (string)$item;
									if($API_header[$i] == "ableSplitShipping") {
										$tmp = $this->repCode2String($API_header[$i], $tmp);
									}
									if($API_header[$i] == "orderedAt") {
										$tmp = str_replace("T", " ", $tmp);
									}

								}
								if(strpos($API_header[$i], "orderer_") !== false) {
									if ($i_key == "orderer") {
										$tmp = $item[str_replace("orderer_", "", $API_header[$i])];
									}
								}
								if(strpos($API_header[$i], "receiver_") !== false) {
									if ($i_key == "receiver") {
										$tmp = $item[str_replace("receiver_", "", $API_header[$i])];
									}
								}
							}
							$arr_row[$API_header[$i]] = (string)$tmp. "";
						}
						$arr_row["receiver_addr1"] = $arr_row["receiver_addr1"]." ".$order["receiver"]["addr2"];

						foreach ($order["orderItems"] as $i_key => $item) {
							$arr_item_row = $arr_row;
							foreach ($item as $oi_key => $order_item) {
								for ($i = 0; $i < count($API_header); $i++) {
									if ($oi_key == str_replace("orderItems_", "", $API_header[$i])) {
										//echo $API_header[$i]." : ".$oi_key . "->" . $order_item . "<br />";
										$arr_item_row[$API_header[$i]] = $order_item. "";
									}
								}
							}
							//print_r2($item);
							if ($arr_row["orderId"] != "") {
								$arrData[$row_num] = $arr_item_row;
								$row_num++;
							}
						}

					}
				}
			}
		}
		print_r2($arrData);

		if($arrRet["status_code"] < 0) {
			$arrRet["result_text"] = "Coupang API Error (발송대기)";
			return $arrRet;
		} else {
			$arrRet["result_text"] = "";
		}


		$arrRet["result_data"] = $arrData;
		return $arrRet;

	}

}
?>
