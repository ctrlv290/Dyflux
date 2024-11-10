<?php

$api_key = "c3f047c8692c485e532f472688290ba6";

$is_post   = false;
$headers   = array();
$headers[] = "openapikey: ".$api_key;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.11st.co.kr/rest/ordservices/packaging/201906190000/201906252359");
curl_setopt($ch, CURLOPT_POST, $is_post);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
$arrRet['result_text'] = $response = curl_exec($ch);
$arrRet['status_code'] = $status_code =  (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$tmp = $arrRet['result_text'];

print_r($response);
$response   = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response);
$response = iconv("euc-kr", "utf-8", $response);
$response = str_replace('\"', '"', $response);
$response = str_replace('euc-kr', 'utf-8', $response);

print_r( json_decode( json_encode( simplexml_load_string($response) ), 1 ))

?>