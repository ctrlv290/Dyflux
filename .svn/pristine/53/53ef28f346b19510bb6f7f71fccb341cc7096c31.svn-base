<?php
/**
 * User: ssawoona
 * Date: 2019-03
 */

class API_LotteCom extends DBConn
{
	private $tmp_xls_header = "주문번호|부주문번호|주문상품번호|진행상태|처리상태|주문자|주문자ID|주문자회원번호|주문자 주소 우편번호|주문자 주소|주문자 주소상세|주문자 전화번호|주문자 핸드폰번호|발주일자|과세구분|배송메모내용|카드메모(선물 메세지)|메시지카드용 보내는사람|받는사람 이름|받는사람 우편번호|받는사람 주소|받는사람 상세주소|받는사람 전화번호|받는사람 휴대폰번호|송장번호|택배사|(부)주문상품순번|닷컴상품코드|업체상품코드|상품명|수량|판매가|실매입가(매입단가)|상품옵션|브랜드명|모델명|교환여부|상품선택설명";
	private $tmp_api_header = "OrdNo|SubOrdNo|OrdProdCode|OrdStat|OrdProcStat|OrderName|OrderID|OrderMemNo|OrderPostCode|OrderAddr1|OrderAddr2|OrderTelNo|OrderHpNo|TrdDate|TaxType|DlvMemoCont|CardMemoCont|CardMemoSndrName|DelvInfo/recvName|DelvInfo/recvPostCode|DelvInfo/recvAddr1|DelvInfo/recvAddr2|DelvInfo/recvTel|DelvInfo/recvHp|DelvInfo/invoiceNo|DelvInfo/delvName|ProdInfo/ProdSeq|ProdInfo/ProdCode|ProdInfo/EntrProdNo|ProdInfo/ProdName|ProdInfo/ordQty|ProdInfo/ordPrice|ProdInfo/buyRealPrice|ProdInfo/prodOption|ProdInfo/brdName|ProdInfo/modelName|ProdInfo/Exchange|ProdInfo/GoodsChocDesc";
	public $XLS_HEADERS;
	public $API_HEADERS;
	private $API_HOST = "https://openapi.lotte.com";
	function __construct()
	{
		DBConn::__construct();
		$this->XLS_HEADERS = explode("|", $this->tmp_xls_header);
		$this->API_HEADERS = explode("|", $this->tmp_api_header);
	}

