<?php
/**
 * Created by IntelliJ IDEA.
 * User: ssawo
 * Date: 2019-01-17
 * Time: 오후 4:56
 */
//Init
include "../_init_.php";

$sc_entrId = "DUCKYUN";
$sc_supplyEntrNo = "3002821861";
$sc_supplyCtrtSeq = "1";
$sc_strDate = "20190117000000";
$sc_endDate = "20190117235959";
$sc_strDate = "20170410000000";
$sc_endDate = "20170410235959";


$C_API_Interpark = new API_Interpark();
$ret           = $C_API_Interpark->setOrderDeliveryConfirm(array(
	'sc_entrId' => $sc_supplyEntrNo,
	'sc_supplyEntrNo' => $sc_supplyEntrNo,
	'sc_supplyCtrtSeq' => $sc_supplyCtrtSeq,
	'sc_ordclmNo' => "20190716152716507915",   // 인터파크 주문번호
	'sc_ordSeq' => "1",   // 인터파크 주문순번
	'sc_delvDt' => date("Ymd"),   // YYYYMMDD 출고완료일자
	'sc_delvEntrNo' => "169168",   // 택배사코드
	'sc_invoNo' => "346685098906",   // 운송장번호
	'sc_optPrdTp' => "01",   // 옵션상품유형
	'sc_optOrdSeqList' => "1",   // 주문순번리스트
));
if($ret["result"]) {
	echo "true<br />";
	print_r2($ret);
} else {
	echo "false<br />";
	print_r2($ret);
}

return;
$sc_entrId = "DUCKYUN";
$sc_supplyEntrNo = "3002806651";
$sc_supplyCtrtSeq = "1";
$sc_strDate = "20190117000000";
$sc_endDate = "20190117235959";
$sc_strDate = "20170410000000";
$sc_endDate = "20170410235959";


$sc_entrId = "DUCKYUN";
$sc_supplyEntrNo = "3002821861";
$sc_supplyCtrtSeq = "1";
$sc_strDate = "20181128000000";
$sc_endDate = "20181128235959";

$query  = "";
$query .= "&sc.entrId=".$sc_entrId;
$query .= "&sc.supplyEntrNo=".$sc_supplyEntrNo;
$query .= "&sc.supplyCtrtSeq=".$sc_supplyCtrtSeq;
$query .= "&sc.strDate=".$sc_strDate;
$query .= "&sc.endDate=".$sc_endDate;

$url = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=orderListForSingle'.$query;    // 신규주문 리스트 및 주문확인 작업
$url = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=orderListDelvForSingle'.$query;  // 주문확인 리스트
$url = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=delvCompListForSingle'.$query;  // 배송정보 리스트

function test($val, $convert_type = 1)
{
	$dt = strtotime($val);
	$convert = "Y-m-d H:i:s";
	switch ($convert_type)
	{
		case 1 :
			$convert = "Y-m-d H:i:s";
			break;
	}

	return date($convert, $dt);

}
$m_order_no = "20190119080713296352";
$m_order_no = substr($m_order_no, 0, 14);
echo strlen($m_order_no)."--->".$m_order_no."<br />";
echo test("20190119080713")."<br />";

$dt = strtotime("2019-01-22");
$convert = "Y-m-d H:i:s";
echo date($convert, $dt)."<br/>";

$time = strtotime('2019-01-22');

$newformat = date('Y-m-d H:i:s',$time);

echo $newformat."<br/>";

