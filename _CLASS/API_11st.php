<?php
/**
 * Class API_11st
 * User : ssawoona
 * Date : 2019
 */
class API_11st extends Dbconn
{
	private $tmp_xls_header = "주문번호|주문순번|결제일시|배송번호|상품명|옵션|수량|주문금액|수취인|배송비결제방식|배송비|도서산간 배송비|휴대폰번호|전화번호|우편번호|주소|배송메시지|구매자|구매자ID|상품번호|판매단가|추가구성여부|추가구성원상품번호|희망배송일자|폐가전수거여부|희망일배송모델코드|묶음배송일련번호|묶음배송유무|고객등급|전세계배송여부|판매자할인금액-각상품별|11번가할인금액-각상품별|주문자기본주소|주문일시|구매자상세주소|구매자우편번호|주문상품옵션결제금액|결제금액|구매자휴대폰번호|주문자전화번호|발주확인일시|주문상품옵션코드|배송지우편번호순번|원클릭체크아웃주문코드|판매자할인금액|판매자상품번호|판매자재고번호|11번가할인금액|주소유형|건물관리번호";
	private $tmp_api_header = "ordNo|ordPrdSeq|ordStlEndDt|dlvNo|prdNm|slctPrdOptNm|ordQty|ordAmt|rcvrNm|dlvCstType|dlvCst|bmDlvCst|rcvrPrtblNo|rcvrTlphn|rcvrMailNo|rcvrBaseAddr|ordDlvReqCont|ordNm|memID|prdNo|selPrc|addPrdYn|addPrdNo|appmtDdDlvDy|appmtEltRefuseYn|appmtselStockCd|bndlDlvSeq|bndlDlvYN|custGrdNm|gblDlvYn|lstSellerDscPrc|lstTmallDscPrc|ordBaseAddr|ordDt|ordDtlsAddr|ordMailNo|ordOptWonStl|ordPayAmt|ordPrtblTel|ordTlphnNo|plcodrCnfDt|prdStckNo|rcvrMailNoSeq|referSeq|sellerDscPrc|sellerPrdCd|sellerStockCd|tmallDscPrc|typeAdd|typeBilNo";
	public $XLS_HEADERS;
	public $API_HEADERS;
	function __construct()
	{
		Dbconn::__construct();
		$this->XLS_HEADERS = explode("|", $this->tmp_xls_header);
		$this->API_HEADERS = explode("|", $this->tmp_api_header);
	}

	public function repCode2String($key, $data) {
		$ret = "";
		//echo $key, $data;
		$arr_dlvCstType = array(
			'01' => '선불',
			'02' => '착불',
			'03' => '무료',
		);
		if($key == "dlvCstType") {
			$ret = $arr_dlvCstType[$data];
		}
		//echo $ret;
		return $ret;
	}

	/**
	 * 11번가 전용 API 읽기
	 * @param $args
	 * @return array
	 */
	public  function getCurl($args) {
		$arrRet = array(
			'status_code' => 0,
			'result_text' => '',
		);
		extract($args);
		$is_post   = false;
		$headers   = array();
		$headers[] = "openapikey: ".$api_key;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_POST, $is_post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$arrRet['result_text'] = $response = curl_exec($ch);
		$arrRet['status_code'] = $status_code =  (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		$tmp = $arrRet['result_text'];

		$response = iconv("euc-kr", "utf-8", $response);
		$response = str_replace('\"', '"', $response);
		$response = str_replace('euc-kr', 'utf-8', $response);
		$response = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response);

		parent::addDebugLog($api_url, array(
			"api_url" => $api_url,
			"result_text" => json_decode( json_encode( simplexml_load_string($response) ), 1 ),
			"status_code" => $status_code,
		));

		return $arrRet;
	}

	/**
	 * 주문 확인 처리 (발송대기로)
	 * @param $args
	 * @return bool
	 */
	public function setOrderConfirm($args)
	{
		$ret = false;
		$addPrdYn = "N";
		extract($args);

		//https://api.11st.co.kr/rest/ordservices/reqpackaging/[ordNo]/[ordPrdSeq]/[addPrdYn]/[addPrdNo]/[dlvNo]
		$api_url = "https://api.11st.co.kr/rest/ordservices/reqpackaging/".$ordNo."/".$ordPrdSeq."/".$addPrdYn."/".$addPrdNo."/".$dlvNo;
		//$api_url = "https://api.11st.co.kr/rest/ordservices/reqpackaging/".$ordNo."1/".$ordPrdSeq."/".$addPrdYn."/".$addPrdNo."/".$dlvNo;
		//$api_url = "https://api.11st.co.kr/rest/ordservices/completed/" . $s_date . "/" . $e_date;
		//return $ret;
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];

