<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공급처 일괄등록 엑셀 로드 -> JSON(jqGrid)
 */
//Page Info
$pageMenuIdx = 50;
//Init
include_once "../_init_.php";

$C_Users = new Users();
$C_Supplier = new Supplier();
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
			 * A : 공급처명
			 * B : 로그인아이디
			 * C : 로그인비밀번호
			 * D : 대표이사
			 * E : 사업자등록번호
			 * F : 담당자
			 * G : 연락처
			 * H : 휴대폰번호
			 * I : 이메일
			 * J : 주소 우편번호
			 * K : 주소 기본주소
			 * L : 주소 상세주소
			 * M : MD
			 * N : 계좌번호
			 * O : 은행
			 * P : 예금주
			 * Q : 비고
			 * R : 결제타입
			 * S : 선급금 사용여부
			 * T : 사용여부
			 */

			array_shift($sheetData);
			$listData = array();
			foreach ($sheetData as $row) {
				$rowValid = true;

				//A : 공급처 명
				if (trim($row["A"]) == "" || strlen(trim($row["A"])) > 40) {
					$rowValid = false;
					$row["A"] = "공급처명이 정확하지 않습니다.";
				}


				//B: 로그인 아이디 (DY_MEMBER 테이블 체크)
				$login_id = strtolower(trim($row["B"]));    //빈칸 제거 및 소문자화
				if ($login_id == "") {
					$rowValid = false;
					$row["B"] = "로그인 아이디가 입력되지 않았습니다.";
				}else{
					//아이디 자리 수 체크
					if(strlen($login_id) < 4 || strlen($login_id) > 12)
					{
						$rowValid = false;
						$row["B"] = "아이디는 4자 이상 12자 이하여야 합니다.";
					}else {
						//아이디 중복 체크
						if (!$C_Users->checkDupID($login_id)) {
							$rowValid = false;
							$row["B"] = "이미 등록된 아이디입니다.";
						}else{

						}
					}
				}

				//C : 로그인비밀번호
				if (strlen(trim($row["C"])) < 4 && strlen(trim($row["C"])) > 12) {
					$rowValid = false;
					$row["C"] = "로그인 비밀번호가 정확하지 않습니다. (4~12자)";
				}

				//D : 대표이사명
				if (trim($row["D"]) == "" || strlen(trim($row["D"])) > 50) {
					$rowValid = false;
					$row["D"] = "대표이사명이 정확하지 않습니다.";
				}

				//E : 사업자등록번호
				$vendor_license_number = preg_replace("/[^0-9]/", "", trim($row["E"]));    // 숫자 이외 제거
				if (trim($row["E"]) == "" || strlen($vendor_license_number) != 10) {
					$rowValid = false;
					$row["E"] = "사업자등록번호가 정확하지 않습니다.";
				}else{
					$row["E"] = preg_replace("/([0-9]{3})([0-9]{2})([0-9]{5})$/", "\\1-\\2-\\3", $vendor_license_number);
				}

				//F : 담당자
				if (trim($row["F"]) == "" || strlen(trim($row["F"])) > 30) {
					$rowValid = false;
					$row["F"] = "담당자가 정확하지 않습니다.";
				}

				//G : 연락처
				if (trim($row["G"]) == "" || strlen(trim($row["G"])) > 20) {
					$rowValid = false;
					$row["G"] = "연락처가 정확하지 않습니다.";
				}

				//H : 휴대폰번호
				if (trim($row["H"]) == "" || strlen(trim($row["H"])) > 20) {
					$rowValid = false;
					$row["H"] = "휴대폰번호가 정확하지 않습니다.";
				}

				//I : 이메일
				if (trim($row["I"]) == "" || strlen(trim($row["I"])) > 100) {
					$rowValid = false;
					$row["I"] = "이메일이 정확하지 않습니다.";
				}

				//J : 우편번호
				if (trim($row["J"]) == "" || strlen(trim($row["J"])) > 6) {
					$rowValid = false;
					$row["J"] = "우편번호가 정확하지 않습니다.";
				}

				//K : 기본주소
				if (trim($row["K"]) == "" || strlen(trim($row["K"])) > 100) {
					$rowValid = false;
					$row["K"] = "기본주소가 정확하지 않습니다.";
				}

				//M : MD
                //담당MD 필수 값 제외
//				if (trim($row["M"]) == "" || strlen(trim($row["M"])) > 50) {
//					$rowValid = false;
//					$row["M"] = "MD가 정확하지 않습니다.";
//				}

				//N : 계좌번호
				if (trim($row["N"]) == "" || strlen(trim($row["N"])) > 100) {
					$rowValid = false;
					$row["N"] = "계좌번호가 정확하지 않습니다.";
				}

				//O : 은행명
				if (trim($row["O"]) == "" || strlen(trim($row["O"])) > 50) {
					$rowValid = false;
					$row["O"] = "은행명이 정확하지 않습니다.";
				}

				//P : 예금주
				if (trim($row["P"]) == "" || strlen(trim($row["P"])) > 50) {
					$rowValid = false;
					$row["P"] = "예금주가 정확하지 않습니다.";
				}

				//Q : 비고
				if (strlen(trim($row["Q"])) > 500) {
					$rowValid = false;
					$row["Q"] = "비고가 정확하지 않습니다.";
				}

                //R : 결제타입
                if (trim($row["R"]) != "일" && trim($row["R"]) != "월") {
                    $rowValid = false;
                    $row["R"] = "결제타입이 정확하지 않습니다.";
                }

                //S : 선급금 사용여부
                if (trim($row["S"]) != "Y" && trim($row["S"]) != "N") {
                    $rowValid = false;
                    $row["S"] = "선급금 사용여부가 정확하지 않습니다.";
                }

				//T : 사용여부
				if (trim($row["T"]) != "Y" && trim($row["T"]) != "N") {
					$rowValid = false;
					$row["T"] = "사용여부가 정확하지 않습니다.";
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
			 * A : 공급처 코드
			 * B : 공급처명
			 * C : 로그인비밀번호
			 * D : 대표이사
			 * E : 사업자등록번호
			 * F : 담당자
			 * G : 연락처
			 * H : 휴대폰번호
			 * I : 이메일
			 * J : 주소 우편번호
			 * K : 주소 기본주소
			 * L : 주소 상세주소
			 * M : MD
			 * N : 계좌번호
			 * O : 은행
			 * P : 예금주
			 * Q : 비고
			 * R : 결제타입
			 * S : 선급금 사용여부
			 * T : 사용여부
			 */

			array_shift($sheetData);
			$listData = array();
			foreach ($sheetData as $row) {
				$rowValid = true;

				if (trim($row["A"]) == "") {
					$rowValid = false;
					$row["A"] = "공급처 코드가 정확하지 않습니다.";
				} else {
					$rst = $C_Vendor->isValidVendor(trim($row["A"]));
					if (!$rst) {
						$rowValid = false;
						$row["A"] = "존재하지 않는 공급처 코드 입니다.";
					}
				}

				//B : 공급처 명
				if (trim($row["B"]) == "") {
					$rowValid = false;
					$row["B"] = "공급처명이 입력되지 않았습니다.";
				}

				//C : 로그인비밀번호
				if (strlen(trim($row["C"])) < 4 && strlen(trim($row["C"])) > 12) {
					$rowValid = false;
					$row["C"] = "로그인 비밀번호가 정확하지 않습니다. (4~12자)";
				}

				//D : 대표이사명
				if (trim($row["D"]) == "" || strlen(trim($row["D"])) > 50) {
					$rowValid = false;
					$row["D"] = "대표이사명이 정확하지 않습니다.";
				}

				//E : 사업자등록번호
				$vendor_license_number = preg_replace("/[^0-9]/", "", trim($row["E"]));    // 숫자 이외 제거
				if (trim($row["E"]) == "" || strlen($vendor_license_number) != 10) {
					$rowValid = false;
					$row["E"] = "사업자등록번호가 정확하지 않습니다.";
				}else{
					$row["E"] = preg_replace("/([0-9]{3})([0-9]{2})([0-9]{5})$/", "\\1-\\2-\\3", $vendor_license_number);
				}

				//F : 담당자
				if (trim($row["F"]) == "" || strlen(trim($row["F"])) > 30) {
					$rowValid = false;
					$row["F"] = "담당자가 정확하지 않습니다.";
				}

				//G : 연락처
				if (trim($row["G"]) == "" || strlen(trim($row["G"])) > 20) {
					$rowValid = false;
					$row["G"] = "연락처가 정확하지 않습니다.";
				}

				//H : 휴대폰번호
				if (trim($row["H"]) == "" || strlen(trim($row["H"])) > 20) {
					$rowValid = false;
					$row["H"] = "휴대폰번호가 정확하지 않습니다.";
				}

				//I : 이메일
				if (trim($row["I"]) == "" || strlen(trim($row["I"])) > 100) {
					$rowValid = false;
					$row["I"] = "이메일이 정확하지 않습니다.";
				}

				//J : 우편번호
				if (trim($row["J"]) == "" || strlen(trim($row["J"])) > 6) {
					$rowValid = false;
					$row["J"] = "우편번호가 정확하지 않습니다.";
				}

				//K : 기본주소
				if (trim($row["K"]) == "" || strlen(trim($row["K"])) > 100) {
					$rowValid = false;
					$row["K"] = "기본주소가 정확하지 않습니다.";
				}

				//M : MD
				if (strlen(trim($row["M"])) > 50) {
					$rowValid = false;
					$row["M"] = "MD가 정확하지 않습니다.";
				}

				//N : 계좌번호
				if (trim($row["N"]) == "" || strlen(trim($row["N"])) > 100) {
					$rowValid = false;
					$row["N"] = "계좌번호가 정확하지 않습니다.";
				}

				//O : 은행명
				if (trim($row["O"]) == "" || strlen(trim($row["O"])) > 50) {
					$rowValid = false;
					$row["O"] = "은행명이 정확하지 않습니다.";
				}

				//P : 예금주
				if (trim($row["P"]) == "" || strlen(trim($row["P"])) > 50) {
					$rowValid = false;
					$row["P"] = "예금주가 정확하지 않습니다.";
				}

				//Q : 비고
				if (strlen(trim($row["Q"])) > 500) {
					$rowValid = false;
					$row["Q"] = "비고가 정확하지 않습니다.";
				}

                //R : 결제타입
                if (trim($row["R"]) != "일" && trim($row["R"]) != "월") {
                    $rowValid = false;
                    $row["R"] = "결제타입이 정확하지 않습니다.";
                }

                //S : 선급금 사용여부
                if (trim($row["S"]) != "Y" && trim($row["S"]) != "N") {
                    $rowValid = false;
                    $row["S"] = "선급금 사용여부가 정확하지 않습니다.";
                }

                //T : 사용여부
                if (trim($row["T"]) != "Y" && trim($row["T"]) != "N") {
                    $rowValid = false;
                    $row["T"] = "사용여부가 정확하지 않습니다.";
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