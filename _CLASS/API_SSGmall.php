<?php
/**
 * User: ssawoona
 * Date: 2019-03
 */

class API_SSGmall extends Dbconn
{
	private $tmp_xls_header = "배송번호|배송순번|최종배송상세진행상태코드(배송단위)|이전주문번호|이전시스템주문순번|이벤트순번|배송구분상세코드|배송구분상세명|재지시여부구분|지연횟수|주문번호|주문순번|주문완료일시|최종배송상세진행상태명(배송상품단위)|최종배송상세진행상태코드(배송상품단위)|공급업체아이디|공급업체명|하위공급업체아이디|하위공급업체명|판매불가신청상태|결품진행상태명|배송유형명|배송유형코드|배송유형상세명|배송유형상세코드|택배사ID|택배사명|운송장번호|박스번호|배송비|배송비 착불여부|해외배송비처리구분코드|해외배송비처리구분|상품명|업체상품번호|상품번호|단품ID|단품명|모델명|특정영업점번호|지시수량|취소수량|주문수량|공급가|판매가|국내/외 구분|중량정보등록여부|주문자|주문자 휴대폰번호|수취인|수취인 휴대폰번호|수취인 집전화번호|수취인 상세주소|수취인 우편번호|수취인 구우편번호(6자리)|수취인도로명주소|사은품|배송업무메모|고객배송메모|상품특성구분코드|배송상태코드|배송상태명|원주문번호|원주문순번|운송장등록오류코드명|운송장등록오류코드|배송주체코드|사이트번호|사이트명|공급가|개인통관고유번호|제휴주문번호|(구)주문번호|판매불가신청상태|수취인주소|수취인상세주소|주문상품구분|자동결품여부|출고기준일|해외배송지국가코드|해외배송지휴대전화번호|해외배송지전화번호|해외배송지우편번호|해외배송지기본주소|해외배송지상세주소";
	private $tmp_api_header = "shppNo|shppSeq|shppTabProgStatCd|bfOrderId|bfOrderSeq|evntSeq|shppDivDtlCd|shppDivDtlNm|reOrderYn|delayNts|ordNo|ordItemSeq|ordCmplDts|lastShppProgStatDtlNm|lastShppProgStatDtlCd|shppVenId|shppVenNm|shppLrnkVenId|shppLrnkVenNm|shortgProgStatCd|shortgProgStatNm|shppTypeNm|shppTypeCd|shppTypeDtlNm|shppTypeDtlCd|delicoVenId|delicoVenNm|wblNo|boxNo|shppcst|shppcstCodYn|frgShppcstProcDivCd|frgShppcstProcDivCdNm|itemNm|splVenItemId|itemId|uitemId|uitemNm|mdlNm|speSalestrNo|dircItemQty|cnclItemQty|ordQty|splprc|sellprc|frgShppYn|wgtRegYn|ordpeNm|ordpeHpno|rcptpeNm|rcptpeHpno|rcptpeTelno|shpplocAddr|shpplocZipcd|shpplocOldZipcd|shpplocRoadAddr|frebieNm|memoCntt|ordMemoCntt|itemChrctDivCd|shppStatCd|shppStatNm|orordNo|orordItemSeq|wblRegErrCdNm|wblRegErrCd|shppMainCd|siteNo|siteNm|splprc|pCus|allnOrdNo|oldOrdNo|itemDiv|shpplocBascAddr|shpplocDtlAddr|ordItemDivNm|autoShortgYn|whoutCritnDt|frgShpplocCntryCd|frgShpplocHpno|frgShpplocTelno|frgShpplocZipcd|frgShpplocBascAddr|frgShpplocDtlAddr";
	public $XLS_HEADERS;
	public $API_HEADERS;
	function __construct()
	{
		Dbconn::__construct();
		$this->XLS_HEADERS = explode("|", $this->tmp_xls_header);
		$this->API_HEADERS = explode("|", $this->tmp_api_header);
	}

