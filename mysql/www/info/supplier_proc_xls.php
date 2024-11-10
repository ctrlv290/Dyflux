<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 공급처 일괄등록 관련 Process (excel)
 */
//Page Info
$pageMenuIdx = 50;
//Init
include "../_init_.php";

$C_Users = new Users();
$C_Supplier = new Supplier();

$C_Code = new Code();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode                   = $_POST["mode"];
$act                    = $_POST["rst"];
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

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			foreach($sheetData as $row)
			{
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
					if(strlen($login_id) < 4 || strlen($login_id) > 12 || preg_match("/[^a-zA-Z0-9\-\_]/", $login_id))
					{
						$rowValid = false;
						$row["B"] = "아이디는 4자 이상 12자 이하의 영문, 숫자, -, _ 만 가능합니다.";
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
				$supplier_license_number = preg_replace("/[^0-9]/", "", trim($row["E"]));    // 숫자 이외 제거
				if (trim($row["E"]) == "" || strlen($supplier_license_number) != 10) {
					$rowValid = false;
					$row["E"] = "사업자등록번호가 정확하지 않습니다.";
				}else{
					$row["E"] = preg_replace("/([0-9]{3})([0-9]{2})([0-9]{5})$/", "\\1-\\2-\\3", $supplier_license_number);
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
                }else{
                    if(trim($row["R"]) == "일" ){
                        $row["R"] = "DAY";
                    }else{
                        $row["R"] = "MONTH";
                    }
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

				if($act == "grid") {
					//리스트로 반환
					$listData[] = $row;

				} elseif($act == "save") {
					//적용!!
					if ($rowValid) {
						$args = array();
						$args["login_id"] = strtolower(trim($row["B"]));
						$args["login_pw"] = crypt(trim($row["C"]), DY_PASSWORD_SALT);

						$args["supplier_name"] = trim($row["A"]);
						$args["manage_group_idx"] = 0;
						$args["supplier_ceo_name"] = trim($row["D"]);
						$args["supplier_license_number"] = trim($row["E"]);
						$args["supplier_zipcode"] = trim($row["J"]);
						$args["supplier_addr1"] = trim($row["K"]);
						$args["supplier_addr2"] = trim($row["L"]);
						$args["supplier_fax"] = "";
						$args["supplier_startdate"] = "";
						$args["supplier_enddate"] = "";
						$args["supplier_license_file"] = 0;
						$args["supplier_bank_account_number"] = trim($row["N"]);
						$args["supplier_bank_name"] = trim($row["O"]);
						$args["supplier_bank_holder_name"] = trim($row["P"]);
						$args["supplier_bank_book_copy_file"] = 0;
						$args["supplier_email_default"] = trim($row["I"]);
						$args["supplier_email_account"] = trim($row["I"]);
						$args["supplier_email_order"] = trim($row["I"]);

						$args["supplier_officer1_name"] = trim($row["F"]);
						$args["supplier_officer1_tel"] = trim($row["G"]);
						$args["supplier_officer1_mobile"] = trim($row["H"]);
						$args["supplier_officer1_email"] = trim($row["I"]);

						$args["supplier_md"] = trim($row["M"]);
						$args["supplier_etc"] = trim($row["Q"]);
                        $args["supplier_payment_type"] = trim($row["R"]);
                        $args["supplier_use_prepay"] = trim($row["S"]);
						$args["is_use"] = trim($row["T"]);

						$tmp_idx = $C_Supplier->insertSupplier($args);

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

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			foreach($sheetData as $row)
			{
				$rowValid = true;

				//A : 공급처 코드
				if (trim($row["A"]) == "") {
					$rowValid = false;
					$row["A"] = "공급처 코드가 정확하지 않습니다.";
				} else {
					$rst = $C_Supplier->isValidSupplier(trim($row["A"]));
					if (!$rst) {
						$rowValid = false;
						$row["A"] = "공급처 코드가 정확하지 않습니다.";
					}
				}

				//B : 공급처 명
				if (trim($row["B"]) == "" || strlen(trim($row["B"])) > 40) {
					$rowValid = false;
					$row["B"] = "공급처명이 정확하지 않습니다.";
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
				$supplier_license_number = preg_replace("/[^0-9]/", "", trim($row["E"]));    // 숫자 이외 제거
				if (trim($row["E"]) == "" || strlen($supplier_license_number) != 10) {
					$rowValid = false;
					$row["E"] = "사업자등록번호가 정확하지 않습니다.";
				}else{
					$row["E"] = preg_replace("/([0-9]{3})([0-9]{2})([0-9]{5})$/", "\\1-\\2-\\3", $supplier_license_number);
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
                }else{
                    if(trim($row["R"]) == "일" ){
                        $row["R"] = "DAY";
                    }else{
                        $row["R"] = "MONTH";
                    }
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
				if($act == "grid") {
					//리스트로 반환
					$listData[] = $row;
				} elseif($act == "save") {
					//적용!!
					if ($rowValid) {
						$args = array();
						$args["idx"] = strtolower(trim($row["A"]));
						$args["login_pw"] = crypt(trim($row["C"]), DY_PASSWORD_SALT);

						$args["supplier_name"] = trim($row["B"]);
						$args["manage_group_idx"] = null;
						$args["supplier_ceo_name"] = trim($row["D"]);
						$args["supplier_license_number"] = trim($row["E"]);
						$args["supplier_zipcode"] = trim($row["J"]);
						$args["supplier_addr1"] = trim($row["K"]);
						$args["supplier_addr2"] = trim($row["L"]);
						$args["supplier_fax"] = null;
						$args["supplier_startdate"] = null;
						$args["supplier_enddate"] = null;
						$args["supplier_license_file"] = null;
						$args["supplier_bank_account_number"] = trim($row["N"]);
						$args["supplier_bank_name"] = trim($row["O"]);
						$args["supplier_bank_holder_name"] = trim($row["P"]);
						$args["supplier_bank_book_copy_file"] = null;
						$args["supplier_email_default"] = null;
						$args["supplier_email_account"] = null;
						$args["supplier_email_order"] = null;

						$args["supplier_officer1_name"] = trim($row["F"]);
						$args["supplier_officer1_tel"] = trim($row["G"]);
						$args["supplier_officer1_mobile"] = trim($row["H"]);
						$args["supplier_officer1_email"] = trim($row["I"]);

						$args["supplier_officer2_name"] = null;
						$args["supplier_officer2_tel"] = null;
						$args["supplier_officer2_mobile"] = null;
						$args["supplier_officer2_email"] = null;

						$args["supplier_officer3_name"] = null;
						$args["supplier_officer3_tel"] = null;
						$args["supplier_officer3_mobile"] = null;
						$args["supplier_officer3_email"] = null;

						$args["supplier_officer4_name"] = null;
						$args["supplier_officer4_tel"] = null;
						$args["supplier_officer4_mobile"] = null;
						$args["supplier_officer4_email"] = null;

						$args["supplier_md"] = trim($row["M"]);
						$args["supplier_etc"] = trim($row["Q"]);
                        $args["supplier_payment_type"] = trim($row["R"]);
                        $args["supplier_use_prepay"] = trim($row["S"]);
                        $args["is_use"] = trim($row["T"]);

						$tmp_idx = $C_Supplier->updateSupplier($args);

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