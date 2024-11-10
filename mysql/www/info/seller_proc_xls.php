<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처 일괄등록 관련 Process (excel)
 */
//Page Info
$pageMenuIdx = 44;
//Permission IDX
$pagePermissionIdx = 43;
//Init
include "../_init_.php";

$C_Seller = new Seller();

$C_Code = new Code();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode                   = $_POST["mode"];
$act                    = $_POST["act"];
$xls_filename           = $_POST["xls_filename"];
$xls_validrow           = $_POST["xls_validrow"];

$response = array();
$response["result"] = false;
$response["msg"] = "";

$xls_filename_fullpath = DY_XLS_UPLOAD_PATH . "/" . $xls_filename;

if(file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath)) {
	$spreadsheet = IOFactory::load($xls_filename_fullpath);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

	/*
	 * A : 판매처(마켓) 코드
	 * B : 판매처 명
	 * C : 마켓 로그인 아이디
	 * D : 마켓 로그인 비밀번호
	 * E : 보안 코드 1
	 * F : 보안 코드 2
	 * G : 관리자 URL
	 * H : 쇼핑몰 URL
	 * I : 상품페이지 URL
	 */

	if ($mode == "add") {
		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			foreach($sheetData as $row)
			{
				$rowValid = true;
				if(trim($row["A"]) == "") {
					$rowValid = false;
				}else{
					$rst = $C_Code->getCodeDataByCode($row["A"]);
					if($rst) {
						$row["market_name"] = $rst["code_name"];
					}else{
						$rowValid = false;
						$row["market_name"] = "판매처 코드가 정확하지 않습니다.";
					}
				}

				if(trim($row["B"]) == "") {
					$rowValid = false;
				}

				$row["valid"] = $rowValid;

				if($act == "grid") {
					//리스트로 반환
					$listData[] = $row;

				} elseif($act == "save") {
					//적용!!
					if ($rowValid) {
						$args = array();
						$args["market_type"] = "MARKET_SELLER";
						$args["market_code"] = trim($row["A"]);
						$args["seller_name"] = trim($row["B"]);
						$args["manage_group_idx"] = 0;
						$args["market_login_id"] = trim($row["C"]);
						$args["market_login_pw"] = trim($row["D"]);
						$args["market_auth_code"] = trim($row["E"]);
						$args["market_auth_code2"] = trim($row["F"]);
						$args["market_admin_url"] = trim($row["G"]);
						$args["market_mall_url"] = trim($row["H"]);
						$args["market_product_url"] = trim($row["I"]);
						$args["seller_auto_order"] = "Y";
						$args["seller_is_use"] = "Y";

						$C_Seller->insertSeller($args);

						$inserted_count++;
					}
				}
			}
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
		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			foreach($sheetData as $row)
			{
				$rowValid = true;
				if (trim($row["A"]) == "") {
					$rowValid = false;
					$row["A"] = "판매처 코드가 정확하지 않습니다.";
				} else {
					$rst = $C_Seller->isValidSeller(trim($row["A"]));
					if (!$rst) {
						$rowValid = false;
						//$row["market_name"] = "존재하지 않는 판매처 코드 입니다.";
					}
				}

				if (trim($row["B"]) == "") {
					$rowValid = false;
				} else {
					$rst = $C_Code->getCodeDataByCode($row["B"]);
					if ($rst) {
						$row["market_name"] = $rst["code_name"];
					} else {
						$rowValid = false;
						$row["market_name"] = "마켓 코드가 정확하지 않습니다.";
					}
				}

				if (trim($row["C"]) == "") {
					$rowValid = false;
					$row["C"] = "판매처 명이 입력되지 않았습니다.";
				}

				$row["valid"] = $rowValid;

				if($act == "grid") {
					//리스트로 반환
					$listData[] = $row;

				} elseif($act == "save") {
					//적용!!
					if ($rowValid) {
						$args = array();
						$args["seller_idx"] = $row["A"];
						$args["market_type"] = "MARKET_SELLER";
						$args["market_code"] = trim($row["B"]);
						$args["seller_name"] = trim($row["C"]);
						$args["market_login_id"] = trim($row["D"]);
						$args["market_login_pw"] = trim($row["E"]);
						$args["market_auth_code"] = trim($row["F"]);
						$args["market_auth_code2"] = trim($row["G"]);
						$args["market_admin_url"] = trim($row["H"]);
						$args["market_mall_url"] = trim($row["I"]);
						$args["market_product_url"] = trim($row["J"]);
						$args["manage_group_idx"] = null;
						$args["seller_auto_order"] = null;
						$args["seller_invoice_product"] = null;
						$args["seller_invoice_option"] = null;
						$args["seller_is_use"] = null;

						$C_Seller->updateSeller($args);

						$inserted_count++;
					}
				}
			}
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