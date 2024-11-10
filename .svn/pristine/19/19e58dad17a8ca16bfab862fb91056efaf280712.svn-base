<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 벤더사 일괄등록 관련 Process (excel)
 */
//Page Info
$pageMenuIdx = 176;
//Init
include "../_init_.php";

$C_Users = new Users();
$C_Product = new Product();

$C_Code = new Code();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode                   = $_POST["mode"];
$act                    = $_POST["act"];
$xls_filename           = $_POST["xls_filename"];
$xls_validrow           = $_POST["xls_validrow"];
$product_idx            = $_POST["product_idx"];


$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["err"] = array();

$xls_filename_fullpath = DY_XLS_UPLOAD_PATH . "/" . $xls_filename;

if(file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath)) {
	$spreadsheet = IOFactory::load($xls_filename_fullpath);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

	//!!!!배열 전체를 검색하여 엔터 값을 빈칸으로 치환 [사용 함수 : removeXlsLinebreak()]
	array_walk_recursive($sheetData, 'removeXlsLinebreak');

	if ($mode == "add") {

		/*
		 * A : 상품코드
		 * B : 옵션명
		 * C : 판매기준가
		 * D : 판매가 (A등급)
		 * E : 판매가 (B등급)
		 * F : 판매가 (C등급)
		 * G : 판매가 (D등급)
		 * H : 판매가 (E등급)
		 * I : 매입가
		 * J : 재고경고수량
		 * K : 재고위협수량
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			foreach($sheetData as $row)
			{
				$rowValid = true;

				//A : 상품코드
				if (trim($row["A"]) == "") {
					$rowValid = false;
					$row["A"] = "상품코드가 입력되지 않았습니다.";
				}else{
					if(trim($row["A"] != $product_idx))
					{
						$rowValid = false;
						$row["A"] = "현재 선택된 상품의 상품코드가 아닙니다.";
					}else {
						$rst = $C_Product->getProductData(trim($row["A"]));
						if (!$rst) {
							$rowValid = false;
							$row["A"] = "상품코드가 정확하지 않습니다.";
						}
					}
				}

				//B: 옵션명
				if (trim($row["B"]) == "") {
					$rowValid = false;
					$row["B"] = "옵션명이 입력되지 않았습니다.";
				}

				//C: 판매기준가
				if (trim($row["C"]) == "") {
					$rowValid = false;
					$row["C"] = "판매기준가가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["C"]))){
						$rowValid = false;
						$row["C"] = "판매기준가는 숫자만 허용됩니다.";
					}else{
						if(intval($row["C"]) == 0){
							$rowValid = false;
							$row["C"] = "판매기준가는 0이 될 수 없습니다.";
						}
					}
				}

				//D: 판매가 (A등급)
				if (trim($row["D"]) == "") {
					$rowValid = false;
					$row["D"] = "판매가 (A등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["D"]))){
						$rowValid = false;
						$row["D"] = "판매가 (A등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["D"]) == 0){
							$rowValid = false;
							$row["D"] = "판매가 (A등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//E: 판매가 (B등급)
				if (trim($row["E"]) == "") {
					$rowValid = false;
					$row["E"] = "판매가 (B등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["E"]))){
						$rowValid = false;
						$row["E"] = "판매가 (B등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["E"]) == 0){
							$rowValid = false;
							$row["E"] = "판매가 (B등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//F: 판매가 (C등급)
				if (trim($row["F"]) == "") {
					$rowValid = false;
					$row["F"] = "판매가 (C등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["F"]))){
						$rowValid = false;
						$row["F"] = "판매가 (C등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["F"]) == 0){
							$rowValid = false;
							$row["F"] = "판매가 (C등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//G: 판매가 (D등급)
				if (trim($row["G"]) == "") {
					$rowValid = false;
					$row["G"] = "판매가 (D등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["G"]))){
						$rowValid = false;
						$row["G"] = "판매가 (D등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["G"]) == 0){
							$rowValid = false;
							$row["G"] = "판매가 (D등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//G: 판매가 (E등급)
				if (trim($row["H"]) == "") {
					$rowValid = false;
					$row["H"] = "판매가 (E등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["H"]))){
						$rowValid = false;
						$row["H"] = "판매가 (E등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["H"]) == 0){
							$rowValid = false;
							$row["H"] = "판매가 (E등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//I : 매입가
				if (trim($row["I"]) == "") {
					$row["I"] = 0;
				}else{
					if(!is_numeric(trim($row["I"]))){
						$rowValid = false;
						$row["I"] = "매입가는 숫자만 허용됩니다.";
					}
				}

				//J : 재고경고수량
				if (trim($row["J"]) == "") {
					$row["J"] = 0;
				}else{
					if(!is_numeric(trim($row["J"]))){
						$rowValid = false;
						$row["J"] = "재고경고수량은 숫자만 허용됩니다.";
					}
				}

				//K : 재고위협수량
				if (trim($row["K"]) == "") {
					$row["K"] = 0;
				}else{
					if(!is_numeric(trim($row["K"]))){
						$rowValid = false;
						$row["K"] = "재고위협수량은 숫자만 허용됩니다.";
					}
				}

				$row["valid"] = $rowValid;

				if($act == "grid") {
					//리스트로 반환
					$listData[] = $row;

				} elseif($act == "save") {
					//적용!!

					if ($rowValid) {
						$args = array();
						$args["product_idx"] = trim($row["A"]);
						$args["product_option_name"] = trim($row["B"]);
						$args["product_option_sale_price"] = trim($row["C"]);
						$args["product_option_sale_price_A"] = trim($row["D"]);
						$args["product_option_sale_price_B"] = trim($row["E"]);
						$args["product_option_sale_price_C"] = trim($row["F"]);
						$args["product_option_sale_price_D"] = trim($row["G"]);
						$args["product_option_sale_price_E"] = trim($row["H"]);
						$args["product_option_purchase_price"] = trim($row["I"]);
						$args["product_option_warning_count"] = trim($row["J"]);
						$args["product_option_danger_count"] = trim($row["K"]);

						$tmp_idx = $C_Product -> insertProductOption($args);

						$inserted_count++;
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

	} elseif ($mode == "mod") {

		/*
		 * A : 상품코드
		 * B : 옵션코드
		 * C : 옵션명
		 * D : 판매기준가
		 * E : 판매가 (A등급)
		 * F : 판매가 (B등급)
		 * G : 판매가 (C등급)
		 * H : 판매가 (D등급)
		 * I : 판매가 (E등급)
		 * J : 매입가
		 * K : 재고경고수량
		 * L : 재고위협수량
		 */


		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			foreach($sheetData as $row)
			{
				$rowValid = true;

				//A : 상품코드
				if (trim($row["A"]) == "") {
					$rowValid = false;
					$row["A"] = "상품코드가 입력되지 않았습니다.";
				}else{
					if(trim($row["A"] != $product_idx))
					{
						$rowValid = false;
						$row["A"] = "현재 선택된 상품의 상품코드가 아닙니다.";
					}else {
						$rst = $C_Product->getProductData(trim($row["A"]));
						if (!$rst) {
							$rowValid = false;
							$row["A"] = "상품코드가 정확하지 않습니다.";
						}
					}
				}

				//B : 옵션코드
				if (trim($row["B"]) == "") {
					$rowValid = false;
					$row["B"] = "옵션코드가 입력되지 않았습니다.";
				}else{
					$rst = $C_Product->getProductOptionData(trim($row["B"]));
					if (!$rst) {
						$rowValid = false;
						$row["B"] = "옵션코드가 정확하지 않습니다.";
					}
				}

				//C: 옵션명
				if (trim($row["C"]) == "") {
					$rowValid = false;
					$row["C"] = "옵션명이 입력되지 않았습니다.";
				}

				//D: 판매기준가
				if (trim($row["D"]) == "") {
					$rowValid = false;
					$row["D"] = "판매기준가가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["D"]))){
						$rowValid = false;
						$row["D"] = "판매기준가는 숫자만 허용됩니다.";
					}else{
						if(intval($row["D"]) == 0){
							$rowValid = false;
							$row["D"] = "판매기준가는 0이 될 수 없습니다.";
						}
					}
				}

				//E: 판매가 (A등급)
				if (trim($row["E"]) == "") {
					$rowValid = false;
					$row["E"] = "판매가 (A등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["E"]))){
						$rowValid = false;
						$row["E"] = "판매가 (A등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["E"]) == 0){
							$rowValid = false;
							$row["E"] = "판매가 (A등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//F: 판매가 (B등급)
				if (trim($row["F"]) == "") {
					$rowValid = false;
					$row["F"] = "판매가 (B등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["F"]))){
						$rowValid = false;
						$row["F"] = "판매가 (B등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["F"]) == 0){
							$rowValid = false;
							$row["F"] = "판매가 (B등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//G: 판매가 (C등급)
				if (trim($row["G"]) == "") {
					$rowValid = false;
					$row["G"] = "판매가 (C등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["G"]))){
						$rowValid = false;
						$row["G"] = "판매가 (C등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["G"]) == 0){
							$rowValid = false;
							$row["G"] = "판매가 (C등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//H: 판매가 (D등급)
				if (trim($row["H"]) == "") {
					$rowValid = false;
					$row["H"] = "판매가 (D등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["H"]))){
						$rowValid = false;
						$row["H"] = "판매가 (D등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["H"]) == 0){
							$rowValid = false;
							$row["H"] = "판매가 (D등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//I: 판매가 (E등급)
				if (trim($row["I"]) == "") {
					$rowValid = false;
					$row["I"] = "판매가 (E등급)가 입력되지 않았습니다.";
				}else{
					if(!is_numeric(trim($row["I"]))){
						$rowValid = false;
						$row["I"] = "판매가 (E등급)는 숫자만 허용됩니다.";
					}else{
						if(intval($row["I"]) == 0){
							$rowValid = false;
							$row["I"] = "판매가 (E등급)는 0이 될 수 없습니다.";
						}
					}
				}

				//J : 매입가
				if (trim($row["J"]) == "") {
					$row["J"] = 0;
				}else{
					if(!is_numeric(trim($row["J"]))){
						$rowValid = false;
						$row["J"] = "매입가는 숫자만 허용됩니다.";
					}
				}

				//K : 재고경고수량
				if (trim($row["K"]) == "") {
					$row["K"] = 0;
				}else{
					if(!is_numeric(trim($row["K"]))){
						$rowValid = false;
						$row["K"] = "재고경고수량은 숫자만 허용됩니다.";
					}
				}

				//L : 재고위협수량
				if (trim($row["L"]) == "") {
					$row["L"] = 0;
				}else{
					if(!is_numeric(trim($row["L"]))){
						$rowValid = false;
						$row["L"] = "재고위협수량은 숫자만 허용됩니다.";
					}
				}

				$row["valid"] = $rowValid;
				$response["err"][] = $row;

				if($act == "grid") {
					//리스트로 반환
					$listData[] = $row;

				} elseif($act == "save") {
					//적용!!
					if ($rowValid) {
						$args = array();
						$args["product_idx"] = trim($row["A"]);
						$args["product_option_idx"] = trim($row["B"]);
						$args["product_option_name"] = trim($row["C"]);
						$args["product_option_sale_price"] = trim($row["D"]);
						$args["product_option_sale_price_A"] = trim($row["E"]);
						$args["product_option_sale_price_B"] = trim($row["F"]);
						$args["product_option_sale_price_C"] = trim($row["G"]);
						$args["product_option_sale_price_D"] = trim($row["H"]);
						$args["product_option_sale_price_E"] = trim($row["I"]);
						$args["product_option_purchase_price"] = trim($row["J"]);
						$args["product_option_warning_count"] = trim($row["K"]);
						$args["product_option_danger_count"] = trim($row["K"]);

						$tmp_idx = $C_Product->updateProductOption($args);

						$inserted_count++;
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
	}
}else{
	$response["msg"] = "파일이 없습니다.";
}
echo json_encode($response, true);
?>