		if ($status_code == 200) {
			$response   = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response); //xmlns:ns2=\"http://skt.tmall.business.openapi.spring.service.client.domain/ URL ????
			$result_xml = simplexml_load_string($response);
			foreach ($result_xml->children() as $order) {
				if ($order->getName() == "result_code") {
					if ($order == 0) {
						$ret = true;
					}
				} else if ($order->getName() == "result_text") {
					//echo $order."<br />";
				}
			}
		}

		return $ret;
	}

	/**
	 * 주문 취소 승인 처리
	 * @param $args
	 * @return bool
	 */
	public function setOrderCancelConfirm($args)
	{
		$ret = false;
		$api_key     = "";  // API_KEY
		$ordPrdCnSeq = "";  // 클레임번호
		$ordNo       = "";  // 주문번호
		$ordPrdSeq   = "";  // 주문순번
		extract($args);

		//http://api.11st.co.kr/rest/claimservice/cancelreqconf/[ordPrdCnSeq]/[ordNo]/[ordPrdSeq]
		$api_url     = "https://api.11st.co.kr/rest/claimservice/cancelreqconf/".$ordPrdCnSeq."/".$ordNo."/".$ordPrdSeq;
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];

		if ($status_code == 200) {
			$response   = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response); //xmlns:ns2=\"http://skt.tmall.business.openapi.spring.service.client.domain/ URL ????
			$result_xml = simplexml_load_string($response);
			foreach ($result_xml->children() as $order) {
				if ($order->getName() == "result_code") {
					if ($order == 0) {
						$ret = true;
					}
				} else if ($order->getName() == "result_text") {
					//echo $order."<br />";
				}
			}
		}

		return $ret;
	}


	/**
	 * 주문 취소 거부 처리
	 * @param $args
	 * @return bool
	 */
	public function setOrderCancelReject($args)
	{
		$ret = false;
		$api_key     = "";  // API_KEY
		$ordPrdCnSeq = "";  // 클레임번호
		$ordNo       = "";  // 주문번호
		$ordPrdSeq   = "";  // 주문순번
		$dlvMthdCd   = "01"; // 택배
		$sendDt      = "";   // 보낸날짜 (yyyyMMdd)
		$dlvEtprsCd  = "";   // 택배사
		$invcNo      = "";   // 송장번호
		extract($args);


		//http://api.11st.co.kr/rest/claimservice/cancelreqreject/[ordNo]/[ordPrdSeq]/[ordPrdCnSeq]/[dlvMthdCd]/[sendDt]/[dlvEtprsCd]/[invcNo]
		$api_url     = "https://api.11st.co.kr/rest/claimservice/cancelreqreject/".$ordNo."/".$ordPrdSeq."/".$ordPrdCnSeq."/".$dlvMthdCd."/".$sendDt."/".$dlvEtprsCd."/".$invcNo;
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];

		if ($status_code == 200) {
			$response   = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response); //xmlns:ns2=\"http://skt.tmall.business.openapi.spring.service.client.domain/ URL ????
			$result_xml = simplexml_load_string($response);
			foreach ($result_xml->children() as $order) {
				if ($order->getName() == "result_code") {
					if ($order == 0) {
						$ret = true;
					}
				} else if ($order->getName() == "result_text") {
					//echo $order."<br />";
				}
			}
		}

		return $ret;
	}

	/**
	 * 주문 발송 처리
	 * @param $args
	 * @return bool
	 */
	public function setOrderDeliveryConfirm($args)
	{
		$arrRet = array(
			'result' => false,
			'result_text' => '',
		);
		$api_key     = "";  // API_KEY
		$ordNo       = "";  // 주문번호
		$ordPrdSeq   = "";  // 주문순번
		$invcNo = "";   // 송장번호
		$dlvNo = "";    // 배송번호
		$dlvEtprsCd = "";   // 배송업체 코드
		extract($args);

		$sendDt = date("YmdHi") ;
		$dlvMthdCd = "01";  // 01 : 택배
		$partDlvYn = "N";    // 부분발송 Y/N
		$api_result_code = "";

		//https://api.11st.co.kr/rest/ordservices/reqdelivery/[sendDt]/[dlvMthdCd]/[dlvEtprsCd]/[invcNo]/[dlvNo]/[partDlvYn]/[ordNo]/[ordPrdSeq]
		$api_url     = "https://api.11st.co.kr/rest/ordservices/reqdelivery/".$sendDt."/".$dlvMthdCd."/".$dlvEtprsCd."/".$invcNo."/".$dlvNo."/".$partDlvYn."/".$ordNo."/".$ordPrdSeq;
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];

		if ($status_code == 200) {
			$response   = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response); //xmlns:ns2=\"http://skt.tmall.business.openapi.spring.service.client.domain/ URL ????
			$result_xml = simplexml_load_string($response);
			foreach ($result_xml->children() as $order) {
				if ($order->getName() == "result_code") {
					if ($order == 0) {
						$arrRet["result"] = true;
					}
					$api_result_code = $order;
				} else if ($order->getName() == "result_text") {
					//echo $order."<br />";
					if(!$arrRet["result"]) {
						$arrRet["result_text"] = $api_result_code . ":" . $order;
					}
				}
			}
		} else {
			$arrRet["result_text"] = "API Error";
		}

		return $arrRet;
	}


	/**
	 * 신규 주문 리스트가져 오기
	 * 신규주문 조회 -> 신규주문 주문 확인 -> 발송대기 리스트 조회
	 * @param $args
	 * @return array
	 */
	public function getOrderList($args)
	{
		$arrRet = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
		);
		$api_key = "";
		$s_date = "";
		$e_date = "";
		$API_header = $this->API_HEADERS;
		extract($args);

		// 신규주문 조회
		$api_url = "https://api.11st.co.kr/rest/ordservices/complete/" . $s_date . "/" . $e_date;
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		//echo $status_code;
		//$arrRet["status_code"] = $status_code;
		//echo $api_url;
		//print_r2($response);
		//$arrRet["status_code"] = $status_code;
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$response   = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response); //xmlns:ns2=\"http://skt.tmall.business.openapi.spring.service.client.domain/ URL ????
			$result_xml = simplexml_load_string($response);
			foreach ($result_xml->children() as $order) {
				//echo $order->getName()."->".$order;
				if ($order->getName() == "ns2:result_code") {
					$arrRet["status_code"] = (int)$order;
				} else if ($order->getName() == "ns2:result_text") {
					$arrRet["result_text"] = $order;
				} else {
					$arrRquert = array('api_key' => $api_key, 'addPrdNo' => 'null',);
					foreach ($order->children() as $val) {
						//echo $val->getName()."->".$val;
						switch ($val->getName()) {
							case "ordNo": case "ordPrdSeq": case "addPrdYn": case "dlvNo":
							$arrRquert[$val->getName()] = (string)$val;
						}
					}
					$this->setOrderConfirm($arrRquert);   // 주문확인
				}
			}
		}
		if($arrRet["status_code"] < 0) {
			$arrRet["result_text"] = "API Error (신규주문)";
			return $arrRet;
		} else {
			$arrRet["result_text"] = "";
		}


		$arrRet["status_code"] = -9999;
		// 주문확인 리스트 (발송대상)
		//https://api.11st.co.kr/rest/ordservices/packaging/[startTime]/[endTime]
		$api_url = "https://api.11st.co.kr/rest/ordservices/packaging/" . $s_date . "/" . $e_date;
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];

		//rcvrBaseAddr(주소) = rcvrBaseAddr + rcvrDtlsAddr
		$arrData   = array();
		$tmp_array = array();
		foreach ($API_header as $val) {
			$tmp_array[$val] = "";
		}
		$row_num = 0;
		if ($status_code == 200) {
			$arrRet["status_code"]  = 0;
			$response   = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response); //xmlns:ns2=\"http://skt.tmall.business.openapi.spring.service.client.domain/ URL ????
			//print_r2($response);
			$result_xml = simplexml_load_string($response);
			foreach ($result_xml->children() as $order) {
				$arr_row = $tmp_array;
				//$arr_row = array();
				//print_r2($order);
				$add_idx = -1;
				//echo $order->getName()."->".$order;
				if ($order->getName() == "ns2:result_code") {
					$arrRet["status_code"] = (int)$order;
				} else if ($order->getName() == "ns2:result_text") {
					$arrRet["result_text"] = $order;
				} else {
					for ($i = 0; $i < count($API_header); $i++) {
						$tmp = "";
						foreach ($order->children() as $val) {
							if ($val->getName() == $API_header[$i]) {
								$tmp = (string)$val;
								if($API_header[$i] == "dlvCstType") {
									$tmp = $this->repCode2String($API_header[$i], $tmp);
								}
							}
						}
						//echo $API_header[$i]."->".(string)$tmp."<br />";
						$arr_row[$API_header[$i]] = (string)$tmp. "";
					}
					foreach ($order->children() as $val) {
						if($val->getName() == "rcvrDtlsAddr" ) {
							$arr_row["rcvrBaseAddr"] = $arr_row["rcvrBaseAddr"]." ".(string)$val;
						}
					}
				}
				if ($arr_row["ordNo"] != "") {
					//print_r2($arr_row);
					$arrData[$row_num] = $arr_row;
					$row_num++;
				}
			}
		}
		//echo $arrRet["result_text"] ;
		if($arrRet["status_code"] < 0) {
			$arrRet["result_text"] = "API Error (발송대기)";
			return $arrRet;
		} else {
			$arrRet["result_text"] = "";
		}


		$arrRet["result_data"] = $arrData;
		return $arrRet;

	}

	/**
	 * 주문 취소 처리
	 * @param $args
	 * @return array
	 */
	public function execCancelProc($args)
	{
		$arrRet = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
			'request_cnt' => 0,    // 전체 취소 요청 수
			'confirm_cnt' => 0,    // 취소 숭인 수
			'reject_cnt' => 0,     // 취소 거부 수 (이미 송장 이상으로)
			'notorder_cnt' => 0,   // DY 에 주문 내역 없음
		);

		$api_key = "";
		$s_date = "";
		$e_date = "";
		$seller_idx = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg = "";
		extract($args);

		// 취소주문 조회
		$api_url = "http://api.11st.co.kr/rest/claimservice/cancelorders/" . $s_date . "/" . $e_date;
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		//$arrRet["status_code"] = $status_code;
		//echo $api_url;
		//echo $response;

		//$arrRet["status_code"] = $status_code;
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$response   = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response); //xmlns:ns2=\"http://skt.tmall.business.openapi.spring.service.client.domain/ URL ????
			$result_xml = simplexml_load_string($response);
			foreach ($result_xml->children() as $order) {
				//echo $order->getName()."->".$order;
				if ($order->getName() == "ns2:order") {
					$arr_row = array(
						'market_order_no' => '',
						'market_order_subno' => '',
					);
					//$arrRquert = array('api_key' => $api_key, 'addPrdNo' => 'null',);
					foreach ($order->children() as $val) {
						//echo $val->getName()."->".$val."<br />";
						$arr_row[$val->getName()] = (string)$val;

						if($val->getName() == "ordNo") {
							$arr_row['market_order_no'] = $val;
						} else if($val->getName() == "ordPrdSeq") {
							$arr_row['market_order_subno'] = $val;
						}

					}
					$arrRet["result_data"][] = $arr_row;

					//$this->setOrderConfirm($arrRquert);   // 주문확인
				}
			}
		}
		//print_r2($arrRet["result_data"]);

		$C_CS = new CS();
		$C_Order = new Order();

		foreach ($arrRet["result_data"] as $cancel_rows) {
			$arrRet["request_cnt"]++;

			$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $cancel_rows["market_order_no"], $cancel_rows["market_order_subno"]);
			if(count($_row) > 0) {
				if($_row["order_progress_step"] == "ORDER_INVOICE" || $_row["order_progress_step"] == "ORDER_SHIPPED") {
					// 주문 취소 할 수 없음
					$arrRet["reject_cnt"]++;
					$arrRquert = array(
						'api_key' => $api_key,  // API_KEY
						'ordPrdCnSeq' => $cancel_rows["ordPrdCnSeq"],   // 클레임번호
						'ordNo' => $cancel_rows["ordNo"],   // 주문번호
						'ordPrdSeq' => $cancel_rows["ordPrdSeq"],   // 주문순번
						'dlvMthdCd'   => "01", // 01:택배
						'sendDt'      => date("Ymd", strtotime( $_row["invoice_date"] ) ),   // 보낸날짜 (yyyyMMdd)
						'dlvEtprsCd'  => $_row["market_delivery_code"],   // 택배사
						'invcNo'      => $_row["invoice_no"],   // 송장번호
					);


					//region *** 마켓 실제 취소 거부 처리 ***
					// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.
					if(DY_MARKET_IS_LIVE) {
						$this->setOrderCancelReject($arrRquert);    // 취소 거부 처리
					}
					//endregion

				} else {
					// 주문 취소 가능 > 주문취소
					$arrRquert = array(
						'api_key' => $api_key,  // API_KEY
						'ordPrdCnSeq' => $cancel_rows["ordPrdCnSeq"],   // 클레임번호
						'ordNo' => $cancel_rows["ordNo"],   // 주문번호
						'ordPrdSeq' => $cancel_rows["ordPrdSeq"],   // 주문순번
					);

					//region *** 마켓 실제 취소 승인 처리 ***
					// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.
					if(DY_MARKET_IS_LIVE) {
						$this->setOrderCancelConfirm($arrRquert);   // 취소 승인 처리
					}
					//endregion

					$cancel_ret = $C_CS->updateOrderCancelOneByOrderIdx($_row["order_idx"], $cs_reason_code1, $cs_reason_code2, $cs_msg); // DY 에 취소상태 변경
					if($cancel_ret)  {
						$arrRet['confirm_cnt']++;
					} else {
						$arrRet["reject_cnt"]++;
					}
				}
			} else {
				$arrRet["notorder_cnt"]++;
			}
		}


		return $arrRet;

	}


	/**
	 * 송장 입력 처리
	 * @param $args
	 * @return array
	 */
	public function execDeliveryProc($args)
	{
		$arrRet = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
			'request_cnt' => 0,    // 전체 발송 대기 수
			'confirm_cnt' => 0,    // 송장입력 수
			'reject_cnt' => 0,     // 송장 거부 수 (에러 및 DY 배송상태 아님 등)
			'notorder_cnt' => 0,   // DY 에 주문 내역 없음
		);

		$api_key = "";
		$s_date = "";
		$e_date = "";
		$seller_idx = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg = "";
		extract($args);


		//https://api.11st.co.kr/rest/ordservices/packaging/[startTime]/[endTime]
		// 발송처리 대상 목록 조회
		$api_url = "https://api.11st.co.kr/rest/ordservices/packaging/" . $s_date . "/" . $e_date;
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		//$arrRet["status_code"] = $status_code;
		//echo $api_url;
		//echo $response;

		//$arrRet["status_code"] = $status_code;
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$response   = preg_replace("/ xmlns:ns2=(\"|\')?([^\"\']+)(\"|\')?/", "", $response); //xmlns:ns2=\"http://skt.tmall.business.openapi.spring.service.client.domain/ URL ????
			$result_xml = simplexml_load_string($response);
			foreach ($result_xml->children() as $order) {
				//echo $order->getName()."->".$order;
				if ($order->getName() == "ns2:order") {
					$arr_row = array(
						'market_order_no' => '',
						'market_order_subno' => '',
					);
					//$arrRquert = array('api_key' => $api_key, 'addPrdNo' => 'null',);
					foreach ($order->children() as $val) {
						//echo $val->getName()."->".$val."<br />";
						$arr_row[$val->getName()] = (string)$val;

						if($val->getName() == "ordNo") {
							$arr_row['market_order_no'] = $val;
						} else if($val->getName() == "ordPrdSeq") {
							$arr_row['market_order_subno'] = $val;
						}

					}
					$arrRet["result_data"][] = $arr_row;

				}
			}
		}
		//print_r2($arrRet["result_data"]);

		$C_Order = new Order();

		foreach ($arrRet["result_data"] as $cancel_rows) {
			$arrRet["request_cnt"]++;

			$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $cancel_rows["market_order_no"], $cancel_rows["market_order_subno"]);
			if(count($_row) > 0) {
				if($_row["order_progress_step"] == "ORDER_SHIPPED") {
					// 배송상태 되어 송장 입력 할 수 있음.

					$arrRquert = array(
						'api_key' => $api_key,  // API_KEY
						'ordNo' => $cancel_rows["ordNo"],   // 주문번호
						'ordPrdSeq' => $cancel_rows["ordPrdSeq"],   // 주문순번
						'invcNo'      => $_row["invoice_no"],   // 송장번호
						'dlvNo'      => $cancel_rows["dlvNo"],   // 송장번호						
						'dlvEtprsCd'  => $_row["market_delivery_code"],   // 택배사 코드
					);


					$C_Order->updateMarketInvoiceState($seller_idx, $cancel_rows["market_order_no"], $cancel_rows["market_order_subno"]
						, "U", "미처리");

					//region *** 마켓 실제 송장입력 처리 ***
					// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.
					if(DY_MARKET_IS_LIVE) {
						$_deliveryConfirm = $this->setOrderDeliveryConfirm($arrRquert);    // 송장입력 처리
						$C_Order->updateMarketInvoiceState($seller_idx, $cancel_rows["market_order_no"], $cancel_rows["market_order_subno"]
							, ($_deliveryConfirm["result"] ? "S" : "F"), $_deliveryConfirm["result_text"]);
					}
					//endregion

					$arrRet["confirm_cnt"]++;

				} else {
					$arrRet["reject_cnt"]++;
				}
			} else {
				$arrRet["notorder_cnt"]++;
			}
		}


		return $arrRet;

	}

}
?>