<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처 수동발주 업로드 엑셀 Process
 * TODO : 발주서 업로드 시 잘못된 데이터가 있을 경우 전체 발주 내역 입력 취소 해야함! 19.01.09 회의 w/서차장
 */
//Page Info
$pageMenuIdx = 176;
//Init
include "../_init_.php";

$C_Login = new Login();
$C_Login->setLoginSessionByToken();     // 토큰으로 로그인 시키기

$C_Users = new Users();
$C_Product = new Product();

$C_Code = new Code();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

//$xls_filename           = $_POST["xls_filename"];
//$seller_idx             = $_POST["seller_idx"];
//$order_date             = $_POST["order_date"];


$xls_filename    = $_POST["xls_filename"];
$collect_num     = $_POST["collect_num"];
$seller_idx      = $_POST["seller_idx"];
$collect_type    = $_POST["collect_type"];
$collect_sdate   = $_POST["collect_sdate"];
$collect_edate   = $_POST["collect_edate"];
$collect_state   = $_POST["collect_state"];
$collect_message = $_POST["collect_message"];

if(!$collect_type) {
	$collect_type = "XLS";
}
if(!$collect_state) {
	$collect_state = "S";
}
if(!$collect_num) {
	$collect_num = 0;
}
if(!$collect_sdate) {
	$collect_sdate = date('Y-m-d H:i:s');
	$collect_edate = date('Y-m-d H:i:s');
}

//로그 입력을 위한 Data Set
$order_collect = array(
	'collect_num' => 0,
	'seller_idx' => $seller_idx,
	'collect_type' => $collect_type,
	'collect_sdate' => $collect_sdate,
	'collect_edate' => $collect_edate,
	'collect_count' => 0,
	'collect_order_count' => 0,
	'collect_state' => $collect_state,
	'collect_message' => $collect_message,
	'collect_filename' => $xls_filename,
);
//print_r2($order_collect);

$C_Order = new Order();

//기본 발주서 포맷 불러오기
$formatList = $C_Order -> getOrderFormatDefaultWithSeller($seller_idx);
//print_r2($formatList);

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["err"] = array();

