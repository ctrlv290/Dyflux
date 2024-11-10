<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매칭일괄 등록 관련 Process (excel)
 * 판매처가 직접 등록 시 권한 작업 필요!
 */
//Page Info
$pageMenuIdx = 67;
//Init
include "../_init_.php";
$C_Users = new Users();
$C_Product = new Product();
$C_Vendor = new Vendor();
$C_Seller = new Seller();

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

	if ($mode == "add") {

		/*
		 * A : 판매처코드
		 * B : 판매처 상품코드
		 * C : 판매처 상품명
		 * D : 판매처 옵션
		 * E : 매칭 상품 1
		 * F : 매칭 상품 1 수량
		 * G : 매칭 상품 2
		 * H : 매칭 상품 2 수량
		 * I : 매칭 상품 3
		 * J : 매칭 상품 3 수량
		 * K : 매칭 상품 4
		 * L : 매칭 상품 4 수량
		 * M : 매칭 상품 5
		 * N : 매칭 상품 5 수량
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			array_shift($sheetData);
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			$row_index = 0;
			foreach($sheetData as $row)
			{
				//6개 열에 값이 모두 없으면 종료
				if(trim($row["A"]) == "" && trim($row["B"]) == "" && trim($row["C"]) == "" && trim($row["D"]) == "" && trim($row["E"]) == "" && trim($row["F"]) == "")
				{
					continue;
				}

				$rowValid = true;

				//Row Index
				$row_index++;
				$row["xls_idx"] = $row_index;

				//판매처 상품 정보 초기화
				$seller_idx             = "";
				$market_product_no      = "";
				$market_product_name    = "";
				$market_product_option  = "";
				$product1_idx           = "";
				$product_option1_idx    = "";
				$product_option1_name   = "";
				$product_option1_option = "";
				$product_option1_cnt    = "";
				$product2_idx           = "";
				$product_option2_idx    = "";
				$product_option2_name   = "";
				$product_option2_option = "";
				$product_option2_cnt    = "";
				$product3_idx           = "";
				$product_option3_idx    = "";
				$product_option3_name   = "";
				$product_option3_option = "";
				$product_option3_cnt    = "";
				$product4_idx           = "";
				$product_option4_idx    = "";
				$product_option4_name   = "";
				$product_option4_option = "";
				$product_option4_cnt    = "";
				$product5_idx           = "";
				$product_option5_idx    = "";
				$product_option5_name   = "";
				$product_option5_option = "";
				$product_option5_cnt    = "";

				//A : 판매처코드
				$c_str = "A";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if(isDYLogin()) {
					if ($cval == "") {
						$rowValid  = false;
						$returnMsg = "판매처명이 입력되지 않았습니다.";
					} else {
						$seller_info_tmp = $C_Seller->getUseSellerAllDataByName($cval);
						if (!$seller_info_tmp) {
							$rowValid  = false;
							$returnMsg = "잘못된 판매처명 입니다.";
						} else {
							$seller_idx  = $seller_info_tmp["seller_idx"];
							$row[$c_str] = $seller_info_tmp["seller_name"];
						}
					}
					if(!$rowValid) $row[$c_str] = $returnMsg;
				}else{
					$seller_info_tmp = $C_Seller->getUseSellerAllData($GL_Member["member_idx"]);
					if (!$seller_info_tmp) {
						$rowValid  = false;
						$returnMsg = "잘못된 판매처코드 입니다.";
					} else {
						$seller_idx  = $seller_info_tmp["seller_idx"];
						$row[$c_str] = $seller_info_tmp["seller_name"];
					}
				}

				//B : 판매처 상품코드
				$c_str = "B";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "판매처 상품코드가 입력되지 않았습니다.";
				}else{
					$market_product_no = $cval;
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//C: 판매처 상품명
				$c_str = "C";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "판매처 상품명이 입력되지 않았습니다.";
				}else{
					$market_product_name = $cval;
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//C: 판매처 옵션
				$c_str = "D";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "판매처 옵션이 입력되지 않았습니다.";
				}else{
					$market_product_option = $cval;
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//매칭 정보 중복 체크!!
				if($rowValid) {
					$dupCheck = $C_Product->dupCheckProductMatchingInfo($seller_idx, $market_product_no, $market_product_name, $market_product_option);
					if (!$dupCheck) {
						$rowValid  = false;
						$returnMsg = "이미 등록된 매칭정보 입니다.(판매처 상품 중복)";
						$row["C"] = $returnMsg;
					}
				}

				//E : 매칭 상품 1
				$c_str = "E";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "매칭 상품 1의 상품옵션코드가 입력되지 않았습니다.";
				}else{
					$product_option_info_tmp = $C_Product->getProductOptionData($cval);
					if(!$product_option_info_tmp){
						$rowValid = false;
						$returnMsg = "매칭 상품 1의 상품옵션코드가 정확하지 않습니다.";
					}else{
						$product_option1_idx = $cval;
						$product1_idx = $product_option_info_tmp["product_idx"];
						$product_option1_name = $product_option_info_tmp["product_name"];
						$product_option1_option = $product_option_info_tmp["product_option_name"];
					}
				}
				//if(!$rowValid) $row[$c_str] = $returnMsg;
				if(!$rowValid) $row["product_option_idx"] = $returnMsg;

				//F : 매칭 상품 1 수량
				$c_str = "F";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "매칭 상품 1의 수량이 입력되지 않았습니다.";

				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$returnMsg = "매칭 상품 1의 수량은 숫자만 허용됩니다.";
					}else{
						if(intval($cval) == 0){
							$rowValid = false;
							$returnMsg = "매칭 상품 1의 수량은 0이 될 수 없습니다.";
						}else{
							$product_option1_cnt = $cval;
						}
					}
				}
				//if(!$rowValid) $row[$c_str] = $returnMsg;
				if(!$rowValid) $row["product_option_cnt"] = $returnMsg;

				//G : 매칭 상품 2
				$c_str = "G";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					//미입력 가능
				}else{
					$product_option_info_tmp = $C_Product->getProductOptionData($cval);
					if(!$product_option_info_tmp){
						$rowValid = false;
						$returnMsg = "매칭 상품 2의 상품옵션코드가 정확하지 않습니다.";
						if(!$rowValid) $row["product_option_idx"] = $returnMsg;
					}else{
						$product_option2_idx = $cval;
						$product2_idx = $product_option_info_tmp["product_idx"];
						$product_option2_name = $product_option_info_tmp["product_name"];
						$product_option2_option = $product_option_info_tmp["product_option_name"];
						//H : 매칭 상품 2 수량
						$c_str = "H";
						$cval = str_replace(",", "", trim($row[$c_str]));
						$returnMsg = "";
						if ($cval == "") {
							$rowValid = false;
							$returnMsg = "매칭 상품 2의 수량이 입력되지 않았습니다.";
						}else{
							if(!is_numeric($cval)){
								$rowValid = false;
								$returnMsg = "매칭 상품 2의 수량은 숫자만 허용됩니다.";
							}else{
								if(intval($cval) == 0){
									$rowValid = false;
									$returnMsg = "매칭 상품 2의 수량은 0이 될 수 없습니다.";
								}else{
									$product_option2_cnt = $cval;

								}
							}
						}

						if(!$rowValid) $row["product_option_cnt"] = $returnMsg;
					}
				}
				//if(!$rowValid) $row[$c_str] = $returnMsg;

				//I : 매칭 상품 3
				$c_str = "I";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					//미입력 가능
				}else{
					$product_option_info_tmp = $C_Product->getProductOptionData($cval);
					if(!$product_option_info_tmp){
						$rowValid = false;
						$returnMsg = "매칭 상품 3의 상품옵션코드가 정확하지 않습니다.";
						if(!$rowValid) $row["product_option_idx"] = $returnMsg;
					}else{
						$product_option3_idx = $cval;
						$product3_idx = $product_option_info_tmp["product_idx"];
						$product_option3_name = $product_option_info_tmp["product_name"];
						$product_option3_option = $product_option_info_tmp["product_option_name"];

						//J : 매칭 상품 3 수량
						$c_str = "J";
						$cval = str_replace(",", "", trim($row[$c_str]));
						$returnMsg = "";
						if ($cval == "") {
							$rowValid = false;
							$returnMsg = "매칭 상품 3의 수량이 입력되지 않았습니다.";
						}else{
							if(!is_numeric($cval)){
								$rowValid = false;
								$returnMsg = "매칭 상품 3의 수량은 숫자만 허용됩니다.";
							}else{
								if(intval($cval) == 0){
									$rowValid = false;
									$returnMsg = "매칭 상품 3의 수량은 0이 될 수 없습니다.";
								}else{
									$product_option3_cnt = $cval;
								}
							}
						}
						if(!$rowValid) $row["product_option_cnt"] = $returnMsg;
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;



				//K : 매칭 상품 4
				$c_str = "K";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					//미입력 가능
				}else{
					$product_option_info_tmp = $C_Product->getProductOptionData($cval);
					if(!$product_option_info_tmp){
						$rowValid = false;
						$returnMsg = "매칭 상품 4의 상품옵션코드가 정확하지 않습니다.";
						if(!$rowValid) $row["product_option_idx"] = $returnMsg;
					}else{
						$product_option4_idx = $cval;
						$product4_idx = $product_option_info_tmp["product_idx"];
						$product_option4_name = $product_option_info_tmp["product_name"];
						$product_option4_option = $product_option_info_tmp["product_option_name"];

						//L : 매칭 상품 4 수량
						$c_str = "L";
						$cval = str_replace(",", "", trim($row[$c_str]));
						$returnMsg = "";
						if ($cval == "") {
							$rowValid = false;
							$returnMsg = "매칭 상품 4의 수량이 입력되지 않았습니다.";
						}else{
							if(!is_numeric($cval)){
								$rowValid = false;
								$returnMsg = "매칭 상품 4의 수량은 숫자만 허용됩니다.";
							}else{
								if(intval($cval) == 0){
									$rowValid = false;
									$returnMsg = "매칭 상품 4의 수량은 0이 될 수 없습니다.";
								}else{
									$product_option4_cnt = $cval;
								}
							}
						}
						if(!$rowValid) $row["product_option_cnt"] = $returnMsg;
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//M : 매칭 상품 5
				$c_str = "M";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					//미입력 가능
				}else{
					$product_option_info_tmp = $C_Product->getProductOptionData($cval);
					if(!$product_option_info_tmp){
						$rowValid = false;
						$returnMsg = "매칭 상품 1의 상품옵션코드가 정확하지 않습니다.";
						if(!$rowValid) $row["product_option_idx"] = $returnMsg;
					}else{
						$product_option5_idx = $cval;
						$product5_idx = $product_option_info_tmp["product_idx"];
						$product_option5_name = $product_option_info_tmp["product_name"];
						$product_option5_option = $product_option_info_tmp["product_option_name"];

						//N : 매칭 상품 5 수량
						$c_str = "N";
						$cval = str_replace(",", "", trim($row[$c_str]));
						$returnMsg = "";
						if ($cval == "") {
							$rowValid = false;
							$returnMsg = "매칭 상품 5의 수량이 입력되지 않았습니다.";
						}else{
							if(!is_numeric($cval)){
								$rowValid = false;
								$returnMsg = "매칭 상품 5의 수량은 숫자만 허용됩니다.";
							}else{
								if(intval($cval) == 0){
									$rowValid = false;
									$returnMsg = "매칭 상품 5의 수량은 0이 될 수 없습니다.";
								}else{
									$product_option5_cnt = $cval;
								}
							}
						}
						if(!$rowValid) $row["product_option_cnt"] = $returnMsg;
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;




				$row["valid"] = $rowValid;

				//$response["err"][] = $row;

				if($act == "grid") {
					//리스트로 반환
					//$listData[] = $row;
					if($rowValid) {

						//매칭 상품 1
						$row["product_idx"]         = $product1_idx;
						$row["product_option_idx"]  = $product_option1_idx;
						$row["product_name"]        = $product_option1_name;
						$row["product_option_name"] = $product_option1_option;
						$row["product_option_cnt"]  = $product_option1_cnt;
						$listData[]                 = $row;

						//매칭 상품 2
						if($product_option2_idx && $product_option2_cnt){
							$row["product_idx"]         = $product2_idx;
							$row["product_option_idx"]  = $product_option2_idx;
							$row["product_name"]        = $product_option2_name;
							$row["product_option_name"] = $product_option2_option;
							$row["product_option_cnt"]  = $product_option2_cnt;
							$listData[]                 = $row;
						}

						//매칭 상품 3
						if($product_option3_idx && $product_option3_cnt){
							$row["product_idx"]         = $product3_idx;
							$row["product_option_idx"]  = $product_option3_idx;
							$row["product_name"]        = $product_option3_name;
							$row["product_option_name"] = $product_option3_option;
							$row["product_option_cnt"]  = $product_option3_cnt;
							$listData[]                 = $row;
						}

						//매칭 상품 4
						if($product_option4_idx && $product_option4_cnt){
							$row["product_idx"]         = $product4_idx;
							$row["product_option_idx"]  = $product_option4_idx;
							$row["product_name"]        = $product_option4_name;
							$row["product_option_name"] = $product_option4_option;
							$row["product_option_cnt"]  = $product_option4_cnt;
							$listData[]                 = $row;
						}

						//매칭 상품 5
						if($product_option5_idx && $product_option5_cnt){
							$row["product_idx"]         = $product5_idx;
							$row["product_option_idx"]  = $product_option5_idx;
							$row["product_name"]        = $product_option5_name;
							$row["product_option_name"] = $product_option5_option;
							$row["product_option_cnt"]  = $product_option5_cnt;
							$listData[]                 = $row;
						}
					}else{
						$row["product_idx"]         = "";
						$row["product_name"]        = "";
						$row["product_option_name"] = "";
						$listData[] = $row;
					}


				} elseif($act == "save") {
					//적용!!

					//적용 시 등록 제외 목록 비교
					if(count($exclude_list) > 0)
					{
						if(in_array($row["xls_idx"], $exclude_list)){
							//제외 목록에 있을 시
							$rowValid = false;
						}
					}


					if($rowValid) {
						$market_product_no = trim($row["B"]);       //판매처 상품코드
						$market_product_name = trim($row["C"]);     //판매처 상품명
						$market_product_option = trim($row["D"]);   //판매처 상품 옵션

						//$product_list             : 매칭 상품 목록 (array) [{product_idx, product_option_idx, product_option_cnt}]
						//매칭 대상 상품 목록 구성
						$product_list = array();

						//매칭 상품 1
						$product_list[] = array("product_idx" => $product1_idx, "product_option_idx" => $product_option1_idx, "product_option_cnt" => $product_option1_cnt);

						//매칭 상품 2
						if($product_option2_idx && $product_option2_cnt){
							$product_list[] = array("product_idx" => $product2_idx, "product_option_idx" => $product_option2_idx, "product_option_cnt" => $product_option2_cnt);
						}

						//매칭 상품 3
						if($product_option3_idx && $product_option3_cnt){
							$product_list[] = array("product_idx" => $product3_idx, "product_option_idx" => $product_option3_idx, "product_option_cnt" => $product_option3_cnt);
						}

						//매칭 상품 4
						if($product_option4_idx && $product_option4_cnt){
							$product_list[] = array("product_idx" => $product4_idx, "product_option_idx" => $product_option4_idx, "product_option_cnt" => $product_option4_cnt);
						}

						//매칭 상품 5
						if($product_option5_idx && $product_option5_cnt){
							$product_list[] = array("product_idx" => $product5_idx, "product_option_idx" => $product_option5_idx, "product_option_cnt" => $product_option5_cnt);
						}

						$rst = $C_Product->insertProductMatchingInfo($seller_idx, $market_product_no, $market_product_name, $market_product_option, $product_list);
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

		/*
		 * A : 판매처명
		 * B : 판매처 상품코드
		 * C : 판매처 상품명
		 * D : 판매처 옵션
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			array_shift($sheetData);
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			$row_index = 0;
			foreach($sheetData as $row)
			{
				//6개 열에 값이 모두 없으면 종료
				if(trim($row["A"]) == "" && trim($row["B"]) == "" && trim($row["C"]) == "" && trim($row["D"]) == "")
				{
					continue;
				}

				$rowValid = true;

				//Row Index
				$row_index++;
				$row["xls_idx"] = $row_index;

				//판매처 상품 정보 초기화
				$seller_idx             = "";
				$market_product_no      = "";
				$market_product_name    = "";
				$market_product_option  = "";

				$matching_info_idx      = "";
				$member_id              = "";

				//A : 판매처코드
				$c_str = "A";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if(isDYLogin()) {
					if ($cval == "") {
						$rowValid  = false;
						$returnMsg = "판매처명이 입력되지 않았습니다.";
					} else {
						$seller_info_tmp = $C_Seller->getUseSellerAllDataByName($cval);
						if (!$seller_info_tmp) {
							$rowValid  = false;
							$returnMsg = "잘못된 판매처명 입니다.";
						} else {
							$seller_idx  = $seller_info_tmp["seller_idx"];
							$row[$c_str] = $seller_info_tmp["seller_name"];
						}
					}
					if(!$rowValid) $row[$c_str] = $returnMsg;
				}else{
					$seller_info_tmp = $C_Seller->getUseSellerAllData($GL_Member["member_idx"]);
					if (!$seller_info_tmp) {
						$rowValid  = false;
						$returnMsg = "잘못된 판매처코드 입니다.";
					} else {
						$seller_idx  = $seller_info_tmp["seller_idx"];
						$row[$c_str] = $seller_info_tmp["seller_name"];
					}
				}

				//B : 판매처 상품코드
				$c_str = "B";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "판매처 상품코드가 입력되지 않았습니다.";
				}else{
					$market_product_no = $cval;
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//C: 판매처 상품명
				$c_str = "C";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "판매처 상품명이 입력되지 않았습니다.";
				}else{
					$market_product_name = $cval;
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//C: 판매처 옵션
				$c_str = "D";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "판매처 옵션이 입력되지 않았습니다.";
				}else{
					$market_product_option = $cval;
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//매칭 정보 체크!!
				if($rowValid) {
					$dupCheck = $C_Product->dupCheckProductMatchingInfo($seller_idx, $market_product_no, $market_product_name, $market_product_option);
					if ($dupCheck) {
						$rowValid  = false;
						$returnMsg = "등록되지 않은 매칭정보 입니다.";
						$row["C"] = $returnMsg;
					}else{
						$_matching_info = $C_Product->getProductMatchingInfo($seller_idx, $market_product_no, $market_product_name, $market_product_option);
						if($_matching_info){
							$matching_info_idx = $_matching_info["matching_info_idx"];
							$member_id         = $_matching_info["member_id"];
						}else{
							$rowValid  = false;
							$returnMsg = "등록되지 않은 매칭정보 입니다.";
							$row["C"]  = $returnMsg;
						}

					}
				}



				$row["valid"] = $rowValid;

				//$response["err"][] = $row;

				if($act == "grid") {
					//리스트로 반환
					//$listData[] = $row;
					if($rowValid) {
						$row["member_id"] = $member_id;
						$listData[] = $row;
					}else{
						$listData[] = $row;
					}


				} elseif($act == "save") {
					//적용!!

					//적용 시 등록 제외 목록 비교
					if(count($exclude_list) > 0)
					{
						if(in_array($row["xls_idx"], $exclude_list)){
							//제외 목록에 있을 시
							$rowValid = false;
						}
					}


					if($rowValid) {
						$row["member_id"] = $member_id;

						$rst = $C_Product->deleteProductMatchingInfo($matching_info_idx);
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