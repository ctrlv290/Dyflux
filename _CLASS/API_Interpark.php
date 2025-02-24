<?php
/**
 * Class API_Interpark
 * User : ssawoona
 * Date : 2019
 */
class API_Interpark extends Dbconn {
	private $tmp_xls_header = "주문번호|주문순번|상품번호|상품명|상품옵션|수량|실제주문금액(*)|판매단가|특판단가|배송비납부방식(선불,착불)|배송비|주문자|주문자 전화번호|주문자 핸드폰번호|수령인|수령인전화번호|수령인핸드폰번호|수령인우편번호|수령인주소|수령인우편번호(도로명)|수령인주소(도로명)|배송메시지|신규주문확인일시|수취인주소2|도로명 수취인주소2|주문자메일주소|주문일자|입금일자|해외통관고유부호|업체 부담 쿠폰할인단가|인터파크 부담 쿠폰할인단가|업체상품코드|쿠폰할인단가|배송비착불여부|I-Point할인단가|옵션부모상품순번|판매자즉시할인단가|옵션상품코드|옵션상품유형|업체번호|선할인단가|공급계약일련번호|옵션명|배송비 번호|상태일자|옵션코드|옵션명|선할인금액|업체번호|공급계약일련번호|추가배송비|초기배송비|배송비|추가배송비";
	private $tmp_api_header = "ORD_NO|PRODUCT/PRD/ORD_SEQ|PRODUCT/PRD/PRD_NO|PRODUCT/PRD/PRD_NM|PRODUCT/PRD/OPT_NM|PRODUCT/PRD/ORD_QTY|PRODUCT/PRD/ORD_AMT|PRODUCT/PRD/SALE_UNITCOST|PRODUCT/PRD/REAL_SALE_UNITCOST|RODUCT/PRD/IS_COLLECTED|DELIVERY/DELV/DEL_AMT|ORD_NM|TEL|MOBILE_TEL|RCVR_NM|DELI_TEL|DELI_MOBILE|DEL_ZIP|DELI_ADDR1|DEL_ZIP_DORO|DELI_ADDR1_DORO|DELI_COMMENT|ORDER_DTS|DELI_ADDR2|DELI_ADDR2_DORO|EMAIL|ORDER_DT|PAY_DTS|RESIDENT_NO|PRODUCT/PRD/ENTR_DC_COUPON_AMT|PRODUCT/PRD/DC_COUPON_AMT|PRODUCT/PRD/ENTR_PRD_NO|PRODUCT/PRD/TOT_DC_COUPON_AMT|PRODUCT/PRD/IS_COLLECTED|PRODUCT/PRD/IPOINT_DC_UNITCOST|PRODUCT/PRD/OPT_PARENT_SEQ|PRODUCT/PRD/ENTR_DIS_UNIT_COST|PRODUCT/PRD/OPT_PRD_NO|PRODUCT/PRD/OPT_PRD_TP|PRODUCT/PRD/SUPPLY_ENTR_NO|PRODUCT/PRD/PRE_USE_UNITCOST|PRODUCT/PRD/SUPPLY_CTRT_SEQ|PRODUCT/PRD/IN_OPT_NM|PRODUCT/PRD/DELVSETL_SEQ|PRODUCT/PRD/ORDCLM_STAT_DTS|PRODUCT/PRD/OPT_NO|PRODUCT/PRD/SEL_OPT_NM|PRODUCT/PRD/PRE_USE_AMT|DELIVERY/DELV/SUPPLY_ENTR_NO|DELIVERY/DELV/SUPPLY_CTRT_SEQ|DELIVERY/DELV/ADD_DEL_AMT|DELIVERY/DELV/INITIAL_DELV_AMT|DELIVERY_DETAIL/PRD_DELV/DELV_AMT|DELIVERY_DETAIL/PRD_DELV/ADD_DELV_AMT";
	public $XLS_HEADERS;
	public $API_HEADERS;
	function __construct()
	{
		Dbconn::__construct();
		$this->XLS_HEADERS = explode("|", $this->tmp_xls_header);
		$this->API_HEADERS = explode("|", $this->tmp_api_header);
	}

