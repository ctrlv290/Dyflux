<?php
/**
 * User: ssawona
 * Date: 2018-12-11
 * Desc: 자동발주 관련 Process
 */

//Init
include "../_init_.php";

$seller_idx  = $_POST["seller_idx"];
$market_code = $_POST["market_code"];
$seller_type = $_POST["seller_type"];
/*
$finfo_esmplus = array(
dy_filed_nm => "market_order_no", "mk_filed_nm" => "OrderNo"
);*/

$finfo_esmplus = array(
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "order_pack_code", "mk_filed_nm" => "TransNo", "xls_filed_nm" => "배송번호"),  // 묶음배송 코드
	array("mk_pk" => false, "charset" => "datetime", "isnull" => "false", "dy_filed_nm" => "order_pay_date", "mk_filed_nm" => "PayDate", "xls_filed_nm" => "주문일자(결제확인전)"),  // 결재완료 일시 (주문일시)
	array("mk_pk" => false, "charset" => "datetime", "isnull" => "true", "dy_filed_nm" => "order_confirm_date", "mk_filed_nm" => "", "xls_filed_nm" => ""),  // 발주일시
	array("mk_pk" => false, "charset" => "datetime", "isnull" => "true", "dy_filed_nm" => "invoice_date", "mk_filed_nm" => "", "xls_filed_nm" => ""),  // 송장 입력일
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "market_order_no", "mk_filed_nm" => "SiteOrderNo", "xls_filed_nm" => "주문번호"),  // 쇼핑몰 주문번호
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "market_order_subno", "mk_filed_nm" => "-", "xls_filed_nm" => ""),  // 쇼핑몰 상세 주문번호
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "market_product_no", "mk_filed_nm" => "GoodsNo", "xls_filed_nm" => "상품번호"),  // 판매처 상품코드
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "market_product_name", "mk_filed_nm" => "GoodsName", "xls_filed_nm" => "상품명"),  // 판매처 상품명
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "market_product_option", "mk_filed_nm" => "SelOption", "xls_filed_nm" => "주문옵션"),  // 판매처 상품 옵션
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "market_order_id", "mk_filed_nm" => "BuyerID", "xls_filed_nm" => "구매자ID"),  // 판매처 구매자 ID
	array("mk_pk" => false, "charset" => "int", "isnull" => "false", "dy_filed_nm" => "order_amt", "mk_filed_nm" => "OrderAmnt", "xls_filed_nm" => "판매금액"),  // 판매금액
	array("mk_pk" => false, "charset" => "int", "isnull" => "true", "dy_filed_nm" => "order_pay_amt", "mk_filed_nm" => "-", "xls_filed_nm" => ""),  // 결제금액
	array("mk_pk" => false, "charset" => "int", "isnull" => "true", "dy_filed_nm" => "order_calculation_amt", "mk_filed_nm" => "SttlExpectedAmnt", "xls_filed_nm" => "정산예정금액"),  // 정산예정금액
	array("mk_pk" => false, "charset" => "int", "isnull" => "false", "dy_filed_nm" => "order_cnt", "mk_filed_nm" => "OrderQty", "xls_filed_nm" => "수량"),  // 주문수량
	array("mk_pk" => false, "charset" => "int", "isnull" => "true", "dy_filed_nm" => "delivery_fee", "mk_filed_nm" => "DeliveryFee", "xls_filed_nm" => "배송비 금액"),  // 배송비
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "order_pay_type", "mk_filed_nm" => "-", "xls_filed_nm" => ""),  // 결제수단 (카드/현금)
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "order_name", "mk_filed_nm" => "BuyerName", "xls_filed_nm" => "구매자명"),  // 구매자 이름
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "order_tp_num", "mk_filed_nm" => "BuyerHt", "xls_filed_nm" => "구매자 전화번호"),  // 구매자 전화번호
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "order_hp_num", "mk_filed_nm" => "BuyerCp", "xls_filed_nm" => "구매자 휴대폰"),  // 구매자 휴대폰번호
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "order_addr1", "mk_filed_nm" => "-", "xls_filed_nm" => ""),  // 구매자 주소
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "order_addr2", "mk_filed_nm" => "-", "xls_filed_nm" => ""),  // 구매자 상세 주소
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "order_zipcode", "mk_filed_nm" => "-", "xls_filed_nm" => ""),  // 구매자 우편번호
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "receive_name", "mk_filed_nm" => "RcverName", "xls_filed_nm" => "수령인명"),  // 수령자 이름
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "receive_tp_num", "mk_filed_nm" => "RcverInfoHt", "xls_filed_nm" => "수령인 전화번호"),  // 수령자 전화번호
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "receive_hp_num", "mk_filed_nm" => "RcverInfoCp", "xls_filed_nm" => "수령인 휴대폰"),  // 수령자 휴대폰번호
	array("mk_pk" => false, "charset" => "string", "isnull" => "false", "dy_filed_nm" => "receive_addr1", "mk_filed_nm" => "RcverInfoAd", "xls_filed_nm" => "주소"),  // 수령자 주소
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "receive_addr2", "mk_filed_nm" => "-", "xls_filed_nm" => ""),  // 수령자 상세주소
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "receive_zipcode", "mk_filed_nm" => "ZipCode", "xls_filed_nm" => "우편번호"),  // 수령자 우편번호
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "receive_memo", "mk_filed_nm" => "DeliveryMemo", "xls_filed_nm" => "배송시 요구사항"),  // 배송 메모
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "delivery_code", "mk_filed_nm" => "", "xls_filed_nm" => ""),  // 택배사 코드
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "invoice_no", "mk_filed_nm" => "", "xls_filed_nm" => ""),  // 송장번호
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "delivery_type", "mk_filed_nm" => "DeliveryFeeType", "xls_filed_nm" => "배송비"),  // 배송비 정산구분 (선불/착불)
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "order_is_auto", "mk_filed_nm" => "", "xls_filed_nm" => ""),  // 자동 수집 여부  (Y/N)
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "order_org_data1", "mk_filed_nm" => "", "xls_filed_nm" => ""),  // 자동 수집 원본 데이터1
	array("mk_pk" => false, "charset" => "string", "isnull" => "true", "dy_filed_nm" => "order_org_data2", "mk_filed_nm" => "", "xls_filed_nm" => ""),  // 자동 수집 원본 데이터2

);

