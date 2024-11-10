<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 송장입력 엑셀 처리 Process
 */
//Page Info
$pageMenuIdx = 77;
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
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

	if ($mode == "invoice") {

		/*
		 * A : 관리번호
		 * B : 송장번호
		 * C : 택배사
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			$row_index = 0;
			$checked_order_pack_idx_ary = array();
			foreach($sheetData as $row)
			{
				//6개 열에 값이 하나라도 없으면 종료
				if(trim($row["A"]) == "" || trim($row["B"]) == "")
				{
					continue;
				}

				$rowValid = true;

				//Row Index
				$row_index++;
				$row["xls_idx"] = $row_index;

				//정보 초기화
				$order_idx                              = "";                //관리번호
				$invoice_no                             = "";                //반영송장번호
				$delivery_code                          = "";                //택배사코드
				$row["order_progress_step_accept_date"] = "";                //접수일 (발주일로 표시됨)
				$row["market_order_no"]                 = "";                //쇼핑몰 주문번호
				$row["invoice_no"]                      = "";                //기입력 된 송장번호
				$row["delivery_name"]                      = "";             //기입력 된 택배사명
				$row["order_progress_step_han"]         = "";                //주문진행 상태
				$row["order_upload_date"]               = $upload_datetime;  //업로드 시간
			    $row["invoice_date"]                    = "";                //업로드 시간

				//A : 관리번호
				$c_str = "A";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "관리번호가 입력되지 않았습니다.";
				}else{
					$rst = $C_Order->getOrderDataForInvoiceUpload($cval);
					if (!$rst) {
						$rowValid = false;
						$row[$c_str] = "관리번호가 정확하지 않습니다.";
					}else{
						$order_idx                              = $cval;
						$order_pack_idx                         = $rst["order_pack_idx"];
						$row["order_progress_step"]             = $rst["order_progress_step"];
						$row["order_progress_step_accept_date"] = $rst["order_progress_step_accept_date"];
						$row["market_order_no"]                 = $rst["market_order_no"];
						$row["invoice_no"]                      = $rst["invoice_no"];
						$row["delivery_name"]                   = $rst["delivery_name"];
						$row["order_progress_step_han"]         = $rst["order_progress_step_han"];
						$row["invoice_date"]                    = $rst["invoice_date"];

						//이미 배송 완료 인지 체크
						if ($rst["order_progress_step"] == "ORDER_SHIPPED") {
							$rowValid       = false;
							$row["err_msg"] = "이미 배송된 주문건입니다.";
						} elseif ($rst["order_progress_step"] != "ORDER_ACCEPT" && $rst["order_progress_step"] != "ORDER_INVOICE") {
							$rowValid       = false;
							$row["err_msg"] = "접수 또는 송장 상태의 주문건만 송장입력이 가능합니다.";
						}

						//합포 단위로 상품 재고를 체크하므로
						//이미 체크한 합포IDX 일 경우 Pass
						//이미 송장이 입력된 상태인 주문은 재고조사 및 차감이 되었으므로 재고조사 하지 않음
						if(!in_array($order_pack_idx, $checked_order_pack_idx_ary) && $rst["order_progress_step"] == "ORDER_ACCEPT") {
							//재고 확인
							$chkStock = $C_Order->checkOrderProductStock($order_idx, false);
							if (!$chkStock) {
								$rowValid       = false;
								$row["err_msg"] = "관련 상품의 재고부족 인하여 송장입력이 불가능합니다.";
							}

							$checked_order_pack_idx_ary[] = $order_pack_idx;
						}
					}
				}

				//B : 반영송장번호
				$c_str = "B";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$cval = str_replace("-", "", $cval);

				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "반영송장번호가 입력되지 않았습니다.";
				}else{
					$isUsed = $C_Order -> isUsedInvoiceNo($cval);

					if($isUsed){
						$rowValid = false;
						$row[$c_str] = "이미 사용중인 송장번호입니다.";
					}else{
						$invoice_no = $cval;
						$row[$c_str] = $invoice_no;
					}
				}

				//C : 택배사
				$c_str = "C";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "택배사가 입력되지 않았습니다.";
				}else{
					//택배사 코드 가져오기
					$dRst = $C_Order -> getDeliveryCodeByName($cval);

					if(!$dRst){
						$rowValid = false;
						$row[$c_str] = "등록할 수 없는 택배사입니다.";
					}else{
						$delivery_code = $dRst;
					}
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

					//송장입력 및 상태 변경
					if($rowValid) {

						//합포 단위로 상품 재고를 체크 및 차감 하므로
						//이미 체크&차감 한 경우 재고조사 를 한 것으로 간주
						$isAlreadyStockCheck = false;
						if(!in_array($order_pack_idx, $checked_order_pack_idx_ary) && $row["order_progress_step"] == "ORDER_ACCEPT") {
							$isAlreadyStockCheck = true;
						}


						$rst = $C_Order->updateOrderStepToInvoice($order_idx, $invoice_no, $delivery_code, $isAlreadyStockCheck, "XLS");

						if($rst["result"]){
							$inserted_count++;
						}else{
							$response["err"][] = array("order_idx" => $order_idx, "invoice_no" => $invoice_no, "error_msg" => $rst["msg"]);
						}

					}
				}
			}

			//적용일 경우 로그 남기기
			if($act == "save"){
				//$user_filename
				$rst = $C_Order -> insertOrderInvoiceUploadLog($xls_filename, $user_filename, $inserted_count);
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