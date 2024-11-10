<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 재고 일괄 조정 Process
 */
//Page Info
$pageMenuIdx = 114;
//Init
include "../_init_.php";
$C_Product = new Product();
$C_Stock = new Stock();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode                   = $_POST["mode"];
$act                    = $_POST["act"];
$xls_filename           = $_POST["xls_filename"];
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

if(file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath)) {
	$spreadsheet = IOFactory::load($xls_filename_fullpath);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

	if ($mode == "move") {

		/*
		 * A : 상품옵션코드
		 * B : 원가
		 * C : 처리 전 상태
		 * D : 처리 후 상태
		 * E : 작업 수량
		 * F : 메모
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			$row_index = 0;
			foreach($sheetData as $row)
			{
				//6개 열에 값이 하나라도 없으면 종료
				if(trim($row["A"]) == "" || trim($row["B"]) == "" || trim($row["C"]) == "" || trim($row["D"]) == "" || trim($row["E"]) == "")
				{
					continue;
				}

				$rowValid = true;

				//Row Index
				$row_index++;
				$row["xls_idx"] = $row_index;

				//정보 초기화
				$product_option_idx     = "";
				$stock_unit_price       = "";
				$stock_move_status_prev = "";
				$stock_move_status_next = "";
				$stock_move_amount      = 0;
				$stock_move_msg         = 0;
				$product_name           = "";
				$product_option_name    = "";


				//A : 상품옵션코드
				$c_str = "A";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "상품옵션코드가 입력되지 않았습니다.";
				}else{
					$rst = $C_Product->getProductOptionData($cval);
					if (!$rst) {
						$rowValid = false;
						$row[$c_str] = "상품옵션코드가 정확하지 않습니다.";
					}else{
						$product_option_idx = $cval;
						$row["product_name"] = $rst["product_name"];
						$row["product_option_name"] = $rst["product_option_name"];
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//B : 원가
				$c_str = "B";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "원가가 입력되지 않았습니다.";

				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$row[$c_str] = "원가는 숫자만 허용됩니다.";
					}else{
						if(intval($cval) == 0){
							$rowValid = false;
							$row[$c_str] = "원가는 0이 될 수 없습니다.";
						}else{
							$stock_unit_price = $cval;
						}
					}
				}

				//C: 처리 전 상태
				$c_str = "C";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "처리 전 상태가 입력되지 않았습니다.";
				}else{
					if(!in_array($cval, $GL_controlStockToAbleStatusList, true))
					{
						$rowValid = false;
						$row[$c_str] = "처리 전 상태 값(".$cval.")이 유효하지 않습니다.";
					}else {
						$stock_move_status_prev = array_search($cval, $GL_controlStockToAbleStatusList);
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//C: 처리 후 상태
				$c_str = "D";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "처리 후 상태가 입력되지 않았습니다.";
				}else{
					if(!in_array($cval, $GL_controlStockFromAbleStatusList, true))
					{
						$rowValid = false;
						$row[$c_str] = "처리 전 상태 값(".$cval.")이 유효하지 않습니다.";
					}else {
						$stock_move_status_next = array_search($cval, $GL_controlStockFromAbleStatusList);
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//E : 작업 수량
				$c_str = "E";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$row[$c_str] = "작업수량이 입력되지 않았습니다.";

				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$row[$c_str] = "작업수량은 숫자만 허용됩니다.";
					}else{
						if(intval($cval) == 0){
							$rowValid = false;
							$row[$c_str] = "작업수량은 0이 될 수 없습니다.";
						}else{
							$stock_move_amount = $cval;
						}
					}
				}

				//처리 전, 처리 후 상태 비교
				if($stock_move_status_prev == $stock_move_status_next){
					$rowValid = false;
					$row["err_msg"] = "처리 전 상태와 처리 후 상태는 같을 수 없습니다.";
				}

				//재고 작업 가능 수량 확인
				$current_stock_amount = $C_Stock->getCurrentStockAmountByPrice($product_option_idx, $stock_move_status_prev, $stock_unit_price);
				$row["current_stock_amount"] = $current_stock_amount;
				if($current_stock_amount < $stock_move_amount){
					$rowValid = false;
					$row["err_msg"] = "작업 가능 수량이 모자랍니다.";
				}

				$row["valid"] = $rowValid;

				//$response["err"][] = $row;

				if($act == "grid") {
					//리스트로 반환
					//$listData[] = $row;
					if($rowValid) {

						//매칭 상품 1

						$listData[] = $row;

					}else{
						$listData[] = $row;
					}


				} elseif($act == "save") {
					//적용!!

					if($rowValid) {

						$rst = $C_Stock -> controlStockAmount($product_option_idx, $stock_unit_price, $stock_move_status_prev, $stock_move_status_next, $stock_move_amount, "[재고일괄조정]".$row["F"]);
						if($rst["result"]){
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
			$response["page"] = 1;
			$response["records"] = count($listData);
			$response["total"] = 1;
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