	public function getCurl($args)
	{
		$arrRet = array(
			'status_code' => 0,
			'result_text' => '',
		);
		$url = "";
		extract($args);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/xml;charset=euc-kr"));
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

		$arrRet['result_text'] = curl_exec($curl);
		$arrRet['status_code'] = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $arrRet;
	}

	/**
	 * 주문 리스트
	 * @param $args
	 * @return array
	 */
	public function getOrderList($args, $orderConfirm = true)
	{
		$arrRet = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
		);
		$sc_entrId = "";
		$sc_supplyEntrNo = "";
		$sc_supplyCtrtSeq = "";
		$s_date = "";
		$e_date = "";
		$API_header = $this->API_HEADERS;
		extract($args);
		$arrData     = array();
		$tmp_array   = array();
		foreach ($API_header as $val) {
			$tmp_array[$val] = "";
		}
		$row_num = 0;

//		$sc_entrId = "DUCKYUN";
//		$sc_supplyEntrNo = "3002821861";
//		$sc_supplyCtrtSeq = "1";

//		$sc_strDate = "20181128000000";
//		$sc_endDate = "20181128235959";

//		$sc_supplyEntrNo = "3002806651";
//		$sc_strDate = "20170410000000";
//		$sc_endDate = "20170410235959";


		$search_s_date = new DateTime($s_date);
		$search_e_date = new DateTime($e_date);
//echo date(strtotime($end. ' + 1 days'));
		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($search_s_date, $interval, $search_e_date->modify('+1 day'));