	/**
	 * SSGmall 전용 API 읽기
	 * @param $args
	 * @return array
	 */
	public function getCurl($args) {
		$arrRet = array(
			'status_code' => 0,
			'result_text' => '',
		);
		$api_url = "";
		$json_ret_param = array();
		extract($args);
		$is_post   = false;
		$headers   = array();
		$headers[] = "Content-Type: application/x-www-form-urlencoded";
		//echo "api_url->".$api_url."<br />";
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $api_url);
		curl_setopt($curl, CURLOPT_POST, $is_post);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json_ret_param);
		$arrRet['result_text'] = curl_exec($curl);
		$arrRet['status_code'] = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $arrRet;
	}

	/**
	 * 롯데닷컴 API 인증키 생성 조회
	 * @param $args
	 * @return array
	 */
	public function getSubscriptionId($args) {
		$arrRet = array(
			'status_code' => -9999,
			'result_text' => '',
		);
		$UserId = "";
		$PassWd = "";
		extract($args);
		$api_url = $this->API_HOST."/openapi/createCertification.lotte?strUserId=".$UserId."&strPassWd=".$PassWd;
		$ret_curl    = $this->getCurl(array('api_url' => $api_url));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$result_xml            = simplexml_load_string($response);
			foreach ($result_xml->children() as $key => $val) {
				if ($key == "Result") {
					foreach ($val->children() as $r_key => $r_val) {
						if ($r_key == "SubscriptionId") {
							$arrRet["result_text"] = (string)$r_val;
						}
					}
				}
			}
		}
		return $arrRet;
	}


	/**
	 * 발송 처리
	 * @param $args
	 * @return bool
	 */
	public function setOrderDeliveryConfirm($args)
	{
		$arrRet = array(
			'result' => false,
			'result_text' => '',
		);
		$UserId = "";
		$PassWd = "";
		$ord_no = "";
		$ord_dtl_sn = "";
		$hdc_cd = "";
		$inv_no = "";
		extract($args);
		$_sscId = $this->getSubscriptionId($args);
		if($_sscId["status_code"] < 0) {
			$arrRet["status_code"] = -8888;
			$arrRet["result_text"] = "SubscriptionId Error ";
			return $arrRet;
		}
		$_SUBSCRIPTIONID = $_sscId["result_text"];

		$api_url = $this->API_HOST . "/openapi/searchNewOrdLstOpenApi.lotte";
		$param   = "?subscriptionId=" . $_SUBSCRIPTIONID;
		$param   .= "&ord_no=" . $ord_no;
		$param   .= "&ord_dtl_sn=" . $ord_dtl_sn;
		$param   .= "&hdc_cd=" . $hdc_cd;       // 택배사 코드
		$param   .= "&inv_no=" . $inv_no;       // 송장번호
		$ret_curl    = $this->getCurl(array('api_url' => $api_url.$param));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];

		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$result_xml            = simplexml_load_string($response);
			foreach ($result_xml->children() as $key => $val) {
				if ($key == "Result") {
					if($val == "1") {
						$arrRet["result"] = true;
					} else {
						$arrRet["result_text"] = $val;
					}
				}
			}
		}
		return $arrRet;
	}

	/**
	 * 신규 주문 리스트가져 오기
	 * 신규주문 조회(신규주문 주문 확인) -> 발송대기 리스트 조회
	 * @param $args
	 * @return array
	 */
	public function getOrderList($args)
	{
		$arrRet     = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
		);
		$s_date = "";
		$e_date = "";
		$UserId = "";
		$PassWd = "";
		$API_header = $this->API_HEADERS;
		extract($args);
		$_sscId = $this->getSubscriptionId($args);
		if($_sscId["status_code"] < 0) {
			$arrRet["status_code"] = -8888;
			$arrRet["result_text"] = "SubscriptionId Error ";
			return $arrRet;
		}
		$_SUBSCRIPTIONID = $_sscId["result_text"];

		//region *** 신규주문 조회 후 주문 확인 처리 (조회하면 확인 처리됨)***
		$api_url = $this->API_HOST."/openapi/searchNewOrdLstOpenApi.lotte";
		$param = "?subscriptionId=".$_SUBSCRIPTIONID;
		$param .= "&start_date=".$s_date;
		$param .= "&end_date=".$e_date;
		$param .= "&SelOption=00";  // 00:주문접수/주문 완료(출고지시이전), 01:미발주(신규주문), 02:발주확인(상품준비), 03:발송약정
		$ret_curl    = $this->getCurl(array('api_url' => $api_url.$param));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
		}
		$api_url = $this->API_HOST."/openapi/searchNewOrdLstOpenApi.lotte";
		$param = "?subscriptionId=".$_SUBSCRIPTIONID;
		$param .= "&start_date=".$s_date;
		$param .= "&end_date=".$e_date;
		$param .= "&SelOption=01";  // 00:주문접수/주문 완료(출고지시이전), 01:미발주(신규주문), 02:발주확인(상품준비), 03:발송약정
		$ret_curl    = $this->getCurl(array('api_url' => $api_url.$param));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
		}
		//endregion



		//region *** 주문확인 리스트 ***
		$api_url = $this->API_HOST."/openapi/searchNewOrdLstOpenApi.lotte";
		$param = "?subscriptionId=".$_SUBSCRIPTIONID;
		$param .= "&start_date=".$s_date;
		$param .= "&end_date=".$e_date;
		$param .= "&SelOption=03";  // 00:주문접수/주문 완료(출고지시이전), 01:미발주(신규주문), 02:발주확인(상품준비), 03:발송약정
		$ret_curl    = $this->getCurl(array('api_url' => $api_url.$param));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		$arrData     = array();
		$tmp_array   = array();
		//echo $response;
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$result_xml            = simplexml_load_string($response);
			foreach ($result_xml->children() as $key => $val) {
				if ($key == "Result") {
					foreach ($val->children() as $r_key => $r_val) {
						if ($r_key == "OrderInfo") {
							for ($i = 0; $i < count($API_header); $i++) {
								$tmp = "";
								foreach ($r_val->children() as $o_key => $o_val) {
									if ($o_key == $API_header[$i]) {
										$tmp = trim((string)$o_val);
										//echo $API_header[$i]." : " .$o_key . "->" . $tmp . "<br />";
									}
									if ($o_key == "DelvInfo") {
										foreach ($o_val->children() as $delv_key => $delv_val) {
											if ($o_key."/".$delv_key == $API_header[$i]) {
												$tmp = trim((string)$delv_val);
												//echo "---" .$API_header[$i]." : " .$delv_key . "->" . $tmp . "<br />";
											}
										}
									}

								}
								$arr_row[$API_header[$i]] = (string)$tmp. "";
							}

							foreach ($r_val->children() as $o_key => $o_val) {
								if ($o_key == "ProdInfo") {
									//echo "=====================================<br />";
									$arr_item_row = $arr_row;
									foreach ($o_val->children() as $prod_key => $prod_val) {
										for ($i = 0; $i < count($API_header); $i++) {
											if ($o_key . "/" . $prod_key == $API_header[$i]) {
												$tmp = trim((string)$prod_val);
												$arr_item_row[$API_header[$i]] = $tmp."";
												//echo "===" . $API_header[$i] . " : " . $prod_key . "->" . $tmp . "<br />";
											}
										}

										//echo "===" . $prod_key . "->" . $prod_val . "<br />";
									}
									$arrData[] = $arr_item_row;
								}
							}
						}
					}
				}
			}
		}
		//endregion

		//print_r2($arrData);

		if($arrRet["status_code"] < 0) {
			$arrRet["result_text"] = "API Error ";
			return $arrRet;
		} else {
			$arrRet["result_text"] = "";
		}
		$arrRet["result_data"] = $arrData;
		return $arrRet;

	}


	/**
	 * 주문 취소 요청 리스트
	 * @param $args
	 * @return array
	 */
	public function execCancelProc($args)
	{
		$arrRet     = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
			'request_cnt' => 0,    // 전체 취소 요청 수
			'confirm_cnt' => 0,    // 취소 숭인 수
			'reject_cnt' => 0,     // 취소 거부 수 (이미 송장 이상으로)
			'notorder_cnt' => 0,   // DY 에 주문 내역 없음
		);
		$seller_idx      = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg          = "";
		$s_date = "";
		$e_date = "";
		$UserId = "";
		$PassWd = "";
		$API_header = $this->API_HEADERS;
		extract($args);
		$_sscId = $this->getSubscriptionId($args);
		if($_sscId["status_code"] < 0) {
			$arrRet["status_code"] = -8888;
			$arrRet["result_text"] = "SubscriptionId Error ";
			return $arrRet;
		}
		$_SUBSCRIPTIONID = $_sscId["result_text"];

		$C_CS    = new CS();
		$C_Order = new Order();

		$api_url = $this->API_HOST."/openapi/searchCnclList.lotte";
		$param = "?subscriptionId=".$_SUBSCRIPTIONID;
		$param .= "&start_date=".$s_date;
		$param .= "&end_date=".$e_date;
		$ret_curl    = $this->getCurl(array('api_url' => $api_url.$param));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$result_xml            = simplexml_load_string($response);
			foreach ($result_xml->children() as $key => $val) {
				if ($key == "Result") {
					foreach ($val->children() as $r_key => $r_val) {
						if ($r_key == "OrderInfo") {
							foreach ($r_val->children() as $o_key => $o_val) {
								if ($o_key == "OrdNo") {
									$_OrdNo = trim((string)$o_val);
								}
								if ($o_key == "ProdInfo") {
									foreach ($o_val->children() as $prod_key => $prod_val) {
										if ($prod_key == "OrdDtlSn") {
											$_OrdDtlSn = trim((string)$prod_val);
										}
									}
									if($_OrdNo != "" && $_OrdDtlSn != "") {
										$arrRet['request_cnt']++;
										$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $_OrdNo, $_OrdDtlSn);
										if (count($_row) > 0) {
											if($_row["order_progress_step"] == "ORDER_INVOICE" || $_row["order_progress_step"] == "ORDER_SHIPPED") {
												$arrRet["reject_cnt"]++;
											} else {
												$arrRet["result_data"][] = array(
													'market_order_no' => $_OrdNo,
													'market_order_subno' => $_OrdDtlSn,
												);
												$cancel_ret = $C_CS->updateOrderCancelOneByOrderIdx($_row["order_idx"], $cs_reason_code1, $cs_reason_code2, $cs_msg); // DY 에 취소상태 변경
												if($cancel_ret)  {
													$arrRet['confirm_cnt']++;
												} else {
													$arrRet["reject_cnt"]++;
												}
											}
										} else {
											$arrRet['notorder_cnt']++;
										}
									}
								}
							}

						}
					}
				}
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
		$arrRet     = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
			'request_cnt' => 0,    // 전체 발송 대기 수
			'confirm_cnt' => 0,    // 송장입력 수
			'reject_cnt' => 0,     // 송장 거부 수 (에러 및 DY 배송상태 아님 등)
			'notorder_cnt' => 0,   // DY 에 주문 내역 없음
		);
		$seller_idx      = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg          = "";
		$s_date = "";
		$e_date = "";
		$UserId = "";
		$PassWd = "";
		$API_header = $this->API_HEADERS;
		extract($args);
		$_sscId = $this->getSubscriptionId($args);
		if($_sscId["status_code"] < 0) {
			$arrRet["status_code"] = -8888;
			$arrRet["result_text"] = "SubscriptionId Error ";
			return $arrRet;
		}
		$_SUBSCRIPTIONID = $_sscId["result_text"];

		$C_CS    = new CS();
		$C_Order = new Order();

		//region *** 주문확인 리스트 ***
		$api_url = $this->API_HOST."/openapi/searchNewOrdLstOpenApi.lotte";
		$param = "?subscriptionId=".$_SUBSCRIPTIONID;
		$param .= "&start_date=".$s_date;
		$param .= "&end_date=".$e_date;
		$param .= "&SelOption=03";  // 00:주문접수/주문 완료(출고지시이전), 01:미발주(신규주문), 02:발주확인(상품준비), 03:발송약정
		$ret_curl    = $this->getCurl(array('api_url' => $api_url.$param));
		$status_code = $ret_curl['status_code'];
		$response    = $ret_curl['result_text'];
		$arrData     = array();
		$tmp_array   = array();
		//echo $response;
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$result_xml            = simplexml_load_string($response);
			foreach ($result_xml->children() as $key => $val) {
				if ($key == "Result") {
					foreach ($val->children() as $r_key => $r_val) {
						if ($r_key == "OrderInfo") {
							for ($i = 0; $i < count($API_header); $i++) {
								$tmp = "";
								foreach ($r_val->children() as $o_key => $o_val) {
									if ($o_key == $API_header[$i]) {
										$tmp = trim((string)$o_val);
									}
									if ($o_key == "DelvInfo") {
										foreach ($o_val->children() as $delv_key => $delv_val) {
											if ($o_key."/".$delv_key == $API_header[$i]) {
												$tmp = trim((string)$delv_val);
											}
										}
									}

								}
								$arr_row[$API_header[$i]] = (string)$tmp. "";
							}

							foreach ($r_val->children() as $o_key => $o_val) {
								if ($o_key == "ProdInfo") {
									//echo "=====================================<br />";
									$arr_item_row = $arr_row;
									foreach ($o_val->children() as $prod_key => $prod_val) {
										for ($i = 0; $i < count($API_header); $i++) {
											if ($o_key . "/" . $prod_key == $API_header[$i]) {
												$tmp = trim((string)$prod_val);
												$arr_item_row[$API_header[$i]] = $tmp."";
											}
										}
									}
									$arrData[] = $arr_item_row;
								}
							}
						}
					}
				}
			}
		}
		//endregion

		if(count($arrData) > 0) {
			foreach ($arrData as $order) {
				$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $order["OrdNo"], $order["ProdInfo/ProdSeq"]);
				if (count($_row) > 0) {
					if($_row["order_progress_step"] == "ORDER_SHIPPED") {
						$arrRet["confirm_cnt"]++;
						//echo $_row["order_progress_step"];
						$arrRquert = array(
							'UserId' => $UserId,
							'PassWd' => $PassWd,
							'ord_no' => $order["OrdNo"],
							'ord_dtl_sn' => $order["ProdInfo/ProdSeq"],
							'hdc_cd' => $_row["market_delivery_code"],   // 택배사코드
							'inv_no' => $_row["invoice_no"],   // 운송장번호
						);

						$C_Order->updateMarketInvoiceState($seller_idx, $val["ORD_NO"], $val["PRODUCT/PRD/ORD_SEQ"]
							, "U", "미처리");

						//region *** 마켓 발송 처리 ***
						// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.
						if(DY_MARKET_IS_LIVE) {
							$_deliveryConfirm = $this->setOrderDeliveryConfirm($arrRquert);   // 발송 처리
							$C_Order->updateMarketInvoiceState($seller_idx, $val["ORD_NO"], $val["PRODUCT/PRD/ORD_SEQ"]
								, ($_deliveryConfirm["result"] ? "S" : "F"), $_deliveryConfirm["result_text"]);
						}
						//endregion

						$arrRet['confirm_cnt']++;
					} else {
						$arrRet["reject_cnt"]++;
					}
				} else {
					$arrRet['notorder_cnt']++;
				}

			}
		}


		return $arrRet;
	}

}
?>