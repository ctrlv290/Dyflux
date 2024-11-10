<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처일괄등록 엑셀 로드 -> JSON(jqGrid)
 */
//Page Info
$pageMenuIdx = 44;
//Permission IDX
$pagePermissionIdx = 44;
//Init
include_once "../_init_.php";

$C_Seller = new Seller();
$C_Code = new Code();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode = $_GET["mode"];
$xls_filename = $_GET["xls_filename"];
$xls_filename_fullpath = DY_XLS_UPLOAD_PATH . "/" . $xls_filename;


if(file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath)) {
	$spreadsheet = IOFactory::load($xls_filename_fullpath);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

	//echo count($sheetData);
	//print_r2($sheetData);
	if(count($sheetData) > 1)
	{

		if($mode == "add") {
			//등록일때..

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

			array_shift($sheetData);
			$listData = array();
			foreach ($sheetData as $row) {
				$rowValid = true;
				if (trim($row["A"]) == "") {
					$rowValid = false;
				} else {
					$rst = $C_Code->getCodeDataByCode($row["A"]);
					if ($rst) {
						$row["market_name"] = $rst["code_name"];
					} else {
						$rowValid = false;
						$row["market_name"] = "마켓 코드가 정확하지 않습니다.";
					}
				}

				if (trim($row["B"]) == "") {
					$rowValid = false;
					$row["B"] = "판매처 명이 입력되지 않았습니다.";
				}

				$row["valid"] = $rowValid;

				$listData[] = $row;
			}

			$grid_response = array();
			$grid_response["page"] = 1;
			$grid_response["records"] = count($listData);
			$grid_response["total"] = 1;
			$grid_response["rows"] = $listData;

		}elseif($mode == "mod"){
			//수정일때..
			/*
			 * A : 판매처 코드 IDX
			 * B : 판매처(마켓) 코드
			 * C : 판매처 명
			 * D : 마켓 로그인 아이디
			 * E : 마켓 로그인 비밀번호
			 * F : 보안 코드 1
			 * G : 보안 코드 2
			 * H : 관리자 URL
			 * I : 쇼핑몰 URL
			 * J : 상품페이지 URL
			 */

			array_shift($sheetData);
			$listData = array();
			foreach ($sheetData as $row) {
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

				$listData[] = $row;
			}

			$grid_response = array();
			$grid_response["page"] = 1;
			$grid_response["records"] = count($listData);
			$grid_response["total"] = 1;
			$grid_response["rows"] = $listData;
		}
		echo json_encode($grid_response, true);

	}else{
		echo "[]";
	}
}else{
	echo "[]";
}

?>