		foreach ($period as $dt) {
			//echo $dt->format("Ymd") . "<br />";
			$sc_strDate = (string)$dt->format("Ymd"). "000000";
			$sc_endDate = (string)$dt->format("Ymd"). "235959";

			$query = "";
			$query .= "&sc.entrId=" . $sc_entrId;
			$query .= "&sc.supplyEntrNo=" . $sc_supplyEntrNo;
			$query .= "&sc.supplyCtrtSeq=" . $sc_supplyCtrtSeq;
			$query .= "&sc.strDate=" . $sc_strDate;
			$query .= "&sc.endDate=" . $sc_endDate;

			if($orderConfirm) {
				/// **** 주문확인 ****
				$url = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=orderListForSingle' . $query;    // 신규주문 리스트 및 주문확인 작업
				$this->getCurl(array(
						'url' => $url,
					)
				);
			}

			$url      = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=orderListDelvForSingle' . $query;  // 주문확인 리스트
			//$url      = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=delvCompListForSingle' . $query;  // 배송정보 리스트
			//echo $url. "<br />";
			$ret_curl = $this->getCurl(array(
					'url' => $url,
				)
			);


			$status_code           = $ret_curl['status_code'];
			$response              = $ret_curl['result_text'];
			$arrRet["status_code"] = $status_code;

			/*if ($status_code == 200) {
				$result_xml            = simplexml_load_string($response);
				if (count($result_xml->ORDER) > 0) {
					foreach ($result_xml->ORDER as $k_order => $v_order) {
						echo "<Br /><Br /><Br />";
						foreach ($v_order as $k_order_item => $v_order_item) {
							echo $k_order_item . "->" . $v_order_item . "<Br />";
						}
						if (count($v_order->PRODUCT) > 0) {
							foreach ($v_order->PRODUCT->PRD as $k_order_product => $v_order_product) {
								echo "<Br />";
								foreach ($v_order_product as $k_order_product_item => $v_order_product_item) {
									echo "PRODUCT/PRD/"  . $k_order_product_item . "->" . $v_order_product_item . "<Br />";
								}
							}
						}
						if (count($v_order->DELIVERY) > 0) {
							foreach ($v_order->DELIVERY->DELV as $k_order_delivery => $v_order_delivery) {
								foreach ($v_order_delivery as $k_order_delivery_item => $v_order_delivery_item) {
									echo "DELIVERY/DELV/"  . $k_order_delivery_item . "->" . $v_order_delivery_item . "<Br />";
								}
							}
						}
						if (count($v_order->DELIVERY_DETAIL) > 0) {
							foreach ($v_order->DELIVERY_DETAIL->PRD_DELV as $k_order_delivery => $v_order_delivery) {
								foreach ($v_order_delivery as $k_order_delivery_item => $v_order_delivery_item) {
									echo "DELIVERY_DETAIL/PRD_DELV/"  . $k_order_delivery_item . "->" . $v_order_delivery_item . "<Br />";
								}
							}
						}
					}
				}
			}*/

			if ($status_code == 200) {
				$arrRet["status_code"] = 0;
				$result_xml            = simplexml_load_string($response);
				if (count($result_xml->ORDER) > 0) {
					foreach ($result_xml->ORDER as $k_order => $v_order) {
						//echo $k_order."->".$v_order."<Br />";
						for ($i = 0; $i < count($API_header); $i++) {
							$tmp = "";
							foreach ($v_order as $k_order_item => $v_order_item) {
								//echo $k_order_item . "->" . $v_order_item . "<Br />";
								if ($k_order_item == $API_header[$i]) {
									//echo $API_header[$i].":::".$k_order_item . "->" . $v_order_item . "<Br />";
									$tmp = (string)$v_order_item;
								}
							}
							if (count($v_order->DELIVERY) > 0) {
								foreach ($v_order->DELIVERY->DELV as $k_order_delivery => $v_order_delivery) {
									foreach ($v_order_delivery as $k_order_delivery_item => $v_order_delivery_item) {
										if ("DELIVERY/DELV/".$k_order_delivery_item == $API_header[$i]) {
											$tmp = (string)$v_order_delivery_item;
										}
									}
								}
							}
							if (count($v_order->DELIVERY_DETAIL) > 0) {
								foreach ($v_order->DELIVERY_DETAIL->PRD_DELV as $k_order_delivery => $v_order_delivery) {
									foreach ($v_order_delivery as $k_order_delivery_item => $v_order_delivery_item) {
										if ("DELIVERY/PRD_DELV/".$k_order_delivery_item == $API_header[$i]) {
											$tmp = (string)$v_order_delivery_item;
										}
									}
								}
							}
							$arr_row[$API_header[$i]] = (string)$tmp . "";
						}
						$arr_row["DELI_ADDR1"]      = $arr_row["DELI_ADDR1"] . " " . (string)$v_order->DELI_ADDR2;
						$arr_row["DELI_ADDR1_DORO"] = $arr_row["DELI_ADDR1_DORO"] . " " . (string)$v_order->DELI_ADDR2_DORO;

						//echo "COUNT(PRODUCT) -> ".count($v_order->PRODUCT)."<br />";
						if (count($v_order->PRODUCT) > 0) {
							foreach ($v_order->PRODUCT->PRD as $k_order_product => $v_order_product) {
								$arr_item_row = $arr_row;
								//echo "------------<Br />";
								foreach ($v_order_product as $k_order_product_item => $v_order_product_item) {
									$location = "PRODUCT/PRD/";
									//echo "&nbsp;&nbsp;&nbsp;&nbsp;" . $location . $k_order_product_item . "->" . $v_order_product_item . "<Br />";
									for ($i = 0; $i < count($API_header); $i++) {
										if ($location . $k_order_product_item == $API_header[$i]) {
											$arr_item_row[$API_header[$i]] = $v_order_product_item . "";
											//echo "&nbsp;&nbsp;&nbsp;&nbsp;" . $location . $k_order_product_item . "->" . $v_order_product_item . "<Br />";
										}
									}
								}
								if ($arr_row["ORD_NO"] != "") {
									$arrData[$row_num] = $arr_item_row;
									$row_num++;
								}
							}
						}

					}
				}
			}

		}