	/**
	 * SSGmall 전용 API 읽기
	 * @param $args
	 * @return array
	 */
	public  function getCurl($args) {
		$arrRet = array(
			'status_code' => 0,
			'result_text' => '',
		);
		$api_url = "";
		$api_key = "";
		$json_ret_param = array();
		extract($args);
		$is_post   = true;
		$headers   = array();
		$headers[] = "Authorization: ".$api_key;
		$headers[] = "accept: application/json";
		$headers[] = "Content-Type: application/json";

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
	 * 주문 확인 처리 (발송대기로)
	 * @param $args
	 * @return bool
	 */
	public function setOrderConfirm($args)
	{
		$ret = false;
		$api_key = "";
		$shppNo = "";
		$shppSeq = "";
		extract($args);

		// 주문 확인 처리
		$api_url = "https://eapi.ssgadm.com/api/pd/1/updateOrderSubjectManage.ssg";
		$ret_param = array('requestOrderSubjectManage' => array());
		$ret_param["requestOrderSubjectManage"]["shppNo"] = $shppNo;
		$ret_param["requestOrderSubjectManage"]["shppSeq"] = $shppSeq;
		$json_ret_param = json_encode($ret_param, true);

		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url, 'json_ret_param' => $json_ret_param));
		$status_code = $ret_curl['status_code'];
		$response  = $ret_curl['result_text'];
		//echo $response;
		if ($status_code == 200) {
			$data_array = json_decode($response, true);;
			if($data_array["result"]["resultCode"] == "00") {
				$ret = true;
			}
		}

