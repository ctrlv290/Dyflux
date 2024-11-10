<?php
/**
 * User: woox
 * Date: 2018-11-01
 * Desc: 상품 일괄 선택 수정 시
 *       업로드 된 엑셀 상의 필드 명 검증 및 수정 값 검증 배열
 */

$productSelectedUpdateFieldInfo = array(
	"[상품]공급처 코드" => array("type" => "product", "field" => "supplier_idx", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$C_Supplier = new Supplier();
			$rst        = $C_Supplier->getSupplierData($val);
			if (!$rst) {
				$returnAry["msg"] = "존재하지 않는 [상품]공급처 코드입니다.";
			} else {
				$returnAry["result"] = true;
				$returnAry["val"]    = $val;
			}
		} else {
			$returnAry["msg"] = "[상품]공급처 코드가 입력되지 않았습니다.";
		}
		$C_Supplier = null;
		return $returnAry;
	}),
	"[상품]상품명" => array("type" => "product", "field" => "product_name", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$returnAry["result"] = true;
			$returnAry["val"]    = $val;
		}else{
			$returnAry["msg"] = "[상품]상품명이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]공급처 상품명" => array("type" => "product", "field" => "product_supplier_name", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$returnAry["result"] = true;
			$returnAry["val"]    = $val;
		}else{
			$returnAry["msg"] = "[상품]공급처 상품명이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]공급처 옵션" => array("type" => "product", "field" => "product_supplier_option", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$returnAry["result"] = true;
			$returnAry["val"]    = $val;
		}else{
			$returnAry["msg"] = "[상품]공급처 옵션이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]판매처 코드" => array("type" => "product", "field" => "seller_idx", "validate" => function ($val) { $returnAry           = array();
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$C_Seller = new Seller();
			$rst        = $C_Seller->getSellerData($val);
			if (!$rst) {
				$returnAry["msg"] = "존재하지 않는 [상품]판매처 코드입니다.";
			} else {
				$returnAry["result"] = true;
				$returnAry["val"]    = $val;
			}
		} else {
			$returnAry["msg"] = "[상품]판매처 코드가 입력되지 않았습니다.";
		}
		$C_Seller = null;
		return $returnAry;
	}),
	"[상품]원산지" => array("type" => "product", "field" => "product_origin", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$returnAry["result"] = true;
			$returnAry["val"]    = $val;
		}else{
			$returnAry["msg"] = "[상품]가 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]제조사" => array("type" => "product", "field" => "product_manufacturer", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$returnAry["result"] = true;
			$returnAry["val"]    = $val;
		}else{
			$returnAry["msg"] = "[상품]제조사가 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]담당MD" => array("type" => "product", "field" => "product_md", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$returnAry["result"] = true;
			$returnAry["val"]    = $val;
		}else{
			$returnAry["msg"] = "[상품]담당MD가 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]매출배송비" => array("type" => "product", "field" => "product_delivery_fee_sale", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$val = str_replace(',', '', $val);
			if(!is_numeric($val)){
				$returnAry["msg"] = "[상품]매출배송비은 숫자만 허용됩니다.";
			}else {
				$returnAry["result"] = true;
				$returnAry["val"]    = $val;
			}
		}else{
			$returnAry["msg"] = "[상품]매출배송비가 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]매입배송비" => array("type" => "product", "field" => "product_delivery_fee_buy", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			$val = str_replace(',', '', $val);
			if(!is_numeric($val)){
				$returnAry["msg"] = "[상품]매입배송비은 숫자만 허용됩니다.";
			}else {
				$returnAry["result"] = true;
				$returnAry["val"]    = $val;
			}
		}else{
			$returnAry["msg"] = "[상품]매입배송비가 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]배송타입" => array("type" => "product", "field" => "product_delivery_type", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		if (trim($val) != "") {
			if(trim($val) != "택배" && trim($val) != "직배") {
				$returnAry["msg"] = "배송타입은 '택배' 또는 '직배' 만 가능합니다.";
			}else {
				$returnAry["result"] = true;
				$returnAry["val"]    = $val;
			}
		}else{
			$returnAry["msg"] = "[상품]배송타입이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]판매시작일" => array("type" => "product", "field" => "product_sales_date", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		if ($val != "") {
			if($timestamp = strtotime($val) !== false) {
				$returnAry["result"] = true;
				$returnAry["val"]    = date('Y-m-d', strtotime($val));
			}else{
				$returnAry["msg"] = "[상품]판매시작일이 날짜형식이 올바르지 않습니다.";
			}
		}else{
			$returnAry["msg"] = "[상품]판매시작일이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]대상세금종류" => array("type" => "product", "field" => "product_tax_type", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";
		$val = trim($val);
		if ($val != "") {
			if($val != "과세" && $val != "면세" && $val != "영세"){
				$returnAry["msg"] = "대상세금종류는 '과세' 또는 '면세' 또는 '영세' 만 가능합니다.";
			}else {
				$returnAry["result"] = true;
				$returnAry["val"]    = $val;
			}
		}else{
			$returnAry["msg"] = "[상품]대상세금종류가 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[상품]상품설명" => array("type" => "product", "field" => "product_desc", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		if ($val != "") {
			$returnAry["result"] = true;
			$returnAry["val"]    = $val;
		}else{
			$returnAry["msg"] = "[상품]상품설명이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]옵션명" => array("type" => "product_option", "field" => "supplier_idx", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		if ($val != "") {
			$returnAry["result"] = true;
			$returnAry["val"]    = $val;
		}else{
			$returnAry["msg"] = "[옵션]옵션명이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]판매기준가" => array("type" => "product_option", "field" => "product_option_sale_price", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		$val = str_replace(',', '', $val);
		if ($val != "") {
			if(!is_numeric($val)){
				$returnAry["result"] = "[옵션]판매기준가는 숫자만 허용됩니다.";
			}else{
				if(intval($val) == 0){
					$returnAry["result"] = "[옵션]판매기준가는 0이 될 수 없습니다.";
				}else{
					$returnAry["result"] = true;
					$returnAry["val"]    = $val;
				}
			}
		}else{
			$returnAry["msg"] = "[옵션]옵션명이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]판매가 (A등급)" => array("type" => "product_option", "field" => "product_option_sale_price_A", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		$val = str_replace(',', '', $val);
		if ($val != "") {
			if(!is_numeric($val)){
				$returnAry["result"] = "[옵션]판매가 (A등급)는 숫자만 허용됩니다.";
			}else{
				if(intval($val) == 0){
					$returnAry["result"] = "[옵션]판매가 (A등급)는 0이 될 수 없습니다.";
				}else{
					$returnAry["result"] = true;
					$returnAry["val"]    = $val;
				}
			}
		}else{
			$returnAry["msg"] = "[옵션]판매가 (A등급)이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]판매가 (B등급)" => array("type" => "product_option", "field" => "product_option_sale_price_B", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		$val = str_replace(',', '', $val);
		if ($val != "") {
			if(!is_numeric($val)){
				$returnAry["result"] = "[옵션]판매가 (B등급)는 숫자만 허용됩니다.";
			}else{
				if(intval($val) == 0){
					$returnAry["result"] = "[옵션]판매가 (B등급)는 0이 될 수 없습니다.";
				}else{
					$returnAry["result"] = true;
					$returnAry["val"]    = $val;
				}
			}
		}else{
			$returnAry["msg"] = "[옵션]판매가 (B등급)이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]판매가 (C등급)" => array("type" => "product_option", "field" => "product_option_sale_price_C", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		$val = str_replace(',', '', $val);
		if ($val != "") {
			if(!is_numeric($val)){
				$returnAry["result"] = "[옵션]판매가 (C등급)는 숫자만 허용됩니다.";
			}else{
				if(intval($val) == 0){
					$returnAry["result"] = "[옵션]판매가 (C등급)는 0이 될 수 없습니다.";
				}else{
					$returnAry["result"] = true;
					$returnAry["val"]    = $val;
				}
			}
		}else{
			$returnAry["msg"] = "[옵션]판매가 (C등급)이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]판매가 (D등급)" => array("type" => "product_option", "field" => "product_option_sale_price_D", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		$val = str_replace(',', '', $val);
		if ($val != "") {
			if(!is_numeric($val)){
				$returnAry["result"] = "[옵션]판매가 (D등급)는 숫자만 허용됩니다.";
			}else{
				if(intval($val) == 0){
					$returnAry["result"] = "[옵션]판매가 (D등급)는 0이 될 수 없습니다.";
				}else{
					$returnAry["result"] = true;
					$returnAry["val"]    = $val;
				}
			}
		}else{
			$returnAry["msg"] = "[옵션]판매가 (D등급)이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]판매가 (E등급)" => array("type" => "product_option", "field" => "product_option_sale_price_E", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		$val = str_replace(',', '', $val);
		if ($val != "") {
			if(!is_numeric($val)){
				$returnAry["result"] = "[옵션]판매가 (E등급)는 숫자만 허용됩니다.";
			}else{
				if(intval($val) == 0){
					$returnAry["result"] = "[옵션]판매가 (E등급)는 0이 될 수 없습니다.";
				}else{
					$returnAry["result"] = true;
					$returnAry["val"]    = $val;
				}
			}
		}else{
			$returnAry["msg"] = "[옵션]판매가 (E등급)이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]재고경고수량" => array("type" => "product_option", "field" => "product_option_warning_count", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		$val = str_replace(',', '', $val);
		if ($val != "") {
			if(!is_numeric($val)){
				$returnAry["result"] = "[옵션]재고경고수량은 숫자만 허용됩니다.";
			}else{
				$returnAry["result"] = true;
				$returnAry["val"]    = $val;
			}
		}else{
			$returnAry["msg"] = "[옵션]재고경고수량이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]재고위협수량" => array("type" => "product_option", "field" => "product_option_danger_count", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		$val = str_replace(',', '', $val);
		if ($val != "") {
			if(!is_numeric($val)){
				$returnAry["result"] = "[옵션]재고위협수량은 숫자만 허용됩니다.";
			}else{
				$returnAry["result"] = true;
				$returnAry["val"]    = $val;
			}
		}else{
			$returnAry["msg"] = "[옵션]재고위협수량이 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
	"[옵션]매입가" => array("type" => "product_option", "field" => "product_option_purchase_price", "validate" => function ($val) {
		$returnAry           = array();
		$returnAry["result"] = false;
		$returnAry["msg"]    = "";
		$returnAry["val"]    = "";

		$val = trim($val);
		$val = str_replace(',', '', $val);
		if ($val != "") {
			if(!is_numeric($val)){
				$returnAry["result"] = "[옵션]매입가는 숫자만 허용됩니다.";
			}else{
				$returnAry["result"] = true;
				$returnAry["val"]    = $val;
			}
		}else{
			$returnAry["msg"] = "[옵션]매입가가 입력되지 않았습니다.";
		}
		return $returnAry;
	}),
);
?>