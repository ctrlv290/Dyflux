<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 판매처 수동발주 업로드 엑셀 Process
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

$xls_filename           = $_GET["xls_filename"];
$seller_idx             = $_GET["seller_idx"];
$order_date             = $_GET["order_date"];

$C_Order = new Order();

//기본 발주서 포맷 불러오기
$formatList = $C_Order -> getOrderFormatDefaultWithSeller($seller_idx);
//print_r2($formatList);

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["err"] = array();

$xls_filename_fullpath = DY_ORDER_XLS_UPLOAD_PATH . "/" . $xls_filename;

if(file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath)) {
	$spreadsheet = IOFactory::load($xls_filename_fullpath);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

	//헤더 찾기 시작
	foreach($sheetData as $row)
	{
		//최소 4열 이상 값이 있어야 헤더로 인식
		if(trim($row["A"]) != "" && trim($row["B"]) != "" && trim($row["C"]) != "" && trim($row["D"]) != ""){
			break;
		}else{
			array_shift($sheetData);
		}
	}

	$_xls_header = current($sheetData);

	//print_r2($_xls_header);
	//배열 구성
	$matchHeader = array();
	foreach ($formatList as $fRow){
		$mKey = $fRow["order_format_default_header_name"];
		$defaultHeaderName = trim($fRow["order_format_default_header_name"]);
		$customHeaderName = trim($fRow["order_format_seller_header_name"]);
		$matchHeader[$mKey] = "";
		$gubun = "";
		foreach($_xls_header as $key => $val)
		{
			if($customHeaderName != "") {
				/// 헤더명에 "|" 기호가 있으면 필드 두개를 붙여라!! -> ssawoona
				$customHeaderNameList = explode("|", $customHeaderName);
				foreach($customHeaderNameList as $split_customHeaderName) {
					if ($split_customHeaderName == trim($val)) {
						$matchHeader[$mKey] = $matchHeader[$mKey] . $gubun . $key;
						$gubun = "|";
					}
				}
			}else{
				if ($defaultHeaderName == trim($val)) {
					$matchHeader[$mKey] = $key;
				}
			}
		}
		if($matchHeader[$mKey] == "") {
			//$matchHeader[$mKey] = $matchHeader[$mKey] . $key;
		}
	}

	//print_r2($matchHeader);

	//order_collect_idx 임시 번호 부여
	$tmp_order_collect_idx = mt_rand(10000, 20000);
	$matchResult = array();
	$etc_data = "";
	array_shift($sheetData);

	/// 발주서포멧에 맞지 않은 데이터 따로 담아서 저장하기 위함. -> ssawoona
	$_xls_DataList =array();
	foreach ($sheetData as $val) {
		$tmpRow = array();
		foreach ($val as $key => $col) {
			$tmpRow[$_xls_header[$key]] = $col;
		}
		$_xls_DataList[] = $tmpRow;
	}
	//unset($_xls_DataList[0]["아이디"]);
	//print_r2($_xls_DataList);
	foreach($sheetData as $idx => $row){
		$tmpRow = array();

		foreach($matchHeader as $key => $col){
			//echo $idx.".".$key ."=>". $col."<br />";
			$tmpRow[$key] = "";
			$find_key  = array_search($key, array_column($formatList, 'order_format_default_header_name'));
			$is_req    = $formatList[$find_key]["order_format_default_is_req"];
			$data_type = $formatList[$find_key]["order_format_default_data_type"];
			$customHeaderName = $formatList[$find_key]["order_format_seller_header_name"];

			//초기값 설정
			if($data_type == "int") $tmpRow[$key] = 0;

			/// 헤더명에 "|" 기호가 있으면 필드 두개를 붙여라!! -> ssawoona
			/// String : Col[A]." | " . Col[b]
			/// Int  : Col[A] + Col[b]
			$colList = explode("|", $col);
			$gubun = "";
			foreach($colList as $split_col) {
				//매칭 된 헤더만 검증 및 값 부여
				if($split_col != ""){
					//echo $key . "<br>";

					//데이터 타입 검증
					if($data_type == "int"){
						//숫자형 검증

						//echo $key . ":" . $find_key . ":" . $col . ":" . $row[$col] . "<br>";

						//콤마 제거
						$row[$split_col] = str_replace(",", "", trim($row[$split_col]));

						//빈값일 경우 0으로 설정
						if($row[$split_col] == "") $row[$split_col] = 0;

						//숫자형이 아니면 pass
						if(!is_numeric($row[$split_col])){
							continue 2;
						}
						$tmpRow[$key] = (int)$tmpRow[$key] + (int)$row[$split_col];

					} else {
						if($row[$split_col] != "") {
							$tmpRow[$key] = $tmpRow[$key] . $gubun . $row[$split_col];
							$gubun        = "|";
						}
					}

				}

			}


			//필수 필드 인데 빈값이면 pass
			if($is_req == "Y" && trim($tmpRow[$key]) == "") {
				//echo $key . ":" . $find_key . ":" . $col . ":" . $row[$col] . "<br>";
				continue 2;
			}



			//주문번호 생성
			if($customHeaderName == "auto_order_no"){
				$tmpRow[$key] = $customHeaderName;
			}

			//상품코드 생성
			if($customHeaderName == "auto_product_code"){
				$tmpRow[$key] = $customHeaderName;
			}
			
			/// 발주서 포멧에 맞지 않은 데이터 저장하기 위함
			if($tmpRow[$key] != "" ) {
				unset($_xls_DataList[$idx][$key]);
			}
		}

		$tmpRow["order_collect_idx"] = $tmp_order_collect_idx;

		$matchResult[] = $tmpRow;
	}

//	print_r2($matchResult);
//	echo "<br/><br/><br/><br/><br/>";
//	print_r2($_xls_DataList);
	//exit;


	$try_count = count($matchResult);
	$inserted_count = 0;
	//발주 입력
	$inserted_count = $C_Order -> insertOrder($seller_idx, $matchResult);

	//발주수량
	//echo "업로드 : " . $try_count;
	//echo "발주 : " . $inserted_count;

	//로그 입력
	$real_order_collect_idx = $C_Order -> insertOrderUpload($seller_idx, date('Y-m-d H:i:s'), $try_count, $inserted_count, $xls_filename);

	//입력된 로그 IDX 를 발주서에 Update
	$tmp = $C_Order -> updateOrderCollectIDX($tmp_order_collect_idx, $real_order_collect_idx);


	$response["result"] = true;
	$response["msg"] = $inserted_count;

}else{
	$response["msg"] = "파일이 없습니다.";
}
echo json_encode($response, true);

?>