		return $ret;
	}

	/**
	 * 신규 주문 리스트가져 오기
	 * 신규주문 조회 -> 신규주문 주문 확인 -> 발송대기 리스트 조회
	 * @param $args
	 * @return array
	 */
	public function getOrderList($args) {
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

		//region *** 신규주문 조회 후 주문 확인 처리 ***
		$api_url = "https://eapi.ssgadm.com/api/pd/1/listShppDirection.ssg";
		$ret_param = array('requestShppDirection' => array());
		$ret_param["requestShppDirection"]["perdType"] = "01";  // 01: 배송지시일, 02:주문완료일, 03:출고예정일
		$ret_param["requestShppDirection"]["perdStrDts"] = $s_date;
		$ret_param["requestShppDirection"]["perdEndDts"] = $e_date;
		$json_ret_param = json_encode($ret_param, true);
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url, 'json_ret_param' => $json_ret_param));
		//print_r2($ret_curl['result_text']);
		if ($ret_curl['status_code'] == 200) {
			$data_array = json_decode($ret_curl['result_text'], true);;
			foreach ($data_array["result"]["shppDirections"] as $o_key => $order) {
				foreach ($order["shppDirection"] as $items) {
					// 주문 확인
					$this->setOrderConfirm(array('api_key' => $api_key, 'shppNo' => $items["shppNo"], 'shppSeq' => $items["shppSeq"]));
				}
			}
		}
		//endregion

		//region *** 주문확인 리스트 ***
		$api_url = "https://eapi.ssgadm.com/api/pd/1/listWarehouseOut.ssg";
		$ret_param = array('requestWarehouseOut' => array());
		$ret_param["requestWarehouseOut"]["perdType"] = "01";  // 01: 배송지시일, 02:주문완료일, 03:출고예정일
		$ret_param["requestWarehouseOut"]["perdStrDts"] = $s_date;
		$ret_param["requestWarehouseOut"]["perdEndDts"] = $e_date;
		$json_ret_param = json_encode($ret_param, true);
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url, 'json_ret_param' => $json_ret_param));

		$status_code = $ret_curl['status_code'];
		$response  = $ret_curl['result_text'];
		//echo $status_code;
		//print_r2($response);
		$API_header = $this->API_HEADERS;
		// 주문 확인 리스트 Array 파싱
		$arrData     = array();
		$tmp_array   = array();
		foreach ($API_header as $val) {
			$tmp_array[$val] = "";
		}
		$row_num  = 0;
		if ($status_code == 200) {
			$arrRet["status_code"]  = 0;
			$data_array = json_decode($response, true);;
			//echo $data_array;
			foreach ($data_array["result"]["warehouseOuts"] as $o_key => $order) {
				foreach ($order["warehouseOut"] as $items) {
					//echo "Order Confirm : ". $order["shppDirection"]["shppNo"]."/".$order["shppDirection"]["shppSeq"]."<br />";
					//echo "Order Confirm : ".$this->setOrderConfirm(array('api_key' => $api_key, 'shppNo' => $order["shppDirection"]["shppNo"], 'shppSeq' => $order["shppDirection"]["shppSeq"]))."<br />";
					$arr_row = $tmp_array;
					for ($i = 0; $i < count($API_header); $i++) {
						$tmp = "";
						foreach ($items as $i_key => $item) {
							//echo $i_key . "->" . $item . "<br />";
							if ($i_key == $API_header[$i]) {
								$tmp = (string)$item;
							}
						}
						$arr_row[$API_header[$i]] = (string)$tmp . "";
					}
					if ($arr_row["ordNo"] != "") {
						$arrData[] = $arr_row;
						$row_num++;
					}
				}

				//$array_shipmentBoxIds[$new_order_cnt] = $order["shipmentBoxId"];
				//$new_order_cnt ++;
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
		$arrRet          = array(
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
		$api_key = "";
		$s_date = "";
		$e_date = "";
		extract($args);

		$C_CS    = new CS();
		$C_Order = new Order();

		//region *** 취소 주문 조회 ***
		$api_url = "https://eapi.ssgadm.com/api/pd/1/listOrdCancel.ssg ";
		$ret_param = array('requestShppDirection' => array());
		$ret_param["requestShppDirection"]["perdStrDts"] = $s_date;
		$ret_param["requestShppDirection"]["perdEndDts"] = $e_date;
		$json_ret_param = json_encode($ret_param, true);
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url, 'json_ret_param' => $json_ret_param));
		//print_r2($ret_curl['result_text']);
		if ($ret_curl['status_code'] == 200) {
			$arrRet["status_code"] = 0;
			$data_array = json_decode($ret_curl['result_text'], true);;
			foreach ($data_array["result"]["shppDirections"] as $o_key => $order) {
				$arrRet['request_cnt']++;
				$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $order["shppDirection"]["shppNo"], $order["shppDirection"]["shppSeq"]);
				if (count($_row) > 0) {
					if($_row["order_progress_step"] == "ORDER_INVOICE" || $_row["order_progress_step"] == "ORDER_SHIPPED") {
						$arrRet["reject_cnt"]++;
					} else {
						$arrRet["result_data"][] = array(
							'market_order_no' => $order["shppDirection"]["shppNo"],
							'market_order_subno' => $order["shppDirection"]["shppSeq"],
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
		//endregion

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
		$seller_idx      = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg          = "";
		$api_key = "";
		$s_date = "";
		$e_date = "";
		extract($args);

		$C_CS    = new CS();
		$C_Order = new Order();

		//region *** 배송 대기 내역 조회 ***
		$api_url = "https://eapi.ssgadm.com/api/pd/1/listWarehouseOut.ssg";
		$ret_param = array('requestWarehouseOut' => array());
		$ret_param["requestWarehouseOut"]["perdType"] = "01";  // 01: 배송지시일, 02:주문완료일, 03:출고예정일
		$ret_param["requestWarehouseOut"]["perdStrDts"] = $s_date;
		$ret_param["requestWarehouseOut"]["perdEndDts"] = $e_date;
		$json_ret_param = json_encode($ret_param, true);
		$ret_curl    = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url, 'json_ret_param' => $json_ret_param));
		//print_r2($ret_curl['result_text']);
		if ($ret_curl['status_code'] == 200) {
			$arrRet["status_code"] = 0;
			$data_array = json_decode($ret_curl['result_text'], true);
			foreach ($data_array["result"]["warehouseOuts"][0]["warehouseOut"] as $order) {
				$arrRet['request_cnt']++;
				$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $order["ordNo"], $order["ordItemSeq"]);
				if (count($_row) > 0) {
					if($_row["order_progress_step"] == "ORDER_SHIPPED") {
						$_delivery[] = array('requestWhOutCompleteProcess' => array(
							'shppNo' => $order["shppNo"],
							'shppSeq' => $order["shppSeq"],
							'wblNo' => $_row["invoice_no"],
							'delicoVenId' => $_row["market_delivery_code"],
							'shppTypeCd' => "20",
							'shppTypeDtlCd' => "22",
							'orderNo' => $order["ordNo"],
							'orderSubNo' => $order["ordItemSeq"]
						));
						$arrRet['confirm_cnt']++;
					} else {
						$arrRet["reject_cnt"]++;
					}
				} else {
					$arrRet['notorder_cnt']++;
				}
			}

			if(count($_delivery) > 0) {
				//region *** 마켓 실제 송장입력 처리 ***
				// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.

				foreach ($_delivery as $d_val) {
					//추가 데이터 존재 시, 오류로 인해 새로 생성
					$post_data = array('requestWhOutCompleteProcess' => array(
						'shppNo' => $d_val["requestWhOutCompleteProcess"]["shppNo"],
						'shppSeq' => $d_val["requestWhOutCompleteProcess"]["shppSeq"],
						'wblNo' => $d_val["requestWhOutCompleteProcess"]["wblNo"],
						'delicoVenId' => $d_val["requestWhOutCompleteProcess"]["delicoVenId"],
						'shppTypeCd' => "20",
						'shppTypeDtlCd' => "22",
					));

					$api_url = "https://eapi.ssgadm.com/api/pd/1/saveWblNo.ssg ";
					$json_ret_param = json_encode($post_data, true);
					$C_Order->updateMarketInvoiceState($seller_idx, $d_val["requestWhOutCompleteProcess"]["orderNo"], $d_val["requestWhOutCompleteProcess"]["orderSubNo"]
						, "U", "미처리");
					if(DY_MARKET_IS_LIVE) {
						$_d_ret_curl = $this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url, 'json_ret_param' => $json_ret_param));

						if ($_d_ret_curl['status_code'] == 200) {
							$_d_data_array = json_decode($_d_ret_curl['result_text'], true);
							if($_d_data_array["result"]["resultCode"] = "00") {
								$C_Order->updateMarketInvoiceState($seller_idx, $d_val["requestWhOutCompleteProcess"]["orderNo"], $d_val["requestWhOutCompleteProcess"]["orderSubNo"]
									, ($_d_data_array["result"]["resultCode"] == "00" ? "S" : "F")
									, ($_d_data_array["result"]["resultCode"] == "00" ? "" : $_d_data_array["result"]["resultCode"] . ":" . $_d_data_array["result"]["resultMessage"]));

								//출고 완료 처리 20200316 kyu
								$post_data = array("requestWhOutCompleteProcess"=>array(
									"shppNo"=> $d_val["requestWhOutCompleteProcess"]["shppNo"],
									"shppSeq"=> $d_val["requestWhOutCompleteProcess"]["shppSeq"],
									"procItemQty"=> 1,
									"shppInstlDcsnDt"=>""
								));

								$api_url = "https://eapi.ssgadm.com/api/pd/1/saveWhOutCompleteProcess.ssg ";
								$json_ret_param = json_encode($post_data, true);

								$this->getCurl(array('api_key' => $api_key, 'api_url' => $api_url, 'json_ret_param' => $json_ret_param));
								// $arrRet["deliveryInfo"][] = $res["result_text"]; test
							}
						}
					}
				}
				//endregion
			}
		}
		//endregion
		return $arrRet;
	}
}
