<?php
/**
 * Created by IntelliJ IDEA.
 * User: ssawo
 * Date: 2019-03-25
 * Time: 오후 10:02
 */
//Init
include "../_init_.php";



$is_post = false;
$url = "https://openapi.lotte.com/openapi/createCertification.lotte?strUserId=292787&strPassWd=ejrdbs5500!";
$headers   = array();
$headers[] = "Content-Type: application/x-www-form-urlencoded";
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_POST, $is_post);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_POSTFIELDS, $json_ret_param);
$response = curl_exec ($curl);
$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$_SUBSCRIPTIONID = "";

if ($status_code == 200) {
	$arrRet["status_code"] = 0;
	$result_xml = simplexml_load_string($response);
	foreach ($result_xml->children() as $key => $val) {
		if($key == "Result") {
			foreach ($val->children() as $r_key => $r_val) {
				if($r_key == "SubscriptionId") {
					$_SUBSCRIPTIONID = (string)$r_val;
				}
				//echo $r_key."->".$r_val."<br />";
			}
		}

//		if ($order->getName() == "ns2:result_code") {
//			$arrRet["status_code"] = (int)$order;
//		} else if ($order->getName() == "ns2:result_text") {
//			$arrRet["result_text"] = $order;
//		} else {
//			$arrRquert = array('api_key' => $api_key, 'addPrdNo' => 'null',);
//			foreach ($order->children() as $val) {
//				//echo $val->getName()."->".$val;
//				switch ($val->getName()) {
//					case "ordNo": case "ordPrdSeq": case "addPrdYn": case "dlvNo":
//					$arrRquert[$val->getName()] = (string)$val;
//				}
//			}
//			$this->setOrderConfirm($arrRquert);   // 주문확인
//		}
	}
}

echo "_SUBSCRIPTIONID ->".$_SUBSCRIPTIONID."<br />";

$C_API_LotteCom = new API_LotteCom();

print_r2( $C_API_LotteCom->getSubscriptionId(array('UserId' => '292787', 'PassWd' => 'ejrdbs5500!')));

?>