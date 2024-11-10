<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 배송일괄처리(파일) 엑셀 처리 Process
 */
//Page Info
$pageMenuIdx = 86;
//Init
include "../_init_.php";
$C_Order = new Order();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode                   = $_POST["mode"];
$act                    = $_POST["act"];
$xls_filename           = $_POST["xls_filename"];
$user_filename          = $_POST["user_filename"];
$xls_validrow           = $_POST["xls_validrow"];

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["err"] = array();
$response["userdata"] = array("field_name" => "");

$xls_filename_fullpath = DY_XLS_UPLOAD_PATH . "/" . $xls_filename;

if(file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath)) {
	$spreadsheet = IOFactory::load($xls_filename_fullpath);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

	if ($mode == "update") {

		/*
		 * A : 송장번호
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			$row_index = 0;
			foreach($sheetData as $row)
			{
				//1개 열에 값이 하나라도 없으면 종료
				if(trim($row["A"]) == "")
				{
					continue;
				}

				$rowValid = true;

				//Row Index
				$row_index++;
				$row["xls_idx"] = $row_index;

				//정보 초기화
				$invoice_no                             = "";                //반영송장번호
				$row["order_idx"]                       = "";                //관리번호
				$row["order_progress_step_han"]         = "";                //주문진행 상태
				$row["order_is_hold"]                   = "";                //보류여부

				//A : 송장번호
				$c_str = "A";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "송장번호가 입력되지 않았습니다.";
				}else{
					$rst = $C_Order->getOrderListByInvoiceNo($cval);
					if (!$rst) {
						$rowValid = false;
						$row[$c_str] = "송장번호가 정확하지 않습니다.";
					}else{
						$invoice_no = $cval;
					}
				}

				$row["valid"] = $rowValid;


				//$response["err"][] = $row;
				if($act == "grid") {
					//리스트로 반환
					$listData[] = $row;

				} elseif($act == "save") {
					//적용!!

					//배송처리
					if($rowValid)
					{
						$rst = $C_Order->updateOrderStepToShippedByInvoiceNo($invoice_no);
						if ($rst) {
							$inserted_count++;
						}
					}
				}
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