		//print_r2($arrData);

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
	 * 주문 취소 승인 처리
	 * @param $args
	 * @return bool
	 */
	public function setOrderCancelConfirm($args)
	{
		$ret = false;
		$sc_entrId = "";
		$sc_supplyEntrNo = "";
		$sc_supplyCtrtSeq = "";
		$sc_ordclmNo = "";
		$sc_ordSeq = "";
		$sc_clmReqSeq = "";
		$sc_optPrdTp = "";
		$sc_optOrdSeqList = "";
		extract($args);

		$query = "";
		$query .= "&sc.entrId=" . $sc_entrId;
		$query .= "&sc.ordclmNo=" . $sc_ordclmNo;
		$query .= "&sc.ordSeq=" . $sc_ordSeq;
		$query .= "&sc.clmReqSeq=" . $sc_clmReqSeq;
		$query .= "&sc.optPrdTp=" . $sc_optPrdTp;
		$query .= "&sc.optOrdSeqList=" . $sc_optOrdSeqList;

		// 취소주문 승인
		$url      = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=cnclReqAcceptForComm' . $query;  // 취소주문 승인
		$ret_curl = $this->getCurl(array(
				'url' => $url,
			)
		);
		$status_code           = $ret_curl['status_code'];
		$response              = $ret_curl['result_text'];
		$arrRet["status_code"] = $status_code;
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$result_xml            = simplexml_load_string($response);

			foreach ($result_xml->RESULT as $xml_ret) {
				foreach ($xml_ret as $key => $val) {
					if ($key == "CODE") {
						if ($val == "000") {
							$ret = true;
						}
					}
				}
			}

		}
		return $ret;
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
		$sc_entrId = "";
		$sc_ordclmNo = "";
		$sc_ordSeq = "";
		$sc_delvDt = "";
		$sc_delvEntrNo = "";
		$sc_invoNo = "";
		$sc_optPrdTp = "";
		$sc_optOrdSeqList = "";
		extract($args);

		$query = "";
		$query .= "&sc.entrId=" . $sc_entrId;
		$query .= "&sc.ordclmNo=" . $sc_ordclmNo;
		$query .= "&sc.ordSeq=" . $sc_ordSeq;
		$query .= "&sc.delvDt=" . $sc_delvDt;   //YYYYMMDD 출고완료일자
		$query .= "&sc.delvEntrNo=" . $sc_delvEntrNo;   //택배사코드
		$query .= "&sc.invoNo=" . $sc_invoNo;   //운송장번호
		$query .= "&sc.optPrdTp=" . $sc_optPrdTp;
		$query .= "&sc.optOrdSeqList=" . $sc_optOrdSeqList;

		$api_result_code = "";
		// 발송 처리
		$url      = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=delvCompForComm' . $query;  // 발송처리
		$ret_curl = $this->getCurl(array(
				'url' => $url,
			)
		);

		$status_code           = $ret_curl['status_code'];
		$response              = $ret_curl['result_text'];
		$arrRet["status_code"] = $status_code;

		if ($status_code == 200) {
			//$arrRet["status_code"] = 0;
			$result_xml            = simplexml_load_string($response);

			foreach ($result_xml->RESULT as $xml_ret) {
				foreach ($xml_ret as $key => $val) {

					if ($key == "CODE") {
						if ($val == "000") {
							$arrRet["result"] = true;
						}
						$api_result_code = $val;
					}
					if ($key == "MESSAGE") {
						//echo $order."<br />";
						if (!$arrRet["result"]) {
							$arrRet["result_text"] = $api_result_code . ":" . $val;
						}
					}
				}
			}

		} else {
			$arrRet["result_text"] = $status_code . ":API Error" ;
		}
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
		$sc_entrId = "";
		$sc_supplyEntrNo = "";
		$sc_supplyCtrtSeq = "";
		$s_date = "";
		$e_date = "";
		$API_header = $this->API_HEADERS;
		$seller_idx = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg = "";
		extract($args);
		$arrData     = array();
		$tmp_array   = array();
		foreach ($API_header as $val) {
			$tmp_array[$val] = "";
		}
		$row_num = 0;


