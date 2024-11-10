<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 벤더사 일괄등록 관련 Process (excel)
 */
//Page Info
$pageMenuIdx = 47;
//Init
include "../_init_.php";

$C_Users = new Users();
$C_Vendor = new Vendor();

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

	if ($mode == "add") {

		/*
		 * A : 벤더사명
		 * B : 등급
		 * C : 로그인아이디
		 * D : 로그인비밀번호
		 * E : 대표이사
		 * F : 사업자등록번호
		 * G : 담당자
		 * H : 연락처
		 * I : 휴대폰번호
		 * J : 이메일
		 * K : 주소 우편번호
		 * L : 주소 기본주소
		 * M : 주소 상세주소
		 * N : MD
		 * O : 계좌번호
		 * P : 은행
		 * Q : 예금주
		 * R : 비고
		 * S : 사용여부
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			foreach($sheetData as $row)
			{
				$rowValid = true;

				//A : 벤더사 명
				if (trim($row["A"]) == "") {
					$rowValid = false;
					$row["B"] = "벤더사명이 입력되지 않았습니다.";
				}

				//B: 벤더사 등급 (A, B, C, D, E 중 하나)
				$vendor_grade = strtoupper(trim($row["B"]));
				if (!in_array($vendor_grade, array("A", "B", "C", "D", "E"))) {
					$rowValid = false;
					$row["C"] = "벤더사 등급이 올바르지 않습니다.";
				}

				//C: 로그인 아이디 (DY_MEMBER 테이블 체크)
				$login_id = strtolower(trim($row["C"]));    //빈칸 제거 및 소문자화
				if ($login_id == "") {
					$rowValid = false;
					$row["C"] = "로그인 아이디가 입력되지 않았습니다.";
				}else{
					//아이디 자리 수 체크
					if(strlen($login_id) < 4 || strlen($login_id) > 12 || preg_match("/[^a-zA-Z0-9\-\_]/", $login_id))
					{
						$rowValid = false;
						$row["C"] = "아이디는 4자 이상 12자 이하의 영문, 숫자, -, _ 만 가능 합니다.";
					}else {
						//아이디 중복 체크
						if (!$C_Users->checkDupID($login_id)) {
							$rowValid = false;
							$row["C"] = "이미 등록된 아이디입니다.";
						}else{

						}
					}
				}

				//D : 로그인비밀번호
				if (strlen(trim($row["D"])) < 4 && strlen(trim($row["D"])) > 12) {
					$rowValid = false;
					$row["D"] = "로그인 비밀번호가 정확하지 않습니다. (4~12자)";
				}

				//D : 대표이사명
				if (trim($row["E"]) == "" || strlen(trim($row["E"])) > 50) {
					$rowValid = false;
					$row["E"] = "대표이사명이 정확하지 않습니다.";
				}

				//F : 사업자등록번호
				$vendor_license_number = preg_replace("/[^0-9]/", "", trim($row["F"]));    // 숫자 이외 제거
				if (trim($row["F"]) == "" || strlen($vendor_license_number) != 10) {
					$rowValid = false;
					$row["F"] = "사업자등록번호가 정확하지 않습니다.";
				}else{
					$row["F"] = preg_replace("/([0-9]{3})([0-9]{2})([0-9]{5})$/", "\\1-\\2-\\3", $vendor_license_number);
				}

				//G : 담당자
				if (trim($row["G"]) == "" || strlen(trim($row["G"])) > 30) {
					$rowValid = false;
					$row["G"] = "담당자가 정확하지 않습니다.";
				}

				//H : 연락처
				if (trim($row["H"]) == "" || strlen(trim($row["H"])) > 20) {
					$rowValid = false;
					$row["H"] = "연락처가 정확하지 않습니다.";
				}

				//I : 휴대폰번호
				if (trim($row["I"]) == "" || strlen(trim($row["I"])) > 20) {
					$rowValid = false;
					$row["I"] = "휴대폰번호가 정확하지 않습니다.";
				}

				//J : 이메일
				if (trim($row["J"]) == "" || strlen(trim($row["J"])) > 100) {
					$rowValid = false;
					$row["J"] = "이메일이 정확하지 않습니다.";
				}

				//K : 우편번호
				if (trim($row["K"]) == "" || strlen(trim($row["K"])) > 6) {
					$rowValid = false;
					$row["K"] = "우편번호가 정확하지 않습니다.";
				}

				//L : 기본주소
				if (trim($row["L"]) == "" || strlen(trim($row["L"])) > 100) {
					$rowValid = false;
					$row["L"] = "기본주소가 정확하지 않습니다.";
				}

				//N : MD
				//담당MD 필수 값 제외
//				if (trim($row["N"]) == "" || strlen(trim($row["N"])) > 50) {
//					$rowValid = false;
//					$row["N"] = "MD가 정확하지 않습니다.";
//				}

				//O : 계좌번호
				if (trim($row["O"]) == "" || strlen(trim($row["O"])) > 100) {
					$rowValid = false;
					$row["O"] = "계좌번호가 정확하지 않습니다.";
				}

				//P : 은행명
				if (trim($row["P"]) == "" || strlen(trim($row["P"])) > 50) {
					$rowValid = false;
					$row["P"] = "은행명이 정확하지 않습니다.";
				}

				//Q : 예금주
				if (trim($row["Q"]) == "" || strlen(trim($row["Q"])) > 50) {
					$rowValid = false;
					$row["Q"] = "예금주가 정확하지 않습니다.";
				}

				//R : 비고
				if (strlen(trim($row["R"])) > 500) {
					$rowValid = false;
					$row["R"] = "비고가 정확하지 않습니다.";
				}

				//S : 충전금 사용여부
				if (trim($row["S"]) != "Y" && trim($row["S"]) != "N") {
					$rowValid = false;
					$row["S"] = "충전금 사용여부가 정확하지 않습니다.";
				}

				//T : 사용여부
				if (trim($row["T"]) != "Y" && trim($row["T"]) != "N") {
					$rowValid = false;
					$row["T"] = "사용여부가 정확하지 않습니다.";
				}

				$row["valid"] = $rowValid;

				if($act == "grid") {
					//리스트로 반환
					$listData[] = $row;

				} elseif($act == "save") {
					//적용!!

					if ($rowValid) {
						$args = array();
						$args["login_id"] = strtolower(trim($row["C"]));
						$args["login_pw"] = crypt(trim($row["D"]), DY_PASSWORD_SALT);

						$args["vendor_name"] = trim($row["A"]);
						$args["manage_group_idx"] = 0;
						$args["vendor_grade"] = trim($row["B"]);
						$args["vendor_ceo_name"] = trim($row["E"]);
						$args["vendor_license_number"] = trim($row["F"]);
						$args["vendor_zipcode"] = trim($row["K"]);
						$args["vendor_addr1"] = trim($row["L"]);
						$args["vendor_addr2"] = trim($row["M"]);
						$args["vendor_fax"] = "";
						$args["vendor_startdate"] = "";
						$args["vendor_enddate"] = "";
						$args["vendor_license_file"] = 0;
						$args["vendor_bank_account_number"] = trim($row["O"]);
						$args["vendor_bank_name"] = trim($row["P"]);
						$args["vendor_bank_holder_name"] = trim($row["Q"]);
						$args["vendor_bank_book_copy_file"] = 0;
						$args["vendor_email_default"] = trim($row["J"]);
						$args["vendor_email_account"] = trim($row["J"]);
						$args["vendor_email_order"] = trim($row["J"]);

						$args["vendor_officer1_name"] = trim($row["G"]);
						$args["vendor_officer1_tel"] = trim($row["H"]);
						$args["vendor_officer1_mobile"] = trim($row["I"]);
						$args["vendor_officer1_email"] = trim($row["J"]);

						$args["vendor_md"] = trim($row["N"]);
						$args["vendor_etc"] = trim($row["R"]);
						$args["vendor_use_charge"] = trim($row["S"]);
						$args["is_use"] = trim($row["T"]);

						$tmp_idx = $C_Vendor->insertVendor($args);

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
		 * A : 벤더사 코드
		 * B : 벤더사명
		 * C : 등급
		 * D : 로그인아이디
		 * E : 로그인비밀번호
		 * F : 대표이사
		 * G : 사업자등록번호
		 * H : 담당자
		 * I : 연락처
		 * J : 휴대폰번호
		 * K : 이메일
		 * L : 주소 우편번호
		 * M : 주소 기본주소
		 * N : 주소 상세주소
		 * O : MD
		 * P : 계좌번호
		 * Q : 은행
		 * R : 예금주
		 * S : 비고
		 * T : 사용여부
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			foreach($sheetData as $row)
			{
				$rowValid = true;

				//A : 벤더사 코드
				if (trim($row["A"]) == "") {
					$rowValid = false;
					$row["A"] = "벤더사 코드가 정확하지 않습니다.";
				} else {
					$rst = $C_Vendor->isValidVendor(trim($row["A"]));
					if (!$rst) {
						$rowValid = false;
						$row["A"] = "벤더사 코드가 정확하지 않습니다.";
					}
				}

				//B : 벤더사 명
				if (trim($row["B"]) == "" || strlen(trim($row["B"])) > 40) {
					$rowValid = false;
					$row["B"] = "벤더사명이 정확하지 않습니다.";
				}

				//C: 벤더사 등급 (A, B, C, D, E 중 하나)
				$vendor_grade = strtoupper(trim($row["C"]));
				if (!in_array($vendor_grade, array("A", "B", "C", "D", "E"))) {
					$rowValid = false;
					$row["C"] = "벤더사 등급이 올바르지 않습니다.";
				}

				//D : 로그인비밀번호
				if (strlen(trim($row["D"])) < 4 && strlen(trim($row["D"])) > 12) {
					$rowValid = false;
					$row["D"] = "로그인 비밀번호가 정확하지 않습니다. (4~12자)";
				}

				//D : 대표이사명
				if (trim($row["E"]) == "" || strlen(trim($row["E"])) > 50) {
					$rowValid = false;
					$row["E"] = "대표이사명이 정확하지 않습니다.";
				}

				//F : 사업자등록번호
				$vendor_license_number = preg_replace("/[^0-9]/", "", trim($row["F"]));    // 숫자 이외 제거
				if (trim($row["F"]) == "" || strlen($vendor_license_number) != 10) {
					$rowValid = false;
					$row["F"] = "사업자등록번호가 정확하지 않습니다.";
				}else{
					$row["F"] = preg_replace("/([0-9]{3})([0-9]{2})([0-9]{5})$/", "\\1-\\2-\\3", $vendor_license_number);
				}

				//G : 담당자
				if (trim($row["G"]) == "" || strlen(trim($row["G"])) > 30) {
					$rowValid = false;
					$row["G"] = "담당자가 정확하지 않습니다.";
				}

				//H : 연락처
				if (trim($row["H"]) == "" || strlen(trim($row["H"])) > 20) {
					$rowValid = false;
					$row["H"] = "연락처가 정확하지 않습니다.";
				}

				//I : 휴대폰번호
				if (trim($row["I"]) == "" || strlen(trim($row["I"])) > 20) {
					$rowValid = false;
					$row["I"] = "휴대폰번호가 정확하지 않습니다.";
				}

				//J : 이메일
				if (trim($row["J"]) == "" || strlen(trim($row["J"])) > 100) {
					$rowValid = false;
					$row["J"] = "이메일이 정확하지 않습니다.";
				}

				//K : 우편번호
				if (trim($row["K"]) == "" || strlen(trim($row["K"])) > 6) {
					$rowValid = false;
					$row["K"] = "우편번호가 정확하지 않습니다.";
				}

				//L : 기본주소
				if (trim($row["L"]) == "" || strlen(trim($row["L"])) > 100) {
					$rowValid = false;
					$row["L"] = "기본주소가 정확하지 않습니다.";
				}

				//N : MD
				if (trim($row["N"]) == "" || strlen(trim($row["N"])) > 50) {
					$rowValid = false;
					$row["L"] = "MD가 정확하지 않습니다.";
				}

				//O : 계좌번호
				if (trim($row["O"]) == "" || strlen(trim($row["O"])) > 100) {
					$rowValid = false;
					$row["O"] = "계좌번호가 정확하지 않습니다.";
				}

				//P : 은행명
				if (trim($row["P"]) == "" || strlen(trim($row["P"])) > 50) {
					$rowValid = false;
					$row["P"] = "은행명이 정확하지 않습니다.";
				}

				//Q : 예금주
				if (trim($row["Q"]) == "" || strlen(trim($row["Q"])) > 50) {
					$rowValid = false;
					$row["Q"] = "예금주가 정확하지 않습니다.";
				}

				//R : 비고
				if (strlen(trim($row["R"])) > 500) {
					$rowValid = false;
					$row["R"] = "비고가 정확하지 않습니다.";
				}

				//S : 충전금 사용여부
				if (trim($row["S"]) != "Y" && trim($row["S"]) != "N") {
					$rowValid = false;
					$row["S"] = "사용여부가 정확하지 않습니다.";
				}

				//T : 사용여부
				if (trim($row["T"]) != "Y" && trim($row["T"]) != "N") {
					$rowValid = false;
					$row["T"] = "사용여부가 정확하지 않습니다.";
				}

				$row["valid"] = $rowValid;

				if($act == "grid") {
					//리스트로 반환
					$listData[] = $row;

				} elseif($act == "save") {
					//적용!!
					if ($rowValid) {
						$args = array();
						$args["idx"] = strtolower(trim($row["A"]));
						$args["login_pw"] = crypt(trim($row["D"]), DY_PASSWORD_SALT);

						$args["vendor_name"] = trim($row["B"]);
						$args["manage_group_idx"] = null;
						$args["vendor_grade"] = trim($row["C"]);
						$args["vendor_ceo_name"] = trim($row["E"]);
						$args["vendor_license_number"] = trim($row["F"]);
						$args["vendor_zipcode"] = trim($row["K"]);
						$args["vendor_addr1"] = trim($row["L"]);
						$args["vendor_addr2"] = trim($row["M"]);
						$args["vendor_fax"] = null;
						$args["vendor_startdate"] = null;
						$args["vendor_enddate"] = null;
						$args["vendor_license_file"] = null;
						$args["vendor_bank_account_number"] = trim($row["O"]);
						$args["vendor_bank_name"] = trim($row["P"]);
						$args["vendor_bank_holder_name"] = trim($row["Q"]);
						$args["vendor_bank_book_copy_file"] = null;
						$args["vendor_email_default"] = null;
						$args["vendor_email_account"] = null;
						$args["vendor_email_order"] = null;

						$args["vendor_officer1_name"] = trim($row["G"]);
						$args["vendor_officer1_tel"] = trim($row["H"]);
						$args["vendor_officer1_mobile"] = trim($row["I"]);
						$args["vendor_officer1_email"] = trim($row["J"]);

						$args["vendor_officer2_name"] = null;
						$args["vendor_officer2_tel"] = null;
						$args["vendor_officer2_mobile"] = null;
						$args["vendor_officer2_email"] = null;

						$args["vendor_officer3_name"] = null;
						$args["vendor_officer3_tel"] = null;
						$args["vendor_officer3_mobile"] = null;
						$args["vendor_officer3_email"] = null;

						$args["vendor_officer4_name"] = null;
						$args["vendor_officer4_tel"] = null;
						$args["vendor_officer4_mobile"] = null;
						$args["vendor_officer4_email"] = null;

						$args["vendor_md"] = trim($row["N"]);
						$args["vendor_etc"] = trim($row["R"]);
						$args["vendor_use_charge"] = trim($row["S"]);
						$args["is_use"] = trim($row["T"]);

						$tmp_idx = $C_Vendor->updateVendor($args);

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