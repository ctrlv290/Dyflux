<?php
/**
 * Created by IntelliJ IDEA.
 * User: ssawo
 * Date: 2019-03-22
 * Time: 오후 10:29
 */
//Init
include "../_init_.php";

// ordNo|bfOrderId|bfOrderSeq|orordNo|shppDivDtlCd|shppDivDtlNm|ordRcpDts|ordItemSeq|siteNo|shppNo|ordShpplocId|shppTypeDtlCd|shppStatCd|shppStatNm|shppProgStatDtlCd|shppSeq|shppRsvtDt|itemNm|itemId|uitemId|uitemNm|mdlNm|frebieNm|rsvtItemYn|frgShppYn|dircItemQty|cnclItemQty|ordQty|splprc|sellprc|ordCmplDts|ordpeNm|ordpeHpno|rcptpeNm|shpplocAddr|shpplocZipcd|shpplocOldZipcd|rcptpeHpno|rcptpeTelno|shppcst|ordCstId|ordCstOccCd|cstPlcyId|shppcstCodYn|ordCmplDts|ordMemoCntt|memoCntt|shortgProgStatCd|ordpeRoadAddr|bookCpnNo|bookCpnDcAmt|pCus|allnOrdNo|reOrderYn|itemDiv|shpplocBascAddr|shpplocDtlAddr|ordItemDivNm|autoShortgYn|whoutCritnDt
// 주문번호|이전주문번호|이전시스템주문순번|원주문번호|배송구분상세코드|배송구분상세명|주문접수일시|주문순번|사이트번호|배송번호|주문배송지ID|배송유형상세코드|배송상태코드|배송상태명|상세배송상태|배송순번|출고예정일자|상품명|상품번호|단품ID|단품명|모델명|사은품|예약판매여부|국내/외구분|지시수량|취소수량|주문수량|공급가|판매가|주문완료일시|주문자|주문자휴대폰번호|수취인|수취인상세주소|수취인우편번호|수취인구우편번호(6자리)|수취인휴대폰번호|수취인집전화번호|배송비|주문비용아이디|주문비용발생코드|배송비정책아이디|배송비착불여부|주문완료일|고객배송메모|배송업무메모|판매불가신청상태|도로명주소|인터파크도서:도서쿠폰번호|인터파크도서:도서쿠폰할인금액|개인통관고유번호|제휴주문번호|재지시여부구분|판매불가신청상태|수취인주소|수취인상세주소|주문상품구분|자동결품여부|출고기준일

$is_post = true;
$url = "https://eapi.ssgadm.com/api/pd/1/listDeliveryEnd.ssg";
$api_key = "483c5921-d35b-4bb7-b84a-85d7f8d70c96";
$headers   = array();
$headers[] = "Authorization: ".$api_key;
$headers[] = "accept: application/json";
$headers[] = "Content-Type: application/json";
/*
 INPUT sample (JSON) :
{
    requestShppDirection: {
        perdType: "01"
      , perdStrDts: 20170614
      , perdEndDts: 20170614
    }
}
 */
$ret_param = array('requestDeliveryEnd' => array());
$ret_param["requestDeliveryEnd"]["perdType"] = "01";
$ret_param["requestDeliveryEnd"]["perdStrDts"] = "20190301";
$ret_param["requestDeliveryEnd"]["perdEndDts"] = "20190323";
$ret_param["requestDeliveryEnd"]["commType"] = "";
$ret_param["requestDeliveryEnd"]["commValue"] = "";
$json_ret_param = json_encode($ret_param, true);


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

//echo "status_code->".$status_code."<br />";
//echo $response;
print_r2($response);


//
//$url = "https://eapi.ssgadm.com/common/0.1/getCommCdDtlc.ssg?commCd=delicoVenId";
////$ret_param = array('commCd' => "delicoVenId");
//////$ret_param["commCd"]["perdType"] = "delicoVenId";
////$json_ret_param = json_encode($ret_param, true);
//
//
//$curl = curl_init();
//curl_setopt($curl, CURLOPT_URL, $url);
//curl_setopt($curl, CURLOPT_POST, false);
//curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
////curl_setopt($curl, CURLOPT_POSTFIELDS, $json_ret_param);
//$response = curl_exec ($curl);
//$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
//curl_close($curl);
//
////echo $status_code;
//print_r2($response);
//
//




?>