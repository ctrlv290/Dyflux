<?php
/**
 * User: ssawoona
 * Date: 2019-03
 */

class API_Cafe24 extends DBConn
{
	//private $tmp_xls_header = "주문번호|품목별 주문번호|배송메시지|총 주문금액|총 결제금액|상품번호|주문상품명|주문상품명(옵션포함)|수량|판매가|수령인|수령인 휴대전화|수령인 우편번호|수령인 주소|수령인 상세 주소|결제구분|발주일";
	//private $tmp_api_header = "order/order_id|items/order_item_code|order/shipping_message|order/order_price_amount|order/actual_payment_amount|items/product_no|items/product_name|items/option_value|items/quantity|items/product_price|receivers/name|receivers/cellphone|receivers/zipcode|receivers/address1|receivers/address2|order/paid|order/order_date|";
	private $tmp_xls_header = "주문번호|품목별 주문번호|배송메시지|총 주문금액|총 결제금액|상품번호|주문상품명|주문상품명(옵션포함)|수량|판매가|수령인|수령인 휴대전화|수령인 우편번호|수령인 주소|수령인 상세 주소|결제구분|발주일|상품추가할인액|추가입력 옵션 값|앱 상품할인금액|마켓연동 상태값|주문취소일|취소요청일|취소 교환 반품 번호|해외통관용 상품구분|해외통관코드|해외통관용 상품정보|옷감|상품별 쿠폰 할인금액|배송완료일|영문 상품명|교환완료일|교환요청일|사은품 여부|HS코드|개별배송비|품주 아이디|네이버페이 클레임 타입|네이버페이 상품별 주문번호|1+N이벤트 여부|마켓연동 상태값|상품옵션 아이디|옵션 추가 가격|기본옵션값|주문상태|주문상태 추가정보|주문일|원산지정보|분리된 세트상품의 기존 품주번호|기존 품주 아이디|결제정보 아이디|우체국 택배연동|세트상품 여부|세트상품 목록|세트상품명|세트상품명(기본)|세트상품번호|세트상품 타입|상품코드|상품소재|영문 상품 소재|기본 상품명|상품 중량|환불완료일|반품완료일|반품요청일|배송시작일|배송번호|배송업체 코드|배송업체 아이디|배송업체 이름|배송비 타입|배송비타입|멀티쇼핑몰 번호|현재 처리상태 코드|현재 처리상태|매장수령여부|공급사 아이디|공급사명|공급사 상품명|공급사 거래 유형|송장번호|품목코드|상품 부피|상품 부피 무게|세트품주 분리여부|주문서 추가항목|앱 주문할인금액​​​|계좌번호|예금주|은행코드|입금자 은행명|결제자명|주문자 휴대 전화|주문자 이메일|주문자 이름|주문자 일반 전화|주문취소일|주문별 쿠폰 할인금액|화폐단위|주문시 회원등급|예치금사용금액|간편결제 결제사 이름|최종 적립금 사용금액|최종 배송비|최초 주문여부|여신상태|마켓 주문자 아이디|마켓 구분값|마켓 기타 정보|회원인증여부|회원아이디|회원할인금액|적립금사용금액|모바일 구분|주문경로|주문경로 텍스트|결제금액 (초기 지불완료 금액)|후불결제 입금확인 가능 여부|결제일|PG 이름|결제수단 코드|결제수단 아이콘|결제수단명|후불결제여부|후불결제수수료|주문상태|반품승인일시|배송비|배송비할인|배송상태|배송 유형|배송 유형명|멀티쇼핑몰 번호|매장수령여부|총 공급가액|에스크로 사용여부|희망배송사 코드|희망배송일|희망배송시간|수령자 도시 (영문)|국가코드|국가명|국가명 (영문)|수령자명 (영문)|수령자명 (발음)|전화번호|멀티쇼핑몰 번호|수령자 주 (영문)|수령자 주소 (영문)|수령자 안심번호";
	private $tmp_api_header = "order/order_id|items/order_item_code|order/shipping_message|order/order_price_amount|order/actual_payment_amount|items/product_no|items/product_name|items/option_value|items/quantity|items/product_price|receivers/name|receivers/cellphone|receivers/zipcode|receivers/address1|receivers/address2|order/paid|order/order_date|items/additional_discount_price|items/additional_option_value|items/app_item_discount_amount|items/bundled_shipping_type|items/cancel_date|items/cancel_request_date|items/claim_code|items/clearance_category|items/clearance_category_code|items/clearance_category_info|items/cloth_fabric|items/coupon_discount_price|items/delivered_date|items/eng_product_name|items/exchange_date|items/exchange_request_date|items/gift|items/hs_code|items/individual_shipping_fee|items/item_no|items/naver_pay_claim_status|items/naver_pay_order_id|items/one_plus_n_event|items/open_market_status|items/option_id|items/option_price|items/option_value_default|items/order_status|items/order_status_additional_info|items/ordered_date|items/origin_place|items/original_bundle_item_no|items/original_item_no|items/payment_info_id|items/post_express_flag|items/product_bundle|items/product_bundle_list|items/product_bundle_name|items/product_bundle_name_default|items/product_bundle_no|items/product_bundle_type|items/product_code|items/product_material|items/product_material_eng|items/product_name_default|items/product_weight|items/refund_date|items/return_date|items/return_request_date|items/shipped_date|items/shipping_code|items/shipping_company_code|items/shipping_company_id|items/shipping_company_name|items/shipping_fee_type|items/shipping_fee_type_text|items/shop_no|items/status_code|items/status_text|items/store_pickup|items/supplier_id|items/supplier_name|items/supplier_product_name|items/supplier_transaction_type|items/tracking_no|items/variant_code|items/volume_size|items/volume_size_weight|items/was_product_bundle|order/additional_order_info_list|order/app_discount_amount|order/bank_account_no|order/bank_account_owner_name|order/bank_code|order/bank_code_name|order/billing_name|order/buyer_cellphone|order/buyer_email|order/buyer_name|order/buyer_phone|order/cancel_date|order/coupon_discount_price|order/currency|order/customer_group_no_when_ordering|order/deposit_spent_amount|order/easypay_name|order/final_mileage_spent_amount|order/final_shipping_fee|order/first_order|order/loan_status|order/market_customer_id|order/market_id|order/market_order_info|order/member_authentication|order/member_id|order/membership_discount_amount|order/mileage_spent_amount|order/order_from_mobile|order/order_place_id|order/order_place_name|order/payment_amount|order/payment_confirmation|order/payment_date|order/payment_gateway_name|order/payment_method|order/payment_method_icon|order/payment_method_name|order/postpay|order/postpay_commission|order/process_status|order/return_confirmed_date|order/shipping_fee|order/shipping_fee_discount_amount|order/shipping_status|order/shipping_type|order/shipping_type_text|order/shop_no|order/store_pickup|order/total_supply_price|order/use_escrow|order/wished_carrier_id|order/wished_delivery_date|order/wished_delivery_time|receivers/city_en|receivers/country_code|receivers/country_name|receivers/country_name_en|receivers/name_en|receivers/name_furigana|receivers/phone|receivers/shop_no|receivers/state_en|receivers/street_en|receivers/virtual_phone_no";
	public $XLS_HEADERS;
	public $API_HEADERS;
	private $THIS_URL = "https://www.dyflux.co.kr";
	function __construct()
	{
		DBConn::__construct();
		$this->XLS_HEADERS = explode("|", $this->tmp_xls_header);
		$this->API_HEADERS = explode("|", $this->tmp_api_header);
	}