$xls_filename_fullpath = DY_ORDER_XLS_UPLOAD_PATH . "/" . $xls_filename;
if($collect_state == "S") {

	if (file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath)) {
		$spreadsheet = IOFactory::load($xls_filename_fullpath);
		$sheetData   = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

		//!!!!배열 전체를 검색하여 엔터 값을 빈칸으로 치환 [사용 함수 : removeXlsLinebreak()]
		array_walk_recursive($sheetData, 'removeXlsLinebreak');

		//헤더 찾기 시작
		foreach ($sheetData as $row) {
			//최소 4열 이상 값이 있어야 헤더로 인식
			if (trim($row["A"]) != "" && trim($row["B"]) != "" && trim($row["C"]) != "" && trim($row["D"]) != "") {
				break;
			} else {
				array_shift($sheetData);
			}
		}

		//업로드된 엑셀 헤더 Array
		$_xls_header = current($sheetData);
		//헤더 값 Trim
		$_xls_header = array_map('trim', $_xls_header);

		//print_r2($_xls_header);
		//print_r2($formatList);
		//배열 구성
		$matchHeader = array();

		//판매처 헤더 양식 Array ForEach
		foreach ($formatList as $fRow) {
			$mKey               = $fRow["order_format_default_header_name"];        //기본 한글 헤더명 [Key 로 사용됨]
			$defaultHeaderName  = trim($fRow["order_format_default_header_name"]);  //기본 한글 헤더명 [비교용 값]
			$customHeaderName   = trim($fRow["order_format_seller_header_name"]);   //판매처가 지정한 한글 헤더명
			$matchHeader[$mKey] = "";                                               //매칭될 헤더 기본값 설정
			$gubun              = "";

//			$find_key_ary = array();    //찾은 헤더 Key 값 배열 선언 ('|' 구분자가 있을 경우 여러개를 찾음)
//			$find_key = false;          //매칭 여부 T/F
//
//			if($customHeaderName != "") {
//				//판매처 지정 한글 헤더명이 있을 경우..
//
//				//헤더 값에 '|' 가 포함 될 경우 여러번을 찾도록
//				//무조건 explode 후 찾기
//				$customHeaderNameList = explode("|", $customHeaderName);
//				foreach ($customHeaderNameList as $split_customHeaderName) {
//					//판매처가 지정한 한글 헤더명으로 찾기
//					$find_key = array_search($split_customHeaderName, $_xls_header);
//
//                  //헤더 Key 를 찾았으면 배열에 저장
//					if ($find_key !== false) {
//						$find_key_ary[] = $find_key;
//					}
//				}
//			}else{
//				//판매처 지정 한글 헤더명이 없는 경우
//				//기본 한글 헤더명으로 찾기
//				$find_key = array_search($defaultHeaderName, $_xls_header);
//
//				//헤더 Key 를 찾았으면 배열에 저장
//  			if ($find_key !== false) {
//	    			$find_key_ary[] = $find_key;
//				}
//			}
//
//			//매칭된 헤더가 있다면
//			if($find_key !== false) {
//				// 헤더 값에 '|' 가 포함 될 경우
//				// 여거래의 find_key 가 array 화 되어 있음
//				// '|' 구분자로 implode 함
//				$matchHeader[$mKey] = implode("|", $find_key_ary);
//			}


			//업로드된 엑셀 헤더 중에서 매칭 찾기
			//업로드된 엑셀 헤더 Array ForEach
			//ex))
			// [A] => 순번
			// [B] => 업체명
			// [C] => 주문번호
			// [D] => 주문상세번호
			// .....
			//
			foreach ($_xls_header as $key => $val) {

				//판매처가 지정한 한글 헤더명이 있다면
				if ($customHeaderName != "") {

					/// 헤더명에 "|" 기호가 있으면 필드 두개를 붙여라!! -> ssawoona
					$customHeaderNameList = explode("|", $customHeaderName);



					//주문번호|주문상세번호

					/*
					$ary["주문번호"] = "A"

					$ary["주문번호"] = "A|B"
					 */

					foreach ($customHeaderNameList as $split_customHeaderName) {
						if ($split_customHeaderName == trim($val)) {
							$matchHeader[$mKey] = $matchHeader[$mKey] . $gubun . $key;
							if(strpos($customHeaderName, "{OR}") === false) {
								$gubun = "|";
							} else {
								$gubun = "|{OR}|";
							}
						}

					}
				} else {

					//판매처가 지정한 한글 헤더명이 없다면
					//기본 한글 헤더명으로 비교
					if ($defaultHeaderName == trim($val)) {
						$matchHeader[$mKey] = $key;
					}
				}
			}

			if ($matchHeader[$mKey] == "") {
				//$matchHeader[$mKey] = $matchHeader[$mKey] . $key;
			}
		}

		// 추가 구성 상품 키 찾기
		$tmp_addOrderKey = "";
		foreach ($_xls_header as $key => $val) {
			//echo $key ."=>". $val."<br />";
			if($val == "추가구성") {
				$tmp_addOrderKey = $key;
			}
		}

		//print_r2($matchHeader);

		//order_collect_idx 임시 번호 부여
		$tmp_order_collect_idx = mt_rand(10000, 20000);
		$matchResult           = array();
		$etc_data              = "";
		$total_count           = 0;
		array_shift($sheetData);

		/// 발주서포멧에 맞지 않은 데이터 따로 담아서 저장하기 위함. -> ssawoona
		$_xls_DataList = array();
		foreach ($sheetData as $val) {
			$tmpRow = array();
			foreach ($val as $key => $col) {
				$tmpRow[$_xls_header[$key]] = $col;
			}
			$_xls_DataList[] = $tmpRow;
		}
		//unset($_xls_DataList[0]["아이디"]);
		//print_r2($_xls_DataList);
//		print_r2($matchHeader);
//		echo "array_search--->".$matchHeader["옵션"]."<br />";
		foreach ($sheetData as $idx => $row) {
			$tmpRow = array();
//			print_r2($row);
			//echo "옵션--->".$row[$matchHeader["옵션"]]."<br />";

			foreach ($matchHeader as $key => $col) {
				$total_count++;
				//echo $idx.".".$key ."=>". $col."~~~".$row[$col] ."<br />";
				$tmpRow[$key]     = "";
				$find_key         = array_search($key, array_column($formatList, 'order_format_default_header_name'));
				$is_req           = $formatList[$find_key]["order_format_default_is_req"];
				$data_type        = $formatList[$find_key]["order_format_default_data_type"];
				$customHeaderName = $formatList[$find_key]["order_format_seller_header_name"];

				//초기값 설정
				if ($data_type == "int") $tmpRow[$key] = 0;

				/// 헤더명에 "|" 기호가 있으면 필드 두개를 붙여라!! -> ssawoona
				/// String : Col[A]." | " . Col[b]
				/// String : Col[A]." |{OR}|" . Col[b]  => 둘중 데이터가 있는거로.. (둘다 있으면 먼저 있는 셀 데이터..)
				/// Int  : Col[A] + Col[b]
				$is_or = false;
				$colList = explode("|", $col);
				$gubun   = "";
				foreach ($colList as $split_col) {
					//매칭 된 헤더만 검증 및 값 부여
					if ($split_col != "") {
						//echo $key . "<br>";

						//데이터 타입 검증
						if ($data_type == "int") {
							//숫자형 검증

							//echo $key . ":" . $find_key . ":" . $col . ":" . $row[$col] . "<br>";

							//콤마 제거
							$row[$split_col] = str_replace(",", "", trim($row[$split_col]));

							//빈값일 경우 0으로 설정
							if ($row[$split_col] == "") $row[$split_col] = 0;

							//숫자형이 아니면 pass
							if (!is_numeric($row[$split_col])) {
								continue 2;
							}

							$tmpRow[$key] = (int)$tmpRow[$key] + (int)$row[$split_col];

						} else {
							$col_val = trim($row[$split_col]);
							if(strpos($col_val, "'") == 0) {
								if(strpos($col_val, "'", 1) == false) {
									$col_val = str_replace("'", "", $col_val);
								}
							}

							if($split_col == "{OR}") {
								$is_or = true;
							}
							if ($row[$split_col] != "") {
								if($is_or) {
									//echo $tmpRow[$key]."<Br />";
									if($tmpRow[$key] == "") {
										$tmpRow[$key] = $col_val;
									}
								} else {
									$tmpRow[$key] = $tmpRow[$key] . $gubun . $col_val;
									$gubun        = "|";
								}
							}
						}
					}
				}

				//필수 필드 인데 빈값이면 pass
				if ($is_req == "Y") {
					if ($data_type == "int") {
						if (trim($tmpRow[$key]) == 0) continue 2;
					} else {
						if (trim($tmpRow[$key]) == "") continue 2;
					}
				}

				//주문번호 생성
				if ($customHeaderName == "auto_order_no") {
					$tmpRow[$key] = $customHeaderName;
				}

				//상품코드 생성
				if ($customHeaderName == "auto_product_code") {
					$tmpRow[$key] = $customHeaderName;
				}

				//중복방지를 위해 옵션명의 해시코드 입력 (티몬)
				if ($customHeaderName == "auto_option_code") {
					//echo "------->".$row[$matchHeader["옵션"]]."<br />";
					$tmpRow[$key] = hash('md5', $row[$matchHeader["옵션"]]);
				}

				/// 발주서 포멧에 맞지 않은 데이터 저장하기 위함
				if ($tmpRow[$key] != "") {
					unset($_xls_DataList[$idx][$key]);
				}

			}

			$tmpRow["order_collect_idx"] = $tmp_order_collect_idx;

			$matchResult[] = $tmpRow;

			/*if($tmp_addOrderKey != "") {
				if ($row[$tmp_addOrderKey]) {
					// 추가구성이 있으면 상품 추가
					$tmpRow["옵션"]  = $row[$tmp_addOrderKey];
					$tmpRow["주문번호"]  = $tmpRow["주문번호"]."A";
					$tmpRow["판매금액"] = "0";
					$tmpRow["판매단가"] = "0";
					$tmpRow["정산금액"] = "0";
					// 수량에 문제가 있음
					$matchResult[] = $tmpRow;
				}
			}*/

		}

//	print_r2($matchResult);
//	echo "<br/><br/><br/><br/><br/>";
//	print_r2($_xls_DataList);
		//exit;


		$try_count      = count($matchResult);
		$inserted_count = 0;
		//발주 입력
		//$inserted_count = $C_Order->insertOrder($seller_idx, $matchResult);


		//발주수량
		//echo "업로드 : " . $try_count;
		//echo "발주 : " . $inserted_count;

		/*************************************************************************************************************************************/
        //성공시에만 입력 되던 로그를
		//실패 시에도 입력되도록
		// 하단으로 이동됨!
		//로그 입력
		/*
		$real_order_collect_idx = $C_Order->insertOrderUpload($seller_idx, date('Y-m-d H:i:s'), $try_count, $inserted_count, $xls_filename);

		//입력된 로그 IDX 를 발주서에 Update
		$tmp = $C_Order->updateOrderCollectIDX($tmp_order_collect_idx, $real_order_collect_idx);
		*/
		/*************************************************************************************************************************************/


		$response["result"] = true;
		$response["msg"]    = $inserted_count;

	} else {
		$response["msg"] = "파일이 없습니다.";
		if($collect_type == "AUTO") {
			$response["result"] = true;
			$response["msg"]    = "";
		}
	}

	$try_count = count($matchResult);
	$inserted_count = 0;
	$dup_count = 0;
	if($try_count > 0) {
		//발주 입력
		$insert_result_ary = $C_Order -> insertOrder($seller_idx, $matchResult);
		$inserted_count = $insert_result_ary["inserted"];
		$dup_count = $insert_result_ary["dup"];
	}
	$order_collect["collect_count"] = $try_count;
	$order_collect["collect_order_count"] = $inserted_count;
	$response["collect_count"] = $try_count;
	$response["collect_order_count"] = $inserted_count;
	$response["collect_edate"] = $order_collect["collect_edate"];
	$response["msg"]    = $inserted_count;
	$response["dup_count"]    = $dup_count;
	$response["total_count"]    = $total_count;
	//print_r2($response);
	if($response["result"]) {
		$order_collect["collect_state"] = "S";
	} else {
		$order_collect["collect_state"] = "F";
	}
}

//로그 입력
$real_order_collect_idx = $C_Order->insertOrderUpload($order_collect);

//입력된 로그 IDX 를 발주서에 Update
$tmp = $C_Order->updateOrderCollectIDX($tmp_order_collect_idx, $real_order_collect_idx);
//print_r2($order_collect);

echo json_encode($response, true);
?>