		$search_s_date = new DateTime($s_date);
		$search_e_date = new DateTime($e_date);
		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($search_s_date, $interval, $search_e_date->modify('+1 day'));

		foreach ($period as $dt) {
			//echo $dt->format("Ymd") . "<br />";
			$sc_strDate = (string)$dt->format("Ymd"). "000000";
			$sc_endDate = (string)$dt->format("Ymd"). "235959";

			$query = "";
			$query .= "&sc.entrId=" . $sc_entrId;
			$query .= "&sc.supplyEntrNo=" . $sc_supplyEntrNo;
			$query .= "&sc.supplyCtrtSeq=" . $sc_supplyCtrtSeq;
			$query .= "&sc.strDate=" . $sc_strDate;
			$query .= "&sc.endDate=" . $sc_endDate;

			// 취소주문 리스트
			$url      = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=cnclNClmReqListForSingle' . $query;  // 취소주문 리스트
			//$url      = 'https://joinapi.interpark.com/order/OrderClmAPI.do?_method=orderCompListForSingle' . $query;  // 배송정보 리스트
			$ret_curl = $this->getCurl(array(
					'url' => $url,
				)
			);

			$status_code           = $ret_curl['status_code'];
			$response              = $ret_curl['result_text'];
			$arrRet["status_code"] = $status_code;
			//echo $response;
			if ($status_code == 200) {
				$arrRet["status_code"] = 0;
				$result_xml            = simplexml_load_string($response);
				if (count($result_xml->ORDER) > 0) {
					//echo $response;
					foreach ($result_xml->ORDER as $k_order => $v_order) {
						$arr_row = array(
							'market_order_no' => '',
							'market_order_subno' => '',
							'ORD_NO' => '', //인터파크주문번호
							'CLMREQ_SEQ' => '', //클레임요청순번
							'PRODUCT/PRD/ORD_SEQ' => '',    //주문순번
							'PRODUCT/PRD/OPT_PRD_TP' => '', //옵션상품유형
							'PRODUCT/PRD/OPT_PARENT_SEQ' => '', //옵션부모상품순번
						);
						foreach ($v_order as $k_order_item => $v_order_item) {
							switch ($k_order_item) {
								case "ORD_NO" :
									$arr_row["market_order_no"] = $v_order_item;
									$arr_row["ORD_NO"] = $v_order_item;
									break;
								case "CLMREQ_SEQ":
									$arr_row["CLMREQ_SEQ"] = $v_order_item;
									break;
							}
						}


						if (count($v_order->PRODUCT) > 0) {
							foreach ($v_order->PRODUCT->PRD as $k_order_product => $v_order_product) {
								$arr_item_row = $arr_row;
								//echo "------------<Br />";
								foreach ($v_order_product as $k_order_product_item => $v_order_product_item) {
									$location = "PRODUCT/PRD/";
									switch ($location . $k_order_product_item) {
										case "PRODUCT/PRD/ORD_SEQ":
											$arr_item_row["market_order_subno"] = $v_order_product_item . "";
											$arr_item_row["PRODUCT/PRD/ORD_SEQ"] = $v_order_product_item . "";
											break;
										case "PRODUCT/PRD/OPT_PRD_TP":
											$arr_item_row["PRODUCT/PRD/OPT_PRD_TP"] = $v_order_product_item . "";
											break;
										case "PRODUCT/PRD/OPT_PARENT_SEQ":
											$arr_item_row["PRODUCT/PRD/OPT_PARENT_SEQ"] = $v_order_product_item . "";
											break;
									}
								}
								if ($arr_row["market_order_no"] != "") {
									$arrRet["result_data"][]  = $arr_item_row;
									$row_num++;
								}
							}
						}
					}
				}
			}



		}


		$C_CS = new CS();
		$C_Order = new Order();