	public function getCurl($args)
	{
		$arrRet = array(
			'status_code' => 0,
			'result_text' => '',
		);
		$arrayFields = array();
		$headers = array();
		$api_url = "";
		$method = "";
		$request_type = "";
		extract($args);

		if($method == "GET") {
			$is_post = false;
		} else {
			$is_post = true;
		}
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_URL, $api_url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, $is_post);
		if($method != "GET") {
			if($method == "PUT") {
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($arrayFields));
				//curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($arrayFields));

			} else {
				if($request_type == "REQUEST") {
					curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($arrayFields));
				} else {
					curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($arrayFields));
				}
			}
		}
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

		$arrRet['result_text'] = curl_exec($curl);
		$arrRet['status_code'] = (int)curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		return $arrRet;
	}



	/**
	 * Cafw24 API Code 및 Token 갱신
	 * @param $args
	 * @return array
	 */
	public function getAPIToken($args)
	{
		$arrRet = array(
			'result' => false,
			'result_text' => '',
		);
		$mall_id       = "";
		$access_token  = "";
		$refresh_token = "";
		$client_id     = "";
		$client_secret = "";
		$seller_idx    = "";
		$_code = "";
		extract($args);
		$C_Seller     = new Seller();

		//region *** 신규 코드 발급이 필요 한경우 ***
		if($_code != "") {
			$_seller       = $C_Seller->getAllSellerData($seller_idx);
			$mall_id       = $_seller["market_login_id"];
			$client_id     = $_seller["market_auth_code"];
			$client_secret = $_seller["market_auth_code2"];
			$this_url      = $this -> THIS_URL;   // https:// 사용
			$sCode         = $_code;
			$aFields = array(
				'grant_type'   => 'authorization_code',
				'code'         => $sCode,
				'redirect_uri' => $this_url,
			);
			$headers = array(
				"Content-Type: application/x-www-form-urlencoded",
				"Authorization: Basic " . base64_encode($client_id . ':' . $client_secret),
			);
			$param = array(
				'arrayFields' => $aFields,
				'headers' => $headers,
				'api_url' => "https://" . $mall_id . ".cafe24api.com/api/v2/oauth/token",
				'method' => "POST",
			);
			$c_ret = $this -> getCurl($param);
			//print_r2($param);
			//print_r2($c_ret);
			if ($c_ret["status_code"] == 200) {
				$data_array = json_decode($c_ret["result_text"], true);
				$_ret = $C_Seller -> updateSeller_AuthCodes(array(
					'seller_idx' => $seller_idx,
					'market_auth_code3' => $_code,
					'market_auth_code4' => $data_array["access_token"],
					'market_auth_code5' => $data_array["refresh_token"],
				));
			}
		}
		//endregion


		//region *** refresh_token 으로 access_token 갱신
		$_seller    = $C_Seller -> getAllSellerData($seller_idx);
		if($_seller != null) {
			$mall_id       = $_seller["market_login_id"];
			$client_id     = $_seller["market_auth_code"];
			$client_secret = $_seller["market_auth_code2"];
			$aFields       = array(
				'grant_type' => 'refresh_token',
				'refresh_token' => $_seller["market_auth_code5"],
			);
			$headers       = array(
				"Content-Type: application/x-www-form-urlencoded",
				"Authorization: Basic " . base64_encode($client_id . ':' . $client_secret),
			);
			$param         = array(
				'arrayFields' => $aFields,
				'headers' => $headers,
				'api_url' => "https://" . $mall_id . ".cafe24api.com/api/v2/oauth/token",
				'method' => "POST",
			);
			$c_ret = $this -> getCurl($param);
			//print_r2($c_ret);
			if ($c_ret["status_code"] == 200) {
				$data_array = json_decode($c_ret["result_text"], true);
				$_ret = $C_Seller -> updateSeller_AuthCodes(array(
					'seller_idx' => $seller_idx,
					'market_auth_code3' => $_code,
					'market_auth_code4' => $data_array["access_token"],
					'market_auth_code5' => $data_array["refresh_token"],
				));
				$arrRet["result"] = true;
				$arrRet["result_text"] = "";
			} else {
				$arrRet["result_text"] = "Code 및 Token 조회 실패 (재인증필요1)";
			}
		} else {
			$arrRet["result_text"] = "잘못된 접근";
		}
		//endregion


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
		$mall_id = "";
		$order_id = "";
		$access_token = "";
		$shop_no = "";
		$order_item_code = array();
		extract($args);


		// 주문 확인 처리
		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token,
		);
		$arrayFields = array(
			'shop_no'   => $shop_no,
			'request'   => array( 'process_status' => "prepare",
				'order_item_code' => $order_item_code,
			),
		);
		$api_url = "https://".$mall_id.".cafe24api.com/api/v2/admin/orders/".$order_id;
		$param = array(
			'arrayFields' => $arrayFields,
			'headers' => $headers,
			'api_url' => $api_url,
			'method' => "PUT",
		);
		//echo "==>" .json_encode($arrayFields);
		//print_r2($param);
		$ret_curl = $this->getCurl($param);

		$status_code = $ret_curl['status_code'];
		$response  = $ret_curl['result_text'];

		//echo $status_code;
		//print_r2($response);

		if ($status_code == 200) {
			$data_array = json_decode($response, true);
			if($data_array["order"]["process_status"] == "prepare") {
				$ret = true;
			}
		}

		return $ret;
	}

	/**
	 * 주문 내역에 해당하는 주문 ItemList 조회
	 * @param $args
	 * @return array
	 */
	public function getOrderItemList($args)
	{
		$mall_id = "";
		$order_id = "";
		$access_token = "";
		extract($args);

		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token,
		);
		$api_url = "https://".$mall_id.".cafe24api.com/api/v2/admin/orders/".$order_id."/items";
		$param = array(
			'headers' => $headers,
			'api_url' => $api_url,
			'method' => "GET",
		);
		$ret_curl = $this->getCurl($param);
		return $ret_curl;
	}


	/**
	 * 주문 내역에 수령자 정보 조회
	 * @param $args
	 * @return array
	 */
	public function getOrderReceivers($args)
	{
		$mall_id = "";
		$order_id = "";
		$access_token = "";
		extract($args);

		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token,
		);
		$api_url = "https://".$mall_id.".cafe24api.com/api/v2/admin/orders/".$order_id."/receivers";
		$param = array(
			'headers' => $headers,
			'api_url' => $api_url,
			'method' => "GET",
		);
		$ret_curl = $this->getCurl($param);
		return $ret_curl;
	}


	/**
	 * 주문 내역에 배송 정보 조회
	 * @param $args
	 * @return array
	 */
	public function setOrderDeliveryConfirm($args)
	{
		$mall_id = "";
		$order_id = "";
		$access_token = "";
		$shop_no = "";
		$invoice_no = "";
		$delivery_code = "";
		$order_item_code = array();
		extract($args);

		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token,
		);
		$arrayFields = array(
			'shop_no'   => $shop_no,
			'request'   => array(
				'tracking_no' => $invoice_no,
				'shipping_company_code' => $delivery_code,
				'status' => 'shipping',    // shipping : 배송중
				'order_item_code' => $order_item_code,
				),
		);
		//print_r2($arrayFields);
		$api_url = "https://".$mall_id.".cafe24api.com/api/v2/admin/orders/".$order_id."/shipments";
		$param = array(
			'arrayFields' => $arrayFields,
			'headers' => $headers,
			'api_url' => $api_url,
			'method' => "POST",
			'request_type' => "REQUEST",
		);
		//echo json_encode($arrayFields);
		$ret_curl = $this->getCurl($param);
		return $ret_curl;
	}


	/**
	 * 신규 주문 리스트가져 오기 (페이징을 위해 재기호출)
	 * @param $args
	 * @return array
	 */
	public function getOrderList_API($args)
	{
		$arrRet     = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
		);
		$s_date = "";
		$e_date = "";
		$seller_idx = "";
		$API_header = $this->API_HEADERS;
		$offset        = 0;
		$arrData = array();
		$result_data = array();
		extract($args);

		$page_count = 100;

		$mall_id       = "";
		$client_id     = "";
		$client_secret = "";
		$access_token  = "";
		$refresh_token = "";
		$C_Seller = new Seller();
		$_seller  = $C_Seller->getAllSellerData($seller_idx);
		if($_seller != null) {
			$mall_id       = $_seller["market_login_id"];
			$client_id     = $_seller["market_auth_code"];
			$client_secret = $_seller["market_auth_code2"];
			$access_token  = $_seller["market_auth_code4"];
			$refresh_token = $_seller["market_auth_code5"];
		} else {

		}
		//print_r2($result_data);
		$arrData = $result_data;

		//echo count($arrData)."<br />";

		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token,
		);

		if($mall_id != "") {
			//region *** 배송준비중 리스트 가져오기 ***
			$row_num  = 0;
			$order_num  = 0;
			$api_url = "https://".$mall_id.".cafe24api.com/api/v2/admin/orders?";
			$getData = "start_date=".$s_date."&end_date=".$e_date;
			$getData .= "&order_status=N20";        // N20 : 배송 준비중
			$getData .= "&date_type=pay_date&limit=".$page_count."&offset=".$offset;
			$param = array(
				'headers' => $headers,
				'api_url' => $api_url.$getData,
				'method' => "GET",
			);
			//echo $api_url.$getData."<br />";
			$ret_curl = $this->getCurl($param);
			$status_code = $ret_curl['status_code'];
			$response = $ret_curl['result_text'];
			//$arrData = $result_data;


			$tmp_array = array();
			foreach ($API_header as $val) {
				$tmp_array[$val] = "";
			}
			if ($status_code == 200) {
				$arrRet["status_code"] = 0;
				$data_array = json_decode($response, true);
				//print_r2($data_array);
				foreach ($data_array["orders"] as $order) {
					$order_num ++;
					$arr_row = $tmp_array;
					for ($i = 0; $i < count($API_header); $i++) {
						$tmp = "";
						foreach ($order as $o_key => $order_val) {
							//echo $o_key . "->" . $order_val . "<br />";
							if ("order/".$o_key == $API_header[$i]) {
								//echo "order/".$o_key . "->" . $order_val . "<br />";
								$tmp = (string)$order_val;
								$arr_row[$API_header[$i]] = (string)$tmp. "";
							}
						}

					}

					//region *** 주문의 수령자 정보 ***
					$ret_receivers = $this -> getOrderReceivers(array(
						'mall_id' => $mall_id,
						'order_id' => $order["order_id"],
						'access_token' => $access_token,
					));
					if($ret_receivers['status_code'] == 200) {
						$receivers_array = json_decode($ret_receivers["result_text"], true);
						//print_r2($receivers_array);
						for ($i = 0; $i < count($API_header); $i++) {
							$tmp = "";
							foreach ($receivers_array["receivers"][0] as $recv_key => $recv_val) {
								if ("receivers/".$recv_key == $API_header[$i]) {
									//echo "receivers/".$recv_key . "->" . $recv_val . "<br />";
									$tmp = (string)$recv_val;
									$arr_row[$API_header[$i]] = (string)$tmp. "";
								}
							}
						}
					}
					//endregion

					//region *** 주문의 상세 Item 정보 ***
					$ret_item = $this -> getOrderItemList(array(
						'mall_id' => $mall_id,
						'order_id' => $order["order_id"],
						'access_token' => $access_token,
					));
					if($ret_item['status_code'] == 200) {
						$item_array = json_decode($ret_item["result_text"], true);
						//print_r2($item_array);
						foreach ($item_array["items"] as $key => $val) {
							$item_row = $arr_row;
							for ($i = 0; $i < count($API_header); $i++) {
								$tmp = "";
								foreach ($val as $item_key => $item_val) {
									//echo "items/".$item_key . "->" . $item_val . "<br />";
									if ("items/".$item_key == $API_header[$i]) {
										//echo "items/".$item_key . "->" . $item_val . "<br />";
										$tmp = (string)$item_val;
										$item_row[$API_header[$i]] = (string)$tmp. "";
									}
								}
							}
							//print_r2($item_row);
							//echo "order_date->" . date('Y-m-d H:i:s', strtotime($order["order_date"])). "<br />";
							$item_row["items/option_value"] = trim($item_row["items/product_name"])." ".$item_row["items/option_value"];
							$item_row["order/order_date"] = date('Y-m-d H:i:s', strtotime($item_row["order/order_date"]));
							$arrData[] = $item_row;
							$row_num++;
						}
					}
					//endregion

				}
			}
			//endregion

//			echo "order_num=>".$order_num."<br />";
//			echo "row_num=>".$row_num."<br />";
//			echo count($arrData)."<br />";

			$arrRet["result_data"] = $arrData;

			if($order_num >= $page_count) {
				$args["result_data"] = $arrData;
				$args["offset"] = $offset + $page_count;
				$arrRet = $this->getOrderList_API($args);
			}
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
		$arrRet     = array(
			'status_code' => -9999,
			'result_text' => '',
			'result_data' => array(),
		);
		$s_date = "";
		$e_date = "";
		$seller_idx = "";
		$API_header = $this->API_HEADERS;
		extract($args);

		//region *** 토큰 및 기본 정보 가져 오기 ***
		$ret_token = $this->getAPIToken($args);
		if(!$ret_token["result"]) {
			$arrRet["result_text"] = $ret_token["result_text"];
			return $arrRet;
		}
		$mall_id       = "";
		$client_id     = "";
		$client_secret = "";
		$access_token  = "";
		$refresh_token = "";
		$C_Seller = new Seller();
		$_seller  = $C_Seller->getAllSellerData($seller_idx);
		if($_seller != null) {
			$mall_id       = $_seller["market_login_id"];
			$client_id     = $_seller["market_auth_code"];
			$client_secret = $_seller["market_auth_code2"];
			$access_token  = $_seller["market_auth_code4"];
			$refresh_token = $_seller["market_auth_code5"];
		} else {
			$arrRet["result_text"] = "판매처 에러";
			return $arrRet;
		}
		//endregion

		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token,
		);

		//region *** 신규 주문을 조회해서 배송준비중 처리 ***
		$api_url = "https://".$mall_id.".cafe24api.com/api/v2/admin/orders?";
		$getData = "start_date=".$s_date."&end_date=".$e_date;
		$getData .= "&order_status=N10";        // N10 : 상품 준비중
		$getData .= "&date_type=pay_date&limit=500";
		$param = array(
			'headers' => $headers,
			'api_url' => $api_url.$getData,
			'method' => "GET",
		);
		$ret_curl = $this->getCurl($param);
		$status_code = $ret_curl['status_code'];
		$response = $ret_curl['result_text'];
		//print_r2($response);
		$arrData = array();
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$data_array = json_decode($response, true);
			//print_r2($data_array);
			foreach ($data_array["orders"] as $o_key => $order) {
				//echo $o_key . "->" . $order["order_id"] . "<br />";

				$order_items = array();
				//region *** 주문의 상세 Item 정보 ***
				$ret_item = $this -> getOrderItemList(array(
					'mall_id' => $mall_id,
					'order_id' => $order["order_id"],
					'access_token' => $access_token,
				));
				if($ret_item['status_code'] == 200) {
					$item_array = json_decode($ret_item["result_text"], true);
					//print_r2($item_array);
					foreach ($item_array["items"] as $key => $val) {
						$order_items[] = $val["order_item_code"];;
					}
				}
				//endregion
				//print_r2($order_items);
				$this -> setOrderConfirm(array(
					'mall_id' => $mall_id,
					'order_id' => $order["order_id"],
					'access_token' => $access_token,
					'shop_no' => $order["shop_no"],
					'order_item_code' => $order_items,
				));


			}
		}
		//endregion

		//region *** 배송준비중 리스트 가져오기 ***
		/*$row_num  = 0;
		$api_url = "https://".$mall_id.".cafe24api.com/api/v2/admin/orders?";
		$getData = "start_date=".$s_date."&end_date=".$e_date;
		$getData .= "&order_status=N20";        // N20 : 배송 준비중
		$getData .= "&date_type=pay_date&limit=50&offset=50";
		$param = array(
			'headers' => $headers,
			'api_url' => $api_url.$getData,
			'method' => "GET",
		);
		$ret_curl = $this->getCurl($param);
		$status_code = $ret_curl['status_code'];
		$response = $ret_curl['result_text'];
		$arrData = array();
		$tmp_array = array();
		foreach ($API_header as $val) {
			$tmp_array[$val] = "";
		}
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$data_array = json_decode($response, true);
			//print_r2($data_array);
			foreach ($data_array["orders"] as $order) {
				$arr_row = $tmp_array;
				for ($i = 0; $i < count($API_header); $i++) {
					$tmp = "";
					foreach ($order as $o_key => $order_val) {
						//echo $o_key . "->" . $order_val . "<br />";
						if ("order/".$o_key == $API_header[$i]) {
							//echo "order/".$o_key . "->" . $order_val . "<br />";
							$tmp = (string)$order_val;
							$arr_row[$API_header[$i]] = (string)$tmp. "";
						}
					}

				}

				//region *** 주문의 수령자 정보 ***
				$ret_receivers = $this -> getOrderReceivers(array(
					'mall_id' => $mall_id,
					'order_id' => $order["order_id"],
					'access_token' => $access_token,
				));
				if($ret_receivers['status_code'] == 200) {
					$receivers_array = json_decode($ret_receivers["result_text"], true);
					//print_r2($receivers_array);
					for ($i = 0; $i < count($API_header); $i++) {
						$tmp = "";
						foreach ($receivers_array["receivers"][0] as $recv_key => $recv_val) {
							if ("receivers/".$recv_key == $API_header[$i]) {
								//echo "receivers/".$recv_key . "->" . $recv_val . "<br />";
								$tmp = (string)$recv_val;
								$arr_row[$API_header[$i]] = (string)$tmp. "";
							}
						}
					}
				}
				//endregion

				//region *** 주문의 상세 Item 정보 ***
				$ret_item = $this -> getOrderItemList(array(
					'mall_id' => $mall_id,
					'order_id' => $order["order_id"],
					'access_token' => $access_token,
				));
				if($ret_item['status_code'] == 200) {
					$item_array = json_decode($ret_item["result_text"], true);
					//print_r2($item_array);
					foreach ($item_array["items"] as $key => $val) {
						$item_row = $arr_row;
						for ($i = 0; $i < count($API_header); $i++) {
							$tmp = "";
							foreach ($val as $item_key => $item_val) {
								//echo "items/".$item_key . "->" . $item_val . "<br />";
								if ("items/".$item_key == $API_header[$i]) {
									//echo "items/".$item_key . "->" . $item_val . "<br />";
									$tmp = (string)$item_val;
									$item_row[$API_header[$i]] = (string)$tmp. "";
								}
							}
						}
						//print_r2($item_row);
						//echo "order_date->" . date('Y-m-d H:i:s', strtotime($order["order_date"])). "<br />";
						$item_row["items/option_value"] = trim($item_row["items/product_name"])." ".$item_row["items/option_value"];
						$item_row["order/order_date"] = date('Y-m-d H:i:s', strtotime($item_row["order/order_date"]));
						$arrData[] = $item_row;
						$row_num++;
					}
				}
				//endregion

			}
		}*/
		//endregion


		$_arrOrder = $this->getOrderList_API($args);
		$arrData = $_arrOrder["result_data"];


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
		$s_date = "";
		$e_date = "";
		extract($args);
		$C_CS    = new CS();
		$C_Order = new Order();
		
		//region *** 토큰 및 기본 정보 가져 오기 ***
		$ret_token = $this->getAPIToken($args);
		if(!$ret_token["result"]) {
			$arrRet["result_text"] = $ret_token["result_text"];
			return $arrRet;
		}
		$mall_id       = "";
		$client_id     = "";
		$client_secret = "";
		$access_token  = "";
		$refresh_token = "";
		$C_Seller = new Seller();
		$_seller  = $C_Seller->getAllSellerData($seller_idx);
		if($_seller != null) {
			$mall_id       = $_seller["market_login_id"];
			$client_id     = $_seller["market_auth_code"];
			$client_secret = $_seller["market_auth_code2"];
			$access_token  = $_seller["market_auth_code4"];
			$refresh_token = $_seller["market_auth_code5"];
		} else {
			$arrRet["result_text"] = "판매처 에러";
			return $arrRet;
		}
		//endregion

		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token,
		);

		//region *** 취소 주문 조회 ***
		$api_url = "https://".$mall_id.".cafe24api.com/api/v2/admin/orders?";
		$getData = "start_date=".$s_date."&end_date=".$e_date;
		$getData .= "&order_status=C00,C10";        // C00 : 취소신청, C10 : 취소 접수 - 관리자
		$getData .= "&date_type=pay_date&limit=500";
		$param = array(
			'headers' => $headers,
			'api_url' => $api_url.$getData,
			'method' => "GET",
		);
		$ret_curl = $this->getCurl($param);
		$status_code = $ret_curl['status_code'];
		$response = $ret_curl['result_text'];
		$arrData = array();
		//print_r2($status_code);
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$data_array = json_decode($response, true);
			//ㄱprint_r2($data_array);
			foreach ($data_array["orders"] as $o_key => $order) {
				$order_id = $order["order_id"];
				//echo "order_id->" . $order_id . "<br />";

				//region *** 주문의 상세 Item 정보 ***
				$ret_item = $this -> getOrderItemList(array(
					'mall_id' => $mall_id,
					'order_id' => $order["order_id"],
					'access_token' => $access_token,
				));
				if($ret_item['status_code'] == 200) {
					$item_array = json_decode($ret_item["result_text"], true);
					//print_r2($item_array);
					foreach ($item_array["items"] as $key => $item) {
						$order_item_code = $item["order_item_code"];
						//echo "order_item_code->" . $item["order_item_code"] . "<br />";
						$arrRet['request_cnt']++;
						$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $order_id, $order_item_code);
						if (count($_row) > 0) {
							if($_row["order_progress_step"] == "ORDER_INVOICE" || $_row["order_progress_step"] == "ORDER_SHIPPED") {
								$arrRet["reject_cnt"]++;
							} else {
								$arrRet["result_data"][] = array(
									'market_order_no' => $order_id,
									'market_order_subno' => $order_item_code,
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
			}
		}
		//endregion


		if($arrRet["status_code"] < 0) {
			$arrRet["result_text"] = "API Error ";
			return $arrRet;
		} else {
			$arrRet["result_text"] = "";
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
		$seller_idx      = "";
		$cs_reason_code1 = "";
		$cs_reason_code2 = "";
		$cs_msg          = "";
		$s_date = "";
		$e_date = "";
		extract($args);
		$C_CS    = new CS();
		$C_Order = new Order();


		//region *** 토큰 및 기본 정보 가져 오기 ***
		$ret_token = $this->getAPIToken($args);
		if(!$ret_token["result"]) {
			$arrRet["result_text"] = $ret_token["result_text"];
			return $arrRet;
		}
		$mall_id       = "";
		$client_id     = "";
		$client_secret = "";
		$access_token  = "";
		$refresh_token = "";
		$C_Seller = new Seller();
		$_seller  = $C_Seller->getAllSellerData($seller_idx);
		if($_seller != null) {
			$mall_id       = $_seller["market_login_id"];
			$client_id     = $_seller["market_auth_code"];
			$client_secret = $_seller["market_auth_code2"];
			$access_token  = $_seller["market_auth_code4"];
			$refresh_token = $_seller["market_auth_code5"];
		} else {
			$arrRet["result_text"] = "판매처 에러";
			return $arrRet;
		}
		//endregion

		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token,
		);

		$api_url = "https://".$mall_id.".cafe24api.com/api/v2/admin/orders?";
		$getData = "start_date=".$s_date."&end_date=".$e_date;
		$getData .= "&order_status=N20";        // N20 : 배송 준비중
		$getData .= "&date_type=pay_date&limit=500";
		$param = array(
			'headers' => $headers,
			'api_url' => $api_url.$getData,
			'method' => "GET",
		);
		$ret_curl = $this->getCurl($param);
		$status_code = $ret_curl['status_code'];
		$response = $ret_curl['result_text'];
		$arrData = array();
		if ($status_code == 200) {
			$arrRet["status_code"] = 0;
			$data_array = json_decode($response, true);
			//print_r2($data_array);
			foreach ($data_array["orders"] as $o_key => $order) {
				$order_id = $order["order_id"];
				$shop_no = $order["shop_no"];
				//echo "order_id->" . $order_id . "<br />";

				//region *** 주문의 상세 Item 정보 ***
				$ret_item = $this -> getOrderItemList(array(
					'mall_id' => $mall_id,
					'order_id' => $order["order_id"],
					'access_token' => $access_token,
				));
				$order_items = array();
				if($ret_item['status_code'] == 200) {
					$item_array = json_decode($ret_item["result_text"], true);
					//print_r2($item_array);
					foreach ($item_array["items"] as $key => $val) {
						$order_items[] = $val["order_item_code"];;
					}
				}

				$arrRet['request_cnt']++;
				$_row = $C_Order->getOrderByMarketOrderNo_autoSubNo($seller_idx, $order_id);

				if (count($_row) > 0) {

					if($_row["order_progress_step"] == "ORDER_SHIPPED") {

						parent::addDebugLog($order_id, array(
							"order_id" => $order_id.":".$seller_idx,
							"result_text" => $_row["order_progress_step"],
							"status_code" => $status_code,
						));

						$arrRet["confirm_cnt"]++;
						//echo $_row["order_progress_step"];

						$_delivery = array(
							'mall_id' => $mall_id,
							'order_id' => $order_id,
							'access_token' => $access_token,
							'shop_no' => $shop_no,
							'invoice_no' => $_row["invoice_no"],
							'delivery_code' => $_row["market_delivery_code"],
							'order_item_code' => $order_items,
						);

						$C_Order->updateMarketInvoiceState_autoSubNo($seller_idx, $order_id
							, "U", "미처리");
						//region *** 마켓 실제 송장입력 처리 ***
						// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.
						if(DY_MARKET_IS_LIVE) {
							$ret_delivery = $this -> setOrderDeliveryConfirm($_delivery);
							if($ret_delivery['status_code'] == 200 || $ret_delivery['status_code'] == 201) {
								//$delivery_array = json_decode($ret_delivery["result_text"], true);
								$C_Order->updateMarketInvoiceState_autoSubNo($seller_idx, $order_id
									, "S", "");
							} else {
								$C_Order->updateMarketInvoiceState_autoSubNo($seller_idx, $order_id
									, "F", "송장등록 실패");

							}
						}
						//endregion

						$arrRet['confirm_cnt']++;
					} else {
						$arrRet["reject_cnt"]++;
					}

				} else {
					$arrRet['notorder_cnt']++;
				}




				if($ret_item['status_code'] == 200 && 1==2) {
					$item_array = json_decode($ret_item["result_text"], true);
					//print_r2($item_array);
					foreach ($item_array["items"] as $key => $item) {
						$order_item_code = $item["order_item_code"];
						//echo "order_item_code->" . $item["order_item_code"] . "<br />";
						$arrRet['request_cnt']++;
						$_row = $C_Order->getOrderByMarketOrderNo($seller_idx, $order_id, $order_item_code);
						if (count($_row) > 0) {
							if($_row["order_progress_step"] == "ORDER_SHIPPED") {
								$arrRet["confirm_cnt"]++;
								//echo $_row["order_progress_step"];

								$_delivery = array(
									'mall_id' => $mall_id,
									'order_id' => $order_id,
									'access_token' => $access_token,
									'shop_no' => $shop_no,
									'invoice_no' => $_row["invoice_no"],
									'delivery_code' => $_row["market_delivery_code"],
									'order_item_code' => array($order_item_code),
								);
								$C_Order->updateMarketInvoiceState($seller_idx, $order_id, $order_item_code
									, "U", "미처리");
								//region *** 마켓 실제 송장입력 처리 ***
								// TODO : ▽▽▽ 마켓 처리는 이지어드민이랑 충돌남 실 오픈시 오픈 해야 함.
								if(DY_MARKET_IS_LIVE) {
									$ret_delivery = $this -> setOrderDeliveryConfirm($_delivery);
									if($ret_delivery['status_code'] == 200 || $ret_delivery['status_code'] == 201) {
										//$delivery_array = json_decode($ret_delivery["result_text"], true);
										$C_Order->updateMarketInvoiceState($seller_idx, $order_id, $order_item_code
											, "S", "");
									} else {
										$C_Order->updateMarketInvoiceState($seller_idx, $order_id, $order_item_code
											, "F", "송장등록 실패");

										parent::addDebugLog($order_id, array(
											"order_id" => $order_id."=".$order_item_code,
											"result_text" => $ret_delivery,
											"status_code" => $status_code,
										));

									}
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
				//endregion
			}
		}

		if($arrRet["status_code"] < 0) {
			$arrRet["result_text"] = "API Error ";
			return $arrRet;
		} else {
			$arrRet["result_text"] = "";
		}

		return $arrRet;
	}


	public function execDeliveryProc_test($args)
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
		$s_date = "";
		$e_date = "";
		extract($args);
		$C_CS    = new CS();
		$C_Order = new Order();


		//region *** 토큰 및 기본 정보 가져 오기 ***
		$ret_token = $this->getAPIToken($args);
		if(!$ret_token["result"]) {
			$arrRet["result_text"] = $ret_token["result_text"];
			return $arrRet;
		}
		$mall_id       = "";
		$client_id     = "";
		$client_secret = "";
		$access_token  = "";
		$refresh_token = "";
		$C_Seller = new Seller();
		$_seller  = $C_Seller->getAllSellerData($seller_idx);
		if($_seller != null) {
			$mall_id       = $_seller["market_login_id"];
			$client_id     = $_seller["market_auth_code"];
			$client_secret = $_seller["market_auth_code2"];
			$access_token  = $_seller["market_auth_code4"];
			$refresh_token = $_seller["market_auth_code5"];
		} else {
			$arrRet["result_text"] = "판매처 에러";
			return $arrRet;
		}
		//endregion

		$headers = array(
			"Content-Type: application/json",
			"Authorization: Bearer ".$access_token,
		);
		$order_id = "20190709-0003192";
		$shop_no = "1";
		$invoice_no = "346685081734";
		$market_delivery_code = "0006";
		//$order_item_code = array("20190709-0003192-01");
		$_delivery = array(
			'mall_id' => $mall_id,
			'order_id' => $order_id,
			'access_token' => $access_token,
			'shop_no' => $shop_no,
			'invoice_no' => $invoice_no,
			'delivery_code' => $market_delivery_code,
			'order_item_code' => array("20190709-0003192-04"),
		);

		//print_r2($_delivery);
		$ret_delivery = $this -> setOrderDeliveryConfirm($_delivery);
		//print_r2($ret_delivery);

		return $arrRet;
	}

}
?>