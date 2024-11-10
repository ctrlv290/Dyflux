<?php

include_once "_init_.php";

require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;

$pool = new ApcuCachePool();
$sc = new SimpleCacheBridge($pool);

\PhpOffice\PhpSpreadsheet\Settings::setCache($sc);

if (file_exists("D:/download/판매일보 업로드 테스트_.xlsx") == false)
	exit;

class chunkReadFilter implements PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
	private $_startRow = 0;
	private $_endRow   = 0;

	/**  Set the list of rows that we want to read
	 * @param $startRow
	 * @param $chunkSize
	 */
	public function setRows($startRow, $chunkSize) {
		$this->_startRow = $startRow;
		$this->_endRow   = $startRow + $chunkSize;
	}

	public function readCell($column, $row, $worksheetName = '') {
		//  Only read the heading row, and the configured rows
		if ($row >= $this->_startRow && $row < $this->_endRow) {
			return true;
		}
		return false;
	}
}

// info test
//$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
//$chunkFilter = new chunkReadFilter();
//$objReader->setReadFilter($chunkFilter);
//$objReader->setReadDataOnly(true);
//$objReader->setInputEncoding("EUC-KR");
//$startRow = 3;
//$chunkSize = 100;
//$highestRow = 1000;
//$normal_count = 0;
//$error_count = 0;
//$error_rows = Array();
//$start_memory = memory_get_usage();
//$row_index = 0;
//print("start memory: ".date("Y-m-d H:i:s"));
//print("<br>");
//print("start time: ");
//print(memory_get_usage());
//print("<br>");
//for ($startRow = 3; $startRow < $highestRow + 3; $startRow = $startRow + $chunkSize) {
//    $chunkFilter->setRows($startRow, $chunkSize);
//    $spreadsheet = $objReader->load("D:/download/판매일보 업로드 테스트_.csv");
//    $spreadsheet->garbageCollect();
//    $maxRow = $spreadsheet->getActiveSheet()->getHighestRow();
//    $maxCol = $spreadsheet->getActiveSheet()->getHighestColumn();
//    $sheetData = $spreadsheet->getActiveSheet()->rangeToArray('A' . $startRow . ':' . $maxCol . $maxRow, null, true, true, true);
//    print(memory_get_usage());
//    $spreadsheet->disconnectWorksheets();
//    unset($spreadsheet);
//    print("<br>");
//
//    foreach($sheetData as $row)
//    {
//        $rowValid = true;
//        //Row Index
//        $row_index++;
//
//        //보정 정보 초기화
//        $settle_type                              = "";
//        $settle_idx                               = "";
//        $supplier_idx                             = "";
//        $settle_date                              = "";
//        $seller_idx                               = "";
//        $product_idx                              = "";
//        $order_unit_price                         = "";
//        $settle_sale_supply                       = "";
//        $settle_sale_commission_ex_vat            = "";
//        $settle_sale_supply_ex_vat                = "";
//        $settle_sale_commission_in_vat            = "";
//        $settle_delivery_in_vat                   = "";
//        $settle_delivery_commission_ex_vat        = "";
//        $settle_delivery_ex_vat                   = "";
//        $settle_delivery_commission_in_vat        = "";
//        $settle_purchase_unit_supply              = "";
//        $settle_purchase_supply                   = "";
//        $settle_purchase_unit_supply_ex_vat       = "";
//        $settle_purchase_supply_ex_vat            = "";
//        $settle_purchase_delivery_in_vat          = "";
//        $settle_settle_amt                        = "";
//        $settle_purchase_delivery_ex_vat          = "";
//        $settle_ad_amt                            = "";
//        $settle_sale_profit                       = "";
//        $settle_memo                              = "";
//
//        //A : 날짜
//        $c_str = "A";
//        $cval = str_replace("-", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $rowValid = false;
//            $error_count ++;
//            array_push($error_rows,$row_index + 2);
//            continue;
//        }else{
//            if(strlen($cval) == 8){
//                $date = date("Y-m-d", strtotime($cval));
//            }else {
//                $date = date("Y-m-d", \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($row[$c_str]));
//            }
//            $settle_date = $date;
//        }
//
//        //B : 판매처코드
//        $c_str = "B";
//        $cval = trim($row[$c_str]);
//        if ($cval == "") {
//            $rowValid  = false;
//            $error_count ++;
//            array_push($error_rows,$row_index + 2);
//            continue;
//        } else {
//            $seller_idx  = $cval;
//        }
//
//        //C : 상품옵션코드
//        $c_str = "C";
//        $cval = trim($row[$c_str]);
//        if ($cval == "") {
//            $rowValid = false;
//            $error_count ++;
//            array_push($error_rows,$row_index + 2);
//            continue;
//        } else {
//            $product_idx  = $cval;
//        }
//
//        //D: 판매수량
//        $c_str = "D";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $row[$c_str] = 0;
//            $product_option_cnt = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            }else {
//                $row[$c_str] = $cval;
//                $product_option_cnt = $cval;
//            }
//        }
//
//        //E : 판매단가
//        $c_str = "E";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $order_unit_price = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            }else {
//                $order_unit_price = $cval;
//            }
//        }
//
//        //F : 판매수수료 수수료
//        $c_str = "F";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_sale_commission_ex_vat = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_sale_commission_ex_vat = $cval;
//            }
//        }
//        //G : 판매수수료 공급가액
//        $c_str = "G";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_sale_commission_in_vat = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_sale_commission_in_vat = $cval;
//            }
//        }
//
//        //H : 매출배송비 배송비
//        $c_str = "H";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $row[$c_str] = 0;
//            $settle_delivery_in_vat = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_delivery_in_vat = $cval;
//            }
//        }
//
//        //I : 매출배송비 공급가액
//        $c_str = "I";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $row[$c_str] = 0;
//            $settle_delivery_ex_vat = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_delivery_ex_vat = $cval;
//            }
//        }
//
//        //J : 판매가 판매가
//        $c_str = "J";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_sale_supply = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_sale_supply = $cval;
//            }
//        }
//
//        //K : 판매가 공급가액
//        $c_str = "K";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_sale_supply_ex_vat = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_sale_supply_ex_vat = $cval;
//            }
//        }
//
//        //L : 매입단가 매입가
//        $c_str = "L";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_purchase_unit_supply = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_purchase_unit_supply = $cval;
//            }
//        }
//
//        //M : 매입단가 공급가액
//        $c_str = "M";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_purchase_unit_supply_ex_vat = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_purchase_unit_supply_ex_vat = $cval;
//            }
//        }
//
//        //N : 매입배송비 배송비
//        $c_str = "N";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_purchase_delivery_in_vat = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_purchase_delivery_in_vat = $cval;
//            }
//        }
//
//        //O : 매입배송비 공급가액
//        $c_str = "O";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_purchase_delivery_ex_vat = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_purchase_delivery_ex_vat = $cval;
//            }
//        }
//
//        //P : 매입가 단가
//        $c_str = "P";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_purchase_supply = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_purchase_supply = $cval;
//            }
//        }
//
//        //Q : 매입가 공급가액
//        $c_str = "Q";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_purchase_supply_ex_vat = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_purchase_supply_ex_vat = $cval;
//            }
//        }
//
//        //R : 정산/배송비
//        $c_str = "R";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_settle_amt = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_settle_amt = $cval;
//            }
//        }
//
//        //S : 광고비
//        $c_str = "S";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_ad_amt = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_ad_amt = $cval;
//            }
//        }
//
//        //T : 매출이익
//        $c_str = "T";
//        $cval = str_replace(",", "", trim($row[$c_str]));
//        if ($cval == "") {
//            $settle_sale_profit = 0;
//        }else{
//            if(!is_numeric($cval)){
//                $rowValid = false;
//                $error_count ++;
//                array_push($error_rows,$row_index + 2);
//                continue;
//            } else {
//                $settle_sale_profit = $cval;
//            }
//        }
//
//        $row["valid"] = $rowValid;
//
//        if($rowValid) {
//            $normal_count ++;
//        }
//    }
//}

$objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
$chunkFilter = new chunkReadFilter();
$objReader->setReadFilter($chunkFilter);
$objReader->setReadDataOnly(true);
//$objReader->setInputEncoding("EUC-KR");
$startRow = 3;
$chunkSize = 1000;
$highestRow = 10000;
$normal_count = 0;
$error_count = 0;
$error_rows = Array();
$start_memory = memory_get_usage();
$row_index = 0;
print("start memory: ".date("Y-m-d H:i:s"));
print("<br>");
print("start time: ");
print(memory_get_usage());
print("<br>");
for ($startRow = 3; $startRow < $highestRow + 3; $startRow = $startRow + $chunkSize) {
    $chunkFilter->setRows($startRow, $chunkSize);
    $spreadsheet = $objReader->load("D:/download/판매일보 업로드 테스트.xlsx");
    $spreadsheet->garbageCollect();
    $maxRow = $spreadsheet->getActiveSheet()->getHighestRow();
    $maxCol = $spreadsheet->getActiveSheet()->getHighestColumn();
    $sheetData = $spreadsheet->getActiveSheet()->rangeToArray('A' . $startRow . ':' . $maxCol . $maxRow, null, true, true, true);
    print(memory_get_usage());
    $spreadsheet->disconnectWorksheets();
    unset($spreadsheet);
    print("<br>");

    foreach($sheetData as $row)
    {
        $rowValid = true;
        //Row Index
        $row_index++;
        //A : 날짜
        $c_str = "A";
        $cval = str_replace("-", "", trim($row[$c_str]));
        if ($cval == "") {
            $rowValid = false;
            $error_count ++;
            array_push($error_rows,$row_index + 2);
            continue;
        }

        //B : 판매처코드
        $c_str = "B";
        $cval = trim($row[$c_str]);
        if ($cval == "") {
            $rowValid  = false;
            $error_count ++;
            array_push($error_rows,$row_index + 2);
            continue;
        }

        //C : 상품옵션코드
        $c_str = "C";
        $cval = trim($row[$c_str]);
        if ($cval == "") {
            $rowValid = false;
            $error_count ++;
            array_push($error_rows,$row_index + 2);
            continue;
        }

        //D: 판매수량
        $c_str = "D";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {
            $row[$c_str] = 0;

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //E : 판매단가
        $c_str = "E";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //F : 판매수수료 수수료
        $c_str = "F";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }
        //G : 판매수수료 공급가액
        $c_str = "G";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //H : 매출배송비 배송비
        $c_str = "H";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //I : 매출배송비 공급가액
        $c_str = "I";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //J : 판매가 판매가
        $c_str = "J";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //K : 판매가 공급가액
        $c_str = "K";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //L : 매입단가 매입가
        $c_str = "L";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //M : 매입단가 공급가액
        $c_str = "M";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //N : 매입배송비 배송비
        $c_str = "N";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //O : 매입배송비 공급가액
        $c_str = "O";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //P : 매입가 단가
        $c_str = "P";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //Q : 매입가 공급가액
        $c_str = "Q";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //R : 정산/배송비
        $c_str = "R";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {

        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //S : 광고비
        $c_str = "S";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {
        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        //T : 매출이익
        $c_str = "T";
        $cval = str_replace(",", "", trim($row[$c_str]));
        if ($cval == "") {
        }else{
            if(!is_numeric($cval)){
                $rowValid = false;
                $error_count ++;
                array_push($error_rows,$row_index + 2);
                continue;
            }
        }

        $row["valid"] = $rowValid;

        if($rowValid) {
            $normal_count ++;
        }
    }
}
print("<br>");
print("전체: ".$row_index);
print("<br>");
print("정상: ".$normal_count);
print("<br>");
print("오류: ".$error_count);
print("<br>");
print("오류행: ");
echo "<pre>\n";
print_r($error_rows);
echo "</pre>\n";
print("<br>");
print("end memory: ".memory_get_usage());
print("<br>");
print("end time: ".date("Y-m-d H:i:s"));
print("<br>");