//print_r2($finfo_esmplus);11 12321
/*
print_r2(array_column($finfo_esmplus, 'dy_filed_nm'));

echo $key = array_search("delivery_type", array_column($finfo_esmplus, 'dy_filed_nm'));
echo "<br>";
echo $finfo_esmplus[31]["dy_filed_nm"];
echo "<br>";

print_r2(array_filter($finfo_esmplus, function($value) { return $value["dy_filed_nm"] == "delivery_type"; }));

foreach ($finfo_esmplus as $key => $val)
{
	echo $val["dy_filed_nm"]."<br />";

}*/

$json = file_get_contents("20181212175911_ESMPlus.json");
$json = iconv("UTF-8", "UTF-8", $json);
$result_json = json_decode($json, true);
//print_r2($result_json);
$insert_fileds = "";
$insert_values = "";
foreach ($result_json["data"] as $root => $data)
{
	foreach ($data as $key => $val)
	{
		$f_idx = array_search($key, array_column($finfo_esmplus, 'mk_filed_nm'));
		//print_r2(array_filter($finfo_esmplus, function($value) { return $value["mk_filed_nm"] == "TransNo"; }));
		if($f_idx !== false) {
			$insert_fileds .= ", ".$finfo_esmplus[$f_idx]["dy_filed_nm"];
			$insert_values .= ", N'".$val."'";
			if($finfo_esmplus[$f_idx]["isnull"] == "false" && $val == "")
			{
				echo "Error:필수값없음";
			}
			if($finfo_esmplus[$f_idx]["isnull"] == "false" && $val == "")
			{
				echo "Error:필수값없음";
			}
			//print_r2($finfo_esmplus[$f_idx]);
			echo $finfo_esmplus[$f_idx]["dy_filed_nm"] . ' : ' . $val . " (" . $f_idx . "." . $key . ")". "<br />";
			
		}
	}
	//echo json_encode($data);
	echo $insert_fileds."<br />";
	echo $insert_values."<br />";
	echo "<br />";
}


?>

