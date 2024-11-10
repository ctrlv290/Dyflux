<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 매칭일괄 등록 관련 Process (excel)
 * 판매처가 직접 등록 시 권한 작업 필요!
 */
//Page Info
$pageMenuIdx = 306;
//Init
include "../_init_.php";
$C_Product = new Product();
$C_Seller = new Seller();
$C_SETTLE = new Settle();

require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;

$pool = new ApcuCachePool();
$simpleCache = new SimpleCacheBridge($pool);
\PhpOffice\PhpSpreadsheet\Settings::setCache($simpleCache);

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

$mode                   = $_POST["mode"];
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
    if ($mode == "transaction_upload") {
        $objReader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $chunkFilter = new chunkReadFilter();
        $objReader->setReadFilter($chunkFilter);
        $objReader->setReadDataOnly(true);
//        $objReader->setInputEncoding("EUC-KR");
        $start_time = (date("Y-m-d H:i:s"));
        $startRow = 3;
        $chunkSize = 1000;
        $highestRow = 30000;
        $normal_count = 0;
        $error_count = 0;
        $error_rows = Array();
        $row_index = 0;
        $C_SETTLE->db_connect();
        $C_SETTLE->sqlTransactionBegin();  //트랜잭션 시작
        for ($startRow = 3; $startRow < $highestRow + 3; $startRow = $startRow + $chunkSize) {
            $chunkFilter->setRows($startRow, $chunkSize);
            $spreadsheet = $objReader->load($xls_filename_fullpath);
            $spreadsheet->garbageCollect();
            $maxRow = $spreadsheet->getActiveSheet()->getHighestRow();
            $maxCol = $spreadsheet->getActiveSheet()->getHighestColumn();
            $sheetData = $spreadsheet->getActiveSheet()->rangeToArray('A' . $startRow . ':' . $maxCol . $maxRow, null, true, true, true);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);

            foreach ($sheetData as $row) {
                $rowValid = true;
                //Row Index
                $row_index++;

                //보정 정보 초기화
                $settle_type = "";
                $settle_idx = "";
                $supplier_idx = "";
                $settle_date = "";
                $seller_idx = "";
                $product_idx = "";
                $order_unit_price = "";
                $settle_sale_supply = "";
                $settle_sale_commission_ex_vat = "";
                $settle_sale_supply_ex_vat = "";
                $settle_sale_commission_in_vat = "";
                $settle_delivery_in_vat = "";
                $settle_delivery_commission_ex_vat = "";
                $settle_delivery_ex_vat = "";
                $settle_delivery_commission_in_vat = "";
                $settle_purchase_unit_supply = "";
                $settle_purchase_supply = "";
                $settle_purchase_unit_supply_ex_vat = "";
                $settle_purchase_supply_ex_vat = "";
                $settle_purchase_delivery_in_vat = "";
                $settle_settle_amt = "";
                $settle_purchase_delivery_ex_vat = "";
                $settle_ad_amt = "";
                $settle_sale_profit = "";
                $settle_memo = "";

                //A : 날짜
                $c_str = "A";
                $cval = str_replace("-", "", trim($row[$c_str]));
                if ($cval == "") {
                    $rowValid = false;
                    $error_count++;
                    array_push($error_rows, $row_index + 2);
                    continue;
                } else {
                    if (strlen($cval) == 8) {
                        $date = date("Y-m-d", strtotime($cval));
                    } else {
                        $date = date("Y-m-d", \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($row[$c_str]));
                    }
                    $settle_date = $date;
                }

                //B : 판매처코드
                $c_str = "B";
                $cval = trim($row[$c_str]);
                if(isDYLogin()) {
                    if ($cval == "") {
                        $rowValid  = false;
                        $error_count ++;
                        array_push($error_rows,$row_index + 2);
                        continue;
                    } else {
                        $seller_info_tmp = $C_Seller->getUseSellerAllDataByName($cval);
                        if (!$seller_info_tmp) {
                            $rowValid  = false;
                            $error_count ++;
                            array_push($error_rows,$row_index + 2);
                            continue;
                        } else {
                            $seller_idx  = $seller_info_tmp["seller_idx"];
                        }
                    }
                }else{
                    $seller_info_tmp = $C_Seller->getUseSellerAllData($GL_Member["member_idx"]);
                    if (!$seller_info_tmp) {
                        $rowValid  = false;
                        $error_count ++;
                        array_push($error_rows,$row_index + 2);
                        continue;
                    } else {
                        $seller_idx  = $seller_info_tmp["seller_idx"];
                    }
                }

                //C : 상품옵션코드
                $c_str = "C";
                $cval = trim($row[$c_str]);
                if ($cval == "") {
                    $rowValid = false;
                    $error_count ++;
                    array_push($error_rows,$row_index + 2);
                    continue;
                } else {
                    $exists_target = $C_Product -> getProductOptionDataDetail($cval);
                    if(!$exists_target){
                        $rowValid = false;
                        $error_count ++;
                        array_push($error_rows,$row_index + 2);
                        continue;
                    }else{
                        $product_idx  = $exists_target["product_idx"];
                        $product_option_idx  = $exists_target["product_option_idx"];
                        $supplier_idx  = $exists_target["supplier_idx"];
                    }
                }

                //D: 판매수량
                $c_str = "D";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $product_option_cnt = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $row[$c_str] = $cval;
                        $product_option_cnt = $cval;
                    }
                }

                //E : 판매단가 & 판매가 
                $c_str = "E";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $order_unit_price = 0;
                    $settle_sale_supply = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $order_unit_price = $cval;
                        $settle_sale_supply = $order_unit_price * $product_option_cnt;
                    }
                }

                //F : 판매가 공급가액
                $c_str = "F";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_sale_supply_ex_vat = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_sale_supply_ex_vat = $cval * $product_option_cnt;
                    }
                }


                //G : 판매수수료 수수료
                $c_str = "G";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_sale_commission_in_vat = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_sale_commission_in_vat = $cval;
                    }
                }
                //H : 판매수수료 공급가액
                $c_str = "H";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_sale_commission_ex_vat = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_sale_commission_ex_vat = $cval;
                    }
                }

                //I : 매출배송비 배송비
                $c_str = "I";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_delivery_in_vat = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_delivery_in_vat = $cval;
                    }
                }

                //J : 매출배송비 공급가액
                $c_str = "J";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_delivery_ex_vat = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_delivery_ex_vat = $cval;
                    }
                }

                //K : 판매가 판매가 (매출합계)
                $c_str = "K";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_sale_supply = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_sale_sum = $cval;
                    }
                }

                //L : 매입단가 매입가
                $c_str = "L";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_purchase_unit_supply = 0;
                    $settle_purchase_supply =0 ;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_purchase_unit_supply = $cval;
                        $settle_purchase_supply = $settle_purchase_unit_supply * $product_option_cnt;
                    }
                }

                //M : 매입단가 공급가액
                $c_str = "M";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_purchase_unit_supply_ex_vat = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_purchase_unit_supply_ex_vat = $cval;
                        $settle_purchase_supply_ex_vat = $settle_purchase_unit_supply_ex_vat * $product_option_cnt;
                    }
                }

                //N : 매입배송비 배송비
                $c_str = "N";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_purchase_delivery_in_vat = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_purchase_delivery_in_vat = $cval;
                    }
                }

                //O : 매입배송비 공급가액
                $c_str = "O";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_purchase_delivery_ex_vat = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_purchase_delivery_ex_vat = $cval;
                    }
                }

                //P : 매입가 (매입합계)
                $c_str = "P";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_purchase_sum = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_purchase_sum = $cval;
                    }
                }

                //Q : 정산/배송비
                $c_str = "Q";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_settle_amt = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_settle_amt = $cval;
                    }
                }

                //R : 광고비
                $c_str = "R";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_ad_amt = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_ad_amt = $cval;
                    }
                }

                //S : 매출이익
                $c_str = "S";
                $cval = str_replace(",", "", trim($row[$c_str]));
                if ($cval == "") {
                    $settle_sale_profit = 0;
                } else {
                    if (!is_numeric($cval)) {
                        $rowValid = false;
                        $error_count++;
                        array_push($error_rows, $row_index + 2);
                        continue;
                    } else {
                        $settle_sale_profit = $cval;
                    }
                }

                $row["valid"] = $rowValid;

                if ($rowValid) {

                    $args = array();
                    $args["settle_date"] = $settle_date;
                    $args["settle_type"] = 'UPLOAD';
                    $args["settle_closing"] = 'Y';
                    $args["seller_idx"] = $seller_idx;
                    $args["product_idx"] = $product_idx;
                    $args["product_option_idx"] = $product_option_idx;
                    $args["supplier_idx"] = $supplier_idx;
                    $args["product_option_cnt"] = $product_option_cnt;
                    $args["purchase_amt"] = 0;
                    $args["order_amt"] = 0;
                    $args["order_unit_price"] = round($order_unit_price);
                    $args["settle_sale_supply"] = round($settle_sale_supply);
                    $args["settle_sale_supply_ex_vat"] = round($settle_sale_supply_ex_vat);
                    $args["settle_sale_commission_ex_vat"] = round($settle_sale_commission_ex_vat);
                    $args["settle_sale_commission_in_vat"] = round($settle_sale_commission_in_vat);
                    $args["settle_delivery_in_vat"] =round( $settle_delivery_in_vat);
                    $args["settle_delivery_ex_vat"] = round($settle_delivery_ex_vat);
                    $args["settle_delivery_commission_ex_vat"] = round($settle_delivery_commission_ex_vat);
                    $args["settle_delivery_commission_in_vat"] = round($settle_delivery_commission_in_vat);
                    $args["settle_purchase_supply"] = round($settle_purchase_supply);
                    $args["settle_purchase_supply_ex_vat"] = round($settle_purchase_supply_ex_vat);
                    $args["settle_purchase_delivery_in_vat"] = round($settle_purchase_delivery_in_vat);
                    $args["settle_purchase_delivery_ex_vat"] = round($settle_purchase_delivery_ex_vat);
                    $args["settle_sale_profit"] = round($settle_sale_profit);
                    $args["settle_sale_amount"] = 0;
                    $args["settle_sale_cost"] = 0;
                    $args["settle_memo"] = $settle_memo;
                    $args["settle_purchase_unit_supply"] = round($settle_purchase_unit_supply);
                    $args["settle_purchase_unit_supply_ex_vat"] = round($settle_purchase_unit_supply_ex_vat);
                    $args["settle_settle_amt"] = round($settle_settle_amt);
                    $args["settle_ad_amt"] = round($settle_ad_amt);
                    $args["settle_sale_sum"] = round($settle_sale_sum);
                    $args["settle_purchase_sum"] = round($settle_purchase_sum);

                    $rst = $C_SETTLE->insertTransaction($args, true);

                    $normal_count++;
                }
            }
            unset($sheetData);
        }
        $response["result"] = true;
        $response["total_rows"] = $row_index;
        $response["normal_count"] = $normal_count;
        $response["error_count"] = $error_count;
        $response["error_rows"] = $error_rows;
        $response["msg"] = $normal_count;
        $response["start_time"] = $start_time;
        $response["end_time"] = date("Y-m-d H:i:s");
        $C_SETTLE->sqlTransactionCommit();
        $C_SETTLE->db_close();
    }
}else{
    $response["msg"] = "파일이 없습니다.";
}
echo json_encode($response, true);

?>