		foreach ($arrRet["result_data"] as $cancel_rows) {
			$arrRet["request_cnt"]++;

			$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $cancel_rows["market_order_no"], $cancel_rows["market_order_subno"]);
			if(count($_row) > 0) {
				if($_row["order_progress_step"] == "ORDER_INVOICE" || $_row["order_progress_step"] == "ORDER_SHIPPED") {
					// 주문 취소 할 수 없음
					$arrRet["reject_cnt"]++;

					//region *** 마켓 실제 취소 거부 처리 ***
					// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.
					// 취소 거부 처리 ??? API 없음
					//endregion

				} else {
					// 주문 취소 가능 > 주문취소
					$arrRquert = array(
						'sc_entrId' => $sc_supplyEntrNo,
						'sc_supplyEntrNo' => $sc_supplyEntrNo,
						'sc_supplyCtrtSeq' => $sc_supplyCtrtSeq,
						'sc_ordclmNo' => $cancel_rows["ORD_NO"],   // 인터파크 주문번호
						'sc_ordSeq' => $cancel_rows["PRODUCT/PRD/ORD_SEQ"],   // 인터파크 주문순번
						'sc_clmReqSeq' => $cancel_rows["CLMREQ_SEQ"],   // 클레임요청순번
						'sc_optPrdTp' => $cancel_rows["PRODUCT/PRD/OPT_PRD_TP"],   // 옵션상품유형
						'sc_optOrdSeqList' => $cancel_rows["PRODUCT/PRD/OPT_PARENT_SEQ"],   // 주문순번리스트
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

		//print_r2($arrData);

		return $arrRet;
	}


	public function execDeliveryProc($args)
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
		$sc_entrId = "";
		$sc_supplyEntrNo = "";
		$sc_supplyCtrtSeq = "";
		$s_date = "";
		$e_date = "";
		$API_header = $this->API_HEADERS;
		$seller_idx = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg = "";
		extract($args);

		$_delivery = array();

		$C_Order = new Order();
		// 주문 조회
		$ret_cancel = $this->getOrderList($args, false);
		$status_code = $ret_cancel['status_code'];
		//print_r2($ret_cancel['result_data']);
		if ($status_code == 0) {
			$arrRet["status_code"] = 0;
			foreach ($ret_cancel['result_data'] as $val) {
				$arrRet['request_cnt']++;
				$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $val["ORD_NO"], $val["PRODUCT/PRD/ORD_SEQ"]);
				if (count($_row) > 0) {
					if($_row["order_progress_step"] == "ORDER_SHIPPED") {
						$arrRet["confirm_cnt"]++;

						$arrRquert = array(
							'sc_entrId' => $sc_supplyEntrNo,
							'sc_supplyEntrNo' => $sc_supplyEntrNo,
							'sc_supplyCtrtSeq' => $sc_supplyCtrtSeq,
							'sc_ordclmNo' => $val["ORD_NO"],   // 인터파크 주문번호
							'sc_ordSeq' => $val["PRODUCT/PRD/ORD_SEQ"],   // 인터파크 주문순번

							'sc_delvDt' => date("Ymd"),   // YYYYMMDD 출고완료일자
							'sc_delvEntrNo' => $_row["market_delivery_code"],   // 택배사코드
							'sc_invoNo' => $_row["invoice_no"],   // 운송장번호

							'sc_optPrdTp' => $val["PRODUCT/PRD/OPT_PRD_TP"],   // 옵션상품유형
							'sc_optOrdSeqList' => $val["PRODUCT/PRD/OPT_PARENT_SEQ"],   // 주문순번리스트
						);

						$C_Order->updateMarketInvoiceState($seller_idx, $val["ORD_NO"], $val["PRODUCT/PRD/ORD_SEQ"]
							, "U", "미처리");
						//region *** 마켓 발송 처리 ***
						// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.
						if(DY_MARKET_IS_LIVE) {
							$_deliveryConfirm = $this->setOrderDeliveryConfirm($arrRquert);   // 발송 처리
							$C_Order->updateMarketInvoiceState($seller_idx, $val["ORD_NO"], $val["PRODUCT/PRD/ORD_SEQ"]
								, ($_deliveryConfirm["result"] ? "S" : "E"), $_deliveryConfirm["result_text"]);
						}
						//endregion

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