echo $url."<br/>";
//주문번호|주문순번|상품번호|상품명|상품옵션|수량|실제주문금액(*)|판매단가|특판단가|배송비납부방식(선불,착불)|배송비|주문자|주문자 전화번호|주문자 핸드폰번호|수령인|수령인전화번호|수령인핸드폰번호|수령인우편번호|수령인주소|수령인우편번호(도로명)|수령인주소(도로명)|배송메시지
//ORD_NO|PRODUCT/PRD/ORD_SEQ|PRODUCT/PRD/PRD_NO|PRODUCT/PRD/PRD_NM|PRODUCT/PRD/OPT_NM|PRODUCT/PRD/ORD_QTY|PRODUCT/PRD/ORD_AMT|PRODUCT/PRD/SALE_UNITCOST|/PRODUCT/PRD/REAL_SALE_UNITCOST|RODUCT/PRD/IS_COLLECTED|DELIVERY/DELV/DEL_AMT|ORD_NM|TEL|MOBILE_TEL|RCVR_NM|DELI_TEL|DELI_MOBILE|DEL_ZIP|DELI_ADDR1 + DELI_ADDR2|DEL_ZIP_DORO|DELI_ADDR1_DORO + DELI_ADDR2_DORO|DELI_COMMENT
//echo $url;
$tmp_xls_header = "주문번호|주문순번|상품번호|상품명|상품옵션|수량|실제주문금액(*)|판매단가|특판단가|배송비납부방식(선불,착불)|배송비|주문자|주문자 전화번호|주문자 핸드폰번호|수령인|수령인전화번호|수령인핸드폰번호|수령인우편번호|수령인주소|수령인우편번호(도로명)|수령인주소(도로명)|배송메시지";
$tmp_api_header = "ORD_NO|PRODUCT/PRD/ORD_SEQ|PRODUCT/PRD/PRD_NO|PRODUCT/PRD/PRD_NM|PRODUCT/PRD/OPT_NM|PRODUCT/PRD/ORD_QTY|PRODUCT/PRD/ORD_AMT|PRODUCT/PRD/SALE_UNITCOST|/PRODUCT/PRD/REAL_SALE_UNITCOST|RODUCT/PRD/IS_COLLECTED|DELIVERY/DELV/DEL_AMT|ORD_NM|TEL|MOBILE_TEL|RCVR_NM|DELI_TEL|DELI_MOBILE|DEL_ZIP|DELI_ADDR1|DEL_ZIP_DORO|DELI_ADDR1_DORO|DELI_COMMENT";

$begin = new DateTime('2010-05-01');
$end = new DateTime('2010-05-10');
//echo date(strtotime($end. ' + 1 days'));
$interval = DateInterval::createFromDateString('1 day');
$period = new DatePeriod($begin, $interval, $end->modify('+1 day'));

foreach ($period as $dt) {
	//echo $dt->format("l Y-m-d H:i:s\n")."<br />";
}

$is_post = false;
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, $is_post);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml;charset=euc-kr"));
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
$response = curl_exec ($curl);
$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

//echo "status_code:".$status_code."<br />";
//echo $response;
if($status_code == 200) {
	//echo $response;
	$result_xml = simplexml_load_string($response);
	echo count($result_xml->ORDER)."<Br />";
	foreach ($result_xml->ORDER as $k_order => $v_order) {
		echo $k_ret."->".$v_ret."<Br />";
		foreach ($v_order as $k_order_item => $v_order_item) {
			echo $k_order_item."->".$v_order_item."<Br />";
		}

	}

	return;

	$result_xml = (array)$result_xml;
	//echo $result_xml;
	//print_r($result_xml);
	return;
	$location = "";
	foreach ($result_xml as $k_ret => $v_ret) {
		//echo $k_ret."->".$v_ret."<Br />";
		if ($k_ret == "ORDER") {
			$order_list = (array)$v_ret;
			print_r($order_list);
			foreach ($order_list as $k_order_item => $v_order_item) {
				echo $k_order_item."->".$v_order_item."<Br />";
				//print_r($v_order);
			}

			foreach ((array)((array)$order_list["PRODUCT"])["PRD"] as $k_order_product => $v_order_product) {
				echo $k_ret."/PRODUCT:::/".$k_order_product."->".$v_order_product."<Br />";
				foreach ((array)$v_order_product as $k_order_product_item => $v_order_product_item) {
					$location = "PRODUCT/PRD/";
					echo "&nbsp;&nbsp;&nbsp;&nbsp;".$location.$k_order_product_item."->".$v_order_product_item."<Br />";
				}
			}
			//echo "--->".$result_xml['ORDER']."<br />";
			//print_r($v_ret["ORDER"]);

		}
	}
//	foreach ($result_xml->children() as $result) {
//		echo $result->getName()."->".$result."<Br />";
//		if ($result->getName() == "ORDER") {
//			foreach ($result->children() as $order) {
//				echo $result->getName()."/".$order->getName()."->".$order."<Br />";
//			}
//			echo "---->".$result_xml["ORDER"];
//			foreach ($result["PRODUCT"]->children() as $order_product) {
//				echo "=-====>".$result->getName()."/".$order_product->getName()."->".$order_product."<Br />";
//			}
//		}
//	}



}

?>

