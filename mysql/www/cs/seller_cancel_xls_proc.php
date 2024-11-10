<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처취소 엑셀 처리 Process
 */
//Page Info
$pageMenuIdx = 104;
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
$seller_idx             = $_POST["seller_idx"];

$C_CS = new CS();
$seller_format = $C_CS -> getSellerCancelFormat($seller_idx);

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["err"] = array();
$response["userdata"] = array("field_name" => "");

$xls_filename_fullpath = DY_XLS_UPLOAD_PATH . "/" . $xls_filename;

if(file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath) && $seller_format) {
	$spreadsheet = IOFactory::load($xls_filename_fullpath);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

	if ($mode == "cancel") {

		/*
		 * A : 송장번호
		 */


		if(count($sheetData) > 0) {
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
				$order_idx = "";
				$market_order_no = "";
				$market_product_no = "";
				$seller_name = "";
				$order_name = "";
				$market_product_name = "";
				$order_cnt = "";
				$reason = "";
				$cancel_date = "";
				$return_invoice_no = "";

				//존재하는 주문인지 찾는다
				$market_order_no = $row[$seller_format["market_order_no"]];

				if($market_order_no)
				{
					$_order = $C_CS->getSellerCancelOrderData($seller_idx, $market_order_no);
					if($_order){

						if($_order["order_progress_step"] == "ORDER_ACCEPT" || $_order["order_progress_step"] == "ORDER_SHIPPED") {
							$order_idx = $row["order_idx"]           = $_order["order_idx"];
							$row["order_pack_idx"]      = $_order["order_pack_idx"];
							$row["market_order_no"]     = $_order["market_order_no"];
							$row["market_product_no"]   = $_order["market_product_no"];
							$row["seller_name"]         = $_order["seller_name"];
							$row["order_name"]          = $_order["order_name"];
							$row["market_product_name"] = $_order["market_product_name"];
							$row["order_cnt"]           = $_order["order_cnt"];
						}else{
							continue;
						}
					}else{
						continue;
					}
				}

				$reason = $row[$seller_format["reason"]];
				$cancel_date = $row[$seller_format["cancel_date"]];
				$return_invoice_no = $row[$seller_format["return_invoice_no"]];

				$cancel_date = date('Y-m-d H:i:s', strtotime($cancel_date));

				$row["reason"]           = $reason;
				$row["cancel_date"]      = $cancel_date;
				$row["return_invoice_no"]      = $return_invoice_no;


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

					//송장입력 및 상태 변경
					if($rowValid) {
						//$rst = $C_Order -> updateOrderShippedToCancelByInvoiceNo($invoice_no);
						$rst = $C_CS->updateOrderCancelOneByOrderIdx($order_idx, "CS_REASON_CANCEL", "", "판매처취소", "Y", $reason, $cancel_date, $return_invoice_no);
						if($rst){
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