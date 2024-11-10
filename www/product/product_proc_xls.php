<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 일괄등록 관련 Process (excel)
 */
//Page Info
$pageMenuIdx = 38;
//Init
include "../_init_.php";

$C_Users = new Users();
$C_Product = new Product();
$C_Code = new Code();
$C_Vendor = new Vendor();
$C_Seller = new Seller();
$C_Supplier = new Supplier();
$C_Category = new Category();
$C_VendorGrade = new VendorGrade();

//벤더사 할인율 가져오기
$_vendor_grade_list = $C_VendorGrade->getVendorGradeList();
foreach($_vendor_grade_list as $vg)
{
	$tmp = "product_option_sale_price_".$vg["vendor_grade"]."_percent";
	$$tmp = $vg["vendor_grade_discount"];
}
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$mode                   = $_POST["mode"];
$act                    = $_POST["act"];
$xls_filename           = $_POST["xls_filename"];
$xls_validrow           = $_POST["xls_validrow"];

$response = array();
$response["result"] = false;
$response["msg"] = "";
$response["err"] = array();
$response["userdata"] = array("field_name" => "");

$xls_filename_fullpath = DY_XLS_UPLOAD_PATH . "/" . $xls_filename;

if(file_exists($xls_filename_fullpath) && !is_dir($xls_filename_fullpath)) {
	$spreadsheet = IOFactory::load($xls_filename_fullpath);
	$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

	//!!!!배열 전체를 검색하여 엔터 값을 빈칸으로 치환 [사용 함수 : removeXlsLinebreak()]
	array_walk_recursive($sheetData, 'removeXlsLinebreak');

	if ($mode == "add") {

		/*
		 * A : 판매타입
		 * B : 공급처코드
		 * C : 상품명
		 * D : 브랜드명
		 * E : 출고지정보
		 * F : Location
		 * G : 공급처 상품명
		 * H : 공급처 옵션
		 * I : 판매처
		 * J : 원산지
		 * K : 제조사
		 * L : 담당MD
		 * M : 매출배송비
		 * N : 매입배송비
		 * O : 배송타입
		 * P : 카테고리1
		 * Q : 카테고리2
		 * R : 판매시작일
		 * S : 대상세금종류
		 * T : A/S안내
		 * U : 상품설명
		 * V : 옵션1
		 * W : 옵션2
		 * X : 옵션3
		 * Y : 판매기준가격
		 * Z : 재고경고수량
		 * AA : 재고위협수량
		 * AB : 매입가
		 */

		if(count($sheetData) > 1) {
			array_shift($sheetData);
			array_shift($sheetData);
			$listData = array();
			$inserted_count = 0;
			foreach($sheetData as $row)
			{
				$rowValid = true;

				//A : 판매타입
				$c_str = "A";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "판매타입이 입력되지 않았습니다.";
				}else{
					if($cval != "사입/자체" && $cval != "위탁")
					{
						$rowValid = false;
						$returnMsg = "판매타입은 '사입/자체' 또는 '위탁' 만 가능합니다.";
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//B : 공급처코드
				$c_str = "B";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "공급처코드가 입력되지 않았습니다.";
				}else{
					if(!$C_Supplier->getSupplierData($cval))
					{
						$rowValid = false;
						$returnMsg = "잘못된 공급처코드 입니다.";
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//C: 상품명
				$c_str = "C";
				$cval = (trim($row[$c_str]));
				//$row[$c_str] = $cval;
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "상품명이 입력되지 않았습니다.";
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//I: 판매처 체크
				$c_str = "I";
				$cval = (trim($row[$c_str]));
				//$row[$c_str] = $cval;
				$returnMsg = "";
				if ($cval != "") {
					if(!is_numeric($cval)) {
						$rowValid  = false;
						$returnMsg = "판매처는 코드로 입력해야 합니다.";
					}else{
						if(!$C_Seller->getAllSellerData($cval))
						{
							$rowValid = false;
							$returnMsg = "잘못된 판매처코드 입니다.";
						}
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//M : 매출배송비
				$c_str = "M";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval != "") {
					if(!is_numeric($cval)){
						$rowValid = false;
						$returnMsg = "매출배송비는 숫자만 허용됩니다.";
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//N : 매입배송비
				$c_str = "N";
				$cval = str_replace(",", "", trim($row[$c_str]));
				$returnMsg = "";
				if ($cval != "") {
					if(!is_numeric($cval)){
						$rowValid = false;
						$returnMsg = "매입배송비는 숫자만 허용됩니다.";
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//O : 배송타입
				$c_str = "O";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval != "") {
					if($cval != "직배" && $cval != "택배")
					{
						$rowValid = false;
						$returnMsg = "배송타입은 '택배' 또는 '직배' 만 가능합니다.";
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//P : 카테고리1
				$c_str = "P";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval != "") {
					$category_l_info = $C_Category->getCategoryLByName($cval);
					if(!$category_l_info){
						$rowValid = false;
						$returnMsg = "존재 하지 않는 카테고리1 입니다.";
					}else{
						$row["category_l_info"] = $category_l_info["idx"];
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//Q : 카테고리2
				if($category_l_info) {
					$c_str     = "P";
					$cval      = trim($row[$c_str]);
					$returnMsg = "";
					if ($cval != "") {
						$category_m_info = $C_Category->getCategoryMByName($category_l_info["idx"], $cval);
						if (!$category_m_info) {
							$rowValid  = false;
							$returnMsg = "존재 하지 않는 카테고리2 입니다.";
						}else{
							$row["category_m_info"] = $category_m_info["idx"];
						}
					}
					if (!$rowValid) $row[$c_str] = $returnMsg;
				}

				//R : 판매시작일
				$c_str = "R";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval != "") {
					$row[$c_str] = date('Y-m-d', strtotime($cval));
				}

				//S: 대상세금종류
				$c_str = "S";
				$cval = trim($row[$c_str]);
				$returnMsg = "";
				if ($cval == "") {
					$rowValid = false;
					$returnMsg = "대상세금종류가 입력되지 않았습니다.";
				}else{
					if($cval != "과세" && $cval != "면세" && $cval != "영세")
					{
						$rowValid = false;
						$returnMsg = "대상세금종류는 '과세' 또는 '면세' 또는 '영세' 만 가능합니다.";
					}else{
						if($cval == "과세") {
							$row["PRODUCT_TAX_TYPE"] = "TAXATION";
						}elseif($cval == "면세") {
							$row["PRODUCT_TAX_TYPE"] = "FREE";
						}elseif($cval == "영세") {
							$row["PRODUCT_TAX_TYPE"] = "SMALL";
						}
					}
				}
				if(!$rowValid) $row[$c_str] = $returnMsg;

				//V: 옵션1
				$c_str = "V";
				$cval = trim($row[$c_str]);

				if ($cval != "") {
                    //Y : 판매기준가
                    $c_str = "Y";
                    $cval = str_replace(",", "", trim($row[$c_str]));
                    $returnMsg = "";
                    if ($cval == "") {
                        $rowValid = false;
                        $returnMsg = "판매기준가가 입력되지 않았습니다.";
                    } else {
                        if (!is_numeric($cval)) {
                            $rowValid = false;
                            $returnMsg = "판매기준가가는 숫자만 허용됩니다.";
                        } else {
                            if (intval($cval) == 0) {
                                $rowValid = false;
                                $returnMsg = "판매기준가가는 0이 될 수 없습니다.";
                            }
                        }
                    }
                    if (!$rowValid) $row[$c_str] = $returnMsg;

                    //Z : 재고경고수량
                    $c_str = "Z";
                    $cval = str_replace(",", "", trim($row[$c_str]));
                    $returnMsg = "";
                    if ($cval != "") {
                        if (!is_numeric($cval)) {
                            $rowValid = false;
                            $returnMsg = "재고경고수량은 숫자만 허용됩니다.";
                        }
                    }
                    if (!$rowValid) $row[$c_str] = $returnMsg;

                    //AA : 재고위협수량
                    $c_str = "AA";
                    $cval = str_replace(",", "", trim($row[$c_str]));
                    $returnMsg = "";
                    if ($cval != "") {
                        if (!is_numeric($cval)) {
                            $rowValid = false;
                            $returnMsg = "재고위협수량은 숫자만 허용됩니다.";
                        }
                    }
                    if (!$rowValid) $row[$c_str] = $returnMsg;

                    //AB : 매입가
                    $c_str = "AB";
                    $cval = str_replace(",", "", trim($row[$c_str]));
                    $returnMsg = "";
                    if ($cval != "") {
                        if (!is_numeric($cval)) {
                            $rowValid = false;
                            $returnMsg = "매입가는 숫자만 허용됩니다.";
                        }
                    }
                    if (!$rowValid) $row[$c_str] = $returnMsg;
                } else {
                    //W 옵션2
                    $c_str = "W";
                    $returnMsg = "옵션1이 입력되지 않았습니다.";
                    $row["V"] = $returnMsg;

                    //X 옵션3
                    $c_str = "X";
                    $rowValid = false;
                    $returnMsg = "옵션1이 입력되지 않았습니다.";
                    $row["V"] = $returnMsg;

                    //Y : 판매기준가
                    $c_str = "Y";
                    $cval = str_replace(",", "", trim($row[$c_str]));
                    $returnMsg = "";
                    if ($cval != "") {
                        $rowValid = false;
                        $returnMsg = "옵션1이 입력되지 않았습니다.";
                    }
                    if (!$rowValid) $row[$c_str] = $returnMsg;

                    //Z : 재고경고수량
                    $c_str = "Z";
                    $cval = str_replace(",", "", trim($row[$c_str]));
                    $returnMsg = "";
                    if ($cval != "") {
                        $rowValid = false;
                        $returnMsg = "옵션1이 입력되지 않았습니다.";
                    }
                    if (!$rowValid) $row[$c_str] = $returnMsg;

                    //AA : 재고위협수량
                    $c_str = "AA";
                    $cval = str_replace(",", "", trim($row[$c_str]));
                    $returnMsg = "";
                    if ($cval != "") {
                        $rowValid = false;
                        $returnMsg = "옵션1이 입력되지 않았습니다.";
                    }
                    if (!$rowValid) $row[$c_str] = $returnMsg;

                    //AB : 매입가
                    $c_str = "AB";
                    $cval = str_replace(",", "", trim($row[$c_str]));
                    $returnMsg = "";
                    if ($cval != "") {
                        $rowValid = false;
                        $returnMsg = "옵션1이 입력되지 않았습니다.";
                    }
                    if (!$rowValid) $row[$c_str] = $returnMsg;
                }


				$row["valid"] = $rowValid;

				//$response["err"][] = $row;

				if($act == "grid") {
					//리스트로 반환
					//$listData[] = $row;
					if($rowValid) {
                        //옵션 처리
                        if ($row["V"] != ""){
                            $option1 = trim($row["V"]); //옵션1
                            $option2 = trim($row["W"]); //옵션2
                            $option3 = trim($row["X"]); //옵션3

                            $product_option_mix_1_ary = explode(",", $option1);
                            $product_option_mix_2_ary = explode(",", $option2);
                            $product_option_mix_3_ary = explode(",", $option3);

                            $product_option_mix_1_ary = array_filter($product_option_mix_1_ary, function ($value) {
                                return $value !== '';
                            });
                            $product_option_mix_2_ary = array_filter($product_option_mix_2_ary, function ($value) {
                                return $value !== '';
                            });
                            $product_option_mix_3_ary = array_filter($product_option_mix_3_ary, function ($value) {
                                return $value !== '';
                            });

                            $product_option_name_list = array();
                            foreach ($product_option_mix_1_ary as $mix1) {
                                $mix1_val = trim($mix1);
                                if (count($product_option_mix_2_ary) > 0) {
                                    foreach ($product_option_mix_2_ary as $mix2) {
                                        $mix2_val = trim($mix2);
                                        if (count($product_option_mix_3_ary) > 0) {
                                            foreach ($product_option_mix_3_ary as $mix3) {
                                                $mix3_val = trim($mix3);
                                                $product_option_name_list[] = sprintf("%s-%s-%s", $mix1_val, $mix2_val, $mix3_val);
                                            }
                                        } else {
                                            $product_option_name_list[] = sprintf("%s-%s", $mix1_val, $mix2_val);
                                        }
                                    }
                                } else {
                                    $product_option_name_list[] = $mix1_val;
                                }
                            }


                            foreach ($product_option_name_list as $option) {
                                //옵션명
                                $row["V"] = $option;
                                $listData[] = $row;
                            }
                        }else{
                            $listData[] = $row;
                        }
					}else{
						$listData[] = $row;
					}


				} elseif($act == "save") {
					//적용!!

					if($rowValid) {
						//옵션 처리
						$option1 = trim($row["V"]); //옵션1
						$option2 = trim($row["W"]); //옵션2
						$option3 = trim($row["X"]); //옵션3

						$product_option_mix_1_ary = explode(",", $option1);
						$product_option_mix_2_ary = explode(",", $option2);
						$product_option_mix_3_ary = explode(",", $option3);

						$product_option_mix_1_ary = array_filter($product_option_mix_1_ary, function ($value) {
							return $value !== '';
						});
						$product_option_mix_2_ary = array_filter($product_option_mix_2_ary, function ($value) {
							return $value !== '';
						});
						$product_option_mix_3_ary = array_filter($product_option_mix_3_ary, function ($value) {
							return $value !== '';
						});

						$product_option_name_list = array();
						foreach ($product_option_mix_1_ary as $mix1) {
							$mix1_val = trim($mix1);
							if (count($product_option_mix_2_ary) > 0) {
								foreach ($product_option_mix_2_ary as $mix2) {
									$mix2_val = trim($mix2);
									if (count($product_option_mix_3_ary) > 0) {
										foreach ($product_option_mix_3_ary as $mix3) {
											$mix3_val                   = trim($mix3);
											$product_option_name_list[] = sprintf("%s-%s-%s", $mix1_val, $mix2_val, $mix3_val);
										}
									} else {
										$product_option_name_list[] = sprintf("%s-%s", $mix1_val, $mix2_val);
									}
								}
							} else {
								$product_option_name_list[] = $mix1_val;
							}
						}

						$args                              = array();
						$args["product_sale_type"]         = (trim($row["A"]) == "사입/자체") ? "SELF" : "CONSIGNMENT";
						$args["supplier_idx"]              = trim($row["B"]);
						$args["product_name"]              = trim($row["C"]);
						$args["product_supplier_name"]     = trim($row["G"]);
						$args["product_supplier_option"]   = trim($row["H"]);
						$args["seller_idx"]                = trim($row["I"]);
						$args["product_origin"]            = trim($row["J"]);
						$args["product_manufacturer"]      = trim($row["K"]);
						$args["product_md"]                = trim($row["L"]);
						$args["product_delivery_fee_sale"] = str_replace(",", "", trim($row["M"]));
						$args["product_delivery_fee_buy"]  = str_replace(",", "", trim($row["N"]));
						$args["product_delivery_type"]     = (trim($row["O"]) == "직배") ? "DIRECT_DELIVERY" : "COURIER_DELIVERY";;
						$args["product_category_l_idx"]    = $row["category_l_info"];
						$args["product_category_m_idx"]    = $row["category_m_info"];
						$args["product_sales_date"]        = (trim($row["R"]) != "") ? date('Y-m-d', strtotime(trim($row["R"]))) : "";
						$args["product_tax_type"]          = $row["PRODUCT_TAX_TYPE"];
						$args["product_desc"]              = trim($row["U"]);
						$args["product_vendor_show"]       = "HIDE";
						$args["product_vendor_show_list"]  = "";
						$args["product_detail_mall_name"]  = "";
						$args["product_detail_url"]        = "";

						$product_idx = $C_Product -> insertProduct($args);
						$response["err"][] = $product_idx;

						//판매기준가격
						$product_option_sale_price = str_replace(",", "", trim($row["Y"]));
						$product_option_sale_price_A = $product_option_sale_price - ceil($product_option_sale_price * ($product_option_sale_price_A_percent/100));
						$product_option_sale_price_B = $product_option_sale_price - ceil($product_option_sale_price * ($product_option_sale_price_B_percent/100));
						$product_option_sale_price_C = $product_option_sale_price - ceil($product_option_sale_price * ($product_option_sale_price_C_percent/100));
						$product_option_sale_price_D = $product_option_sale_price - ceil($product_option_sale_price * ($product_option_sale_price_D_percent/100));
						$product_option_sale_price_E = $product_option_sale_price - ceil($product_option_sale_price * ($product_option_sale_price_E_percent/100));

						$args2                                  = array();
						$args2["product_idx"]                   = $product_idx;
						$args2["product_option_sale_price"]     = str_replace(",", "", trim($row["Y"]));;
						$args2["product_option_sale_price_A"]   = $product_option_sale_price_A;
						$args2["product_option_sale_price_B"]   = $product_option_sale_price_B;
						$args2["product_option_sale_price_C"]   = $product_option_sale_price_C;
						$args2["product_option_sale_price_D"]   = $product_option_sale_price_D;
						$args2["product_option_sale_price_E"]   = $product_option_sale_price_E;
						$args2["product_option_warning_count"]  = str_replace(",", "", trim($row["Z"]));;
						$args2["product_option_danger_count"]   = str_replace(",", "", trim($row["AA"]));;
						$args2["product_option_purchase_price"] = str_replace(",", "", trim($row["AB"]));;
						foreach ($product_option_name_list as $option) {
							//옵션명
							$args2["product_option_name"] = $option;
							$C_Product -> insertProductOption($args2);

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

	} elseif ($mode == "mod") {

		//업로드 된 엑셀 검증 배열 Include
		include_once "../include/_include_product_select_update_field_info.php";

		/*
		 * A : 상품코드 또는 옵션코드
		 * B : 업데이트 필드 값
		 */

		if(count($sheetData) > 1) {

			$head_row = $sheetData[1];
			/*
			 * 수정 필드 확인
			 */

			$field_name_text = trim($head_row["B"]);
			$update_target = $productSelectedUpdateFieldInfo[$field_name_text];
			if($update_target) {

				$response["userdata"]["field_name"] = $field_name_text;

				array_shift($sheetData);
				$listData       = array();
				$inserted_count = 0;
				foreach ($sheetData as $row) {
					$rowValid = true;

					//A : 상품코드 또는 옵션코드
					$c_str = "A";
					$cval = trim($row[$c_str]);
					$returnMsg = "";
					if ($cval == "") {
						$rowValid = false;
						$returnMsg = "상품코드 또는 옵션코드가 입력되지 않았습니다.";
					} else {
						if($update_target["type"] == "product")
						{
							$exists_target = $C_Product -> getProductData($cval);
						}else{
							$exists_target = $C_Product -> getProductOptionData($cval);
						}

						if(!$exists_target){
							$rowValid = false;
							$returnMsg = "존재하지 않는 상품코드 또는 옵션코드 입니다.";
						}
					}
					if(!$rowValid) $row[$c_str] = $returnMsg;
					
					//B : 수정 필드
					$c_str     = "B";
					$cval      = trim($row[$c_str]);
					if($exists_target) {
						$returnMsg = "";
						if ($cval == "") {
							$rowValid  = false;
							$returnMsg = "수정할 값이 입력되지 않았습니다.";
						} else {
							$valid_rst = call_user_func($update_target["validate"], $cval);
							if (!$valid_rst["result"]) {
								$rowValid  = false;
								$returnMsg = $valid_rst["msg"];
							}else{
								$row[$c_str] = $valid_rst["val"];
							}
						}
						if (!$rowValid) $row[$c_str] = $returnMsg;
					}else{
						$row[$c_str] = $cval;
					}
					$row["valid"]      = $rowValid;
					$response["err"][] = $row;

					if ($act == "grid") {
						//리스트로 반환
						$listData[] = $row;

					} elseif ($act == "save") {
						//적용!!
						if ($rowValid) {

							//선택 업데이트 실행
							$tmp_rst = $C_Product->updateProductXlsSelected($update_target["type"], trim($row["A"]), $update_target["field"], $row["B"]);

							$inserted_count++;
						}
					}
				}

				$response["result"] = true;
				$response["msg"]    = $inserted_count;
			}else{
				$response["result"] = false;
				$response["msg"]    = $head_row["B"] . " => 수정 필드 선택이 올바르지 않습니다.";
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