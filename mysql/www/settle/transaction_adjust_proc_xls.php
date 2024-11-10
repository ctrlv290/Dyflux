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
		 * A : 보정유형
		 * B : 날짜
		 * C : 판매처코드
		 * D : 상품코드
		 * E : 판매단가
		 * F : 판매 수량
		 * G : 판매가 판매가
		 * H : 판매가 공급가액
		 * I : 판매수수료 수수료
		 * J : 판매수수료 공급가액
		 * K : 매출배송비 배송비
		 * L : 매출배송비 공급가액
		 * M : 배송비수수료 수수료
		 * N : 배송비수수료 공급가액
		 * O : 매입단가 매입가
		 * P : 매입단가 공급가액
		 * Q : 매입가 단가
		 * R : 매입가 공급가액
		 * S : 매입배송비 배송비
		 * T : 배입배송비 공급가액
		 * U : 정산/배송비
		 * V : 광고비
		 * W : 매출이익
		 * X : 매출이익 공급가액
		 * Y : 메모
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
            array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			$row_index = 0;
			foreach($sheetData as $row)
			{
				//4개 열에 값이 모두 없으면 종료
				if(trim($row["A"]) == "" && trim($row["B"]) == "" && trim($row["C"]) == "" && trim($row["D"]) == "")
				{
					continue;
				}

				$rowValid = true;

				//Row Index
				$row_index++;
				$row["xls_idx"] = $row_index;

				//보정 정보 초기화
				$settle_type                              = "";
				$settle_idx                               = "";
				$supplier_idx                             = "";
				$settle_date                              = "";
				$seller_idx                               = "";
                $product_idx                              = "";
				$order_unit_price                         = "";
                $settle_sale_supply                       = "";
                $settle_sale_commission_ex_vat            = "";
                $settle_sale_supply_ex_vat                = "";
                $settle_sale_commission_in_vat            = "";
                $settle_delivery_in_vat                   = "";
                $settle_delivery_commission_ex_vat        = "";
                $settle_delivery_ex_vat                   = "";
                $settle_delivery_commission_in_vat        = "";
                $settle_purchase_unit_supply              = "";
                $settle_purchase_supply                   = "";
                $settle_purchase_unit_supply_ex_vat       = "";
                $settle_purchase_supply_ex_vat            = "";
                $settle_purchase_delivery_in_vat          = "";
                $settle_settle_amt                        = "";
                $settle_purchase_delivery_ex_vat          = "";
                $settle_ad_amt                            = "";
                $settle_sale_profit                       = "";
				$settle_sale_profit_ex_vat                = "";
                $settle_memo                              = "";

                //A : 보정유형
                $c_str = "A";
                $cval = trim($row[$c_str]);
                $returnMsg = "";
                if ($cval == "") {
                    $rowValid = false;
                    $returnMsg = "보정유형이 입력되지 않았습니다.";
                }else{
                    if($cval == "매입보정"){
                        $settle_type = "ADJUST_PURCHASE";
                    }elseif($cval == "매출보정"){
                        $settle_type = "ADJUST_SALE";
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //B : 날짜
                $c_str = "B";
                $cval = str_replace("-", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $rowValid = false;
                    $returnMsg = "날짜가 입력되지 않았습니다.";
                }else{
                    if(strlen($cval) == 8){
                        $date = date("Y-m-d", strtotime($cval));
                    }else {
                        $rowValid = false;
                        $returnMsg = "잘못된 날짜 형식입니다.";
//                      $date= date("Y-m-d", \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($cval));
                    }
                    $row[$c_str] = $date;
                    $settle_date = $date;
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

				//C : 판매처코드
				$c_str = "C";
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

                //D : 상품옵션코드
                $c_str = "D";
                $cval = trim($row[$c_str]);
                $returnMsg = "";
                if ($cval == "") {
                    $rowValid = false;
                    $returnMsg = "상품옵션코드가 입력되지 않았습니다.";
                } else {
                        $exists_target = $C_Product -> getProductOptionDataDetail($cval);
                    if(!$exists_target){
                        $rowValid = false;
                        $returnMsg = "존재하지 않는 상품옵션코드 입니다.";
                    }else{
                        $product_idx  = $exists_target["product_idx"];
                        $product_option_idx  = $exists_target["product_option_idx"];
                        $supplier_idx  = $exists_target["supplier_idx"];
                        $row[$c_str] = $exists_target["product_option_name"];
                        $row['product_name'] = $exists_target["product_name"];
                        $row['supplier_name'] = $exists_target["supplier_name"];
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

				//E : 판매단가
                $c_str = "E";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $order_unit_price = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "판매단가는 숫자만 허용됩니다.";
                    }else {
                        $row[$c_str] = $cval;
                        $order_unit_price = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //F: 판매수량
                $c_str = "F";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $product_option_cnt = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "판매수량은 숫자만 허용됩니다.";
                    }else {
                        $row[$c_str] = $cval;
                        $product_option_cnt = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //G : 판매가 판매가
                $c_str = "G";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_sale_supply = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "판매가는 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_sale_supply = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //H : 판매가 공급가액
                $c_str = "H";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_sale_supply_ex_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "공급가액은 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_sale_supply_ex_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //I : 판매수수료 수수료
                $c_str = "I";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_sale_commission_in_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "수수료는 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_sale_commission_in_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //J : 판매수수료 공급가액
                $c_str = "J";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_sale_commission_ex_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "공급가액은 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_sale_commission_ex_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //K : 매출배송비 배송비
                $c_str = "K";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_delivery_in_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "배송비는 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_delivery_in_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //L : 매출배송비 배송비
                $c_str = "L";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_delivery_ex_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "공급가액은 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_delivery_ex_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //M : 배송비수수료 수수료
                $c_str = "M";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_delivery_commission_in_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "수수료는 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_delivery_commission_in_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //N : 배송비수수료 공급가액
                $c_str = "N";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_delivery_commission_ex_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "공급가액은 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_delivery_commission_ex_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //O : 매입단가 매입가
                $c_str = "O";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_purchase_unit_supply = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "매입가는 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_purchase_unit_supply = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //P : 매입단가 공급가액
                $c_str = "P";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_purchase_unit_supply_ex_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "공급가액 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_purchase_unit_supply_ex_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //Q : 매입가 단가
                $c_str = "Q";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_purchase_supply = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "단가는 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_purchase_supply = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //R : 매입가 공급가액
                $c_str = "R";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_purchase_supply_ex_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "공급가액은 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_purchase_supply_ex_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //S : 매입배송비 배송비
                $c_str = "S";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_purchase_delivery_in_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "배송비는 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_purchase_delivery_in_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //T : 매입배송비 공급가액
                $c_str = "T";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_purchase_delivery_ex_vat = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "공급가액은 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_purchase_delivery_ex_vat = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //U : 정산/배송비
                $c_str = "U";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_settle_amt = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "정산/배송비는 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_settle_amt = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //V : 광고비
                $c_str = "V";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_ad_amt = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "광고비는 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_ad_amt = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                //W : 매출이익
                $c_str = "W";
                $cval = str_replace(",", "", trim($row[$c_str]));
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = 0;
                    $settle_sale_profit = 0;
                }else{
                    if(!is_numeric($cval)){
                        $rowValid = false;
                        $returnMsg = "매출이익은 숫자만 허용됩니다.";
                    } else {
                        $row[$c_str] = $cval;
                        $settle_sale_profit = $cval;
                    }
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

				//X : 매출이익 공급가액
				$c_str = "W";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval == "") {
					$row[$c_str] = 0;
					$settle_sale_profit_ex_vat= 0;
				}else{
					if(!is_numeric($cval)){
						$rowValid = false;
						$returnMsg = "매출이익은 숫자만 허용됩니다.";
					} else {
						$row[$c_str] = $cval;
						$settle_sale_profit_ex_vat = $cval;
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

                //Y : 메모
                $c_str = "Y";
                $cval = trim($row[$c_str]);
                $returnMsg = "";
                if ($cval == "") {
                    $row[$c_str] = "";
                }else{
                        $row[$c_str] = $cval;
                        $settle_memo = $cval;
                }
                if(!$rowValid) $row[$c_str] = $returnMsg;

                $row["valid"] = $rowValid;

                //$response["err"][] = $row;
                if($act == "grid") {
                    //리스트로 반환
                    //$listData[] = $row;
                    if($rowValid) {

                        $listData[] = $row;

                    }else{
                        $listData[] = $row;
                    }


                } elseif($act == "save") {
                    //적용!!

                    if($rowValid) {

                        $args                                       = array();
                        $args["settle_date"]                        = $settle_date;
                        $args["settle_type"]                        = $settle_type;
                        $args["seller_idx"]                         = $seller_idx;
                        $args["product_idx"]                        = $product_idx;
                        $args["product_option_idx"]                 = $product_option_idx;
                        $args["supplier_idx"]                       = $supplier_idx;
                        $args["product_option_cnt"]                 = $product_option_cnt;
                        $args["purchase_amt"]                       = 0;
                        $args["order_amt"]                          = 0;
                        $args["order_unit_price"]                   = $order_unit_price;
                        $args["product_option_purchase_price"]      = $product_option_purchase_price;
                        $args["settle_sale_supply"]                 = $settle_sale_supply;
                        $args["settle_sale_supply_ex_vat"]          = $settle_sale_supply_ex_vat;
                        $args["settle_sale_commission_ex_vat"]      = $settle_sale_commission_ex_vat;
                        $args["settle_sale_commission_in_vat"]      = $settle_sale_commission_in_vat;
                        $args["settle_delivery_in_vat"]             = $settle_delivery_in_vat;
                        $args["settle_delivery_ex_vat"]             = $settle_delivery_ex_vat;
                        $args["settle_delivery_commission_ex_vat"]  = $settle_delivery_commission_ex_vat;
                        $args["settle_delivery_commission_in_vat"]  = $settle_delivery_commission_in_vat;
                        $args["settle_purchase_supply"]             = $settle_purchase_supply;
                        $args["settle_purchase_supply_ex_vat"]      = $settle_purchase_supply_ex_vat;
                        $args["settle_purchase_delivery_in_vat"]    = $settle_purchase_delivery_in_vat;
                        $args["settle_purchase_delivery_ex_vat"]    = $settle_purchase_delivery_ex_vat;
                        $args["settle_sale_profit"]                 = $settle_sale_profit;
						$args["settle_sale_profit_ex_vat"]          = $settle_sale_profit_ex_vat;
                        $args["settle_sale_amount"]                 = 0;
                        $args["settle_sale_cost"]                   = 0;
                        $args["settle_memo"]                        = $settle_memo;
                        $args["settle_purchase_unit_supply"]        = $settle_purchase_unit_supply;
                        $args["settle_purchase_unit_supply_ex_vat"] = $settle_purchase_unit_supply_ex_vat;
                        $args["settle_settle_amt"]                  = $settle_settle_amt;
                        $args["settle_ad_amt"]                      = $settle_ad_amt;
                        $args["settle_sale_sum"]                    = $settle_sale_supply - $settle_sale_commission_in_vat + $settle_delivery_in_vat - $settle_delivery_commission_in_vat;
                        $args["settle_purchase_sum"]                = $settle_purchase_supply + $settle_purchase_delivery_in_vat;

                        $rst = $C_SETTLE->insertTransaction($args);

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