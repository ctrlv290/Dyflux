<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 정산예정금 일괄등록 엑셀 처리 Process
 */
//Page Info
$pageMenuIdx = 269;
//Init
include "../_init_.php";
$C_Settle = new Settle();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode                   = $_POST["mode"];
$act                    = $_POST["act"];
$xls_filename           = $_POST["xls_filename"];
$user_filename          = $_POST["user_filename"];
$xls_validrow           = $_POST["xls_validrow"];
$seller_idx             = $_POST["seller_idx"];
$exclude_list           = $_POST["exclude_list"];

if($exclude_list){
	$exclude_list = explode(",", $exclude_list);
}

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["err"] = array();
$response["userdata"] = array("field_name" => "");

$xls_filename_fullpath = DY_XLS_UPLOAD_PATH . "/" . $xls_filename;

$upload_datetime = date("Y-m-d H:i:s"); //업로드 일시로 사용됨

if(file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath)) {
	$spreadsheet = IOFactory::load($xls_filename_fullpath);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, false, true);

	if ($mode == "loss") {

		/*
		 * A : 주문번호
		 * B : 구매자명
		 * C : 제품명
		 * D : 판매수량
		 * E : 매출금액
		 * F : 수수료
		 * G : 공제/환급내역/기타수수료
		 * H : 배송비
		 * I : 배송비수수료
		 * J : 정산금액
		 * K : 정산일자
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			$row_index = 0;
			$checked_order_pack_idx_ary = array();
			foreach($sheetData as $row)
			{
				//아래 값이 하나라도 없으면 종료
				if(trim($row["A"]) == "" || trim($row["E"]) == "" || trim($row["J"]) == "" || trim($row["K"]) == "")
				{
					continue;
				}

				$rowValid = true;

				//Row Index
				$row_index++;
				$row["xls_idx"] = $row_index;

				//정보 초기화
				$market_order_no = "";
				$order_name = "";
				$market_product_name = "";
				$order_cnt = "";
				$order_amt = "";
				$commission = "";
				$commission_etc = "";
				$delivery_fee = "";
				$delivery_commission = "";
				$settle_amount = "";
				$loss_date = "";

				//A : 주문번호
				$c_str = "A";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "주문번호가 입력되지 않았습니다.";
				}else{

					$rst = $C_Settle->existsMarketOrderNoInSettle($seller_idx, $cval);
					if (!$rst) {
						$row[$c_str] .= "\n(주문번호 미존재)";
					}

					$isDup = $C_Settle->existsMarketOrderNoInLoss($seller_idx, $cval);
					if($isDup){
						$row[$c_str] .= "\n(존재하는 정산예정 - 수정)";
					}
					$market_order_no = $cval;
				}

				//D : 판매수량
				$c_str = "D";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$row[$c_str] = 0;
					$order_cnt = 0;
				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$row[$c_str] = "판매수량은 숫자만 허용됩니다.";
					}
					$row[$c_str] = $cval = abs($cval);
					$order_cnt = abs($cval);
				}

				//E : 매출금액
				$c_str = "E";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$row[$c_str] = 0;
					$order_amt = 0;
				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$row[$c_str] = "매출금액은 숫자만 허용됩니다.";
					}
					$row[$c_str] = $cval = abs($cval);
					$order_amt = $cval;
				}

				//F : 수수료
				$c_str = "F";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$row[$c_str] = 0;
					$commission = 0;
				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$row[$c_str] = "수수료는 숫자만 허용됩니다.";
					}
					$row[$c_str] = $cval = abs($cval);
					$commission = $cval;
				}

				//G : 공제/환급내역/기타수수료
				$c_str = "G";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$row[$c_str] = 0;
					$commission_etc = 0;
				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$row[$c_str] = "공제/환급내역/기타수수료은 숫자만 허용됩니다.";
					}
					$row[$c_str] = $cval = abs($cval);
					$commission_etc = $cval;
				}

				//H : 배송비
				$c_str = "H";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$row[$c_str] = 0;
					$delivery_fee = 0;
				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$row[$c_str] = "배송비는 숫자만 허용됩니다.";
					}
					$row[$c_str] = $cval = abs($cval);
					$delivery_fee = $cval;
				}

				//I : 배송비수수료
				$c_str = "I";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$row[$c_str] = 0;
					$delivery_commission = 0;
				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$row[$c_str] = "배송비수수료는 숫자만 허용됩니다.";
					}
					$row[$c_str] = $cval = abs($cval);
					$delivery_commission = $cval;
				}

				//J : 정산금액
				$c_str = "J";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = 0;
					$settle_amount = 0;
				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$row[$c_str] = "정산금액은 숫자만 허용됩니다.";
					}
					$row[$c_str] = $cval = abs($cval);
					$settle_amount = $cval;
				}


				//K : 정산일
				$c_str = "K";
				$cval = str_replace("-", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "정산일이 입력되지 않았습니다.";
				}else{
					if(strlen($cval) == 8){
						$date = date("Y-m-d", strtotime($cval));
					}else {
						$date = date("Y-m-d", \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($row[$c_str]));
					}
					$row[$c_str] = $date;
					$loss_date = $date;
				}

				$row["valid"] = $rowValid;

				//$response["err"][] = $row;
				if($act == "grid") {
					//리스트로 반환
					//$listData[] = $row;
					if($rowValid) {

						$listData[] = $row;

					}else{
						$listData[] = $row;
					}


				} elseif($act == "save") {
					//적용!!

					if($rowValid) {

						$order_name          = trim($row["B"]);
						$market_product_name = trim($row["C"]);

						$rst = $C_Settle->insertLossXls($seller_idx, $market_order_no, $order_name, $market_product_name, $order_cnt, $order_amt, $commission, $commission_etc, $delivery_fee, $delivery_commission, $settle_amount, $loss_date);

						$inserted_count++;

					}
				}
			}

			//적용일 경우 로그 남기기
			if($act == "save"){
				//$user_filename
				$rst = $C_Settle -> insertLossUploadLog($xls_filename, $user_filename, $inserted_count);
			}

			$response["result"] = true;
			$response["msg"] = $inserted_count;
		}

		if($act == "grid") {
			//그리드 리스트 리턴일 때..
			$userdata["upload_datetime"] = $upload_datetime;

			$response["page"] = 1;
			$response["records"] = count($listData);
			$response["total"] = 1;
			$response["userdata"] = array();
			$response["userdata"] = $userdata;
			$response["rows"] = $listData;
		}elseif($act == "save"){
			//적용일 때..
			$response["result"] = true;
			$response["msg"] = $inserted_count;
		}
	}elseif($mode == "delete"){

	}
}else{
	$response["msg"] = "파일이 없습니다.";
}
echo json_encode($response, true);


?>